<?php 
session_start();

// Clear any existing success messages when page loads (unless it's a redirect from form submission)
// Don't clear error messages as they should be shown even on page refresh
if (!isset($_POST['otp_code'])) {
    unset($_SESSION['otp_verification_success']);
    unset($_SESSION['otp_verification_timestamp']);
    unset($_SESSION['new_password_success']);
    unset($_SESSION['new_password_timestamp']);
}

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
    <title>Verify Code - Buy Sell Phone</title>
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
                <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                <h2>Verify Your Code</h2>
                <p class="text-muted">Enter the 6-digit verification code sent to <strong><?php echo htmlspecialchars($_SESSION['reset_email']); ?></strong></p>
                <div data-reset-email="<?php echo htmlspecialchars($_SESSION['reset_email']); ?>" style="display: none;"></div>
            </div>

            <?php if (!empty($_SESSION['otp_verification_errors'])): ?>
                <div class="alert alert-danger">
                    <?php 
                    foreach ($_SESSION['otp_verification_errors'] as $error) {
                        echo "<p class='mb-0'>$error</p>";
                    }
                    // Clear error messages after displaying them
                    unset($_SESSION['otp_verification_errors']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($_SESSION['otp_verification_success']) && isset($_SESSION['otp_verification_timestamp']) && (time() - $_SESSION['otp_verification_timestamp']) < 300): ?>
                <div class="alert alert-success">
                    <p class='mb-0'><?php 
                        echo $_SESSION['otp_verification_success']; 
                        unset($_SESSION['otp_verification_success']); 
                        unset($_SESSION['otp_verification_timestamp']);
                    ?></p>
                </div>
            <?php endif; ?>

            <form id="verifyOtpForm" method="POST">
                <div class="mb-3">
                    <label class="form-label">Verification Code</label>
                    <div class="otp-container">
                        <input type="text" name="otp_code" id="otp_code" class="otp-input" maxlength="6" required style="display: none;">
                        <div class="otp-boxes">
                            <input type="text" class="otp-box" maxlength="1" data-index="0">
                            <input type="text" class="otp-box" maxlength="1" data-index="1">
                            <input type="text" class="otp-box" maxlength="1" data-index="2">
                            <input type="text" class="otp-box" maxlength="1" data-index="3">
                            <input type="text" class="otp-box" maxlength="1" data-index="4">
                            <input type="text" class="otp-box" maxlength="1" data-index="5">
                        </div>
                    </div>
                    <div class="form-text">
                        <a href="#" id="resendCode" class="text-decoration-none">Didn't receive the code? Resend</a>
                    </div>
                </div>
                
                <button type="submit" class="btn login-btn w-100 mb-3" id="submitBtn">
                    <i class="fas fa-check me-2"></i>Verify Code
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
    <script>
        // OTP Box functionality
        document.addEventListener('DOMContentLoaded', function() {
            const otpBoxes = document.querySelectorAll('.otp-box');
            const hiddenInput = document.getElementById('otp_code');
            
            // Focus first box on load
            otpBoxes[0].focus();
            
            // Handle input in each box
            otpBoxes.forEach((box, index) => {
                box.addEventListener('input', function(e) {
                    // Only allow numbers
                    this.value = this.value.replace(/[^0-9]/g, '');
                    
                    // Add visual feedback
                    if (this.value) {
                        this.classList.add('filled');
                    } else {
                        this.classList.remove('filled');
                    }
                    
                    // Update hidden input
                    updateHiddenInput();
                    
                    // Move to next box if digit entered
                    if (this.value && index < otpBoxes.length - 1) {
                        otpBoxes[index + 1].focus();
                    }
                    
                    // Auto-submit when all boxes are filled
                    if (hiddenInput.value.length === 6) {
                        setTimeout(() => {
                            document.getElementById('verifyOtpForm').submit();
                        }, 100);
                    }
                });
                
                box.addEventListener('keydown', function(e) {
                    // Handle backspace
                    if (e.key === 'Backspace' && !this.value && index > 0) {
                        otpBoxes[index - 1].focus();
                    }
                    
                    // Handle arrow keys
                    if (e.key === 'ArrowLeft' && index > 0) {
                        otpBoxes[index - 1].focus();
                    }
                    if (e.key === 'ArrowRight' && index < otpBoxes.length - 1) {
                        otpBoxes[index + 1].focus();
                    }
                });
                
                box.addEventListener('paste', function(e) {
                    e.preventDefault();
                    const paste = (e.clipboardData || window.clipboardData).getData('text');
                    const numbers = paste.replace(/[^0-9]/g, '').slice(0, 6);
                    
                    // Fill boxes with pasted numbers
                    numbers.split('').forEach((digit, i) => {
                        if (otpBoxes[i]) {
                            otpBoxes[i].value = digit;
                            otpBoxes[i].classList.add('filled');
                        }
                    });
                    
                    updateHiddenInput();
                    
                    // Focus next empty box or last box
                    const nextIndex = Math.min(numbers.length, otpBoxes.length - 1);
                    otpBoxes[nextIndex].focus();
                    
                    // Auto-submit if all filled
                    if (hiddenInput.value.length === 6) {
                        setTimeout(() => {
                            document.getElementById('verifyOtpForm').submit();
                        }, 100);
                    }
                });
            });
            
            function updateHiddenInput() {
                const otpValue = Array.from(otpBoxes).map(box => box.value).join('');
                hiddenInput.value = otpValue;
            }
        });

        // Form submission
        document.getElementById('verifyOtpForm').addEventListener('submit', function(e) {
            const otpCode = document.getElementById('otp_code').value;
            const submitBtn = document.getElementById('submitBtn');
            const originalContent = submitBtn.innerHTML;

            // Validate OTP code
            if (!/^\d{6}$/.test(otpCode)) {
                e.preventDefault();
                alert('Please enter a valid 6-digit verification code.');
                return;
            }

            // Add loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Verifying...';
            submitBtn.disabled = true;
        });
    </script>
</body>
</html>
