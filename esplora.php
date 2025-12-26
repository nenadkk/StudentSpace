<?php

require_once "dbConnect.php";
require_once "tool.php";

use DB\DBAccess;

$dbAccess = new DBAccess();

$htmlPage = file_get_contents("pages/esplora.html");

$categoria = "";
$cerca = "";

$cards = "";
 
$filtriGenerali = array(
                "citta"=>"",
                "pubblicazione-inizio"=>"",
                "pubblicazione-fine"=>"");

$filtriAffitti = array(
                "coinquilini-max"=>"",
                "costo-mese-affitto-max"=>"",
                "indirizzo-affitto"=>"");

$filtriEsperimenti = array(
                "laboratorio"=>"",
                "esperimento-durata-min"=>"",
                "esperimento-durata-max"=>"",
                "esperimento-compenso-min"=>"",
                "esperimento-compenso-max"=>"");

$filtriEventi = array(
                "evento-inizio"=>"",
                "evento-fine"=>"",
                "evento-costo-max"=>"",
                "luogo-evento"=>"");

$filtriRipetizioni = array(
                "materia"=>"",
                "livello"=>"",
                "prezzo-ripetizioni-max"=>"");


function controlloGeneraliQuery($query, $filtriGenerali) {
    if ($filtriGenerali['citta'] != '') 
    {
        if (!str_contains($query,"WHERE"))
            $query.="WHERE ";

        $query.= "NomeCitta='".$_GET['citta']."' AND ";
    }

    //Potrebbe essere necessario usare DATE_TRUNC per le prossime due
    if ($filtriGenerali['pubblicazione-inizio'] != '') 
    {
        if (!str_contains($query,"WHERE"))
            $query.="WHERE ";

        $query.= "DataPubblicazione >='".$_GET['pubblicazione-inizio']."' AND ";
    }

    if ($filtriGenerali['pubblicazione-fine'] != '') 
    {
        if (!str_contains($query,"WHERE"))
            $query.="WHERE ";

        $query.= "DataPubblicazione <='".$_GET['pubblicazione-fine']."' AND ";
    }

    return $query;
}

if(isset($_GET['submit'])) 
{
    $cerca = $_GET['cerca'] ?? "";
    $categoria =$_GET['categoria'] ?? "";

    foreach ($filtriGenerali as $key => $value) {
        $filtriGenerali[$key] = $_GET[$key] ?? "";
    }

    $query="SELECT * ";

    switch ($categoria) {
        case '':
            $query .= "FROM Annuncio a JOIN Citta c ON a.IdCitta=c.IdCitta 
                      LEFT JOIN ImmaginiAnnuncio as i ON a.IdAnnuncio = i.IdAnnuncio ";
            $query = controlloGeneraliQuery($query, $filtriGenerali); 

            break;
        

        case 'Affitti':
            foreach ($filtriAffitti as $key => $value) {
                $filtriAffitti[$key] = isset($_GET[$key])? $_GET[$key] : '';
            }

            $query .= "FROM Annuncio a JOIN AnnuncioAffitti f ON a.IdAnnuncio= f.IdAnnuncio 
                       JOIN Citta c ON a.IdCitta=c.IdCitta 
                       LEFT JOIN ImmaginiAnnuncio as i ON a.IdAnnuncio = i.IdAnnuncio ";

            $query = controlloGeneraliQuery($query, $filtriGenerali); 

            if ($filtriAffitti['coinquilini-max'] != '') 
            {
                if (!str_contains($query,"WHERE"))
                    $query.="WHERE ";
                $query.= "NumeroInquilini<='".$_GET['coinquilini-max']."' AND ";
            }

            if ($filtriAffitti['costo-mese-affitto-max'] != '') 
            {
                if (!str_contains($query,"WHERE"))
                    $query.="WHERE ";
                $query.= "PrezzoMensile<='".$_GET['costo-mese-affitto-max']."' AND ";
            }

            if ($filtriAffitti['indirizzo-affitto'] != '') 
            {
                if (!str_contains($query,"WHERE"))
                    $query.="WHERE ";
                $query.= "Indirizzo LIKE '%".$_GET['indirizzo-affitto']."%' AND ";
            }
            break;

        case 'Esperimenti':
            foreach ($filtriEsperimenti as $key => $value) {
                $filtriEsperimenti[$key] = isset($_GET[$key])? $_GET[$key] : '';
            }

            $query .= "FROM Annuncio a JOIN AnnuncioEsperimenti e ON a.IdAnnuncio= e.IdAnnuncio 
                       JOIN Citta c ON a.IdCitta=c.IdCitta 
                       LEFT JOIN ImmaginiAnnuncio as i ON a.IdAnnuncio = i.IdAnnuncio ";
            $query = controlloGeneraliQuery($query, $filtriGenerali); 

            if ($filtriEsperimenti['laboratorio'] != '') 
            {
                if (!str_contains($query,"WHERE"))
                    $query.="WHERE ";
                $query.= "Laboratorio LIKE '%".$_GET['laboratorio']."%' AND ";
            }

            if ($filtriEsperimenti['esperimento-durata-min'] != '') 
            {
                if (!str_contains($query,"WHERE"))
                    $query.="WHERE ";
                $query.= "DurataPrevista>='".$_GET['esperimento-durata-min']."' AND ";
            }

            if ($filtriEsperimenti['esperimento-durata-max'] != '') 
            {
                if (!str_contains($query,"WHERE"))
                    $query.="WHERE ";
                $query.= "DurataPrevista<='".$_GET['esperimento-durata-max']."' AND ";
            }
            if ($filtriEsperimenti['esperimento-compenso-min'] != '') 
            {
                if (!str_contains($query,"WHERE"))
                    $query.="WHERE ";
                $query.= "Compenso>='".$_GET['esperimento-compenso-min']."' AND ";
            }
            if ($filtriEsperimenti['esperimento-compenso-max'] != '') 
            {
                if (!str_contains($query,"WHERE"))
                    $query.="WHERE ";
                $query.= "Compenso<='".$_GET['esperimento-compenso-max']."' AND ";
            }
            break;

           
       case 'Eventi':
            foreach ($filtriEventi as $key => $value) {
                $filtriEventi[$key] = isset($_GET[$key])? $_GET[$key] : '';
            }

            $query .= "FROM Annuncio a JOIN AnnuncioEventi e ON a.IdAnnuncio= e.IdAnnuncio 
                       JOIN Citta c ON a.IdCitta=c.IdCitta 
                       LEFT JOIN ImmaginiAnnuncio as i ON a.IdAnnuncio = i.IdAnnuncio ";
            $query = controlloGeneraliQuery($query, $filtriGenerali);

            if ($filtriEventi['luogo-evento'] != '') 
            {
                if (!str_contains($query,"WHERE"))
                    $query.="WHERE ";
                $query.= "Luogo LIKE '%".$_GET['luogo-evento']."%' AND ";
            }

            if ($filtriEventi['evento-inizio'] != '') 
            {
                if (!str_contains($query,"WHERE"))
                    $query.="WHERE ";
                $query.= "DataEvento>='".$_GET['evento-inizio']."' AND ";
            }

            if ($filtriEventi['evento-fine'] != '') 
            {
                if (!str_contains($query,"WHERE"))
                    $query.="WHERE ";
                $query.= "DataEvento<='".$_GET['evento-fine']."' AND ";
            }
            if ($filtriEventi['evento-costo-max'] != '') 
            {
                if (!str_contains($query,"WHERE"))
                    $query.="WHERE ";
                $query.= "CostoEntrata<='".$_GET['evento-costo-max']."' AND ";
            }

            break;

        case 'Ripetizioni':
            foreach ($filtriRipetizioni as $key => $value) {
                $filtriRipetizioni[$key] = isset($_GET[$key])? $_GET[$key] : '';
            }

            $query .= "FROM Annuncio a JOIN AnnuncioRipetizioni r ON a.IdAnnuncio= r.IdAnnuncio 
                       JOIN Citta c ON a.IdCitta=c.IdCitta 
                       LEFT JOIN ImmaginiAnnuncio as i ON a.IdAnnuncio = i.IdAnnuncio ";
            $query = controlloGeneraliQuery($query,$filtriGenerali); 

            if ($filtriRipetizioni['materia'] != '') 
            {
                if (!str_contains($query,"WHERE"))
                    $query.="WHERE ";
                $query.= "Materia LIKE '%".$_GET['materia']."%' AND ";
            }

            if ($filtriRipetizioni['livello'] != '') 
            {
                if (!str_contains($query,"WHERE"))
                    $query.="WHERE ";
                $query.= "Livello LIKE '%".$_GET['livello']."%' AND ";
            }

            if ($filtriRipetizioni['prezzo-ripetizioni-max'] != '') 
            {
                if (!str_contains($query,"WHERE"))
                    $query.="WHERE ";
                $query.= "PrezzoOrario<='".$_GET['prezzo-ripetizioni-max']."' AND ";
            }
            break;

        default:
            break;
    }
    //CERCA
    if ($cerca != '') 
    {
        if (!str_contains($query,"WHERE"))
            $query.="WHERE ";
        $query.= "(Titolo LIKE '%".$_GET['cerca']."%' OR Descrizione LIKE '%".$_GET['cerca']."%') AND ";
    }
    
    //Per le immagini
    if (!str_contains($query,"WHERE"))
        $query.="WHERE ";
    $query.= "i.Ordine = 1;";

    $dbAccess->openDBConnection();
    $cardsData = $dbAccess->searchEsplora($query);
    $dbAccess->closeConnection();

    if($cardsData !== false)
        $cards = Tool::createCard($cardsData);
    else 
        $cards .= file_get_contents("pages/cardTemplate.html");

}
else
{
    //nel caso non siano stati applicati filtri o ricerche mostro tutti gli annunci presenti
    $dbAccess->openDBConnection();
    $cardsData = $dbAccess->getAnnouncements();
    $dbAccess->closeConnection();

    if($cardsData !== false)
        $cards = Tool::createCard($cardsData);
    else 
        $cards .= file_get_contents("pages/cardTemplate.html");
}

$htmlPage = str_replace("[cerca]", $cerca, $htmlPage);


//Sostituzione dei placeholder

foreach ($filtriGenerali as $key => $value) {
    $htmlPage = str_replace("[$key]", $value, $htmlPage);
}
foreach ($filtriAffitti as $key => $value) {
    $htmlPage = str_replace("[$key]", $value, $htmlPage);
}
foreach ($filtriEsperimenti as $key => $value) {
    $htmlPage = str_replace("[$key]", $value, $htmlPage);
}
foreach ($filtriEventi as $key => $value) {
    $htmlPage = str_replace("[$key]", $value, $htmlPage);
}
foreach ($filtriRipetizioni as $key => $value) {
    $htmlPage = str_replace("[$key]", $value, $htmlPage);
}

$htmlPage = str_replace("[noneSelected]", $categoria=='' ? 'selected' : '' , $htmlPage);
$htmlPage = str_replace("[affittiSelected]", $categoria=='Affitti' ? 'selected' : '' , $htmlPage);
$htmlPage = str_replace("[esperimentiSelected]", $categoria=='Esperimenti' ? 'selected' : '' , $htmlPage);
$htmlPage = str_replace("[eventiSelected]", $categoria=='Eventi' ? 'selected' : '' , $htmlPage);
$htmlPage = str_replace("[ripetizioniSelected]", $categoria=='Ripetizioni' ? 'selected' : '' , $htmlPage);


//INSERIMENTO CARDS
$htmlPage = str_replace("[Cards]", $cards, $htmlPage);

$htmlPage = str_replace("[TopNavLog]", Tool::getTopNavLog(), $htmlPage);
$htmlPage = str_replace("[BottomNavLog]", Tool::getBottomNavLog(), $htmlPage);

echo $htmlPage;
?>
