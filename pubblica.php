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

if(isset($_POST['submit'])) {
    $titolo = Tool::pulisciInput($_POST['titolo'] ?? '');
    $categoria = Tool::pulisciInput($_POST['categoria-campi'] ?? '');
    $citta = Tool::pulisciInput($_POST['citta'] ?? ''); // da prendere l'id
    $descrizione = Tool::pulisciInput($_POST['descrizione'] ?? '');
    
    for ($i = 1; $i<=4; $i++) {
        $fileKey = 'foto'.$i;
        $altKey = 'alt'.$i;
        $decKey = 'decorativa'.$i;

        if (
            !isset($_FILES[$fileKey]) ||
            $_FILES[$fileKey]['error'] === UPLOAD_ERR_NO_FILE
        ) {
            continue;
        }

        if ($_FILES[$fileKey]['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Errore upload immagine $i");
        }

        $file = $_FILES[$fileKey];

        $isDecorativa = isset($_POST[$decKey]);

        $altText = null;
        if(!$isDecorativa) {
            $altText = Tool::pulisciInput($_POST[$altKey] ?? '');
            if($altText === '') {
                die("Errore: il testo alternativo per l'immagine $i è obbligatorio.");
            }
        }

        // Validazione MIME
        $mimeConsentiti = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($file['type'], $mimeConsentiti)) {
            die("Formato non valido per l'immagine $i.");
        }

        // Rinomina sicura
        $estensione = pathinfo($file['name'], PATHINFO_EXTENSION);
        $nomeFile = uniqid('img_') . '.' . $estensione;

        move_uploaded_file($file['tmp_name'], __DIR__ . '/assets/' . $nomeFile);

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
        $str = "Location: annuncio.php?id=". $idAnnuncio;
        header($str);
        exit;
    }
}

$htmlPage = str_replace("[TopNavLog]", Tool::getTopNavLog(), $htmlPage);
$htmlPage = str_replace("[BottomNavLog]", Tool::getBottomNavLog(), $htmlPage);

$htmlPage = str_replace("[ValueTitolo]", $titolo, $htmlPage);
# $htmlPage = str_replace("[ValueCategoria]", $categoria, $htmlPage);
# $htmlPage = str_replace("[ValueCitta]", $citta, $htmlPage);
# $htmlPage = str_replace("[ValueDescrizione]", $descrizione, $htmlPage);

echo $htmlPage;

?>