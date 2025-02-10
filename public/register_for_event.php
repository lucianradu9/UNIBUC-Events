<?php
session_start();
include 'db_connection.php'; // Conectarea la baza de date

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = $_POST['event_id'];
    $user_id = $_SESSION['user_id'];
    $recaptcha_response = $_POST['g-recaptcha-response'];

    // 1. Verificare reCAPTCHA
    $secret_key = "6LdPodIqAAAAAOfKxnW9T0BkxRX5VDdMtaX0sa_D";
    $verify_url = "https://www.google.com/recaptcha/api/siteverify";
    $response = file_get_contents($verify_url . "?secret=" . $secret_key . "&response=" . $recaptcha_response);
    $response_keys = json_decode($response, true);

    if (!$response_keys["success"]) {
        header("Location: view_event_details.php?event_id=$event_id&error=recaptcha_failed");
        exit;
    }

    // 2. Verificăm dacă utilizatorul este deja înscris
    $query = "SELECT * FROM event_registrations WHERE user_id = ? AND event_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $user_id, $event_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        header("Location: view_event_details.php?event_id=$event_id&error=already_registered");
        exit;
    }

    // 3. Inserăm înregistrarea în baza de date
    $query = "INSERT INTO event_registrations (user_id, event_id) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $user_id, $event_id);

    if ($stmt->execute()) {
        header("Location: view_event_details.php?event_id=$event_id&success=registered");
    } else {
        header("Location: view_event_details.php?event_id=$event_id&error=registration_failed");
    }
    exit;
}
?>
