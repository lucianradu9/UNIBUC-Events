<?php
session_start();
include 'db_connection.php'; // Conectarea la baza de date

// Verifică dacă utilizatorul este autentificat
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Verifică dacă există un ID valid pentru eveniment
if (!isset($_GET['event_id'])) {
    die("ID-ul evenimentului nu a fost specificat.");
}

$event_id = $_GET['event_id'];
$user_id = $_SESSION['user_id']; // ID-ul utilizatorului conectat

// Obține detaliile evenimentului
$query = "SELECT * FROM events WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $event_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Evenimentul nu există sau nu aparține utilizatorului conectat.");
}

$event = $result->fetch_assoc();
$error = '';
$success = '';

// Verifică dacă formularul a fost trimis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_name = $_POST['event_name'];
    $event_description = $_POST['event_description'];
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];

    // Validări simple
    if (empty($event_name) || empty($event_description) || empty($event_date) || empty($event_time)) {
        $error = "Toate câmpurile sunt obligatorii.";
    } else {
        // Actualizare eveniment în baza de date
        $update_query = "UPDATE events SET event_name = ?, event_description = ?, event_date = ?, event_time = ? WHERE id = ? AND user_id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("ssssii", $event_name, $event_description, $event_date, $event_time, $event_id, $user_id);

        if ($update_stmt->execute()) {
            $success = "Evenimentul a fost actualizat cu succes!";
        } else {
            $error = "A apărut o eroare la actualizarea evenimentului. Încearcă din nou.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editare Eveniment</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="container">
        <h2>Editare Eveniment</h2>

        <?php if (!empty($error)) { echo "<p class='error'>$error</p>"; } ?>
        <?php if (!empty($success)) { echo "<p class='success'>$success</p>"; } ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="event_name">Nume Eveniment</label>
                <input type="text" name="event_name" id="event_name" value="<?= htmlspecialchars($event['event_name']) ?>" required>
            </div>
            <div class="form-group">
                <label for="event_description">Descriere Eveniment</label>
                <textarea name="event_description" id="event_description" required><?= htmlspecialchars($event['event_description']) ?></textarea>
            </div>
            <div class="form-group">
                <label for="event_date">Data Evenimentului</label>
                <input type="date" name="event_date" id="event_date" value="<?= htmlspecialchars($event['event_date']) ?>" required>
            </div>
            <div class="form-group">
                <label for="event_time">Ora Evenimentului</label>
                <input type="time" name="event_time" id="event_time" value="<?= htmlspecialchars($event['event_time']) ?>" required>
            </div>
            <button type="submit">Actualizează Eveniment</button>
        </form>

        <div class="back-link">
            <a href="organizer_dashboard.php">Înapoi la Dashboard</a>
        </div>
    </div>
</body>
</html>
