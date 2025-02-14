<?php
session_start();
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Verificare date utilizator
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verificăm parola criptată
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];

            // Redirecționare pe baza rolului
            if ($user['role'] === 'organizer') {
                header("Location: organizer_dashboard.php");
            } elseif ($user['role'] === 'participant') {
                header("Location: participant_dashboard.php");
            }
            exit;
        } else {
            $error = "Email sau parolă incorecte.";
        }
    } else {
        $error = "Email sau parolă incorecte.";
    }
}
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
    <title>Autentificare</title>
    <link rel="icon" href="../media/favicon.png" type="image/png">
    <link rel="shortcut icon" href="../media/favicon.png" type="image/png">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <section class="content">
        <div class="container_img">
            <p class="unibuc">UNIBUC Events</p>
        </div>
        <div class="container">
            <h2>Autentificare</h2>
            <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Parolă</label>
                    <input type="password" name="password" id="password" required>
                </div>
                <button type="submit">Loghează-te</button>
            </form>
            <div class="signup-link">
                <p>Nu ai cont? <a href="signup.php">Creează cont</a></p>
            </div>
            <div class="signup-link">
                <p><a href="contact.php">Contactează-ne</a></p>
            </div>
            <div class="signup-link">
                <p><a href="../docs/detalii.html">Detalii aplicație</a></p>
            </div>
        </div>
    </section>
</body>
</html>
