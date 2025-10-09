<?php
/**
 * Test API Endpoint
 * Test the phones API endpoint directly
 */

echo "=== Testing API Endpoint ===\n\n";

// Test the fetchDataPhone.php directly
echo "Testing fetchDataPhone.php directly...\n";

// Simulate GET request
$_GET['limit'] = 5;

// Capture output
ob_start();
include 'php/fetchDataPhone.php';
$output = ob_get_clean();

echo "API Response:\n";
echo $output . "\n\n";

// Test if it's valid JSON
$data = json_decode($output, true);
if (json_last_error() === JSON_ERROR_NONE) {
    echo "✓ Valid JSON response\n";
    echo "Number of phones: " . count($data) . "\n";
    
    if (count($data) > 0) {
        echo "First phone: " . $data[0]['name'] . " - $" . $data[0]['price'] . "\n";
    }
} else {
    echo "✗ Invalid JSON response\n";
    echo "JSON Error: " . json_last_error_msg() . "\n";
}

echo "\n=== Test Complete ===\n";
?>
