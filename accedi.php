<?php

require_once "dbConnect.php";
require_once "tool.php";

if (Tool::isLoggedIn()) {
    if (isset($_GET['redirect'])) {
        header("Location: " . $_GET['redirect']);
    } else {
        header("Location: index.php");
    }
    exit;
}

$htmlPage = file_get_contents("pages/accedi.html");
$redirectMessage = "";
$errorMessage = "";
$idUtente = "";
$erroreEmail = "";
$errorePassword = "";
$genericError = "";

$email = isset($_POST['email']) ? Tool::pulisciInput($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if(isset($_POST['submit'])) {
    if(!Tool::contieneTagHtml($email))
    {
        if(Tool::validaEmail($email) && Tool::validaPassword($password)) {
            $db = new DB\DBAccess();
            if ($db->openDBConnection()) {
                $idUtente = $db->verifyUserCredential($email, $password);
                if ($idUtente !== false) {
                    Tool::startUserSession($idUtente);

                    $redirect = $_POST['redirect'] ?? $_GET['redirect'] ?? "";
                    if ($redirect !== "") {
                        header("Location: " . $redirect);
                        exit;
                    }

                    header("Location: index.php");
                    exit;
                } else {
                    $errorMessage = "<ul class='riquadro-spieg messaggi-errore-form'><li>Email o password non validi.</li></ul>";
                }
                $db->closeConnection();
            } else {
                Tool::renderError(500);
            }
        } else {
            $errorMessage = "<p class='riquadro-spieg messaggi-errori-form'>Errore di connessione al database.</p>";
        }
    }
    else
    { 
        $errorMessage = "<ul class='riquadro-spieg messaggi-errore-form'><li>Non si possono inserire tag HTML all'interno dei campi.</li></ul>";
    }
        
}

if (isset($_GET['redirect']) && $_GET['redirect'] !== "") {
    $redirectMessage = '
        <p class="riquadro-spieg alt-spiegazione">
            Per accedere alla pagina selezionata è necessario effettuare il <span lang="en">login</span>.
        </p>
    ';
}

$htmlPage = str_replace("[RedirectValue]", $_GET['redirect'] ?? "", $htmlPage);
$htmlPage = str_replace("[RedirectMessage]", $redirectMessage, $htmlPage);
$htmlPage = str_replace("[EmailValue]", $email, $htmlPage);
$htmlPage = str_replace("[ErrorMessage]", $errorMessage, $htmlPage);
$htmlPage = str_replace("[ErroreMail]", $erroreEmail, $htmlPage);
$htmlPage = str_replace("[ErrorePassword]", $errorePassword, $htmlPage);

echo $htmlPage;

?>
