<?php

class RssController {
    private $db;

    public function __construct() {
        $db_location = realpath($_SERVER["DOCUMENT_ROOT"]) . "/rss.db";

        if(!file_exists($db_location)) {
            $this->db = new Database($db_location);

            $sql = "CREATE TABLE \"rss\" (
                    \"id\"	INTEGER PRIMARY KEY AUTOINCREMENT,
                    \"feed_id\"	 INTEGER,
                    \"title\"	TEXT,
                    \"url\"	TEXT,
                    \"tags\"	TEXT,
                    \"description\"	 TEXT,
                    \"notes\"	TEXT,
                    \"category\"	TEXT,
                    \"created_at\"	DATE
                    );
            ";
            $this->db->run($sql);

            $sql = "CREATE TABLE \"feeds\" (
                    \"id\"	INTEGER PRIMARY KEY AUTOINCREMENT,
                    \"url\"	TEXT,
                    \"category\" TEXT,
                    \"tags\" TEXT,
                    \"updated_at\"	DATE
                    );
            ";
            $this->db->run($sql);
        }

        $this->db = new Database($db_location);
    }

    public function saveSummary($id, $feed_id, $title, $url, $tags, $category, $description, $notes, $date) {
        if(!(preg_match("/^http:\/\//i", $url) || preg_match("/^https:\/\//i", $url))) {
            $url = "http://" . $url;
        }

        $values = array(
            "feed_id" => $feed_id,
            "title" => $title,
            "url" => $url,
            "tags" => $tags,
            "category" => $category,
            "description" => $description,
            "notes" => $notes,
            "created_at" => $date
        );

        if($id) {
            $row = $this->db->selectSingle("SELECT * FROM rss WHERE id='" . $this->db->escape($id) . "'");
        } else {
            $row = $this->db->selectSingle("SELECT * FROM rss WHERE url='" . $this->db->escape($url) . "'");
        }
        if($row) {
            $id = $row["id"];
            $this->db->update("rss", $values, "WHERE id='" . $this->db->escape($id) . "'");
        } else {
            $this->db->insert("rss", $values);
        }

        $_SESSION["msg"] = "Summary was updated";
    }

    public function deleteSummary() {
        $id = Request::get("id");
        $this->db->delete("DELETE FROM rss WHERE id='" . $this->db->escape($id) . "'");
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

    public function search($start, $limit) {
        $query = Request::get("query");

        $rows = $this->db->select("SELECT * FROM rss WHERE title LIKE '%" . $this->db->escape($query) . "%' OR url LIKE '%" . $this->db->escape($query) . "%' OR notes LIKE '%" . $this->db->escape($query) . "%' ORDER BY created_at DESC LIMIT " . $this->db->escape($start) . ", " . $this->db->escape($limit));
        return $this->getHelper($rows);
    }

    public function get($id) {
        return $this->db->selectSingle("SELECT * FROM rss WHERE id='" . $this->db->escape($id) . "'");
    }

    public function getAll($start, $limit) {
        $rows = $this->db->select("SELECT * FROM rss ORDER BY created_at DESC LIMIT " . $this->db->escape($start) . ", " . $this->db->escape($limit));
        return $this->getHelper($rows);
    }

    public function getAllByCategory($category, $start, $limit) {
        $rows = $this->db->select("SELECT * FROM rss WHERE category='" . $this->db->escape($category) . "' ORDER BY created_at DESC LIMIT " . $this->db->escape($start) . ", " . $this->db->escape($limit));
        return $this->getHelper($rows);
    }

    public function getAllByFrom($from, $start, $limit) {
        $rows = $this->db->select("SELECT * FROM rss WHERE url LIKE '%" . $this->db->escape($from) . "%' ORDER BY created_at DESC LIMIT " . $this->db->escape($start) . ", " . $this->db->escape($limit));
        return $this->getHelper($rows);
    }

    public function getAllByTag($tag, $start, $limit) {
        $rows = $this->db->select("SELECT * FROM rss WHERE tags LIKE '%" . $this->db->escape($tag) . "%' ORDER BY created_at DESC LIMIT " . $this->db->escape($start) . ", " . $this->db->escape($limit));
        return $this->getHelper($rows);
    }

    public function getAllByYear($year, $start, $limit) {
        $start_date = "$year-01-01";
        $end_date = "$year-12-31";

        $rows = $this->db->select("SELECT * FROM rss WHERE created_at BETWEEN '" . $this->db->escape($start_date) . "' AND '" . $this->db->escape($end_date) . "' ORDER BY created_at DESC LIMIT " . $this->db->escape($start) . ", " . $this->db->escape($limit));
        return $this->getHelper($rows);
    }

    public function getAllByMonth($month, $start, $limit) {
        $start_date = "$month-01";
        $end_date = date("Y-m-t", strtotime($start_date));

        $rows = $this->db->select("SELECT * FROM rss WHERE created_at BETWEEN '" . $this->db->escape($start_date) . "' AND '" . $this->db->escape($end_date) . "' ORDER BY created_at DESC LIMIT " . $this->db->escape($start) . ", " . $this->db->escape($limit));
        return $this->getHelper($rows);
    }

    public function getAllByDay($day, $start, $limit) {
        $rows = $this->db->select("SELECT * FROM rss WHERE created_at='" . $this->db->escape($day) . "' ORDER BY created_at DESC LIMIT " . $this->db->escape($start) . ", " . $this->db->escape($limit));
        return $this->getHelper($rows);
    }

    public function getCategories() {
        $rows = $this->db->select("SELECT DISTINCT category FROM rss");

        $arr = [];
        foreach($rows as $row) {
            $arr[$row["category"]] = urlencode($row["category"]);
        }
        return $arr;
    }

    public function deleteFeed() {
        $id = Request::get("id");

        $this->db->delete("DELETE FROM rss WHERE feed_id='" . $this->db->escape($id) . "'");
        $this->db->delete("DELETE FROM feeds WHERE id='" . $this->db->escape($id) . "'");

        $_SESSION["msg"] = "Feed was deleted";
    }

    public function saveFeed() {
        $url = Request::post("url");
        $category = Request::post("category");
        $tags = Request::post("tags");

        $values = [
            "url" => $url,
            "category" => $category,
            "tags" => $tags
        ];

        $id = $this->db->insert("feeds", $values);

        $this->importSingleFeed($id, $url, $category, $tags);

        $_SESSION["msg"] = "Feeds was updated";
    }

    public function getFeeds() {
        return $this->db->select("SELECT * FROM feeds");
    }

    public function refreshFeeds() {
        $feeds = $this->getFeeds();
        foreach($feeds as $entry) {
            $this->importSingleFeed($entry["id"], $entry["url"], $entry["category"], $entry["tags"]);
        }
    }

    public function importSingleFeed($feed_id, $url, $category, $tags) {
        $date = date("Y-m-d");
        $values = [
            "updated_at" => $date
        ];
        $this->db->update("feeds", $values,"WHERE id='" . $this->db->escape($feed_id) . "'");

        $feeds = @simplexml_load_file($url);

        if(!empty($feeds)) {
            $site = $feeds->channel->title;
            $sitelink = $feeds->channel->link;

            $items = null;
            if(isset($feeds->channel->item)) {
                $items = $feeds->channel->item;
            } else {
                $items = $feeds->entry;
            }

            foreach ($items as $item) {
                $title = $item->title;

                $url = $item->link;
                if(isset($item->link['href'])) {
                    $url = $item->link['href'];
                }

                $description = null;
                if(isset($item->description)) {
                    $description = $item->description;
                } else if(isset($item->content)) {
                    $description = $item->content;
                }

                $date = null;
                if(isset($item->pubDate)) {
                    $date = $item->pubDate;
                } else if(isset($item->updated)) {
                    $date = $item->updated;
                }

                if($date == "") {
                    $date = date("Y-m-d");
                } else {
                    $date = date("Y-m-d", strtotime($date));
                }

                $this->saveSummary(null, $feed_id, $title, $url, $tags, $category, $description, "", $date);
            }
        }
    }
}

