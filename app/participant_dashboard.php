<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'participant') {
    header("Location: login.php");
    exit;
}

include 'db_connection.php';

// Lista evenimente la care s-a inscris utilizatorul
$user_id = $_SESSION['user_id'];
$query = "SELECT events.id, events.event_name, events.event_description, events.event_date, events.event_time 
          FROM event_registrations 
          JOIN events ON event_registrations.event_id = events.id 
          WHERE event_registrations.user_id = ?";
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
    <title>Dashboard Participant | UNIBUC Events</title>
    <link rel="icon" href="../media/favicon.png" type="image/png">
    <link rel="shortcut icon" href="../media/favicon.png" type="image/png">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="container_event">
        <h2>Evenimente la care te-ai înscris:</h2>
        <table>
            <thead>
                <tr>
                    <th>Nume Eveniment</th>
                    <th>Descriere</th>
                    <th>Data</th>
                    <th>Ora</th>
                    <th>Acțiuni</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($event = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . htmlspecialchars($event['event_name']) . "</td>
                                <td>" . htmlspecialchars($event['event_description']) . "</td>
                                <td>" . htmlspecialchars($event['event_date']) . "</td>
                                <td>" . htmlspecialchars($event['event_time']) . "</td>
                                <td>
                                    <form method='POST' action='unregister_from_event.php' style='display:inline;'>
                                        <input type='hidden' name='event_id' value='{$event['id']}'>
                                        <button type='submit' class='btn delete'>Anulare înscriere</button>
                                    </form>
                                </td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>Nu te-ai înscris la niciun eveniment.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <div class="back-link">
            <a href="view_events.php">Vezi evenimente disponibile</a> |
            <a href="logout.php">Ieși din cont</a>
        </div>
        <br>
        <div class="back-link">
            <a href="contact.php">Contactează-ne</a>
        </div>
    </div>
</body>
</html>
