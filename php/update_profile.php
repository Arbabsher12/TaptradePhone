<?php
session_start();

 if (!isset($_SESSION['user_id'])) {
     header('Location: login.php');
     exit;
}

// Database connection
include __DIR__ . '/db.php';

$user_id =  $_SESSION['user_id'];

// Process form data
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';

// Validate inputs
if (empty($name) || empty($email)) {
    $_SESSION['error'] = "Name and email are required fields.";
    header('Location: profile.php');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = "Invalid email format.";
    header('Location: profile.php');
    exit;
}

// Handle file upload
$profile_picture = null;
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $filename = $_FILES['profile_picture']['name'];
    $filetype = pathinfo($filename, PATHINFO_EXTENSION);
    
    // Verify file extension
    if (in_array(strtolower($filetype), $allowed)) {
        // Create unique filename
        $new_filename = uniqid() . '.' . $filetype;
        $upload_dir = 'uploads/';
        
        // Create directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $upload_path = $upload_dir . $new_filename;
        
        // Move the file
        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_path)) {
            $profile_picture = $upload_path;
        } else {
            $_SESSION['error'] = "Failed to upload file.";
            header('Location: profile.php');
            exit;
        }
    } else {
        $_SESSION['error'] = "Invalid file type. Only JPG, JPEG, PNG and GIF are allowed.";
        header('Location: profile.php');
        exit;
    }
}

// Update user data in database
if ($profile_picture) {
    $sql = "UPDATE users SET name = ?, email = ?, phone = ?, profile_picture = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $name, $email, $phone, $profile_picture, $user_id);
} else {
    $sql = "UPDATE users SET name = ?, email = ?, phone = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $name, $email, $phone, $user_id);
}

if ($stmt->execute()) {
    $_SESSION['success'] = "Profile updated successfully.";
} else {
    $_SESSION['error'] = "Error updating profile: " . $conn->error;
}

$stmt->close();
$conn->close();

// Redirect back to profile page
header('Location: ../html/profile.php');
exit;
