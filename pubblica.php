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

$campo1 = "";
$campo2 = "";
$campo3 = "";

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
            $campo1 = Tool::pulisciInput($_POST['coinquilini'] ?? 0);
            $campo2 = Tool::pulisciInput($_POST['costo-mese-affitto'] ?? 0);
            $campo3 = Tool::pulisciInput($_POST['indirizzo-affitto'] ?? '');
            break;
        case 'Esperimenti':
            $campo1 = Tool::pulisciInput($_POST['laboratorio'] ?? '');
            $campo2 = Tool::pulisciInput($_POST['esperimento-durata'] ?? 0);
            $campo3 = Tool::pulisciInput($_POST['esperimento-compenso'] ?? 0);
            break;
        case 'Eventi':
            $campo1 = Tool::pulisciInput($_POST['data-evento'] ?? '');
            $campo2 = Tool::pulisciInput($_POST['costo-evento'] ?? 0);
            $campo3 = Tool::pulisciInput($_POST['luogo-evneto'] ?? '');
            break;
        case 'Ripetizioni':
            $campo1 = Tool::pulisciInput($_POST['materia'] ?? '');
            $campo2 = Tool::pulisciInput($_POST['livello'] ?? '');
            $campo3 = Tool::pulisciInput($_POST['prezzo-ripetizioni'] ?? 0);
            break;
        default:
            break;
    }

    $db = new DB\DBAccess;
    if ($db->openDBConnection()) {
        $idCitta = $db->getIdCitta($citta);
        $idAnnuncio = $db->inserimentoAnnuncio($titolo, $descrizione, $categoria, $idUtente, $idCitta, $campo1, $campo2, $campo3, $immagini);
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