<?php
session_start();
include 'db_connection.php'; // Conectare la BD

require '../vendor/autoload.php'; // PDF Parser

use Smalot\PdfParser\Parser;

if (!isset($_SESSION['user_id'])) {
    die("Eroare: Nu ești autentificat.");
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['pdf_file'])) {
    $pdf_tmp = $_FILES['pdf_file']['tmp_name'];

    if (mime_content_type($pdf_tmp) !== 'application/pdf') {
        die("Eroare: Fișierul nu este un PDF valid.");
    }

    $parser = new Parser();
    $pdf = $parser->parseFile($pdf_tmp);
    $text = $pdf->getText();


    // Extragem evenimentele folosind regex
    preg_match_all('/(.+?)\s+(\d{4}-\d{2}-\d{2})(\d{2}:\d{2}:\d{2})/', $text, $matches, PREG_SET_ORDER);

    if (!$matches) {
        die("Eroare: Nu s-au găsit evenimente valide în PDF.");
    }

    $stmt = $conn->prepare("INSERT INTO events (user_id, event_name, event_description, event_date, event_time) VALUES (?, ?, 'Eveniment importat', ?, ?)");

    foreach ($matches as $event) {
        $event_name = trim($event[1]);
        $event_date = $event[2];
        $event_time = $event[3];

        $stmt->bind_param("isss", $user_id, $event_name, $event_date, $event_time);
        $stmt->execute();
    }

    header("Location: manage_events.php?success=imported");
    exit;

} else {
    die("Eroare: Nicio acțiune validă.");
}
?>
