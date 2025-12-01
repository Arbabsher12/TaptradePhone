<?php
/**
 * Download Phone Images Script
 * Downloads proper phone images from free sources and saves them to uploads directory
 */

// Create phones directory if it doesn't exist
$phonesDir = __DIR__ . '/../uploads/phones';
if (!file_exists($phonesDir)) {
    mkdir($phonesDir, 0755, true);
}

echo "<h1>📱 Downloading Phone Images</h1>";

// Phone image URLs from free sources (using direct URLs to avoid API complexity)
$phoneImages = [
    'iPhone 15 Pro Max' => [
        'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=800&h=600&fit=crop',
        'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=800&h=600&fit=crop',
        'https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?w=800&h=600&fit=crop'
    ],
    'Samsung Galaxy S23 Ultra' => [
        'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=800&h=600&fit=crop',
        'https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?w=800&h=600&fit=crop',
        'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=800&h=600&fit=crop'
    ],
    'Google Pixel 8 Pro' => [
        'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=800&h=600&fit=crop',
        'https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?w=800&h=600&fit=crop',
        'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=800&h=600&fit=crop'
    ],
    'OnePlus 11' => [
        'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=800&h=600&fit=crop',
        'https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?w=800&h=600&fit=crop',
        'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=800&h=600&fit=crop'
    ],
    'Xiaomi 13 Pro' => [
        'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=800&h=600&fit=crop',
        'https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?w=800&h=600&fit=crop',
        'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=800&h=600&fit=crop'
    ],
    'iPhone 14 Pro' => [
        'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=800&h=600&fit=crop',
        'https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?w=800&h=600&fit=crop',
        'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=800&h=600&fit=crop'
    ],
    'Samsung Galaxy S22 Ultra' => [
        'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=800&h=600&fit=crop',
        'https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?w=800&h=600&fit=crop',
        'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=800&h=600&fit=crop'
    ],
    'Google Pixel 7 Pro' => [
        'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=800&h=600&fit=crop',
        'https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?w=800&h=600&fit=crop',
        'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=800&h=600&fit=crop'
    ],
    'OnePlus Nord 3' => [
        'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=800&h=600&fit=crop',
        'https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?w=800&h=600&fit=crop',
        'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=800&h=600&fit=crop'
    ],
    'Xiaomi Redmi Note 12 Pro+' => [
        'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=800&h=600&fit=crop',
        'https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?w=800&h=600&fit=crop',
        'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=800&h=600&fit=crop'
    ]
];

function downloadImage($url, $filename) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $data = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode == 200 && $data !== false) {
        return file_put_contents($filename, $data);
    }
    
    return false;
}

$downloadedImages = [];
$successCount = 0;
$totalCount = 0;

echo "<h2>Downloading Images...</h2>";

foreach ($phoneImages as $phoneName => $urls) {
    echo "<h3>$phoneName</h3>";
    $phoneImages = [];
    
    foreach ($urls as $index => $url) {
        $totalCount++;
        $filename = 'phone_' . preg_replace('/[^a-zA-Z0-9]/', '_', $phoneName) . '_' . ($index + 1) . '.jpg';
        $filepath = $phonesDir . '/' . $filename;
        
        echo "Downloading image " . ($index + 1) . "... ";
        
        if (downloadImage($url, $filepath)) {
            echo "✓ Success<br>";
            $phoneImages[] = 'uploads/phones/' . $filename;
            $successCount++;
        } else {
            echo "✗ Failed<br>";
        }
    }
    
    if (!empty($phoneImages)) {
        $downloadedImages[$phoneName] = implode(',', $phoneImages);
    }
}

echo "<h2>Summary</h2>";
echo "<p><strong>Successfully downloaded:</strong> $successCount out of $totalCount images</p>";

if ($successCount > 0) {
    echo "<h2>Downloaded Images</h2>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Phone Name</th><th>Image Paths</th></tr>";
    
    foreach ($downloadedImages as $phoneName => $imagePaths) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($phoneName) . "</td>";
        echo "<td>" . htmlspecialchars($imagePaths) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Save the image mapping to a file for use in the dummy data script
    file_put_contents($phonesDir . '/image_mapping.json', json_encode($downloadedImages, JSON_PRETTY_PRINT));
    echo "<p><strong>Image mapping saved to:</strong> uploads/phones/image_mapping.json</p>";
}

echo "<br><p><strong>✅ Image download completed!</strong></p>";
echo "<p><a href='update_dummy_data_with_phone_images.php'>Update Dummy Data with Phone Images</a></p>";
?>

