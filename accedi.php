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

$htmlPage = file_get_contents(__DIR__ . "/pages/accedi.html");
$redirectMessage = "";
$errorMessage = "";
$idUtente = "";
$erroreEmail = "";
$errorePassword = "";
$genericError = "";

$email = isset($_POST['email']) ? Tool::pulisciInput($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if(isset($_POST['submit'])) {
    if (Tool::contieneTagHtml($email)) {
        $errorMessage = "
            <ul class='riquadro-spieg messaggi-errore-form'>
                <li>Non si possono inserire tag HTML nei campi.</li>
            </ul>";

    }
    elseif (!Tool::validaEmail($email) || !Tool::validaPassword($password)) {
        $errorMessage = "
            <ul class='riquadro-spieg messaggi-errore-form'>
                <li>Email o password non valide.</li>
                <li>Inserisci un indirizzo nel formato nome@dominio.it.</li>
                <li>La password deve rispettare i criteri minimi.</li>
                <li>Se non hai un account, <a class='link' href='registrati.php'>registrati</a>.</li>
            </ul>";
    }
    else {
        // email e password hanno formato valido → tentiamo login
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
                $errorMessage = "
                    <ul class='riquadro-spieg messaggi-errore-form'>
                        <li>Utente inesistente o password errata.</li>
                        <li>Se non hai un account, <a class='link' href='registrati.php'>registrati</a>.</li>
                    </ul>";
            }

            $db->closeConnection();
        } else {
            Tool::renderError(500);
        }
    }
}

if (isset($_GET['redirect']) && $_GET['redirect'] !== "") {
    $pagina = $_GET['redirect'];

    $messaggi = [
        "pubblica.php" => "Per pubblicare un annuncio è necessario effettuare il <span lang='en'>login</span>.",
        "annuncio.php" => "Per salvare nei preferiti un annuncio  è necessario effettuare il <span lang='en'>login</span>.",
        "profilo.php"  => "Per accedere al tuo profilo è necessario effettuare il <span lang='en'>login</span>.",
        "modificaAnnuncio.php" => "Per modificare un tuo annuncio è necessario effettuare il <span lang='en'>login</span>."
    ];

    // Messaggio di default
    $testo = "Per accedere alla pagina selezionata è necessario effettuare il <span lang='en'>login</span>.";

    // Se la pagina è riconosciuta, usa il messaggio specifico
    foreach ($messaggi as $chiave => $msg) {
        if (str_contains($pagina, $chiave)) {
            $testo = $msg;
            break;
        }
    }

    $redirectMessage = "
        <p class='riquadro-spieg alt-spiegazione'>
            $testo
        </p>
    ";
}


$htmlPage = str_replace("[RedirectValue]", $_GET['redirect'] ?? "", $htmlPage);
$htmlPage = str_replace("[RedirectMessage]", $redirectMessage, $htmlPage);
$htmlPage = str_replace("[EmailValue]", $email, $htmlPage);
$htmlPage = str_replace("[ErrorMessage]", $errorMessage, $htmlPage);
$htmlPage = str_replace("[ErroreMail]", $erroreEmail, $htmlPage);
$htmlPage = str_replace("[ErrorePassword]", $errorePassword, $htmlPage);

$htmlPage = str_replace("[TopNavBar]", Tool::buildTopNavBar("accedi"), $htmlPage);
$htmlPage = str_replace("[BottomNavBar]", Tool::buildBottomNavBar("accedi"), $htmlPage);

echo $htmlPage;

?>
