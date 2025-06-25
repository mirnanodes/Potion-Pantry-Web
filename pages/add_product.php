<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../auth/auth_check.php';

$error = '';
$success = '';

// Get all ingredients for selection
$ingredients = getAllIngredients();

if ($_POST) {
    $product_name = trim($_POST['product_name']);
    $brand = trim($_POST['brand']);
    $product_type = trim($_POST['product_type']);
    $purchase_date = $_POST['purchase_date'];
    $expiration_date = $_POST['expiration_date'];
    $price = floatval($_POST['price']);
    $volume_ml = intval($_POST['volume_ml']);
    $notes = trim($_POST['notes']);
    $selected_ingredients = $_POST['ingredients'] ?? [];
    
    // Validation
    if (empty($product_name) || empty($brand) || empty($product_type) || 
        empty($purchase_date) || empty($expiration_date) || $price <= 0 || $volume_ml <= 0) {
        $error = 'Please fill all required fields';
    } elseif (strtotime($expiration_date) <= strtotime($purchase_date)) {
        $error = 'Expiration date must be after purchase date';
    } else {
        try {
            $db = getDB();
            $db->beginTransaction();
            
            // Handle image upload
            $image_path = '';
            if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
                try {
                    $image_path = uploadProductImage($_FILES['product_image']);
                } catch (Exception $e) {
                    $error = 'Image upload failed: ' . $e->getMessage();
                    $db->rollBack();
                }
            }
            
            if (empty($error)) {
                // Insert product
                $stmt = $db->prepare("
                    INSERT INTO Products (user_id, product_name, brand, product_type, purchase_date, 
                                        expiration_date, price, volume_ml, notes, image_path) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $_SESSION['user_id'], $product_name, $brand, $product_type,
                    $purchase_date, $expiration_date, $price, $volume_ml, $notes, $image_path
                ]);
                
                $product_id = $db->lastInsertId();
                
                // Insert product ingredients
                if (!empty($selected_ingredients)) {
                    $stmt = $db->prepare("INSERT INTO ProductIngredients (product_id, ingredient_id) VALUES (?, ?)");
                    foreach ($selected_ingredients as $ingredient_id) {
                        $stmt->execute([$product_id, $ingredient_id]);
                    }
                }
                
                $db->commit();
                redirect('dashboard.php', 'Product added successfully!');
            }
        } catch (Exception $e) {
            $db->rollBack();
            $error = 'Failed to add product. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Potion Pantry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <main class="col-12 main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Add New Product</h1>
                    <a href="dashboard.php" class="btn btn-outline-secondary">Back to Dashboard</a>
                </div>

                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6">
                        <div class="card shadow">
                            <div class="card-body p-4">
                                <div class="text-center mb-4">
                                    <h3>Add New Product</h3>
                                    <p class="text-muted">Fill in your skincare product details</p>
                                </div>
                                
                                <?php if ($error): ?>
                                    <div class="alert alert-danger">
                                        <?= htmlspecialchars($error) ?>
                                    </div>
                                <?php endif; ?>
                                
                                <form method="POST" enctype="multipart/form-data">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="product_name" class="form-label">Product Name *</label>
                                            <input type="text" class="form-control" id="product_name" name="product_name" required 
                                                   value="<?= htmlspecialchars($_POST['product_name'] ?? '') ?>"
                                                   placeholder="e.g., Hyaluronic Acid Serum">
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="brand" class="form-label">Brand *</label>
                                            <input type="text" class="form-control" id="brand" name="brand" required 
                                                   value="<?= htmlspecialchars($_POST['brand'] ?? '') ?>"
                                                   placeholder="e.g., SOMETHINC">
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="product_type" class="form-label">Product Type *</label>
                                            <select class="form-select" id="product_type" name="product_type" required>
                                                <option value="">Select type</option>
                                                <option value="Cleanser" <?= ($_POST['product_type'] ?? '') === 'Cleanser' ? 'selected' : '' ?>>Cleanser</option>
                                                <option value="Toner" <?= ($_POST['product_type'] ?? '') === 'Toner' ? 'selected' : '' ?>>Toner</option>
                                                <option value="Serum" <?= ($_POST['product_type'] ?? '') === 'Serum' ? 'selected' : '' ?>>Serum</option>
                                                <option value="Moisturizer" <?= ($_POST['product_type'] ?? '') === 'Moisturizer' ? 'selected' : '' ?>>Moisturizer</option>
                                                <option value="Sunscreen" <?= ($_POST['product_type'] ?? '') === 'Sunscreen' ? 'selected' : '' ?>>Sunscreen</option>
                                                <option value="Treatment" <?= ($_POST['product_type'] ?? '') === 'Treatment' ? 'selected' : '' ?>>Treatment</option>
                                                <option value="Mask" <?= ($_POST['product_type'] ?? '') === 'Mask' ? 'selected' : '' ?>>Mask</option>
                                                <option value="Eye Care" <?= ($_POST['product_type'] ?? '') === 'Eye Care' ? 'selected' : '' ?>>Eye Care</option>
                                            </select>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="volume_ml" class="form-label">Volume (ml) *</label>
                                            <input type="number" class="form-control" id="volume_ml" name="volume_ml" required min="1"
                                                   value="<?= htmlspecialchars($_POST['volume_ml'] ?? '') ?>"
                                                   placeholder="e.g., 30">
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="purchase_date" class="form-label">Purchase Date *</label>
                                            <input type="date" class="form-control" id="purchase_date" name="purchase_date" required
                                                   value="<?= htmlspecialchars($_POST['purchase_date'] ?? '') ?>">
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="expiration_date" class="form-label">Expiration Date *</label>
                                            <input type="date" class="form-control" id="expiration_date" name="expiration_date" required
                                                   value="<?= htmlspecialchars($_POST['expiration_date'] ?? '') ?>">
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="price" class="form-label">Price (IDR) *</label>
                                            <input type="number" class="form-control" id="price" name="price" required min="1"
                                                   value="<?= htmlspecialchars($_POST['price'] ?? '') ?>"
                                                   placeholder="e.g., 85000">
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="product_image" class="form-label">Product Image</label>
                                            <input type="file" class="form-control" id="product_image" name="product_image" 
                                                   accept="image/*">
                                            <small class="text-muted">Max 5MB (JPG, PNG, GIF, WebP)</small>
                                        </div>
                                    </div>

                                    <!-- Ingredients Selection -->
                                    <div class="mb-3">
                                        <label class="form-label">Ingredients</label>
                                        <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                                            <div class="row">
                                                <?php foreach ($ingredients as $ingredient): ?>
                                                <div class="col-md-6 col-lg-4 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" 
                                                               name="ingredients[]" value="<?= $ingredient['ingredient_id'] ?>"
                                                               id="ingredient_<?= $ingredient['ingredient_id'] ?>"
                                                               <?= in_array($ingredient['ingredient_id'], $_POST['ingredients'] ?? []) ? 'checked' : '' ?>>
                                                        <label class="form-check-label small" for="ingredient_<?= $ingredient['ingredient_id'] ?>">
                                                            <?= htmlspecialchars($ingredient['ingredient_name']) ?>
                                                        </label>
                                                    </div>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="notes" class="form-label">Notes</label>
                                        <textarea class="form-control" id="notes" name="notes" rows="3"
                                                  placeholder="Any additional notes about this product..."><?= htmlspecialchars($_POST['notes'] ?? '') ?></textarea>
                                    </div>
                                    
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <a href="dashboard.php" class="btn btn-secondary me-md-2">Cancel</a>
                                        <button type="submit" class="btn btn-primary">Add Product</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/app.js"></script>
    <script>
        // Set default dates
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            const futureDate = new Date();
            futureDate.setFullYear(futureDate.getFullYear() + 2);
            const future = futureDate.toISOString().split('T')[0];
            
            if (!document.getElementById('purchase_date').value) {
                document.getElementById('purchase_date').value = today;
            }
            if (!document.getElementById('expiration_date').value) {
                document.getElementById('expiration_date').value = future;
            }
        });
    </script>
</body>
</html>