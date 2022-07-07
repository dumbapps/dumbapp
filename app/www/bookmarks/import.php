<?php
include("../include/Request.php");
include("../include/Database.php");
include("../include/Utils.php");
include("../include/BookmarksController.php");

$action = Request::all("action");
$message = "";

switch($action) {
    case "import":
        $BookmarkController = new BookmarksController();
        $BookmarkController->import();
        break;
    default:
        break;
}

$h1 = "Import Bookmarks";
?>

<?include("../header.php")?>

<h1><?=$h1?></h1>

<p>Import your Chrome or Firefox bookmarks.</p>

<form action="import.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="action" value="import">
    <label for="file">File:</label>
    <input name="file" id="file" type="file" value="">
    <button>Submit</button>
</form>

<?include("../footer.php")?>