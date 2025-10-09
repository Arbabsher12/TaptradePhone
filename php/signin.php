<?php
session_start(); // Start session
ob_start();
 // Start output buffering


include 'db.php';
 
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validate inputs
    if (empty($email) || empty($password)) {
        $errors[] = "Email and Password are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($errors)) {
        // Check if email exists
        $stmt = $conn->prepare("SELECT user_id, name, password, profile_picture FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $name, $hashed_password, $dp);
            $stmt->fetch(); 

            if( password_verify($password, $hashed_password)) {
                // Store user details in session
                $_SESSION['user_id'] = $id;
                $_SESSION['user_name'] = $name;
                $_SESSION['profile_picture'] = !empty($dp) ? $dp : "../Components/noDp.png"; // Set default if null
                
                
                session_write_close(); // Ensure session is saved before redirecting

                header("Location: /"); // Redirect to home page
                exit();
            } else {
                $errors[] = "Incorrect password.";
            }
        } else {
            $errors[] = "No account found with this email.";
        }

        $stmt->close();
    }
}


 

// If there are errors, redirect back to login with error messages
if (!empty($errors)) {
    $_SESSION['login_errors'] = $errors;
    session_write_close();
    header("Location: /login");
    exit();
}

$conn->close();
ob_end_clean();

?>
