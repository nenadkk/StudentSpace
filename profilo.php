<?php

require_once "tool.php";
require_once "dbConnect.php";

if (!Tool::isLoggedIn()) {
    header("Location: accedi.php?redirect=profilo.php");
    exit;
}

$htmlPage = file_get_contents(__DIR__ . "/pages/profilo.html");

$idUtente = $_SESSION["user_id"];
$infoUtente = "";
$annunciUtente = "";
$nomeUtente = "";
$cognomeUtente = "";
$cittaUtente = "";
$emailUtente = "";
$numPreferiti = "";
$numPubblicati = "";

$db = new DB\DBAccess();
if($db->openDBConnection()) {
    $infoUtente = $db->getUtente($idUtente);
    if($infoUtente !== false) {
        $nomeUtente = $infoUtente["Nome"];
        $cognomeUtente = $infoUtente["Cognome"];
        $cittaUtente = $infoUtente["NomeCitta"];
        $emailUtente = $infoUtente["Email"];

        $annunciPreferiti = $db->getAnnunciPreferiti($idUtente);
        if($annunciPreferiti !== false) {
            $cardsPreferiti = Tool::createCard($annunciPreferiti);
            $numPreferiti = count($annunciPreferiti);
        } else {
            $cardsPreferiti = '<li class="centered">
                        <p>Nessun annuncio tra i preferiti.</p>
                        <div class="azioni">
                            <a class="link btn-base call-to-action" href="esplora">Esplora gli annunci</a>
                        </div>
                    </li>';
            $numPreferiti = 0;
        }

        $annunciUtente = $db->getAnnunciUtente($idUtente);
        if($annunciUtente !== false) {
            $cards = Tool::createCard($annunciUtente);
            $numPubblicati = count($annunciUtente);
        } else {
            $cards = '<li class="centered">
                        <p>Nessun annuncio pubblicato.</p>
                        <div class="azioni">
                            <a class="link btn-base call-to-action" href="pubblica">Pubblica un annuncio</a>
                        </div>
                    </li>';
            $numPubblicati = 0;
        }
    }
    $db->closeConnection();
} else {
    Tool::renderError(500);
}

$htmlPage = str_replace("[IdUtente]", $idUtente, $htmlPage);
$htmlPage = str_replace("[Nome]", $nomeUtente, $htmlPage);
$htmlPage = str_replace("[Cognome]", $cognomeUtente, $htmlPage);
$htmlPage = str_replace("[Citta]", $cittaUtente, $htmlPage);
$htmlPage = str_replace("[Email]", $emailUtente, $htmlPage);
$htmlPage = str_replace("[NumPreferiti]", $numPreferiti, $htmlPage);
$htmlPage = str_replace("[CardsPreferiti]", $cardsPreferiti, $htmlPage);
$htmlPage = str_replace("[NumPubblicati]", $numPubblicati, $htmlPage);
$htmlPage = str_replace("[Cards]", $cards, $htmlPage);
$htmlPage = str_replace("[TopNavBar]", Tool::buildTopNavBar("profilo"), $htmlPage);
$htmlPage = str_replace("[BottomNavBar]", Tool::buildBottomNavBar("profilo"), $htmlPage);

echo $htmlPage;

?>