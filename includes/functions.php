<?php
// Helper Functions for Potion Pantry

/**
 * Get user statistics for dashboard
 */
function getUserStats($user_id) {
    $db = getDB();
    
    // Total products
    $stmt = $db->prepare("SELECT COUNT(*) FROM Products WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $total_products = $stmt->fetchColumn();
    
    // Total value
    $stmt = $db->prepare("SELECT COALESCE(SUM(price), 0) FROM Products WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $total_value = $stmt->fetchColumn();
    
    // Products used today
    $stmt = $db->prepare("
        SELECT COUNT(DISTINCT ul.product_id) 
        FROM UsageLog ul 
        JOIN Products p ON ul.product_id = p.product_id 
        WHERE p.user_id = ? AND DATE(ul.usage_date) = CURDATE()
    ");
    $stmt->execute([$user_id]);
    $used_today = $stmt->fetchColumn();
    
    // Expiring products (within 180 days)
    $stmt = $db->prepare("
        SELECT COUNT(*) 
        FROM Products 
        WHERE user_id = ? 
        AND expiration_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 180 DAY)
    ");
    $stmt->execute([$user_id]);
    $expiring_soon = $stmt->fetchColumn();
    
    return [
        'total_products' => $total_products,
        'total_value' => $total_value,
        'used_today' => $used_today,
        'expiring_soon' => $expiring_soon
    ];
}

/**
 * Calculate days until expiration
 */
function calculateDaysUntilExpiration($expiration_date) {
    $today = new DateTime();
    $expiry = new DateTime($expiration_date);
    $diff = $expiry->diff($today);
    
    if ($expiry < $today) {
        return -$diff->days; // Negative for expired products
    }
    
    return $diff->days;
}

/**
 * Get expiry status badge class
 */
function getExpiryBadgeClass($expiration_date) {
    $days = calculateDaysUntilExpiration($expiration_date);
    
    if ($days < 0) {
        return 'bg-danger'; // Expired
    } elseif ($days <= 30) {
        return 'bg-warning'; // Expires soon
    } elseif ($days <= 180) {
        return 'bg-info'; // Monitor
    } else {
        return 'bg-success'; // Fresh
    }
}

/**
 * Get product ingredients
 */
function getProductIngredients($product_id) {
    $db = getDB();
    
    $stmt = $db->prepare("
        SELECT i.ingredient_name 
        FROM ProductIngredients pi 
        JOIN Ingredients i ON pi.ingredient_id = i.ingredient_id 
        WHERE pi.product_id = ?
        ORDER BY i.ingredient_name
    ");
    $stmt->execute([$product_id]);
    
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

/**
 * Get all ingredients
 */
function getAllIngredients() {
    $db = getDB();
    
    $stmt = $db->query("SELECT * FROM Ingredients ORDER BY ingredient_name");
    return $stmt->fetchAll();
}

/**
 * Sanitize input
 */
function sanitizeInput($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Hash password
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verify password
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Check if user owns product
 */
function userOwnsProduct($user_id, $product_id) {
    $db = getDB();
    
    $stmt = $db->prepare("SELECT COUNT(*) FROM Products WHERE product_id = ? AND user_id = ?");
    $stmt->execute([$product_id, $user_id]);
    
    return $stmt->fetchColumn() > 0;
}

/**
 * Upload product image
 */
function uploadProductImage($file) {
    $upload_dir = 'assets/images/uploads/';
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    // Create upload directory if it doesn't exist
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Validate file
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Upload failed');
    }
    
    if ($file['size'] > $max_size) {
        throw new Exception('File too large (max 5MB)');
    }
    
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mime_type, $allowed_types)) {
        throw new Exception('Invalid file type');
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('product_') . '.' . $extension;
    $filepath = $upload_dir . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        throw new Exception('Failed to save file');
    }
    
    return $filename;
}

/**
 * Delete product image
 */
function deleteProductImage($filename) {
    if ($filename && file_exists('assets/images/uploads/' . $filename)) {
        unlink('assets/images/uploads/' . $filename);
    }
}

/**
 * Format price for display
 */
function formatPrice($price) {
    return 'Rp' . number_format($price, 0, ',', '.');
}

/**
 * Format date for display
 */
function formatDate($date) {
    return date('M j, Y', strtotime($date));
}

/**
 * Redirect with message
 */
function redirect($url, $message = '', $type = 'success') {
    if ($message) {
        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_type'] = $type;
    }
    header("Location: $url");
    exit();
}

/**
 * Display flash message
 */
function displayFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'success';
        
        $alert_class = $type === 'error' ? 'alert-danger' : 'alert-success';
        
        echo "<div class='alert $alert_class alert-dismissible fade show' role='alert'>";
        echo htmlspecialchars($message);
        echo "<button type='button' class='btn-close' data-bs-dismiss='alert'></button>";
        echo "</div>";
        
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
    }
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Get user's recent usage logs
 */
function getUserUsageLogs($user_id, $limit = 20) {
    $db = getDB();
    
    $stmt = $db->prepare("
        SELECT ul.*, p.product_name, p.brand, p.product_type
        FROM UsageLog ul
        JOIN Products p ON ul.product_id = p.product_id
        WHERE p.user_id = ?
        ORDER BY ul.usage_date DESC, ul.created_at DESC
        LIMIT ?
    ");
    $stmt->execute([$user_id, $limit]);
    
    return $stmt->fetchAll();
}

/**
 * Get expiring products for user
 */
function getExpiringProducts($user_id, $days = 180) {
    $db = getDB();
    
    $stmt = $db->prepare("
        SELECT *, DATEDIFF(expiration_date, CURDATE()) as days_until_expiration
        FROM Products 
        WHERE user_id = ? 
        AND expiration_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)
        ORDER BY expiration_date ASC
    ");
    $stmt->execute([$user_id, $days]);
    
    return $stmt->fetchAll();
}
?>