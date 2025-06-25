<?php
// Database configuration for Potion Pantry
define('DB_HOST', 'localhost');
define('DB_NAME', 'u985354573_potionPantry');
define('DB_USER', 'u985354573_ayu_mirna');
define('DB_PASS', 'Miwa200531');

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    echo("ganteng");
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Function to get database connection
function getDB() {
    global $pdo;
    return $pdo;
}
?>