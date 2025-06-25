<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../auth/auth_check.php';

// Get user's products
$stmt = getDB()->prepare("SELECT * FROM Products WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$products = $stmt->fetchAll();

// Get statsa
$stats = getUserStats($_SESSION['user_id']);

// Get expiring products
$expiring_products = getExpiringProducts($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Potion Pantry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <main class="col-12 main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Your Skincare Collection</h1>
                    <a href="add_product.php" class="btn btn-primary">Add New Product</a>
                </div>

                <?php displayFlashMessage(); ?>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card bg-primary text-white stats-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <span style="font-size: 2rem;">📦</span>
                                    </div>
                                    <div>
                                        <h6 class="card-title mb-0">Total Products</h6>
                                        <h3 class="mb-0"><?= $stats['total_products'] ?></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <div class="card bg-warning text-white stats-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <span style="font-size: 2rem;">⚠️</span>
                                    </div>
                                    <div>
                                        <h6 class="card-title mb-0">Expiring Soon</h6>
                                        <h3 class="mb-0"><?= $stats['expiring_soon'] ?></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <div class="card bg-success text-white stats-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <span style="font-size: 2rem;">💰</span>
                                    </div>
                                    <div>
                                        <h6 class="card-title mb-0">Total Value</h6>
                                        <h3 class="mb-0"><?= formatPrice($stats['total_value']) ?></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <div class="card bg-info text-white stats-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <span style="font-size: 2rem;">📈</span>
                                    </div>
                                    <div>
                                        <h6 class="card-title mb-0">Used Today</h6>
                                        <h3 class="mb-0"><?= $stats['used_today'] ?></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Expiring Products Alert -->
                <?php if (!empty($expiring_products)): ?>
                <div class="alert alert-warning mb-4">
                    <h5 class="alert-heading">
                        <span class="me-2">⚠️</span>
                        Products About to Expire
                    </h5>
                    <?php foreach ($expiring_products as $product): ?>
                        <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                            <div>
                                <strong><?= htmlspecialchars($product['product_name']) ?></strong>
                                <small class="text-muted d-block"><?= htmlspecialchars($product['brand']) ?></small>
                            </div>
                            <div class="text-end">
                                <span class="badge <?= getExpiryBadgeClass($product['expiration_date']) ?>">
                                    <?= $product['days_until_expiration'] ?> days
                                </span>
                                <small class="text-muted d-block"><?= formatDate($product['expiration_date']) ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Products Grid -->
                <?php if (empty($products)): ?>
                    <div class="text-center py-5">
                        <h3 class="text-muted">No products yet</h3>
                        <p class="text-muted">Start building your skincare collection!</p>
                        <a href="add_product.php" class="btn btn-primary">Add Your First Product</a>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($products as $product): ?>
                            <?php 
                                $days_until_expiration = calculateDaysUntilExpiration($product['expiration_date']);
                                $ingredients = getProductIngredients($product['product_id']);
                            ?>
                            <div class="col-md-4 col-lg-3 mb-4">
                                <div class="card h-100 product-card" data-product-id="<?= $product['product_id'] ?>">
                                    <?php if ($product['image_path']): ?>
                                        <img src="../assets/images/uploads/<?= htmlspecialchars($product['image_path']) ?>" 
                                             class="card-img-top" style="height: 200px; object-fit: cover;" 
                                             alt="<?= htmlspecialchars($product['product_name']) ?>">
                                    <?php else: ?>
                                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                             style="height: 200px; background: linear-gradient(135deg, #f8bbd9 0%, #e1bee7 100%);">
                                            <span style="font-size: 4rem;">🧴</span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($product['product_name']) ?></h5>
                                        <p class="card-text text-muted"><?= htmlspecialchars($product['brand']) ?></p>
                                        
                                        <div class="mb-2">
                                            <span class="badge bg-primary"><?= htmlspecialchars($product['product_type']) ?></span>
                                            <?php if ($product['last_used_at']): ?>
                                                <span class="badge bg-success ms-1">
                                                    Last used: <?= date('M j', strtotime($product['last_used_at'])) ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="small text-muted mb-2">
                                            <div class="d-flex justify-content-between">
                                                <span>Volume:</span>
                                                <span><?= $product['volume_ml'] ?>ml</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span>Price:</span>
                                                <span><?= formatPrice($product['price']) ?></span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span>Expires:</span>
                                                <span class="<?= $days_until_expiration <= 30 ? 'text-danger fw-bold' : ($days_until_expiration <= 180 ? 'text-warning fw-bold' : '') ?>">
                                                    <?= formatDate($product['expiration_date']) ?>
                                                </span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span>Days left:</span>
                                                <span class="<?= $days_until_expiration <= 30 ? 'text-danger fw-bold' : ($days_until_expiration <= 180 ? 'text-warning fw-bold' : 'text-success') ?>">
                                                    <?= $days_until_expiration ?> days
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <?php if (!empty($ingredients)): ?>
                                        <div class="mb-2">
                                            <small class="text-muted">Ingredients:</small>
                                            <div>
                                                <?php foreach (array_slice($ingredients, 0, 3) as $ingredient): ?>
                                                    <span class="ingredient-tag"><?= htmlspecialchars($ingredient) ?></span>
                                                <?php endforeach; ?>
                                                <?php if (count($ingredients) > 3): ?>
                                                    <small class="text-muted">+<?= count($ingredients) - 3 ?> more</small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="card-footer bg-transparent">
                                        <div class="btn-group w-100" role="group">
                                            <button class="btn btn-success btn-sm" onclick="logUsage(<?= $product['product_id'] ?>)">
                                                Log Usage
                                            </button>
                                            <button class="btn btn-outline-primary btn-sm" onclick="editProduct(<?= $product['product_id'] ?>)">
                                                Edit
                                            </button>
                                            <button class="btn btn-outline-danger btn-sm" onclick="deleteProduct(<?= $product['product_id'] ?>)">
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/app.js"></script>
</body>
</html>