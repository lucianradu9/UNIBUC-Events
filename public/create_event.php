<?php
session_start();
include 'db_connection.php'; // Conectarea la baza de date

// Verifică dacă utilizatorul este autentificat
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$error = '';
$success = '';

// Verifică dacă formularul a fost trimis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_name = $_POST['event_name'];
    $event_description = $_POST['event_description'];
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $recaptcha_response = $_POST['g-recaptcha-response'];

    // 1. Verificare reCAPTCHA
    $secret_key = "6LdPodIqAAAAAOfKxnW9T0BkxRX5VDdMtaX0sa_D";
    $verify_url = "https://www.google.com/recaptcha/api/siteverify";
    $response = file_get_contents($verify_url . "?secret=" . $secret_key . "&response=" . $recaptcha_response);
    $response_keys = json_decode($response, true);

    if (!$response_keys["success"]) {
        $error = "Verificarea reCAPTCHA a eșuat. Te rog încearcă din nou.";
    }

    // 2. Validări simple
    if (empty($event_name) || empty($event_description) || empty($event_date) || empty($event_time)) {
        $error = "Toate câmpurile sunt obligatorii.";
    }

    // 3. Inserare eveniment dacă nu există erori
    if (empty($error)) {
        $user_id = $_SESSION['user_id']; // ID-ul utilizatorului autentificat
        $query = "INSERT INTO events (user_id, event_name, event_description, event_date, event_time) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("issss", $user_id, $event_name, $event_description, $event_date, $event_time);

        if ($stmt->execute()) {
            $success = "Evenimentul a fost creat cu succes!";
        } else {
            $error = "A apărut o eroare la crearea evenimentului. Încearcă din nou.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Creare Eveniment</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    <div class="container">
        <h2>Creare Eveniment</h2>

        <?php if (!empty($error)) { echo "<p class='error'>$error</p>"; } ?>
        <?php if (!empty($success)) { echo "<p class='success'>$success</p>"; } ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="event_name">Nume Eveniment</label>
                <input type="text" name="event_name" id="event_name" required>
            </div>
            <div class="form-group">
                <label for="event_description">Descriere Eveniment</label>
                <input type="text" name="event_description" id="event_description" required>
            </div>
            <div class="form-group">
                <label for="event_date">Data Evenimentului</label>
                <input type="date" name="event_date" id="event_date" required>
            </div>
            <div class="form-group">
                <label for="event_time">Ora Evenimentului</label>
                <input type="time" name="event_time" id="event_time" required>
            </div>

            <!-- reCAPTCHA -->
            <div class="g-recaptcha" data-sitekey="6LdPodIqAAAAAJ7EDAUNKl7l7o8HEI3X1JDPdWAw"></div>
            <br><br>
            <button type="submit">Creează Eveniment</button>
        </form>

        <div class="back-link">
            <a href="organizer_dashboard.php">Înapoi la Dashboard</a>
        </div>
    </div>
</body>
</html>
