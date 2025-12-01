<?php
// Database configuration
// Check if we're on localhost (development) or hosting
if (!isset($_SERVER['HTTP_HOST']) || $_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1' || strpos($_SERVER['HTTP_HOST'], 'localhost:') === 0) {
    // Local development settings (also used for command line execution)
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "buy_sell_phone";
} else {
    // Hosting settings - update these with your hosting provider's database details
    $servername = "sql100.infinityfree.com"; // Usually localhost on shared hosting
    $username = "if0_40123157"; // Replace with your actual username
    $password = "Arbab4321sher"; // Replace with your actual password
    $dbname = "if0_40123157_taptrade"; // Replace with your actual database name
}

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}
?>
