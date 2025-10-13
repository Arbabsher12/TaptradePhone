# Brevo OTP Setup Guide

## 1. Get Your Brevo API Key

1. Go to [Brevo.com](https://www.brevo.com) and create an account
2. Navigate to Settings > API Keys
3. Create a new API key with SMTP permissions
4. Copy the API key

## 2. Configure Your Gmail Account

1. In Brevo dashboard, go to Settings > SMTP & API
2. Add your Gmail address as sender
3. Verify your Gmail address through the verification email

## 3. Update Configuration Files

### Update `php/brevo_config.php`:

```php
// Replace these values with your actual credentials
define('BREVO_API_KEY', 'YOUR_ACTUAL_BREVO_API_KEY');
define('FROM_EMAIL', 'your-gmail@gmail.com'); // Your Gmail address
```

## 4. Test the System

1. Start your XAMPP server
2. Navigate to `http://localhost/buy_sell_Phone/signup`
3. Enter an email address and click "Send OTP"
4. Check your email for the OTP code
5. Enter the OTP and complete registration

## 5. Troubleshooting

- **API Key Error**: Make sure your Brevo API key is correct
- **Email Not Sending**: Verify your Gmail address in Brevo
- **OTP Not Working**: Check database connection and OTP table setup

## 6. Security Features

- OTP expires in 10 minutes
- Maximum 3 attempts per OTP
- Automatic cleanup of expired OTPs
- Email verification required for registration
