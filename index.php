<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: public/login.php");
    exit;
}

if ($_SESSION['user_role'] === 'organizer') {
    header("Location: public/organizer_dashboard.php");
    exit;
} elseif ($_SESSION['user_role'] === 'participant') {
    header("Location: public/participant_dashboard.php");
    exit;
} else {
    header("Location: public/login.php");
    exit;
}
?>
