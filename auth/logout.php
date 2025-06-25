<?php
session_start();

// Destroy all session data
session_destroy();

// Redirect to login with logout message
header('Location: login.php?logout=1');
exit();
?>