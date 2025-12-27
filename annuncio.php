<?php

require_once "dbConnect.php";
require_once "tool.php";

session_start();

use DB\DBAccess;

$htmlPage = file_get_contents("pages/annuncio.html");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["azione"])) {

    if (!Tool::isLoggedIn()) {
        header("Location: accedi.php");
        exit;
    }

    $idUtente = $_SESSION["user_id"];
    $idAnnuncio = intval($_POST["id_annuncio"]);

    $db = new DBAccess();
    if ($db->openDBConnection()) {

        if ($_POST["azione"] === "aggiungi_preferito") {
            $db->insertPrefe($idAnnuncio, $idUtente);
        }

        if ($_POST["azione"] === "rimuovi_preferito") {
            $db->deletePrefe($idAnnuncio, $idUtente);
        }

        $db->closeConnection();
    }

    header("Location: annuncio.php?id=" . $idAnnuncio);
    exit;
}

if (isset($_GET["id"]) && ctype_digit($_GET["id"])) { 
    $idAnnuncio = intval($_GET["id"]); 
} else { 
    die("Annuncio non valido."); 
}
 
$annuncio = ""; 
$attr = "";
$listaAttr = "";
$immagini = [];  
$isPreferito = false; 

$db = new DB\DBAccess(); 
if ($db->openDBConnection()) {

    $annuncio = $db->getAnnuncioBase($idAnnuncio); 

    if ($annuncio === false) { 
        $db->closeConnection(); 
        die("Annuncio non trovato."); 
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
        <a href="accedi.php" id="preferiti-style" class="btn-base" aria-label="Accedi per salvare nei preferiti">★ Preferiti</a>
        <a href="accedi.php" id="preferiti-mini" class="btn-base" aria-label="Accedi per salvare nei preferiti">★</a>
    ';

} else {

    if ($isPreferito) {
        $preferitiHTML = '
            <form action="annuncio.php?id='.$idAnnuncio.'" method="POST">
                <input type="hidden" name="azione" value="rimuovi_preferito">
                <input type="hidden" name="id_annuncio" value="'.$idAnnuncio.'">
                <button id="preferiti-style" class="btn-base" title:"Rimuovi dai preferiti">✓ Salvato</button>
                <button id="preferiti-mini" class="btn-base" aria-label="Annuncio salvato, rimuovi dai preferiti">✓</button>
            </form>
        ';
    } else {
        $preferitiHTML = '
            <form action="annuncio.php?id='.$idAnnuncio.'" method="POST">
                <input type="hidden" name="azione" value="aggiungi_preferito">
                <input type="hidden" name="id_annuncio" value="'.$idAnnuncio.'">
                <button id="preferiti-style" class="btn-base" aria-label="Salva nei">★ Preferiti</button>
                <button id="preferiti-mini" class="btn-base" aria-label="Salva nei preferiti">★</button>
            </form>
        ';
    }
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

$htmlPage = str_replace("[TopNavLog]", Tool::getTopNavLog(), $htmlPage);
$htmlPage = str_replace("[BottomNavLog]", Tool::getBottomNavLog(), $htmlPage);

echo $htmlPage;