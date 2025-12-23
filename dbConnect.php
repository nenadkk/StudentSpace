<?php

namespace DB;

class DBAccess {

    private const HOST_DB = "db";           // docker = db,         server_tecweb = localhost
    private const DATABASE_NAME = "testdb"; // docker = testdb,     server_tecweb = nome_utente
    private const USERNAME = "user";        // docker = user,       server_tecweb = nome_utente
    private const PASSWORD = "password";    // docker = password,   server_tecweb = password_server
    private $connection;

    public function openDBConnection() {
        $this->connection = mysqli_connect(DBAccess::HOST_DB, DBAccess::USERNAME, DBAccess::PASSWORD, DBAccess::DATABASE_NAME);

        if(!$this->connection) {
            throw new Exception("Connessione al database fallita: " . mysqli_connect_error());
        }
        return true;
    }

    public function closeConnection() {
        if($this->connection)
            mysqli_close($this->connection);
    }

    public function getAllCity() {
        $query = "SELECT nomeCitta FROM Citta ORDER BY nomeCitta ASC";
        $queryResult = mysqli_query($this->connection, $query) or die ("Query fallita: " . mysqli_error($this->connection));

        if(mysqli_num_rows($queryResult) == 0) {
            return false;
        } else {
            $cities = array();
            while($row = mysqli_fetch_assoc($queryResult)) {
                $cities[] = $row['nomeCitta'];
            }
            $queryResult->free();
            return $cities;
        }
    }

    public function getLastAnnouncements() {
        $query = "SELECT a.IdAnnuncio, a.Titolo, a.DataPubblicazione, a.Categoria, c.nomeCitta, i.Percorso, i.AltText
            FROM Annuncio as a JOIN Citta as c ON a.IdCitta = c.IdCitta LEFT JOIN ImmaginiAnnuncio as i ON a.IdAnnuncio = i.IdAnnuncio
            WHERE i.Ordine = 1
            ORDER BY a.DataPubblicazione DESC
            LIMIT 5;";
        $queryResult = mysqli_query($this->connection, $query) or die ("Query fallita: " . mysqli_error($this->connection));

        if(mysqli_num_rows($queryResult) == 0) {
            return false;
        } else {
            $results = array();
            while($row = mysqli_fetch_assoc($queryResult)) {
                array_push($results, $row);
            }
            $queryResult->free();
        }

        return $results;
    }

    public function getAnnouncements() {
        $query = "SELECT a.IdAnnuncio, a.Titolo, a.DataPubblicazione, a.Categoria, c.nomeCitta, i.Percorso, i.AltText
            FROM Annuncio as a JOIN Citta as c ON a.IdCitta = c.IdCitta LEFT JOIN ImmaginiAnnuncio as i ON a.IdAnnuncio = i.IdAnnuncio
            WHERE i.Ordine = 1
            ORDER BY a.DataPubblicazione DESC;";

        $queryResult = mysqli_query($this->connection, $query) or die ("Query fallita: " . mysqli_error($this->connection));

        if(mysqli_num_rows($queryResult) == 0) {
            return false;
        } else {
            $results = array();
            
            while($row = mysqli_fetch_assoc($queryResult)) {
                array_push($results, $row);
            }
            $queryResult->free();
        }

        return $results;
    }

    public function getIdCitta($nomeCitta) 
    {
        $query = "SELECT IdCitta FROM Citta WHERE NomeCitta ='$nomeCitta';";
        $queryResult = mysqli_query($this->connection, $query) or die ("Query fallita: " . mysqli_error($this->connection));

        if(mysqli_num_rows($queryResult) != 1) //ad ogni nomeCittà corrisponde un solo id
            return false;
        else 
        {
            $row = mysqli_fetch_assoc($queryResult);
            $queryResult->free();
            return (int) $row['IdCitta'];
        }
    }

    public function getIdUtente($emailUtente) 
    {
        $query = "SELECT IdUtente FROM Utente WHERE Email ='$emailUtente';";
        $queryResult = mysqli_query($this->connection, $query) or die ("Query fallita: " . mysqli_error($this->connection));

        if(mysqli_num_rows($queryResult) != 1) //ad ogni email corrisponde un solo utente
            return false;
        else 
        {
            $row = mysqli_fetch_assoc($queryResult);
            $queryResult->free();
            return $row;
        }
    }

    public function insertUtente($arrayRegistrazione) 
    {
        $query = "INSERT INTO Utente (Nome, Cognome, Email, Password, IdCitta) VALUES('"
                .$arrayRegistrazione['Nome']."','"
                .$arrayRegistrazione['Cognome']."','"
                .$arrayRegistrazione['Email']."','"
                .$arrayRegistrazione['Password']."','"
                .$arrayRegistrazione['IdCitta']."');";

        $queryResult = mysqli_query($this->connection, $query) or die ("Query fallita: " . mysqli_error($this->connection));
        if(!$queryResult) //mysqli_query in questo caso restituisce un true se è andato tutto bene 
            return false;
        
        return true;
    }

    public function verifyUserCredential($email, $password) {
        $stmt = $this->connection->prepare(
            "SELECT IdUtente, Password FROM Utente WHERE Email = ?"
        );

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if (!$user) {
            return false;
        }

        if (!password_verify($password, $user['Password'])) {
            return false;
        }

        return (int) $user['IdUtente'];
    }

    public function getUtente($idUtente) {
        $query = "SELECT * FROM Utente as u JOIN Citta as c ON u.IdCitta = c.IdCitta WHERE IdUtente = $idUtente";

        $queryResult = mysqli_query($this->connection, $query) or die ("Query fallita: " . mysqli_error($this->connection));

        $result = "";

        if(mysqli_num_rows($queryResult) == 0) {
            return false;
        } else {
            $result = mysqli_fetch_assoc($queryResult);
            $queryResult->free();
        }

        return $result;
    }

    public function getAnnunciUtente($idUtente) {
        $query = "SELECT a.IdAnnuncio, a.Titolo, a.DataPubblicazione, a.Categoria, c.nomeCitta, i.Percorso, i.AltText
            FROM Annuncio as a JOIN Citta as c ON a.IdCitta = c.IdCitta LEFT JOIN ImmaginiAnnuncio as i ON a.IdAnnuncio = i.IdAnnuncio
            WHERE i.Ordine = 1 AND a.IdUtente = $idUtente
            ORDER BY a.DataPubblicazione DESC;";

        $queryResult = mysqli_query($this->connection, $query) or die ("Query fallita: " . mysqli_error($this->connection));

        if(mysqli_num_rows($queryResult) == 0) {
            return false;
        } else {
            $results = array();
            
            while($row = mysqli_fetch_assoc($queryResult)) {
                array_push($results, $row);
            }
            $queryResult->free();
        }

        return $results;
    }

    // PRECONDIZIONE - $categoria={Affitti, Esperimenti, Eventi, Ripetizioni}
    public function inserimentoAnnuncio(string $titolo, string $descrizione, string $categoria, int $idUtente, int $idCitta, $campo1, $campo2, $campo3) : int|false {

        mysqli_begin_transaction($this->connection);

        try {
            $stmtAnnuncio = $this->connection->prepare("INSERT INTO Annuncio (Titolo, Descrizione, Categoria, IdUtente, IdCitta) VALUES (?, ?, ?, ?, ?)");

            if(!$stmtAnnuncio) {
                throw new Exception("Prepare annuncio fallita");
            }

            $stmtAnnuncio->bind_param('sssii', $titolo, $descrizione, $categoria, $idUtente, $idCitta);

            if(!$stmtAnnuncio->execute()) {
                throw new Exception("Execute Annuncio Fallita");
            }

            $idAnnuncio = $stmtAnnuncio->insert_id;
            $stmtAnnuncio->close();
            $stmt = "";

            switch ($categoria) {
                case 'Affitti':
                    $stmt = $this->connection->prepare(
                        "INSERT INTO AnnuncioAffitti 
                        (IdAnnuncio, PrezzoMensile, Indirizzo, NumeroInquilini)
                        VALUES (?, ?, ?, ?)"
                    );
                    $stmt->bind_param("idsi", $idAnnuncio, $campo1, $campo2, $campo3);
                    break;

                case 'Esperimenti':
                    $stmt = $this->connection->prepare(
                        "INSERT INTO AnnuncioEsperimenti
                        (IdAnnuncio, Laboratorio, DurataPrevista, Compenso)
                        VALUES (?, ?, ?, ?)"
                    );
                    $stmt->bind_param("isid", $idAnnuncio, $campo1, $campo2, $campo3);
                    break;

                case 'Eventi':
                    $stmt = $this->connection->prepare(
                        "INSERT INTO AnnuncioEventi
                        (IdAnnuncio, DataEvento, CostoEntrata, Luogo)
                        VALUES (?, ?, ?, ?)"
                    );
                    $stmt->bind_param("issd", $idAnnuncio, $campo1, $campo2, $campo3);
                    break;

                case 'Ripetizioni':
                    $stmt = $this->connection->prepare(
                        "INSERT INTO AnnuncioRipetizioni
                        (IdAnnuncio, Materia, Livello, PrezzoOrario)
                        VALUES (?, ?, ?, ?)"
                    );
                    $stmt->bind_param("issd", $idAnnuncio, $campo1, $campo2, $campo3);
                    break;

                default:
                    throw new Exception("Categoria non valida");
            }

            if(!$stmt->execute()) {
                throw new Exception("Execute Specifico Fallita");
            }

            $stmt->close();

            mysqli_commit($this->connection);

            return $idAnnuncio;
        } catch (Exception $e) {
            mysqli_rollback($this->connection);
            return false;
        }


    }
}

?>
