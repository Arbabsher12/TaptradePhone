<?php
// Test file to verify server configuration
echo "<h1>Server Configuration Test</h1>";
echo "<h2>Server Information:</h2>";
echo "<p><strong>Server Name:</strong> " . $_SERVER['SERVER_NAME'] . "</p>";
echo "<p><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p><strong>Request URI:</strong> " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p><strong>Script Name:</strong> " . $_SERVER['SCRIPT_NAME'] . "</p>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";

echo "<h2>File System Test:</h2>";
echo "<p><strong>Current Directory:</strong> " . getcwd() . "</p>";
echo "<p><strong>Index.php exists:</strong> " . (file_exists('index.php') ? 'Yes' : 'No') . "</p>";
echo "<p><strong>Router.php exists:</strong> " . (file_exists('router.php') ? 'Yes' : 'No') . "</p>";
echo "<p><strong>.htaccess exists:</strong> " . (file_exists('.htaccess') ? 'Yes' : 'No') . "</p>";

echo "<h2>Database Test:</h2>";
try {
    include 'db.php';
    if ($conn && $conn->ping()) {
        echo "<p style='color: green;'><strong>Database Connection:</strong> Success</p>";
    } else {
        echo "<p style='color: red;'><strong>Database Connection:</strong> Failed</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'><strong>Database Error:</strong> " . $e->getMessage() . "</p>";
}

echo "<h2>Links Test:</h2>";
echo "<p><a href='/'>Home</a></p>";
echo "<p><a href='/login'>Login</a></p>";
echo "<p><a href='/sellYourPhone'>Sell Phone</a></p>";
?>
