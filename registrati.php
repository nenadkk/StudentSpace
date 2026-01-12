<?php

require_once "dbConnect.php";
require_once "tool.php";

use DB\DBAccess;
$db = new DBAccess();

$paginaHTML = file_get_contents(__DIR__ . '/pages/registrati.html');

// --- Variabili iniziali ---
$nome = '';
$cognome = '';
$citta = '';
$email = '';
$password = '';
$conferma_password = '';
$idutente = '';

// --- Variabili per gestione errori di inserimento ---
$numMsgErrore=0;
//ognuno di questi array contiene i messaggi di errore relativi a quel campo, nel
//caso si vogliano aggiungere altri controlli (e quindi messaggi di errore) in futuro
$messaggiErrore = array(
    "[errore-nome]" => array(),
    "[errore-cognome]" => array(),
    "[errore-citta]" => array(),
    "[errore-email]" => array(),
    "[errore-password]" => array(),
    "[errore-conferma-password]" => array(),
);

// --- Inserimento lista città nel datalist ---
$cities = [];
if ($db->openDBConnection()) {
    $cities = $db->getAllCity();
    $db->closeConnection();
}

/* -------------------------------
 * SE L’UTENTE HA INVIATO IL FORM
 * ------------------------------- */
if(isset($_POST['submit'])) {

    // Pulizia input
    $nome = Tool::pulisciInput($_POST['nome'] ?? '');
    $cognome = Tool::pulisciInput($_POST['cognome'] ?? '');
    $citta = Tool::pulisciInput($_POST['citta'] ?? '');
    $email = Tool::pulisciInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $conferma_password = $_POST['confermaPassword'] ?? '';

    /* -----------------------------------
     * VALIDAZIONE DEI CAMPI
     * ----------------------------------- */

    if (!Tool::validaNome($nome)) {
        $messaggiErrore['[errore-nome]'][] = "Il nome deve contenere solo lettere e deve avere almeno 2 caratteri.";
        $numMsgErrore++;
    }
    if(Tool::contieneTagHtml($nome)){
        $messaggiErrore['[errore-nome]'][] = "Non si possono inserire tag HTML all'interno dei campi.";
        $numMsgErrore++;
    }

    if (!Tool::validaNome($cognome)) {
        $messaggiErrore['[errore-cognome]'][] = "Il cognome deve contenere solo lettere e deve avere almeno 2 caratteri.";
        $numMsgErrore++;
    }
    if(Tool::contieneTagHtml($cognome)){
        $messaggiErrore['[errore-cognome]'][] = "Non si possono inserire tag HTML all'interno dei campi.";
        $numMsgErrore++;
    }

    if (!Tool::validaCitta($citta)) {
        $messaggiErrore['[errore-citta]'][] = "La città inserita non è valida, seleziona una città dall’elenco.";
        $numMsgErrore++;
    }
    if(Tool::contieneTagHtml($citta)){
        $messaggiErrore['[errore-citta]'][] = "Non si possono inserire tag HTML all'interno dei campi.";
        $numMsgErrore++;
    }

    if (!Tool::validaEmail($email)) {
        $messaggiErrore['[errore-email]'][] = "L'email inserita non è valida.";
        $numMsgErrore++;
    }
    if(Tool::contieneTagHtml($email)){
        $messaggiErrore['[errore-email]'][] = "Non si possono inserire tag HTML all'interno dei campi.";
        $numMsgErrore++;
    }

    if (!Tool::validaPassword($password)) {
        $messaggiErrore['[errore-password]'][] = "La password deve avere almeno 8 caratteri, con almeno:
                    1 maiuscola, 1 minuscola, 1 numero e 1 simbolo.";
        $numMsgErrore++;
    }

    if ($password !== $conferma_password) {
        $messaggiErrore['[errore-conferma-password]'][] = "Le due password non coincidono.";
        $numMsgErrore++;
    }

    //controllo se esiste già un utente con questa email
    $db->openDBConnection();
    $result = $db->getIdUtente($email);
    $db->closeConnection();
    if($result)
    {
        $messaggiErrore['[errore-email]'][] = "Questa email è già utilizzata da un'altro utente.";
        $numMsgErrore++;
    }

    /* -----------------------------------
     * RISULTATO
     * ----------------------------------- */
    if ($numMsgErrore==0) 
    {
        if($db->openDBConnection()) {

            //ottengo l'IdCitta della città selezionata
            $idCitta = $db->getIdCitta($citta);

            //inserisco l'utente
            $arrayRegistrazione = [];
            $arrayRegistrazione['Nome'] = $nome;
            $arrayRegistrazione['Cognome'] = $cognome;
            $arrayRegistrazione['Email'] = $email;
            $arrayRegistrazione['Password'] = password_hash($password, PASSWORD_DEFAULT);
            $arrayRegistrazione['IdCitta'] = $idCitta;
            
            $db->insertUtente($arrayRegistrazione);

            # $idutente = intval($db->getIdUtente($email));//perché l'id viene aggiunto automaticamente da database
            $idutente = $db->verifyUserCredential($email, $password);

            $db->closeConnection();

            Tool::startUserSession($idutente);

            header("location: index.php");
        } else {
            Tool::renderError(500);
        }
    }
    else
    {
        //per ogni tipologia campo inserisco gli errori se questi sono presenti
        foreach ($messaggiErrore as $placeHolder => $arrayErrori) 
        {
            if(empty($arrayErrori))//se non ci sono errori per quel field
            {
                $htmlPage = str_replace($placeHolder, "", $htmlPage);
            }
            else
            {
                $msgErrore = "<ul class='riquadro-spieg messaggi-errore-form'>";
                foreach ($arrayErrori as $err) {
                    $msgErrore .= "<li class='msgErrore' role='alert'>$err</li>";
                }
                $msgErrore .= "</ul>";
                $htmlPage = str_replace($placeHolder, $msgErrore, $htmlPage);
            }
        }
    }
}
else {
    foreach ($messaggiErrore as $placeHolder => $arrayErrori) 
        $htmlPage = str_replace($placeHolder, "", $htmlPage);
}

$htmlPage = str_replace("[CityOptionsList]", Tool::renderCityOptions($cities), $htmlPage);

$htmlPage = str_replace("[nome]", $nome, $htmlPage);
$htmlPage = str_replace("[cognome]", $cognome, $htmlPage);
$htmlPage = str_replace("[citta]", $citta, $htmlPage);
$htmlPage = str_replace("[email]", $email, $htmlPage);

$htmlPage = str_replace("[TopNavBar]", Tool::buildTopNavBar("registrati"), $htmlPage);
$htmlPage = str_replace("[BottomNavBar]", Tool::buildBottomNavBar("registrati"), $htmlPage);

echo $htmlPage;

?>
