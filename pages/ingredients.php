<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../auth/auth_check.php';

// Get user's ingredients with product counts
$stmt = getDB()->prepare("
    SELECT 
        i.ingredient_id,
        i.ingredient_name,
        COUNT(DISTINCT p.product_id) as product_count,
        GROUP_CONCAT(DISTINCT CONCAT(p.product_name, ' (', p.brand, ')') ORDER BY p.product_name SEPARATOR '|') as products
    FROM Ingredients i
    LEFT JOIN ProductIngredients pi ON i.ingredient_id = pi.ingredient_id
    LEFT JOIN Products p ON pi.product_id = p.product_id AND p.user_id = ?
    GROUP BY i.ingredient_id, i.ingredient_name
    HAVING product_count > 0
    ORDER BY product_count DESC, i.ingredient_name
");
$stmt->execute([$_SESSION['user_id']]);
$user_ingredients = $stmt->fetchAll();

// Get all ingredients for reference
$all_ingredients = getAllIngredients();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingredients - Potion Pantry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <main class="col-12 main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Ingredients Library</h1>
                    <a href="dashboard.php" class="btn btn-outline-secondary">Back to Dashboard</a>
                </div>

                <!-- Search Bar -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" class="form-control" id="searchIngredients" 
                                       placeholder="Search ingredients..." onkeyup="filterIngredients()">
                            </div>
                            <div class="col-md-6">
                                <select class="form-select" id="sortIngredients" onchange="sortIngredients()">
                                    <option value="count">Sort by Product Count</option>
                                    <option value="name">Sort by Name (A-Z)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User's Ingredients -->
                <?php if (empty($user_ingredients)): ?>
                    <div class="text-center py-5">
                        <h3 class="text-muted">No ingredients found</h3>
                        <p class="text-muted">Add products with ingredients to see them here!</p>
                        <a href="add_product.php" class="btn btn-primary">Add Your First Product</a>
                    </div>
                <?php else: ?>
                    <div class="row" id="ingredientsContainer">
                        <?php foreach ($user_ingredients as $ingredient): ?>
                            <div class="col-md-6 col-lg-4 mb-4 ingredient-card" 
                                 data-name="<?= strtolower($ingredient['ingredient_name']) ?>"
                                 data-count="<?= $ingredient['product_count'] ?>">
                                <div class="card h-100 hover-lift">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <h5 class="card-title"><?= htmlspecialchars($ingredient['ingredient_name']) ?></h5>
                                            <span class="badge bg-primary"><?= $ingredient['product_count'] ?> product(s)</span>
                                        </div>
                                        
                                        <?php if ($ingredient['products']): ?>
                                            <h6 class="text-muted small mb-2">Found in:</h6>
                                            <div class="products-list">
                                                <?php 
                                                $products = explode('|', $ingredient['products']);
                                                foreach ($products as $product): 
                                                ?>
                                                    <div class="badge bg-light text-dark me-1 mb-1" style="font-size: 0.8rem;">
                                                        <?= htmlspecialchars($product) ?>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <!-- Ingredient Info (you can add more details here) -->
                                        <div class="mt-3">
                                            <button class="btn btn-outline-info btn-sm" 
                                                    onclick="showIngredientInfo('<?= addslashes($ingredient['ingredient_name']) ?>')">
                                                Learn More
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- All Ingredients Reference -->
                <div class="card mt-5">
                    <div class="card-header">
                        <h5 class="mb-0">🧪 Complete Ingredients Reference</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Common skincare ingredients available in our database:</p>
                        <div class="row">
                            <?php foreach (array_chunk($all_ingredients, ceil(count($all_ingredients) / 3)) as $column): ?>
                                <div class="col-md-4">
                                    <?php foreach ($column as $ingredient): ?>
                                        <div class="d-flex justify-content-between align-items-center py-1">
                                            <span class="small"><?= htmlspecialchars($ingredient['ingredient_name']) ?></span>
                                            <?php
                                            // Check if user has this ingredient
                                            $has_ingredient = false;
                                            foreach ($user_ingredients as $user_ing) {
                                                if ($user_ing['ingredient_id'] == $ingredient['ingredient_id']) {
                                                    $has_ingredient = true;
                                                    break;
                                                }
                                            }
                                            ?>
                                            <?php if ($has_ingredient): ?>
                                                <span class="badge bg-success">✓</span>
                                            <?php else: ?>
                                                <span class="badge bg-light text-muted">-</span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Ingredient Info Modal -->
    <div class="modal fade" id="ingredientModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ingredientModalTitle">Ingredient Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="ingredientModalBody">
                    <!-- Ingredient info will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/app.js"></script>
    <script>
        function filterIngredients() {
            const searchTerm = document.getElementById('searchIngredients').value.toLowerCase();
            const cards = document.querySelectorAll('.ingredient-card');
            
            cards.forEach(card => {
                const name = card.getAttribute('data-name');
                if (name.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function sortIngredients() {
            const sortBy = document.getElementById('sortIngredients').value;
            const container = document.getElementById('ingredientsContainer');
            const cards = Array.from(document.querySelectorAll('.ingredient-card'));
            
            cards.sort((a, b) => {
                if (sortBy === 'count') {
                    return parseInt(b.getAttribute('data-count')) - parseInt(a.getAttribute('data-count'));
                } else {
                    return a.getAttribute('data-name').localeCompare(b.getAttribute('data-name'));
                }
            });
            
            // Re-append sorted cards
            cards.forEach(card => container.appendChild(card));
        }

        function showIngredientInfo(ingredientName) {
            // Simple ingredient info - you can expand this with real data
            const ingredientInfo = {
                'Hyaluronic Acid': 'A powerful humectant that can hold up to 1000 times its weight in water. Great for hydration.',
                'Niacinamide': 'Also known as Vitamin B3, helps minimize pores, regulate oil production, and improve skin texture.',
                'Salicylic Acid': 'A beta hydroxy acid (BHA) that exfoliates inside pores, helping to treat acne and blackheads.',
                'Vitamin C': 'An antioxidant that brightens skin, fades dark spots, and protects against environmental damage.',
                'Retinol': 'A form of Vitamin A that speeds up cell turnover, reduces fine lines, and improves skin texture.',
                'Ceramides': 'Lipids that help restore and maintain skin barrier function, keeping moisture in.',
                'Glycerin': 'A humectant that draws moisture from the air to hydrate the skin.',
                'Centella Asiatica Extract': 'A soothing plant extract that calms inflammation and promotes healing.',
                'Rose Water': 'A gentle astringent and toner that helps balance skin pH and provides light hydration.',
                'Tea Tree Oil': 'An antimicrobial essential oil that helps fight acne-causing bacteria.'
            };
            
            const info = ingredientInfo[ingredientName] || 'No detailed information available for this ingredient yet. Consult with a dermatologist for specific advice.';
            
            document.getElementById('ingredientModalTitle').textContent = ingredientName;
            document.getElementById('ingredientModalBody').innerHTML = `
                <p><strong>About ${ingredientName}:</strong></p>
                <p>${info}</p>
                <div class="alert alert-info">
                    <small><strong>Note:</strong> This is general information. Always patch test new products and consult with a dermatologist for personalized advice.</small>
                </div>
            `;
            
            const modal = new bootstrap.Modal(document.getElementById('ingredientModal'));
            modal.show();
        }
    </script>
</body>
</html>