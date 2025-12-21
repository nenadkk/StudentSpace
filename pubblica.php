<?php

require_once "tool.php";
require_once "dbConnect.php";

use DB\DBAccess;

$htmlPage = file_get_contents("pages/pubblica.html");

$titolo = "";
$categoria = ""; # da capire se queste due cose con la datalist sono impostabili
$citta = "";
$descrizione = "";

if(isset($_POST['submit'])) {
    $titolo = Tool::pulisciInput($_POST['titolo'] ?? '');
    $categoria = Tool::pulisciInput($_POST['categoria'] ?? '');
    $citta = Tool::pulisciInput($_POST['citta'] ?? '');
    $descrizione = Tool::pulisciInput($_POST['descrizione'] ?? '');

    switch ($categoria) {
        case 'Affitti':
            $coinquilini = Tool::pulisciInput($_POST['coinquilini'] ?? '');
            $costoMeseAffitto = Tool::pulisciInput($_POST['costo-mese-affitto'] ?? '');
            $indirizzo = Tool::pulisciInput($_POST['indirizzo-affitto'] ?? '');
            break;
        case 'Esperimenti':
            $laboratorio = Tool::pulisciInput($_POST['laboratorio'] ?? '');
            $esperimentoDurata = Tool::pulisciInput($_POST['esperimento-durata'] ?? '');
            $esperimentoCompenso = Tool::pulisciInput($_POST['esperimento-compenso'] ?? '');
            break;
        case 'Eventi':
            $dataEvento = Tool::pulisciInput($_POST['data-evento'] ?? '');
            $costoEvento = Tool::pulisciInput($_POST['costo-evento'] ?? '');
            $luogoEvento = Tool::pulisciInput($_POST['luogo-evneto'] ?? '');
            break;
        case 'Ripetizioni':
            $materia = Tool::pulisciInput($_POST['materia'] ?? '');
            $livello = Tool::pulisciInput($_POST['livello'] ?? '');
            $prezzoRipetizioni = Tool::pulisciInput($_POST['prezzo-ripetizioni'] ?? '');
            break;
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