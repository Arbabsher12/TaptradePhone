<?php 
session_start();

// Function to clear forgot password success messages and timestamps
// Don't clear error messages as they should be shown even on page refresh
function clearForgotPasswordSession() {
    unset($_SESSION['forgot_password_success']);
    unset($_SESSION['otp_verification_success']);
    unset($_SESSION['new_password_success']);
    unset($_SESSION['forgot_password_timestamp']);
    unset($_SESSION['otp_verification_timestamp']);
    unset($_SESSION['new_password_timestamp']);
}

// Clear any existing success messages when page loads (unless it's a redirect from form submission)
if (!isset($_POST['email'])) {
    clearForgotPasswordSession();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Buy Sell Phone</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="login-box">
            <div class="text-center mb-4">
                <i class="fas fa-key fa-3x text-primary mb-3"></i>
                <h2>Forgot Password</h2>
                <p class="text-muted">Enter your email address and we'll send you a verification code to reset your password.</p>
            </div>

            <?php if (!empty($_SESSION['forgot_password_errors'])): ?>
                <div class="alert alert-danger">
                    <?php 
                    foreach ($_SESSION['forgot_password_errors'] as $error) {
                        echo "<p class='mb-0'>$error</p>";
                    }
                    // Clear error messages after displaying them
                    unset($_SESSION['forgot_password_errors']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($_SESSION['forgot_password_success']) && isset($_SESSION['forgot_password_timestamp']) && (time() - $_SESSION['forgot_password_timestamp']) < 300): ?>
                <div class="alert alert-success">
                    <p class="mb-0"><?php 
                        echo $_SESSION['forgot_password_success']; 
                        unset($_SESSION['forgot_password_success']); 
                        unset($_SESSION['forgot_password_timestamp']);
                    ?></p>
                </div>
            <?php endif; ?>

            <form id="forgotPasswordForm" method="POST">
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-envelope"></i></span>
                        <input type="email" name="email" id="email" class="form-control border-start-0" placeholder="Enter your email address" required>
                    </div>
                </div>
                
                <button type="submit" class="btn login-btn w-100 mb-3" id="submitBtn">
                    <i class="fas fa-paper-plane me-2"></i>Send Verification Code
                </button>
            </form>

            <div class="text-center">
                <a href="/login" class="text-decoration-none">
                    <i class="fas fa-arrow-left me-1"></i>Back to Login
                </a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/forgot_password.js"></script>
</body>
</html>
