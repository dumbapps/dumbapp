<?php
include("../include/Request.php");
include("../include/Database.php");
include("../include/Utils.php");
include("../include/RssController.php");

$id = Request::all("id");
$title = Request::all("title");
$url = Request::all("url");
$tags = Request::all("tags");
$category = Request::all("category");
$description = Request::all("description");
$notes = Request::all("notes");
$date = Request::all("date");

$RssController = new RssController();

$action = Request::all("action");
switch($action) {
    case "save":
        $RssController->saveSummary(null, $id, $title, $url, $tags, $category, $description, $notes, $date);
        break;
    case "delete":
        $RssController->deleteSummary();

        echo "Deleted";
        exit;
        break;

    default:
        $h1 = "Edit Summary";
        break;
}

if($id) {
    $h1 = "Edit Summary";
    $item = $RssController->get($id);
    $title = $item["title"];
    $url = $item["url"];
    $tags = $item["tags"];
    $category = $item["category"];
    $description = $item["description"];
    $notes = $item["notes"];
    $date = $item["created_at"];
}
?>

<?include("../header.php")?>

<h1><?=$h1?></h1>

<form action="summary.php" method="post">
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="id" value="<?=$id?>">

    <label for="title">Title:</label>
    <input name="title" id="title" type="text" value="<?=$title?>">

    <label for="url">URL:</label>
    <input name="url" id="url" type="text" value="<?=$url?>">

    <label for="category">Category:</label>
    <input name="category" id="category" type="text" value="<?=$category?>">

    <label for="tags">Tags:</label>
    <input id="tags" type="text" name="tags" value="<?=$tags?>">

    <label for="notes">Notes:</label>
    <input name="notes" id="notes" type="text" value="<?=$notes?>">

    <label for="description">Description:</label>
    <textarea name="description" id="description"><?=$description?></textarea>

    <label for="date">Date:</label>
    <input name="date" id="date" type="date" value="<?=$date?>">

    <button>Submit</button>
</form>

<?include("../header.php")?>