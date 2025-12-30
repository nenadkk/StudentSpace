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
            $numPreferitiy = count($cardsPreferiti);
        } else {
            $cardsPreferiti = '<div class="centered">
                        <p>Nessun annuncio tra i preferiti.</p>
                        <div class="azioni">
                            <a class="link btn-base call-to-action" href="esplora.php">Esplora gli annunci</a>
                        </div>
                    </div>';
            $numPreferiti = 0;
        }

        $annunciUtente = $db->getAnnunciUtente($idUtente);
        if($annunciUtente !== false) {
            $cards = Tool::createCard($annunciUtente);
            $numPubblicati = count($annunciUtente);
        } else {
            $cards = '<div class="centered">
                        <p>Nessun annuncio pubblicato.</p>
                        <div class="azioni">
                            <a class="link btn-base call-to-action" href="pubblica.php">Pubblica un annuncio</a>
                        </div>
                    </div>';
            $numPubblicati = 0;
        }
    }
    $db->closeConnection();
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
$htmlPage = str_replace("[TopNavLog]", Tool::getTopNavLog(), $htmlPage);
$htmlPage = str_replace("[BottomNavLog]", Tool::getBottomNavLog(), $htmlPage);

echo $htmlPage;

?>