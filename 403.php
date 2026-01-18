<?php

require_once "tool.php";

$htmlPage = file_get_contents(__DIR__ . "/pages/403.html");

$htmlPage = str_replace("[TopNavBar]", Tool::buildTopNavBar("403"), $htmlPage);
$htmlPage = str_replace("[BottomNavBar]", Tool::buildBottomNavBar("403"), $htmlPage);

echo $htmlPage;

?>