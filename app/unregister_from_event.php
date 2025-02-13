<?php
session_start();
include 'db_connection.php'; // Conectare la baza de date

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['event_id'])) {
    $event_id = $_POST['event_id'];
    $user_id = $_SESSION['user_id'];

    // Ștergem înregistrarea utilizatorului pentru acest eveniment
    $query = "DELETE FROM event_registrations WHERE user_id = ? AND event_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $user_id, $event_id);

    if ($stmt->execute()) {
        // Redirecționează înapoi la pagina detaliilor evenimentului cu un mesaj de succes
        header("Location: view_event_details.php?event_id=$event_id&success=unregistered");
        exit;
    } else {
        // Redirecționează cu un mesaj de eroare dacă nu s-a putut efectua dezabonarea
        header("Location: view_event_details.php?event_id=$event_id&error=unregister_failed");
        exit;
    }
} else {
    // Dacă accesul nu este prin POST, redirecționează către dashboard
    header("Location: participant_dashboard.php");
    exit;
}
?>
