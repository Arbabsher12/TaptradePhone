<?php
// Start the session
session_start();

// Unset all session variables
session_unset();

// Destroy the session
session_destroy();

// Redirect to login page (change if needed)
header("Location: /");
exit();
?>
