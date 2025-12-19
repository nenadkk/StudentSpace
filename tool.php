<?php

class Tool {

    public static function renderCityOptions(array $cities): string {
        $html = "";

        foreach ($cities as $city) {
            $safeCity = htmlspecialchars($city, ENT_QUOTES, 'UTF-8');
            $html .= "<option value=\"$safeCity\"></option>";
        }

        return $html;
    }

    public static function sessionsStarter($userId): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
            $_SESSION['userId'] = $userId;
            $_SESSION['logged'] = true;
            session_regenerate_id(true);
        }
    }

    public static function sessionsDestroyer(): void {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = array();
            session_destroy();
        }
    }

    public static function isLogged(): bool {
        return (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['logged']) && $_SESSION['logged'] === true);
    }

    /*  Per Nenad
use DB\DBAccess;

$dbAccess = new DBAccess();

if (!$dbAccess->openDBConnection()) {
    echo "Connessione al database fallita.";
    exit;
}

$cities = $dbAccess->getAllCity();
$dbAccess->closeConnection();

echo "<label for=\"citta\">Città (sede universitaria)
      <span class=\"required\"> (richiesto)</span></label>";

echo "<input list=\"listaCitta\" name=\"citta\" id=\"citta\" 
       placeholder=\"Scrivi e seleziona\" required>";

echo "<datalist id=\"listaCitta\">";

if (!empty($cities)) {
    echo Tool::renderCityOptions($cities);
} else {
    echo "<option value=\"Nessuna città disponibile\"></option>";
}

echo "</datalist>";
    */

}
