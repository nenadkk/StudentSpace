<?php

require_once "dbConnect.php";
require_once "tool.php";

use DB\DBAccess;

$dbAccess = new DBAccess();

$htmlPage = file_get_contents("pages/esplora.html");

$categoria = "";
$cerca = "";
$filtri = array();

$cards = "";

if(isset($_GET['submit'])) 
{
    $cerca = $_GET['cerca'] ?? "";
    $categoria =$_GET['categoria'] ?? "";

    
    $filtri = array(
                "citta"=> $_GET['citta'] ?? "",
                "pubblicazione-inizio"=> $_GET['pubblicazione-inizio'] ?? "",
                "pubblicazione-fine"=> $_GET['pubblicazione-fine'] ?? "",

                //SPIEGAZIONE DELLE ASSEGNAZIONI SUBITO SOTTO:
                //- se la categoria a cui appartiene il campo è selezionata e il campo è stato inserito, allora assegno il valore
                //- in tutti gli altri casi lascio vuoto

                //Se non facessi questo ci sarebbe la seguente situa: (Es) 
                //seleziono Affitti -> metto num coinquilini a 5 -> seleziono eventi 
                //->faccio submit -> seleziono Affitti e mi ritrovo num coinquilini a 5
                //Vogliamo che i parametri messi nei filtri vengano visualizzati
                //anche dopo la ricerca, ma solo quelli della categoria selezionata
                //al momento del sumbit

                "coinquilini-max"=>($categoria=='Affitti' && isset($_GET['coinquilini-max'])? $_GET['coinquilini-max'] : ''),
                "costo-mese-affitto-max"=>($categoria=='Affitti' && isset($_GET['costo-mese-affitto-max'])? $_GET['costo-mese-affitto-max'] : ''),
                "indirizzo-affitto"=>($categoria=='Affitti' && isset($_GET['indirizzo-affitto'])? $_GET['indirizzo-affitto'] : ''),

                "laboratorio"=>($categoria=='Esperimenti' && isset($_GET['laboratorio'])? $_GET['laboratorio'] : ''),
                "esperimento-durata-min"=>($categoria=='Esperimenti' && isset($_GET['esperimento-durata-min'])? $_GET['esperimento-durata-min'] : ''),
                "esperimento-durata-max"=>($categoria=='Esperimenti' && isset($_GET['esperimento-durata-max'])? $_GET['esperimento-durata-max'] : ''),
                "esperimento-compenso-min"=>($categoria=='Esperimenti' && isset($_GET['esperimento-compenso-min'])? $_GET['esperimento-compenso-min'] : ''),
                "esperimento-compenso-max"=>($categoria=='Esperimenti' && isset($_GET['esperimento-compenso-max'])? $_GET['esperimento-compenso-max'] : ''),

                "evento-inizio"=>($categoria=='Eventi' && isset($_GET['evento-inizio'])? $_GET['evento-inizio'] : ''),
                "evento-fine"=>($categoria=='Eventi' && isset($_GET['evento-fine'])? $_GET['evento-fine'] : ''),
                "evento-costo-max"=>($categoria=='Eventi' && isset($_GET['evento-costo-max'])? $_GET['evento-costo-max'] : ''),
                "luogo-evento"=>($categoria=='Eventi' && isset($_GET['luogo-evento'])? $_GET['luogo-evento'] : ''),

                "materia"=>($categoria=='Ripetizioni' && isset($_GET['materia'])? $_GET['materia'] : ''),
                "livello"=>($categoria=='Ripetizioni' && isset($_GET['livello'])? $_GET['livello'] : ''),
                "prezzo-ripetizioni-max"=>($categoria=='Ripetizioni' && isset($_GET['prezzo-ripetizioni-max'])? $_GET['prezzo-ripetizioni-max'] : ''));

    switch ($categoria) {
        case '':
            break;
        

        case 'Affitti':
            break;

        case 'Esperimenti':
            break;

        case 'Eventi':
            break;

        case 'Ripetizioni':
            break;
        
        default:
            break;
    }

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

foreach ($filtri as $key => $value) {
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
