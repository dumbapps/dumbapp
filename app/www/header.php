<!DOCTYPE html>
<html lang="en">
<head>
    <title><?=$h1?></title>
    <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
    <link rel="stylesheet" href="/assets/style.css">
    <style>
        textarea {
            width: 100%;
            height: 600px;
        }
    </style>

</head>
<body>
<nav>
    <ul>
        <li><a href="/search/">Search</a></li>
        <li><a href="/bookmarks/">Bookmarks</a>
            <ul>
                <li><a href="/bookmarks/post.php">Add Bookmark</a></li>
            </ul>
        </li>
        <li><a href="/rss/">RSS</a>
            <ul>
                <li><a href="/rss/feed.php">Add Feed</a></li>
                <li><a href="/rss/refresh.php">Refresh Feeds</a></li>
            </ul>
        </li>
    </ul>
</nav>

<?alert()?>