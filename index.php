<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

/**
 * Simple Router for Clean URLs
 * Handles URL routing for the PHP built-in server
 */

class Router {
    private $routes = [];
    private $basePath = '';
    
    public function __construct($basePath = '') {
        $this->basePath = rtrim($basePath, '/');
    }
    
    /**
     * Add a route
     */
    public function addRoute($method, $path, $handler) {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler
        ];
    }
    
    /**
     * Add GET route
     */
    public function get($path, $handler) {
        $this->addRoute('GET', $path, $handler);
    }
    
    /**
     * Add POST route
     */
    public function post($path, $handler) {
        $this->addRoute('POST', $path, $handler);
    }
    
    /**
     * Dispatch the request
     */
    public function dispatch() {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = $_SERVER['REQUEST_URI'];
        
        // Remove query string
        $requestUri = strtok($requestUri, '?');
        
        // Remove base path if set
        if ($this->basePath && strpos($requestUri, $this->basePath) === 0) {
            $requestUri = substr($requestUri, strlen($this->basePath));
        }
        
        // Handle different server configurations
        // Remove leading slash for consistency
        $requestUri = ltrim($requestUri, '/');
        
        // If empty, set to root
        if (empty($requestUri)) {
            $requestUri = '/';
        } else {
            $requestUri = '/' . $requestUri;
        }
        
        $requestUri = rtrim($requestUri, '/');
        if (empty($requestUri)) {
            $requestUri = '/';
        }
        
        // Find matching route
        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod && $this->matchRoute($route['path'], $requestUri)) {
                $this->executeHandler($route['handler'], $requestUri);
                return;
            }
        }
        
        // No route found, return 404
        $this->handle404();
    }
    
    /**
     * Match route pattern with request URI
     */
    private function matchRoute($pattern, $uri) {
        // Convert route pattern to regex
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $pattern);
        $pattern = '#^' . $pattern . '$#';
        
        return preg_match($pattern, $uri);
    }
    
    /**
     * Execute route handler
     */
    private function executeHandler($handler, $uri) {
        if (is_string($handler)) {
            // If handler is a file path, include it
            if (file_exists($handler)) {
                include $handler;
            } else {
                $this->handle404();
            }
        } elseif (is_callable($handler)) {
            // If handler is a function, call it
            call_user_func($handler);
        } else {
            $this->handle404();
        }
    }
    
    /**
     * Handle 404 errors
     */
    private function handle404() {
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
            <a href='/'>← Go back to home</a>
        </body>
        </html>";
    }
}

// Create router instance
$router = new Router();

// Define routes
$router->get('/', 'html/home.php');
$router->get('/home', 'html/home.php');
$router->get('/allPhones', 'html/allPhones.php');
$router->get('/login', 'html/login.php');
$router->get('/signup', 'html/signup.php');
$router->get('/otp-verification', 'html/otp_verification.php');
$router->get('/forgot-password', 'html/forgot_password.php');
$router->get('/verify-reset-otp', 'html/verify_reset_otp.php');
$router->get('/new-password', 'html/new_password.php');
$router->get('/reset-password', 'html/reset_password.php');
$router->get('/profile', 'html/profile.php');
$router->get('/mylisting', 'html/myListing.php');
$router->get('/sellYourPhone', 'html/sellYourPhone.php');
$router->get('/phoneDetail', 'html/phoneDetail.php');
$router->get('/chats', 'html/chats.php');
$router->post('/chats', 'html/chats.php');
$router->get('/conversations', 'html/conversations.php');
$router->get('/debug', 'debug.php');
$router->get('/test', 'test_router.php');

// POST routes for forms
$router->post('/login', 'php/signin.php');
$router->post('/signup', 'php/register_with_otp.php');
$router->post('/forgot-password', 'php/forgot_password.php');
$router->post('/verify-reset-otp', 'php/verify_reset_otp.php');
$router->post('/new-password', 'php/new_password.php');
$router->post('/reset-password', 'php/reset_password.php');
$router->post('/php/otp_handler.php', 'php/otp_handler.php');
$router->post('/php/check_email.php', 'php/check_email.php');
$router->post('/sellPhone', 'php/sellPhone.php');
$router->post('/updateProfile', 'php/update_profile.php');
$router->post('/updatePhone', 'php/updatePhone.php');
$router->post('/deletePhone', 'php/deletePhone.php');

// Additional routes
$router->get('/updatePhoneForm', 'html/updatePhoneForm.php');

// API routes
$router->get('/api/phones', 'php/fetchDataPhone.php');
$router->get('/api/models', 'php/get-models.php');
$router->get('/api/messages', 'php/get_messages.php');
$router->post('/api/messages', 'php/chats_system.php');

// Handle logout
$router->get('/logout', function() {
    include 'php/logout.php';
});

// Handle static files (CSS, JS, images)
if (preg_match('/\.(css|js|png|jpg|jpeg|gif|ico|svg)$/', $_SERVER['REQUEST_URI'])) {
    $filePath = ltrim($_SERVER['REQUEST_URI'], '/');
    if (file_exists($filePath)) {
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
        
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        if (isset($mimeTypes[$extension])) {
            header('Content-Type: ' . $mimeTypes[$extension]);
        }
        
        readfile($filePath);
        exit;
    }
}

// Handle Components directory
if (strpos($_SERVER['REQUEST_URI'], '/Components/') === 0) {
    $filePath = ltrim($_SERVER['REQUEST_URI'], '/');
    if (file_exists($filePath)) {
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
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
        
        readfile($filePath);
        exit;
    }
}

// Handle uploads directory
if (strpos($_SERVER['REQUEST_URI'], '/uploads/') === 0) {
    $filePath = ltrim($_SERVER['REQUEST_URI'], '/');
    if (file_exists($filePath)) {
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
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
        
        readfile($filePath);
        exit;
    }
}

// Dispatch the request
$router->dispatch();
?>