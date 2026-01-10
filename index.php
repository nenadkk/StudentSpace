<?php

require_once "dbConnect.php";
require_once "tool.php";

use DB\DBAccess;

$htmlPage = file_get_contents("pages/index.html");

$dbAccess = new DBAccess();
if (!$dbAccess->openDBConnection()) {
    Tool::renderError(500);
}
$cardsData = $dbAccess->getLastAnnouncements();
$dbAccess->closeConnection();

$cards = "";
$cardhtml = "";

if($cardsData !== false) {
    $cards = Tool::createCard($cardsData);
} else {
    $cards = '<li class="centered">
                    <p>Nessun annuncio.</p>
                    <div class="azioni">
                        <a class="link btn-base call-to-action" href="pubblica.php">Pubblica un annuncio</a>
                    </div>
            </li>';
}

$htmlPage = str_replace("[Cards]", $cards, $htmlPage);

$htmlPage = str_replace("[TopNavBar]", Tool::buildTopNavBar("index"), $htmlPage);
$htmlPage = str_replace("[BottomNavBar]", Tool::buildBottomNavBar("index"), $htmlPage);

echo $htmlPage;