<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'db_connection.php'; // Conectarea la baza de date

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role']; // 'organizer' sau 'participant'
    $recaptcha_response = $_POST['g-recaptcha-response'];

    // 1. Verificare reCAPTCHA
    $secret_key = "6LdPodIqAAAAAOfKxnW9T0BkxRX5VDdMtaX0sa_D";
    $verify_url = "https://www.google.com/recaptcha/api/siteverify";
    $response = file_get_contents($verify_url . "?secret=" . $secret_key . "&response=" . $recaptcha_response);
    $response_keys = json_decode($response, true);

    if (!$response_keys["success"]) {
        $error = "Verificarea reCAPTCHA a eșuat. Te rog încearcă din nou.";
    }

    // 2. Validare email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email invalid.";
    }

    // 3. Verificare dacă emailul există deja în baza de date
    if (empty($error)) {
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Acest email este deja folosit.";
        } else {
            // 4. Criptarea parolei
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // 5. Inserare utilizator în baza de date
            $query = "INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, ?, 'inactive')";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssss", $name, $email, $hashed_password, $role);
            $stmt->execute();

            // 6. Redirecționare către login
            header("Location: login.php"); 
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Creare cont</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    <div class="container">
        <h2>Creare cont</h2>
        <?php if (!empty($error)) { echo "<p class='error'>$error</p>"; } ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="name">Nume</label>
                <input type="text" name="name" id="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div class="form-group">
                <label for="password">Parolă</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div class="form-group">
                <label for="role">Tip cont</label>
                <select name="role" id="role" required>
                    <option value="organizer">Organizator</option>
                    <option value="participant">Participant</option>
                </select>
            </div>
            <!-- reCAPTCHA -->
            <div class="g-recaptcha" data-sitekey="6LdPodIqAAAAAJ7EDAUNKl7l7o8HEI3X1JDPdWAw"></div> <!-- 🔹 Înlocuiește cu site key -->
            <br><br>
            <button type="submit">Creează cont</button>
        </form>
        <div class="login-link">
            <p>Ai deja cont? <a href="login.php">Autentifică-te</a></p>
        </div>
    </div>
</body>
</html>
