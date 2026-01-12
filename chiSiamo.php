<?php

require_once "dbConnect.php";
require_once "tool.php";

$htmlPage = file_get_contents("pages/chiSiamo.html");

$htmlPage = str_replace("[TopNavBar]", Tool::buildTopNavBar("chiSiamo"), $htmlPage);
$htmlPage = str_replace("[BottomNavBar]", Tool::buildBottomNavBar("chiSiamo"), $htmlPage);

echo $htmlPage;