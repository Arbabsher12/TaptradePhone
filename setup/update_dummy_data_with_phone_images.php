<?php
/**
 * Update Dummy Data with Phone Images Script
 * Updates the existing dummy phone data with proper phone images
 */

// Database configuration for local development
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "buy_sell_phone";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if database connection is successful
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

echo "<h1>📱 Updating Dummy Data with Phone Images</h1>";

// Load phone image mapping
$imageMappingFile = __DIR__ . '/../uploads/phones/phone_image_mapping.json';

if (!file_exists($imageMappingFile)) {
    echo "<div style='color: red;'>❌ Phone image mapping file not found!</div>";
    echo "<p>Please run <a href='create_phone_images.php'>create_phone_images.php</a> first.</p>";
    exit;
}

$phoneImageMapping = json_decode(file_get_contents($imageMappingFile), true);

if (!$phoneImageMapping) {
    echo "<div style='color: red;'>❌ Failed to load phone image mapping!</div>";
    exit;
}

echo "<h2>Phone Image Mapping Loaded</h2>";
echo "<p>Found images for " . count($phoneImageMapping) . " phones</p>";

// Update phones with new images
echo "<h2>Updating Phone Images...</h2>";

$updateCount = 0;
$errorCount = 0;

foreach ($phoneImageMapping as $phoneName => $imagePaths) {
    $sql = "UPDATE phones SET image_paths = ? WHERE phone_name = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        echo "✗ Error preparing statement for $phoneName: " . $conn->error . "<br>";
        $errorCount++;
        continue;
    }
    
    $stmt->bind_param("ss", $imagePaths, $phoneName);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "✓ Updated: $phoneName<br>";
            $updateCount++;
        } else {
            echo "⚠ No rows updated for: $phoneName (phone may not exist in database)<br>";
        }
    } else {
        echo "✗ Error updating $phoneName: " . $stmt->error . "<br>";
        $errorCount++;
    }
    
    $stmt->close();
}

echo "<h2>Summary</h2>";
echo "<p><strong>Successfully updated:</strong> $updateCount phones</p>";
echo "<p><strong>Errors:</strong> $errorCount</p>";

// Display updated phones
echo "<h2>Updated Phone Listings</h2>";
$recentSql = "SELECT phone_name, phone_price, phone_condition, phone_storage, phone_color, 
                     seller_name, seller_location, image_paths
              FROM phones 
              WHERE image_paths LIKE 'uploads/phones/%'
              ORDER BY id DESC 
              LIMIT 10";
$result = $conn->query($recentSql);

if ($result && $result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Phone Name</th><th>Price</th><th>Condition</th><th>Storage</th><th>Color</th><th>Seller</th><th>Location</th><th>Images</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        $imageCount = substr_count($row['image_paths'], ',') + 1;
        
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['phone_name']) . "</td>";
        echo "<td>PKR " . number_format($row['phone_price']) . "</td>";
        echo "<td>" . $row['phone_condition'] . "/10</td>";
        echo "<td>" . htmlspecialchars($row['phone_storage']) . "</td>";
        echo "<td>" . htmlspecialchars($row['phone_color']) . "</td>";
        echo "<td>" . htmlspecialchars($row['seller_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['seller_location']) . "</td>";
        echo "<td>$imageCount images</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No phones with phone images found.</p>";
}

// Check total phones with proper images
$imageSql = "SELECT COUNT(*) as with_phone_images FROM phones WHERE image_paths LIKE 'uploads/phones/%'";
$result = $conn->query($imageSql);
$withPhoneImages = $result->fetch_assoc()['with_phone_images'];

$totalSql = "SELECT COUNT(*) as total FROM phones";
$result = $conn->query($totalSql);
$totalPhones = $result->fetch_assoc()['total'];

echo "<h2>Image Status</h2>";
echo "<p><strong>Phones with proper phone images:</strong> $withPhoneImages out of $totalPhones</p>";

$conn->close();

echo "<br><p><strong>✅ Phone image update completed!</strong></p>";
echo "<p><a href='../html/allPhones.php'>View All Phones</a> | <a href='../index.php'>Go to Home</a> | <a href='verify_dummy_data.php'>Verify Data</a></p>";
?>

