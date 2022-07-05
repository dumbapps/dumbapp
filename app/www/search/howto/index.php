<?php
include("../../include/Utils.php");

$title = "How to setup search";
?>

<?include("../../header.php")?>

<h1>How to setup search</h1>

<ol type="1">
    <li>Go to <a target="_blank" href="https://programmablesearchengine.google.com/cse/create/new">https://programmablesearchengine.google.com/cse/create/new</a></li>
    <li><img src="step1.png" width="75%">
        <br>
        Enter a name for the Search engine name<br>
        Click "Search the web" for What to search?<br>
        Click the Create button<br>
        <br>
        <br>
    </li>
    <li><img src="step2.png" width="75%">
        <br>
        Copy the cx ID found in the URL (ex. 86ad2e8f479834a3f)<br>
        <br>
        <br>
    </li>
    <li>Go to <a target="_blank" href="https://developers.google.com/custom-search/v1/introduction">https://developers.google.com/custom-search/v1/introduction</a></li>
    <li><img src="step3.png" width="75%">
        <br>
        Click the Get Key button<br>
        <br>
        <br>
    </li>
    <li><img src="step4.png" width="75%">
        <br>
        Enter a name for the project name<br>
        Click Yes to agree to the Terms of Service<br>
        Click the Next button<br>
        <br>
        <br>
    </li>
    <li><img src="step5.png" width="75%">
        <br>
        Click the Show Key button<br>
        <br>
        <br>
    </li>
    <li><img src="step6.png" width="75%">
        <br>
        Copy the API Key<br>
        <br>
        <br>
    </li>
    <li>
        Enter the CX ID and API Key into the <a target="_blank" href="../settings.php">settings</a> page
    </li>
</ol>


<?include("../../footer.php")?>
