<?php

require_once "tool.php";
require_once "dbConnect.php";

use DB\DBAccess;
$db = new DB\DBAccess();

$htmlPage = file_get_contents(__DIR__ . '/pages/modificaAnnuncio.html');

$idAnnuncio = 0;
if (isset($_GET["id"]) && ctype_digit($_GET["id"])) { 
    $idAnnuncio = intval($_GET["id"]); 
} else { 
    Tool::renderError(404);
}

if (!Tool::isLoggedIn()) {
    header("Location: accedi?redirect=pubblica");
    exit;
} else {
    $idUtente = $_SESSION["user_id"];
}

$annuncio = ""; 
$attr = "";
$listaAttr = "";

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

$cities = [];

if($db->openDBConnection()) {
    $cities = $db->getAllCity();
    $db->closeConnection();
}

if($db->openDBConnection()) {

    $annuncio = $db->getAnnuncioBase($idAnnuncio);


    if ($annuncio === false) { 
        $db->closeConnection(); 
        Tool::renderError(404);
    }

    if ($annuncio["IdUtente"] != $_SESSION["user_id"]) {
        $db->closeConnection(); 
        Tool::renderError(403);
    }

    $attr = $db->getAttributiSpecifici($annuncio["Categoria"], $idAnnuncio); 

    $immagini = $db->getImmagini($idAnnuncio);

    $db->closeConnection();
} else {
    Tool::renderError(500);
}

switch ($annuncio["Categoria"]) {
    case 'Affitti':
        $campiAffitti['coinquilini'] = Tool::pulisciInput($attr['NumeroInquilini'] ?? 0);
        $campiAffitti['costo-mese-affitto'] = Tool::pulisciInput($attr['PrezzoMensile'] ?? 0);
        $campiAffitti['indirizzo-affitto'] = Tool::pulisciInput($attr['Indirizzo'] ?? 0);
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

$errorMessageTitolo = "";
$errorMessageCategoria = "";
$errorMessageDescrizione = "";

$numMessaggiErrore = 0;
$erroriImmagini = [];
$erroreCitta = "";
$logger = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {

    // --- PULIZIA INPUT ---
    $titolo = Tool::pulisciInput($_POST['titolo'] ?? '');
    $categoria = Tool::pulisciInput($_POST['categoria-campi'] ?? '');
    $citta = Tool::pulisciInput($_POST['citta'] ?? '');
    $descrizione = Tool::pulisciInput($_POST['descrizione'] ?? '');

    

    // --- VALIDAZIONI GENERALI ---
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

    if ($descrizione === "") {
        $errorMessageDescrizione = 
        "<ul class='riquadro-spieg messaggi-errore-form'>
            <li class='msgErrore' id='errore-descrizione' role='alert'>
                La descrizione è obbligatoria.
            </li>
        </ul>";
        $numMessaggiErrore++;
    } else $errorMessageDescrizione = "";

    if (!Tool::validaCitta($citta)) {
        $erroreCitta = 
        "<ul class='riquadro-spieg messaggi-errore-form'>
            <li class='msgErrore' id='errore-citta' role='alert'>
                La città inserita non è valida.
            </li>
        </ul>";
        $numMessaggiErrore++;
    }

    // --- AGGIORNO I CAMPI SPECIFICI (NON USO PIÙ $attr VECCHIO) ---
    switch ($categoria) {
        case 'Affitti':
            $campi = [
                "coinquilini" => Tool::pulisciInput($_POST['coinquilini'] ?? ''),
                "costo-mese-affitto" => Tool::pulisciInput($_POST['costo-mese-affitto'] ?? ''),
                "indirizzo-affitto" => Tool::pulisciInput($_POST['indirizzo-affitto'] ?? '')
            ];
            break;

        case 'Esperimenti':
            $campi = [
                "laboratorio" => Tool::pulisciInput($_POST['laboratorio'] ?? ''),
                "esperimento-durata" => Tool::pulisciInput($_POST['esperimento-durata'] ?? ''),
                "esperimento-compenso" => Tool::pulisciInput($_POST['esperimento-compenso'] ?? '')
            ];
            break;

        case 'Eventi':
            $campi = [
                "data-evento" => Tool::pulisciInput($_POST['data-evento'] ?? ''),
                "costo-evento" => Tool::pulisciInput($_POST['costo-evento'] ?? ''),
                "luogo-evento" => Tool::pulisciInput($_POST['luogo-evento'] ?? '')
            ];
            break;

        case 'Ripetizioni':
            $campi = [
                "materia" => Tool::pulisciInput($_POST['materia'] ?? ''),
                "livello" => Tool::pulisciInput($_POST['livello'] ?? ''),
                "prezzo-ripetizioni" => Tool::pulisciInput($_POST['prezzo-ripetizioni'] ?? '')
            ];
            break;
    }

    // --- Gestione immagini ---
    // --- RECUPERO IMMAGINI VECCHIE ---
    if ($db->openDBConnection()) {
        $immaginiVecchie = $db->getImmagini($idAnnuncio);
        $db->closeConnection();
    } else Tool::renderError(500);

    // Normalizzo il formato delle immagini vecchie
    $immaginiVecchie = array_map(function($img) {
        return [
            'file'       => $img['Percorso'] ?? null,
            'alt'        => $img['AltText'] ?? null,
            'decorativa' => (int)($img['Decorativa'] ?? 0),
            'ordine'     => (int)($img['Ordine'] ?? 0)
        ];
    }, $immaginiVecchie);

    $immaginiNuove = [];

    for ($i = 1; $i <= 4; $i++) {

        $fileKey = 'foto'.$i;
        $altKey = 'alt'.$i;
        $decKey = 'decorativa'.$i;

        if (!isset($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] === UPLOAD_ERR_NO_FILE) {
            continue;
        }

        $file = $_FILES[$fileKey];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $erroriImmagini[] = "Errore nel caricamento dell'immagine $i.";
            $numMessaggiErrore++;
            continue;
        }

        if ($file['size'] > 1 * 1024 * 1024) {
            $erroriImmagini[] = "L'immagine $i supera 1MB.";
            $numMessaggiErrore++;
            continue;
        }

        $mimeConsentiti = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($file['type'], $mimeConsentiti)) {
            $erroriImmagini[] = "Formato non valido per l'immagine $i.";
            $numMessaggiErrore++;
            continue;
        }

        $isDecorativa = isset($_POST[$decKey]);
        $altText = $isDecorativa ? null : Tool::pulisciInput($_POST[$altKey] ?? '');

        if (!$isDecorativa && $altText === '') {
            $erroriImmagini[] = "L'immagine $i richiede un testo alternativo.";
            $numMessaggiErrore++;
            continue;
        }

        $estensione = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $percorso = uniqid('img_') . '.' . $estensione;
        move_uploaded_file($file['tmp_name'], __DIR__ . '/img_annunci/' . $percorso);

        $immaginiNuove[] = [
            'file' => $percorso,
            'alt' => $altText,
            'decorativa' => (int)$isDecorativa,
            'ordine' => $i
        ];
    }

    // Se non ho caricato nuove immagini → tengo le vecchie
    $immagini = empty($immaginiNuove) ? $immaginiVecchie : $immaginiNuove;


        if ($numMessaggiErrore == 0) {

        if ($db->openDBConnection()) {

            $idCitta = $db->getIdCitta($citta);

            $stmt = $db->modificaAnnuncio(
                $idAnnuncio,
                $titolo,
                $descrizione,
                $categoria,
                $idUtente,
                $idCitta,
                $campi,
                $immagini
            );

            $db->closeConnection();

            if ($stmt) {
                header("Location: annuncio?id=".$idAnnuncio);
                exit;
            } else {
                $logger = "<p>Errore durante l'aggiornamento dell'annuncio.</p>";
            }

        } else Tool::renderError(500);
    }
}

$htmlPage = str_replace("[TitoloSEO]",Tool::titoloSEO($annuncio["Titolo"], "", "Modifica:", false),$htmlPage);

//rimetto la categoria selezionata
$htmlPage = str_replace("[categoriaSelected]", $annuncio["Categoria"] ?? '' , $htmlPage);

//riempio i campi compilati al momento del submit
//GENERALI
$htmlPage = str_replace("[titolo]", $annuncio["Titolo"], $htmlPage);
$htmlPage = str_replace("[descrizione]", $annuncio["Descrizione"], $htmlPage);
$htmlPage = str_replace("[citta]", $annuncio["NomeCitta"], $htmlPage);

$htmlPage = str_replace("[campiDettagli]", Tool::getModificaAnnuncioSpecifico($annuncio["Categoria"]), $htmlPage);
$htmlPage = Tool::sostituisciPlaceholderValori($htmlPage, $campi);

$htmlPage = str_replace("[IdAnnuncio]", $idAnnuncio, $htmlPage);

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

if ($errorMessageTitolo !== "") {
    $htmlPage = str_replace("id='titolo'", "id='titolo' aria-invalid='true' aria-describedby='errore-titolo'", $htmlPage);
}
$htmlPage = str_replace("[Errore-titolo]", $errorMessageTitolo, $htmlPage);

if ($errorMessageCategoria !== "") {
    $htmlPage = str_replace("id='categoria-campi'", "id='categoria-campi' aria-invalid='true' aria-describedby='errore-categoria'", $htmlPage);
}
$htmlPage = str_replace("[Errore-categoria]", $errorMessageCategoria, $htmlPage);

if ($errorMessageDescrizione !== "") {
    $htmlPage = str_replace("id='descrizione'", "id='descrizione' aria-invalid='true' aria-describedby='errore-descrizione'", $htmlPage);
}
$htmlPage = str_replace("[Errore-descrizione]", $errorMessageDescrizione, $htmlPage);

if ($erroreCitta !== "") {
    $htmlPage = str_replace("id='citta'", "id='citta' aria-invalid='true' aria-describedby='errore-citta'", $htmlPage);
}
$htmlPage = str_replace("[Errore-citta]", $erroreCitta, $htmlPage);

$htmlPage = str_replace("[CityOptionsList]", Tool::renderCityOptions($cities), $htmlPage);

$htmlPage = str_replace("[Logger]", $logger, $htmlPage);

$htmlPage = str_replace("[TopNavBar]", Tool::buildTopNavBar("modifica"), $htmlPage);
$htmlPage = str_replace("[BottomNavBar]", Tool::buildBottomNavBar("modifica"), $htmlPage);


echo $htmlPage;

?>