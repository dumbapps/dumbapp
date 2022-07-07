<?php
include("../include/Request.php");
include("../include/Database.php");
include("../include/Utils.php");
include("../include/BookmarksController.php");

$BookmarksController = new BookmarksController();

$action = Request::all("action");
switch($action) {
    case "save":
        $BookmarksController->saveSettings();
        break;
}

$settings = $BookmarksController->getSettings();

$h1 = "Settings";

?>

<?include("../header.php")?>

<h1><?=$h1?></h1>

<form method="post" action="<?=$_SERVER['PHP_SELF']?>">
    <input type="hidden" name="action" value="save">

    <label for="cache">Cache URLs?</label><br>
    <input id="cache" type="checkbox" name="cache" value="1" <?if($settings["cache"] == "1") { echo " checked"; }?>><br><br>

    <label for="cache_type">Cache Type</label><br>
    <select id="cache_type" name="cache_type">
        <option value="pdf" <?if($settings["cache_type"] == "pdf") { echo " selected"; }?>>PDF</option>
        <option value="png" <?if($settings["cache_type"] == "png") { echo " selected"; }?>>PNG</option>
    </select><br><br>

    <button>Save</button>
</form>


<?include("../footer.php")?>