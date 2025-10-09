<?php
// Fallback index.php for servers without URL rewriting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Simple routing without .htaccess
$request = $_SERVER['REQUEST_URI'];
$request = strtok($request, '?'); // Remove query string
$request = ltrim($request, '/'); // Remove leading slash

// Handle different requests
switch ($request) {
    case '':
    case '/':
        include 'minimal_home.php';
        break;
    case 'home':
        include 'minimal_home.php';
        break;
    case 'debug':
        include 'debug.php';
        break;
    case 'test':
        include 'test_router.php';
        break;
    case 'server_test':
        include 'server_test.php';
        break;
    case 'login':
        include 'html/login.php';
        break;
    case 'signup':
        include 'html/signup.php';
        break;
    case 'sellYourPhone':
        include 'html/sellYourPhone.php';
        break;
    case 'allPhones':
        include 'html/allPhones.php';
        break;
    case 'profile':
        include 'html/profile.php';
        break;
    case 'mylisting':
        include 'html/myListing.php';
        break;
    case 'phoneDetail':
        include 'html/phoneDetail.php';
        break;
    case 'chats':
        include 'html/chats.php';
        break;
    case 'conversations':
        include 'html/conversations.php';
        break;
    case 'updatePhoneForm':
        include 'html/updatePhoneForm.php';
        break;
    default:
        // Check if it's a static file
        if (preg_match('/\.(css|js|png|jpg|jpeg|gif|ico|svg)$/', $request)) {
            if (file_exists($request)) {
                $extension = pathinfo($request, PATHINFO_EXTENSION);
                $mimeTypes = [
                    'css' => 'text/css',
                    'js' => 'application/javascript',
                    'png' => 'image/png',
                    'jpg' => 'image/jpeg',
                    'jpeg' => 'image/jpeg',
                    'gif' => 'image/gif',
                    'ico' => 'image/x-icon',
                    'svg' => 'image/svg+xml'
                ];
                
                if (isset($mimeTypes[$extension])) {
                    header('Content-Type: ' . $mimeTypes[$extension]);
                }
                
                readfile($request);
                exit;
            }
        }
        
        // Check if it's a Components file
        if (strpos($request, 'Components/') === 0) {
            if (file_exists($request)) {
                $extension = pathinfo($request, PATHINFO_EXTENSION);
                $mimeTypes = [
                    'png' => 'image/png',
                    'jpg' => 'image/jpeg',
                    'jpeg' => 'image/jpeg',
                    'gif' => 'image/gif',
                    'svg' => 'image/svg+xml'
                ];
                
                if (isset($mimeTypes[$extension])) {
                    header('Content-Type: ' . $mimeTypes[$extension]);
                }
                
                readfile($request);
                exit;
            }
        }
        
        // Check if it's an uploads file
        if (strpos($request, 'uploads/') === 0) {
            if (file_exists($request)) {
                $extension = pathinfo($request, PATHINFO_EXTENSION);
                $mimeTypes = [
                    'png' => 'image/png',
                    'jpg' => 'image/jpeg',
                    'jpeg' => 'image/jpeg',
                    'gif' => 'image/gif',
                    'svg' => 'image/svg+xml'
                ];
                
                if (isset($mimeTypes[$extension])) {
                    header('Content-Type: ' . $mimeTypes[$extension]);
                }
                
                readfile($request);
                exit;
            }
        }
        
        // 404 for everything else
        http_response_code(404);
        echo "<!DOCTYPE html>
        <html>
        <head>
            <title>404 - Page Not Found</title>
            <style>
                body { font-family: Arial, sans-serif; text-align: center; margin-top: 100px; }
                h1 { color: #e74c3c; }
                a { color: #3498db; text-decoration: none; }
                a:hover { text-decoration: underline; }
            </style>
        </head>
        <body>
            <h1>404 - Page Not Found</h1>
            <p>The page you're looking for doesn't exist.</p>
            <p>Request: " . htmlspecialchars($request) . "</p>
            <a href='/'>← Go back to home</a>
        </body>
        </html>";
        break;
}
?>
