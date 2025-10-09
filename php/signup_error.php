<?php 
 if (isset($_SESSION['signup_errors'])) {
    echo "<p style='color: red; text-align: center;'>Please fix the following errors:</p>";
    foreach ($_SESSION['errors'] as $error) {
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
 