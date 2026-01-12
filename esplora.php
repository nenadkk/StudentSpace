<?php

require_once "dbConnect.php";
require_once "tool.php";

use DB\DBAccess;
$db = new DBAccess();

$htmlPage = file_get_contents("pages/esplora.html");

$categoria = "";
$query = "";
$cardsData="";
$cards = "";
$numRisultati = "";
$oggi = date('Y-m-d');

$filtriGenerali = array(
                "cerca"=>"",
                "citta"=>"",
                "pubblicazione-inizio"=>"",
                "pubblicazione-fine"=>"");

$filtriAffitti = array(
                "coinquilini-max"=>"",
                "costo-mese-affitto-max"=>"",
                "indirizzo-affitto"=>"");

$filtriEsperimenti = array(
                "laboratorio"=>"",
                "esperimento-durata-max"=>"",
                "esperimento-compenso-min"=>"");

$filtriEventi = array(
                "evento-inizio"=>"", 
                "evento-fine"=>"", 
                "evento-costo-max"=>"",
                "luogo-evento"=>"");

$filtriRipetizioni = array(
                "materia"=>"",
                "livello"=>"",
                "prezzo-ripetizioni-max"=>"");


if(isset($_GET['submit'])) 
{
    $categoria =$_GET['categoria'] ?? "";

    foreach ($filtriGenerali as $key => $value) {
        $filtriGenerali[$key] = isset($_GET[$key])? Tool::pulisciInputCompleto($_GET[$key]) : "";
    }

    if($db->openDBConnection())
    {
        switch ($categoria) {
            case '':
                $cardsData = $db->searchEsplora($categoria, $filtriGenerali);
            break;

            case 'Affitti':
                foreach ($filtriAffitti as $key => $value) {
                    $filtriAffitti[$key] = isset($_GET[$key])? Tool::pulisciInputCompleto($_GET[$key]) : '';
                }
                $cardsData = $db->searchEsplora($categoria, array_merge($filtriGenerali, $filtriAffitti));
                break;

            case 'Esperimenti':
                foreach ($filtriEsperimenti as $key => $value) {
                    $filtriEsperimenti[$key] = isset($_GET[$key])? Tool::pulisciInputCompleto($_GET[$key]) : '';
                }
                $cardsData = $db->searchEsplora($categoria, array_merge($filtriGenerali, $filtriEsperimenti));
                break;

            
        case 'Eventi':
                foreach ($filtriEventi as $key => $value) {
                    $filtriEventi[$key] = isset($_GET[$key])? Tool::pulisciInputCompleto($_GET[$key]) : '';
                }
                $cardsData = $db->searchEsplora($categoria, array_merge($filtriGenerali, $filtriEventi));
                break;

            case 'Ripetizioni':
                foreach ($filtriRipetizioni as $key => $value) {
                    $filtriRipetizioni[$key] = isset($_GET[$key])? Tool::pulisciInputCompleto($_GET[$key]) : '';
                }
                $cardsData = $db->searchEsplora($categoria, array_merge($filtriGenerali, $filtriRipetizioni));
                break;

            default:
                break;
        }
        $db->closeConnection();
    } else {
        Tool::renderError(500);
    }
    

    if($cardsData !== false){
        $cards = Tool::createCard($cardsData);
        $numRisultati = count($cardsData);
    } else {
        $cards = '<li class="centered">
                        <p>Nessun annuncio corrisponde alla tua ricerca.</p>
                        <a class="link btn-base call-to-action" href="esplora.php">Esplora le proposte</a>
                        <a class="link btn-base call-to-action" href="pubblica.php">Pubblica un annuncio</a>
                    </li>';
        $numRisultati = 0;
    }
} else {
    //nel caso non siano stati applicati filtri o ricerche mostro tutti gli annunci presenti
    if($db->openDBConnection()) {
        $cardsData = $db->getAnnouncements();
        $db->closeConnection();
    } else {
        Tool::renderError(500);
    }

    if($cardsData !== false){
        $cards = Tool::createCard($cardsData);
        $numRisultati = count($cardsData);
    } else {
        $cards = '<li class="centered">
                        <p>Nessun annuncio.</p>
                        <a class="link btn-base call-to-action" href="pubblica.php">Pubblica un annuncio</a>
                    </li>';
        $numRisultati = 0;
    }
}


//Sostituzione dei placeholder
foreach ($filtriGenerali as $key => $value) {
    $htmlPage = str_replace("[$key]", $value, $htmlPage);
}
foreach ($filtriAffitti as $key => $value) {
    $htmlPage = str_replace("[$key]", $value, $htmlPage);
}
foreach ($filtriEsperimenti as $key => $value) {
    $htmlPage = str_replace("[$key]", $value, $htmlPage);
}
foreach ($filtriEventi as $key => $value) {
    $htmlPage = str_replace("[$key]", $value, $htmlPage);
}
foreach ($filtriRipetizioni as $key => $value) {
    $htmlPage = str_replace("[$key]", $value, $htmlPage);
}


$htmlPage = str_replace("[NumRisultati]", $numRisultati, $htmlPage);
$htmlPage = str_replace("[noneSelected]", $categoria=='' ? 'selected' : '' , $htmlPage);
$htmlPage = str_replace("[affittiSelected]", $categoria=='Affitti' ? 'selected' : '' , $htmlPage);
$htmlPage = str_replace("[esperimentiSelected]", $categoria=='Esperimenti' ? 'selected' : '' , $htmlPage);
$htmlPage = str_replace("[eventiSelected]", $categoria=='Eventi' ? 'selected' : '' , $htmlPage);
$htmlPage = str_replace("[ripetizioniSelected]", $categoria=='Ripetizioni' ? 'selected' : '' , $htmlPage);
$htmlPage = str_replace("[oggi]", $oggi, $htmlPage);

$htmlPage = str_replace("[Logger]", $query, $htmlPage);

$htmlPage = str_replace("[Cards]", $cards, $htmlPage);

$htmlPage = str_replace("[TopNavBar]", Tool::buildTopNavBar("esplora"), $htmlPage);
$htmlPage = str_replace("[BottomNavBar]", Tool::buildBottomNavBar("esplora"), $htmlPage);

echo $htmlPage;
?>
