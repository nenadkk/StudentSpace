<?php

require_once "tool.php";

$htmlPage = file_get_contents(__DIR__ . "/pages/chiSiamo.html");

$htmlPage = str_replace("[TopNavBar]", Tool::buildTopNavBar("chiSiamo"), $htmlPage);
$htmlPage = str_replace("[BottomNavBar]", Tool::buildBottomNavBar("chiSiamo"), $htmlPage);

echo $htmlPage;