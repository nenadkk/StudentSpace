<?php

require_once "dbConnect.php";
require_once "tool.php";

use DB\DBAccess;

$htmlPage = file_get_contents("pages/esplora.html");

$dbAccess = new DBAccess();
if (!$dbAccess->openDBConnection()) {
    echo "Connessione al database fallita.";
    exit;
}
$cardsData = $dbAccess->getAnnouncements();
$dbAccess->closeConnection();

$cards = "";

if($cardsData !== false) {
    $cards = Tool::createCard($cardsData);
} else {
    $cardhtml = file_get_contents("pages/cardTemplate.html");
    $cards .= $cardhtml;
}

$htmlPage = str_replace("[cards]", $cards, $htmlPage);

echo $htmlPage;
?>
