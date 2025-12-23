<?php

require_once "dbConnect.php";
require_once "tool.php";

use DB\DBAccess;

$dbAccess = new DBAccess();

$htmlPage = file_get_contents("pages/esplora.html");
$inputCerca = "";
$categoria = "";
$cards = "";
$cerca = "";
$filtri = array();

if(isset($_GET['submit'])) 
{
    $cerca = $_GET['cerca'] ?? "";
    $filtri = array("categoria"=> $_GET['categoria'] ?? "",
                "citta"=> $_GET['citta'] ?? "",
                "pubblicazione-inizio"=> $_GET['pubblicazione-inizio'] ?? "",
                "pubblicazione-fine"=> $_GET['pubblicazione-fine'] ?? "",

                "coinquilini-max"=> $_GET['coinquilini-max'] ?? "",
                "costo-mese-affitto-max"=> $_GET['costo-mese-affitto-max'] ?? "",
                "indirizzo-affitto"=> $_GET['indirizzo-affitto'] ?? "",

                "laboratorio"=> $_GET['laboratorio'] ?? "",
                "esperimento-durata-min"=> $_GET['esperimento-durata-min'] ?? "",
                "esperimento-durata-max"=> $_GET['esperimento-durata-max'] ?? "",
                "esperimento-compenso-min"=> $_GET['esperimento-compenso-min'] ?? "",
                "esperimento-compenso-max"=> $_GET['esperimento-compenso-max'] ?? "",

                "evento-inizio"=> $_GET['evento-inizio'] ?? "",
                "evento-fine"=> $_GET['evento-fine'] ?? "",
                "evento-costo-max"=> $_GET['evento-costo-max'] ?? "",
                "luogo-evento"=> $_GET['luogo-evento'] ?? "",

                "materia"=> $_GET['materia'] ?? "",
                "livello"=> $_GET['livello'] ?? "",
                "prezzo-ripetizioni-max"=> $_GET['prezzo-ripetizioni-max'] ?? "");
}
else
{
    //nel caso non siano stati applicati filtri o ricerche mostro tutti gli annunci presenti
    $dbAccess->openDBConnection();
    $cardsData = $dbAccess->getAnnouncements();
    $dbAccess->closeConnection();

    if($cardsData !== false)
        $cards = Tool::createCard($cardsData);
    else 
        $cards .= file_get_contents("pages/cardTemplate.html");
}

$htmlPage = str_replace("[cerca]", $cerca, $htmlPage);

foreach ($filtri as $key => $value) {
    $htmlPage = str_replace("[$key]", $value, $htmlPage);
}


//INSERIMENTO CARDS
$htmlPage = str_replace("[Cards]", $cards, $htmlPage);

$htmlPage = str_replace("[TopNavLog]", Tool::getTopNavLog(), $htmlPage);
$htmlPage = str_replace("[BottomNavLog]", Tool::getBottomNavLog(), $htmlPage);

echo $htmlPage;
?>
