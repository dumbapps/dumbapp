<?php

class SearchController {
    private $db;

    public function __construct() {
    }

    public function blockHostname() {
        $hostname = Request::get("hostname");
        $file = file_put_contents("exclude.txt", $hostname.PHP_EOL , FILE_APPEND | LOCK_EX);
        echo "<h5>$hostname is blocked.</h5>";
        exit;
    }

    public function saveExclusions() {
        $exclusions = Request::post("exclusions");

        file_put_contents("exclude.txt", $exclusions);

        $_SESSION["msg"] = "Exclusion list updated";
    }

    public function getExclusions() {
        return file_get_contents("exclude.txt");
    }

    public function search() {
        $key = "AIzaSyA36LnqiFNt1bz5BQ_aK2ZaXn-XryB3VHY";
        $cx = "ec7b8149a218a0676";

        $query = Request::get("q");
        $start = Request::get("start", 1);

        $data = file_get_contents("exclude.txt");
        $exclude = explode("\n", $data);

        $exclusions = "";
        foreach($exclude as $item) {
            if($item != "") {
                $exclusions .= "-site:" . $item . " ";
            }
        }

        $google_url = "https://www.googleapis.com/customsearch/v1?key=" . urlencode($key) . "&cx=$cx&start=" . $start . "&q=" . urlencode($query . " " . $exclusions);

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