<?php

require_once "dbConnect.php";
use DB\DBAccess;

$dbAccess = new DBAccess();
$connectionOk = $dbAccess->openDBConnection();
if($connectionOk) {
    echo "Connessione al database avvenuta con successo.";
    $dbAccess->closeConnection();
} else {
    echo "Connessione al database fallita.";
}

?>