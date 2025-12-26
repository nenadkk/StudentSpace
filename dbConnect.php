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
        $query = "SELECT NomeCitta FROM Citta ORDER BY NomeCitta ASC";
        $queryResult = mysqli_query($this->connection, $query) or die ("Query fallita: " . mysqli_error($this->connection));

        if(mysqli_num_rows($queryResult) == 0) {
            return false;
        } else {
            $cities = array();
            while($row = mysqli_fetch_assoc($queryResult)) {
                $cities[] = $row['NomeCitta'];
            }
            $queryResult->free();
            return $cities;
        }
    }

    public function getLastAnnouncements() {
        $query = "SELECT a.IdAnnuncio, a.Titolo, a.DataPubblicazione, a.Categoria, c.NomeCitta, i.Percorso, i.AltText
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
        $query = "SELECT a.IdAnnuncio, a.Titolo, a.DataPubblicazione, a.Categoria, c.NomeCitta, i.Percorso, i.AltText
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

    public function searchEsplora($categoria, $filtri) {

        $query="SELECT * ";
    
        function controlloGeneraliQuery($query,$filtri) {
        
            if ($filtri['citta'] != '') 
            {
                if (!str_contains($query,"WHERE"))
                    $query.="WHERE ";

                $query.= "NomeCitta='".$filtri['citta']."' AND ";
            }

            //Potrebbe essere necessario usare DATE_TRUNC per le prossime due
            if ($filtri['pubblicazione-inizio'] != '') 
            {
                if (!str_contains($query,"WHERE"))
                    $query.="WHERE ";

                $query.= "DataPubblicazione >='".$filtri['pubblicazione-inizio']."' AND ";
            }

            if ($filtri['pubblicazione-fine'] != '') 
            {
                if (!str_contains($query,"WHERE"))
                    $query.="WHERE ";

                $query.= "DataPubblicazione <='".$filtri['pubblicazione-fine']."' AND ";
            }
            return $query;
        }

        switch ($categoria) {
            case '':
                $query .= "FROM Annuncio a JOIN Citta c ON a.IdCitta=c.IdCitta 
                          LEFT JOIN ImmaginiAnnuncio as i ON a.IdAnnuncio = i.IdAnnuncio ";
                $query = controlloGeneraliQuery($query, $filtri); 

                break;
        

            case 'Affitti':
                foreach ($filtri as $key => $value) {
                    $filtri[$key] = isset($filtri[$key])? $filtri[$key] : '';
                }

                $query .= "FROM Annuncio a JOIN AnnuncioAffitti f ON a.IdAnnuncio= f.IdAnnuncio 
                           JOIN Citta c ON a.IdCitta=c.IdCitta 
                           LEFT JOIN ImmaginiAnnuncio as i ON a.IdAnnuncio = i.IdAnnuncio ";

                $query = controlloGeneraliQuery($query, $filtri); 

                if ($filtri['coinquilini-max'] != '') 
                {
                    if (!str_contains($query,"WHERE"))
                        $query.="WHERE ";
                    $query.= "NumeroInquilini<='".$filtri['coinquilini-max']."' AND ";
                }

                if ($filtri['costo-mese-affitto-max'] != '') 
                {
                    if (!str_contains($query,"WHERE"))
                        $query.="WHERE ";
                    $query.= "PrezzoMensile<='".$filtri['costo-mese-affitto-max']."' AND ";
                }

                if ($filtri['indirizzo-affitto'] != '') 
                {
                    if (!str_contains($query,"WHERE"))
                        $query.="WHERE ";
                    $query.= "Indirizzo LIKE '%".$filtri['indirizzo-affitto']."%' AND ";
                }
                break;

            case 'Esperimenti':
                foreach ($filtri as $key => $value) {
                    $filtri[$key] = isset($filtri[$key])? $filtri[$key] : '';
                }

                $query .= "FROM Annuncio a JOIN AnnuncioEsperimenti e ON a.IdAnnuncio= e.IdAnnuncio 
                           JOIN Citta c ON a.IdCitta=c.IdCitta 
                           LEFT JOIN ImmaginiAnnuncio as i ON a.IdAnnuncio = i.IdAnnuncio ";
                $query = controlloGeneraliQuery($query, $filtri); 

                if ($filtri['laboratorio'] != '') 
                {
                    if (!str_contains($query,"WHERE"))
                        $query.="WHERE ";
                    $query.= "Laboratorio LIKE '%".$filtri['laboratorio']."%' AND ";
                }

                if ($filtri['esperimento-durata-min'] != '') 
                {
                    if (!str_contains($query,"WHERE"))
                        $query.="WHERE ";
                    $query.= "DurataPrevista>='".$filtri['esperimento-durata-min']."' AND ";
                }

                if ($filtri['esperimento-durata-max'] != '') 
                {
                    if (!str_contains($query,"WHERE"))
                        $query.="WHERE ";
                    $query.= "DurataPrevista<='".$filtri['esperimento-durata-max']."' AND ";
                }
                if ($filtri['esperimento-compenso-min'] != '') 
                {
                    if (!str_contains($query,"WHERE"))
                        $query.="WHERE ";
                    $query.= "Compenso>='".$filtri['esperimento-compenso-min']."' AND ";
                }
                if ($filtri['esperimento-compenso-max'] != '') 
                {
                    if (!str_contains($query,"WHERE"))
                        $query.="WHERE ";
                    $query.= "Compenso<='".$filtri['esperimento-compenso-max']."' AND ";
                }
                break;

           
            case 'Eventi':
                foreach ($filtri as $key => $value) {
                    $filtri[$key] = isset($filtri[$key])? $filtri[$key] : '';
                }

                $query .= "FROM Annuncio a JOIN AnnuncioEventi e ON a.IdAnnuncio= e.IdAnnuncio 
                            JOIN Citta c ON a.IdCitta=c.IdCitta 
                            LEFT JOIN ImmaginiAnnuncio as i ON a.IdAnnuncio = i.IdAnnuncio ";
                $query = controlloGeneraliQuery($query, $filtri);

                if ($filtri['luogo-evento'] != '') 
                {
                    if (!str_contains($query,"WHERE"))
                        $query.="WHERE ";
                    $query.= "Luogo LIKE '%".$filtri['luogo-evento']."%' AND ";
                }

                if ($filtri['evento-inizio'] != '') 
                {
                    if (!str_contains($query,"WHERE"))
                        $query.="WHERE ";
                    $query.= "DataEvento>='".$filtri['evento-inizio']."' AND ";
                }

                if ($filtri['evento-fine'] != '') 
                {
                    if (!str_contains($query,"WHERE"))
                        $query.="WHERE ";
                    $query.= "DataEvento<='".$filtri['evento-fine']."' AND ";
                }
                if ($filtri['evento-costo-max'] != '') 
                {
                    if (!str_contains($query,"WHERE"))
                        $query.="WHERE ";
                    $query.= "CostoEntrata<='".$filtri['evento-costo-max']."' AND ";
                }

                break;

            case 'Ripetizioni':
                foreach ($filtri as $key => $value) {
                    $filtri[$key] = isset($filtri[$key])? $filtri[$key] : '';
                }

                $query .= "FROM Annuncio a JOIN AnnuncioRipetizioni r ON a.IdAnnuncio= r.IdAnnuncio 
                           JOIN Citta c ON a.IdCitta=c.IdCitta 
                           LEFT JOIN ImmaginiAnnuncio as i ON a.IdAnnuncio = i.IdAnnuncio ";
                $query = controlloGeneraliQuery($query,$filtri); 

                if ($filtri['materia'] != '') 
                {
                    if (!str_contains($query,"WHERE"))
                        $query.="WHERE ";
                    $query.= "Materia LIKE '%".$filtri['materia']."%' AND ";
                }

                if ($filtri['livello'] != '') 
                {
                    if (!str_contains($query,"WHERE"))
                        $query.="WHERE ";
                    $query.= "Livello LIKE '%".$filtri['livello']."%' AND ";
                }

                if ($filtri['prezzo-ripetizioni-max'] != '') 
                {
                    if (!str_contains($query,"WHERE"))
                        $query.="WHERE ";
                    $query.= "PrezzoOrario<='".$filtri['prezzo-ripetizioni-max']."' AND ";
                }
                break;

            default:
                break;
        }
        //CERCA
        if ($filtri['cerca'] != '') 
        {
            if (!str_contains($query,"WHERE"))
                $query.="WHERE ";
            $query.= "(Titolo LIKE '%".$filtri['cerca']."%' OR Descrizione LIKE '%".$filtri['cerca']."%') AND ";
        }
    
        //Per le immagini
        if (!str_contains($query,"WHERE"))
            $query.="WHERE ";
        $query.= "i.Ordine = 1;";

        //echo $query;
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
                .$arrayRegistrazione['Password']."',"
                .$arrayRegistrazione['IdCitta'].");";

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
        $query = "SELECT a.IdAnnuncio, a.Titolo, a.DataPubblicazione, a.Categoria, c.NomeCitta, i.Percorso, i.AltText
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

    // PRECONDIZIONE - $categoria={Affitti, Esperimenti, Eventi, Ripetizioni} && $immagini.len >= 1
    public function inserimentoAnnuncio(string $titolo, string $descrizione, string $categoria, int $idUtente, int $idCitta, $campo1, $campo2, $campo3, $immagini) : int|false {

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

            foreach ($immagini as $img) {
                $stmtImmagine = $this->connection->prepare("INSERT INTO ImmaginiAnnuncio (IdAnnuncio, Percorso, AltText, Decorativa, Ordine) VALUES (?, ?, ?, ?, ?)");

                if(!$stmtImmagine) {
                    throw new Exception("Prepare immagine fallita");
                }

                $stmtImmagine->bind_param('issii', $idAnnuncio, $img['file'], $img['alt'], $img['decorativa'], $img['ordine']);

                if(!$stmtImmagine->execute()) {
                    throw new Exception("Execute Immagine Fallita");
                }

                $stmtImmagine->close();
            }

            mysqli_commit($this->connection);

            return $idAnnuncio;
        } catch (Exception $e) {
            mysqli_rollback($this->connection);
            return false;
        }


    }
}

?>
