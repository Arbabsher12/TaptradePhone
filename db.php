<?php
// Database configuration
// Check if we're on localhost (development) or hosting
if ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1' || strpos($_SERVER['HTTP_HOST'], 'localhost:') === 0) {
    // Local development settings
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "buy_sell_phone";
} else {
    // Hosting settings - update these with your hosting provider's database details
    $servername = "localhost"; // Usually localhost on shared hosting
    $username = "your_hosting_db_username"; // Replace with your actual username
    $password = "your_hosting_db_password"; // Replace with your actual password
    $dbname = "your_hosting_db_name"; // Replace with your actual database name
}

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>