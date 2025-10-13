// Forgot Password JavaScript Functionality

document.addEventListener('DOMContentLoaded', function() {
    // Initialize forgot password functionality
    initializeForgotPassword();
});

function initializeForgotPassword() {
    // Handle forgot password form submission
    const forgotPasswordForm = document.getElementById('forgotPasswordForm');
    if (forgotPasswordForm) {
        forgotPasswordForm.addEventListener('submit', handleForgotPasswordSubmit);
    }

    // Handle OTP verification form submission
    const verifyOtpForm = document.getElementById('verifyOtpForm');
    if (verifyOtpForm) {
        verifyOtpForm.addEventListener('submit', handleVerifyOtpSubmit);
        addOTPInputFormatting();
    }

    // Handle new password form submission
    const newPasswordForm = document.getElementById('newPasswordForm');
    if (newPasswordForm) {
        newPasswordForm.addEventListener('submit', handleNewPasswordSubmit);
        addPasswordStrengthIndicator();
    }

    // Handle reset password form submission (legacy)
    const resetPasswordForm = document.getElementById('resetPasswordForm');
    if (resetPasswordForm) {
        resetPasswordForm.addEventListener('submit', handleResetPasswordSubmit);
        addPasswordStrengthIndicator();
        addOTPInputFormatting();
    }

    // Handle resend code functionality
    const resendCodeLink = document.getElementById('resendCode');
    if (resendCodeLink) {
        resendCodeLink.addEventListener('click', handleResendCode);
    }
}

function handleForgotPasswordSubmit(e) {
    const submitBtn = document.getElementById('submitBtn');
    const emailInput = document.getElementById('email');

    // Validate email
    if (!validateEmail(emailInput.value)) {
        e.preventDefault();
        showError('Please enter a valid email address.');
        return;
    }

    // Add loading state
    const originalContent = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';
    submitBtn.disabled = true;

    // Re-enable button after timeout in case of error
    setTimeout(() => {
        submitBtn.innerHTML = originalContent;
        submitBtn.disabled = false;
    }, 10000);
}

function handleVerifyOtpSubmit(e) {
    const otpCode = document.getElementById('otp_code').value;
    const submitBtn = document.getElementById('submitBtn');

    // Validate OTP code
    if (!validateOTPCode(otpCode)) {
        e.preventDefault();
        showError('Please enter a valid 6-digit verification code.');
        return;
    }

    // Add loading state
    const originalContent = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Verifying...';
    submitBtn.disabled = true;
}

function handleNewPasswordSubmit(e) {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    const submitBtn = document.getElementById('submitBtn');

    // Validate password
    if (!validatePassword(newPassword)) {
        e.preventDefault();
        showError('Password must be at least 8 characters long.');
        return;
    }

    // Validate password confirmation
    if (newPassword !== confirmPassword) {
        e.preventDefault();
        showError('Passwords do not match.');
        return;
    }

    // Add loading state
    const originalContent = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
    submitBtn.disabled = true;
}

function handleResetPasswordSubmit(e) {
    const otpCode = document.getElementById('otp_code').value;
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    const submitBtn = document.getElementById('submitBtn');

    // Validate OTP code
    if (!validateOTPCode(otpCode)) {
        e.preventDefault();
        showError('Please enter a valid 6-digit verification code.');
        return;
    }

    // Validate password
    if (!validatePassword(newPassword)) {
        e.preventDefault();
        showError('Password must be at least 8 characters long.');
        return;
    }

    // Validate password confirmation
    if (newPassword !== confirmPassword) {
        e.preventDefault();
        showError('Passwords do not match.');
        return;
    }

    // Add loading state
    const originalContent = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Resetting...';
    submitBtn.disabled = true;
}

function handleResendCode(e) {
    e.preventDefault();

    const resendLink = e.target;
    const originalText = resendLink.textContent;

    // Add loading state
    resendLink.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Sending...';
    resendLink.style.pointerEvents = 'none';

    // Get email from session (this would need to be passed from PHP)
    const email = getResetEmail();

    if (!email) {
        showError('Unable to resend code. Please start the password reset process again.');
        resetResendLink(resendLink, originalText);
        return;
    }

    // Send AJAX request to resend OTP
    fetch('/php/otp_handler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'send_otp',
                email: email,
                purpose: 'password_reset'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                resendLink.innerHTML = '<i class="fas fa-check me-1"></i>Code sent!';
                showSuccess('Verification code sent successfully!');

                setTimeout(() => {
                    resetResendLink(resendLink, originalText);
                }, 3000);
            } else {
                showError('Failed to resend code: ' + data.error);
                resetResendLink(resendLink, originalText);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('An error occurred while resending the code.');
            resetResendLink(resendLink, originalText);
        });
}

function addPasswordStrengthIndicator() {
    const passwordInput = document.getElementById('new_password');
    if (!passwordInput) return;

    // Find existing strength indicator in the password input container
    const passwordContainer = passwordInput.closest('.password-input-container');
    const strengthIndicator = passwordContainer ? passwordContainer.querySelector('.password-strength') : null;

    if (!strengthIndicator) {
        console.log('Password strength indicator not found');
        return;
    }

    // Clear any existing event listeners by cloning the element
    const newPasswordInput = passwordInput.cloneNode(true);
    passwordInput.parentNode.replaceChild(newPasswordInput, passwordInput);

    newPasswordInput.addEventListener('input', function() {
        const password = this.value;
        const strength = calculatePasswordStrength(password);

        // Update strength indicator
        strengthIndicator.className = 'password-strength ' + strength.level;

        // Update form text
        const formText = this.closest('.mb-3').querySelector('.form-text');
        if (formText) {
            formText.textContent = strength.message;
        }
    });
}

function addOTPInputFormatting() {
    const otpInput = document.getElementById('otp_code');
    if (!otpInput) return;

    otpInput.addEventListener('input', function() {
        // Remove non-numeric characters
        this.value = this.value.replace(/[^0-9]/g, '');

        // Limit to 6 digits
        if (this.value.length > 6) {
            this.value = this.value.slice(0, 6);
        }

        // Auto-submit when 6 digits are entered (optional)
        if (this.value.length === 6) {
            // Focus on password field
            const passwordInput = document.getElementById('new_password');
            if (passwordInput) {
                passwordInput.focus();
            }
        }
    });

    // Handle paste events
    otpInput.addEventListener('paste', function(e) {
        setTimeout(() => {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
        }, 10);
    });
}

// Utility Functions
function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function validateOTPCode(code) {
    return /^\d{6}$/.test(code);
}

function validatePassword(password) {
    return password.length >= 8;
}

function calculatePasswordStrength(password) {
    let score = 0;
    let message = '';

    if (password.length >= 8) score++;
    if (password.length >= 12) score++;
    if (/[a-z]/.test(password)) score++;
    if (/[A-Z]/.test(password)) score++;
    if (/[0-9]/.test(password)) score++;
    if (/[^A-Za-z0-9]/.test(password)) score++;

    if (score < 2) {
        return { level: 'weak', message: 'Weak password' };
    } else if (score < 4) {
        return { level: 'fair', message: 'Fair password' };
    } else if (score < 5) {
        return { level: 'good', message: 'Good password' };
    } else {
        return { level: 'strong', message: 'Strong password' };
    }
}

function getResetEmail() {
    // This would need to be passed from PHP or stored in a data attribute
    const emailElement = document.querySelector('[data-reset-email]');
    return emailElement ? emailElement.getAttribute('data-reset-email') : null;
}

function resetResendLink(link, originalText) {
    link.textContent = originalText;
    link.style.pointerEvents = 'auto';
}

function showError(message) {
    // Create or update error alert
    let errorAlert = document.querySelector('.alert-danger');
    if (!errorAlert) {
        errorAlert = document.createElement('div');
        errorAlert.className = 'alert alert-danger';
        const form = document.querySelector('form');
        form.parentNode.insertBefore(errorAlert, form);
    }

    errorAlert.innerHTML = '<p class="mb-0">' + message + '</p>';
    errorAlert.style.display = 'block';

    // Auto-hide after 5 seconds
    setTimeout(() => {
        errorAlert.style.display = 'none';
    }, 5000);
}

function showSuccess(message) {
    // Create or update success alert
    let successAlert = document.querySelector('.alert-success');
    if (!successAlert) {
        successAlert = document.createElement('div');
        successAlert.className = 'alert alert-success';
        const form = document.querySelector('form');
        form.parentNode.insertBefore(successAlert, form);
    }

    successAlert.innerHTML = '<p class="mb-0">' + message + '</p>';
    successAlert.style.display = 'block';

    // Auto-hide after 3 seconds
    setTimeout(() => {
        successAlert.style.display = 'none';
    }, 3000);
}

// Toggle password visibility (if not already defined)
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