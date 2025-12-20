<?php

class Tool {

    /* -------------------------------
    * FUNZIONE PER LISTA CITTÀ DA DATI DB
    * ------------------------------- */
    public static function renderCityOptions(array $cities): string {
        $html = "";

        foreach ($cities as $city) {
            $safeCity = htmlspecialchars($city, ENT_QUOTES, 'UTF-8');
            $html .= "<option value=\"$safeCity\"></option>";
        }

        return $html;
    }

    /* -------------------------------
    * FUNZIONE PER CREARE LE CARDS DA DATI DB
    * ------------------------------- */
    public static function createCard($cardsData): string {
        $cards = "";
        $cardhtml = "";
        foreach ($cardsData as $card) {
            $cardhtml = file_get_contents("pages/cardTemplate.html");
            $cardhtml = str_replace("[ImmagineAnnuncio]", htmlspecialchars($card['Percorso']), $cardhtml);
            $cardhtml = str_replace("[AltImmagineAnnuncio]", htmlspecialchars($card['AltText']), $cardhtml);
            $cardhtml = str_replace("[TitoloAnnuncio]", htmlspecialchars($card['Titolo']), $cardhtml);
            
            $dataDB = $card['DataPubblicazione'];
            $data = new DateTime($dataDB);
            $dataISO = $data->format('Y-m-d'); # formato ISO per l'attributo datetime
            $dataIT = $data->format('d/m/Y'); # formato italiano per la visualizzazione
            $cardhtml = str_replace("[dataInsertUs]", htmlspecialchars($dataISO), $cardhtml);
            $cardhtml = str_replace("[DataInsert]", htmlspecialchars($dataIT), $cardhtml);

            $cardhtml = str_replace("[CittaAnnuncio]", htmlspecialchars($card['nomeCitta']), $cardhtml);
            $cardhtml = str_replace("[CategoriaAnnuncioMinuscolo]", htmlspecialchars(strtolower($card['Categoria'])), $cardhtml);
            $cardhtml = str_replace("[CategoriaAnnuncio]", htmlspecialchars($card['Categoria']), $cardhtml);
            $cardhtml = str_replace("[idAnnuncio]", htmlspecialchars($card['IdAnnuncio']), $cardhtml);
            
            $cards .= $cardhtml;
        }
        return $cards;
    }

    public static function getTopNavLog() {
        if(Tool::isLoggedIn()) return file_get_contents("pages/topNavLogTrue.html");
        return file_get_contents("pages/topNavLogFalse.html");
    }
    public static function getBottomNavLog() {
        if(Tool::isLoggedIn()) return file_get_contents("pages/bottomNavLogTrue.html");
        return file_get_contents("pages/bottomNavLogFalse.html");
    }

    /* -------------------------------
    * FUNZIONI DI PULIZIA (SANIFICAZIONE)
    * ------------------------------- */
    public static function pulisciInput($value) {
        $value = trim($value);
        $value = strip_tags($value);
        $value = htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE);
        return $value;
    }

    /* -------------------------------
    * VALIDAZIONI MIRATE
    * ------------------------------- */
    // Nome e cognome: solo lettere, minimo 2 caratteri
    public static function validaNome($str) {
        return preg_match('/^[a-zA-ZÀ-ÿ\s]{2,30}$/', $str);
    }

    // Città: lettere e spazi, accetta accenti
    public static function validaCitta($str) {
        return preg_match('/^[a-zA-ZÀ-ÿ\s]{2,50}$/', $str);
    }

    // Email valida
    public static function validaEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    // Password forte: 8+ caratteri, maiuscola, minuscola, numero, simbolo
    public static function validaPassword($pass) {
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $pass);
    }

    /* -------------------------------
    * FUNZIONI DI GESTIONE DELLE SESSIONI
    * ------------------------------- */
    public static function startUserSession(int $userId): void {
        if (session_status() === PHP_SESSION_NONE)  session_start();

        session_regenerate_id(true);

        $_SESSION['logged']  = true;
        $_SESSION['user_id'] = $userId;
    }

    public static function endUserSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
    }

    public static function isLoggedIn(): bool {
        if (session_status() === PHP_SESSION_NONE) session_start();

        return isset($_SESSION['logged'], $_SESSION['user_id'])
            && $_SESSION['logged'] === true;
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
