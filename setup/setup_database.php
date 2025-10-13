<?php
/**
 * Database Setup Script for Buy & Sell Phone Application
 * This script helps you set up the database without XAMPP
 */

echo "=== Buy & Sell Phone Database Setup ===\n\n";

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "buy_sell_phone";

echo "Attempting to connect to MySQL server...\n";

try {
    // Create connection without specifying database
    $conn = new mysqli($servername, $username, $password);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    echo "✓ Connected to MySQL server successfully!\n\n";
    
    // Check if database exists
    $result = $conn->query("SHOW DATABASES LIKE '$dbname'");
    
    if ($result->num_rows > 0) {
        echo "✓ Database '$dbname' already exists!\n";
        echo "✓ Your application should work now.\n\n";
    } else {
        echo "Database '$dbname' does not exist. Creating it...\n";
        
        // Create database
        $sql = "CREATE DATABASE $dbname";
        if ($conn->query($sql) === TRUE) {
            echo "✓ Database '$dbname' created successfully!\n";
        } else {
            throw new Exception("Error creating database: " . $conn->error);
        }
        
        // Select the database
        $conn->select_db($dbname);
        
        // Read and execute SQL file
        echo "Importing database structure and data...\n";
        $sqlFile = file_get_contents('buy_sell_phone.sql');
        
        if ($sqlFile === false) {
            throw new Exception("Could not read buy_sell_phone.sql file");
        }
        
        // Split SQL file into individual queries
        $queries = explode(';', $sqlFile);
        $successCount = 0;
        
        foreach ($queries as $query) {
            $query = trim($query);
            if (!empty($query) && !preg_match('/^--/', $query)) {
                if ($conn->query($query) === TRUE) {
                    $successCount++;
                }
            }
        }
        
        echo "✓ Database structure imported successfully!\n";
        echo "✓ $successCount queries executed.\n\n";
    }
    
    echo "=== Setup Complete! ===\n";
    echo "You can now run your PHP application with:\n";
    echo "php -S localhost:8000\n\n";
    echo "Then visit: http://localhost:8000\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n\n";
    echo "=== Troubleshooting ===\n";
    echo "1. Make sure MySQL is installed and running\n";
    echo "2. Check if the username/password are correct\n";
    echo "3. Try installing MySQL separately:\n";
    echo "   - Windows: Download from https://dev.mysql.com/downloads/mysql/\n";
    echo "   - Or use a portable MySQL like XAMPP Portable\n";
    echo "4. Alternative: Use SQLite (modify db.php to use SQLite)\n\n";
}

if (isset($conn)) {
    $conn->close();
}
?>
