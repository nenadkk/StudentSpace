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
        // DA SISTEMARE
        $db->closeConnection(); 
        Tool::renderError(404);
    }

    $attr = $db->getAttributiSpecifici($annuncio["Categoria"], $idAnnuncio)[0]; 

    $immagini = $db->getImmagini($idAnnuncio);

    $db->closeConnection();
} else {
    Tool::renderError(500);
}

switch ($annuncio["Categoria"]) {
    case 'Affitti':
        $campiAffitti['coinquilini'] = Tool::pulisciInput($attr['PrezzoMensile'] ?? 0);
        $campiAffitti['costo-mese-affitto'] = Tool::pulisciInput($attr['Indirizzo'] ?? 0);
        $campiAffitti['indirizzo-affitto'] = Tool::pulisciInput($attr['NumeroInquilini'] ?? 0);
        $campi = $campiAffitti;
        break;
    case 'Esperimenti':
        $campiEsperimenti['laboratorio'] = Tool::pulisciInput($attr['Laboratorio'] ?? 0);
        $campiEsperimenti['esperimento-durata'] = Tool::pulisciInput($attr['DurataPrevista'] ?? 0);
        $campiEsperimenti['esperimento-compenso'] = Tool::pulisciInput($attr['Compenso'] ?? 0);
        $campi = $campiEsperimenti;
        break;
    case 'Eventi':
        $campiEventi['data-evento'] = Tool::pulisciInput($attr['DataEvento'] ?? 0);
        $campiEventi['costo-evento'] = Tool::pulisciInput($attr['CostoEntrata'] ?? 0);
        $campiEventi['luogo-evento'] = Tool::pulisciInput($attr['Luogo'] ?? 0);
        $campi = $campiEventi;
        break;
    case 'Ripetizioni':
        $campiRipetizioni['materia'] = Tool::pulisciInput($attr['Materia'] ?? 0);
        $campiRipetizioni['livello'] = Tool::pulisciInput($attr['Livello'] ?? 0);
        $campiRipetizioni['prezzo-ripetizioni'] = Tool::pulisciInput($attr['PrezzoOrario'] ?? 0);
        $campi = $campiRipetizioni;
        break;
    default:
        break;
}


$htmlPage = file_get_contents('pages/modificaAnnuncio.html');

//rimetto la categoria selezionata
$htmlPage = str_replace("[categoriaSelected]", $annuncio["Categoria"] ?? '' , $htmlPage);

//riempio i campi compilati al momento del submit
//GENERALI
$htmlPage = str_replace("[titolo]", $annuncio["Titolo"], $htmlPage);
$htmlPage = str_replace("[descrizione]", $annuncio["Descrizione"], $htmlPage);
$htmlPage = str_replace("[citta]", $annuncio["NomeCitta"], $htmlPage);

$htmlPage = str_replace("[campiDettagli]", Tool::getModificaAnnuncioSpecifico($annuncio["Categoria"]), $htmlPage);

foreach ($campi as $key => $value) {
    $htmlPage = str_replace("[$key]", $value, $htmlPage);
}

$htmlPage = str_replace("[TopNavLog]", Tool::getTopNavLog(), $htmlPage);
$htmlPage = str_replace("[BottomNavLog]", Tool::getBottomNavLog(), $htmlPage);

$htmlPage = str_replace("[Logger]", $annuncio["Categoria"], $htmlPage);
$htmlPage = str_replace("[Logger]", $annuncio["Categoria"], $htmlPage);

echo $htmlPage;

?>