<?php

require_once "tool.php";

$htmlPage = file_get_contents(__DIR__ . "/pages/500.html");

$htmlPage = str_replace("[TopNavBar]", Tool::buildTopNavBar("500"), $htmlPage);
$htmlPage = str_replace("[BottomNavBar]", Tool::buildBottomNavBar("500"), $htmlPage);

echo $htmlPage;

?>