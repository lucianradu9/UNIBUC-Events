<?php
$servername = "localhost"; // Serverul MySQL
$username = "root"; // Numele de utilizator pentru MySQL
$password = ""; // Parola pentru utilizatorul root (goală în MAMP)
$dbname = "event_app"; // Numele bazei de date create
$port = 8889; // Portul pentru MySQL în MAMP

// Crearea conexiunii
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Verificăm conexiunea
if ($conn->connect_error) {
    die("Conexiunea la baza de date a eșuat: " . $conn->connect_error);
}
?>
