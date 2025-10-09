<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phone Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/phoneDetail.css">
   
    <link rel="stylesheet" href="../css/footer.css">
</head>
<body>
    <?php include __DIR__ . '/../php/PhoneDetail.php'; ?>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="/">Phone Marketplace</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Browse</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="sell-phone.html">Sell Phone</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a href="/sellYourPhone" class="btn btn-outline-light">+ Sell Your Phone</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mb-5">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item"><a href="/">Phones</a></li>
                <li class="breadcrumb-item active"><?php echo htmlspecialchars($phone['phone_name']); ?></li>
            </ol>
        </nav>

        <div class="row">
            <!-- Left Column - Images -->
            <div class="col-lg-7 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="image-gallery">
                            <img src="../uploads/<?php echo htmlspecialchars($images[0]); ?>" id="mainImage" class="gallery-main-image" alt="<?php echo htmlspecialchars($phone['phone_name']); ?>" onerror="this.src='../uploads/none.jpg'">
                            <?php if ($has_actual_images && count($images) > 1): ?>
                                <button class="gallery-nav gallery-prev" onclick="changeImage('prev')">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button class="gallery-nav gallery-next" onclick="changeImage('next')">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($has_actual_images && count($images) > 1): ?>
                            <div class="thumbnails">
                                <?php foreach ($images as $index => $img): ?>
                                    <img src="../uploads/<?php echo htmlspecialchars($img); ?>" 
                                         class="thumbnail <?php echo $index === 0 ? 'active' : ''; ?>" 
                                         onclick="setMainImage(this, <?php echo $index; ?>)" 
                                         alt="<?php echo htmlspecialchars($phone['phone_name']) . ' - Image ' . ($index + 1); ?>"
                                         onerror="this.src='../uploads/none.jpg'">
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Right Column - Details -->
            <div class="col-lg-5">
                <div class="card mb-4">
                    <div class="card-body">
                        <h1 class="mb-3"><?php echo htmlspecialchars($phone['phone_name']); ?></h1>
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="phone-price">$<?php echo number_format($phone['phone_price'], 2); ?></div>
                            <span class="badge bg-success">Available</span>
                        </div>
                        
                        <div class="mb-4">
                            <div class="condition-stars mb-2">
                                <?php for ($i = 1; $i <= 10; $i++): ?>
                                    <i class="fas fa-star <?php echo ($i <= $phone['phone_condition']) ? 'filled' : ''; ?>"></i>
                                <?php endfor; ?>
                                <span class="ms-2"><?php echo $phone['phone_condition']; ?>/10</span>
                            </div>
                            <p class="phone-condition-text">Condition: <strong><?php echo getConditionText($phone['phone_condition']); ?></strong></p>
                        </div>
                        
                        <div class="mb-4">
                            <h5>Description</h5>
                            <p class="phone-details"><?php echo nl2br(htmlspecialchars($phone['phone_details'] ?? '')); ?></p>
                        </div>
                        
                        <div class="mb-4">
                            <h5>Contact Seller</h5>
                            <div class="d-grid gap-2">
                                <button id="message_seller" class="btn btn-primary">
                                    <i class="fas fa-comment-alt me-2"></i>Message Seller
                                </button>
                                <button class="btn btn-outline-primary">
                                    <i class="fas fa-phone-alt me-2"></i>Show Phone Number
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="mb-3">Seller Information</h5>
                        <div class="seller-info">
                            <?php if (!empty($phone['profile_picture'])): ?>
                                <img src="../uploads/<?php echo htmlspecialchars($phone['profile_picture']); ?>" class="seller-avatar" alt="Seller">
                            <?php else: ?>
                                <img src="../components/noDp.png" class="seller-avatar" alt="Seller">
                            <?php endif; ?>
                            <div>
                                <h6 class="seller-name"><?php echo htmlspecialchars($phone['seller_name'] ?? 'Anonymous'); ?></h6>
                                <small class="text-muted">Member since <?php echo !empty($phone['user_created_at']) ? date('F Y', strtotime($phone['user_created_at'])) : 'N/A'; ?></small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-3">Listing Details</h5>
                        <div class="listing-info">
                            <p><i class="far fa-calendar-alt me-2"></i>Listed: <strong><?php echo date('F j, Y', strtotime($phone['created_at'])); ?></strong></p>
                            <p><i class="fas fa-eye me-2"></i>Views: <strong><?php echo $phone['views'] ?? 0; ?></strong></p>
                            <p><i class="fas fa-tag me-2"></i>Category: <strong>Smartphones</strong></p>
                            <p><i class="fas fa-info-circle me-2"></i>Listing ID: <strong>#<?php echo $phone['id']; ?></strong></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Specifications -->
        <div class="card m-4 ">
            <div class="card-body">
                <h4 class="mb-4">Phone Specifications</h4>
                <div class="row">
                    <div class="col-md-6">
                        <table class="table specs-table">
                            <tbody>
                                <?php
                                // Extract brand from phone name (first word usually)
                                $brand = explode(' ', $phone['phone_name'])[0];
                                
                                // Basic specs we can show
                                $specs = [
                                    'Brand' => $brand,
                                    'Model' => $phone['phone_name'],
                                    'Price' => '$' . number_format($phone['phone_price'], 2),
                                    'Condition' => $phone['phone_condition'] . '/10 - ' . getConditionText($phone['phone_condition'])
                                ];
                                
                                // Display first half of specs
                                $half = ceil(count($specs) / 2);
                                $i = 0;
                                
                                foreach ($specs as $key => $value) {
                                    if ($i < $half) {
                                        echo '<tr>
                                                <th>' . htmlspecialchars($key) . '</th>
                                                <td>' . htmlspecialchars($value) . '</td>
                                              </tr>';
                                    }
                                    $i++;
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table specs-table">
                            <tbody>
                                <?php
                                // Display second half of specs
                                $i = 0;
                                
                                foreach ($specs as $key => $value) {
                                    if ($i >= $half) {
                                        echo '<tr>
                                                <th>' . htmlspecialchars($key) . '</th>
                                                <td>' . htmlspecialchars($value) . '</td>
                                              </tr>';
                                    }
                                    $i++;
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Similar Phones -->
        <?php if (!empty($similar_phones)): ?>
            <div class="similar-phones m-5">
                <h4 class="mb-4">Similar Phones</h4>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                    <?php foreach ($similar_phones as $similar): ?>
                        <div class="col">
                            <a href="/phoneDetail?id=<?php echo $similar['id']; ?>" class="text-decoration-none">
                                <div class="card similar-phone-card">
                                    <?php 
                                    $similar_image = "none.jpg"; // Default image path
                                    
                                    // First try to get the first image from image_paths
                                    if (!empty($similar['image_paths'])) {
                                        $img_paths = explode(',', $similar['image_paths']);
                                        if (!empty($img_paths[0])) {
                                            $similar_image = trim($img_paths[0]);
                                        }
                                    }
                                    ?>
                                    <img src="../uploads/<?php echo htmlspecialchars($similar_image); ?>" 
                                         class="similar-phone-img" 
                                         alt="<?php echo htmlspecialchars($similar['phone_name']); ?>"
                                         onerror="this.src='../uploads/none.jpg'">
                                    <div class="card-body">
                                        <h5 class="card-title text-dark"><?php echo htmlspecialchars($similar['phone_name']); ?></h5>
                                        <p class="card-text text-primary fw-bold">$<?php echo number_format($similar['phone_price'], 2); ?></p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="condition-stars">
                                                <?php for ($i = 1; $i <= 10; $i++): ?>
                                                    <i class="fas fa-star <?php echo ($i <= $similar['phone_condition']) ? 'filled' : ''; ?>"></i>
                                                <?php endfor; ?>
                                            </div>
                                            <span><?php echo $similar['phone_condition']; ?>/10</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Footer -->
    <?php include 'footer.php'; ?>



<!-- ------------------                 JavaScript                  ------------------------------------- -->

<script>

// Store images array for gallery
const images = <?php echo json_encode($images); ?>;
let currentImageIndex = 0;



function setMainImage(thumbnail, index) {
    // Update main image
    document.getElementById('mainImage').src = '../uploads/'+images[index];

    // Update active thumbnail
    const thumbnails = document.querySelectorAll('.thumbnail');
    thumbnails.forEach(thumb => {
        thumb.classList.remove('active');
    });
    thumbnail.classList.add('active');

    // Update current index
    currentImageIndex = index;
}

function changeImage(direction) {
    if (images.length <= 1) return;

    let newIndex;

    if (direction === 'next') {
        newIndex = (currentImageIndex + 1) % images.length;
    } else {
        newIndex = (currentImageIndex - 1 + images.length) % images.length;
    }

    // Update main image
    document.getElementById('mainImage').src = '../uploads/'+images[newIndex];

    // Update active thumbnail
    const thumbnails = document.querySelectorAll('.thumbnail');
    thumbnails.forEach((thumb, idx) => {
        if (idx === newIndex) {
            thumb.classList.add('active');
        } else {
            thumb.classList.remove('active');
        }
    });

    // Update current index
    currentImageIndex = newIndex;
}

// Back to top button functionality (only if element exists)
const backToTopButton = document.getElementById('backToTop');

if (backToTopButton) {
    window.addEventListener('scroll', () => {
        if (window.pageYOffset > 300) {
            backToTopButton.classList.add('visible');
        } else {
            backToTopButton.classList.remove('visible');
        }
    });

    backToTopButton.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}


// Message seller button functionality
function initMessageSellerButton() {
    console.log('Initializing message seller button...');
    
    const messageButton = document.getElementById('message_seller');
    const phoneId = <?php echo json_encode($phone['id']); ?>;
    const sellerId = <?php echo json_encode($phone['sellerId']); ?>;

    console.log('Message button element:', messageButton);
    console.log('Phone ID:', phoneId);
    console.log('Seller ID:', sellerId);

    if (messageButton) {
        console.log('Message button found!');
        
        // Remove any existing event listeners
        messageButton.removeEventListener('click', handleMessageClick);
        
        // Add new event listener
        messageButton.addEventListener('click', handleMessageClick);
        
        console.log('Event listener added successfully!');
    } else {
        console.error('Message seller button not found!');
        console.log('Available buttons:', document.querySelectorAll('button'));
    }
}

// Separate function for the click handler
function handleMessageClick(e) {
    e.preventDefault();
    console.log('Message seller button clicked!');
    
    const phoneId = <?php echo json_encode($phone['id']); ?>;
    const sellerId = <?php echo json_encode($phone['sellerId']); ?>;
    
    // Validate that we have the required parameters
    if (!phoneId || !sellerId) {
        console.error('Missing phone_id or seller_id');
        alert('Error: Unable to start conversation. Please try again.');
        return;
    }
    
    console.log('Parameters valid, redirecting to chats page...');
    console.log('URL: /chats?phone_id=' + phoneId + '&seller_id=' + sellerId);
    
    // Redirect to chats page
    window.location.href = `/chats?phone_id=${phoneId}&seller_id=${sellerId}`;
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing message seller button...');
    initMessageSellerButton();
});

// Also try immediately in case DOM is already ready
if (document.readyState !== 'loading') {
    console.log('DOM already ready, initializing immediately...');
    initMessageSellerButton();
}

</script> 

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
