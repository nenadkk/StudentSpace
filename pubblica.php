<?php

require_once "tool.php";
require_once "dbConnect.php";

use DB\DBAccess;

$htmlPage = file_get_contents("pages/pubblica.html");

$htmlPage = str_replace("[TopNavLog]", Tool::getTopNavLog(), $htmlPage);
$htmlPage = str_replace("[BottomNavLog]", Tool::getBottomNavLog(), $htmlPage);

echo $htmlPage;

?>