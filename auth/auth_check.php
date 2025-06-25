<?php
// Authentication check - include this in protected pages

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

// Update last activity
$_SESSION['last_activity'] = time();
?>