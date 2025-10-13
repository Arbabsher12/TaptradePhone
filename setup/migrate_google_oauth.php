<?php
/**
 * Database Migration Script for Google OAuth Support
 * 
 * This script adds necessary columns to support Google OAuth authentication
 * Run this script once to update your database schema
 */

// Database connection - use local settings for migration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "buy_sell_phone";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Starting database migration for Google OAuth support...\n";

try {
    // Add google_id column to users table
    $sql = "ALTER TABLE users ADD COLUMN google_id VARCHAR(255) NULL AFTER email";
    if ($conn->query($sql) === TRUE) {
        echo "✅ Added google_id column to users table\n";
    } else {
        if (strpos($conn->error, 'Duplicate column name') !== false) {
            echo "ℹ️  google_id column already exists\n";
        } else {
            echo "❌ Error adding google_id column: " . $conn->error . "\n";
        }
    }

    // Add login_method column to users table
    $sql = "ALTER TABLE users ADD COLUMN login_method ENUM('email', 'google') DEFAULT 'email' AFTER google_id";
    if ($conn->query($sql) === TRUE) {
        echo "✅ Added login_method column to users table\n";
    } else {
        if (strpos($conn->error, 'Duplicate column name') !== false) {
            echo "ℹ️  login_method column already exists\n";
        } else {
            echo "❌ Error adding login_method column: " . $conn->error . "\n";
        }
    }

    // Add unique index on google_id
    $sql = "ALTER TABLE users ADD UNIQUE KEY unique_google_id (google_id)";
    if ($conn->query($sql) === TRUE) {
        echo "✅ Added unique index on google_id\n";
    } else {
        if (strpos($conn->error, 'Duplicate key name') !== false) {
            echo "ℹ️  Unique index on google_id already exists\n";
        } else {
            echo "❌ Error adding unique index on google_id: " . $conn->error . "\n";
        }
    }

    // Make phone column nullable for Google users (since Google doesn't provide phone)
    $sql = "ALTER TABLE users MODIFY COLUMN phone VARCHAR(255) NULL";
    if ($conn->query($sql) === TRUE) {
        echo "✅ Made phone column nullable\n";
    } else {
        echo "❌ Error making phone column nullable: " . $conn->error . "\n";
    }

    // Make password column nullable for Google users (they use OAuth)
    $sql = "ALTER TABLE users MODIFY COLUMN password VARCHAR(255) NULL";
    if ($conn->query($sql) === TRUE) {
        echo "✅ Made password column nullable\n";
    } else {
        echo "❌ Error making password column nullable: " . $conn->error . "\n";
    }

    echo "\n🎉 Database migration completed successfully!\n";
    echo "Your database is now ready for Google OAuth authentication.\n";

} catch (Exception $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
}

$conn->close();
?>
