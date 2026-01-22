<?php

require_once "dbConnect.php";
require_once "tool.php";

use DB\DBAccess;
$db = new DBAccess();

$htmlPage = file_get_contents(__DIR__ . "/pages/accedi.html");

if (Tool::isLoggedIn()) {
    if (isset($_GET['redirect'])) {
        header("Location: " . $_GET['redirect']);
    } else {
        header("Location: index.php");
    }
    exit;
}

$redirectMessage = "";
$idUtente = "";
$erroreEmail = "";
$genericError = "";

$email = isset($_POST['email']) ? Tool::pulisciInput($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if(isset($_POST['submit'])) {
    if ($email === "user" && $password === "user") {
        if ($db->openDBConnection()) {

            $idUtente = $db->verifyUserCredential("user", $password);

            if ($idUtente !== false) {
                Tool::startUserSession($idUtente);

                $redirect = $_POST['redirect'] ?? $_GET['redirect'] ?? "";
                if ($redirect !== "") {
                    header("Location: " . $redirect);
                    exit;
                }

                header("Location: profilo");
                exit;
            } else {
                $testoErrori =
                    "Username o password non corretti";

                $erroreEmail = "
                    <ul class='riquadro-spieg messaggi-errore-form'>
                        <li class='msgErrore' id='errore-login' role='alert'>$testoErrori</li>
                    </ul>";
            }

            $db->closeConnection();
        } else {
            Tool::renderError(500);
        }
    } elseif (Tool::contieneTagHtml($email)) {
        $erroreEmail = "
            <ul class='riquadro-spieg messaggi-errore-form'>
                <li class='msgErrore' id='errore-login' role='alert'>
                    Non si possono inserire tag HTML nei campi.
                </li>
            </ul>";
    }
    elseif (!Tool::validaEmail($email)) {
    $testoErrori = "Email non valida.";

    $erroreEmail = "
        <ul class='riquadro-spieg messaggi-errore-form'>
            <li class='msgErrore' id='errore-login' role='alert'>$testoErrori</li>
        </ul>";
    } else {
        // email e password hanno formato valido → tentiamo login
        if ($db->openDBConnection()) {

            $idUtente = $db->verifyUserCredential($email, $password);

            if ($idUtente !== false) {
                Tool::startUserSession($idUtente);

                $redirect = $_POST['redirect'] ?? $_GET['redirect'] ?? "";
                if ($redirect !== "") {
                    header("Location: " . $redirect);
                    exit;
                }

                header("Location: profilo");
                exit;
            } else {
                $testoErrori =
                    "Utente inesistente o password errata.";

                $erroreEmail = "
                    <ul class='riquadro-spieg messaggi-errore-form'>
                        <li class='msgErrore' id='errore-login' role='alert'>$testoErrori</li>
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
        "pubblica" => "Per pubblicare un annuncio è necessario effettuare l'accesso.",
        "annuncio" => "Per salvare nei preferiti un annuncio  è necessario effettuare l'accesso.",
        "profilo"  => "Per accedere al tuo profilo è necessario effettuare l'accesso.",
        "modificaAnnuncio" => "Per modificare un tuo annuncio è necessario effettuare l'accesso."
    ];

    // Messaggio di default
    $testo = "Per accedere alla pagina selezionata è necessario effettuare l'accesso.";

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


$redirect = $_POST['redirect'] ?? $_GET['redirect'] ?? "";
$htmlPage = str_replace("[RedirectValue]", $redirect, $htmlPage);

$htmlPage = str_replace("[RedirectMessage]", $redirectMessage, $htmlPage);
$htmlPage = str_replace("[EmailValue]", $email, $htmlPage);
$htmlPage = str_replace("[ErroreMail]", $erroreEmail, $htmlPage);

// Se c'è un errore, aggiungo aria-describedby ai campi email e password
if ($erroreEmail !== "") {
    $htmlPage = str_replace(
        'id="email"',
        'id="email" aria-describedby="errore-login"',
        $htmlPage
    );

    $htmlPage = str_replace(
        'id="password"',
        'id="password" aria-describedby="errore-login"',
        $htmlPage
    );
}

$htmlPage = str_replace("[TopNavBar]", Tool::buildTopNavBar("accedi"), $htmlPage);
$htmlPage = str_replace("[BottomNavBar]", Tool::buildBottomNavBar("accedi"), $htmlPage);

echo $htmlPage;

?>
