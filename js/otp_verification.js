document.addEventListener('DOMContentLoaded', function() {
    // Get elements
    const otpForm = document.getElementById('otp-form');
    const otpInputs = document.querySelectorAll('.otp-input');
    const verifyBtn = document.getElementById('verify-btn');
    const resendBtn = document.getElementById('resend-btn');
    const timerDisplay = document.getElementById('timer');
    const emailDisplay = document.getElementById('email-display');
    const alertContainer = document.getElementById('alert-container');

    // Get data from URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const email = urlParams.get('email');
    const name = urlParams.get('name');
    const phone = urlParams.get('phone');
    const password = urlParams.get('password');
    const profileImage = urlParams.get('profile_image');

    // Timer variables
    let timeLeft = 600; // 10 minutes in seconds
    let timerInterval = null;
    let cooldownTime = 60; // 1 minute cooldown for resend
    let cooldownInterval = null;

    // Initialize page
    if (!email) {
        showAlert('Invalid access. Please go back to signup.', 'error');
        return;
    }

    emailDisplay.textContent = email;
    startTimer();

    // Auto-send OTP when page loads
    sendOTPOnLoad();

    // OTP input handling
    otpInputs.forEach((input, index) => {
        input.addEventListener('input', function(e) {
            // Only allow numbers
            e.target.value = e.target.value.replace(/\D/g, '');

            // Add filled class
            if (e.target.value) {
                e.target.classList.add('filled');
            } else {
                e.target.classList.remove('filled');
            }

            // Move to next input
            if (e.target.value && index < otpInputs.length - 1) {
                otpInputs[index + 1].focus();
            }

            // Auto-submit when all fields are filled
            if (getOTPCode().length === 6) {
                setTimeout(() => verifyOTP(), 100);
            }
        });

        input.addEventListener('keydown', function(e) {
            // Handle backspace
            if (e.key === 'Backspace' && !e.target.value && index > 0) {
                otpInputs[index - 1].focus();
            }

            // Handle paste
            if (e.key === 'v' && e.ctrlKey) {
                e.preventDefault();
                handlePaste(e);
            }
        });

        input.addEventListener('paste', function(e) {
            e.preventDefault();
            handlePaste(e);
        });
    });

    // Handle paste functionality
    function handlePaste(e) {
        const pastedData = e.clipboardData.getData('text');
        const numbers = pastedData.replace(/\D/g, '').slice(0, 6);

        numbers.split('').forEach((digit, index) => {
            if (index < otpInputs.length) {
                otpInputs[index].value = digit;
                otpInputs[index].classList.add('filled');
            }
        });

        // Focus on the last filled input or the next empty one
        const lastFilledIndex = numbers.length - 1;
        if (lastFilledIndex < otpInputs.length - 1) {
            otpInputs[lastFilledIndex + 1].focus();
        } else {
            otpInputs[otpInputs.length - 1].focus();
        }

        // Auto-submit if all fields are filled
        if (numbers.length === 6) {
            setTimeout(() => verifyOTP(), 100);
        }
    }

    // Get OTP code from all inputs
    function getOTPCode() {
        return Array.from(otpInputs).map(input => input.value).join('');
    }

    // Clear OTP inputs
    function clearOTPInputs() {
        otpInputs.forEach(input => {
            input.value = '';
            input.classList.remove('filled', 'error');
        });
        otpInputs[0].focus();
    }

    // Show error on OTP inputs
    function showOTPError() {
        otpInputs.forEach(input => {
            input.classList.add('error');
        });

        setTimeout(() => {
            otpInputs.forEach(input => {
                input.classList.remove('error');
            });
        }, 500);
    }

    // Form submission
    otpForm.addEventListener('submit', function(e) {
        e.preventDefault();
        verifyOTP();
    });

    // Resend OTP
    resendBtn.addEventListener('click', function() {
        resendOTP();
    });

    // Verify OTP function
    function verifyOTP() {
        const otpCode = getOTPCode();

        if (otpCode.length !== 6) {
            showAlert('Please enter a valid 6-digit code', 'error');
            showOTPError();
            return;
        }

        verifyBtn.disabled = true;
        verifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';

        fetch('/php/otp_handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'verify_otp',
                    email: email,
                    otp_code: otpCode,
                    purpose: 'registration'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // OTP verified, now complete registration
                    completeRegistration();
                } else {
                    showAlert(data.error, 'error');
                    showOTPError();
                    verifyBtn.disabled = false;
                    verifyBtn.innerHTML = '<i class="fas fa-check"></i> Verify & Complete Registration';
                }
            })
            .catch(error => {
                console.error('OTP verification error:', error);
                showAlert('Verification failed. Please try again.', 'error');
                showOTPError();
                verifyBtn.disabled = false;
                verifyBtn.innerHTML = '<i class="fas fa-check"></i> Verify & Complete Registration';
            });
    }

    // Complete registration function
    function completeRegistration() {
        verifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Account...';

        // Create FormData for registration
        const formData = new FormData();
        formData.append('name', name);
        formData.append('email', email);
        formData.append('phone', phone);
        formData.append('password', password);
        formData.append('password_confirmation', password);
        formData.append('otp_code', getOTPCode());

        // Add profile image if exists
        if (profileImage) {
            formData.append('profile_image_data', profileImage);
        }

        fetch('/signup', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    showAlert('Registration successful! Redirecting to login...', 'success');
                    setTimeout(() => {
                        window.location.href = '/login';
                    }, 2000);
                } else {
                    return response.text().then(text => {
                        throw new Error('Registration failed');
                    });
                }
            })
            .catch(error => {
                console.error('Registration error:', error);
                showAlert('Registration failed. Please try again.', 'error');
                verifyBtn.disabled = false;
                verifyBtn.innerHTML = '<i class="fas fa-check"></i> Verify & Complete Registration';
            });
    }

    // Auto-send OTP when page loads
    function sendOTPOnLoad() {
        fetch('/php/otp_handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'send_otp',
                    email: email,
                    purpose: 'registration'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('OTP sent successfully! Check your email.', 'success');
                    startCooldownTimer();
                } else {
                    showAlert('Failed to send OTP: ' + data.error, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Failed to send OTP. Please try again.', 'error');
            });
    }

    // Start cooldown timer for resend button
    function startCooldownTimer() {
        cooldownTime = 60; // Reset to 60 seconds
        resendBtn.disabled = true;

        cooldownInterval = setInterval(() => {
            const minutes = Math.floor(cooldownTime / 60);
            const seconds = cooldownTime % 60;
            resendBtn.innerHTML = `<i class="fas fa-clock"></i> Resend in ${minutes}:${seconds.toString().padStart(2, '0')}`;

            if (cooldownTime <= 0) {
                clearInterval(cooldownInterval);
                resendBtn.disabled = false;
                resendBtn.innerHTML = '<i class="fas fa-redo"></i> Resend Code';
            }
            cooldownTime--;
        }, 1000);
    }

    // Resend OTP function
    function resendOTP() {
        resendBtn.disabled = true;
        resendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

        fetch('/php/otp_handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'send_otp',
                    email: email,
                    purpose: 'registration'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('New OTP sent successfully!', 'success');
                    clearOTPInputs();
                    timeLeft = 600; // Reset timer
                    startTimer();
                    startCooldownTimer(); // Start cooldown timer
                } else {
                    showAlert('Failed to resend OTP: ' + data.error, 'error');
                    resendBtn.disabled = false;
                    resendBtn.innerHTML = '<i class="fas fa-redo"></i> Resend Code';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Failed to resend OTP. Please try again.', 'error');
                resendBtn.disabled = false;
                resendBtn.innerHTML = '<i class="fas fa-redo"></i> Resend Code';
            });
    }

    // Start countdown timer
    function startTimer() {
        if (timerInterval) {
            clearInterval(timerInterval);
        }

        timerInterval = setInterval(() => {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerDisplay.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;

            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                timerDisplay.textContent = 'Expired';
                timerDisplay.classList.add('expired');
                otpInputs.forEach(input => input.disabled = true);
                verifyBtn.disabled = true;
                resendBtn.disabled = false;
                showAlert('OTP has expired. Please request a new code.', 'error');
            }
            timeLeft--;
        }, 1000);
    }

    // Show alert function
    function showAlert(message, type) {
        alertContainer.innerHTML = `
            <div class="alert alert-${type === 'error' ? 'danger' : 'success'} alert-dismissible fade show" role="alert">
                <i class="fas fa-${type === 'error' ? 'exclamation-triangle' : 'check-circle'}"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;

        // Auto-hide success messages
        if (type === 'success') {
            setTimeout(() => {
                const alert = alertContainer.querySelector('.alert');
                if (alert) {
                    alert.remove();
                }
            }, 5000);
        }
    }

    // Focus on first OTP input when page loads
    otpInputs[0].focus();
});