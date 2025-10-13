<?php
session_start();
include 'db.php';
include 'brevo_config.php';
include 'otp_functions.php';

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    // Validate email
    if (empty($email)) {
        $errors[] = "Email address is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($errors)) {
        // Check if email exists in users table
        $stmt = $conn->prepare("SELECT user_id, name FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id, $name);
            $stmt->fetch();
            $stmt->close();

            // Generate and send OTP for password reset
            $result = generateAndSendOTP($conn, $email, 'password_reset');
            
            if ($result['success']) {
                // Store email in session for the reset process
                $_SESSION['reset_email'] = $email;
                $_SESSION['reset_user_id'] = $user_id;
                $_SESSION['reset_user_name'] = $name;
                
                $_SESSION['forgot_password_success'] = "Verification code sent to your email address. Please check your inbox and enter the code to verify your identity.";
                $_SESSION['forgot_password_timestamp'] = time();
                header("Location: /verify-reset-otp");
                exit();
            } else {
                $errors[] = $result['error'];
            }
        } else {
            $errors[] = "No account found with this email address.";
        }
    }
}

// If there are errors, redirect back to forgot password page
if (!empty($errors)) {
    $_SESSION['forgot_password_errors'] = $errors;
    header("Location: /forgot-password");
    exit();
}

$conn->close();
?>
