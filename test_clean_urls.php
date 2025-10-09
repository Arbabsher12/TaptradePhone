<?php
/**
 * Clean URL Test Script
 * Test various clean URLs to ensure they work properly
 */

echo "=== Clean URL Test ===\n\n";

$baseUrl = "http://localhost:8000";
$testUrls = [
    '/' => 'Home page',
    '/login' => 'Login page',
    '/signup' => 'Signup page',
    '/profile' => 'Profile page',
    '/mylisting' => 'My listings page',
    '/sellYourPhone' => 'Sell phone page',
    '/conversations' => 'Conversations page',
    '/api/phones' => 'API - Get phones',
    '/api/models' => 'API - Get models',
];

echo "Testing clean URLs...\n\n";

foreach ($testUrls as $url => $description) {
    $fullUrl = $baseUrl . $url;
    echo "Testing: $url ($description)\n";
    
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'timeout' => 5,
            'ignore_errors' => true
        ]
    ]);
    
    $response = @file_get_contents($fullUrl, false, $context);
    
    if ($response !== false) {
        $httpCode = 200;
        if (isset($http_response_header)) {
            foreach ($http_response_header as $header) {
                if (preg_match('/HTTP\/\d\.\d\s+(\d+)/', $header, $matches)) {
                    $httpCode = intval($matches[1]);
                    break;
                }
            }
        }
        
        if ($httpCode === 200) {
            echo "✓ Success (200)\n";
        } else {
            echo "⚠ Response code: $httpCode\n";
        }
    } else {
        echo "✗ Failed to connect\n";
    }
    
    echo "\n";
}

echo "=== Test Complete ===\n";
echo "Visit http://localhost:8000 to test manually\n";
?>
