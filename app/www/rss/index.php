<?php
include("../include/Request.php");
include("../include/Database.php");
include("../include/Utils.php");
include("../include/RssController.php");

$page = Request::get("p", 1);
$limit = 25;
$start = ($page-1)*$limit;

$category = Request::get('category');
$from = Request::get('from');
$tag = Request::get('tag');
$year = Request::get('year');
$month = Request::get('month');
$day = Request::get('day');
$query = Request::get("query");

$RssController = new RssController();

if($category) {
    $rss = $RssController->getAllByCategory($category, $start, $limit);
    $next_url = "?category=$category&p=" . ($page + 1);
    $h1 = $category;
} else if($from) {
    $rss = $RssController->getAllByFrom($from, $start, $limit);
    $next_url = "?from=$from&p=" . ($page + 1);
    $h1 = "From";
} else if($tag) {
    $rss = $RssController->getAllByTag($tag, $start, $limit);
    $next_url = "?tag=$tag&p=" . ($page + 1);
    $h1 = $tag;
} else if($year) {
    $rss = $RssController->getAllByYear($year, $start, $limit);
    $next_url = "?year=$year&p=" . ($page + 1);
    $h1 = $year;
}  else if($month) {
    $rss = $RssController->getAllByMonth($month, $start, $limit);
    $next_url = "?month=$month&p=" . ($page + 1);
    $h1 = $month;
}  else if($day) {
    $rss = $RssController->getAllByDay($day, $start, $limit);
    $next_url = "?day=$day&p=" . ($page + 1);
    $h1 = $day;
} else if($query) {
    $rss = $RssController->search($start, $limit);
    $next_url = "?p=" . ($page + 1);
    $h1 = $query;
} else {
    $rss = $RssController->getAll($start, $limit);
    $next_url = "?p=" . ($page + 1);
    $h1 = "RSS";
}

$categories = $RssController->getCategories();;
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
        <ol class="rss" start="<?=$start+1?>">
            <?foreach($rss as $entry) {?>
                <li id="link<?=$entry['id']?>">
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
                            <a href="#" onClick="hide(<?=$entry["id"]?>)">summary</a>
                            &nbsp;|&nbsp;
                            <a href="summary.php?id=<?=$entry["id"]?>">edit</a>
                            &nbsp;|&nbsp;
                            <a href="#" onClick="deleteSummary(<?=$entry["id"]?>)">delete</a>
                        </small>
                    </p>

                    <div id="summary<?=$entry['id']?>" style="display: none;">
                        <p><?=$entry["description"]?></p>
                    </div>
                </li>
            <?}?>
        </ol>

        <?if(count($rss) == 25) {?>
            <a href="<?=$next_url?>">More</a>
        <?}?>
    </div>
</div>

<br>

<form method="get" action="/rss/index.php">
    <div class="row">
        <div class="col">
            <input name="query" type="search" value="">
        </div>
        <div class="col-1">
            <button>Search</button>
        </div>
    </div>
</form>

<script>
function deleteSummary(id) {
    if (id.length != 0) {
        if(confirm("Are you sure?")) {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("link"+id).innerHTML = this.responseText;
                }
            };
            xmlhttp.open("GET", "/rss/summary.php?action=delete&id=" + id, true);
            xmlhttp.send();
        }
    }
    return false;
}
function hide(id) {
    var x = document.getElementById("summary"+id);
    if (x.style.display === "none") {
        x.style.display = "block";
    } else {
        x.style.display = "none";
    }
}
</script>

<?include("../footer.php")?>