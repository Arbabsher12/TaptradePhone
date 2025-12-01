<?php
/**
 * Add Dummy Phone Data Script
 * This script adds realistic dummy phone data to the database with proper images
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

echo "<h1>Adding Dummy Phone Data</h1>";

// Sample phone data with realistic information
$dummyPhones = [
    [
        'phone_name' => 'iPhone 15 Pro Max',
        'phone_price' => 450000,
        'phone_condition' => 9,
        'phone_details' => 'Brand new iPhone 15 Pro Max in excellent condition. Never used, still in original packaging. Comes with all accessories including charger, cable, and documentation.',
        'image_paths' => 'uploads/1746692847_Apple-iPhone-16-Pro-.jpg,uploads/1745509489_pexels-samandgos-709552.jpg,uploads/1745509497_pexels-samandgos-709552.jpg',
        'brand_id' => 1, // Apple
        'model_id' => 1, // iPhone 15 Pro Max
        'phone_storage' => '256GB',
        'phone_color' => 'Natural Titanium',
        'sellerId' => 19,
        'seller_name' => 'Arbab Rahim Ullah',
        'seller_email' => 'a776@gmail.com',
        'seller_phone' => '0333-0411255',
        'seller_location' => 'Peshawar'
    ],
    [
        'phone_name' => 'Samsung Galaxy S23 Ultra',
        'phone_price' => 380000,
        'phone_condition' => 8,
        'phone_details' => 'Samsung Galaxy S23 Ultra in great condition. Used for 3 months, no scratches or damage. Comes with original box and charger.',
        'image_paths' => 'uploads/1746110057_Screenshot 2024-06-23 224352.png,uploads/1746110057_Screenshot 2024-06-23 224444.png,uploads/1746110057_Screenshot 2024-06-23 224509.png',
        'brand_id' => 2, // Samsung
        'model_id' => 21, // Galaxy S23 Ultra
        'phone_storage' => '512GB',
        'phone_color' => 'Phantom Black',
        'sellerId' => 18,
        'seller_name' => 'Arbab Rahim',
        'seller_email' => 'arbabrahimullahjan@gmail.com',
        'seller_phone' => '0333-0411255',
        'seller_location' => 'Peshawar'
    ],
    [
        'phone_name' => 'Google Pixel 8 Pro',
        'phone_price' => 320000,
        'phone_condition' => 9,
        'phone_details' => 'Google Pixel 8 Pro in mint condition. Purchased 2 months ago, still under warranty. Excellent camera quality and performance.',
        'image_paths' => 'uploads/1745685408_cars.jpeg,uploads/1745685053_cars.jpeg,uploads/1745685832_cars.jpeg',
        'brand_id' => 3, // Google
        'model_id' => 41, // Pixel 8 Pro
        'phone_storage' => '128GB',
        'phone_color' => 'Obsidian',
        'sellerId' => 17,
        'seller_name' => 'Arbab',
        'seller_email' => 'a55@gmail.com',
        'seller_phone' => '0333-0411255',
        'seller_location' => 'Peshawar'
    ],
    [
        'phone_name' => 'OnePlus 11',
        'phone_price' => 280000,
        'phone_condition' => 7,
        'phone_details' => 'OnePlus 11 in good condition. Used for 6 months, minor wear on edges but screen is perfect. Fast charging and great performance.',
        'image_paths' => 'uploads/1745852744_cars.jpeg,uploads/1745853019_cars.jpeg,uploads/1745852610_cars.jpeg',
        'brand_id' => 5, // OnePlus
        'model_id' => 61, // OnePlus 11
        'phone_storage' => '256GB',
        'phone_color' => 'Titan Black',
        'sellerId' => 16,
        'seller_name' => 'Arbab',
        'seller_email' => 'a6@gmail.com',
        'seller_phone' => '0333-0411255',
        'seller_location' => 'Peshawar'
    ],
    [
        'phone_name' => 'Xiaomi 13 Pro',
        'phone_price' => 250000,
        'phone_condition' => 8,
        'phone_details' => 'Xiaomi 13 Pro in excellent condition. Used for 4 months, no issues. Great camera and battery life. Comes with original accessories.',
        'image_paths' => 'uploads/1746181873_cars.jpeg,uploads/1746181873_contact us.jpg,uploads/1746181873_gallery.png',
        'brand_id' => 4, // Xiaomi
        'model_id' => 51, // Xiaomi 13 Pro
        'phone_storage' => '256GB',
        'phone_color' => 'Ceramic White',
        'sellerId' => 15,
        'seller_name' => 'Arbab',
        'seller_email' => 'a2@gmail.com',
        'seller_phone' => '13425364756',
        'seller_location' => 'Peshawar'
    ],
    [
        'phone_name' => 'iPhone 14 Pro',
        'phone_price' => 350000,
        'phone_condition' => 8,
        'phone_details' => 'iPhone 14 Pro in very good condition. Used for 8 months, well maintained. Dynamic Island and excellent camera system.',
        'image_paths' => 'uploads/1743871284_Amin CV.png,uploads/1743871284_phones.png,uploads/1743871284_Screenshot 2024-06-10 163541.png',
        'brand_id' => 1, // Apple
        'model_id' => 6, // iPhone 14 Pro
        'phone_storage' => '128GB',
        'phone_color' => 'Deep Purple',
        'sellerId' => 14,
        'seller_name' => 'kn',
        'seller_email' => 'a1@gmail.com',
        'seller_phone' => 'a@gmail.com',
        'seller_location' => 'Peshawar'
    ],
    [
        'phone_name' => 'Samsung Galaxy S22 Ultra',
        'phone_price' => 300000,
        'phone_condition' => 7,
        'phone_details' => 'Samsung Galaxy S22 Ultra in good condition. Used for 1 year, some minor scratches on back but screen is perfect. S Pen included.',
        'image_paths' => 'uploads/1746110057_Screenshot 2024-06-23 224543.png,uploads/1746110057_Screenshot 2024-06-23 224624.png,uploads/1746110057_Screenshot 2024-06-23 224753.png',
        'brand_id' => 2, // Samsung
        'model_id' => 24, // Galaxy S22 Ultra
        'phone_storage' => '256GB',
        'phone_color' => 'Burgundy',
        'sellerId' => 13,
        'seller_name' => 'Arbab',
        'seller_email' => 'a@gmail.com',
        'seller_phone' => '123',
        'seller_location' => 'Peshawar'
    ],
    [
        'phone_name' => 'Google Pixel 7 Pro',
        'phone_price' => 260000,
        'phone_condition' => 8,
        'phone_details' => 'Google Pixel 7 Pro in excellent condition. Used for 5 months, no issues. Outstanding camera performance and clean Android experience.',
        'image_paths' => 'uploads/1745685883_cars.jpeg,uploads/1745686042_cars.jpeg,uploads/1745686405_cars.jpeg',
        'brand_id' => 3, // Google
        'model_id' => 43, // Pixel 7 Pro
        'phone_storage' => '128GB',
        'phone_color' => 'Hazel',
        'sellerId' => 12,
        'seller_name' => 'Arbab',
        'seller_email' => 'arbabsherahmad9@gmail.com',
        'seller_phone' => 'arbabsherahmad8@gmail.com',
        'seller_location' => 'Peshawar'
    ],
    [
        'phone_name' => 'OnePlus Nord 3',
        'phone_price' => 180000,
        'phone_condition' => 9,
        'phone_details' => 'OnePlus Nord 3 in perfect condition. Purchased 3 months ago, still under warranty. Great value for money with flagship features.',
        'image_paths' => 'uploads/1746182875_fun3.jpg,uploads/1746182875_fun5.jpg,uploads/1746182875_fun6.jpg',
        'brand_id' => 5, // OnePlus
        'model_id' => 64, // OnePlus Nord 3
        'phone_storage' => '128GB',
        'phone_color' => 'Misty Green',
        'sellerId' => 11,
        'seller_name' => 'Arbab',
        'seller_email' => 'arbabsherahmad10@gmail.com',
        'seller_phone' => 'arbabsherahmad6@gmail.com',
        'seller_location' => 'Peshawar'
    ],
    [
        'phone_name' => 'Xiaomi Redmi Note 12 Pro+',
        'phone_price' => 120000,
        'phone_condition' => 8,
        'phone_details' => 'Xiaomi Redmi Note 12 Pro+ in very good condition. Used for 6 months, excellent battery life and camera. Great budget flagship.',
        'image_paths' => 'uploads/1746236980_download (11).jpeg,uploads/1746236980_download (9).jpeg,uploads/1745509516_pexels-samandgos-709552.jpg',
        'brand_id' => 4, // Xiaomi
        'model_id' => 57, // Redmi Note 12 Pro+
        'phone_storage' => '256GB',
        'phone_color' => 'Ice Blue',
        'sellerId' => 10,
        'seller_name' => 'Arbab',
        'seller_email' => 'arbabsherahmad8@gmail.com',
        'seller_phone' => 'arbabsherahmad8@gmail.com',
        'seller_location' => 'Peshawar'
    ]
];

// Function to insert phone data
function insertPhone($conn, $phoneData) {
    $sql = "INSERT INTO phones (
        phone_name, phone_price, phone_condition, phone_details, image_paths,
        brand_id, model_id, phone_storage, phone_color,
        sellerId, seller_name, seller_email, seller_phone, seller_location
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        echo "Error preparing statement: " . $conn->error . "<br>";
        return false;
    }
    
    $stmt->bind_param("sdissiisssssss",
        $phoneData['phone_name'],
        $phoneData['phone_price'],
        $phoneData['phone_condition'],
        $phoneData['phone_details'],
        $phoneData['image_paths'],
        $phoneData['brand_id'],
        $phoneData['model_id'],
        $phoneData['phone_storage'],
        $phoneData['phone_color'],
        $phoneData['sellerId'],
        $phoneData['seller_name'],
        $phoneData['seller_email'],
        $phoneData['seller_phone'],
        $phoneData['seller_location']
    );
    
    if ($stmt->execute()) {
        echo "✓ Successfully added: " . $phoneData['phone_name'] . " - PKR " . number_format($phoneData['phone_price']) . "<br>";
        return true;
    } else {
        echo "✗ Error adding " . $phoneData['phone_name'] . ": " . $stmt->error . "<br>";
        return false;
    }
    
    $stmt->close();
}

// Clear existing dummy data (optional - uncomment if you want to start fresh)
echo "<h2>Clearing existing dummy data...</h2>";
$clearSql = "DELETE FROM phones WHERE sellerId IN (10, 11, 12, 13, 14, 15, 16, 17, 18, 19)";
if ($conn->query($clearSql)) {
    echo "✓ Cleared existing dummy data<br><br>";
} else {
    echo "✗ Error clearing data: " . $conn->error . "<br><br>";
}

// Insert dummy phones
echo "<h2>Adding dummy phone data...</h2>";
$successCount = 0;
$totalCount = count($dummyPhones);

foreach ($dummyPhones as $phone) {
    if (insertPhone($conn, $phone)) {
        $successCount++;
    }
}

echo "<br><h2>Summary</h2>";
echo "Successfully added: $successCount out of $totalCount phones<br>";

// Display current phone count
$countSql = "SELECT COUNT(*) as total FROM phones";
$result = $conn->query($countSql);
if ($result) {
    $row = $result->fetch_assoc();
    echo "Total phones in database: " . $row['total'] . "<br>";
}

// Display recent additions
echo "<h2>Recent Additions</h2>";
$recentSql = "SELECT phone_name, phone_price, phone_condition, seller_name, seller_location 
              FROM phones 
              ORDER BY id DESC 
              LIMIT 5";
$result = $conn->query($recentSql);

if ($result && $result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Phone Name</th><th>Price</th><th>Condition</th><th>Seller</th><th>Location</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['phone_name']) . "</td>";
        echo "<td>PKR " . number_format($row['phone_price']) . "</td>";
        echo "<td>" . $row['phone_condition'] . "/10</td>";
        echo "<td>" . htmlspecialchars($row['seller_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['seller_location']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No phones found in database.<br>";
}

$conn->close();
echo "<br><p><strong>Dummy data insertion completed!</strong></p>";
echo "<p><a href='../html/allPhones.php'>View All Phones</a> | <a href='../index.php'>Go to Home</a></p>";
?>
