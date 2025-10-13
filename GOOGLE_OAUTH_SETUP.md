# Google OAuth Setup Guide

This guide will help you set up Google OAuth authentication for your phone buy/sell application.

## Prerequisites

- Google Cloud Console account
- Your application running on localhost or a live domain

## Step 1: Google Cloud Console Setup

1. **Go to Google Cloud Console**
   - Visit [Google Cloud Console](https://console.cloud.google.com/)

2. **Create or Select a Project**
   - Create a new project or select an existing one
   - Note your project ID

3. **Enable Google+ API**
   - Go to "APIs & Services" > "Library"
   - Search for "Google+ API" and enable it
   - Also enable "Google OAuth2 API"

4. **Create OAuth 2.0 Credentials**
   - Go to "APIs & Services" > "Credentials"
   - Click "Create Credentials" > "OAuth 2.0 Client ID"
   - Choose "Web application" as the application type

5. **Configure OAuth Consent Screen**
   - Go to "APIs & Services" > "OAuth consent screen"
   - Choose "External" user type
   - Fill in required fields:
     - App name: "Phone Buy/Sell App"
     - User support email: Your email
     - Developer contact: Your email
   - Add scopes: `email` and `profile`

6. **Set Authorized Redirect URIs**
   - In your OAuth 2.0 Client ID settings, add these URIs:
   - For local development: `http://localhost/buy_sell_Phone/google_callback.php`
   - For production: `https://yourdomain.com/google_callback.php`

7. **Get Your Credentials**
   - Copy your Client ID and Client Secret

## Step 2: Configure Your Application

1. **Update Google Configuration**
   - Open `php/google_config.php`
   - Replace `YOUR_GOOGLE_CLIENT_ID_HERE` with your actual Client ID
   - Replace `YOUR_GOOGLE_CLIENT_SECRET_HERE` with your actual Client Secret
   - Update `GOOGLE_REDIRECT_URI` with your actual domain

2. **Run Database Migration**
   - Run the migration script: `php migrate_google_oauth.php`
   - This will add necessary columns to your users table

## Step 3: Test the Implementation

1. **Start Your Application**
   - Make sure your XAMPP server is running
   - Navigate to your signup page

2. **Test Google Signup**
   - Click the Google sign-in button
   - You should be redirected to Google's OAuth page
   - After authorization, you'll be redirected back to your app
   - Check if the user is created in your database

## Step 4: Production Deployment

1. **Update Redirect URIs**
   - Add your production domain to Google Cloud Console
   - Update `GOOGLE_REDIRECT_URI` in `google_config.php`

2. **Test on Production**
   - Deploy your application
   - Test Google signup on the live site

## Troubleshooting

### Common Issues

1. **"redirect_uri_mismatch" Error**
   - Make sure the redirect URI in Google Console matches exactly
   - Check for trailing slashes and http vs https

2. **"invalid_client" Error**
   - Verify your Client ID and Client Secret are correct
   - Make sure the OAuth consent screen is configured

3. **Database Errors**
   - Run the migration script: `php migrate_google_oauth.php`
   - Check database connection settings

4. **Google Button Not Working**
   - Check browser console for JavaScript errors
   - Verify `google_config.php` is included correctly

### Testing Checklist

- [ ] Google OAuth button appears on signup page
- [ ] Clicking button redirects to Google OAuth page
- [ ] After authorization, user is redirected back
- [ ] New users are created in database
- [ ] Existing users can log in with Google
- [ ] Profile pictures are imported from Google
- [ ] Session variables are set correctly

## Security Notes

- Never commit your Client Secret to version control
- Use environment variables for production credentials
- Regularly rotate your OAuth credentials
- Monitor OAuth usage in Google Cloud Console

## Support

If you encounter issues:
1. Check the error logs in your application
2. Verify Google Cloud Console settings
3. Test with a different Google account
4. Check browser developer tools for errors

## Files Modified/Created

- `php/google_config.php` - Google OAuth configuration
- `google_callback.php` - OAuth callback handler
- `migrate_google_oauth.php` - Database migration script
- `html/signup.php` - Updated signup form
- `js/signup.js` - Enhanced with Google OAuth support
