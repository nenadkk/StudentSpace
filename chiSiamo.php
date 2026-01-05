<?php

require_once "dbConnect.php";
require_once "tool.php";

use DB\DBAccess;

$htmlPage = file_get_contents("pages/chiSiamo.html");

$dbAccess = new DBAccess();
if (!$dbAccess->openDBConnection()) {
    Tool::renderError(500);
}
$cardsData = $dbAccess->getLastAnnouncements();
$dbAccess->closeConnection();

$htmlPage = str_replace("[TopNavLog]", Tool::getTopNavLog(), $htmlPage);
$htmlPage = str_replace("[BottomNavLog]", Tool::getBottomNavLog(), $htmlPage);

echo $htmlPage;