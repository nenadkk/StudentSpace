<?php
require_once "tool.php";
require_once "dbConnect.php";

if (!Tool::isLoggedIn()) {
    header("Location: accedi.php?redirect=pubblica.php");
    exit;
}

$htmlPage = file_get_contents(__DIR__ . "/pages/pubblica.html");

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

    for ($i = 1; $i<=4; $i++) {
        $fileKey = 'foto'.$i;
        $altKey = 'alt'.$i;
        $decKey = 'decorativa'.$i;

        if (!isset($_FILES[$fileKey]) ||$_FILES[$fileKey]['error'] === UPLOAD_ERR_NO_FILE) {
            continue;
        }

        if ($_FILES[$fileKey]['error'] !== UPLOAD_ERR_OK) {
            $errorMessageImmagini = "<p class='riquadro-spieg messaggi-errore-form'>Errore nel caricamento dell'immagine $i.</p>";
            $numMessaggiErrore++;
            break;
        }

        $file = $_FILES[$fileKey];

        // Dimensione massima 1MB 
        if ($file['size'] > 1 * 1024 * 1024) { 
            $errorMessageImmagini = "<p class='riquadro-spieg messaggi-errore-form'>L'immagine $i supera la dimensione massima di 1MB.</p>"; 
            $numMessaggiErrore++;
            break; 
        }

        // MIME consentiti
        $mimeConsentiti = ['image/jpeg', 'image/png', 'image/webp']; 
        if (!in_array($file['type'], $mimeConsentiti)) { 
            $errorMessageImmagini = "<p class='riquadro-spieg messaggi-errore-form'>Formato non valido per l'immagine $i.</p>"; 
            $numMessaggiErrore++;
            break; 
        }

        $isDecorativa = isset($_POST[$decKey]);
        $altText = null;

        if(!$isDecorativa) {
            $altText = Tool::pulisciInput($_POST[$altKey] ?? '');
            if ($altText === '') { 
                $errorMessageImmagini = "<p class='riquadro-spieg messaggi-errore-form'>Il testo alternativo per l'immagine $i è obbligatorio, a meno che non si selezioni l'opzione “Decorativa”.</p>";
                $numMessaggiErrore++;
                break; 
            }
        }

        // Salvataggio file originale
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
        $db = new DB\DBAccess;

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
    $db = new DB\DBAccess;
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

$cities = [];
if($db->openDBConnection()) {
    $cities = $db->getAllCity();
    $db->closeConnection();
}
$htmlPage = str_replace("[CityOptionsList]", Tool::renderCityOptions($cities), $htmlPage);

$htmlPage = str_replace("[ErrorMessageImmagini]", $errorMessageImmagini, $htmlPage);
$htmlPage = str_replace("[Errore-citta]", $erroreCitta, $htmlPage);

$htmlPage = str_replace("[TopNavBar]", Tool::buildTopNavBar("pubblica"), $htmlPage);
$htmlPage = str_replace("[BottomNavBar]", Tool::buildBottomNavBar("pubblica"), $htmlPage);

# $htmlPage = str_replace("[ValueCategoria]", $categoria, $htmlPage);
# $htmlPage = str_replace("[ValueCitta]", $citta, $htmlPage);
# $htmlPage = str_replace("[ValueDescrizione]", $descrizione, $htmlPage);

echo $htmlPage;
?>
