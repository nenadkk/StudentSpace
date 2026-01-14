<?php

require_once "tool.php";

$htmlPage = file_get_contents(__DIR__ . "/pages/404.html");

$htmlPage = str_replace("[TopNavBar]", Tool::buildTopNavBar("404"), $htmlPage);
$htmlPage = str_replace("[BottomNavBar]", Tool::buildBottomNavBar("404"), $htmlPage);

echo $htmlPage;

?>