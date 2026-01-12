<?php

require_once "dbConnect.php";
require_once "tool.php";

use DB\DBAccess;
$db = new DB\DBAccess();

$htmlPage = file_get_contents(__DIR__ . "/pages/annuncio.html");

if (isset($_GET["id"]) && ctype_digit($_GET["id"])) { 
    $idAnnuncio = intval($_GET["id"]); 
} else { 
    Tool::renderError(404);
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["azione"])) {

    if (!Tool::isLoggedIn()) {
        header("Location: accedi.php?redirect=annuncio.php?id=" . $idAnnuncio);
        exit;
    }

    $idUtente = $_SESSION["user_id"];
    $idAnnuncio = intval($_POST["id_annuncio"]);

    if ($db->openDBConnection()) {

        if ($_POST["azione"] === "aggiungi_preferito") {
            $db->insertPrefe($idAnnuncio, $idUtente);
        }

        if ($_POST["azione"] === "rimuovi_preferito") {
            $db->deletePrefe($idAnnuncio, $idUtente);
        }

        $db->closeConnection();
    } else {
        Tool::renderError(500);
    }

    header("Location: annuncio.php?id=" . $idAnnuncio);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["elimina"]) && Tool::isLoggedIn()) {
    if($db->openDBConnection()) {

        $annuncio = $db->getAnnuncioBase($idAnnuncio)[0];

        if ($annuncio === false) { 
            $db->closeConnection(); 
            Tool::renderError(404);
        }
        
        if ($annuncio["IdUtente"] == $_SESSION["user_id"]) {
            $res = $db->deleteAnnuncio($idAnnuncio);
        }

        $db->closeConnection();
        header("Location: /index.php");
        exit;
    } else {
        Tool::renderError(500);
    }
}

$annuncio = ""; 
$attr = "";
$listaAttr = "";
$immagini = [];  
$isPreferito = false; 

if ($db->openDBConnection()) {

    $annuncio = $db->getAnnuncioBase($idAnnuncio); 

    if ($annuncio === false) { 
        $db->closeConnection(); 
        Tool::renderError(404);
    }

    $annuncio = $annuncio[0];

    $attr = $db->getAttributiSpecifici($annuncio["Categoria"], $idAnnuncio); 
    $attr = $attr[0];
    $listaAttr = Tool::mappaAttributi($annuncio["Categoria"], $attr);

    $immagini = $db->getImmagini($idAnnuncio);

    if (Tool::isLoggedIn()) { 
        $isPreferito = $db->isPreferito($idAnnuncio, $_SESSION["user_id"]); 
    }

    $db->closeConnection();
} else {
    Tool::renderError(500);
}

$caroselloPrincipale = ""; 
if (!empty($immagini)) { 
    $img = $immagini[0]; 
    $alt = ($img["Decorativa"] == 1) ? "" : htmlspecialchars($img["AltText"]); 
    $caroselloPrincipale = ' <img src="img_annunci/'.$img["Percorso"].'" alt="'.$alt.'" class="attiva"> '; 
}

$caroselloThumbnails = ""; 
foreach ($immagini as $index => $img) { 
    $alt = ($img["Decorativa"] == 1) ? "" : htmlspecialchars($img["AltText"]); 
    $active = $index === 0 ? "attiva" : ""; 
    $caroselloThumbnails .= ' <img src="img_annunci/'.$img["Percorso"].'" alt="'.$alt.'" class="miniatura '.$active.'"> '; 
}

if (!Tool::isLoggedIn()) {

    $preferitiHTML = '
        <a href="accedi.php?redirect=annuncio.php?id='.$idAnnuncio.'" id="preferiti" class="link btn-base call-to-action" aria-label="Accedi per salvare nei preferiti">★</a>
    ';

} else {

    if ($isPreferito) {
        $preferitiHTML = '
            <form action="annuncio/'.$idAnnuncio.'" method="POST">
                <input type="hidden" name="azione" value="rimuovi_preferito">
                <input type="hidden" name="id_annuncio" value="'.$idAnnuncio.'">
                <button id="salvato" class="btn-base" aria-label="Annuncio salvato, rimuovi dai preferiti">✓</button>
            </form>
        ';
    } else {
        $preferitiHTML = '
            <form action="annuncio/'.$idAnnuncio.'" method="POST">
                <input type="hidden" name="azione" value="aggiungi_preferito">
                <input type="hidden" name="id_annuncio" value="'.$idAnnuncio.'">
                <button id="preferiti" class="btn-base" aria-label="Salva nei preferiti">★</button>
            </form>
        ';
    }
}

$bottonRimuovi = "";
$modButton = "";
if(Tool::isLoggedIn() && $annuncio["IdUtente"] == $_SESSION["user_id"]){
    $bottonRimuovi = '
        <form action="annuncio/'.$idAnnuncio.'" method="POST" id="delete-form">
            <input type="hidden" name="elimina" value="rimuovi_annuncio">
            <input type="hidden" name="id_annuncio" value="'.$idAnnuncio.'">
            <button type="submit" class="btn-base" title:"Cancella l\'annuncio">Cancella Annuncio</button>
        </form>
    ';
    $modButton = '
        <a href="modificaAnnuncio/'.$idAnnuncio.'">
            <button class="btn-base" title:"Modifica l\'annuncio">Modifica Annuncio</button>
        </a>
    ';
}

$htmlPage = str_replace("[TitoloAnnuncio]", htmlspecialchars($annuncio["Titolo"]), $htmlPage); 
$htmlPage = str_replace("[CittaAnnuncio]", htmlspecialchars($annuncio["NomeCitta"]), $htmlPage); 
$htmlPage = str_replace("[DescrizioneAnnuncio]", Tool::convertiInParagrafi($annuncio["Descrizione"]), $htmlPage);

$htmlPage = str_replace("[DtAttr1]", $listaAttr[0][0], $htmlPage); 
$htmlPage = str_replace("[DdAttr1]", $listaAttr[0][1], $htmlPage); 
$htmlPage = str_replace("[DtAttr2]", $listaAttr[1][0], $htmlPage); 
$htmlPage = str_replace("[DdAttr2]", $listaAttr[1][1], $htmlPage); 
$htmlPage = str_replace("[DtAttr3]", $listaAttr[2][0], $htmlPage); 
$htmlPage = str_replace("[DdAttr3]", $listaAttr[2][1], $htmlPage); 

$htmlPage = str_replace("[CaroselloPrincipale]", $caroselloPrincipale, $htmlPage);
$htmlPage = str_replace("[CaroselloThumbnails]", $caroselloThumbnails, $htmlPage);

$htmlPage = str_replace("[PreferitiButton]", $preferitiHTML, $htmlPage);
$htmlPage = str_replace("[RimuoviButton]", $bottonRimuovi, $htmlPage);
$htmlPage = str_replace("[ModificaButton]", $modButton, $htmlPage);
$htmlPage = str_replace("[TopNavBar]", Tool::buildTopNavBar("annuncio"), $htmlPage);
$htmlPage = str_replace("[BottomNavBar]", Tool::buildBottomNavBar("annuncio"), $htmlPage);

echo $htmlPage;