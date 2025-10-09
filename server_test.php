<?php
// Server compatibility test
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Server Compatibility Test</h1>";
echo "<p>If you can see this page, PHP is working on your server.</p>";

echo "<h2>Server Information:</h2>";
echo "<p><strong>Server Software:</strong> " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "</p>";
echo "<p><strong>Document Root:</strong> " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . "</p>";
echo "<p><strong>Script Name:</strong> " . ($_SERVER['SCRIPT_NAME'] ?? 'Unknown') . "</p>";
echo "<p><strong>Request URI:</strong> " . ($_SERVER['REQUEST_URI'] ?? 'Unknown') . "</p>";
echo "<p><strong>HTTP Host:</strong> " . ($_SERVER['HTTP_HOST'] ?? 'Unknown') . "</p>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";

echo "<h2>File System Test:</h2>";
echo "<p><strong>Current Directory:</strong> " . getcwd() . "</p>";
echo "<p><strong>Index.php exists:</strong> " . (file_exists('index.php') ? 'Yes' : 'No') . "</p>";
echo "<p><strong>.htaccess exists:</strong> " . (file_exists('.htaccess') ? 'Yes' : 'No') . "</p>";

echo "<h2>URL Rewriting Test:</h2>";
echo "<p>Testing if URL rewriting is working...</p>";
echo "<p><a href='/'>Test Home Page</a></p>";
echo "<p><a href='/debug'>Test Debug Page</a></p>";

echo "<h2>Direct File Access Test:</h2>";
echo "<p><a href='index.php'>Direct access to index.php</a></p>";
echo "<p><a href='minimal_home.php'>Direct access to minimal_home.php</a></p>";

echo "<h2>Apache Modules (if available):</h2>";
if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    if (in_array('mod_rewrite', $modules)) {
        echo "<p style='color: green;'>✓ mod_rewrite is enabled</p>";
    } else {
        echo "<p style='color: red;'>✗ mod_rewrite is NOT enabled</p>";
    }
} else {
    echo "<p>Apache modules info not available</p>";
}

echo "<h2>Next Steps:</h2>";
echo "<ol>";
echo "<li>If you can access this page directly, PHP is working</li>";
echo "<li>Try clicking the direct file access links above</li>";
echo "<li>If direct access works but clean URLs don't, .htaccess/mod_rewrite is the issue</li>";
echo "<li>If nothing works, check file permissions and upload paths</li>";
echo "</ol>";
?>
