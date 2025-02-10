<?php
session_start();

// Verificăm dacă utilizatorul este autentificat și dacă este organizator
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'organizer') {
    header("Location: login.php");
    exit;
}

// Conectare la baza de date
include 'db_connection.php';

// Aici vor veni funcționalitățile pentru organizator, de exemplu, crearea de evenimente
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Organizator</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="container">
        <h2>Dashboard Organizator</h2>
        <p>Bine ai venit!</p>
        <p><a href="create_event.php" class="btn">Creează eveniment nou</a></p>
        <p><a href="manage_events.php" class="btn">Gestionează evenimente</a></p>
        <p><a href="logout.php">Ieși din cont</a></p>
    </div>
</body>
</html>
