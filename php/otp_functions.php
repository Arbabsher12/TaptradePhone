<?php
// OTP Functions for Password Reset and Registration
// This file contains reusable OTP functions without request handling

// Function to clean expired OTPs
function cleanExpiredOTPs($conn) {
    $sql = "DELETE FROM otp_verification WHERE expires_at < NOW()";
    $conn->query($sql);
}

// Function to generate and send OTP
function generateAndSendOTP($conn, $email, $purpose = 'registration') {
    // Clean expired OTPs first
    cleanExpiredOTPs($conn);
    
    // Check if email exists in users table for registration
    if ($purpose === 'registration') {
        $check_sql = "SELECT user_id FROM users WHERE email = ?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $stmt->close();
            return [
                'success' => false,
                'error' => 'Email already registered'
            ];
        }
        $stmt->close();
    }
    
    // Check if email exists in users table for password reset
    if ($purpose === 'password_reset') {
        $check_sql = "SELECT user_id FROM users WHERE email = ?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $stmt->close();
            return [
                'success' => false,
                'error' => 'No account found with this email address'
            ];
        }
        $stmt->close();
    }
    
    // Check for existing unexpired OTP
    $check_otp_sql = "SELECT id, attempts FROM otp_verification WHERE email = ? AND purpose = ? AND expires_at > NOW() AND is_verified = 0";
    $stmt = $conn->prepare($check_otp_sql);
    $stmt->bind_param("ss", $email, $purpose);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row['attempts'] >= MAX_OTP_ATTEMPTS) {
            $stmt->close();
            return [
                'success' => false,
                'error' => 'Maximum OTP attempts reached. Please try again later.'
            ];
        }
        
        // Update attempts
        $update_sql = "UPDATE otp_verification SET attempts = attempts + 1 WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("i", $row['id']);
        $update_stmt->execute();
        $update_stmt->close();
        $stmt->close();
        
        return [
            'success' => false,
            'error' => 'OTP already sent. Please check your email or wait before requesting again.'
        ];
    }
    $stmt->close();
    
    // Generate new OTP
    $otp_code = generateOTP();
    
    // Insert OTP record with MySQL NOW() function for proper timezone handling
    $insert_sql = "INSERT INTO otp_verification (email, otp_code, purpose, expires_at) VALUES (?, ?, ?, DATE_ADD(NOW(), INTERVAL ? MINUTE))";
    $stmt = $conn->prepare($insert_sql);
    $expiry_minutes = OTP_EXPIRY_MINUTES;
    $stmt->bind_param("sssi", $email, $otp_code, $purpose, $expiry_minutes);
    
    if (!$stmt->execute()) {
        $stmt->close();
        return [
            'success' => false,
            'error' => 'Failed to generate OTP'
        ];
    }
    
    $otp_id = $conn->insert_id;
    $stmt->close();
    
    // Send email
    try {
        $name = explode('@', $email)[0]; // Use email prefix as name
        $email_result = sendOTPEmail($email, $name, $otp_code, $purpose);
        
        if ($email_result['success']) {
            return [
                'success' => true,
                'message' => 'OTP sent successfully',
                'otp_id' => $otp_id
            ];
        } else {
            // Delete the OTP record if email failed
            $delete_sql = "DELETE FROM otp_verification WHERE id = ?";
            $delete_stmt = $conn->prepare($delete_sql);
            $delete_stmt->bind_param("i", $otp_id);
            $delete_stmt->execute();
            $delete_stmt->close();
            
            return [
                'success' => false,
                'error' => $email_result['error']
            ];
        }
    } catch (Exception $e) {
        // Delete the OTP record if email failed
        $delete_sql = "DELETE FROM otp_verification WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $otp_id);
        $delete_stmt->execute();
        $delete_stmt->close();
        
        // Log the actual error for debugging (optional)
        error_log("OTP Email Error: " . $e->getMessage());
        
        return [
            'success' => false,
            'error' => 'Unable to send email. Please check your internet connection and try again'
        ];
    }
}

// Function to verify OTP
function verifyOTP($conn, $email, $otp_code, $purpose = 'registration') {
    // Clean expired OTPs first
    cleanExpiredOTPs($conn);
    
    $sql = "SELECT id, attempts FROM otp_verification WHERE email = ? AND otp_code = ? AND purpose = ? AND expires_at > NOW() AND is_verified = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $email, $otp_code, $purpose);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $stmt->close();
        return [
            'success' => false,
            'error' => 'Invalid or expired OTP'
        ];
    }
    
    $row = $result->fetch_assoc();
    
    // Check attempts
    if ($row['attempts'] >= MAX_OTP_ATTEMPTS) {
        $stmt->close();
        return [
            'success' => false,
            'error' => 'Maximum verification attempts reached'
        ];
    }
    
    // Mark as verified
    $update_sql = "UPDATE otp_verification SET is_verified = 1, verified_at = NOW() WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("i", $row['id']);
    $update_stmt->execute();
    $update_stmt->close();
    $stmt->close();
    
    return [
        'success' => true,
        'message' => 'OTP verified successfully'
    ];
}
?>
