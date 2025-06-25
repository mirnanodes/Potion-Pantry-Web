<!-- Top Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top shadow-sm">
    <div class="container-fluid">
        <button class="btn btn-outline-secondary me-3" type="button" id="sidebarToggle">
            <span>☰</span>
        </button>
        
        <a class="navbar-brand fw-bold" href="../pages/dashboard.php">
            Potion Pantry
        </a>
        
        <div class="navbar-nav ms-auto">
            <div class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                    Welcome, <?= htmlspecialchars($_SESSION['username']) ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="../auth/logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<!-- Sidebar -->
<nav id="sidebar" class="sidebar">
    <div class="sidebar-sticky">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>" 
                   href="../pages/dashboard.php">
                    <span class="me-2">🏠</span>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'add_product.php' ? 'active' : '' ?>" 
                   href="../pages/add_product.php">
                    <span class="me-2">➕</span>
                    Add Product
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'usage_log.php' ? 'active' : '' ?>" 
                   href="../pages/usage_log.php">
                    <span class="me-2">📝</span>
                    Usage Log
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'ingredients.php' ? 'active' : '' ?>" 
                   href="../pages/ingredients.php">
                    <span class="me-2">🧪</span>
                    Ingredients
                </a>
            </li>
            <li class="nav-item mt-3">
                <hr class="sidebar-divider">
            </li>
            <li class="nav-item">
                <a class="nav-link text-danger" href="../auth/logout.php">
                    <span class="me-2">🚪</span>
                    Logout
                </a>
            </li>
        </ul>
    </div>
</nav>

<!-- Sidebar Overlay for mobile -->
<div id="sidebarOverlay" class="sidebar-overlay"></div>