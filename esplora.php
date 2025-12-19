<?php

require_once "dbConnect.php";
require_once "tool.php";

use DB\DBAccess;

$htmlPage = file_get_contents("pages/esplora.html");

$dbAccess = new DBAccess();
if (!$dbAccess->openDBConnection()) {
    echo "Connessione al database fallita.";
    exit;
}
$cardsData = $dbAccess->getAnnouncements();
$dbAccess->closeConnection();

$cards = "";
$cardhtml = "";

if($cardsData !== false) {
    foreach ($cardsData as $card) {
        $cardhtml = file_get_contents("pages/cardTemplate.html");
        $cardhtml = str_replace("[ImmagineAnnuncio]", htmlspecialchars($card['Percorso']), $cardhtml);
        $cardhtml = str_replace("[AltImmagineAnnuncio]", htmlspecialchars($card['AltText']), $cardhtml);
        $cardhtml = str_replace("[TitoloAnnuncio]", htmlspecialchars($card['Titolo']), $cardhtml);
        
        $dataDB = $card['DataPubblicazione'];
        $data = new DateTime($dataDB);
        $dataISO = $data->format('Y-m-d'); # formato ISO per l'attributo datetime
        $dataIT = $data->format('d/m/Y'); # formato italiano per la visualizzazione
        $cardhtml = str_replace("[dataInsertUs]", htmlspecialchars($dataISO), $cardhtml);
        $cardhtml = str_replace("[DataInsert]", htmlspecialchars($dataIT), $cardhtml);

        $cardhtml = str_replace("[CittaAnnuncio]", htmlspecialchars($card['nomeCitta']), $cardhtml);
        $cardhtml = str_replace("[CategoriaAnnuncioMinuscolo]", htmlspecialchars(strtolower($card['Categoria'])), $cardhtml);
        $cardhtml = str_replace("[CategoriaAnnuncio]", htmlspecialchars($card['Categoria']), $cardhtml);
        $cardhtml = str_replace("[idAnnuncio]", htmlspecialchars($card['IdAnnuncio']), $cardhtml);
        
        $cards .= $cardhtml;
    }
} else {
    $cardhtml = file_get_contents("pages/cardTemplate.html");
    $cards .= $cardhtml;
}

$htmlPage = str_replace("[cards]", $cards, $htmlPage);

echo $htmlPage;
?>
