<?php

require_once "tool.php";
require_once "dbConnect.php";

$idAnnuncio = 0;
if (isset($_GET["id"]) && ctype_digit($_GET["id"])) { 
    $idAnnuncio = intval($_GET["id"]); 
} else { 
    Tool::renderError(404);
}

if (!Tool::isLoggedIn()) {
    header("Location: accedi.php?redirect=pubblica.php");
    exit;
} else {
    $idUtente = $_SESSION["user_id"];
}

use DB\DBAccess;
$db = new DB\DBAccess();

$logger = "";
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
        Tool::renderError(403);
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

$immagini = [];
$errorMessageImmagini = "";
$erroreCitta = "";
$numMessaggiErrore=0;

if(isset($_POST['submit'])) {
    $titolo = Tool::pulisciInput($_POST['titolo'] ?? '');
    $categoria = Tool::pulisciInput($_POST['categoria-campi'] ?? '');
    $citta = Tool::pulisciInput($_POST['citta'] ?? ''); // da prendere l'id
    $descrizione = Tool::pulisciInput($_POST['descrizione'] ?? '');

    if ($titolo === "") {
        $errorMessageTitolo = "<p class='riquadro-spieg messaggi-errore-form'>Il titolo è obbligatorio.</p>";
        $numMessaggiErrore++;
    }

    if ($categoria === "") {
        $errorMessageCategoria = "<p class='riquadro-spieg messaggi-errore-form'>La categoria è obbligatoria.</p>";
        $numMessaggiErrore++;
    }

    if ($descrizione === "") {
        $errorMessageDescrizione = "<p class='riquadro-spieg messaggi-errore-form'>La descrizione è obbligatoria.</p>";
        $numMessaggiErrore++;
    }    
    
    if (!Tool::validaCitta($citta)) {
        $erroreCitta = "<p class='riquadro-spieg messaggi-errore-form'>La città inserita non è valida. </p>";
        $numMessaggiErrore++;
    }

    switch ($categoria) {
        case 'Affitti':
            $campiAffitti['coinquilini'] = Tool::pulisciInput($_POST['coinquilini'] ?? 0);
            $campiAffitti['costo-mese-affitto'] = Tool::pulisciInput($_POST['costo-mese-affitto'] ?? 0);
            $campiAffitti['indirizzo-affitto'] = Tool::pulisciInput($_POST['indirizzo-affitto'] ?? 0);
            $campi = $campiAffitti;
            break;
        case 'Esperimenti':
            $campiEsperimenti['laboratorio'] = Tool::pulisciInput($_POST['laboratorio'] ?? 0);
            $campiEsperimenti['esperimento-durata'] = Tool::pulisciInput($_POST['esperimento-durata'] ?? 0);
            $campiEsperimenti['esperimento-compenso'] = Tool::pulisciInput($_POST['esperimento-compenso'] ?? 0);
            $campi = $campiEsperimenti;
            break;
        case 'Eventi':
            $campiEventi['data-evento'] = Tool::pulisciInput($_POST['data-evento'] ?? 0);
            $campiEventi['costo-evento'] = Tool::pulisciInput($_POST['costo-evento'] ?? 0);
            $campiEventi['luogo-evento'] = Tool::pulisciInput($_POST['luogo-evento'] ?? 0);
            $campi = $campiEventi;
            break;
        case 'Ripetizioni':
            $campiRipetizioni['materia'] = Tool::pulisciInput($_POST['materia'] ?? 0);
            $campiRipetizioni['livello'] = Tool::pulisciInput($_POST['livello'] ?? 0);
            $campiRipetizioni['prezzo-ripetizioni'] = Tool::pulisciInput($_POST['prezzo-ripetizioni'] ?? 0);
            $campi = $campiRipetizioni;
            break;
        default:
            break;
    }

    // Gestione Immagini

    if ($numMessaggiErrore==0) {
        $db = new DB\DBAccess;

        if ($db->openDBConnection()) {
            $idCitta = $db->getIdCitta($citta);
            $stmt = $db->modificaAnnuncio($idAnnuncio, $titolo, $descrizione, $categoria, $idUtente, $idCitta, $campi, $immagini);
            $db->closeConnection();

            if($stmt) {
                header("Location: annuncio.php?id=". $idAnnuncio);
                exit;
            } else {
                $logger = "UPDATE Annuncio SET Titolo = $titolo, Descrizione = $descrizione, Citta = $idCitta WHERE IdAnnuncio = $idAnnuncio";
                switch ($categoria) {
                    case 'Affitti':
                        $campo1 = $attr["PrezzoMensile"]; $campo2 = $attr["Indirizzo"]; $campo3 = attr["NumeroInquilini"];
                        $logger = $logger.
                            "UPDATE AnnuncioAffitti
                            SET PrezzoMensile = $campo1, Indirizzo = $campo2, NumeroInquilini = $campo3
                            WHERE IdAnnuncio = $idAnnuncio"
                        ;
                        break;

                    case 'Esperimenti':
                        $campo1 = $attr["Laboratorio"]; $campo2 = $attr["DurataPrevista"]; $campo3 = attr["Compenso"];
                        $logger = $logger.
                            "UPDATE AnnuncioEsperimenti
                            SET Laboratorio = $campo1, DurataPrevista = $campo2, Compenso = $campo3
                            WHERE IdAnnuncio = $idAnnuncio"
                        ;
                        break;

                    case 'Eventi':
                        $campo1 = $attr["DataEvento"]; $campo2 = $attr["CostoEntrata"]; $campo3 = $attr["Luogo"];
                        $logger = $logger.
                            "UPDATE AnnuncioEventi
                            SET DataEvento = $campo1, CostoEntrata = $campo2, Luogo = $campo3
                            WHERE IdAnnuncio = $idAnnuncio"
                        ;
                        break;

                    case 'Ripetizioni':
                        $campo1 = $attr["Materia"]; $campo2 = $attr["Livello"]; $campo3 = attr["PrezzoOrario"];
                        $logger = $logger.
                            "UPDATE AnnuncioRipetizioni
                            SET Materia = $campo1, Livello = $campo2, PrezzoOrario = $campo3
                            WHERE IdAnnuncio = $idAnnuncio"
                        ;
                        break;

                    default:
                        $logger = $logger." Categoria Inesistente";
                }
            }
        } else {
            Tool::renderError(500);
        }
    }
}


$htmlPage = file_get_contents(__DIR__ . '/pages/modificaAnnuncio.html');

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

$htmlPage = str_replace("[IdAnnuncio]", $idAnnuncio, $htmlPage);
$htmlPage = str_replace("[Logger]", $logger, $htmlPage);

$htmlPage = str_replace("[ErrorMessageImmagini]", $errorMessageImmagini, $htmlPage);
$htmlPage = str_replace("[Errore-citta]", $erroreCitta, $htmlPage);

$cities = [];
if($db->openDBConnection()) {
    $cities = $db->getAllCity();
    $db->closeConnection();
}
$htmlPage = str_replace("[CityOptionsList]", Tool::renderCityOptions($cities), $htmlPage);

$htmlPage = str_replace("[TopNavBar]", Tool::buildTopNavBar("modifica"), $htmlPage);
$htmlPage = str_replace("[BottomNavBar]", Tool::buildBottomNavBar("modifica"), $htmlPage);


echo $htmlPage;

?>