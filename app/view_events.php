<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'participant') {
    header("Location: login.php");
    exit;
}

// Lista evenimentelor
$query = "SELECT * FROM events ORDER BY event_date ASC, event_time ASC";
$result = $conn->query($query);

?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-E9SNKB5WMQ"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-E9SNKB5WMQ');
    </script>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vizualizare Evenimente | UNIBUC Events</title>
    <link rel="icon" href="../media/favicon.png" type="image/png">
    <link rel="shortcut icon" href="../media/favicon.png" type="image/png">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="container_event">
        <h2>Evenimente Disponibile</h2>

        <?php if ($result && $result->num_rows > 0): ?>
            <div class="event-list">
                <?php while ($event = $result->fetch_assoc()): ?>
                    <div class="event-item">
                        <h3><?php echo htmlspecialchars($event['event_name']); ?></h3>
                        <p><strong>Descriere:</strong> <?php echo htmlspecialchars($event['event_description']); ?></p>
                        <p><strong>Data:</strong> <?php echo htmlspecialchars($event['event_date']); ?></p>
                        <p><strong>Ora:</strong> <?php echo htmlspecialchars($event['event_time']); ?></p>
                        <a href="view_event_details.php?event_id=<?php echo $event['id']; ?>" class="btn">Vezi Detalii</a>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>Nu exista evenimente disponibile in acest moment.</p>
        <?php endif; ?>

        <div class="back-link">
            <a href="events2025.php">Alte evenimente</a> |
            <a href="participant_dashboard.php">Înapoi la Dashboard</a>
        </div>

        <div class="back-link">
            
        </div>
    </div>
</body>
</html>
