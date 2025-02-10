<?php
session_start();
include 'db_connection.php'; // Conectarea la baza de date

// Verifică dacă utilizatorul este autentificat
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Dacă utilizatorul vrea să șteargă un eveniment
if (isset($_GET['delete'])) {
    $event_id = $_GET['delete'];
    $query = "DELETE FROM events WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $event_id, $user_id);

    if ($stmt->execute()) {
        $success = "Evenimentul a fost șters cu succes.";
    } else {
        $error = "A apărut o eroare la ștergerea evenimentului.";
    }
}

// Obține lista de evenimente ale utilizatorului
$query = "SELECT * FROM events WHERE user_id = ?";
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
    <title>Gestionare Evenimente</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="container_event">
        <h2>Gestionare Evenimente</h2>

        <?php if (isset($success)) { echo "<p class='success'>$success</p>"; } ?>
        <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>

        <a href="organizer_dashboard.php">Înapoi la Dashboard</a>

        <h3>Evenimentele tale:</h3>
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
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['event_name']}</td>
                                <td>{$row['event_description']}</td>
                                <td>{$row['event_date']}</td>
                                <td>{$row['event_time']}</td>
                                <td>
                                    <a href='event_details.php?event_id={$row['id']}' class='btn'>Vezi Detalii</a>
                                </td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>Nu ai niciun eveniment creat.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <div class="back-link">
            <a href="create_event.php">Creează un Eveniment Nou</a>
        </div>
    </div>
</body>
</html>
