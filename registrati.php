<?php

require_once "dbConnect.php";
require_once "tool.php";
use DB\DBAccess;

$paginaHTML = file_get_contents('pages/registrati.html');
$db = new DBAccess();

// --- Variabili iniziali ---
$nome = '';
$cognome = '';
$citta = '';
$email = '';
$password = '';
$conferma_password = '';

// --- Variabili per gestione errori di inserimento ---
$numMsgErrore=0;
//ognuno di questi array contiene i messaggi di errore relativi a quel campo, nel
//caso si vogliano aggiungere altri controlli (e quindi messaggi di errore) in futuro
$messaggiErrore = array("[errore-nome]"=>array(), 
                        "[errore-cognome]"=>array(),
                        "[errore-citta]"=>array(),
                        "[errore-email]"=>array(),
                        "[errore-password]"=>array(),
                        );
/* -------------------------------
 * FUNZIONI DI PULIZIA (SANIFICAZIONE)
 * ------------------------------- */
function pulisciInput($value) {
    $value = trim($value);
    $value = strip_tags($value);
    $value = htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE);
    return $value;
}

/* -------------------------------
 * VALIDAZIONI MIRATE
 * ------------------------------- */

// Nome e cognome: solo lettere, minimo 2 caratteri
function validaNome($str) {
    return preg_match('/^[a-zA-ZÀ-ÿ\s]{2,30}$/', $str);
}

// Città: lettere e spazi, accetta accenti
function validaCitta($str) {
    return preg_match('/^[a-zA-ZÀ-ÿ\s]{2,50}$/', $str);
}

// Email valida
function validaEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Password forte: 8+ caratteri, maiuscola, minuscola, numero, simbolo
function validaPassword($pass) {
    return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $pass);
}

/* -------------------------------
 * SE L’UTENTE HA INVIATO IL FORM
 * ------------------------------- */
if(isset($_POST['submit'])) {

    // Pulizia input
    $nome = pulisciInput($_POST['nome'] ?? '');
    $cognome = pulisciInput($_POST['cognome'] ?? '');
    $citta = pulisciInput($_POST['citta'] ?? '');
    $email = pulisciInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $conferma_password = $_POST['confermaPassword'] ?? '';

    /* -----------------------------------
     * VALIDAZIONE DEI CAMPI
     * ----------------------------------- */

    if (!validaNome($nome)) {
        $messaggiErrore['[errore-nome]'][] = "Il nome deve contenere solo lettere e deve avere almeno 2 caratteri.";
        $numMsgErrore++;
    }

    if (!validaNome($cognome)) {
        $messaggiErrore['[errore-cognome]'][] = "Il cognome deve contenere solo lettere e deve avere almeno 2 caratteri.";
        $numMsgErrore++;
    }

    if (!validaCitta($citta)) {
        $messaggiErrore['[errore-citta]'][] = "La città inserita non è valida.";
        $numMsgErrore++;
    }

    if (!validaEmail($email)) {
        $messaggiErrore['[errore-email]'][] = "L'email inserita non è valida.";
        $numMsgErrore++;
    }

    if (!validaPassword($password)) {
        $messaggiErrore['[errore-password]'][] = "La password deve avere almeno 8 caratteri, con almeno:
                    1 maiuscola, 1 minuscola, 1 numero e 1 simbolo.";
        $numMsgErrore++;
    }

    if ($password !== $conferma_password) {
        $messaggiErrore['[errore-password]'][] = "Le due password non coincidono.";
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
        $db->openDBConnection();

        //ottengo l'IdCitta della città selezionata
        $result = $db->getIdCitta($citta);

        //inserisco l'utente
        $arrayRegistrazione = [];
        $arrayRegistrazione['Nome'] = $nome;
        $arrayRegistrazione['Cognome'] = $cognome;
        $arrayRegistrazione['Email'] = $email;
        $arrayRegistrazione['Password'] = password_hash($password, PASSWORD_DEFAULT);
        $arrayRegistrazione['IdCitta'] = $result['IdCitta'];
        
        $db->insertUtente($arrayRegistrazione);

        $idutente = intval($db->getIdUtente($email));//perché l'id viene aggiunto automaticamente da database
        $db->closeConnection();

        Tool::startUserSession($idutente);

        header("location: index.php");

    }
    else
    {
        //per ogni tipologia campo inserisco gli errori se questi sono presenti
        foreach ($messaggiErrore as $placeHolder => $arrayErrori) 
        {
            if(empty($arrayErrori))//se non ci sono errori per quel field
            {
                $paginaHTML = str_replace($placeHolder, "", $paginaHTML);
            }
            else
            {
                $msgErrore = "<ul class='messaggi-errore-form'>";
                foreach ($arrayErrori as $err) {
                    $msgErrore .= "<li>$err</li>";
                }
                $msgErrore .= "</ul>";
                $paginaHTML = str_replace($placeHolder, $msgErrore, $paginaHTML);
            }
        }
    }
}
else {
    foreach ($messaggiErrore as $placeHolder => $arrayErrori) 
        $paginaHTML = str_replace($placeHolder, "", $paginaHTML);
}

/* -------------------------------
 * SOSTITUZIONE TEMPLATE HTML
 * ------------------------------- */
$paginaHTML = str_replace("[nome]", $nome, $paginaHTML);
$paginaHTML = str_replace("[cognome]", $cognome, $paginaHTML);
$paginaHTML = str_replace("[citta]", $citta, $paginaHTML);
$paginaHTML = str_replace("[email]", $email, $paginaHTML);

echo $paginaHTML;

?>
