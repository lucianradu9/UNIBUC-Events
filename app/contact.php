<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
include('db_connection.php');

$success_message = "";
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    $recaptcha_response = $_POST['g-recaptcha-response'];

    // Verificare reCAPTCHA
    $secret_key = "6LdPodIqAAAAAOfKxnW9T0BkxRX5VDdMtaX0sa_D";
    $verify_url = "https://www.google.com/recaptcha/api/siteverify";
    $response = file_get_contents($verify_url . "?secret=" . $secret_key . "&response=" . $recaptcha_response);
    $response_keys = json_decode($response, true);

    if (!$response_keys["success"]) {
        $error_message = "Verificarea reCAPTCHA a eșuat. Te rog încearcă din nou.";
    }

    if (empty($error_message)) {
        $sql = "INSERT INTO contact_messages (name, email, message) VALUES ('$name', '$email', '$message')";
        
        if (mysqli_query($conn, $sql)) {
            $success_message = "Mesajul a fost trimis cu succes.";
            
            // Trimitere e-mail de confirmare
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.mailersend.net';
                $mail->SMTPAuth = true;
                $mail->Username = 'MS_LdBNGk@trial-z86org8qvqzgew13.mlsender.net';
                $mail->Password = 'mssp.1ce9Plb.0r83ql3j28zgzw1j.Me0wGMH';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                
                $mail->setFrom('MS_LdBNGk@trial-z86org8qvqzgew13.mlsender.net', 'UNIBUC Events');
                $mail->addAddress($email, $name);
                
                $mail->isHTML(true);
                $mail->Subject = 'Confirmare mesaj primit';
                $mail->Body    = 'Salut ' . $name . ',<br><br>Am primit mesajul tau si iti vom raspunde in cel mai scurt timp posibil.';
                
                $mail->send();
            } catch (Exception $e) {
                $error_message = "E-mailul de confirmare nu a putut fi trimis. Eroare: {$mail->ErrorInfo}";
            }
        } else {
            $error_message = "Eroare la salvarea mesajului în baza de date.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
    <title>Contact</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    <section class="content">
        <div class="container">
            <form action="contact.php" method="POST">
                <label for="name">Numele tău:</label>
                <input type="text" name="name" id="name" required><br>
                <br>
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required><br>
                <br>
                <label for="message">Mesaj:</label>
                <textarea name="message" id="message" required></textarea>
                <br><br>
                <div class="g-recaptcha" data-sitekey="6LdPodIqAAAAAJ7EDAUNKl7l7o8HEI3X1JDPdWAw"></div>
                <br>
                <button type="submit">Trimite mesajul</button>
            </form>
        </div>

        <div class="back-link">
            <a href="javascript:window.history.back();">Înapoi</a>
        </div>

        <?php if ($success_message): ?>
            <p style="color: green;"> <?php echo $success_message; ?> </p>
        <?php elseif ($error_message): ?>
            <p style="color: red;"> <?php echo $error_message; ?> </p>
        <?php endif; ?>
    </section>
</body>
</html>
