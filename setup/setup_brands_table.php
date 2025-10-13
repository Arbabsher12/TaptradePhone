<?php
// Script to setup brands table if it doesn't exist
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Setup Brands Table</h2>";

// Include database connection
include 'php/db.php';

if (!$conn) {
    die("❌ Database connection failed");
}

echo "✅ Database connected<br>";

// Check if brands table exists
$tableCheck = $conn->query("SHOW TABLES LIKE 'brands'");
if ($tableCheck && $tableCheck->num_rows > 0) {
    echo "✅ Brands table exists<br>";
    
    // Check if table has data
    $countResult = $conn->query("SELECT COUNT(*) as count FROM brands");
    if ($countResult) {
        $row = $countResult->fetch_assoc();
        echo "📊 Brands count: " . $row['count'] . "<br>";
        
        if ($row['count'] == 0) {
            echo "⚠️ Table is empty, inserting default brands...<br>";
            insertDefaultBrands();
        } else {
            echo "✅ Table has data<br>";
        }
    }
} else {
    echo "❌ Brands table does not exist, creating it...<br>";
    createBrandsTable();
    insertDefaultBrands();
}

function createBrandsTable() {
    global $conn;
    
    $sql = "CREATE TABLE `brands` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(100) NOT NULL,
        `logo` varchar(255) DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    
    if ($conn->query($sql) === TRUE) {
        echo "✅ Brands table created successfully<br>";
    } else {
        echo "❌ Error creating table: " . $conn->error . "<br>";
    }
}

function insertDefaultBrands() {
    global $conn;
    
    $brands = [
        ['Apple', 'apple-logo.png'],
        ['Samsung', 'samsung-logo.png'],
        ['Google', 'google-logo.png'],
        ['Xiaomi', 'xiaomi-logo.png'],
        ['OnePlus', 'oneplus-logo.png'],
        ['Huawei', 'huawei-logo.png'],
        ['Motorola', 'motorola-logo.png'],
        ['Sony', 'sony-logo.png'],
        ['LG', 'lg-logo.png'],
        ['Nokia', 'nokia-logo.png']
    ];
    
    $stmt = $conn->prepare("INSERT INTO brands (name, logo) VALUES (?, ?)");
    
    foreach ($brands as $brand) {
        $stmt->bind_param("ss", $brand[0], $brand[1]);
        if ($stmt->execute()) {
            echo "✅ Inserted: " . $brand[0] . "<br>";
        } else {
            echo "❌ Error inserting " . $brand[0] . ": " . $stmt->error . "<br>";
        }
    }
    
    $stmt->close();
}

$conn->close();
echo "<br>✅ Setup complete!";
?>
