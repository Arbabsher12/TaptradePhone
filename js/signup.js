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
});
