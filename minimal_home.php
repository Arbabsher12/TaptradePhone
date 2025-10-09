<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phone Marketplace - Test</title>
</head>
<body>
    <h1>Minimal Home Page Test</h1>
    <p>This is a minimal test page to verify basic functionality.</p>
    <p>If you can see this, the basic routing is working.</p>
    
    <h2>Test Links:</h2>
    <ul>
        <li><a href="/login">Login</a></li>
        <li><a href="/signup">Sign Up</a></li>
        <li><a href="/sellYourPhone">Sell Phone</a></li>
        <li><a href="/debug.php">Debug Page</a></li>
    </ul>
    
    <h2>Server Info:</h2>
    <p>REQUEST_URI: <?php echo $_SERVER['REQUEST_URI'] ?? 'Not set'; ?></p>
    <p>REQUEST_METHOD: <?php echo $_SERVER['REQUEST_METHOD'] ?? 'Not set'; ?></p>
    <p>HTTP_HOST: <?php echo $_SERVER['HTTP_HOST'] ?? 'Not set'; ?></p>
</body>
</html>
