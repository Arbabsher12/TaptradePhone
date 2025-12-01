<?php
session_start();

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$profile_picture = "../Components/noDp.png"; // Default image

if ($isLoggedIn) {
    $user_id = $_SESSION['user_id'];

    // Database connection
    include_once __DIR__ . '/../php/db.php';

    // Check if we have profile picture in session (for Google users)
    if (isset($_SESSION['user_profile_picture']) && !empty($_SESSION['user_profile_picture'])) {
        $profile_picture = $_SESSION['user_profile_picture'];
    } else {
        // Fetch user details from database
        $query = "SELECT profile_picture FROM users WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($profile_picture);
        $stmt->fetch();
        $stmt->close();
    }

    // Set default profile picture if none is found
    if (empty($profile_picture)) {
        $profile_picture = "../Components/noDp.png";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phone Marketplace</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/footer.css">
</head>
<body>
    <!-- Include Navbar -->
    <?php include 'navbar.php'; ?>

    <!-- Main Content -->
    <div class="container main-content">
        <!-- Hero Section -->
        <section class="hero-section mb-5">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">Find Your Perfect Phone</h1>
                    <p class="lead mb-4">Browse thousands of used and new phones at great prices. Buy and sell with confidence on our secure platform.</p>
                    <div class="d-flex gap-3">
                        <a href="#phone-listings" class="btn btn-primary btn-lg">Browse Phones</a>
                        <a href="sellYourPhone" class="btn btn-outline-dark btn-lg">Sell Your Phone</a>
                    </div>
                </div>
                <div class="col-lg-6 mt-4 mt-lg-0">
                    <img src="/Components/home.jpg" alt="Smartphones" class="img-fluid rounded hero-image">
                </div>
            </div>
        </section>

        <!-- Search Section -->
        <section class="search-section mb-5">
            <div class="card">
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Search for phones by name, brand, or features...">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button id="searchButton" class="btn btn-primary w-100">
                                <i class="fas fa-search me-2"></i>Search All Phones
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Phone Listings Section -->
        <section id="phone-listings" class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="section-title">Latest Phones</h2>
                <div class="d-flex gap-2">
                    <!-- View All Button -->
                    <a href="allPhones" class="btn btn-primary">
                        <i class="fas fa-eye me-2"></i>View All Phones
                    </a>
                </div>
            </div>
            
            <!-- Phone Grid -->
            <div class="row" id="phone-container">
                <!-- Phone cards will be dynamically inserted here -->
                <div class="col-12 text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3">Loading phones...</p>
                </div>
            </div>
        </section>


        <!-- Categories Section -->
        <section class="mb-5">
            <h2 class="section-title mb-4">Popular Categories</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="category-card">
                        <div class="category-icon">
                            <i class="fas fa-apple-alt"></i>
                        </div>
                        <h3>iPhones</h3>
                        <p>Browse the latest and classic Apple iPhone models at competitive prices.</p>
                        <a href="#" class="btn btn-outline-primary">View iPhones</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="category-card">
                        <div class="category-icon">
                            <i class="fab fa-android"></i>
                        </div>
                        <h3>Android Phones</h3>
                        <p>Explore a wide range of Android smartphones from top manufacturers.</p>
                        <a href="#" class="btn btn-outline-primary">View Android</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="category-card">
                        <div class="category-icon">
                            <i class="fas fa-tag"></i>
                        </div>
                        <h3>Budget Phones</h3>
                        <p>Find affordable smartphones that offer great value for your money.</p>
                        <a href="#" class="btn btn-outline-primary">View Budget</a>
                    </div>
                </div>    
            </div>
        </section>

        <!-- Features Section -->
        <section class="mb-5">
            <h2 class="section-title mb-4">Why Choose Us</h2>
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3>Secure Transactions</h3>
                        <p>Our platform ensures your payments and personal information are always protected.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h3>Verified Sellers</h3>
                        <p>All sellers on our platform are verified to ensure a safe buying experience.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-truck"></i>
                        </div>
                        <h3>Fast Shipping</h3>
                        <p>Get your purchased phones delivered quickly with our reliable shipping partners.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-undo"></i>
                        </div>
                        <h3>Easy Returns</h3>
                        <p>Not satisfied with your purchase? Return it within 7 days for a full refund.</p>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Include Footer -->
    <?php include 'footer.php'; ?>

    <!-- Bootstrap JS -->
    
    
    <!-- Bootstrap Bundle with Popper (no need for separate Popper.js) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    
    <!-- Custom JS -->
    <script src="../js/home.js"></script>
</body>
</html>