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

$email = isset($_POST['email']) ? Tool::pulisciInput($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if(isset($_POST['submit'])) {
    $db = new DB\DBAccess();
    if ($db->openDBConnection()) {
        $idUtente = $db->verifyUserCredential($email, $password);
        if ($idUtente !== false) {
            Tool::startUserSession($idUtente);
            header("Location: index.php");
            exit;
        } else {
            $errorMessage = "<p class='error-message'>Email o password non validi.</p>";
        }
        $db->closeConnection();
    } else {
        $errorMessage = "<p class='error-message'>Errore di connessione al database.</p>";
    }
}

$htmlPage = str_replace("[ErrorMessage]", $errorMessage, $htmlPage);

echo $htmlPage;

?>