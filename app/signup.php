<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'db_connection.php';

// Includere PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role']; // 'organizer' sau 'participant'
    $recaptcha_response = $_POST['g-recaptcha-response'];

    // 1. reCAPTCHA
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

    // 3. Verificare email
    if (empty($error)) {
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Acest email este deja folosit.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // 4. Adaugare utilizator
            $query = "INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, ?, 'inactive')";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssss", $name, $email, $hashed_password, $role);
            $stmt->execute();

            // 5. E-mail de bun venit
            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.mailersend.net';
                $mail->SMTPAuth = true;
                $mail->Username = 'MS_flCJ0E@trial-3zxk54v9zj1gjy6v.mlsender.net';
                $mail->Password = 'mssp.Lv3DFma.neqvygm9jy8l0p7w.NZBCYcl';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('MS_flCJ0E@trial-3zxk54v9zj1gjy6v.mlsender.net', 'UNIBUC Events');
                $mail->addAddress($email, $name);

                $mail->isHTML(true);
                $mail->Subject = 'Bun venit!';
                $mail->Body    = 'Bun venit, ' . $name . '! Inregistrarea ta pe UNIBUC Events a fost efectuata cu succes.';

                $mail->send();
            } catch (Exception $e) {
                $error = "E-mailul de bun venit nu a putut fi trimis. Eroare: {$mail->ErrorInfo}";
            }

            header("Location: login.php"); 
            exit;
        }
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
    <title>Creare cont | UNIBUC Events</title>
    <link rel="icon" href="../media/favicon.png" type="image/png">
    <link rel="shortcut icon" href="../media/favicon.png" type="image/png">
    <link rel="stylesheet" href="../css/styles.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    <section class="content">
        <div class="container_img">
            <p class="unibuc">UNIBUC Events</p>
        </div>
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
                <div class="g-recaptcha" data-sitekey="6LdPodIqAAAAAJ7EDAUNKl7l7o8HEI3X1JDPdWAw"></div>
                <br><br>
                <button type="submit">Creează cont</button>
            </form>
            <div class="signup-link">
                <p>Ai deja cont? <a href="login.php">Autentifică-te</a></p>
            </div>
            <div class="signup-link">
                <p><a href="contact.php">Contactează-ne</a></p>
            </div>
        </div>
    </section>
</body>
</html>
