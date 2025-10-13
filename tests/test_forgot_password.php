<?php
/**
 * Test file for Forgot Password Functionality
 * This file helps test the forgot password flow
 */

echo "<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .test-section { margin: 20px 0; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .success { background-color: #d4edda; border-color: #c3e6cb; }
        .error { background-color: #f8d7da; border-color: #f5c6cb; }
        .info { background-color: #d1ecf1; border-color: #bee5eb; }
        a { color: #007bff; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h1>Forgot Password Functionality Test</h1>
    
    <div class='test-section info'>
        <h2>Test Steps</h2>
        <ol>
            <li>Click on 'Forgot Password' link on login page</li>
            <li>Enter a valid email address that exists in the database</li>
            <li>Check email for verification code</li>
            <li>Enter the 6-digit verification code on the OTP verification page</li>
            <li>Set your new password on the new password page</li>
            <li>Try logging in with new password</li>
        </ol>
    </div>
    
    <div class='test-section'>
        <h2>Test Links</h2>
        <p><a href='/login' target='_blank'>Login Page (with Forgot Password link)</a></p>
        <p><a href='/forgot-password' target='_blank'>Forgot Password Page</a></p>
        <p><a href='/verify-reset-otp' target='_blank'>OTP Verification Page (requires valid session)</a></p>
        <p><a href='/new-password' target='_blank'>New Password Page (requires verified OTP)</a></p>
        <p><a href='/reset-password' target='_blank'>Legacy Reset Password Page (requires valid session)</a></p>
    </div>
    
    <div class='test-section'>
        <h2>Database Check</h2>";

// Check if database connection works
try {
    include 'php/db.php';
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        echo "<div class='error'>Database connection failed: " . $conn->connect_error . "</div>";
    } else {
        echo "<div class='success'>Database connection successful</div>";
        
        // Check if otp_verification table exists
        $result = $conn->query("SHOW TABLES LIKE 'otp_verification'");
        if ($result->num_rows > 0) {
            echo "<div class='success'>OTP verification table exists</div>";
        } else {
            echo "<div class='error'>OTP verification table does not exist</div>";
        }
        
        // Check if users table exists
        $result = $conn->query("SHOW TABLES LIKE 'users'");
        if ($result->num_rows > 0) {
            echo "<div class='success'>Users table exists</div>";
            
            // Show sample users (for testing)
            $result = $conn->query("SELECT user_id, name, email FROM users LIMIT 5");
            if ($result->num_rows > 0) {
                echo "<h3>Sample Users (for testing):</h3><ul>";
                while ($row = $result->fetch_assoc()) {
                    echo "<li>ID: {$row['user_id']}, Name: {$row['name']}, Email: {$row['email']}</li>";
                }
                echo "</ul>";
            }
        } else {
            echo "<div class='error'>Users table does not exist</div>";
        }
    }
    $conn->close();
} catch (Exception $e) {
    echo "<div class='error'>Error: " . $e->getMessage() . "</div>";
}

echo "</div>
    
    <div class='test-section'>
        <h2>Email Configuration Check</h2>";

// Check Brevo configuration
try {
    include 'php/brevo_config.php';
    if (defined('BREVO_API_KEY') && BREVO_API_KEY !== 'YOUR_BREVO_API_KEY_HERE') {
        echo "<div class='success'>Brevo API key is configured</div>";
    } else {
        echo "<div class='error'>Brevo API key is not configured properly</div>";
    }
    
    if (defined('FROM_EMAIL') && !empty(FROM_EMAIL)) {
        echo "<div class='success'>From email is configured: " . FROM_EMAIL . "</div>";
    } else {
        echo "<div class='error'>From email is not configured</div>";
    }
} catch (Exception $e) {
    echo "<div class='error'>Error checking email configuration: " . $e->getMessage() . "</div>";
}

echo "</div>
    
    <div class='test-section info'>
        <h2>Notes</h2>
        <ul>
            <li>Make sure your Brevo API key is properly configured in php/brevo_config.php</li>
            <li>The OTP expires in " . (defined('OTP_EXPIRY_MINUTES') ? OTP_EXPIRY_MINUTES : '10') . " minutes</li>
            <li>Maximum OTP attempts: " . (defined('MAX_OTP_ATTEMPTS') ? MAX_OTP_ATTEMPTS : '3') . "</li>
            <li>Check your email spam folder if you don't receive the verification code</li>
        </ul>
    </div>
    
</body>
</html>";
?>
