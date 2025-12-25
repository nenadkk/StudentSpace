<?php

require_once "dbConnect.php";
require_once "tool.php";

if (Tool::isLoggedIn()) {
    header("Location: index.php");
    exit;
}

$htmlPage = file_get_contents("pages/accedi.html");
$errorMessage = "";
$idUtente = "";
$erroreEmail = "";
$errorePassword = "";
$genericError = "";

$email = isset($_POST['email']) ? Tool::pulisciInput($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if(isset($_POST['submit'])) {
    if(Tool::validaEmail($email) && Tool::validaPassword($password)) {
        $db = new DB\DBAccess();
        if ($db->openDBConnection()) {
            $idUtente = $db->verifyUserCredential($email, $password);
            if ($idUtente !== false) {
                Tool::startUserSession($idUtente);
                header("Location: index.php");
                exit;
            } else {
                $errorMessage = "<ul class='messaggi-errore-form'><li>Email o password non validi.</li></ul>";
            }
            $db->closeConnection();
        } else {
            $errorMessage = "<ul class='messaggi-errore-form'><li>Errore di connessione al database.</li></ul>";
        }
    } else {
        $errorMessage  = "<ul class='messaggi-errore-form'><li>Email o password non validi.</li></ul>";
    }
    
}

$htmlPage = str_replace("[EmailValue]", $email, $htmlPage);
$htmlPage = str_replace("[ErrorMessage]", $errorMessage, $htmlPage);
$htmlPage = str_replace("[ErroreMail]", $erroreEmail, $htmlPage);
$htmlPage = str_replace("[ErrorePassword]", $errorePassword, $htmlPage);

echo $htmlPage;

?>