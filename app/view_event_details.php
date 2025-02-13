<?php
session_start();
include 'db_connection.php'; // Conectarea la baza de date

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$event_id = $_GET['event_id']; // Preluăm event_id din URL

// Verificăm dacă evenimentul există în baza de date
$query_event = "SELECT * FROM events WHERE id = ?";
$stmt_event = $conn->prepare($query_event);
$stmt_event->bind_param("i", $event_id);
$stmt_event->execute();
$event = $stmt_event->get_result()->fetch_assoc();

// Dacă evenimentul nu există, redirecționăm către o pagină de eroare
if (!$event) {
    header("Location: participant_dashboard.php?error=event_not_found");
    exit;
}

// Verificăm dacă utilizatorul este deja înscris la eveniment
$user_id = $_SESSION['user_id'];
$query_registration = "SELECT * FROM event_registrations WHERE user_id = ? AND event_id = ?";
$stmt_registration = $conn->prepare($query_registration);
$stmt_registration->bind_param("ii", $user_id, $event_id);
$stmt_registration->execute();
$result = $stmt_registration->get_result();
$already_registered = $result->num_rows > 0;
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalii Eveniment</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    <div class="container">
        <h2>Detalii Eveniment: <?php echo htmlspecialchars($event['event_name']); ?></h2>
        <p><strong>Descriere:</strong> <?php echo htmlspecialchars($event['event_description']); ?></p>
        <p><strong>Data:</strong> <?php echo htmlspecialchars($event['event_date']); ?></p>
        <p><strong>Ora:</strong> <?php echo htmlspecialchars($event['event_time']); ?></p>
        <p><strong>Organizator:</strong> <?php echo htmlspecialchars($event['organizer_name']); ?></p>

        <?php if ($already_registered): ?>
            <form method="POST" action="unregister_from_event.php">
                <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">
                <button type="submit" class="btn btn-danger">Anulează înscrierea</button>
            </form>
        <?php else: ?>
            <form method="POST" action="register_for_event.php">
                <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">

                <!-- reCAPTCHA -->
                <div class="g-recaptcha" data-sitekey="6LdPodIqAAAAAJ7EDAUNKl7l7o8HEI3X1JDPdWAw"></div>
                <br><br>
                <button type="submit" class="btn btn-primary">Înscrie-te la acest eveniment</button>
            </form>
        <?php endif; ?>

        <br>
        <a href="participant_dashboard.php" class="btn btn-secondary">Înapoi la Dashboard</a>
        <br><br>

        <?php
        if (isset($_GET['success']) && $_GET['success'] == 'registered') {
            echo "<p class='success'>Te-ai înscris cu succes la eveniment!</p>";
        }
        if (isset($_GET['error'])) {
            if ($_GET['error'] == 'recaptcha_failed') {
                echo "<p class='error'>Verificarea reCAPTCHA a eșuat. Te rog încearcă din nou.</p>";
            }
            if ($_GET['error'] == 'already_registered') {
                echo "<p class='error'>Ești deja înscris la acest eveniment.</p>";
            }
            if ($_GET['error'] == 'registration_failed') {
                echo "<p class='error'>A apărut o eroare la înscriere. Încearcă din nou.</p>";
            }
        }
        ?>
    </div>
</body>
</html>
