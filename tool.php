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

    //questa è per quei campi che vanno controllati ma per cui non è previsto 
    //un messaggio di errore, es. esplora
    public static function pulisciInputCompleto($value) {
        $value = trim($value);
        $value = strip_tags($value);
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

        $testo = preg_replace('/[\x{00A0}\x{FEFF}\x{200B}\x{200C}\x{200D}]/u', '', $testo);

        $paragrafi = preg_split('/\r\n|\r|\n/', $testo);

        $html = "";
        foreach ($paragrafi as $p) {
            $p = trim($p);
            $html .= "<p>$p</p>";
        }

        $html = preg_replace('/<p>\s*<\/p>/', '', $html);

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

    /* -------------------------------
    * FUNZIONE CAMPI MODIFICA ANNUNCIO
    * ------------------------------- */
    public static function getModificaAnnuncioSpecifico(string $categoria) : string {
        $modAnn = '<div id="attr-specifici-container">';
        switch($categoria) {
            case 'Affitti':
                $modAnn = $modAnn . '<div id="campi-affitti" class="">
                            <fieldset  class="campi-specifici fieldset-base">
                                <legend>Dettagli Affitti</legend>
                                <div class="form-div">
                                    <label for="coinquilini">Numero coinquilini</label>
                                    <input type="number" id="coinquilini" name="coinquilini" value="[coinquilini]" min="0">
                                    <p>Es. 2</p>
                                </div>
                                <div class="form-div">
                                    <label for="costo-mese-affitto">Costo mensile (€ al mese)</label>
                                    <input type="number" id="costo-mese-affitto" value="[costo-mese-affitto]" name="costo-mese-affitto" min="0">
                                    <p>Es. 300</p>
                                </div>
                                <div class="form-div">
                                    <label for="indirizzo-affitto">Indirizzo</label>
                                    <input type="text" id="indirizzo-affitto" value="[indirizzo-affitto]" name="indirizzo-affitto">
                                    <p>Es. Via Roma 10</p>
                                </div>
                            </fieldset>
                        </div>';
                break;
            case 'Esperimenti':
                $modAnn = $modAnn . '<div id="campi-esperimenti" class="">
                            <fieldset class="campi-specifici fieldset-base">
                                <legend>Dettagli Esperimenti</legend>
                                <div class="form-div">
                                    <label for="laboratorio">Laboratorio di riferimento</label>
                                    <input type="text" id="laboratorio" value="[laboratorio]" name="laboratorio">
                                    <p>Es. Laboratorio di Psicologia Sperimentale</p>
                                </div>
                                <div class="form-div">
                                    <label for="esperimento-durata">Durata esperimento (minuti)</label>
                                    <input type="text" id="esperimento-durata" value="[esperimento-durata]" name="esperimento-durata" placeholder="Es. 75">
                                    <p>Es. 75</p>
                                </div>
                                <div class="form-div">
                                    <label for="esperimento-compenso">Compenso (€)</label>
                                    <input type="number" id="esperimento-compenso" value="[esperimento-compenso]" name="esperimento-compenso" min="0">
                                    <p>Es. 15</p>
                                </div>
                            </fieldset>
                        </div>';
                break;
            case 'Eventi':
                $modAnn = $modAnn . '<div id="campi-eventi" class="">
                            <fieldset class="campi-specifici fieldset-base">
                                <legend>Dettagli Eventi</legend>
                                <div class="form-div">
                                    <label for="data-evento">Data evento</label>
                                    <input type="date" id="data-evento" name="data-evento" value="[data-evento]">
                                    <p>Es. 18/10/2025</p>
                                </div>
                                <div class="form-div">
                                    <label for="costo-evento">Costo entrata (€)</label>
                                    <input type="number" id="costo-evento" name="costo-evento" value="[costo-evento]" min="0">
                                    <p>Es. 10</p>
                                </div>
                                <div class="form-div">
                                    <label for="luogo-evento">Luogo</label>
                                    <input type="text" id="luogo-evento" name="luogo-evento" value="[luogo-evento]">
                                    <p>Es. Teatro Comunale</p>
                                </div>
                            </fieldset>
                        </div>';
                break;
            case 'Ripetizioni':
                $modAnn = $modAnn . '<div id="campi-ripetizioni" class="">
                            <fieldset class="campi-specifici fieldset-base">
                                <legend>Dettagli Ripetizioni</legend>
                                <div class="form-div">
                                    <label for="materia">Materia</label>
                                    <input type="text" id="materia" name="materia" value="[materia]">
                                    <p>Es. Matematica</p>
                                </div>
                                <div class="form-div">
                                    <label for="livello">Livello</label>
                                    <input list="listlivelli" placeholder="Es. Superiori, università" value="[livello]" name="livello" id="livello">
                                    <datalist id="listlivelli">
                                        <option value="Medie">
                                        <option value="Superiori">
                                        <option value="Università">
                                    </datalist>
                                </div>
                                <div class="form-div">
                                    <label for="prezzo-ripetizioni">Prezzo orario (€ all\'ora)</label>
                                    <input type="number" id="prezzo-ripetizioni" name="prezzo-ripetizioni" min="0" value="[prezzo-ripetizioni]">
                                    <p>Es. 15</p>
                                </div>
                            </fieldset>
                        </div>';
                break;
            default:
                break;
        }

        return $modAnn . '</div>';
    }
}
