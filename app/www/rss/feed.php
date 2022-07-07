<?php
include("../include/Request.php");
include("../include/Database.php");
include("../include/Utils.php");
include("../include/RssController.php");

$RssController = new RssController();

$action = Request::all("action");
switch($action) {
    case "save":
        $RssController->saveFeed();
        break;
    case "delete":
        $RssController->deleteFeed();
        break;
    default:
        break;
}

$feeds = $RssController->getFeeds();

$h1 = "Add Feed";
?>

<?include("../header.php")?>

<h1><?=$h1?></h1>

<form method="post" action="feed.php">
<input type="hidden" name="action" value="save">

    <div class="row">
        <div class="col"><b>Feed URL</b></div>
        <div class="col"><b>Category</b></div>
        <div class="col"><b>Tags</b></div>
        <div class="col"><b>Last Updated</b></div>
        <div class="col"><b>Delete</b></div>
    </div>
    <?foreach($feeds as $entry) {?>
        <div class="row">
            <div class="col"><?=$entry["url"]?></div>
            <div class="col"><?=$entry["category"]?></div>
            <div class="col"><?=$entry["tags"]?></div>
            <div class="col"><?=$entry["updated_at"]?></div>
            <div class="col"><a href="feed.php?action=delete&id=<?=$entry["id"]?>">Delete</a></div>
        </div>
    <?}?>
    <div class="row">
        <div class="col"><input id="url" type="text" name="url" value=""></div>
        <div class="col"><input id="category" type="text" name="category" value=""></div>
        <div class="col"><input id="tags" type="text" name="tags" value=""></div>
        <div class="col"></div>
        <div class="col"></div>
    </div>

    <button>Save</button>
</form>

<?include("../footer.php")?>
