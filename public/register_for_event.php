<?php
session_start();
include 'db_connection.php'; // Conectarea la baza de date

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$event_id = $_POST['event_id'];

// Verificăm dacă utilizatorul este deja înscris
$query_check = "SELECT * FROM event_registrations WHERE user_id = ? AND event_id = ?";
$stmt_check = $conn->prepare($query_check);
$stmt_check->bind_param("ii", $user_id, $event_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    // Utilizatorul este deja înscris
    header("Location: view_event_details.php?event_id=$event_id&error=already_registered");
    exit;
} else {
    // Înregistrăm utilizatorul
    $query_insert = "INSERT INTO event_registrations (user_id, event_id) VALUES (?, ?)";
    $stmt_insert = $conn->prepare($query_insert);
    $stmt_insert->bind_param("ii", $user_id, $event_id);

    if ($stmt_insert->execute()) {
        header("Location: view_event_details.php?event_id=$event_id&success=registered");
    } else {
        header("Location: view_event_details.php?event_id=$event_id&error=registration_failed");
    }
}
?>
