<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: app/login.php");
    exit;
}

if ($_SESSION['user_role'] === 'organizer') {
    header("Location: app/organizer_dashboard.php");
    exit;
} elseif ($_SESSION['user_role'] === 'participant') {
    header("Location: app/participant_dashboard.php");
    exit;
} else {
    header("Location: app/login.php");
    exit;
}
?>
