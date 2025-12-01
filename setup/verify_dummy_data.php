<?php
/**
 * Verify Dummy Data Script
 * This script checks if dummy phone data was added successfully
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

echo "<h1>📱 Dummy Data Verification</h1>";

// Get total phone count
$countSql = "SELECT COUNT(*) as total FROM phones";
$result = $conn->query($countSql);
$totalPhones = $result->fetch_assoc()['total'];

echo "<h2>Database Summary</h2>";
echo "<p><strong>Total phones in database:</strong> $totalPhones</p>";

// Get phones by brand
$brandSql = "SELECT b.name as brand_name, COUNT(p.id) as phone_count 
             FROM brands b 
             LEFT JOIN phones p ON b.id = p.brand_id 
             GROUP BY b.id, b.name 
             ORDER BY phone_count DESC";
$result = $conn->query($brandSql);

echo "<h2>Phones by Brand</h2>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Brand</th><th>Phone Count</th></tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['brand_name']) . "</td>";
    echo "<td>" . $row['phone_count'] . "</td>";
    echo "</tr>";
}
echo "</table>";

// Get recent phones with images
$recentSql = "SELECT phone_name, phone_price, phone_condition, phone_storage, phone_color, 
                     seller_name, seller_location, image_paths
              FROM phones 
              ORDER BY id DESC 
              LIMIT 10";
$result = $conn->query($recentSql);

echo "<h2>Recent Phone Listings</h2>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Phone Name</th><th>Price</th><th>Condition</th><th>Storage</th><th>Color</th><th>Seller</th><th>Location</th><th>Has Images</th></tr>";

while ($row = $result->fetch_assoc()) {
    $hasImages = !empty($row['image_paths']) && $row['image_paths'] !== 'uploads/none.jpg' ? '✓' : '✗';
    
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['phone_name']) . "</td>";
    echo "<td>PKR " . number_format($row['phone_price']) . "</td>";
    echo "<td>" . $row['phone_condition'] . "/10</td>";
    echo "<td>" . htmlspecialchars($row['phone_storage']) . "</td>";
    echo "<td>" . htmlspecialchars($row['phone_color']) . "</td>";
    echo "<td>" . htmlspecialchars($row['seller_name']) . "</td>";
    echo "<td>" . htmlspecialchars($row['seller_location']) . "</td>";
    echo "<td>$hasImages</td>";
    echo "</tr>";
}
echo "</table>";

// Check for phones with images
$imageSql = "SELECT COUNT(*) as with_images FROM phones WHERE image_paths IS NOT NULL AND image_paths != '' AND image_paths != 'uploads/none.jpg'";
$result = $conn->query($imageSql);
$withImages = $result->fetch_assoc()['with_images'];

echo "<h2>Image Status</h2>";
echo "<p><strong>Phones with images:</strong> $withImages out of $totalPhones</p>";

// Price range analysis
$priceSql = "SELECT MIN(phone_price) as min_price, MAX(phone_price) as max_price, AVG(phone_price) as avg_price FROM phones";
$result = $conn->query($priceSql);
$priceData = $result->fetch_assoc();

echo "<h2>Price Analysis</h2>";
echo "<p><strong>Price Range:</strong> PKR " . number_format($priceData['min_price']) . " - PKR " . number_format($priceData['max_price']) . "</p>";
echo "<p><strong>Average Price:</strong> PKR " . number_format($priceData['avg_price']) . "</p>";

$conn->close();

echo "<br><p><strong>✅ Verification completed!</strong></p>";
echo "<p><a href='../html/allPhones.php'>View All Phones</a> | <a href='../index.php'>Go to Home</a> | <a href='run_dummy_data.php'>Add More Dummy Data</a></p>";
?>

