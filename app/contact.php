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

    // reCAPTCHA
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
            
            // E-mail de confirmare
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
    <link rel="stylesheet" href="../css/styles.css">
    <title>Contact | UNIBUC Events</title>
    <link rel="icon" href="../media/favicon.png" type="image/png">
    <link rel="shortcut icon" href="../media/favicon.png" type="image/png">
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
