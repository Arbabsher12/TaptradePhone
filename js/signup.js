// Email validation function
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

document.addEventListener('DOMContentLoaded', function() {
    // Profile image preview
    const profileInput = document.getElementById('profile-input');
    const profilePreview = document.getElementById('profile-preview');

    profileInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                profilePreview.innerHTML = `<img src="${e.target.result}" alt="Profile Preview">`;
            }
            reader.readAsDataURL(file);
        }
    });

    // Password toggle visibility
    const togglePasswordButtons = document.querySelectorAll('.toggle-password');

    togglePasswordButtons.forEach(button => {
        button.addEventListener('click', function() {
            const passwordField = this.previousElementSibling;
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);

            // Toggle eye icon
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    });

    // Password strength meter
    const passwordInput = document.getElementById('password');
    const passwordMeter = document.getElementById('password-meter');
    const passwordFeedback = document.getElementById('password-feedback');

    passwordInput.addEventListener('input', function() {
        const password = this.value;
        let strength = 0;
        let feedback = '';

        if (password.length > 0) {
            // Length check
            if (password.length >= 8) {
                strength += 25;
            }

            // Lowercase and uppercase check
            if (password.match(/[a-z]/) && password.match(/[A-Z]/)) {
                strength += 25;
            }

            // Number check
            if (password.match(/\d/)) {
                strength += 25;
            }

            // Special character check
            if (password.match(/[^a-zA-Z\d]/)) {
                strength += 25;
            }

            // Feedback based on strength
            if (strength <= 25) {
                feedback = 'Weak password';
                passwordMeter.style.background = '#f44336';
            } else if (strength <= 50) {
                feedback = 'Moderate password';
                passwordMeter.style.background = '#ff9800';
            } else if (strength <= 75) {
                feedback = 'Good password';
                passwordMeter.style.background = '#4cc9f0';
            } else {
                feedback = 'Strong password';
                passwordMeter.style.background = '#4caf50';
            }
        }

        passwordMeter.style.width = strength + '%';
        passwordFeedback.textContent = feedback;
    });

    // Password confirmation validation
    const confirmPassword = document.getElementById('confirm-password');
    const form = document.querySelector('form');

    form.addEventListener('submit', function(e) {
        if (passwordInput.value !== confirmPassword.value) {
            e.preventDefault();
            confirmPassword.classList.add('is-invalid');
            confirmPassword.parentElement.classList.add('shake');

            // Create error message if it doesn't exist
            let errorMsg = document.querySelector('.password-match-error');
            if (!errorMsg) {
                errorMsg = document.createElement('div');
                errorMsg.className = 'password-feedback password-match-error';
                errorMsg.style.color = '#f44336';
                errorMsg.textContent = 'Passwords do not match';
                confirmPassword.parentElement.appendChild(errorMsg);
            }

            // Remove shake animation after it completes
            setTimeout(() => {
                confirmPassword.parentElement.classList.remove('shake');
            }, 500);
        }
    });

    // Remove error when user starts typing again
    confirmPassword.addEventListener('input', function() {
        this.classList.remove('is-invalid');
        const errorMsg = document.querySelector('.password-match-error');
        if (errorMsg) {
            errorMsg.remove();
        }
    });

    // Phone number formatting
    const phoneInput = document.querySelector('input[name="phone"]');

    phoneInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 0) {
            if (value.length <= 3) {
                value = value;
            } else if (value.length <= 6) {
                value = value.slice(0, 4) + '-' + value.slice(4);
            } else {
                value = value.slice(0, 4) + '-' + value.slice(4, 11);
            }
        }
        e.target.value = value;
    });

    // Real-time email validation
    const emailInput = document.getElementById('email');
    let emailCheckTimeout = null;

    emailInput.addEventListener('input', function() {
        const email = this.value.trim();

        // Clear previous timeout
        if (emailCheckTimeout) {
            clearTimeout(emailCheckTimeout);
        }

        // Only check if email is valid format and not empty
        if (email && isValidEmail(email)) {
            // Debounce the check - wait 500ms after user stops typing
            emailCheckTimeout = setTimeout(() => {
                checkEmailExists(email).then(exists => {
                    if (exists) {
                        showEmailError('An account with this email already exists');
                    } else {
                        clearEmailError();
                        clearFormError();
                    }
                }).catch(error => {
                    console.error('Error checking email:', error);
                });
            }, 500);
        } else {
            clearEmailError();
        }
    });

    // Function to show email error
    function showEmailError(message) {
        clearEmailError();

        const errorDiv = document.createElement('div');
        errorDiv.className = 'email-error-message';
        errorDiv.style.color = '#dc3545';
        errorDiv.style.fontSize = '14px';
        errorDiv.style.marginTop = '5px';
        errorDiv.style.fontWeight = '500';
        errorDiv.textContent = message;

        // Insert after the email input group
        const emailGroup = emailInput.closest('.mb-3');
        emailGroup.appendChild(errorDiv);
        emailInput.style.borderColor = '#dc3545';
    }

    // Function to clear email error
    function clearEmailError() {
        const emailGroup = emailInput.closest('.mb-3');
        const existingError = emailGroup.querySelector('.email-error-message');
        if (existingError) {
            existingError.remove();
        }
        emailInput.style.borderColor = '';
    }

    // Function to show form-level error
    function showFormError(message) {
        const errorContainer = document.getElementById('form-error-container');
        const errorMessage = document.getElementById('form-error-message');

        errorMessage.textContent = message;
        errorContainer.style.display = 'block';

        // Scroll to error
        errorContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });

        // Auto-hide after 5 seconds
        setTimeout(() => {
            errorContainer.style.display = 'none';
        }, 5000);
    }

    // Function to clear form error
    function clearFormError() {
        const errorContainer = document.getElementById('form-error-container');
        errorContainer.style.display = 'none';
    }

    // Form submission - redirect to OTP page
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Get form data
        const formData = new FormData(form);
        const name = formData.get('name');
        const email = formData.get('email');
        const phone = formData.get('phone');
        const password = formData.get('password');
        const passwordConfirmation = formData.get('password_confirmation');

        // Basic validation
        if (!name || !email || !phone || !password) {
            alert('Please fill in all required fields');
            return;
        }

        if (password !== passwordConfirmation) {
            alert('Passwords do not match');
            return;
        }

        if (password.length < 8) {
            alert('Password must be at least 8 characters long');
            return;
        }

        if (!isValidEmail(email)) {
            alert('Please enter a valid email address');
            return;
        }

        // Check if email already exists
        checkEmailExists(email).then(exists => {
            if (exists) {
                showFormError('An account with this email already exists. Please use a different email or try logging in.');
                showEmailError('An account with this email already exists');
                return;
            }

            // Email is available, proceed with OTP
            proceedToOTP();
        }).catch(error => {
            console.error('Error checking email:', error);
            showFormError('Error checking email availability. Please try again.');
        });

        function proceedToOTP() {
            // Handle profile image
            const profileImageInput = document.getElementById('profile-input');
            let profileImageData = null;

            if (profileImageInput.files.length > 0) {
                const file = profileImageInput.files[0];
                const reader = new FileReader();
                reader.onload = function(e) {
                    profileImageData = e.target.result;
                    redirectToOTP();
                };
                reader.readAsDataURL(file);
            } else {
                redirectToOTP();
            }

            function redirectToOTP() {
                // Create URL parameters
                const params = new URLSearchParams({
                    email: email,
                    name: name,
                    phone: phone,
                    password: password
                });

                if (profileImageData) {
                    params.append('profile_image', profileImageData);
                }

                // Redirect to OTP verification page
                window.location.href = `/otp-verification?${params.toString()}`;
            }
        }
    });

    // Function to check if email already exists
    function checkEmailExists(email) {
        return fetch('/php/check_email.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    email: email
                })
            })
            .then(response => response.json())
            .then(data => {
                return data.exists;
            })
            .catch(error => {
                console.error('Error checking email:', error);
                return false;
            });
    }

    // Google OAuth functionality
    const googleSignupBtn = document.getElementById('google-signup-btn');

    if (googleSignupBtn) {
        googleSignupBtn.addEventListener('click', function(e) {
            e.preventDefault();

            // Add loading state
            const originalContent = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            this.style.pointerEvents = 'none';

            // Get the Google OAuth URL from the href attribute
            const googleUrl = this.getAttribute('href');

            if (googleUrl && googleUrl !== '#') {
                // Redirect to Google OAuth
                window.location.href = googleUrl;
            } else {
                // Reset button state
                this.innerHTML = originalContent;
                this.style.pointerEvents = 'auto';
                showFormError('Google signup is not properly configured. Please contact support.');
            }
        });
    }

    // Add loading state to regular form submission
    const submitBtn = document.querySelector('button[type="submit"]');
    if (submitBtn) {
        const originalForm = document.querySelector('form');
        originalForm.addEventListener('submit', function() {
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Account...';
            submitBtn.disabled = true;
        });
    }
});