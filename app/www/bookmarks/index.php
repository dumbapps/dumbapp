<?php
include("../include/Request.php");
include("../include/Database.php");
include("../include/Utils.php");
include("../include/BookmarksController.php");

$page = Request::get("p", 1);
$limit = 25;
$start = ($page-1)*$limit;

$category = Request::get("category");
$from = Request::get("from");
$tag = Request::get("tag");
$year = Request::get("year");
$month = Request::get("month");
$day = Request::get("day");
$query = Request::get("query");

$BookmarkController = new BookmarksController();

if($category) {
    $bookmarks = $BookmarkController->getAllByCategory($category);
    $h1 = $category;
} else if($from) {
    $bookmarks = $BookmarkController->getAllByFrom($from);
    $h1 = $from;
} else if($tag) {
    $bookmarks = $BookmarkController->getAllByTag($tag);
    $h1 = $tag;
} else if($year) {
    $bookmarks = $BookmarkController->getAllByYear($year);
    $h1 = $year;
} else if($month) {
    $bookmarks = $BookmarkController->getAllByMonth($month);
    $h1 = $month;
} else if($day) {
    $bookmarks = $BookmarkController->getAllByDay($day);
    $h1 = $day;
} else if($query) {
    $bookmarks = $BookmarkController->search();
    $h1 = $query;
} else {
    $bookmarks = $BookmarkController->getAll($start, $limit);
    $h1 = "Bookmarks";
}

$categories = $BookmarkController->getCategories();
?>

<?include("../header.php")?>

<h1><?=$h1?></h1>

<div class="row">
    <?if(count($categories) > 0) {?>
        <div class="col-3" style="background-color: #e7e9eb;">
            <?foreach($categories as $name => $url) {?>
                <a href="?category=<?=$url?>"><?=$name?></a><br>
            <?}?>
        </div>
    <?}?>
    <div class="col">
        <ol class="bookmark" start="<?=$start+1?>">
            <?foreach($bookmarks as $entry) {?>
                <li id="link<?=$entry["id"]?>">
                    <h5><a target="_blank" href="<?=$entry["url"]?>"><?=$entry["title"]?></a> <small>(<a href="?from=<?=$entry["from"]["url"]?>"><?=$entry["from"]["name"]?></a>)</small></h5>

                    <p>
                        <small>
                            <a href="?year=<?=$entry["year"]["url"]?>"><?=$entry["year"]["name"]?></a>-
                            <a href="?month=<?=$entry["month"]["url"]?>"><?=$entry["month"]["name"]?></a>-
                            <a href="?day=<?=$entry["day"]["url"]?>"><?=$entry["day"]["name"]?></a>
                            &nbsp;|&nbsp;
                            <?if($entry["category"]) {?>
                                <a href="?category=<?=$entry["cat"]["url"]?>"><?=$entry["category"]?></a>
                                &nbsp;|&nbsp;
                            <?}?>
                            <?
                            if($entry["tags"]) {
                                $tags = "";
                                foreach ($entry["tag_list"] as $entry2) {
                                    $tags .= '<a href="?tag=' . $entry2["url"] . '">' . $entry2["name"] . "</a>, ";
                                }
                                $tags = rtrim($tags, ", ");
                                echo $tags;
                                echo "&nbsp;|&nbsp";
                            }
                            ?>
                            <?if($entry["notes"]) {?>
                                <?=$entry["notes"]?>
                                &nbsp;|&nbsp;
                            <?}?>
                            <a target="_blank" href="cache.php?url=<?=$entry["url"]?>">cache</a>
                            &nbsp;|&nbsp;
                            <a href="post.php?id=<?=$entry["id"]?>">edit</a>
                            &nbsp;|&nbsp;
                            <span class="link" onClick="deleteBookmark(<?=$entry["id"]?>)">delete</span>
                        </small>
                    </p>
                </li>
            <?}?>
        </ol>

        <?if(count($bookmarks) == 25) {?>
            <a href="?p=<?=$page+1?>">More</a>
        <?}?>
    </div>
</div>

<br>

<form method="get" action="/bookmarks/index.php">
    <div class="row">
        <div class="col">
            <input name="query" type="search" value="">
        </div>
        <div class="col-1">
            <button>Search</button>
        </div>
    </div>
</form>

<br>
<br>
<p><a href="settings.php">Settings</a> &nbsp; <a href="bookmarklet.php">Bookmarklet</a> &nbsp; <a href="import.php">Import Bookmarks</a></p>

<script>
function deleteBookmark(id) {
    if (id.length != 0) {
        if(confirm("Are you sure?")) {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("link"+id).innerHTML = this.responseText;
                }
            };
            xmlhttp.open("GET", "/bookmarks/post.php?action=delete&id=" + id, true);
            xmlhttp.send();
        }
    }
    return false;
}
</script>

<?include("../footer.php")?>