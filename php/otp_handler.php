<?php
// OTP Handler for Brevo Integration
session_start();
include 'db.php';
include 'brevo_config.php';
include 'otp_functions.php';

header('Content-Type: application/json');

// Handle API requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? '';
    
    switch ($action) {
        case 'send_otp':
            $email = $input['email'] ?? '';
            $purpose = $input['purpose'] ?? 'registration';
            
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['success' => false, 'error' => 'Valid email required']);
                exit;
            }
            
            $result = generateAndSendOTP($conn, $email, $purpose);
            echo json_encode($result);
            break;
            
        case 'verify_otp':
            $email = $input['email'] ?? '';
            $otp_code = $input['otp_code'] ?? '';
            $purpose = $input['purpose'] ?? 'registration';
            
            if (empty($email) || empty($otp_code)) {
                echo json_encode(['success' => false, 'error' => 'Email and OTP code required']);
                exit;
            }
            
            $result = verifyOTP($conn, $email, $otp_code, $purpose);
            echo json_encode($result);
            break;
            
        default:
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
            break;
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
}

$conn->close();
?>
