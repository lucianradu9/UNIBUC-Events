<?php
session_start();

// Verificăm dacă utilizatorul este autentificat și dacă este participant
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'participant') {
    header("Location: login.php");
    exit;
}

// Conectare la baza de date
include 'db_connection.php';

// Obținem evenimentele la care utilizatorul este înscris
$user_id = $_SESSION['user_id'];
$query = "SELECT events.event_name, events.event_date, events.event_time FROM event_registrations 
          JOIN events ON event_registrations.event_id = events.id WHERE event_registrations.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Participant</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="container">
        <h2>Dashboard Participant</h2>
        <p>Bine ai venit, <?php echo $_SESSION['user_name']; ?>!</p>

        <h3>Evenimente la care te-ai înscris:</h3>
        <?php if ($result->num_rows > 0): ?>
            <ul>
                <?php while ($event = $result->fetch_assoc()): ?>
                    <li><?php echo htmlspecialchars($event['event_name']) . " - " . htmlspecialchars($event['event_date']) . " la " . htmlspecialchars($event['event_time']); ?></li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>Nu te-ai înscris la niciun eveniment.</p>
        <?php endif; ?>

        <p><a href="view_events.php">Vezi evenimente disponibile</a></p>
        <p><a href="logout.php">Ieși din cont</a></p>
    </div>
</body>
</html>
