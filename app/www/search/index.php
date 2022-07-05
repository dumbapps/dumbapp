<?php
include("../include/Request.php");
include("../include/Database.php");
include("../include/Utils.php");
include("../include/SearchController.php");

$action = Request::get("action");
$query = Request::get("q");
$title = "Search";

if($query) {
    $SearchController = new SearchController();
    $items = $SearchController->search();
    $pagination = $SearchController->pagination();
    $title = "Search results for $query";
}

?>

<?include("../header.php")?>

<form method="get" action="<?=$_SERVER["PHP_SELF"]?>">
    <div class="row">
        <div class="col">
            <input type="text" name="q" value="<?=$query?>" />
        </div>
        <div class="col-1">
            <button>Search</button>
        </div>
    </div>
</form>

<?if($query) {?>
    <p>About <?=$items["total"]?> results (<?=$items["time"]?> seconds) </p>

    <?foreach($items["results"] as $entry) {?>
        <div id="<?=$entry["hostname"]?>">
            <h5><a href="<?=$entry["link"]?>"><?=$entry["title"]?></a></h5>

            <a href="<?=$entry["link"]?>"><?=$entry["link"]?></a> &nbsp; <a href="#" onClick="blockHostname('<?=$entry["hostname"]?>')">(block)</a>
            <p><?=$entry["htmlSnippet"]?></p>
        </div>
    <?}?>

    <br>
    <br>

    <?if($pagination["previous"]) {?>
        <a href="<?=$pagination["previous"]?>">Previous</a>&nbsp; &nbsp;
    <?}?>
    <?
    foreach($pagination["pages"] as $entry) {
        if ($entry["current"]) {
            echo '<a href="' . $entry["url"] . '"><b>' . $entry["name"] . '</b></a>&nbsp; &nbsp; ';
        } else {
            echo '<a href="' . $entry["url"] . '">' . $entry["name"] . '</a>&nbsp; &nbsp; ';
        }
    }
    ?>
    &nbsp; &nbsp; <a href="<?=$pagination["next"]?>">Next</a>
<?}?>

<br>
<br>
<p><a href="exclude.php">Exclusion List</a></p>

<script>
function blockHostname(hostname) {
    if (hostname.length != 0) {
        if(confirm("Block " + hostname + "?")) {
            xmlhttp = new XMLHttpRequest;
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById(hostname).innerHTML = this.responseText;
                }
            };
            xmlhttp.open("GET", "exclude.php?action=block&hostname=" + hostname, true);
            xmlhttp.send();
        }
    }
    return false;
}
</script>

<?include("../footer.php")?>