<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'db_connection.php';

$user_id = $_SESSION['user_id'];

if (!isset($_GET['event_id'])) {
    header("Location: manage_events.php");
    exit;
}

$event_id = $_GET['event_id'];

$query = "SELECT * FROM events WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $event_id, $user_id);
$stmt->execute();
$event_result = $stmt->get_result();

if ($event_result->num_rows === 0) {
    header("Location: manage_events.php");
    exit;
}

$event = $event_result->fetch_assoc();

// Lista de participanti
$participants_query = "SELECT users.name, users.email FROM event_registrations 
                        JOIN users ON event_registrations.user_id = users.id WHERE event_registrations.event_id = ?";
$participants_stmt = $conn->prepare($participants_query);
$participants_stmt->bind_param("i", $event_id);
$participants_stmt->execute();
$participants_result = $participants_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalii Eveniment | UNIBUC Events</title>
    <link rel="icon" href="../media/favicon.png" type="image/png">
    <link rel="shortcut icon" href="../media/favicon.png" type="image/png">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="container">
        <h2>Detalii Eveniment</h2>

        <h3><?php echo htmlspecialchars($event['event_name']); ?></h3>
        <p><strong>Descriere:</strong> <?php echo htmlspecialchars($event['event_description']); ?></p>
        <p><strong>Data:</strong> <?php echo htmlspecialchars($event['event_date']); ?></p>
        <p><strong>Ora:</strong> <?php echo htmlspecialchars($event['event_time']); ?></p>

        <h4>Participanți:</h4>
        <?php if ($participants_result->num_rows > 0): ?>
            <ul>
                <?php while ($participant = $participants_result->fetch_assoc()): ?>
                    <li><?php echo htmlspecialchars($participant['name']) . " - " . htmlspecialchars($participant['email']); ?></li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>Nu sunt participanți înscriși.</p>
        <?php endif; ?>

        <a href="edit_event.php?event_id=<?php echo $event['id']; ?>" class="btn">Editeaza Evenimentul</a>
        <a href="manage_events.php?delete=<?php echo $event['id']; ?>" class="btn delete">Sterge Evenimentul</a>

        <div class="back-link">
            <a href="manage_events.php">Înapoi la Gestionarea Evenimentelor</a>
        </div>
    </div>
</body>
</html>
