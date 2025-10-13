<?php
// Check if email already exists
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $email = $input['email'] ?? '';
    
    // Debug logging
    error_log("Email check request: " . $email);
    
    if (empty($email)) {
        echo json_encode(['exists' => false, 'error' => 'Email is required']);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['exists' => false, 'error' => 'Invalid email format']);
        exit;
    }
    
    // Check if email exists in users table
    $sql = "SELECT user_id FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $exists = $result->num_rows > 0;
    
    error_log("Email exists check result: " . ($exists ? 'true' : 'false'));
    
    $stmt->close();
    $conn->close();
    
    echo json_encode(['exists' => $exists]);
} else {
    echo json_encode(['exists' => false, 'error' => 'Method not allowed']);
}
?>
