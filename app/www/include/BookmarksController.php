<?php
define('BASE_URL',          'http://127.0.0.1:62386');
define('ARCHIVE_LOCATION',  realpath($_SERVER["DOCUMENT_ROOT"]) . "/cache/");
define('CHROME_LOCATION',   realpath($_SERVER["DOCUMENT_ROOT"]) . "/chrome/Chrome.exe");

class BookmarksController {
    private $db;
    private $settings_db;

    public function __construct() {
        $settings_db_location = realpath($_SERVER["DOCUMENT_ROOT"]) . "/settings.db";

        if(!file_exists($settings_db_location)) {
            $this->settings_db = new Database($settings_db_location);
            $sql = "CREATE TABLE \"settings\" (
                    \"id\"	INTEGER PRIMARY KEY AUTOINCREMENT,
	                \"key\"	TEXT,
	                \"value\"	TEXT
                    );
            ";
            $this->settings_db->run($sql);
        }

        $this->settings_db = new Database($settings_db_location);

        $db_location = realpath($_SERVER["DOCUMENT_ROOT"]) . "/bookmarks.db";

        if(!file_exists($db_location)) {
            $this->db = new Database($db_location);
            $sql = "CREATE TABLE \"bookmarks\" (
                    \"id\"	INTEGER PRIMARY KEY AUTOINCREMENT,
                    \"title\"	TEXT,
                    \"url\"	TEXT,
                    \"tags\"	TEXT,
                    \"category\"	TEXT,
                    \"notes\"	TEXT,
                    \"cache\"	TEXT,
                    \"created_at\"	DATE
                    );
            ";
            $this->db->run($sql);
        }

        $this->db = new Database($db_location);
    }

    public function saveSettings() {
        $cache = Request::post("cache");
        $cache_type = Request::post("cache_type");

        $arr = [
            "cache" => $cache,
            "cache_type" => $cache_type
        ];
        $values = [
            "key" => "bookmarks",
            "value" => json_encode($arr)
        ];

        $row = $this->settings_db->selectSingle("SELECT * FROM settings WHERE key='bookmarks'");
        if($row) {
            $this->settings_db->update("settings", $values, "WHERE key='bookmarks'");
        } else {
            $this->settings_db->insert("settings", $values);
        }

        $_SESSION["msg"] = "Settings was updated";
    }

    public function getSettings() {
        $row = $this->settings_db->selectSingle("SELECT * FROM settings WHERE key='bookmarks'");
        if($row) {
            $arr = json_decode($row["value"], true);
            return $arr;
        }

        return null;
    }

    public function import() {
        $file = Request::file('file');

        $content = file_get_contents($file['tmp_name']);
        $lines = explode("\r\n", $content);

        $category = '';
        $tags = '';
        $notes = '';
        foreach($lines as $line) {
            if(preg_match("/<\/H3>/", $line)) {
                $tmp = preg_split("/\">/", $line);
                $tmp = str_replace('</H3>', '', $tmp[1]);
                $category = $tmp;
            }

            if(preg_match("/<DT><A HREF=/", $line)) {
                $tmp = preg_split("/<DT><A HREF=\"/", $line);
                $tmp = preg_split("/\"/", $tmp[1]);
                $url = $tmp[0];

                $tmp = preg_split("/ADD_DATE=\"/", $line);
                $tmp = preg_split("/\"/", $tmp[1]);
                $created_at = $tmp[0];

                $tmp = preg_split("/<\/A>/", $line);
                $tmp = preg_split("/\">/", $tmp[0]);
                $title = $tmp[1];

                $this->saveHelper(null, $title, $url, $tags, $category, $notes, $created_at);
            }
        }
    }

    public function saveHelper($id, $title, $url, $tags, $category, $notes, $date) {
        if(!(preg_match("/^http:\/\//i", $url) || preg_match("/^https:\/\//i", $url))) {
            $url = "http://" . $url;
        }

        $values = array(
            "title" => $title,
            "url" => $url,
            "tags" => $tags,
            "category" => $category,
            "notes" => $notes,
            "created_at" => $date
        );


        $row = $this->db->selectSingle("SELECT * FROM bookmarks WHERE url='" . $this->db->escape($url) . "'");
        if($row) {
            $this->db->update("bookmarks", $values, "WHERE id='$id'");
        } else {
            $this->db->insert("bookmarks", $values);
        }

        $_SESSION["msg"] = "Bookmark was saved";
    }

    public function save() {
        $id = Request::post("id");
        $title = Request::all("title");
        $url = Request::all("url");
        $tags = Request::post("tags");
        $category = Request::post("category");
        $notes = Request::post("notes");
        $date = Request::post("date");
        $cache = Request::post("cache");

        if($cache) {
            $this->cache($url);
        }

        $this->saveHelper($id, $title, $url, $tags, $category, $notes, $date);
    }

    public function delete() {
        $id = Request::get('id');

        $row = $this->db->selectSingle("SELECT * FROM bookmarks WHERE id='" . $this->db->escape($id) . "'");
        if($row) {
            $id = $row["id"];
            $url = $row["url"];

            $this->db->delete("DELETE FROM bookmarks WHERE id='" . $this->db->escape($id) . "'");

            // delete any cache
            $screenshot = preg_replace("/[^a-z\d]/i", "_", $url);
            $png_file = $screenshot . ".png";
            $pdf_file = $screenshot . ".pdf";

            if(file_exists(ARCHIVE_LOCATION . $png_file)) {
                unlink(ARCHIVE_LOCATION . $png_file);
            }
            if(file_exists(ARCHIVE_LOCATION . $pdf_file)) {
                unlink(ARCHIVE_LOCATION . $pdf_file);
            }
        }


    }

    public function cache($url) {
        $settings = $this->getSettings();
        if($settings) {
            $cache_type = $settings["cache_type"];
        } else {
            $cache_type = "pdf";
        }

        $screenshot = preg_replace('/[^a-z\d]/i', '_', $url);

        if($cache_type == "pdf") {
            $command = CHROME_LOCATION . '  --headless --print-to-pdf="' . ARCHIVE_LOCATION . $screenshot . '.pdf" -window-size=2880,1880 "' . $url . '"';
        } else {
            $command = CHROME_LOCATION . ' --headless --screenshot="' . ARCHIVE_LOCATION . $screenshot . '.png" -window-size=1400,8000 "' . $url . '"';
        }

        exec($command);
    }

    public function getHelper($rows) {
        for($i=0; $i<count($rows); $i++) {
            // from
            $tmp["url"] = str_ireplace("www.", "", parse_url($rows[$i]["url"], PHP_URL_HOST));
            $tmp["name"] = substr($tmp["url"], 0, 125);
            $rows[$i]["from"] = $tmp;

            // category
            $tmp["url"] = urlencode($rows[0]["category"]);
            $tmp["name"] = $rows[0]["category"];
            $rows[$i]["cat"] = $tmp;

            // tags
            $tags = [];
            if($rows[$i]["tags"]) {
                $words = explode(",", $rows[$i]["tags"]);
                foreach($words as $word) {
                    $tmp["url"] = urlencode($word);
                    $tmp["name"] = $word;
                    $tags[] = $tmp;
                }
            }
            $rows[$i]["tag_list"] = $tags;

            // year
            $year = date("Y", strtotime($rows[0]["created_at"]));
            $tmp["url"] = urlencode($year);
            $tmp["name"] = $year;
            $rows[$i]["year"] = $tmp;

            // month
            $tmp["url"] = urlencode(date("Y-m", strtotime($rows[0]["created_at"])));
            $tmp["name"] = date("m", strtotime($rows[0]["created_at"]));
            $rows[$i]["month"] = $tmp;

            // date
            $tmp["url"] = urlencode(date("Y-m-d", strtotime($rows[0]["created_at"])));
            $tmp["name"] = date("d", strtotime($rows[0]["created_at"]));
            $rows[$i]["day"] = $tmp;

        }

        return $rows;
    }

    public function search() {
        $query = Request::get('query');

        return $this->db->select("SELECT * FROM bookmarks WHERE title LIKE '%" . $this->db->escape($query) . "%' OR url LIKE '%" . $this->db->escape($query) . "%' OR notes LIKE '%" . $this->db->escape($query) . "%' ORDER BY created_at DESC");
    }

    public function get($id) {
        return $this->db->selectSingle("SELECT * FROM bookmarks WHERE id='" . $this->db->escape($id) . "'");
    }

    public function getAll($start, $limit) {
        $rows = $this->db->select("SELECT * FROM bookmarks ORDER BY created_at DESC LIMIT " . $this->db->escape($start) . ", " . $this->db->escape($limit));
        return $this->getHelper($rows);
    }

    public function getAllByCategory($category) {
        $rows = $this->db->select("SELECT * FROM bookmarks WHERE category='" . $this->db->escape($category) . "' ORDER BY created_at DESC");
        return $this->getHelper($rows);
    }

    public function getAllByFrom($from) {
        $rows = $this->db->select("SELECT * FROM bookmarks WHERE url LIKE '%" . $this->db->escape($from) . "%' ORDER BY created_at DESC");
        return $this->getHelper($rows);
    }

    public function getAllByTag($tag) {
        $rows = $this->db->select("SELECT * FROM bookmarks WHERE tags LIKE '%" . $this->db->escape($tag) . "%' ORDER BY created_at DESC");
        return $this->getHelper($rows);
    }

    public function getAllByYear($year) {
        $start = "$year-01-01";
        $end = "$year-12-31";

        $rows = $this->db->select("SELECT * FROM bookmarks WHERE created_at BETWEEN '" . $this->db->escape($start) . "' AND '" . $this->db->escape($end) . "' ORDER BY created_at DESC");
        return $this->getHelper($rows);
    }

    public function getAllByMonth($month) {
        $start = "$month-01";
        $end = date("Y-m-t", strtotime($start));

        $rows = $this->db->select("SELECT * FROM bookmarks WHERE created_at BETWEEN '" . $this->db->escape($start) . "' AND '" . $this->db->escape($end) . "' ORDER BY created_at DESC");
        return $this->getHelper($rows);
    }

    public function getAllByDay($day) {
        $rows = $this->db->select("SELECT * FROM bookmarks WHERE created_at='" . $this->db->escape($day) . "' ORDER BY created_at DESC");
        return $this->getHelper($rows);
    }

    public function getCategories() {
        $rows = $this->db->select("SELECT DISTINCT category FROM bookmarks");

        $arr = [];
        foreach($rows as $row) {
            $arr[$row["category"]] = urlencode($row["category"]);
        }
        return $arr;
    }
}