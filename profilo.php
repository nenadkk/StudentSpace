<?php

require_once "tool.php";
require_once "dbConnect.php";

if (!Tool::isLoggedIn()) {
    header("Location: accedi.php");
    exit;
}

$htmlPage = file_get_contents("pages/profilo.html");

$idUtente = $_SESSION["user_id"];
$infoUtente = "";
$annunciUtente = "";
$nomeUtente = "";
$cognomeUtente = "";
$cittaUtente = "";
$emailUtente = "";

$db = new DB\DBAccess();
if($db->openDBConnection()) {
    $infoUtente = $db->getUtente($idUtente);
    if($infoUtente !== false) {
        $nomeUtente = $infoUtente["Nome"];
        $cognomeUtente = $infoUtente["Cognome"];
        $cittaUtente = $infoUtente["NomeCitta"];
        $emailUtente = $infoUtente["Email"];
        $annunciUtente = $db->getAnnunciUtente($idUtente);
        if($annunciUtente !== false) {
            $cards = Tool::createCard($annunciUtente);
        } else {
            $cards = '<div class="centered">
                        <p>Nessun annuncio pubblicato.</p>
                        <div class="azioni">
                            <a class="call-to-action" href="pubblica.php">Pubblica un annuncio</a>
                        </div>
                    </div>';
        }
    }
}

$htmlPage = str_replace("[IdUtente]", $idUtente, $htmlPage);
$htmlPage = str_replace("[Nome]", $nomeUtente, $htmlPage);
$htmlPage = str_replace("[Cognome]", $cognomeUtente, $htmlPage);
$htmlPage = str_replace("[Citta]", $cittaUtente, $htmlPage);
$htmlPage = str_replace("[Email]", $emailUtente, $htmlPage);
$htmlPage = str_replace("[Cards]", $cards, $htmlPage);
$htmlPage = str_replace("[TopNavLog]", Tool::getTopNavLog(), $htmlPage);
$htmlPage = str_replace("[BottomNavLog]", Tool::getBottomNavLog(), $htmlPage);

echo $htmlPage;

?>