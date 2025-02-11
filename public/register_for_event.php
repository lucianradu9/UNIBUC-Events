<?php
session_start();
include 'db_connection.php'; // Conectarea la baza de date

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = $_POST['event_id'];
    $user_id = $_SESSION['user_id'];
    $recaptcha_response = $_POST['g-recaptcha-response'];

    // 1. Verificare reCAPTCHA + preluare date utilizator si eveniment
    $secret_key = "6LdPodIqAAAAAOfKxnW9T0BkxRX5VDdMtaX0sa_D";
    $verify_url = "https://www.google.com/recaptcha/api/siteverify";
    $response = file_get_contents($verify_url . "?secret=" . $secret_key . "&response=" . $recaptcha_response);
    $response_keys = json_decode($response, true);

    if (!$response_keys["success"]) {
        header("Location: view_event_details.php?event_id=$event_id&error=recaptcha_failed");
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $query = "SELECT email, name FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($email, $name);
    $stmt->fetch();
    $stmt->close();

    if (empty($email)) {
        $error = "Nu s-a putut găsi adresa de email a utilizatorului.";
    }

    $query_event = "SELECT event_name, event_date, event_time FROM events WHERE id = ?";
    $stmt_event = $conn->prepare($query_event);
    $stmt_event->bind_param("i", $event_id);
    $stmt_event->execute();
    $stmt_event->bind_result($event_name, $event_date, $event_time);
    $stmt_event->fetch();
    $stmt_event->close();

    // 2. Verificăm dacă utilizatorul este deja înscris
    $query = "SELECT * FROM event_registrations WHERE user_id = ? AND event_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $user_id, $event_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        header("Location: view_event_details.php?event_id=$event_id&error=already_registered");
        exit;
    }

    // 3. Inserăm înregistrarea în baza de date
    $query = "INSERT INTO event_registrations (user_id, event_id) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $user_id, $event_id);

    if ($stmt->execute()) {
        header("Location: view_event_details.php?event_id=$event_id&success=registered");

        // 4. Trimitere email confirmare
        $mail = new PHPMailer(true);

        try {
            // Configurare server SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.mailersend.net';
            $mail->SMTPAuth = true;
            $mail->Username = 'MS_LdBNGk@trial-z86org8qvqzgew13.mlsender.net';
            $mail->Password = 'mssp.1ce9Plb.0r83ql3j28zgzw1j.Me0wGMH';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Setare expeditor și destinatar
            $mail->setFrom('MS_LdBNGk@trial-z86org8qvqzgew13.mlsender.net', 'UNIBUC Events');
            $mail->addAddress($email, $name);

            // Conținut e-mail
            $mail->isHTML(true);
            $mail->Subject = 'Confirmare inscriere';
            $mail->Body    = 'Salut, ' . $name . '! <br> Te-ai inscris cu succes la evenimentul ' . $event_name . ', care va avea loc pe data de ' . $event_date . ' la ora ' . $event_time . '.';

            $mail->send();
        } catch (Exception $e) {
            $error = "E-mailul de confirmare nu a putut fi trimis. Eroare: {$mail->ErrorInfo}";
        }
    } else {
        header("Location: view_event_details.php?event_id=$event_id&error=registration_failed");
    }
    exit;
}
?>
