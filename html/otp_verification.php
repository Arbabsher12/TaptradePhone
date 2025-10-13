<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email - OTP</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/signUp.css">
    <style>
        .otp-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 40px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .otp-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .otp-header h2 {
            color: #333;
            margin-bottom: 10px;
        }
        
        .otp-header p {
            color: #666;
            font-size: 16px;
        }
        
        .email-display {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 30px;
            border: 1px solid #e9ecef;
        }
        
        .email-display strong {
            color: #007bff;
        }
        
        .otp-inputs {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin: 20px 0;
        }
        
        .otp-input {
            width: 50px;
            height: 50px;
            font-size: 24px;
            text-align: center;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            background: white;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        
        .otp-input:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
            outline: none;
        }
        
        .otp-input.filled {
            border-color: #28a745;
            background: #f8fff9;
        }
        
        .otp-input.error {
            border-color: #dc3545;
            background: #fff5f5;
            animation: shake 0.5s ease-in-out;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        .timer-display {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .timer {
            font-size: 18px;
            font-weight: bold;
            color: #dc3545;
        }
        
        .timer.expired {
            color: #6c757d;
        }
        
        .resend-section {
            text-align: center;
            margin-top: 20px;
        }
        
        .resend-btn {
            background: none;
            border: none;
            color: #007bff;
            text-decoration: underline;
            cursor: pointer;
        }
        
        .resend-btn:disabled {
            color: #6c757d;
            cursor: not-allowed;
            opacity: 0.6;
        }
        
        .resend-section small {
            font-size: 12px;
            color: #6c757d;
        }
        
        .back-link {
            text-align: center;
            margin-top: 30px;
        }
        
        .back-link a {
            color: #007bff;
            text-decoration: none;
        }
        
        .back-link a:hover {
            text-decoration: underline;
        }
        
        .alert {
            margin-bottom: 20px;
        }
        
        .success-message {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <div class="otp-container">
            <div class="otp-header">
                <h2><i class="fas fa-shield-alt"></i> Verify Your Email</h2>
                <p>We've sent a verification code to your email address</p>
            </div>
            
            <div class="email-display">
                <i class="fas fa-envelope"></i> <strong id="email-display">Loading...</strong>
            </div>
            
            <div id="alert-container"></div>
            
            <form id="otp-form">
                <div class="otp-inputs">
                    <input type="text" class="otp-input" maxlength="1" data-index="0" required>
                    <input type="text" class="otp-input" maxlength="1" data-index="1" required>
                    <input type="text" class="otp-input" maxlength="1" data-index="2" required>
                    <input type="text" class="otp-input" maxlength="1" data-index="3" required>
                    <input type="text" class="otp-input" maxlength="1" data-index="4" required>
                    <input type="text" class="otp-input" maxlength="1" data-index="5" required>
                </div>
                
                <div class="timer-display">
                    <div class="timer" id="timer">10:00</div>
                    <small class="text-muted">Code expires in</small>
                </div>
                
                <button type="submit" class="btn btn-primary w-100" id="verify-btn">
                    <i class="fas fa-check"></i> Verify & Complete Registration
                </button>
                
                <div class="resend-section">
                    <p class="text-muted">Didn't receive the code?</p>
                    <button type="button" class="resend-btn" id="resend-btn" disabled>
                        <i class="fas fa-redo"></i> Resend Code
                    </button>
                    <small class="text-muted d-block mt-2">You can request a new code every 60 seconds</small>
                </div>
            </form>
            
            <div class="back-link">
                <a href="/signup"><i class="fas fa-arrow-left"></i> Back to Sign Up</a>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/otp_verification.js"></script>
</body>
</html>
