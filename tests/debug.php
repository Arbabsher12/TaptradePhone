<?php
// Debug file to test basic PHP functionality
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Debug Test</h1>";
echo "<p>PHP is working!</p>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Current Directory: " . getcwd() . "</p>";

// Test file includes
echo "<h2>File Tests:</h2>";
echo "<p>index.php exists: " . (file_exists('index.php') ? 'Yes' : 'No') . "</p>";
echo "<p>router.php exists: " . (file_exists('router.php') ? 'Yes' : 'No') . "</p>";
echo "<p>db.php exists: " . (file_exists('db.php') ? 'Yes' : 'No') . "</p>";

// Test database connection
echo "<h2>Database Test:</h2>";
try {
    include 'db.php';
    if ($conn && $conn->ping()) {
        echo "<p style='color: green;'>Database connection: SUCCESS</p>";
    } else {
        echo "<p style='color: red;'>Database connection: FAILED</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Database error: " . $e->getMessage() . "</p>";
}

// Test router
echo "<h2>Router Test:</h2>";
try {
    include 'router.php';
    echo "<p style='color: green;'>Router loaded: SUCCESS</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>Router error: " . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . "</p>";
    echo "<p>Line: " . $e->getLine() . "</p>";
}

echo "<h2>Server Info:</h2>";
echo "<p>REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'Not set') . "</p>";
echo "<p>REQUEST_METHOD: " . ($_SERVER['REQUEST_METHOD'] ?? 'Not set') . "</p>";
echo "<p>HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? 'Not set') . "</p>";
?>
