<?php

require_once "dbConnect.php";
require_once "tool.php";
use DB\DBAccess;

$paginaHTML = file_get_contents('pages/registrati.html');
$messaggiPerForm = "";
$db = new DBAccess();

// --- Variabili iniziali ---
$nome = '';
$cognome = '';
$citta = '';
$email = '';
$password = '';
$conferma_password = '';

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

    $errori = [];

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
        $errori[] = "Il nome deve contenere solo lettere e deve avere almeno 2 caratteri.";
    }

    if (!validaNome($cognome)) {
        $errori[] = "Il cognome deve contenere solo lettere e deve avere almeno 2 caratteri.";
    }

    if (!validaCitta($citta)) {
        $errori[] = "La città inserita non è valida.";
    }

    if (!validaEmail($email)) {
        $errori[] = "L'email inserita non è valida.";
    }

    if (!validaPassword($password)) {
        $errori[] = "La password deve avere almeno 8 caratteri, con almeno:
                    1 maiuscola, 1 minuscola, 1 numero e 1 simbolo.";
    }

    if ($password !== $conferma_password) {
        $errori[] = "Le due password non coincidono.";
    }
    //controllo se esiste già un utente con questa email
    $db->openDBConnection();
    $result = $db->getIdUtente($email);
    $db->closeConnection();
    if($result)
    {
        $errori[] = "Questa email è già utilizzata da un'altro utente.";
    }
    
    /* -----------------------------------
     * RISULTATO
     * ----------------------------------- */
    if (empty($errori)) 
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
        // Mostra errori
        $messaggiPerForm = "<ul class='messaggi-errore-form'>";
        foreach ($errori as $e) {
            $messaggiPerForm .= "<li>$e</li>";
        }
        $messaggiPerForm .= "</ul>";
    }
}

/* -------------------------------
 * SOSTITUZIONE TEMPLATE HTML
 * ------------------------------- */
$paginaHTML = str_replace("[errori-form-registrati]", $messaggiPerForm, $paginaHTML);
$paginaHTML = str_replace("[nome]", $nome, $paginaHTML);
$paginaHTML = str_replace("[cognome]", $cognome, $paginaHTML);
$paginaHTML = str_replace("[citta]", $citta, $paginaHTML);
$paginaHTML = str_replace("[email]", $email, $paginaHTML);

echo $paginaHTML;

?>
