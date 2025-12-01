<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Dummy Phone Data</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
        }
        .button {
            background-color: #007bff;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px;
        }
        .button:hover {
            background-color: #0056b3;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .info {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📱 Add Dummy Phone Data</h1>
        
        <div class="info">
            <strong>What this does:</strong><br>
            • Adds 10 realistic phone listings with images<br>
            • Includes popular brands: Apple, Samsung, Google, OnePlus, Xiaomi<br>
            • Uses existing images from your uploads folder<br>
            • Clears previous dummy data before adding new ones
        </div>

        <?php
        if (isset($_POST['add_dummy_data'])) {
            echo '<div class="success">';
            echo '<h3>Running Dummy Data Script...</h3>';
            
            // Capture output from the script
            ob_start();
            include 'add_dummy_phones.php';
            $output = ob_get_clean();
            
            // Display the output
            echo $output;
            echo '</div>';
        }
        ?>

        <div class="warning">
            <strong>⚠️ Warning:</strong> This will clear existing dummy data and add new sample phones. 
            Your real user data will not be affected.
        </div>

        <form method="POST">
            <button type="submit" name="add_dummy_data" class="button">
                🚀 Add Dummy Phone Data
            </button>
        </form>

        <div style="margin-top: 30px; text-align: center;">
            <a href="../html/allPhones.php" class="button">View All Phones</a>
            <a href="../index.php" class="button">Go to Home</a>
        </div>

        <div class="info" style="margin-top: 30px;">
            <strong>Sample phones that will be added:</strong><br>
            • iPhone 15 Pro Max - PKR 450,000<br>
            • Samsung Galaxy S23 Ultra - PKR 380,000<br>
            • Google Pixel 8 Pro - PKR 320,000<br>
            • OnePlus 11 - PKR 280,000<br>
            • Xiaomi 13 Pro - PKR 250,000<br>
            • iPhone 14 Pro - PKR 350,000<br>
            • Samsung Galaxy S22 Ultra - PKR 300,000<br>
            • Google Pixel 7 Pro - PKR 260,000<br>
            • OnePlus Nord 3 - PKR 180,000<br>
            • Xiaomi Redmi Note 12 Pro+ - PKR 120,000
        </div>
    </div>
</body>
</html>

