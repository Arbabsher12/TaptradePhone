y<?php
/**
 * Google OAuth Configuration
 * 
 * To set up Google OAuth:
 * 1. Go to Google Cloud Console (https://console.cloud.google.com/)
 * 2. Create a new project or select existing one
 * 3. Enable Google+ API
 * 4. Go to Credentials and create OAuth 2.0 Client ID
 * 5. Add authorized redirect URIs:
 *    - For local development: http://localhost/buy_sell_Phone/google_callback.php
 *    - For production: https://yourdomain.com/google_callback.php
 * 6. Copy Client ID and Client Secret below
 */

// Google OAuth Configuration
define('GOOGLE_CLIENT_ID', '259131839495-erm4e5tpspj9sgb9uhf85qt3213jo50a.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', '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');
define('GOOGLE_REDIRECT_URI', 'http://localhost:8001/google_callback.php'); // Update for production

// Google OAuth URLs
define('GOOGLE_AUTH_URL', 'https://accounts.google.com/o/oauth2/auth');
define('GOOGLE_TOKEN_URL', 'https://oauth2.googleapis.com/token');
define('GOOGLE_USER_INFO_URL', 'https://www.googleapis.com/oauth2/v1/userinfo');

/**
 * Generate Google OAuth URL
 */
function getGoogleAuthUrl() {
    $params = array(
        'client_id' => GOOGLE_CLIENT_ID,
        'redirect_uri' => GOOGLE_REDIRECT_URI,
        'scope' => 'email profile',
        'response_type' => 'code',
        'access_type' => 'offline',
        'prompt' => 'consent'
    );
    
    return GOOGLE_AUTH_URL . '?' . http_build_query($params);
}

/**
 * Exchange authorization code for access token
 */
function getGoogleAccessToken($code) {
    $data = array(
        'client_id' => GOOGLE_CLIENT_ID,
        'client_secret' => GOOGLE_CLIENT_SECRET,
        'redirect_uri' => GOOGLE_REDIRECT_URI,
        'grant_type' => 'authorization_code',
        'code' => $code
    );
    
    $options = array(
        'http' => array(
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        )
    );
    
    $context = stream_context_create($options);
    $result = file_get_contents(GOOGLE_TOKEN_URL, false, $context);
    
    if ($result === FALSE) {
        return false;
    }
    
    return json_decode($result, true);
}

/**
 * Get user information from Google
 */
function getGoogleUserInfo($access_token) {
    $url = GOOGLE_USER_INFO_URL . '?access_token=' . $access_token;
    $result = file_get_contents($url);
    
    if ($result === FALSE) {
        return false;
    }
    
    return json_decode($result, true);
}
?>
