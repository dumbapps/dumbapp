<?php

class SearchController {
    private $db;

    public function __construct() {
        $db_location = realpath($_SERVER["DOCUMENT_ROOT"]) . "/settings.db";

        if(!file_exists($db_location)) {
            $this->db = new Database($db_location);
            $sql = "CREATE TABLE \"settings\" (
                    \"id\"	INTEGER PRIMARY KEY AUTOINCREMENT,
	                \"key\"	TEXT,
	                \"value\"	TEXT
                    );
            ";
            $this->db->run($sql);
        }

        $this->db = new Database($db_location);
    }

    public function blockHostname() {
        $hostname = Request::get("hostname");

        $arr = $this->getSettings();
        $arr["exclusions"] = $arr["exclusions"] . "\n" . $hostname;
        $values = [
            "key" => "search",
            "value" => json_encode($arr)
        ];

        $this->db->update("settings", $values, "WHERE key='search'");

        echo "<h5>$hostname is blocked.</h5>";
        exit;
    }

    public function save() {
        $api_key = Request::post("api_key");
        $cx = Request::post("cx");
        $exclusions = Request::post("exclusions");

        $arr = [
            "api_key" => $api_key,
            "cx" => $cx,
            "exclusions" => $exclusions,
        ];
        $values = [
            "key" => "search",
            "value" => json_encode($arr)
        ];

        $row = $this->db->selectSingle("SELECT * FROM settings WHERE key='search'");
        if($row) {
            $this->db->update("settings", $values, "WHERE key='search'");
        } else {
            $this->db->insert("settings", $values);
        }

        $_SESSION["msg"] = "Settings was updated";
    }

    public function getSettings() {
        $row = $this->db->selectSingle("SELECT * FROM settings WHERE key='search'");
        if($row) {
            $arr = json_decode($row["value"], true);
            return $arr;
        }

        return null;
    }

    public function search() {
        $row = $this->getSettings();
        if($row) {
            $key = $row["api_key"];
            $cx = $row["cx"];
            $exclusions = $row["exclusions"];
        } else {
            return [];
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