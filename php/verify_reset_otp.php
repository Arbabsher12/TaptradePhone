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
    $email = $_SESSION['reset_email'];

    // Validate inputs
    if (empty($otp_code)) {
        $errors[] = "Verification code is required.";
    } elseif (strlen($otp_code) !== 6 || !ctype_digit($otp_code)) {
        $errors[] = "Verification code must be 6 digits.";
    }

    if (empty($errors)) {
        // Verify OTP
        $otp_result = verifyOTP($conn, $email, $otp_code, 'password_reset');
        
        if ($otp_result['success']) {
            // OTP verified successfully, mark as verified in session
            $_SESSION['otp_verified'] = true;
            $_SESSION['otp_verification_success'] = "Code verified successfully! Now you can set your new password.";
            $_SESSION['otp_verification_timestamp'] = time();
            header("Location: /new-password");
            exit();
        } else {
            $errors[] = $otp_result['error'];
            // Debug: Log the error for troubleshooting
            error_log("OTP Verification Error: " . $otp_result['error']);
        }
    }
}

// If there are errors, redirect back to OTP verification page
if (!empty($errors)) {
    $_SESSION['otp_verification_errors'] = $errors;
    header("Location: /verify-reset-otp");
    exit();
}

$conn->close();
?>
