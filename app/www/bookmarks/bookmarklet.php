<?php
include("../include/Request.php");
include("../include/Database.php");
include("../include/Utils.php");
include("../include/BookmarksController.php");

$bookmarklet = "javascript:(function(){f='" . BASE_URL . "/bookmarks/post.php?bookmarklet=1&url='+encodeURIComponent(window.location.href)+'&title='+encodeURIComponent(document.title)+encodeURIComponent(''+(window.getSelection?window.getSelection():document.getSelection?document.getSelection():document.selection.createRange().text))+'&v=6&';a=function(){if(!window.open(f+'noui=1&jump=doclose','popupiv6','location=yes,links=no,scrollbars=no,toolbar=no,width=550,height=300'))location.href=f+'jump=yes'};if(/Firefox/.test(navigator.userAgent)){setTimeout(a,0)}else{a()}})()";

$h1 = "Bookmarklet";
?>

<?include("../header.php")?>

<h1><?=$h1?></h1>

<p>Drag this button to your bookmarks bar.</p>
<br>
<p><a href="<?=$bookmarklet?>"><button>Add Bookmark</button></a></p>
<br>

<textarea><?=$bookmarklet?></textarea>

<?include("../footer.php")?>