<?php
$servername = "jfrpocyduwfg38kq.chr7pe7iynqr.eu-west-1.rds.amazonaws.com";
$username = "hp1jxz1f3y09ptk6";
$password = "htgsav0hj7rp6gnz";
$dbname = "evavug5z2045gv8d";
$port = 3306;

// Creare conexiune
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Verificare conexiune
if ($conn->connect_error) {
    die("Conexiunea la baza de date a eșuat: " . $conn->connect_error);
}
?>
