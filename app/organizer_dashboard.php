<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'organizer') {
    header("Location: login.php");
    exit;
}

include 'db_connection.php';

?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-E9SNKB5WMQ"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-E9SNKB5WMQ');
    </script>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Organizator | UNIBUC Events</title>
    <link rel="icon" href="../media/favicon.png" type="image/png">
    <link rel="shortcut icon" href="../media/favicon.png" type="image/png">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <section class="content">
        <div class="container_img">
            <p class="unibuc">UNIBUC Events</p>
        </div>
        <div class="container">
            <h2>Bine ai venit!</h2>
            <div class="organizer_buttons">
                <p><a href="create_event.php" class="btn2">Creează eveniment nou</a></p>
                <p><a href="manage_events.php" class="btn2">Gestionează evenimente</a></p>
            </div>
            <br>
            <div class="back-link">
                <a href="contact.php">Contactează-ne</a>
            </div>
            <br>
            <div class="back-link">
                <a href="logout.php">Ieși din cont</a>
            </div>
        </div>
    </section>
</body>
</html>
