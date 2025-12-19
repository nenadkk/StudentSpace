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

    public function executeQuery($query) {
        //questa funzione apre/chiude una connessione per la query e restituisce 
        //il risultato sotto forma di array associativo per facilitarne l'utilizzo
        //(di base il risultato della query sarebbe un buffer) 
        $this->openDBConnection();

        $result = mysqli_query($this->connection, $query) or 
                  die("Errore: " . mysqli_error($this->connection)); //caso query fallita
        
        if(gettype($result)!="boolean")
        //mysqli_query restituisce un buffer con i risultati della query nel caso essa sia 
        //un SELECT, SHOW, DESCRIBE o EXPLAIN. In tutti gli altri casi ritorna un
        //booleano, quindi non sempre ha senso vedere se il risultato ha delle righe
        {
            //caso query senza risultati 
            if(!mysqli_num_rows($result)) return array();

            //caso query con risultati
            $answer = array();
            while($row = mysqli_fetch_assoc($result))
            //$row è un array associativo contenente un solo elemento 
            //se facessi solo array_push($answer, $row) otterrei un array di array, 
            //il che complicherebbe solo le cose al momento dell'utilizzo. Quindi ne 
            //estraggo il contenuto
            {
                $answer = array_merge($answer, $row);
            }
            $result->free();
            $this->closeConnection();
            return $answer;
        }
    }
}

?>
