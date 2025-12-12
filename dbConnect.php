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
}

?>