<?php

require_once "tool.php";
require_once "dbConnect.php";

if (!Tool::isLoggedIn()) {
    header ("Location: accedi.php");
    exit;
}

$htmlPage = file_get_contents("pages/pubblica.html");

$titolo = "";
$categoria = ""; # da capire se queste due cose con la datalist sono impostabili
$citta = "";
$descrizione = "";
$campo1 = "";
$campo2 = "";
$campo3 = "";
$logger = "";

if(isset($_POST['submit'])) {
    $titolo = Tool::pulisciInput($_POST['titolo'] ?? '');
    $categoria = Tool::pulisciInput($_POST['categoria-campi'] ?? '');
    $citta = Tool::pulisciInput($_POST['citta'] ?? '');
    $descrizione = Tool::pulisciInput($_POST['descrizione'] ?? '');
    $logger = $categoria;

    switch ($categoria) {
        case 'Affitti':
            $campo1 = Tool::pulisciInput($_POST['coinquilini'] ?? 0);
            $campo2 = Tool::pulisciInput($_POST['costo-mese-affitto'] ?? 0);
            $campo3 = Tool::pulisciInput($_POST['indirizzo-affitto'] ?? '');
            $logger = "Aria";
            break;
        case 'Esperimenti':
            $campo1 = Tool::pulisciInput($_POST['laboratorio'] ?? '');
            $campo2 = Tool::pulisciInput($_POST['esperimento-durata'] ?? 0);
            $campo3 = Tool::pulisciInput($_POST['esperimento-compenso'] ?? 0);
            $logger = "Ernia";
            break;
        case 'Eventi':
            $campo1 = Tool::pulisciInput($_POST['data-evento'] ?? '');
            $campo2 = Tool::pulisciInput($_POST['costo-evento'] ?? 0);
            $campo3 = Tool::pulisciInput($_POST['luogo-evneto'] ?? '');
            $logger = "Elleni";
            break;
        case 'Ripetizioni':
            $campo1 = Tool::pulisciInput($_POST['materia'] ?? '');
            $campo2 = Tool::pulisciInput($_POST['livello'] ?? '');
            $campo3 = Tool::pulisciInput($_POST['prezzo-ripetizioni'] ?? 0);
            $logger = "Rame";
            break;
        default:
            break;
    }
}

$htmlPage = str_replace("[Logger]", $logger, $htmlPage);

$htmlPage = str_replace("[TopNavLog]", Tool::getTopNavLog(), $htmlPage);
$htmlPage = str_replace("[BottomNavLog]", Tool::getBottomNavLog(), $htmlPage);

$htmlPage = str_replace("[ValueTitolo]", $titolo, $htmlPage);
# $htmlPage = str_replace("[ValueCategoria]", $categoria, $htmlPage);
# $htmlPage = str_replace("[ValueCitta]", $citta, $htmlPage);
# $htmlPage = str_replace("[ValueDescrizione]", $descrizione, $htmlPage);

echo $htmlPage;

?>