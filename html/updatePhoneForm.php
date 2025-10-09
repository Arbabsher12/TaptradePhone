=<?php
session_start();
require_once __DIR__ . '/../db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo "Unauthorized";
    header('location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$phone_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$phone_id) {
    http_response_code(400);
    echo "Invalid phone ID";
    exit();
}

// Fetch phone data with brand and model information
$query = "SELECT p.*, b.name as brand_name, m.model_name 
          FROM phones p 
          LEFT JOIN brands b ON p.brand_id = b.id 
          LEFT JOIN phone_models m ON p.model_id = m.id 
          WHERE p.id = ? AND p.sellerid = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $phone_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo "Phone not found or you don't have permission to edit it";
    exit();
}

$phone = $result->fetch_assoc();

// Fetch all brands
$brands_query = "SELECT id, name FROM brands ORDER BY name";
$brands_result = $conn->query($brands_query);
$brands = $brands_result->fetch_all(MYSQLI_ASSOC);

// Fetch models for the selected brand
$models_query = "SELECT id, model_name FROM phone_models WHERE brand_id = ? ORDER BY model_name";
$models_stmt = $conn->prepare($models_query);
$models_stmt->bind_param("i", $phone['brand_id']);
$models_stmt->execute();
$models_result = $models_stmt->get_result();
$models = $models_result->fetch_all(MYSQLI_ASSOC);

// Storage options
$storage_options = [
    '16GB', '32GB', '64GB', '128GB', '256GB', '512GB', '1TB', '2TB'
];
?>


<script>

</script>


<form id="editPhoneForm" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $phone_id ?>">
    <input type="hidden" name="removed_images" id="removed_images" value="">
    
    <div class="form-group">
        <label for="phone_name">Phone Name</label>
        <input type="text" id="phone_name" name="phone_name" value="<?= htmlspecialchars($phone['phone_name']) ?>" required>
    </div>
    
    <div class="form-row">
        <div class="form-group">
            <label for="brand_id">Brand</label>
            <select id="brand_id" name="brand_id" required>      
                <option value="">Select Brand</option>
                <?php foreach ($brands as $brand): ?>
                    <option value="<?= $brand['id'] ?>" <?= $phone['brand_id'] == $brand['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($brand['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="model_id">Model</label>
            <select id="model_id" name="model_id" required>
                <option value="">Select Model</option>
                <?php foreach ($models as $model): ?>
                    <option value="<?= $model['id'] ?>" <?= $phone['model_id'] == $model['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($model['model_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    
    <div class="form-row">
        <div class="form-group">
            <label for="phone_price">Price ($)</label>
            <input type="number" id="phone_price" name="phone_price" step="0.01" min="0" value="<?= $phone['phone_price'] ?>" required>
        </div>
        
        <div class="form-group">
            <label for="phone_condition">Condition (1-10)</label>
            <input type="range" id="phone_condition" name="phone_condition" min="1" max="10" value="<?= $phone['phone_condition'] ?>" required>
            <div class="range-value"><span id="condition_value"><?= $phone['phone_condition'] ?></span>/10</div>
        </div>
    </div>
    
    <div class="form-row">
        <div class="form-group">
            <label for="phone_storage">Storage</label>
            <select id="phone_storage" name="phone_storage">
                <option value="">Select Storage</option>
                <?php foreach ($storage_options as $option): ?>
                    <option value="<?= $option ?>" <?= $phone['phone_storage'] == $option ? 'selected' : '' ?>>
                        <?= $option ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="phone_color">Color</label>
            <input type="text" id="phone_color" name="phone_color" value="<?= htmlspecialchars($phone['phone_color'] ?? '') ?>">
        </div>
    </div>
    
    <div class="form-group">
        <label for="phone_details">Details</label>
        <textarea id="phone_details" name="phone_details"><?= htmlspecialchars($phone['phone_details'] ?? '') ?></textarea>
    </div>
    
    <div class="form-group">
        <label>Current Images</label>
        <div class="current-images" id="current-images">
            <?php 
            $image_paths = $phone['image_paths'];
            if ($image_paths && $image_paths != '"/uploads/none.jpg"') {
                // Remove quotes and get images
                $image_paths = str_replace('"', '', $image_paths);
                $images = explode(',', $image_paths);
                
                foreach ($images as $index => $image) {
                    $image = trim($image);
                    echo '<div class="image-preview" data-path="' . htmlspecialchars($image) . '">';
                    echo '<img src="../uploads/' . htmlspecialchars($image) . '" alt="Phone Image ' . ($index + 1) . '">';
                    echo '<button type="button" class="remove-image" onclick="removeExistingImage(this)">×</button>';
                    echo '</div>';
                }
            } else {
                echo '<p>No images available</p>';
            }
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="new_images">Upload New Images (Optional)</label>
        <input type="file" id="new_images" name="new_images[]" multiple accept="image/*" onchange="previewNewImages(this)">
        <div class="new-images-preview" id="new-images-preview"></div>
    </div>
    
    <button type="submit" class="btn btn-primary">Update Phone</button>
</form>

<style>
.form-row {
    display: flex;
    gap: 1rem;
}

.form-row .form-group {
    flex: 1;
}

.current-images, .new-images-preview {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-top: 0.5rem;
}

.image-preview {
    width: 100px;
    height: 100px;
    border: 1px solid #ddd;
    border-radius: 4px;
    overflow: hidden;
    position: relative;
}

.image-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.remove-image {
    position: absolute;
    top: 5px;
    right: 5px;
    width: 20px;
    height: 20px;
    background-color: rgba(255, 0, 0, 0.7);
    color: white;
    border: none;
    border-radius: 50%;
    font-size: 16px;
    line-height: 1;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.range-value {
    text-align: center;
    margin-top: 5px;
    font-weight: bold;
}

.form-status {
    margin-top: 1rem;
    padding: 0.75rem;
    border-radius: 4px;
}

.form-status.success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.form-status.error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

@media (max-width: 768px) {
    .form-row {
        flex-direction: column;
        gap: 0;
    }
}
</style>

