<?php

require_once "dbConnect.php";
require_once "tool.php";

use DB\DBAccess;

$htmlPage = file_get_contents("pages/index.html");

$dbAccess = new DBAccess();
if (!$dbAccess->openDBConnection()) {
    echo "Connessione al database fallita.";
    exit;
}
$cardsData = $dbAccess->getLastAnnouncements();
$dbAccess->closeConnection();

$cards = "";
$cardhtml = "";

if($cardsData !== false) {
    $cards = Tool::createCard($cardsData);
} else {
    $cardhtml = file_get_contents("pages/cardTemplate.html");
    $cards .= $cardhtml;
}

$htmlPage = str_replace("[cards]", $cards, $htmlPage);

echo $htmlPage;