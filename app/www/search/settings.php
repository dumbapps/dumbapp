<?php
include("../include/Request.php");
include("../include/Database.php");
include("../include/Utils.php");
include("../include/SearchController.php");

$SearchController = new SearchController();

$action = Request::all("action");
switch($action) {
    case "block":
        $SearchController->blockHostname();
        break;
    case "save":
        $SearchController->save();
        break;
}

$settings = $SearchController->getSettings();

$h1 = "Settings";

?>

<?include("../header.php")?>

<h1><?=$h1?></h1>

<form method="post" action="<?=$_SERVER['PHP_SELF']?>">
    <input type="hidden" name="action" value="save">

    <label for="api_key">API Key</label>
    <input id="api_key" type="text" name="api_key" value="<?=$settings['api_key']?>">

    <label for="cx">CX</label>
    <input id="cx" type="text" name="cx" value="<?=$settings['cx']?>">

    <label for="exclusions">Exclusion List</label>
    <textarea id="exclusions" name="exclusions"><?=$settings['exclusions']?></textarea>

    <button>Save</button>
</form>


<?include("../footer.php")?>