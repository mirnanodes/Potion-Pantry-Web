<?php
// Application configuration

// Environment
define('ENVIRONMENT', 'development'); // development or production

// Error reporting
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
    ini_set('display_errors', 0);
}

// Application settings
define('APP_NAME', 'Potion Pantry');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/ppw-mirna-uas-potionPantry');

// Upload settings
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);

// Date/Time settings
date_default_timezone_set('Asia/Jakarta');

// Session settings
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_lifetime', 86400); // 24 hours

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>