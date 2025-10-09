<?php
session_start();

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$profile_picture = "../Components/noDp.png"; // Default image

if ($isLoggedIn) {
    $user_id = $_SESSION['user_id'];

    // Database connection
    include_once __DIR__ . '/../db.php';

    // Fetch user details 
    $query = "SELECT profile_picture FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($profile_picture);
    $stmt->fetch();
    $stmt->close();

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
    <title>All Phones - Phone Marketplace</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/allPhones.css">
</head>
<body>
    <!-- Include Navbar -->
    <?php include 'navbar.php'; ?>

    <!-- Main Content -->
    <div class="container main-content">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title">All Phones</h1>
                        <p class="text-muted">Browse all available phones with advanced search and filtering</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="home" class="btn btn-outline-primary">
                            <i class="fas fa-home me-2"></i>Back to Home
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filter Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row g-3">
                            <!-- Search Bar -->
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="fas fa-search text-muted"></i>
                                    </span>
                                    <input type="text" id="searchInput" class="form-control border-start-0" 
                                           placeholder="Search phones by name, brand, or features..." 
                                           value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                                </div>
                            </div>
                            
                            <!-- Filter Button -->
                            <div class="col-md-3">
                                <button id="filterButton" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-filter me-2"></i>Filter
                                </button>
                            </div>
                            
                            <!-- Sort Dropdown -->
                            <div class="col-md-3">
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary dropdown-toggle w-100" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-sort me-2"></i>Sort by: 
                                        <span id="sortText">Newest</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end w-100" aria-labelledby="sortDropdown">
                                        <li><a class="dropdown-item" href="#" data-sort="newest">Newest</a></li>
                                        <li><a class="dropdown-item" href="#" data-sort="oldest">Oldest</a></li>
                                        <li><a class="dropdown-item" href="#" data-sort="price_low">Price: Low to High</a></li>
                                        <li><a class="dropdown-item" href="#" data-sort="price_high">Price: High to Low</a></li>
                                        <li><a class="dropdown-item" href="#" data-sort="popularity">Popularity</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Active Filters Display -->
                        <div id="activeFilters" class="mt-3" style="display: none;">
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge bg-primary">Active Filters:</span>
                                <div id="filterTags"></div>
                                <button id="clearAllFilters" class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-times me-1"></i>Clear All
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Results Section -->
        <div class="row">
            <!-- Phone Grid -->
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 id="resultsTitle">All Phones</h3>
                    <div class="d-flex align-items-center gap-3">
                        <span id="resultsCount" class="text-muted">Loading...</span>
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="viewMode" id="gridView" autocomplete="off" checked>
                            <label class="btn btn-outline-secondary" for="gridView">
                                <i class="fas fa-th"></i>
                            </label>
                            <input type="radio" class="btn-check" name="viewMode" id="listView" autocomplete="off">
                            <label class="btn btn-outline-secondary" for="listView">
                                <i class="fas fa-list"></i>
                            </label>
                        </div>
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
                
                <!-- Load More Button -->
                <div class="text-center mt-4" id="loadMoreContainer" style="display: none;">
                    <button id="loadMoreBtn" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Load More Phones
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">Filter Phones</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Price Range Filter -->
                        <div class="col-md-6">
                            <div class="filter-section">
                                <h5>Price Range</h5>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="priceRange1" value="0-200">
                                    <label class="form-check-label" for="priceRange1">
                                        Under $200
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="priceRange2" value="200-500">
                                    <label class="form-check-label" for="priceRange2">
                                        $200 - $500
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="priceRange3" value="500-800">
                                    <label class="form-check-label" for="priceRange3">
                                        $500 - $800
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="priceRange4" value="800-1200">
                                    <label class="form-check-label" for="priceRange4">
                                        $800 - $1200
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="priceRange5" value="1200-999999">
                                    <label class="form-check-label" for="priceRange5">
                                        $1200+
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Brand Filter -->
                        <div class="col-md-6">
                            <div class="filter-section">
                                <h5>Brand</h5>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="brandApple" value="Apple">
                                    <label class="form-check-label" for="brandApple">
                                        Apple
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="brandSamsung" value="Samsung">
                                    <label class="form-check-label" for="brandSamsung">
                                        Samsung
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="brandXiaomi" value="Xiaomi">
                                    <label class="form-check-label" for="brandXiaomi">
                                        Xiaomi
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="brandGoogle" value="Google">
                                    <label class="form-check-label" for="brandGoogle">
                                        Google
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="brandOnePlus" value="OnePlus">
                                    <label class="form-check-label" for="brandOnePlus">
                                        OnePlus
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="brandHuawei" value="Huawei">
                                    <label class="form-check-label" for="brandHuawei">
                                        Huawei
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <!-- Condition Filter -->
                        <div class="col-md-6">
                            <div class="filter-section">
                                <h5>Condition</h5>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="conditionNew" value="new">
                                    <label class="form-check-label" for="conditionNew">
                                        New
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="conditionLikeNew" value="like_new">
                                    <label class="form-check-label" for="conditionLikeNew">
                                        Like New
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="conditionExcellent" value="excellent">
                                    <label class="form-check-label" for="conditionExcellent">
                                        Excellent
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="conditionGood" value="good">
                                    <label class="form-check-label" for="conditionGood">
                                        Good
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="conditionFair" value="fair">
                                    <label class="form-check-label" for="conditionFair">
                                        Fair
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Storage Filter -->
                        <div class="col-md-6">
                            <div class="filter-section">
                                <h5>Storage</h5>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="storage32" value="32GB">
                                    <label class="form-check-label" for="storage32">
                                        32GB
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="storage64" value="64GB">
                                    <label class="form-check-label" for="storage64">
                                        64GB
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="storage128" value="128GB">
                                    <label class="form-check-label" for="storage128">
                                        128GB
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="storage256" value="256GB">
                                    <label class="form-check-label" for="storage256">
                                        256GB
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="storage512" value="512GB">
                                    <label class="form-check-label" for="storage512">
                                        512GB+
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="clearFilters">Clear All</button>
                    <button type="button" class="btn btn-primary" id="applyFilters">Apply Filters</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Footer -->
    <?php include 'footer.php'; ?>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="../js/allPhones.js"></script>
</body>
</html>
