<?php
session_start();
require_once __DIR__ . '/../php/db.php'; // Include your database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user's phone listings with brand and model information
$query = "SELECT p.*, b.name as brand_name, m.model_name 
          FROM phones p 
          LEFT JOIN brands b ON p.brand_id = b.id 
          LEFT JOIN phone_models m ON p.model_id = m.id 
          WHERE p.sellerid = ?
          ORDER BY p.created_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$phones = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Listings - Phone Marketplace</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/myListing.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/footer.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="main-container container">
        <h1>My Listings</h1>
        
        <?php if (empty($phones)): ?>
            <div class="empty-listings">
                <p>You don't have any phone listings yet.</p>
                <a href="/sellYourPhone" class="btn btn-primary">Add Your First Phone</a>
            </div>
        <?php else: ?>
            <div class="listings-stats">
                <p>You have <strong><?= count($phones) ?></strong> phone listings</p>
                <a href="/sellYourPhone" class="btn btn-primary">Add New Phone</a>
            </div>
            
            <div class="listings-table">
                <table>
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Phone Name</th>
                            <th>Brand</th>
                            <th>Model</th>
                            <th>Price</th>
                            <th>Condition</th>
                            <th>Views</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($phones as $phone): ?>
                            <?php 
                                // Get the first image from image_paths
                                $image_paths = $phone['image_paths'];
                                $first_image = '/uploads/none.jpg'; // Default image
                                
                                if ($image_paths && $image_paths != '"/uploads/none.jpg"') {
                                    // Remove quotes and get the first image
                                    $image_paths = str_replace('"', '', $image_paths);
                                    $images = explode(',', $image_paths);
                                    $first_image = trim($images[0]);
                                }
                                
                                // Map condition code to text
                                $condition_map = [
                                    1 => 'Brand New',
                                    2 => 'Like New',
                                    3 => 'Good',
                                    4 => 'Fair',
                                    5 => 'Poor'
                                ];
                                $condition_text = isset($condition_map[$phone['phone_condition']]) ? 
                                    $condition_map[$phone['phone_condition']] : 'Unknown';
                            ?>
                            <tr>
                                <td class="phone-image">
                                    <img src="../uploads/<?= htmlspecialchars($first_image) ?>" alt="<?= htmlspecialchars($phone['phone_name']) ?>">
                                </td>
                                <td><?= htmlspecialchars($phone['phone_name']) ?></td>
                                <td><?= htmlspecialchars($phone['brand_name'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($phone['model_name'] ?? 'N/A') ?></td>
                                <td class="price">$<?= number_format($phone['phone_price'], 2) ?></td>
                                <td><?= $condition_text ?></td>
                                <td><?= $phone['views'] ?></td>
                                <td class="actions">
                                    <button class="btn-view" onclick="viewPhone(<?= $phone['id'] ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn-edit" onclick="openEditModal(<?= $phone['id'] ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-delete" onclick="confirmDelete(<?= $phone['id'] ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Edit Phone Modal -->
    <div id="editPhoneModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Edit Phone Listing</h2>
            <div id="editPhoneContainer">
                <!-- Form will be loaded here via AJAX -->
                <div class="loading">Loading...</div>
            </div>
        </div>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Confirm Deletion</h2>
            <p>Are you sure you want to delete this phone listing? This action cannot be undone.</p>
            <div class="modal-actions">
                <button id="confirmDeleteBtn" class="btn btn-danger">Delete</button>
                <button id="cancelDeleteBtn" class="btn btn-secondary">Cancel</button>
            </div>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>
    
    <script src="../js/myListing.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // This script needs to be at the end of the form to ensure elements exist
document.addEventListener('DOMContentLoaded', function() {
    // Initialize brand/model dependent dropdown
    const brandSelect = document.getElementById('brand_id');
    const currentModelId = <?= $phone['model_id'] ?: 'null' ?>;
    
    if (brandSelect) {
        brandSelect.addEventListener('change', function() {
            loadModels(this.value);
        });
    }
    
    // Initialize condition range slider
    const conditionSlider = document.getElementById('phone_condition');
    const conditionValue = document.getElementById('condition_value');
    
    if (conditionSlider && conditionValue) {
        conditionSlider.addEventListener('input', function() {
            conditionValue.textContent = this.value;
        });
    }
});

// Array to store removed existing images
let removedImages = [];

// Function to remove existing image
function removeExistingImage(button) {
    const imagePreview = button.parentElement;
    const imagePath = imagePreview.getAttribute('data-path');
    
    // Add to removed images array
    removedImages.push(imagePath);
    document.getElementById('removed_images').value = JSON.stringify(removedImages);
    
    // Remove from DOM
    imagePreview.remove();
}

// Function to preview new images
function previewNewImages(input) {
    const previewContainer = document.getElementById('new-images-preview');
    previewContainer.innerHTML = '';
    
    if (input.files && input.files.length > 0) {
        for (let i = 0; i < input.files.length; i++) {
            const file = input.files[i];
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const preview = document.createElement('div');
                preview.className = 'image-preview';
                preview.innerHTML = `
                    <img src="${e.target.result}" alt="New Image ${i+1}">
                    <button type="button" class="remove-image" data-index="${i}">×</button>
                `;
                previewContainer.appendChild(preview);
                
                // Add event listener to remove button
                preview.querySelector('.remove-image').addEventListener('click', function() {
                    preview.remove();
                    
                    // Create a new FileList without the removed file
                    if (this.hasAttribute('data-index')) {
                        const index = parseInt(this.getAttribute('data-index'));
                        const dt = new DataTransfer();
                        
                        for (let j = 0; j < input.files.length; j++) {
                            if (j !== index) {
                                dt.items.add(input.files[j]);
                            }
                        }
                        
                        input.files = dt.files;
                    }
                });
            }
            
            reader.readAsDataURL(file);
        }
    }
}
    </script>
</body>
</html>
