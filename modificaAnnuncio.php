<?php

require_once "tool.php";
require_once "dbConnect.php";

if (isset($_GET["id"]) && ctype_digit($_GET["id"])) { 
    $idAnnuncio = intval($_GET["id"]); 
} else { 
    Tool::renderError(404);
}

if (!Tool::isLoggedIn()) {
    header("Location: accedi.php?redirect=pubblica.php");
    exit;
}

use DB\DBAccess;
$db = new DB\DBAccess();


$annuncio = ""; 
$attr = "";
$listaAttr = "";
$immagini = [];  


$campi = [];
$campiAffitti = array(
                "coinquilini"=>"",
                "costo-mese-affitto"=>"",
                "indirizzo-affitto"=>"");

$campiEsperimenti = array(
                "laboratorio"=>"",
                "esperimento-durata"=>"",
                "esperimento-compenso"=>"");

$campiEventi = array(
                "data-evento"=>"", 
                "costo-evento"=>"",
                "luogo-evento"=>"");

$campiRipetizioni = array(
                "materia"=>"",
                "livello"=>"",
                "prezzo-ripetizioni"=>"");

if($db->openDBConnection()) {

    $annuncio = $db->getAnnuncioBase($idAnnuncio)[0];


    if ($annuncio === false) { 
        $db->closeConnection(); 
        Tool::renderError(404);
    }

    if ($annuncio["IdUtente"] != $_SESSION["user_id"]) {
        // errore di permessi mancanti
    }

    $attr = $db->getAttributiSpecifici($annuncio["Categoria"], $idAnnuncio)[0]; 

    $immagini = $db->getImmagini($idAnnuncio);

    $db->closeConnection();
} else {
    Tool::renderError(500);
}


$htmlPage = file_get_contents('pages/modificaAnnuncio.html');

//rimetto la categoria selezionata
$htmlPage = str_replace("[noneSelected]", $annuncio["Categoria"]=='' ? 'selected' : '' , $htmlPage);
$htmlPage = str_replace("[affittiSelected]", $annuncio["Categoria"]=='Affitti' ? 'selected' : '' , $htmlPage);
$htmlPage = str_replace("[esperimentiSelected]", $annuncio["Categoria"]=='Esperimenti' ? 'selected' : '' , $htmlPage);
$htmlPage = str_replace("[eventiSelected]", $annuncio["Categoria"]=='Eventi' ? 'selected' : '' , $htmlPage);
$htmlPage = str_replace("[ripetizioniSelected]", $annuncio["Categoria"]=='Ripetizioni' ? 'selected' : '' , $htmlPage);

//riempio i campi compilati al momento del submit
//GENERALI
$htmlPage = str_replace("[titolo]", $annuncio["Titolo"], $htmlPage);
$htmlPage = str_replace("[descrizione]", $annuncio["Descrizione"], $htmlPage);
$htmlPage = str_replace("[citta]", $annuncio["NomeCitta"], $htmlPage);

$htmlPage = str_replace("[TopNavLog]", Tool::getTopNavLog(), $htmlPage);
$htmlPage = str_replace("[BottomNavLog]", Tool::getBottomNavLog(), $htmlPage);

echo $htmlPage;

?>