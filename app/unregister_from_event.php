<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['event_id'])) {
    $event_id = $_POST['event_id'];
    $user_id = $_SESSION['user_id'];

    // Stergere inregistrare
    $query = "DELETE FROM event_registrations WHERE user_id = ? AND event_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $user_id, $event_id);

    if ($stmt->execute()) {
        header("Location: view_event_details.php?event_id=$event_id&success=unregistered");
        exit;
    } else {
        // Eroare stergere inregistrare
        header("Location: view_event_details.php?event_id=$event_id&error=unregister_failed");
        exit;
    }
} else {
    header("Location: participant_dashboard.php");
    exit;
}
?>
