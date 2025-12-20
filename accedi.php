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
$returnValue = "";

$email = isset($_POST['email']) ? Tool::pulisciInput($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if(isset($_POST['submit'])) {
    $db = new DB\DBAccess();
    if ($db->openDBConnection()) {
        $returnValue = $db->verifyUserCredential($email, $password);
        if ($returnValue === 'utenteFalse') {
            $erroreEmail = "<p class='error-message'>Non esiste un account con questa email.</p>";
        } elseif ($returnValue === 'passwordFalse') {
            $errorePassword = "<p class='error-message'>Password errata.</p>";
        } else {
            $idUtente = $returnValue;
            Tool::startUserSession($idUtente);
            header("Location: index.php");
            exit;
        }
        $db->closeConnection();
    } else {
        $errorMessage = "<p class='error-message'>Errore di connessione al database.</p>";
    }
}

$htmlPage = str_replace("[EmailValue]", $email, $htmlPage);
$htmlPage = str_replace("[ErrorMessage]", $errorMessage, $htmlPage);
$htmlPage = str_replace("[ErroreMail]", $erroreEmail, $htmlPage);
$htmlPage = str_replace("[ErrorePassword]", $errorePassword, $htmlPage);

echo $htmlPage;

?>