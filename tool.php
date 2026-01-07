<?php
require_once "dbConnect.php";
use DB\DBAccess;

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
            $cardhtml = str_replace("[AltImmagineAnnuncio]", htmlspecialchars($card['AltText'] ?? ''), $cardhtml);
            $cardhtml = str_replace("[TitoloAnnuncio]", htmlspecialchars($card['Titolo']), $cardhtml);
            
            $dataDB = $card['DataPubblicazione'];
            $data = new DateTime($dataDB);
            $dataISO = $data->format('Y-m-d'); # formato ISO per l'attributo datetime
            $dataIT = $data->format('d/m/Y'); # formato italiano per la visualizzazione
            $cardhtml = str_replace("[dataInsertUs]", htmlspecialchars($dataISO), $cardhtml);
            $cardhtml = str_replace("[DataInsert]", htmlspecialchars($dataIT), $cardhtml);
            $cardhtml = str_replace("[CittaAnnuncio]", htmlspecialchars($card['NomeCitta'] ?? ''), $cardhtml);
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
        $value = htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE);
        return $value;
    }

    public static function contieneTagHtml($str)
    {
        //html_entity_decode() serve nel caso in cui nella stringa che si sta controllando
        //i caratteri speciali siano già stati convertiti in entità HTML
        return ($str!=strip_tags(html_entity_decode($str)));    
    }
    /* -------------------------------
    * VALIDAZIONI MIRATE
    * ------------------------------- */
    // Nome e cognome: solo lettere, minimo 2 caratteri
    public static function validaNome($str) {
        return preg_match('/^[a-zA-ZÀ-ÿ\s]{2,30}$/u', $str);
    }

    // Città: lettere e spazi, accetta accenti
    public static function validaCitta($str) {
        $db = new DBAccess();
        $db->openDBConnection();
        if ($db->isACitta($str) && preg_match('/^[a-zA-ZÀ-ÿ\s]{2,50}$/', $str))
        {
            $db->closeConnection();
            return true ;
        }
        else
        {
            $db->closeConnection();
            return false;
        }
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

    /* -------------------------------
    * FUNZIONE ATTRIBUTI SPECIFICI CATEGORIA
    * ------------------------------- */

    public static function mappaAttributi(string $categoria, array $attr) : array {
        switch ($categoria) {

            case "Affitti":
                return [
                    ["Costo", $attr['PrezzoMensile'] . " € al mese"],
                    ["Indirizzo", $attr['Indirizzo']],
                    ["N° coinquilini", $attr['NumeroInquilini']]
                ];

            case "Esperimenti":
                return [
                    ["Laboratorio", $attr['Laboratorio']],
                    ["Durata prevista", $attr['DurataPrevista'] . " min"],
                    ["Compenso", $attr['Compenso'] . " €"]
                ];

            case "Eventi":
                return [
                    ["Data", $attr['DataEvento']],
                    ["Luogo", $attr['Luogo']],
                    ["Costo entrata", $attr['CostoEntrata'] . " €"]
                ];

            case "Ripetizioni":
                return [
                    ["Materia", $attr['Materia']],
                    ["Livello", $attr['Livello']],
                    ["Prezzo orario", $attr['PrezzoOrario'] . " € all'ora"]
                ];
        }
    }

    /* -------------------------------
    * FUNZIONE PARAGRAFI DESCRIZIONE
    * ------------------------------- */
    public static function convertiInParagrafi(string $testo) : string {
        $testo = htmlspecialchars($testo);
        $paragrafi = preg_split('/\r\n|\r|\n/', $testo);

        $html = "";
        foreach ($paragrafi as $p) {
            $p = trim($p);
            if ($p !== "") {
                $html .= "<p>$p</p>";
            }
        }

        return $html;
    }

    /* -------------------------------
    * FUNZIONE GESTIONE ERRORI
    * ------------------------------- */
    public static function renderError(int $code) {
        http_response_code($code);
        require __DIR__ . "/$code.php";
        exit;
    }
}
