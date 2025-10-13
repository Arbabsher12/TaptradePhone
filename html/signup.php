<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/signUp.css">
</head>
<body>
    <div class="signup-container">
        <div class="signup-box mt-10">
            <h2>Create Account</h2>
            
            <?php 
            session_start(); // Start the session to access session variables
            include __DIR__ . '/../php/google_config.php';
             if (isset($_SESSION['signup_errors']) && is_array($_SESSION['signup_errors'])) {
                foreach ($_SESSION['signup_errors'] as $error) {
                    echo "<p style='color: red; 
                    text-align: center;
                    padding: 15px;
                    border-radius: 5px;
                    font-size: 16px;
                    font-weight: italic;
                    width: 100%;
                    background-color: #f8d7da; /* Light red background */
                    color: #721c24; /* Dark red text */
                    border: 1px solid #f5c6cb; /* Red border */
                    margin-top: 1rem;
                    '> * $error</p> ";
            
                }
                unset($_SESSION['signup_errors']); // Clear errors after displaying
            }
            if (isset($_SESSION['success'])) {
                echo "<p style='color: green; text-align: center;'>" . $_SESSION['success'] . "</p>";
                unset($_SESSION['success']); // Clear success message
            }  
            ?>            
            
            <div id="form-error-container" style="display: none; margin-bottom: 20px;">
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span id="form-error-message"></span>
                </div>
            </div>
            
            <form action="/signup" method="POST" enctype="multipart/form-data">
                <div class="profile-upload">
                    <div class="profile-image" id="profile-preview">
                        <div class="profile-image-placeholder">
                            <i class="fas fa-user"></i>
                        </div>
                    </div>
                    <label for="profile-input" class="upload-btn">
                        <i class="fas fa-camera"></i>
                    </label>
                    <input type="file" id="profile-input" name="profile_image" accept="image/*">
                </div>
                
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-user"></i></span>
                        <input type="text" name="name" class="form-control border-start-0" placeholder="Full Name" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-envelope"></i></span>
                        <input type="email" name="email" id="email" class="form-control border-start-0" placeholder="Email Address" required>
                    </div>
                    <!-- Email error messages will be inserted here -->
                </div>
                
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-phone"></i></span>
                        <input type="tel" name="phone" class="form-control border-start-0" placeholder="Phone Number" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" id="password" class="form-control border-start-0 password" placeholder="Password" required>
                        <span class="input-group-text bg-transparent border-start-0 toggle-password"><i class="fas fa-eye"></i></span>
                    </div>
                </div>
                <div class="password-strength">
                    <div class="password-strength-meter" id="password-meter"></div>
                </div>
                <div class="password-feedback" id="password-feedback"></div>
                
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password_confirmation" id="confirm-password" class="form-control border-start-0 password" placeholder="Confirm Password" required>
                        <span class="input-group-text bg-transparent border-start-0 toggle-password"><i class="fas fa-eye"></i></span>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary w-100 mb-3">
                    <i class="fas fa-user-plus me-2"></i>Create Account
                </button>
                
                <div class="social-divider">
                    <span>or continue with</span>
                </div>
                
                <div class="google-signup-container">
                    <a href="<?php echo getGoogleAuthUrl(); ?>" class="btn google-signup-btn w-100" id="google-signup-btn">
                        <i class="fab fa-google me-2"></i>Sign up with Google
                    </a>
                </div>
                
                <div class="login-link">
                    Already have an account? <a href="/login">Log In</a>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/signup.js"></script>
</body>
</html>