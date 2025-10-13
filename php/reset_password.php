<?php
session_start();
include 'db.php';
include 'brevo_config.php';
include 'otp_functions.php';

$errors = [];

// Check if user has valid reset session
if (!isset($_SESSION['reset_email']) || !isset($_SESSION['reset_user_id'])) {
    $_SESSION['forgot_password_errors'] = ['Invalid or expired reset session. Please request a new password reset.'];
    header("Location: /forgot-password");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $otp_code = trim($_POST['otp_code']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $email = $_SESSION['reset_email'];

    // Validate inputs
    if (empty($otp_code)) {
        $errors[] = "Verification code is required.";
    } elseif (strlen($otp_code) !== 6 || !ctype_digit($otp_code)) {
        $errors[] = "Verification code must be 6 digits.";
    }

    if (empty($new_password)) {
        $errors[] = "New password is required.";
    } elseif (strlen($new_password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }

    if (empty($confirm_password)) {
        $errors[] = "Password confirmation is required.";
    } elseif ($new_password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {
        // Verify OTP
        $otp_result = verifyOTP($conn, $email, $otp_code, 'password_reset');
        
        if ($otp_result['success']) {
            // OTP verified, now update the password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $user_id = $_SESSION['reset_user_id'];
            
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
            $stmt->bind_param("si", $hashed_password, $user_id);
            
            if ($stmt->execute()) {
                // Clear reset session
                unset($_SESSION['reset_email']);
                unset($_SESSION['reset_user_id']);
                unset($_SESSION['reset_user_name']);
                
                // Clear any existing OTP records for this email and purpose
                $cleanup_sql = "DELETE FROM otp_verification WHERE email = ? AND purpose = 'password_reset'";
                $cleanup_stmt = $conn->prepare($cleanup_sql);
                $cleanup_stmt->bind_param("s", $email);
                $cleanup_stmt->execute();
                $cleanup_stmt->close();
                
                $stmt->close();
                $conn->close();
                
                $_SESSION['reset_password_success'] = "Password reset successfully! You can now log in with your new password.";
                header("Location: /login");
                exit();
            } else {
                $errors[] = "Failed to update password. Please try again.";
            }
            
            $stmt->close();
        } else {
            $errors[] = $otp_result['error'];
        }
    }
}

// If there are errors, redirect back to reset password page
if (!empty($errors)) {
    $_SESSION['reset_password_errors'] = $errors;
    header("Location: /reset-password");
    exit();
}

$conn->close();
?>
