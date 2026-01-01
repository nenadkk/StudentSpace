<?php

require_once "tool.php";
require_once "dbConnect.php";

if (!Tool::isLoggedIn()) {
    header("Location: accedi.php?redirect=pubblica.php");
    exit;
}

$htmlPage = file_get_contents("pages/pubblica.html");

$titolo = "";
$categoria = ""; # da capire se queste due cose con la datalist sono impostabili
$citta = "";
$descrizione = "";
$idUtente = $_SESSION['user_id'];

$campi = [];

$immagini = [];
$errorMessageImmagini = "";

if(isset($_POST['submit'])) {
    $titolo = Tool::pulisciInput($_POST['titolo'] ?? '');
    $categoria = Tool::pulisciInput($_POST['categoria-campi'] ?? '');
    $citta = Tool::pulisciInput($_POST['citta'] ?? ''); // da prendere l'id
    $descrizione = Tool::pulisciInput($_POST['descrizione'] ?? '');
    
    for ($i = 1; $i<=4; $i++) {
        $fileKey = 'foto'.$i;
        $altKey = 'alt'.$i;
        $decKey = 'decorativa'.$i;

        if (!isset($_FILES[$fileKey]) ||$_FILES[$fileKey]['error'] === UPLOAD_ERR_NO_FILE) {
            continue;
        }

        if ($_FILES[$fileKey]['error'] !== UPLOAD_ERR_OK) {
            $errorMessageImmagini = "<p class='riquadro-spieg messaggi-errore-form'>Errore nel caricamento dell'immagine $i.</p>";
            break;
        }

        $file = $_FILES[$fileKey];

        // Dimensione massima 1MB 
        if ($file['size'] > 1 * 1024 * 1024) { 
            $errorMessageImmagini = "<p class='riquadro-spieg messaggi-errore-form'>L'immagine $i supera la dimensione massima di 1MB.</p>"; 
            break; 
        }

        // MIME consentiti
        $mimeConsentiti = ['image/jpeg', 'image/png', 'image/webp']; 
        if (!in_array($file['type'], $mimeConsentiti)) { 
            $errorMessageImmagini = "<p class='riquadro-spieg messaggi-errore-form'>Formato non valido per l'immagine $i.</p>"; 
            break; 
        }

        $isDecorativa = isset($_POST[$decKey]);
        $altText = null;

        if(!$isDecorativa) {
            $altText = Tool::pulisciInput($_POST[$altKey] ?? '');
            if ($altText === '') { 
                $errorMessageImmagini = "<p class='riquadro-spieg messaggi-errore-form'>Il testo alternativo per l'immagine $i è obbligatorio.</p>"; 
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

    if ($errorMessageImmagini !== "") { 
        $htmlPage = str_replace("[ErrorMessageImmagini]", $errorMessageImmagini, $htmlPage); 
        $htmlPage = str_replace("[ValueTitolo]", $titolo, $htmlPage); 
        $htmlPage = str_replace("[ValueDescrizione]", $descrizione, $htmlPage); 
        echo $htmlPage; 
        exit; 
    }

    switch ($categoria) {
        case 'Affitti':
            $campi['coinquilini'] = Tool::pulisciInput($_POST['coinquilini'] ?? 0);
            $campi['costo-mese-affitto'] = Tool::pulisciInput($_POST['costo-mese-affitto'] ?? 0);
            $campi['indirizzo-affitto'] = Tool::pulisciInput($_POST['indirizzo-affitto'] ?? 0);
            break;
        case 'Esperimenti':
            $campi['laboratorio'] = Tool::pulisciInput($_POST['laboratorio'] ?? 0);
            $campi['esperimento-durata'] = Tool::pulisciInput($_POST['esperimento-durata'] ?? 0);
            $campi['esperimento-compenso'] = Tool::pulisciInput($_POST['esperimento-compenso'] ?? 0);
            break;
        case 'Eventi':
            $campi['data-evento'] = Tool::pulisciInput($_POST['data-evento'] ?? 0);
            $campi['costo-evento'] = Tool::pulisciInput($_POST['costo-evento'] ?? 0);
            $campi['luogo-evento'] = Tool::pulisciInput($_POST['luogo-evento'] ?? 0);
            break;
        case 'Ripetizioni':
            $campi['materia'] = Tool::pulisciInput($_POST['materia'] ?? 0);
            $campi['livello'] = Tool::pulisciInput($_POST['livello'] ?? 0);
            $campi['prezzo-ripetizioni'] = Tool::pulisciInput($_POST['prezzo-ripetizioni'] ?? 0);
            break;
        default:
            break;
    }

    $db = new DB\DBAccess;
    if ($db->openDBConnection()) {
        $idCitta = $db->getIdCitta($citta);
        $idAnnuncio = $db->inserimentoAnnuncio($titolo, $descrizione, $categoria, $idUtente, $idCitta, $campi, $immagini);
        $db->closeConnection();

        header("Location: annuncio.php?id=". $idAnnuncio);
        exit;
    }
}

$htmlPage = str_replace("[ErrorMessageImmagini]", "", $htmlPage);

$htmlPage = str_replace("[TopNavLog]", Tool::getTopNavLog(), $htmlPage);
$htmlPage = str_replace("[BottomNavLog]", Tool::getBottomNavLog(), $htmlPage);

$htmlPage = str_replace("[ValueTitolo]", $titolo, $htmlPage);
# $htmlPage = str_replace("[ValueCategoria]", $categoria, $htmlPage);
# $htmlPage = str_replace("[ValueCitta]", $citta, $htmlPage);
# $htmlPage = str_replace("[ValueDescrizione]", $descrizione, $htmlPage);

echo $htmlPage;

?>
