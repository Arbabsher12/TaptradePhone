<?php
// Test router functionality step by step
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Router Test</h1>";

// Test 1: Basic router class
echo "<h2>Test 1: Router Class</h2>";
try {
    include_once 'router.php';
    echo "<p style='color: green;'>✓ Router class loaded</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Router error: " . $e->getMessage() . "</p>";
    exit;
}

// Test 2: Create router instance
echo "<h2>Test 2: Router Instance</h2>";
try {
    $router = new Router();
    echo "<p style='color: green;'>✓ Router instance created</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Router instance error: " . $e->getMessage() . "</p>";
    exit;
}

// Test 3: Add a simple route
echo "<h2>Test 3: Add Route</h2>";
try {
    $router->get('/test', function() {
        echo "<p>Test route works!</p>";
    });
    echo "<p style='color: green;'>✓ Route added</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Route add error: " . $e->getMessage() . "</p>";
    exit;
}

// Test 4: Check REQUEST_URI
echo "<h2>Test 4: Request Info</h2>";
echo "<p>REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'Not set') . "</p>";
echo "<p>REQUEST_METHOD: " . ($_SERVER['REQUEST_METHOD'] ?? 'Not set') . "</p>";

// Test 5: Try dispatch (this might be where it fails)
echo "<h2>Test 5: Dispatch</h2>";
try {
    // Simulate a simple request
    $_SERVER['REQUEST_URI'] = '/test';
    $_SERVER['REQUEST_METHOD'] = 'GET';
    
    $router->dispatch();
    echo "<p style='color: green;'>✓ Dispatch completed</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Dispatch error: " . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . "</p>";
    echo "<p>Line: " . $e->getLine() . "</p>";
} catch (Error $e) {
    echo "<p style='color: red;'>✗ Fatal error: " . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . "</p>";
    echo "<p>Line: " . $e->getLine() . "</p>";
}

echo "<h2>Test Complete</h2>";
?>
