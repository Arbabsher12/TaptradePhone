<?php
// Simple test to isolate the issue
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Simple Test</h1>";
echo "<p>This is a basic test.</p>";

// Test if we can include router
echo "<h2>Testing Router Include:</h2>";
try {
    // Don't execute dispatch, just include the class
    include_once 'router.php';
    echo "<p style='color: green;'>Router class loaded successfully!</p>";
    
    // Test creating router instance
    $testRouter = new Router();
    echo "<p style='color: green;'>Router instance created successfully!</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Router error: " . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . "</p>";
    echo "<p>Line: " . $e->getLine() . "</p>";
} catch (Error $e) {
    echo "<p style='color: red;'>Fatal error: " . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . "</p>";
    echo "<p>Line: " . $e->getLine() . "</p>";
}

echo "<h2>Testing File Includes:</h2>";
$testFiles = ['html/home.php', 'db.php'];
foreach ($testFiles as $file) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>$file exists</p>";
    } else {
        echo "<p style='color: red;'>$file NOT FOUND</p>";
    }
}
?>
