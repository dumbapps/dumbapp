<?php
include("../include/Request.php");
include("../include/Database.php");
include("../include/Utils.php");
include("../include/SearchController.php");

$action = Request::all("action");
$title = "Exclusion List";

$SearchController = new SearchController();
switch($action) {
    case "block":
        $SearchController->blockHostname();
        break;
    case "save":
        $SearchController->saveExclusions();
        break;
}

$exclusions = $SearchController->getExclusions();

$title = "Exclusion List";

?>

<?include("../header.php")?>

<h1>Exclusion List</h1>

<form method="post" action="<?=$_SERVER['PHP_SELF']?>">
    <input type="hidden" name="action" value="save">
    <textarea name="exclusions"><?=$exclusions?></textarea>

    <button>Save</button>
</form>


<?include("../footer.php")?>