<?php
session_start();

// Include database connection and Google config
include 'php/db.php';
include 'php/google_config.php';

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if we have an authorization code
if (!isset($_GET['code'])) {
    // No code received, redirect back to signup with error
    $_SESSION['signup_errors'] = ['Google authentication failed. Please try again.'];
    header("Location: /signup");
    exit();
}

$code = $_GET['code'];

try {
    // Exchange code for access token
    $token_data = getGoogleAccessToken($code);
    
    if (!$token_data || isset($token_data['error'])) {
        throw new Exception('Failed to get access token from Google');
    }
    
    $access_token = $token_data['access_token'];
    
    // Get user information from Google
    $user_info = getGoogleUserInfo($access_token);
    
    if (!$user_info || isset($user_info['error'])) {
        error_log("Google OAuth Error: Failed to get user info. Response: " . print_r($user_info, true));
        throw new Exception('Failed to get user information from Google: ' . (isset($user_info['error']) ? $user_info['error'] : 'Unknown error'));
    }
    
    // Debug: Log the user info received from Google
    error_log("Google OAuth: User info received: " . print_r($user_info, true));
    
    // Extract user data
    $google_id = $user_info['id'];
    $name = $user_info['name'];
    $email = $user_info['email'];
    $profile_picture = isset($user_info['picture']) ? $user_info['picture'] : null;
    
    // Debug: Log profile picture URL
    error_log("Google OAuth: Profile picture URL: " . $profile_picture);
    
    // Ensure profile picture URL is properly formatted
    if ($profile_picture && !empty($profile_picture)) {
        // Remove any existing size parameter and add a standard one
        $profile_picture = preg_replace('/=s\d+-c$/', '', $profile_picture);
        $profile_picture = $profile_picture . '=s96-c';
        error_log("Google OAuth: Formatted profile picture URL: " . $profile_picture);
    }
    
    // Check if user already exists by email
    $sql = "SELECT user_id, name, email, profile_picture, login_method, google_id FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // User exists, check how they registered
        $user = $result->fetch_assoc();
        
        // Check if user was registered with email/password (not Google)
        if (!$user['google_id'] && (!$user['login_method'] || $user['login_method'] === 'email')) {
            // User exists with email/password registration, show error message
            $stmt->close();
            $conn->close();
            
            // Log the attempt for debugging
            error_log("Google OAuth: User attempted to sign up with Google using existing email: " . $email);
            
            $_SESSION['signup_errors'] = ['An account with this email already exists. Please sign in with your email and password instead, or use a different Google account.'];
            header("Location: /login");
            exit();
        }
        
        // User exists and was registered with Google, log them in
        // Update Google ID and login method if columns exist
        try {
            $update_sql = "UPDATE users SET google_id = ?, login_method = 'google' WHERE user_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("si", $google_id, $user['user_id']);
            $update_stmt->execute();
            $update_stmt->close();
        } catch (Exception $e) {
            // Columns might not exist yet, that's okay
            error_log("Could not update Google ID: " . $e->getMessage());
        }
        
        // Update profile picture if it's different and user doesn't have one
        if ($profile_picture && (!$user['profile_picture'] || $user['profile_picture'] === '/Components/NoDp.png')) {
            $update_sql = "UPDATE users SET profile_picture = ? WHERE user_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("si", $profile_picture, $user['user_id']);
            $update_stmt->execute();
            $update_stmt->close();
        }
        
        // Set session variables
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_profile_picture'] = $user['profile_picture'];
        $_SESSION['login_method'] = 'google';
        
        // Debug: Log the profile picture URL
        error_log("Google OAuth: Existing user profile picture: " . $user['profile_picture']);
        
        $stmt->close();
        $conn->close();
        
        // Redirect to home page with welcome message
        $_SESSION['success_message'] = "Welcome back! You have been logged in successfully.";
        header("Location: /");
        exit();
        
    } else {
        // New user, create account
        // Try to insert with new columns first, fallback to old schema
        try {
            $sql = "INSERT INTO users (name, email, google_id, login_method, profile_picture) VALUES (?, ?, ?, 'google', ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $name, $email, $google_id, $profile_picture);
        } catch (Exception $e) {
            // Fallback to old schema if new columns don't exist
            $sql = "INSERT INTO users (name, email, phone, password, profile_picture) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $phone = '000-000-0000'; // Placeholder for Google users
            $oauth_password = 'oauth_google_' . $google_id;
            $hashed_password = password_hash($oauth_password, PASSWORD_DEFAULT);
            $stmt->bind_param("sssss", $name, $email, $phone, $hashed_password, $profile_picture);
        }
        
        if ($stmt->execute()) {
            // Get the new user ID
            $new_user_id = $conn->insert_id;
            
        // Set session variables
        $_SESSION['user_id'] = $new_user_id;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_profile_picture'] = $profile_picture;
        $_SESSION['login_method'] = 'google';
        
        // Debug: Log the profile picture URL
        error_log("Google OAuth: New user profile picture set to: " . $profile_picture);
            
            $stmt->close();
            $conn->close();
            
            // Redirect to home page with success message
            $_SESSION['success_message'] = "Welcome! Your account has been created successfully with Google.";
            header("Location: /");
            exit();
            
        } else {
            throw new Exception('Failed to create user account: ' . $conn->error);
        }
    }
    
} catch (Exception $e) {
    // Log error and redirect with error message
    error_log("Google OAuth Error: " . $e->getMessage());
    $_SESSION['signup_errors'] = ['Google authentication failed. Please try again or use email signup.'];
    header("Location: /signup");
    exit();
}

$conn->close();
?>
