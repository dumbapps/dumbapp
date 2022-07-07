<?php
include("../include/Request.php");
include("../include/Database.php");
include("../include/Utils.php");
include("../include/BookmarksController.php");

$BookmarksController = new BookmarksController();

$url = Request::get("url");
$screenshot = preg_replace("/[^a-z\d]/i", "_", $url);

if(!file_exists(ARCHIVE_LOCATION . $screenshot . ".png") && !file_exists(ARCHIVE_LOCATION . $screenshot . ".pdf")) {
    $BookmarksController->cache($url);
}

if(file_exists(ARCHIVE_LOCATION . $screenshot . ".pdf")) {
    header("Location: /cache/" . $screenshot . ".pdf");
} else if(file_exists(ARCHIVE_LOCATION . $screenshot . ".png")) {
    header("Location: /cache/" . $screenshot . ".png");
} else {
    echo "no cache";
}
exit;