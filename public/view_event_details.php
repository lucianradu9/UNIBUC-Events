<?php
session_start();
include 'db_connection.php'; // Conectarea la baza de date

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$event_id = $_GET['event_id'];
$user_id = $_SESSION['user_id'];

// Verificăm dacă utilizatorul este deja înscris la eveniment
$query = "SELECT * FROM event_registrations WHERE user_id = ? AND event_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $event_id);
$stmt->execute();
$result = $stmt->get_result();
$already_registered = $result->num_rows > 0; // Verifică dacă există o înscriere

// Detalii despre eveniment
$query_event = "SELECT * FROM events WHERE id = ?";
$stmt_event = $conn->prepare($query_event);
$stmt_event->bind_param("i", $event_id);
$stmt_event->execute();
$event = $stmt_event->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalii Eveniment</title>
    <link rel="stylesheet" href="../css/styles.css">
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
                <button type="submit" class="btn btn-primary">Înscrie-te la acest eveniment</button>
            </form>
        <?php endif; ?>

        <br>
        <a href="participant_dashboard.php" class="btn btn-secondary">Înapoi la Dashboard</a>
    </div>
</body>
</html>
