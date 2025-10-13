<?php 
session_start();

// Clear any existing success messages when page loads (unless it's a redirect from form submission)
// Don't clear error messages as they should be shown even on page refresh
if (!isset($_POST['new_password'])) {
    unset($_SESSION['new_password_success']);
    unset($_SESSION['new_password_timestamp']);
}

// Check if user has valid reset session and verified OTP
if (!isset($_SESSION['reset_email']) || !isset($_SESSION['reset_user_id']) || !isset($_SESSION['otp_verified'])) {
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
    <title>New Password - Buy Sell Phone</title>
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
                <i class="fas fa-key fa-3x text-success mb-3"></i>
                <h2>Set New Password</h2>
                <p class="text-muted">Create a strong password for your account <strong><?php echo htmlspecialchars($_SESSION['reset_email']); ?></strong></p>
            </div>

            <?php if (!empty($_SESSION['new_password_errors'])): ?>
                <div class="alert alert-danger">
                    <?php 
                    foreach ($_SESSION['new_password_errors'] as $error) {
                        echo "<p class='mb-0'>$error</p>";
                    }
                    unset($_SESSION['new_password_errors']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($_SESSION['new_password_success'])): ?>
                <div class="alert alert-success">
                    <p class='mb-0'><?php echo $_SESSION['new_password_success']; unset($_SESSION['new_password_success']); ?></p>
                </div>
            <?php endif; ?>

            <form id="newPasswordForm" method="POST">
                <div class="mb-3">
                    <label for="new_password" class="form-label">New Password</label>
                    <div class="password-input-container">
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-lock"></i></span>
                            <input type="password" name="new_password" id="new_password" class="form-control border-start-0" placeholder="Enter new password" required>
                            <span class="input-group-text bg-transparent border-start-0 toggle-password" onclick="togglePassword('new_password')">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                        <div class="password-strength"></div>
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
                    <i class="fas fa-save me-2"></i>Update Password
                </button>
            </form>

            <div class="text-center">
                <a href="/verify-reset-otp" class="text-decoration-none">
                    <i class="fas fa-arrow-left me-1"></i>Back to Verify Code
                </a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/forgot_password.js"></script>
    <script>
        // Toggle password visibility
        function togglePassword(fieldId) {
            const passwordInput = document.getElementById(fieldId);
            const toggleIcon = passwordInput.parentElement.querySelector('.toggle-password i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Form validation
        document.getElementById('newPasswordForm').addEventListener('submit', function(e) {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const submitBtn = document.getElementById('submitBtn');
            const originalContent = submitBtn.innerHTML;

            // Validate password length
            if (newPassword.length < 8) {
                e.preventDefault();
                alert('Password must be at least 8 characters long.');
                return;
            }

            // Validate password confirmation
            if (newPassword !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match.');
                return;
            }

            // Add loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
            submitBtn.disabled = true;
        });
    </script>
</body>
</html>
