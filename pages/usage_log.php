<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../auth/auth_check.php';

// Get user's products for quick usage dropdown
$stmt = getDB()->prepare("SELECT product_id, product_name, brand FROM Products WHERE user_id = ? ORDER BY product_name");
$stmt->execute([$_SESSION['user_id']]);
$user_products = $stmt->fetchAll();

// Get usage history
$usage_logs = getUserUsageLogs($_SESSION['user_id'], 50);

$success = '';
$error = '';

// Handle quick usage logging
if ($_POST && isset($_POST['quick_usage'])) {
    $product_id = intval($_POST['product_id']);
    $time_of_day = $_POST['time_of_day'];
    $notes = trim($_POST['notes'] ?? '');
    
    if ($product_id && $time_of_day && userOwnsProduct($_SESSION['user_id'], $product_id)) {
        try {
            $stmt = getDB()->prepare("
                INSERT INTO UsageLog (product_id, usage_date, time_of_day, notes) 
                VALUES (?, CURDATE(), ?, ?)
            ");
            $stmt->execute([$product_id, $time_of_day, $notes]);
            
            $success = 'Usage logged successfully!';
            
            // Refresh usage logs
            $usage_logs = getUserUsageLogs($_SESSION['user_id'], 50);
        } catch (Exception $e) {
            $error = 'Failed to log usage. Please try again.';
        }
    } else {
        $error = 'Invalid product selection.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usage Log - Potion Pantry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <main class="col-12 main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Usage Log</h1>
                    <a href="dashboard.php" class="btn btn-outline-secondary">Back to Dashboard</a>
                </div>

                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?= htmlspecialchars($success) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Quick Usage Log -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">📝 Log Product Usage</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="quick_usage" value="1">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="product_id" class="form-label">Select Product</label>
                                    <select class="form-select" id="product_id" name="product_id" required>
                                        <option value="">Choose a product...</option>
                                        <?php foreach ($user_products as $product): ?>
                                            <option value="<?= $product['product_id'] ?>">
                                                <?= htmlspecialchars($product['product_name']) ?> - <?= htmlspecialchars($product['brand']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="col-md-3">
                                    <label for="time_of_day" class="form-label">Time of Day</label>
                                    <select class="form-select" id="time_of_day" name="time_of_day" required>
                                        <option value="Morning">Morning</option>
                                        <option value="Evening">Evening</option>
                                        <option value="Both">Both</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-3">
                                    <label for="notes" class="form-label">Notes (Optional)</label>
                                    <input type="text" class="form-control" id="notes" name="notes" 
                                           placeholder="How did it work?">
                                </div>
                                
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="submit" class="btn btn-primary w-100">Log Usage</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Usage History -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">📈 Recent Usage History</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($usage_logs)): ?>
                            <div class="text-center py-4">
                                <h6 class="text-muted">No usage history yet</h6>
                                <p class="text-muted">Start logging your skincare routine above!</p>
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach ($usage_logs as $log): ?>
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="card border-start border-primary border-3">
                                            <div class="card-body p-3">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h6 class="card-title mb-0"><?= htmlspecialchars($log['product_name']) ?></h6>
                                                    <span class="badge <?= $log['time_of_day'] === 'Morning' ? 'bg-warning' : ($log['time_of_day'] === 'Evening' ? 'bg-info' : 'bg-secondary') ?>">
                                                        <?= $log['time_of_day'] ?>
                                                    </span>
                                                </div>
                                                
                                                <p class="card-text text-muted small mb-1">
                                                    <?= htmlspecialchars($log['brand']) ?> • <?= htmlspecialchars($log['product_type']) ?>
                                                </p>
                                                
                                                <p class="card-text text-muted small mb-2">
                                                    <strong><?= formatDate($log['usage_date']) ?></strong>
                                                </p>
                                                
                                                <?php if ($log['notes']): ?>
                                                    <p class="card-text small">
                                                        "<?= htmlspecialchars($log['notes']) ?>"
                                                    </p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/app.js"></script>
</body>
</html>