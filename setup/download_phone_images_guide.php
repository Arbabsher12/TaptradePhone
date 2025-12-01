<?php
/**
 * Download Real Phone Images Guide
 * This script provides a guide and tools to download real phone images
 */

echo "<h1>📱 Download Real Phone Images</h1>";

echo "<h2>🎯 What We've Done So Far</h2>";
echo "<ul>";
echo "<li>✅ Created placeholder phone images for all 10 dummy phones</li>";
echo "<li>✅ Updated database with proper phone image paths</li>";
echo "<li>✅ All phones now have 3 images each (30 total images)</li>";
echo "</ul>";

echo "<h2>📥 How to Get Real Phone Images</h2>";

echo "<h3>Option 1: Manual Download (Recommended)</h3>";
echo "<ol>";
echo "<li><strong>Visit Free Image Sources:</strong>";
echo "<ul>";
echo "<li><a href='https://unsplash.com/s/photos/phone' target='_blank'>Unsplash - Phone Photos</a></li>";
echo "<li><a href='https://www.pexels.com/search/phone/' target='_blank'>Pexels - Phone Images</a></li>";
echo "<li><a href='https://pixabay.com/images/search/phone/' target='_blank'>Pixabay - Phone Images</a></li>";
echo "</ul>";
echo "</li>";
echo "<li><strong>Search for specific phones:</strong>";
echo "<ul>";
echo "<li>iPhone 15 Pro Max</li>";
echo "<li>Samsung Galaxy S23 Ultra</li>";
echo "<li>Google Pixel 8 Pro</li>";
echo "<li>OnePlus 11</li>";
echo "<li>Xiaomi 13 Pro</li>";
echo "</ul>";
echo "</li>";
echo "<li><strong>Download 3 images per phone</strong></li>";
echo "<li><strong>Replace files in:</strong> <code>uploads/phones/</code></li>";
echo "</ol>";

echo "<h3>Option 2: Use Our Download Script</h3>";
echo "<p>We've created a script that attempts to download images automatically:</p>";
echo "<p><a href='download_real_phone_images.php' class='button'>Try Automatic Download</a></p>";

echo "<h2>📁 Current Image Structure</h2>";
echo "<p>Your phone images are stored in: <code>uploads/phones/</code></p>";

// List current phone images
$phonesDir = __DIR__ . '/../uploads/phones';
if (file_exists($phonesDir)) {
    $files = scandir($phonesDir);
    $imageFiles = array_filter($files, function($file) {
        return pathinfo($file, PATHINFO_EXTENSION) === 'jpg';
    });
    
    echo "<h3>Current Phone Images (" . count($imageFiles) . " files):</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Filename</th><th>Size</th><th>Type</th></tr>";
    
    foreach ($imageFiles as $file) {
        $filepath = $phonesDir . '/' . $file;
        $size = file_exists($filepath) ? filesize($filepath) : 0;
        $sizeKB = round($size / 1024, 2);
        echo "<tr>";
        echo "<td>" . htmlspecialchars($file) . "</td>";
        echo "<td>{$sizeKB} KB</td>";
        echo "<td>" . (strpos($file, 'phone_') === 0 ? 'Phone Image' : 'Other') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<h2>🔄 Next Steps</h2>";
echo "<ol>";
echo "<li><strong>Download real phone images</strong> from the sources above</li>";
echo "<li><strong>Replace placeholder files</strong> in uploads/phones/ with real images</li>";
echo "<li><strong>Keep the same filenames</strong> so the database references work correctly</li>";
echo "<li><strong>Test your website</strong> to see the real phone images</li>";
echo "</ol>";

echo "<h2>📋 Filename Reference</h2>";
echo "<p>Here are the exact filenames you need to replace:</p>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Phone Name</th><th>Image Files to Replace</th></tr>";

$phoneNames = [
    'iPhone 15 Pro Max',
    'Samsung Galaxy S23 Ultra', 
    'Google Pixel 8 Pro',
    'OnePlus 11',
    'Xiaomi 13 Pro',
    'iPhone 14 Pro',
    'Samsung Galaxy S22 Ultra',
    'Google Pixel 7 Pro',
    'OnePlus Nord 3',
    'Xiaomi Redmi Note 12 Pro+'
];

foreach ($phoneNames as $phoneName) {
    $safeName = preg_replace('/[^a-zA-Z0-9]/', '_', $phoneName);
    echo "<tr>";
    echo "<td>" . htmlspecialchars($phoneName) . "</td>";
    echo "<td>";
    echo "phone_{$safeName}_1.jpg<br>";
    echo "phone_{$safeName}_2.jpg<br>";
    echo "phone_{$safeName}_3.jpg";
    echo "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<h2>✅ Verification</h2>";
echo "<p>After replacing images, verify everything works:</p>";
echo "<p><a href='verify_dummy_data.php' class='button'>Verify Database</a></p>";
echo "<p><a href='../html/allPhones.php' class='button'>View All Phones</a></p>";

echo "<style>";
echo ".button { display: inline-block; background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px; }";
echo ".button:hover { background: #0056b3; }";
echo "table { margin: 10px 0; }";
echo "th, td { padding: 8px; text-align: left; }";
echo "</style>";
?>

