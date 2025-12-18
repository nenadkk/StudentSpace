<?php

require_once "dbConnect.php";
require_once "tool.php";

use DB\DBAccess;

$htmlPage = file_get_contents("pages/index.html");

$dbAccess = new DBAccess();
if (!$dbAccess->openDBConnection()) {
    echo "Connessione al database fallita.";
    exit;
}
$cardsData = $dbAccess->getLastAnnouncements();
$dbAccess->closeConnection();

$cards = "";
$cardhtml = "";

if($cardsData !== false) {
    foreach ($cardsData as $card) {
        $cardhtml = file_get_contents("pages/cardTemplate.html");
        $cardhtml = str_replace("[ImmagineAnnuncio]", htmlspecialchars($card['Percorso']), $cardhtml);
        $cardhtml = str_replace("[AltImmagineAnnuncio]", htmlspecialchars($card['AltText']), $cardhtml);
        $cardhtml = str_replace("[TitoloAnnuncio]", htmlspecialchars($card['Titolo']), $cardhtml);
        
        # da capire cosa mettere per la data, se bisogna manipolarla o meno
        # $cardhtml = str_replace("[dataInsertUs]", htmlspecialchars($card['DataPubblicazione']), $cardhtml);
        # $cardhtml = str_replace("[DataInsert]", htmlspecialchars($card['DataPubblicazione']), $cardhtml);

        $cardhtml = str_replace("[CittaAnnuncio]", htmlspecialchars($card['nomeCitta']), $cardhtml);
        $cardhtml = str_replace("[CategoriaAnnuncioMinuscolo]", htmlspecialchars($card['CategoriaMinuscolo']), $cardhtml); # da trasformare la categoria in minuscolo
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