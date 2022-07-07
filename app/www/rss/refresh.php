<?php
include("../include/Request.php");
include("../include/Database.php");
include("../include/Utils.php");
include("../include/RssController.php");

$RssController = new RssController();

$RssController->refreshFeeds();

$h1 = "Refresh Feeds";
?>

<?include("../header.php")?>

<h1><?=$h1?></h1>

<p>Updated!</p>

<?include("../footer.php")?>