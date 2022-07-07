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
        <div class="col-2"><b>Category</b></div>
        <div class="col-2"><b>Tags</b></div>
        <div class="col-2"><b>Last Updated</b></div>
        <div class="col-1"><b>Delete</b></div>
    </div>
    <?foreach($feeds as $entry) {?>
        <div class="row">
            <div class="col"><?=$entry["url"]?></div>
            <div class="col-2"><?=$entry["category"]?></div>
            <div class="col-2"><?=$entry["tags"]?></div>
            <div class="col-2"><?=$entry["updated_at"]?></div>
            <div class="col-1"><a href="feed.php?action=delete&id=<?=$entry["id"]?>">Delete</a></div>
        </div>
    <?}?>
    <div class="row">
        <div class="col"><input type="text" name="url" value=""></div>
        <div class="col-2"><input type="text" name="category" value=""></div>
        <div class="col-2"><input type="text" name="tags" value=""></div>
        <div class="col-2">&nbsp; </div>
        <div class="col-1">&nbsp; </div>
    </div>

    <button>Save</button>
</form>

<?include("../footer.php")?>
