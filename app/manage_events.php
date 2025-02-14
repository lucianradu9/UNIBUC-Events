<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Stergere eveniment
if (isset($_GET['delete'])) {
    $event_id = $_GET['delete'];
    $query = "DELETE FROM events WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $event_id, $user_id);

    if ($stmt->execute()) {
        $success = "Evenimentul a fost sters cu succes.";
    } else {
        $error = "A aparut o eroare la stergerea evenimentului.";
    }
}

// Lista evenimente utilizator
$query = "SELECT * FROM events WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
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
    <title>Gestionare Evenimente | UNIBUC Events</title>
    <link rel="icon" href="../media/favicon.png" type="image/png">
    <link rel="shortcut icon" href="../media/favicon.png" type="image/png">
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
            <a href="create_event.php">Creează un eveniment nou</a>
        </div>

        <div class="back-link">
            <a href="events2025.php">Alte evenimente</a>
        </div>
        <br><br>

        <div class="container">
            <div class="export-section">
                <label for="export_format">Exportă în format:</label>
                <select id="export_format">
                    <option value="xlsx">Excel (.xlsx)</option>
                    <option value="doc">Word (.doc)</option>
                    <option value="pdf">PDF (.pdf)</option>
                </select>
                <br><br>
                <button id="export_button">Exportă</button>
            </div>

            <script>
            document.getElementById("export_button").addEventListener("click", function () {
                let format = document.getElementById("export_format").value;
                window.location.href = "export_events.php?format=" + format;
            });
            </script>
            <br><br>
            <form action="import_events.php" method="post" enctype="multipart/form-data">
                <label for="export_format">Importă evenimente</label>
                <input type="file" name="event_file" accept=".pdf, .docx, .xls, .xlsx" required>
                <br><br>
                <button type="submit">Importă</button>
            </form>
        </div>
    </div>
</body>
</html>
