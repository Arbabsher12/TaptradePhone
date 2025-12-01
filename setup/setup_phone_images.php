<?php
/**
 * Simple Phone Image Setup Script
 * Creates placeholder phone images using simple HTML/CSS approach
 */

// Create phones directory if it doesn't exist
$phonesDir = __DIR__ . '/../uploads/phones';
if (!file_exists($phonesDir)) {
    mkdir($phonesDir, 0755, true);
}

echo "<h1>📱 Setting Up Phone Images</h1>";

// Phone specifications
$phones = [
    'iPhone 15 Pro Max' => ['color' => '#8B8B8B', 'brand' => 'Apple'],
    'Samsung Galaxy S23 Ultra' => ['color' => '#000000', 'brand' => 'Samsung'],
    'Google Pixel 8 Pro' => ['color' => '#2C2C2C', 'brand' => 'Google'],
    'OnePlus 11' => ['color' => '#1A1A1A', 'brand' => 'OnePlus'],
    'Xiaomi 13 Pro' => ['color' => '#FFFFFF', 'brand' => 'Xiaomi'],
    'iPhone 14 Pro' => ['color' => '#4A148C', 'brand' => 'Apple'],
    'Samsung Galaxy S22 Ultra' => ['color' => '#800020', 'brand' => 'Samsung'],
    'Google Pixel 7 Pro' => ['color' => '#8B4513', 'brand' => 'Google'],
    'OnePlus Nord 3' => ['color' => '#90EE90', 'brand' => 'OnePlus'],
    'Xiaomi Redmi Note 12 Pro+' => ['color' => '#87CEEB', 'brand' => 'Xiaomi']
];

$phoneImageMapping = [];

echo "<h2>Creating Phone Image Placeholders...</h2>";

foreach ($phones as $phoneName => $specs) {
    echo "<h3>$phoneName</h3>";
    $imagePaths = [];
    
    // Create 3 placeholder images for each phone
    for ($i = 1; $i <= 3; $i++) {
        $filename = 'phone_' . preg_replace('/[^a-zA-Z0-9]/', '_', $phoneName) . '_' . $i . '.jpg';
        $filepath = $phonesDir . '/' . $filename;
        
        // Create a simple HTML file that can be converted to image
        $html = "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { margin: 0; padding: 0; background: white; }
                .phone { 
                    width: 200px; 
                    height: 400px; 
                    background: {$specs['color']}; 
                    border-radius: 20px; 
                    margin: 20px auto; 
                    position: relative;
                    box-shadow: 0 4px 8px rgba(0,0,0,0.3);
                }
                .screen { 
                    width: 160px; 
                    height: 280px; 
                    background: black; 
                    border-radius: 10px; 
                    position: absolute; 
                    top: 40px; 
                    left: 20px; 
                }
                .brand { 
                    color: white; 
                    text-align: center; 
                    font-family: Arial; 
                    font-size: 12px; 
                    position: absolute; 
                    bottom: 20px; 
                    width: 100%; 
                }
            </style>
        </head>
        <body>
            <div class='phone'>
                <div class='screen'></div>
                <div class='brand'>{$specs['brand']}</div>
            </div>
        </body>
        </html>";
        
        // For now, create a simple text file that represents the image
        $imageContent = "Phone Image: $phoneName\nView: $i\nColor: {$specs['color']}\nBrand: {$specs['brand']}\n";
        
        if (file_put_contents($filepath, $imageContent)) {
            $imagePaths[] = 'uploads/phones/' . $filename;
            echo "✓ Created placeholder: $filename<br>";
        } else {
            echo "✗ Failed to create: $filename<br>";
        }
    }
    
    if (!empty($imagePaths)) {
        $phoneImageMapping[$phoneName] = implode(',', $imagePaths);
    }
}

echo "<h2>Summary</h2>";
echo "<p><strong>Created placeholders for:</strong> " . count($phoneImageMapping) . " phones</p>";

if (!empty($phoneImageMapping)) {
    // Save the image mapping
    file_put_contents($phonesDir . '/phone_image_mapping.json', json_encode($phoneImageMapping, JSON_PRETTY_PRINT));
    echo "<p><strong>Image mapping saved to:</strong> uploads/phones/phone_image_mapping.json</p>";
    
    echo "<h2>Created Placeholders</h2>";
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

echo "<br><p><strong>✅ Phone image setup completed!</strong></p>";
echo "<p><strong>Note:</strong> These are placeholder images. For real phone images, you can:</p>";
echo "<ul>";
echo "<li>Download images from <a href='https://unsplash.com/s/photos/phone' target='_blank'>Unsplash</a></li>";
echo "<li>Use images from <a href='https://www.pexels.com/search/phone/' target='_blank'>Pexels</a></li>";
echo "<li>Replace the placeholder files in uploads/phones/ with real images</li>";
echo "</ul>";
echo "<p><a href='update_dummy_data_with_phone_images.php'>Update Dummy Data with Phone Images</a></p>";
?>

