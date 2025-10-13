<?php 
session_start();

// Check if user has valid reset session
if (!isset($_SESSION['reset_email']) || !isset($_SESSION['reset_user_id'])) {
    $_SESSION['forgot_password_errors'] = ['Invalid or expired reset session. Please request a new password reset.'];
    header("Location: /forgot-password");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Buy Sell Phone</title>
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
                <i class="fas fa-shield-alt fa-3x text-success mb-3"></i>
                <h2>Reset Password</h2>
                <p class="text-muted">Enter the verification code sent to <strong><?php echo htmlspecialchars($_SESSION['reset_email']); ?></strong> and your new password.</p>
                <div data-reset-email="<?php echo htmlspecialchars($_SESSION['reset_email']); ?>" style="display: none;"></div>
            </div>

            <?php if (!empty($_SESSION['reset_password_errors'])): ?>
                <div class="alert alert-danger">
                    <?php 
                    foreach ($_SESSION['reset_password_errors'] as $error) {
                        echo "<p class='mb-0'>$error</p>";
                    }
                    unset($_SESSION['reset_password_errors']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($_SESSION['reset_password_success'])): ?>
                <div class="alert alert-success">
                    <p class='mb-0'><?php echo $_SESSION['reset_password_success']; unset($_SESSION['reset_password_success']); ?></p>
                </div>
            <?php endif; ?>

            <form id="resetPasswordForm" method="POST">
                <div class="mb-3">
                    <label for="otp_code" class="form-label">Verification Code</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-key"></i></span>
                        <input type="text" name="otp_code" id="otp_code" class="form-control border-start-0" placeholder="Enter 6-digit code" maxlength="6" required>
                    </div>
                    <div class="form-text">
                        <a href="#" id="resendCode" class="text-decoration-none">Didn't receive the code? Resend</a>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="new_password" class="form-label">New Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-lock"></i></span>
                        <input type="password" name="new_password" id="new_password" class="form-control border-start-0" placeholder="Enter new password" required>
                        <span class="input-group-text bg-transparent border-start-0 toggle-password" onclick="togglePassword('new_password')">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                    <div class="form-text">Password must be at least 8 characters long.</div>
                </div>

                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-lock"></i></span>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control border-start-0" placeholder="Confirm new password" required>
                        <span class="input-group-text bg-transparent border-start-0 toggle-password" onclick="togglePassword('confirm_password')">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                </div>
                
                <button type="submit" class="btn login-btn w-100 mb-3" id="submitBtn">
                    <i class="fas fa-save me-2"></i>Reset Password
                </button>
            </form>

            <div class="text-center">
                <a href="/forgot-password" class="text-decoration-none">
                    <i class="fas fa-arrow-left me-1"></i>Back to Forgot Password
                </a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/forgot_password.js"></script>
</body>
</html>
