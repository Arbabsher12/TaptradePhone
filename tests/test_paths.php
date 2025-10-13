<?php
/**
 * Path Test Script
 * Test if all file paths are working correctly
 */

echo "=== Path Test ===\n\n";

// Test database connection
echo "Testing database connection...\n";
try {
    include 'db.php';
    echo "✓ Database connection successful\n";
} catch (Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
}

echo "\nTesting file includes...\n";

// Test navbar include
$navbarPath = __DIR__ . '/html/navbar.php';
if (file_exists($navbarPath)) {
    echo "✓ Navbar file exists: $navbarPath\n";
} else {
    echo "✗ Navbar file not found: $navbarPath\n";
}

// Test Components directory
$componentsPath = __DIR__ . '/Components/noDp.png';
if (file_exists($componentsPath)) {
    echo "✓ Default profile image exists: $componentsPath\n";
} else {
    echo "✗ Default profile image not found: $componentsPath\n";
}

// Test uploads directory
$uploadsPath = __DIR__ . '/uploads';
if (is_dir($uploadsPath)) {
    echo "✓ Uploads directory exists: $uploadsPath\n";
} else {
    echo "✗ Uploads directory not found: $uploadsPath\n";
}

echo "\n=== Test Complete ===\n";
echo "Visit http://localhost:8000 to test the application\n";
?>
