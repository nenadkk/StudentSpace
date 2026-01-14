<?php

require_once "tool.php";
require_once "dbConnect.php";

use DB\DBAccess;
$db = new DB\DBAccess();

$htmlPage = file_get_contents("pages/profiloPubblico.html");

$idUtente = $_SESSION["user_id"];
$infoUtente = "";
$annunciUtente = "";
$emailUtente = "";
$numPubblicati = "";

if($db->openDBConnection()) {
    $infoUtente = $db->getUtente($idUtente);
    if($infoUtente !== false) {
        $emailUtente = $infoUtente["Email"];

        $annunciUtente = $db->getAnnunciUtente($idUtente);
        if($annunciUtente !== false) {
            $cards = Tool::createCard($annunciUtente);
            $numPubblicati = count($annunciUtente);
        } else {
            $cards = '<li class="centered">
                        <p>Nessun annuncio pubblicato.</p>
                        <div class="azioni">
                            <a class="link btn-base call-to-action" href="pubblica.php">Pubblica un annuncio</a>
                        </div>
                    </li>';
            $numPubblicati = 0;
        }
    }
    $db->closeConnection();
} else {
    Tool::renderError(500);
}

$htmlPage = str_replace("[Email]", $emailUtente, $htmlPage);
$htmlPage = str_replace("[Cards]", $cards, $htmlPage);
$htmlPage = str_replace("[TopNavBar]", Tool::buildTopNavBar("profilo"), $htmlPage);
$htmlPage = str_replace("[BottomNavBar]", Tool::buildBottomNavBar("profilo"), $htmlPage);

echo $htmlPage;

?>
