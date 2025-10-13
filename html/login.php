<?php 
session_start();
include __DIR__ . '/../php/google_config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js" crossorigin="anonymous"></script>
     <link rel="stylesheet" href="../css/login.css">
     
</head>
<body>
        <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="login-box">
             <h2>Login</h2>
             <?php 
             if (!empty($_SESSION['login_errors']) || !empty($_SESSION['signup_errors'])): ?>
             <div class="alert alert-danger">
             <?php 
             if (!empty($_SESSION['login_errors'])) {
                 foreach ($_SESSION['login_errors'] as $error) {
                     echo "<p>$error</p>";
                 }
                 unset($_SESSION['login_errors']); // Clear errors after displaying
             }
             if (!empty($_SESSION['signup_errors'])) {
                 foreach ($_SESSION['signup_errors'] as $error) {
                     echo "<p>$error</p>";
                 }
                 unset($_SESSION['signup_errors']); // Clear errors after displaying
             }
             ?>
             </div>
             <?php endif; ?>
   
            <form action="/login" method="POST">
            
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-user"></i></span>
                        <input type="email" name="email" class="form-control border-start-0" placeholder="Type your username" required>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" id="password" class="form-control border-start-0" placeholder="Type your password" required>
                        <span class="input-group-text bg-transparent border-start-0 toggle-password" onclick="togglePassword()">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                </div>
                <div class="text-end mb-3">
                    <a href="/forgot-password" class="text-muted">Forgot password?</a>
                </div>
                <button type="submit" class="btn login-btn w-100 mb-3">
                    <i class="fas fa-sign-in-alt me-2"></i>LOGIN
                </button>
            </form>
            
            <div class="social-divider">
                <span>or continue with</span>
            </div>
            
            <div class="google-login-container mb-3">
                <a href="<?php echo getGoogleAuthUrl(); ?>" class="btn google-login-btn w-100" id="google-login-btn">
                    <i class="fab fa-google me-2"></i>Login with Google
                </a>
            </div>
            
            <div class="signup-link text-center">
                Don't have an account? <a href="/signup" class="fw-bold text-decoration-none" style="color:#3A00E5;">SIGN UP</a>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.querySelector('.toggle-password i');
            
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

        // Google OAuth functionality
        document.addEventListener('DOMContentLoaded', function() {
            const googleLoginBtn = document.getElementById('google-login-btn');
            
            if (googleLoginBtn) {
                googleLoginBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Add loading state
                    const originalContent = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Connecting...';
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
                        alert('Google login is not properly configured. Please contact support.');
                    }
                });
            }

            // Add loading state to regular form submission
            const loginForm = document.querySelector('form');
            const loginBtn = document.querySelector('.login-btn');
            
            if (loginForm && loginBtn) {
                loginForm.addEventListener('submit', function() {
                    loginBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Logging in...';
                    loginBtn.disabled = true;
                });
            }
        });
    </script>
</body>
</html>
