<?php 
session_start();
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
             if (!empty($_SESSION['login_errors'])): ?>
             <div class="alert alert-danger">
             <?php 
             foreach ($_SESSION['login_errors'] as $error) {
             echo "<p>$error</p>";}
             unset($_SESSION['login_errors']); // Clear errors after displaying
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
                    <a href="#" class="text-muted">Forgot password?</a>
                </div>
                <button type="submit" class="btn login-btn w-100">LOGIN</button>
            </form>
            <p class="mt-3">Or Sign Up Using</p>
            <div class="social-icons d-flex justify-content-center gap-3">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>  
                <a href="#"><i class="fab fa-google"></i></a>
            </div>
            <p class="mt-3">Or Sign Up Using</p>
            <a href="/signup" class="fw-bold text-decoration-none" style="color:#3A00E5;">SIGN UP</a>
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
    </script>
</body>
</html>
