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
        header("Location: accedi?redirect=annuncio?id=" . $idAnnuncio);
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

    header("Location: annuncio?id=" . $idAnnuncio);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["elimina"]) && Tool::isLoggedIn()) {
    if($db->openDBConnection()) {

        $annuncio = $db->getAnnuncioBase($idAnnuncio);

        if ($annuncio === false) { 
            $db->closeConnection(); 
            Tool::renderError(404);
        }
        
        if ($annuncio["IdUtente"] == $_SESSION["user_id"]) {
            $res = $db->deleteAnnuncio($idAnnuncio);
        }

        $db->closeConnection();
        header("Location: index");
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

    $attr = $db->getAttributiSpecifici($annuncio["Categoria"], $idAnnuncio); 
    $listaAttr = Tool::mappaAttributi($annuncio["Categoria"], $attr);

    $immagini = $db->getImmagini($idAnnuncio);

    if (Tool::isLoggedIn()) { 
        $isPreferito = $db->isPreferito($idAnnuncio, $_SESSION["user_id"]); 
    }
    
    $publisher = $db->getUtente($annuncio["IdUtente"]);

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
        <a href="accedi?redirect=annuncio?id='.$idAnnuncio.'" id="preferiti" class="link btn-base call-to-action" aria-label="Accedi per salvare nei preferiti">★</a>
    ';

} else {

    if ($isPreferito) {
        $preferitiHTML = '
            <form action="annuncio?id='.$idAnnuncio.'" method="POST">
                <input type="hidden" name="azione" value="rimuovi_preferito">
                <input type="hidden" name="id_annuncio" value="'.$idAnnuncio.'">
                <button id="salvato" class="btn-base" aria-label="Annuncio salvato, rimuovi dai preferiti">✓</button>
            </form>
        ';
    } else {
        $preferitiHTML = '
            <form action="annuncio?id='.$idAnnuncio.'" method="POST">
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
        <form action="annuncio?id='.$idAnnuncio.'" method="POST" id="delete-form">
            <input type="hidden" name="elimina" value="rimuovi_annuncio">
            <input type="hidden" name="id_annuncio" value="'.$idAnnuncio.'">
            <button type="submit" class="btn-base">
                <svg class="icon-btn" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M3 6H21M5 6V20C5 21.1 5.9 22 7 22H17C18.1 22 19 21.1 19 20V6M8 6V4C8 2.9 8.9 2 10 2H14C15.1 2 16 2.9 16 4V6" 
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M14 11V17" 
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M10 11V17" 
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>Cancella Annuncio</span>
            </button>
        </form>
    ';
    $modButton = '
        <a href="modificaAnnuncio?id='.$idAnnuncio.'" class="btn-base call-to-action link no-underline">
            <svg class="icon-btn" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                <path fill="currentColor" d="M21.12 2.71c-1.17-1.17-3.07-1.17-4.24 0l-1.68 1.68-7.9 7.9c-.13.13-.22.3-.26.48l-1 4c-.1.34 0 .7.24.94.24.24.6.34.94.24l4-1c.18-.04.35-.13.48-.26l7.85-7.85 1.74-1.74c1.17-1.17 1.17-3.07 0-4.24zm-2.83 1.41c.39-.39 1.02-.39 1.41 0l.17.17c.39.39.39 1.02 0 1.41l-1.02 1.02-1.58-1.58 1.02-1.02zM15.9 6.52l1.56 1.62-6.96 6.96-2.11.53.53-2.11 6.98-7zM4 8c0-.55.45-1 1-1h5c.55 0 1-.45 1-1s-.45-1-1-1H5C3.34 5 2 6.34 2 8v11c0 1.66 1.34 3 3 3h11c1.66 0 3-1.34 3-3v-5c0-.55-.45-1-1-1s-1 .45-1 1v5c0 .55-.45 1-1 1H5c-.55 0-1-.45-1-1V8z"/>
            </svg>
            <span>Modifica annuncio</span>
        </a>
    ';
}

$htmlPage = str_replace("[TitoloSEO]", Tool::titoloSEO($annuncio["Titolo"], $annuncio["NomeCitta"]), $htmlPage);
$htmlPage = str_replace("[TitoloAnnuncio]", htmlspecialchars($annuncio["Titolo"]), $htmlPage); 
$htmlPage = str_replace("[CittaAnnuncio]", htmlspecialchars($annuncio["NomeCitta"]), $htmlPage); 
$htmlPage = str_replace("[DescrizioneAnnuncio]", Tool::convertiInParagrafi($annuncio["Descrizione"]), $htmlPage);

$htmlPage = str_replace("[DtAttr1]", $listaAttr[0][0], $htmlPage); 
$htmlPage = str_replace("[DdAttr1]", $listaAttr[0][1], $htmlPage); 
$htmlPage = str_replace("[DtAttr2]", $listaAttr[1][0], $htmlPage); 
$htmlPage = str_replace("[DdAttr2]", $listaAttr[1][1], $htmlPage); 
$htmlPage = str_replace("[DtAttr3]", $listaAttr[2][0], $htmlPage); 
$htmlPage = str_replace("[DdAttr3]", $listaAttr[2][1], $htmlPage); 

$htmlPage = str_replace("[profPubblico]", $publisher["Email"], $htmlPage); 
$htmlPage = str_replace("[autore]", $publisher["IdUtente"], $htmlPage); 

$htmlPage = str_replace("[CaroselloPrincipale]", $caroselloPrincipale, $htmlPage);
$htmlPage = str_replace("[CaroselloThumbnails]", $caroselloThumbnails, $htmlPage);

$htmlPage = str_replace("[PreferitiButton]", $preferitiHTML, $htmlPage);
$htmlPage = str_replace("[RimuoviButton]", $bottonRimuovi, $htmlPage);
$htmlPage = str_replace("[ModificaButton]", $modButton, $htmlPage);
$htmlPage = str_replace("[TopNavBar]", Tool::buildTopNavBar("annuncio"), $htmlPage);
$htmlPage = str_replace("[BottomNavBar]", Tool::buildBottomNavBar("annuncio"), $htmlPage);

echo $htmlPage;
