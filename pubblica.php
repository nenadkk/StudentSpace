<?php
require_once "tool.php";
require_once "dbConnect.php";

use DB\DBAccess;
$db = new DB\DBAccess;

$htmlPage = file_get_contents(__DIR__ . "/pages/pubblica.html");

if (!Tool::isLoggedIn()) {
    header("Location: accedi.php?redirect=pubblica.php");
    exit;
}

$titolo = "";
$categoria = ""; # da capire se queste due cose con la datalist sono impostabili
$citta =  "";
$descrizione = "";
$idUtente = $_SESSION['user_id'];

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

$errorMessageTitolo = "";
$errorMessageCategoria = "";
$errorMessageDescrizione = "";
        
$immagini = [];
$erroriImmagini = [];
$erroreCitta = "";
$numMessaggiErrore=0;

$cities = [];

if($db->openDBConnection()) {
    $cities = $db->getAllCity();
    $db->closeConnection();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $titolo = Tool::pulisciInput($_POST['titolo'] ?? '');
    $categoria = Tool::pulisciInput($_POST['categoria-campi'] ?? '');
    $citta = Tool::pulisciInput($_POST['citta'] ?? ''); // da prendere l'id
    $descrizione = Tool::pulisciInput($_POST['descrizione'] ?? '');
    
    if ($titolo === "") {
        $errorMessageTitolo = "
        <ul class='riquadro-spieg messaggi-errore-form'>
            <li class='msgErrore' id='errore-titolo' role='alert'>
                Il titolo è obbligatorio.
            </li>
        </ul>";
        $numMessaggiErrore++;
    } elseif (strlen($titolo) > 50) {
        $errorMessageTitolo = "
        <ul class='riquadro-spieg messaggi-errore-form'>
            <li class='msgErrore' id='errore-titolo' role='alert'>
                Il titolo non può superare i 50 caratteri.
            </li>
        </ul>";
        $numMessaggiErrore++;
    } else {
        $errorMessageTitolo = "";
    }

    if ($categoria === "") {
        $errorMessageCategoria = "
        <ul class='riquadro-spieg messaggi-errore-form'>
            <li class='msgErrore' id='errore-categoria' role='alert'>La categoria è obbligatoria.</li>
        </ul>";
        $numMessaggiErrore++;
    } else {
        $errorMessageCategoria = "";
    }

    if ($descrizione === "") {
        $errorMessageDescrizione = "
        <ul class='riquadro-spieg messaggi-errore-form'>
            <li class='msgErrore' id='errore-descrizione' role='alert'>La descrizione è obbligatoria.</li>
        </ul>";
        $numMessaggiErrore++;
    } else {
        $errorMessageDescrizione = "";
    }

    if (!Tool::validaCitta($citta)) {
        $erroreCitta = "
        <ul class='riquadro-spieg messaggi-errore-form'>
            <li class='msgErrore' id='errore-citta' role='alert'>La città inserita non è valida, seleziona una città dall’elenco.</li>
        </ul>";
        $numMessaggiErrore++;
    } else {
        $erroreCitta = "";
    }

    for ($i = 1; $i <= 4; $i++) {
        $fileKey = 'foto'.$i;
        $altKey  = 'alt'.$i;
        $decKey  = 'decorativa'.$i;

        if (!isset($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] === UPLOAD_ERR_NO_FILE) {
            continue;
        }

        $file = $_FILES[$fileKey];

        // Errore generico upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $erroriImmagini[] = "Errore nel caricamento dell'immagine $i.";
            $numMessaggiErrore++;
            continue;
        }

        // Dimensione massima 1MB 
        if ($file['size'] > 1 * 1024 * 1024) { 
            $erroriImmagini[] = "L'immagine $i supera la dimensione massima di 1MB.";
            $numMessaggiErrore++;
            continue; 
        }

        // MIME consentiti
        $mimeConsentiti = ['image/jpeg', 'image/png', 'image/webp']; 
        if (!in_array($file['type'], $mimeConsentiti)) { 
            $erroriImmagini[] = "Formato non valido per l'immagine $i (consentiti JPG, PNG, WEBP).";
            $numMessaggiErrore++;
            continue;
        }

        $isDecorativa = isset($_POST[$decKey]);
        $altText = null;

        if (!$isDecorativa) {
            $altText = Tool::pulisciInput($_POST[$altKey] ?? '');
            if ($altText === '') { 
                $erroriImmagini[] = "L'immagine $i richiede un testo alternativo oppure la selezione di “Decorativa”.";
                $numMessaggiErrore++;
                continue;
            }
        }

        // Salvataggio file 
        $estensione = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)); 
        $nomeFile = uniqid('img_') . '.' . $estensione; 
        move_uploaded_file($file['tmp_name'], __DIR__ . '/img_annunci/' . $nomeFile);

        // Salvataggio dati
        $immagini[] = [
            'file'       => $nomeFile,
            'alt'        => $altText,
            'decorativa' => (int)$isDecorativa,
            'ordine'     => $i
        ];
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

    if ($numMessaggiErrore==0) {
        if ($db->openDBConnection()) {
            $idCitta = $db->getIdCitta($citta);
            $idAnnuncio = $db->inserimentoAnnuncio($titolo, $descrizione, $categoria, $idUtente, $idCitta, $campi, $immagini);
            $db->closeConnection();

            header("Location: annuncio.php?id=". $idAnnuncio);
            exit;
        } else {
            Tool::renderError(500);
        }
    }
}
else {
    if ($db->openDBConnection()) {
        $citta = $db->getCittaUtente($idUtente) ?? ""; // funzione da creare
        $db->closeConnection();
    } else {
        Tool::renderError(500);
    }
}

//rimetto la categoria selezionata
$htmlPage = str_replace("[noneSelected]", $categoria=='' ? 'selected' : '' , $htmlPage);
$htmlPage = str_replace("[affittiSelected]", $categoria=='Affitti' ? 'selected' : '' , $htmlPage);
$htmlPage = str_replace("[esperimentiSelected]", $categoria=='Esperimenti' ? 'selected' : '' , $htmlPage);
$htmlPage = str_replace("[eventiSelected]", $categoria=='Eventi' ? 'selected' : '' , $htmlPage);
$htmlPage = str_replace("[ripetizioniSelected]", $categoria=='Ripetizioni' ? 'selected' : '' , $htmlPage);

//riempio i campi compilati al momento del submit
//GENERALI
$htmlPage = str_replace("[titolo]", $titolo, $htmlPage);
$htmlPage = str_replace("[descrizione]", $descrizione, $htmlPage);
$htmlPage = str_replace("[citta]", $citta, $htmlPage);
//SPECIFICI
foreach ($campiAffitti as $key => $value) {
    $htmlPage = str_replace("[$key]", $value, $htmlPage);
}
foreach ($campiEsperimenti as $key => $value) {
    $htmlPage = str_replace("[$key]", $value, $htmlPage);
}
foreach ($campiEventi as $key => $value) {
    $htmlPage = str_replace("[$key]", $value, $htmlPage);
}
foreach ($campiRipetizioni as $key => $value) {
    $htmlPage = str_replace("[$key]", $value, $htmlPage);
}

$htmlPage = str_replace("[CityOptionsList]", Tool::renderCityOptions($cities), $htmlPage);

if (!empty($erroriImmagini)) {
    $testoErrori = "Si sono verificati errori nelle immagini. Reinseriscile e correggi quanto segue: ";

    foreach ($erroriImmagini as $msg) {
        $testoErrori .= $msg . " ";
    }

    $erroreGlobaleImmagini = "
        <div id='errore-immagini-globali' class='riquadro-spieg messaggi-errore-form'>
            <p class='msgErrore' role='alert'>$testoErrori</p>
        </div>";

} else {
    $erroreGlobaleImmagini = "";
}

$htmlPage = str_replace("[ErroreImmaginiGlobal]", $erroreGlobaleImmagini, $htmlPage);

$htmlPage = str_replace("[Errore-titolo]", $errorMessageTitolo, $htmlPage);
$htmlPage = str_replace("[Errore-categoria]", $errorMessageCategoria, $htmlPage);
$htmlPage = str_replace("[Errore-descrizione]", $errorMessageDescrizione, $htmlPage);
$htmlPage = str_replace("[Errore-citta]", $erroreCitta, $htmlPage);

$htmlPage = str_replace("[TopNavBar]", Tool::buildTopNavBar("pubblica"), $htmlPage);
$htmlPage = str_replace("[BottomNavBar]", Tool::buildBottomNavBar("pubblica"), $htmlPage);

# $htmlPage = str_replace("[ValueCategoria]", $categoria, $htmlPage);
# $htmlPage = str_replace("[ValueCitta]", $citta, $htmlPage);
# $htmlPage = str_replace("[ValueDescrizione]", $descrizione, $htmlPage);

echo $htmlPage;
?>
