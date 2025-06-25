<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Potion Pantry - Skincare Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
        }
        
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 80vh;
            display: flex;
            align-items: center;
        }
        
        .feature-card {
            padding: 2rem;
            border-radius: 15px;
            background: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
            height: 100%;
        }
        
        .feature-icon {
            width: 60px;
            height: 60px;
            background: #667eea;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            color: white;
            font-size: 1.5rem;
        }
        
        .btn-primary {
            background: #667eea;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
        }
        
        .btn-outline-primary {
            border: 2px solid #667eea;
            color: #667eea;
            padding: 10px 30px;
            border-radius: 25px;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#" style="color: #667eea;">
                <i class="fas fa-magic me-2"></i>Potion Pantry
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item ms-2">
                        <a href="auth/login.php" class="btn btn-outline-primary btn-sm">Login</a>
                    </li>
                    <li class="nav-item ms-2">
                        <a href="auth/register.php" class="btn btn-primary btn-sm">Register</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section text-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">
                        Manage Your Skincare Products
                    </h1>
                    <p class="lead mb-4">
                        Keep track of your skincare products, check expiry dates, and organize your routine in one simple app.
                    </p>
                    <div class="d-flex gap-3 mb-4">
                        <a href="auth/register.php" class="btn btn-light btn-lg">
                            Register Free
                        </a>
                        <a href="auth/login.php" class="btn btn-outline-light btn-lg">
                            Login
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <img src="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 300'><rect width='400' height='300' fill='%23ffffff' rx='20'/><rect x='20' y='20' width='360' height='40' fill='%23667eea' rx='5'/><rect x='30' y='30' width='100' height='20' fill='%23ffffff' rx='3'/><rect x='20' y='80' width='170' height='80' fill='%23f8f9fa' rx='10'/><rect x='30' y='95' width='80' height='10' fill='%23667eea' rx='5'/><rect x='30' y='110' width='120' height='8' fill='%23dee2e6' rx='4'/><rect x='30' y='125' width='100' height='8' fill='%23dee2e6' rx='4'/><rect x='210' y='80' width='170' height='80' fill='%23e7f3ff' rx='10'/><rect x='220' y='95' width='90' height='10' fill='%23007bff' rx='5'/><rect x='220' y='110' width='130' height='8' fill='%23dee2e6' rx='4'/><rect x='220' y='125' width='110' height='8' fill='%23dee2e6' rx='4'/><rect x='20' y='180' width='360' height='80' fill='%23fff3e0' rx='10'/><rect x='30' y='195' width='70' height='10' fill='%23fd7e14' rx='5'/><rect x='30' y='210' width='150' height='8' fill='%23dee2e6' rx='4'/><rect x='30' y='225' width='120' height='8' fill='%23dee2e6' rx='4'/></svg>" 
                         alt="App Preview" class="img-fluid" style="max-width: 350px;">
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h2 class="fw-bold mb-4">Simple Skincare Management</h2>
                    <p class="lead text-muted">
                        Potion Pantry helps you organize your skincare products and routine. 
                        Add your products, track when they expire, and log your daily usage - all in one place.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold mb-3">What You Can Do</h2>
                <p class="text-muted">Everything you need to manage your skincare</p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-list"></i>
                        </div>
                        <h5>Track Products</h5>
                        <p class="text-muted">Add and organize all your skincare products in one place.</p>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <h5>Check Expiry Dates</h5>
                        <p class="text-muted">Never let your products expire again with date tracking.</p>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h5>Log Usage</h5>
                        <p class="text-muted">Keep track of when and how you use your products.</p>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-image"></i>
                        </div>
                        <h5>Add Photos</h5>
                        <p class="text-muted">Upload pictures to easily identify your products.</p>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-flask"></i>
                        </div>
                        <h5>Track Ingredients</h5>
                        <p class="text-muted">Know what ingredients are in your skincare products.</p>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h5>Use Anywhere</h5>
                        <p class="text-muted">Access your skincare data on any device, anytime.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Simple FAQ Section -->
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold mb-3">Common Questions</h2>
            </div>
            
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item mb-3">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    Is it free to use?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Yes, Potion Pantry is completely free to use with all features included.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item mb-3">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    Can I use it on my phone?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Yes, it works on phones, tablets, and computers through your web browser.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item mb-3">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    How do I add products?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Simply fill out a form with your product details like name, brand, and expiry date.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Simple CTA Section -->
    <section class="py-5 text-center" style="background: #667eea; color: white;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <h2 class="fw-bold mb-3">Ready to Organize Your Skincare?</h2>
                    <p class="mb-4">Create your free account and start managing your products today.</p>
                    <a href="auth/register.php" class="btn btn-light btn-lg me-3">Create Account</a>
                    <a href="auth/login.php" class="btn btn-outline-light btn-lg">Login</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Simple Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h6><i class="fas fa-magic me-2"></i>Potion Pantry</h6>
                    <p class="text-muted small">Simple skincare management for everyone.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="auth/login.php" class="text-muted text-decoration-none me-3">Login</a>
                    <a href="auth/register.php" class="text-muted text-decoration-none">Register</a>
                </div>
            </div>
            <hr class="my-3 opacity-25">
            <div class="text-center">
                <p class="mb-0 text-muted small">&copy; 2024 Potion Pantry</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>