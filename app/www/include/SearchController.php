<?php

class SearchController {
    private $db;

    public function __construct() {
        $this->db = new Database("sqlite: settings.db");

        if(!file_exists("settings.txt")) {
            $sql = "CREATE TABLE \"settings\" (
                    \"id\"	INTEGER PRIMARY KEY AUTOINCREMENT,
                    \"api_key\" TEXT,
	                \"cx\"	TEXT,
	                \"exclusions\"	TEXT
                    );
            ";
            $this->db->query($sql);
            file_put_contents("settings.txt", "");
        }
    }

    public function blockHostname() {
        $hostname = Request::get("hostname");

        $row = $this->db->selectSingle("SELECT * FROM settings");
        if($row) {
            $exclusions = $row["exclusions"];
            $api_key = $row["api_key"];
        } else {
            echo "<h5>Please update settings.</h5>";
            exit;
        }

        $values = [
            'exclusions' => $exclusions . "\n" . $hostname
        ];
        $this->db->update("settings", $values, "WHERE api_key=:api_key", ["api_key" => $api_key]);

        echo "<h5>$hostname is blocked.</h5>";
        exit;
    }

    public function save() {
        $api_key = Request::post("api_key");
        $cx = Request::post("cx");
        $exclusions = Request::post("exclusions");

        $values = [
            'api_key' => $api_key,
            'cx' => $cx,
            'exclusions' => $exclusions
        ];
        $this->db->insert("settings", $values);

        $_SESSION["msg"] = "Settings was updated";
    }

    public function getSettings() {
        return $this->db->selectSingle("SELECT * FROM settings");
    }

    public function search() {
        $row = $this->db->selectSingle("SELECT * FROM settings");
        if($row) {
            $key = $row["api_key"];
            $cx = $row["cx"];
            $exclusions = $row["exclusions"];
        } else {
            return array();
        }

        $query = Request::get("q");
        $start = Request::get("start", 1);

        $tmp = explode("\n", $exclusions);

        $excludes = "";
        foreach($tmp as $item) {
            if($item != "") {
                $excludes .= "-site:" . $item . " ";
            }
        }

        $google_url = "https://www.googleapis.com/customsearch/v1?key=" . urlencode($key) . "&cx=$cx&start=" . $start . "&q=" . urlencode($query . " " . $excludes);

        $data = file_get_contents($google_url);
        $json = json_decode($data, true);

        $results = $json["items"];
        $totalResults = $json["searchInformation"]["totalResults"];
        $searchTime = $json["searchInformation"]["searchTime"];

        for($i=0; $i<count($results); $i++) {
            $url_info = parse_url($results[$i]["link"]);
            $results[$i]["hostname"] = $url_info["host"];
        }

        $arr["total"] = $totalResults;
        $arr["time"] = $searchTime;
        $arr["results"] = $results;

        return $arr;
    }

    public function pagination() {
        $query = Request::get("q");
        $start = Request::get("start");
        $page = floor($start / 10) + 1;

        $arr["previous"] = null;
        if($start != 1) {
            $arr["previous"] = $_SERVER["PHP_SELF"] . "?q=" . $query . "&start=" . ($start - 10);
        }
        $arr["next"] = $_SERVER["PHP_SELF"] . "?q=" . $query . "&start=" . ($start + 10);

        for($i=1, $j=1; $i<=10; $i++, $j=$j+10) {
            $tmp["name"] = $i;
            $tmp["url"] = $_SERVER["PHP_SELF"] . "?q=" . $query . "&start=" . $j;
            $tmp["current"] = false;
            if ($i == $page) {
                $tmp["current"] = true;
            }
            $arr["pages"][] = $tmp;
        }

        return $arr;
    }
}