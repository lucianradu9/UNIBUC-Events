<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "event_app";
$port = 8889;

// Creare conexiune
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Verificare conexiune
if ($conn->connect_error) {
    die("Conexiunea la baza de date a eșuat: " . $conn->connect_error);
}
?>
