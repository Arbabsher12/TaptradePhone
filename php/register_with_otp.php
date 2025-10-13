<?php
// Registration with OTP verification (Updated for separate OTP page)
session_start();

// Database connection
include 'db.php';
include 'brevo_config.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to clean expired OTPs
function cleanExpiredOTPs($conn) {
    $sql = "DELETE FROM otp_verification WHERE expires_at < NOW()";
    $conn->query($sql);
}

// Function to verify OTP (or check if already verified)
function verifyOTP($conn, $email, $otp_code) {
    cleanExpiredOTPs($conn);
    
    // Check if OTP exists and is either unverified or already verified
    $sql = "SELECT id, is_verified FROM otp_verification WHERE email = ? AND otp_code = ? AND purpose = 'registration' AND expires_at > NOW()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $otp_code);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $stmt->close();
        return false;
    }
    
    $row = $result->fetch_assoc();
    
    // If already verified, return true
    if ($row['is_verified'] == 1) {
        $stmt->close();
        return true;
    }
    
    // Mark as verified
    $update_sql = "UPDATE otp_verification SET is_verified = 1, verified_at = NOW() WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("i", $row['id']);
    $update_stmt->execute();
    $update_stmt->close();
    $stmt->close();
    
    return true;
}

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $password = $_POST["password"];
    $password_confirmation = $_POST["password_confirmation"];
    $otp_code = trim($_POST["otp_code"] ?? '');
    
    // Initialize errors array
    $errors = [];
    
    // Validate name
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    
    // Validate email
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    } else {
        // Check if email already exists
        $sql = "SELECT user_id FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $errors[] = "Email already exists";
        }
        
        $stmt->close();
    }
    
    // Validate phone
    if (empty($phone)) {
        $errors[] = "Phone number is required";
    }
    
    // Validate password
    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters";
    }
    
    // Validate password confirmation
    if ($password !== $password_confirmation) {
        $errors[] = "Passwords do not match";
    }
    
    // Validate OTP
    if (empty($otp_code)) {
        $errors[] = "OTP code is required";
    } elseif (!verifyOTP($conn, $email, $otp_code)) {
        $errors[] = "Invalid or expired OTP code";
    }
    
    // Process profile image if uploaded
    $profile_image_path = null;
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['profile_image']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        // Verify file extension
        if (!in_array(strtolower($filetype), $allowed)) {
            $errors[] = "Only JPG, JPEG, PNG, and GIF files are allowed";
        } else {
            // Create unique filename
            $new_filename = uniqid() . '.' . $filetype;
            $upload_dir = '../uploads/profile_images/';
            
            // Create directory if it doesn't exist
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $upload_path = $upload_dir . $new_filename;
            
            // Move uploaded file
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_path)) {
                $profile_image_path = 'uploads/profile_images/' . $new_filename;
            } else {
                $errors[] = "Failed to upload image";
            }
        }
    }
    
    // Handle profile image from base64 data (from OTP page)
    if (isset($_POST['profile_image_data']) && !empty($_POST['profile_image_data'])) {
        $image_data = $_POST['profile_image_data'];
        
        // Extract image data and extension
        if (preg_match('/^data:image\/(\w+);base64,/', $image_data, $type)) {
            $image_data = substr($image_data, strpos($image_data, ',') + 1);
            $image_data = base64_decode($image_data);
            
            if ($image_data !== false) {
                $extension = $type[1];
                $new_filename = uniqid() . '.' . $extension;
                $upload_dir = '../uploads/profile_images/';
                
                // Create directory if it doesn't exist
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $upload_path = $upload_dir . $new_filename;
                
                if (file_put_contents($upload_path, $image_data)) {
                    $profile_image_path = 'uploads/profile_images/' . $new_filename;
                }
            }
        }
    }
    
    // If no errors, proceed with registration
    if (empty($errors)) {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Prepare SQL statement
        $sql = "INSERT INTO users (name, email, phone, password, profile_picture, email_verified) VALUES (?, ?, ?, ?, ?, 1)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $name, $email, $phone, $hashed_password, $profile_image_path);
        
        // Execute statement
        if ($stmt->execute()) {
            // Registration successful
            $_SESSION['success_message'] = "Registration successful! Your email has been verified. You can now log in.";
            header("Location: /login");
            exit();
        } else {
            // Registration failed
            $errors[] = "Registration failed: " . $conn->error;
        }
        
        $stmt->close();
    }
    
    // If there are errors, store them in session and redirect back to signup page
    if (!empty($errors)) {
        $_SESSION['signup_errors'] = $errors;
        header("Location: /signup");
        exit();
    }
}

$conn->close();
?>