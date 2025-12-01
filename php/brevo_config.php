<?php
// Load environment variables
require_once __DIR__ . '/env_loader.php';

// Brevo API Configuration
// Load from environment variables
define('BREVO_API_KEY', $_ENV['BREVO_API_KEY'] );

// Brevo API endpoints
define('BREVO_API_URL', $_ENV['BREVO_API_URL'] );

// Email configuration
define('FROM_EMAIL', $_ENV['FROM_EMAIL'] );
define('FROM_NAME', $_ENV['FROM_NAME'] );

// OTP configuration
define('OTP_LENGTH', (int)($_ENV['OTP_LENGTH'] ?? 6));
define('OTP_EXPIRY_MINUTES', (int)($_ENV['OTP_EXPIRY_MINUTES'] ?? 10));
define('MAX_OTP_ATTEMPTS', (int)($_ENV['MAX_OTP_ATTEMPTS'] ?? 3));

// Function to check network connectivity
function checkNetworkConnectivity() {
    $test_urls = [
        'https://www.google.com',
        'https://www.cloudflare.com',
        'https://api.brevo.com'
    ];
    
    foreach ($test_urls as $url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $result = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if (!$error && $http_code < 500) {
            return true; // At least one URL is reachable
        }
    }
    
    return false; // No URLs are reachable
}

// Brevo API function to send email
function sendBrevoEmail($to_email, $to_name, $subject, $html_content, $text_content = '') {
    $api_key = BREVO_API_KEY;
    
    if ($api_key === 'YOUR_BREVO_API_KEY_HERE') {
        return [
            'success' => false,
            'error' => 'Brevo API key not configured. Please update brevo_config.php with your actual API key.'
        ];
    }
    
    // Check network connectivity first
    if (!checkNetworkConnectivity()) {
        return [
            'success' => false,
            'error' => 'Check your internet connection and try again'
        ];
    }
    
    $data = [
        'sender' => [
            'name' => FROM_NAME,
            'email' => FROM_EMAIL
        ],
        'to' => [
            [
                'email' => $to_email,
                'name' => $to_name
            ]
        ],
        'subject' => $subject,
        'htmlContent' => $html_content,
        'textContent' => $text_content
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, BREVO_API_URL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'accept: application/json',
        'api-key: ' . $api_key,
        'content-type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 30 second timeout
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // 10 second connection timeout
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        // Handle specific network errors
        if (strpos($error, 'Could not resolve host') !== false) {
            return [
                'success' => false,
                'error' => 'Check your internet connection and try again'
            ];
        } elseif (strpos($error, 'Connection timed out') !== false) {
            return [
                'success' => false,
                'error' => 'Connection timed out. Please check your internet connection and try again'
            ];
        } elseif (strpos($error, 'SSL') !== false) {
            return [
                'success' => false,
                'error' => 'Network security error. Please try again later'
            ];
        } else {
            return [
                'success' => false,
                'error' => 'Network error. Please check your internet connection and try again'
            ];
        }
    }
    
    if ($http_code === 201) {
        return [
            'success' => true,
            'message' => 'Email sent successfully'
        ];
    } else {
        $response_data = json_decode($response, true);
        
        // Handle specific HTTP error codes
        if ($http_code >= 500) {
            return [
                'success' => false,
                'error' => 'Email service is temporarily unavailable. Please try again later'
            ];
        } elseif ($http_code === 401) {
            return [
                'success' => false,
                'error' => 'Email service configuration error. Please contact support'
            ];
        } elseif ($http_code === 403) {
            return [
                'success' => false,
                'error' => 'Email service access denied. Please contact support'
            ];
        } elseif ($http_code === 429) {
            return [
                'success' => false,
                'error' => 'Too many requests. Please wait a moment and try again'
            ];
        } else {
            return [
                'success' => false,
                'error' => 'Unable to send email. Please try again later'
            ];
        }
    }
}

// Generate OTP code
function generateOTP($length = OTP_LENGTH) {
    $otp = '';
    for ($i = 0; $i < $length; $i++) {
        $otp .= rand(0, 9);
    }
    return $otp;
}

// Send OTP email
function sendOTPEmail($email, $name, $otp_code, $purpose = 'registration') {
    $subject_map = [
        'registration' => 'Verify Your Email - Registration',
        'login' => 'Your Login Code',
        'password_reset' => 'Password Reset Code'
    ];
    
    $subject = $subject_map[$purpose] ?? 'Your Verification Code';
    
    $html_content = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='utf-8'>
        <title>Email Verification</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #007bff; color: white; padding: 20px; text-align: center; }
            .content { padding: 30px; background: #f9f9f9; }
            .otp-code { font-size: 32px; font-weight: bold; color: #007bff; text-align: center; margin: 20px 0; padding: 20px; background: white; border-radius: 8px; letter-spacing: 5px; }
            .footer { text-align: center; padding: 20px; color: #666; font-size: 14px; }
            .warning { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 20px 0; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Buy Sell Phone</h1>
            </div>
            <div class='content'>
                <h2>Hello " . htmlspecialchars($name) . "!</h2>
                <p>Your verification code is:</p>
                <div class='otp-code'>" . $otp_code . "</div>
                <p>This code will expire in " . OTP_EXPIRY_MINUTES . " minutes.</p>
                <div class='warning'>
                    <strong>Security Notice:</strong> Never share this code with anyone. Our team will never ask for your verification code.
                </div>
                <p>If you didn't request this code, please ignore this email.</p>
            </div>
            <div class='footer'>
                <p>© 2025 Buy Sell Phone. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>";
    
    $text_content = "Hello " . $name . "!\n\nYour verification code is: " . $otp_code . "\n\nThis code will expire in " . OTP_EXPIRY_MINUTES . " minutes.\n\nIf you didn't request this code, please ignore this email.\n\n© 2025 Buy Sell Phone";
    
    return sendBrevoEmail($email, $name, $subject, $html_content, $text_content);
}
?>
