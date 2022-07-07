<?php
include("../include/Request.php");
include("../include/Database.php");
include("../include/Utils.php");
include("../include/BookmarksController.php");

$id = Request::all("id");
$bookmarklet = Request::all("bookmarklet");
$title = Request::all("title");
$url = Request::all("url");
$category = Request::all("category");
$date = Request::all("date", date("Y-m-d"));
$tags = Request::all("tags");
$notes = Request::all("notes");

$BookmarkController = new BookmarksController();

$action = Request::all("action");
switch($action) {
    case "add":
        $BookmarkController->save();

        $title = "";
        $url = "";
        $category = "";
        $tags = "";
        $notes = "";
        $cache = "";
        $date = "";
        break;
    case "delete":
        $BookmarkController->delete();
        echo "Deleted";
        exit;

    case "edit":
        $BookmarkController->save();

        break;

    default:
        $h1 = "Add Bookmark";
        break;
}

if($id) {
    $h1 = "Edit Bookmark";
    $item = $BookmarkController->get($id);
    $title = $item["title"];
    $url = $item["url"];
    $category = $item["category"];
    $date = $item["created_at"];
    $tags = $item["tags"];
    $notes = $item["notes"];
}

$settings = $BookmarkController->getSettings();
if($settings) {
    $cache = $settings["cache"];
} else {
    $cache = Request::all("cache");
}
?>

<?if($bookmarklet == "1") {?>

<html>
    <head>
        <title><?=$h1?></title>
        <?if($bookmarklet == "1" && $action == "add") {?><script>window.close();</script><?}?>
    </head>
    <body>
    <b><?=$h1?></b><br><br>
    <form action="post.php" method="post">
        <input type="hidden" name="action" value="add">
        <input type="hidden" name="bookmarklet" value="1">
        <table>
            <tr>
                <td><label for="title">Title:</label></td>
                <td><input name="title" id="title" type="text" value="<?=$title?>" style="width: 400px"></td>
            </tr>
            <tr>
                <td><label for="url">URL:</label></td>
                <td><input name="url" id="url" type="text" value="<?=$url?>" style="width: 400px"></td>
            </tr>
            <tr>
                <td><label for="category">Category:</label></td>
                <td><input name="category" id="category" type="text" value="<?=$category?>" style="width: 400px"></td>
            </tr>
            <tr>
                <td><label for="tags">Tags:</label></td>
                <td><input name="tags" id="tags" type="text" value="<?=$tags?>" style="width: 400px"></td>
            </tr>
            <tr>
                <td><label for="notes">Notes:</label></td>
                <td><input name="notes" id="notes" type="text" value="<?=$notes?>" style="width: 400px"></td>
            </tr>
            <tr>
                <td><label for="date">Date:</label></td>
                <td><input name="date" id="date" type="date" value="<?=$date?>" style="width: 400px"></td>
            </tr>
            <tr>
                <td><label for="cache">Cache:</label></td>
                <td><input name="cache" id="cache" type="checkbox" value="1" <?if($cache) { echo " checked"; }?>></td>
            </tr>
            <tr>
                <td></td>
                <td><button>Save</button></td>
            </tr>
        </table>
    </form>
    </body>
</html>

<?} else {?>

    <?include("../header.php");?>

    <h1><?=$h1?></h1>

    <form action="post.php" method="post">
        <input type="hidden" name="action" value="add">
        <?if($id) {?><input type="hidden" name="id" value="<?=$id?>"><?}?>

        <label for="title">Title</label>
        <input name="title" id="title" type="text" value="<?=$title?>">
        <label for="url">URL</label>
        <input name="url" id="url" type="text" value="<?=$url?>">
        <label for="category">Category</label>
        <input name="category" id="category" type="text" value="<?=$category?>">
        <label for="tags">Tags</label>
        <input name="tags" id="tags" type="text" value="<?=$tags?>">
        <label for="notes">Notes</label>
        <input name="notes" id="notes" type="text" value="<?=$category?>">
        <label for="date">Date</label>
        <input name="date" id="date" type="date" value="<?=$date?>">
        <label for="cache">Cache</label><br>
        <input name="cache" id="cache" type="checkbox" value="1" <?if($cache) { echo " checked"; }?>><br><br>
        <button>Save</button>
    </form>

    <?include("../footer.php")?>
<?}?>
