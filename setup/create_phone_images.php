<?php
/**
 * Create Phone Images Script
 * Creates realistic phone images using placeholder services and saves them locally
 */

// Create phones directory if it doesn't exist
$phonesDir = __DIR__ . '/../uploads/phones';
if (!file_exists($phonesDir)) {
    mkdir($phonesDir, 0755, true);
}

echo "<h1>📱 Creating Phone Images</h1>";

// Phone specifications with realistic image parameters
$phoneSpecs = [
    'iPhone 15 Pro Max' => [
        'color' => 'Natural Titanium',
        'primary_color' => '8B8B8B',
        'secondary_color' => 'F5F5F5',
        'brand' => 'Apple'
    ],
    'Samsung Galaxy S23 Ultra' => [
        'color' => 'Phantom Black',
        'primary_color' => '000000',
        'secondary_color' => '1A1A1A',
        'brand' => 'Samsung'
    ],
    'Google Pixel 8 Pro' => [
        'color' => 'Obsidian',
        'primary_color' => '2C2C2C',
        'secondary_color' => '404040',
        'brand' => 'Google'
    ],
    'OnePlus 11' => [
        'color' => 'Titan Black',
        'primary_color' => '1A1A1A',
        'secondary_color' => '333333',
        'brand' => 'OnePlus'
    ],
    'Xiaomi 13 Pro' => [
        'color' => 'Ceramic White',
        'primary_color' => 'FFFFFF',
        'secondary_color' => 'F0F0F0',
        'brand' => 'Xiaomi'
    ],
    'iPhone 14 Pro' => [
        'color' => 'Deep Purple',
        'primary_color' => '4A148C',
        'secondary_color' => '7B1FA2',
        'brand' => 'Apple'
    ],
    'Samsung Galaxy S22 Ultra' => [
        'color' => 'Burgundy',
        'primary_color' => '800020',
        'secondary_color' => 'A00030',
        'brand' => 'Samsung'
    ],
    'Google Pixel 7 Pro' => [
        'color' => 'Hazel',
        'primary_color' => '8B4513',
        'secondary_color' => 'A0522D',
        'brand' => 'Google'
    ],
    'OnePlus Nord 3' => [
        'color' => 'Misty Green',
        'primary_color' => '90EE90',
        'secondary_color' => '98FB98',
        'brand' => 'OnePlus'
    ],
    'Xiaomi Redmi Note 12 Pro+' => [
        'color' => 'Ice Blue',
        'primary_color' => '87CEEB',
        'secondary_color' => 'B0E0E6',
        'brand' => 'Xiaomi'
    ]
];

function createPhoneImage($phoneName, $specs, $phonesDir) {
    $imagePaths = [];
    
    // Create 3 different angles/views for each phone
    for ($i = 1; $i <= 3; $i++) {
        $filename = 'phone_' . preg_replace('/[^a-zA-Z0-9]/', '_', $phoneName) . '_' . $i . '.jpg';
        $filepath = $phonesDir . '/' . $filename;
        
        // Create a realistic phone image using GD library
        $width = 400;
        $height = 600;
        
        // Create image
        $image = imagecreatetruecolor($width, $height);
        
        // Define colors
        $primaryColor = hexToRgb($specs['primary_color']);
        $secondaryColor = hexToRgb($specs['secondary_color']);
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);
        $gray = imagecolorallocate($image, 128, 128, 128);
        
        $primaryRgb = imagecolorallocate($image, $primaryColor['r'], $primaryColor['g'], $primaryColor['b']);
        $secondaryRgb = imagecolorallocate($image, $secondaryColor['r'], $secondaryColor['g'], $secondaryColor['b']);
        
        // Fill background
        imagefill($image, 0, 0, $white);
        
        // Draw phone body (rounded rectangle)
        $phoneX = 50;
        $phoneY = 50;
        $phoneWidth = 300;
        $phoneHeight = 500;
        
        // Draw phone body
        drawRoundedRectangle($image, $phoneX, $phoneY, $phoneWidth, $phoneHeight, 20, $primaryRgb);
        
        // Draw screen
        $screenX = $phoneX + 20;
        $screenY = $phoneY + 80;
        $screenWidth = $phoneWidth - 40;
        $screenHeight = $phoneHeight - 120;
        
        drawRoundedRectangle($image, $screenX, $screenY, $screenWidth, $screenHeight, 10, $black);
        
        // Draw camera module (for different views)
        if ($i == 1) {
            // Front view - draw notch
            drawRoundedRectangle($image, $screenX + 100, $screenY - 10, 100, 20, 10, $primaryRgb);
        } elseif ($i == 2) {
            // Back view - draw camera array
            $cameraX = $screenX + 50;
            $cameraY = $screenY + 50;
            imagefilledellipse($image, $cameraX, $cameraY, 30, 30, $gray);
            imagefilledellipse($image, $cameraX + 50, $cameraY, 30, 30, $gray);
            imagefilledellipse($image, $cameraX + 100, $cameraY, 30, 30, $gray);
        } else {
            // Side view - draw buttons
            drawRoundedRectangle($image, $phoneX - 10, $phoneY + 150, 20, 40, 5, $secondaryRgb);
            drawRoundedRectangle($image, $phoneX - 10, $phoneY + 250, 20, 40, 5, $secondaryRgb);
        }
        
        // Add brand text
        $font = 3; // Built-in font
        $text = $specs['brand'];
        $textWidth = imagefontwidth($font) * strlen($text);
        $textX = $phoneX + ($phoneWidth - $textWidth) / 2;
        $textY = $phoneY + $phoneHeight - 30;
        imagestring($image, $font, $textX, $textY, $text, $white);
        
        // Save image
        if (imagejpeg($image, $filepath, 90)) {
            $imagePaths[] = 'uploads/phones/' . $filename;
            echo "✓ Created: $filename<br>";
        } else {
            echo "✗ Failed to create: $filename<br>";
        }
        
        imagedestroy($image);
    }
    
    return $imagePaths;
}

function hexToRgb($hex) {
    $hex = ltrim($hex, '#');
    return [
        'r' => hexdec(substr($hex, 0, 2)),
        'g' => hexdec(substr($hex, 2, 2)),
        'b' => hexdec(substr($hex, 4, 2))
    ];
}

function drawRoundedRectangle($image, $x, $y, $width, $height, $radius, $color) {
    // Draw rounded rectangle using multiple rectangles and circles
    imagefilledrectangle($image, $x + $radius, $y, $x + $width - $radius, $y + $height, $color);
    imagefilledrectangle($image, $x, $y + $radius, $x + $width, $y + $height - $radius, $color);
    
    // Draw corners
    imagefilledellipse($image, $x + $radius, $y + $radius, $radius * 2, $radius * 2, $color);
    imagefilledellipse($image, $x + $width - $radius, $y + $radius, $radius * 2, $radius * 2, $color);
    imagefilledellipse($image, $x + $radius, $y + $height - $radius, $radius * 2, $radius * 2, $color);
    imagefilledellipse($image, $x + $width - $radius, $y + $height - $radius, $radius * 2, $radius * 2, $color);
}

echo "<h2>Creating Phone Images...</h2>";

$phoneImageMapping = [];
$successCount = 0;

foreach ($phoneSpecs as $phoneName => $specs) {
    echo "<h3>$phoneName</h3>";
    
    $imagePaths = createPhoneImage($phoneName, $specs, $phonesDir);
    
    if (!empty($imagePaths)) {
        $phoneImageMapping[$phoneName] = implode(',', $imagePaths);
        $successCount++;
        echo "✓ Created " . count($imagePaths) . " images for $phoneName<br>";
    } else {
        echo "✗ Failed to create images for $phoneName<br>";
    }
}

echo "<h2>Summary</h2>";
echo "<p><strong>Successfully created images for:</strong> $successCount out of " . count($phoneSpecs) . " phones</p>";

if ($successCount > 0) {
    // Save the image mapping to a file
    file_put_contents($phonesDir . '/phone_image_mapping.json', json_encode($phoneImageMapping, JSON_PRETTY_PRINT));
    echo "<p><strong>Image mapping saved to:</strong> uploads/phones/phone_image_mapping.json</p>";
    
    echo "<h2>Created Images</h2>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Phone Name</th><th>Image Paths</th></tr>";
    
    foreach ($phoneImageMapping as $phoneName => $imagePaths) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($phoneName) . "</td>";
        echo "<td>" . htmlspecialchars($imagePaths) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<br><p><strong>✅ Phone image creation completed!</strong></p>";
echo "<p><a href='update_dummy_data_with_phone_images.php'>Update Dummy Data with New Phone Images</a></p>";
?>

