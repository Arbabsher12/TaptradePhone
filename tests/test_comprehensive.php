<?php
/**
 * Comprehensive API Test
 * Test all API endpoints to ensure they're working
 */

echo "=== Comprehensive API Test ===\n\n";

// Test 1: Phones API
echo "1. Testing /api/phones endpoint...\n";
$phonesUrl = "http://localhost:8000/api/phones";
$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'timeout' => 5,
        'ignore_errors' => true
    ]
]);

$response = @file_get_contents($phonesUrl, false, $context);
if ($response !== false) {
    $data = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "✓ Phones API working - Found " . count($data) . " phones\n";
        if (count($data) > 0) {
            echo "  Sample: " . $data[0]['name'] . " - $" . $data[0]['price'] . "\n";
        }
    } else {
        echo "✗ Phones API returned invalid JSON\n";
    }
} else {
    echo "✗ Phones API failed to respond\n";
}

echo "\n2. Testing /api/models endpoint...\n";
$modelsUrl = "http://localhost:8000/api/models?brand_id=1";
$response = @file_get_contents($modelsUrl, false, $context);
if ($response !== false) {
    $data = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "✓ Models API working - Found " . count($data) . " models\n";
    } else {
        echo "✗ Models API returned invalid JSON\n";
    }
} else {
    echo "✗ Models API failed to respond\n";
}

echo "\n3. Testing home page...\n";
$homeUrl = "http://localhost:8000/";
$response = @file_get_contents($homeUrl, false, $context);
if ($response !== false) {
    if (strpos($response, 'Phone Marketplace') !== false) {
        echo "✓ Home page loading correctly\n";
    } else {
        echo "✗ Home page content issue\n";
    }
} else {
    echo "✗ Home page failed to load\n";
}

echo "\n=== Test Complete ===\n";
echo "Visit http://localhost:8000 to test the application\n";
?>
