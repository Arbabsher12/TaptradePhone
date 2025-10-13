<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include __DIR__ . '/db.php';

$isLoggedIn = isset($_SESSION['user_id']);
$profile_picture = "../Components/noDp.png"; // Default image

if ($isLoggedIn) {
    $user_id = $_SESSION['user_id'];


    // Fetch user details 
    $query = "SELECT profile_picture FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($profile_picture);
    $stmt->fetch();
    $stmt->close();

    // Set default profile picture if none is found
    if (empty($profile_picture)) {
        $profile_picture = "../Components/noDp.png";
    }
}
else {
    // Redirect to login page if not logged in
    header("Location: /login");
    exit;
}




// Fetch brands for the dropdown
if(!function_exists('getBrands')) {
    function getBrands() {
        global $conn;
        $brands = [];
        
        // Check if database connection exists
        if (!$conn) {
            error_log("Database connection is null in getBrands() function");
            return $brands;
        }
        
        // First check if brands table exists
        $tableCheck = $conn->query("SHOW TABLES LIKE 'brands'");
        if (!$tableCheck || $tableCheck->num_rows == 0) {
            error_log("Brands table does not exist in database");
            return $brands;
        }
        
        $sql = "SELECT id, name, logo FROM brands ORDER BY name";
        $result = $conn->query($sql);
        
        if ($result) {
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $brands[] = $row;
                }
                error_log("Successfully loaded " . count($brands) . " brands");
            } else {
                error_log("Brands table exists but is empty");
            }
        } else {
            error_log("Query failed: " . $conn->error);
        }
        return $brands;
    }
}

$brands = getBrands();

// If no brands found in database, provide fallback brands
if (empty($brands)) {
    $brands = [
        ['id' => 1, 'name' => 'Apple', 'logo' => 'apple-logo.png'],
        ['id' => 2, 'name' => 'Samsung', 'logo' => 'samsung-logo.png'],
        ['id' => 3, 'name' => 'Google', 'logo' => 'google-logo.png'],
        ['id' => 4, 'name' => 'Xiaomi', 'logo' => 'xiaomi-logo.png'],
        ['id' => 5, 'name' => 'OnePlus', 'logo' => 'oneplus-logo.png'],
        ['id' => 6, 'name' => 'Huawei', 'logo' => 'huawei-logo.png'],
        ['id' => 7, 'name' => 'Motorola', 'logo' => 'motorola-logo.png'],
        ['id' => 8, 'name' => 'Sony', 'logo' => 'sony-logo.png']
    ];
    error_log("Using fallback brands as database brands are not available");
}



// Fetch phone models by brand
if (isset($_GET['action']) && $_GET['action'] == 'getModels' && isset($_GET['brand_id'])) {
    $brand_id = intval($_GET['brand_id']);
    
    $stmt = $conn->prepare("SELECT id, model_name FROM phone_models WHERE brand_id = ? ORDER BY model_name");
    $stmt->bind_param("i", $brand_id);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $models = [];
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $models[] = $row;
        }
    }
    
    echo json_encode($models);
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $brand_id = $_POST["brand_id"];
    $model_id = $_POST["model_id"];
    $custom_model = isset($_POST["custom_model"]) ? $_POST["custom_model"] : null;
    $phonePrice = $_POST["phonePrice"];
    $phoneStorage = $_POST["phoneStorage"];
    $phoneColor = $_POST["phoneColor"];
    $phoneCondition = $_POST["phoneCondition"];
    $phoneDetails = $_POST["phoneDetails"];
    $sellerId= $_SESSION['user_id']; 
    $sellerName = $_POST["sellerName"];
    $sellerEmail = $_POST["sellerEmail"];
    $sellerPhone = $_POST["sellerPhone"];
    $sellerLocation = $_POST["sellerLocation"];
    

    $uploadDir = "../uploads/"; // Ensure folder exists inside the current directory

    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0777, true)) {
            die(json_encode(["status" => "error", "message" => "Failed to create upload directory."]));
        }
    }

    $imagePaths = [];

    if (!empty($_FILES["phoneImages"]["name"][0])) {
        foreach ($_FILES["phoneImages"]["tmp_name"] as $key => $tmpName) {
            if ($_FILES["phoneImages"]["error"][$key] == 0) {
                $fileName = time() . "_" . basename($_FILES["phoneImages"]["name"][$key]);
                $targetPath = $uploadDir . $fileName;

                if (move_uploaded_file($tmpName, $targetPath)) {
                    $imagePaths[] = $fileName; // Store only the filename, not the full path
                } else {
                    echo json_encode(["status" => "error", "message" => "Error uploading file: " . $_FILES["phoneImages"]["name"][$key]]);
                    exit;
                }
            }
        }
    }

    if (empty($imagePaths)) {
        echo json_encode(["status" => "error", "message" => "No images were uploaded."]);
        exit;
    }

    $imagePathsStr = implode(",", $imagePaths); // Store multiple paths as a comma-separated string

    // Determine the phone name (either from model or custom input)
    $phoneName = $custom_model;
    if (empty($custom_model) && !empty($model_id)) {
        $modelStmt = $conn->prepare("SELECT model_name FROM phone_models WHERE id = ?");
        $modelStmt->bind_param("i", $model_id);
        $modelStmt->execute();
        $modelResult = $modelStmt->get_result();
        if ($modelRow = $modelResult->fetch_assoc()) {
            $phoneName = $modelRow['model_name'];
        }
        $modelStmt->close();
    }

    $stmt = $conn->prepare("INSERT INTO phones (brand_id, model_id, sellerId, phone_name, phone_price, phone_storage, phone_color, phone_condition, phone_details, image_paths, seller_name, seller_email, seller_phone, seller_location, created_at)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    
    if (!$stmt) {
        die(json_encode(["status" => "error", "message" => "SQL Prepare failed: " . $conn->error]));
    }

    $stmt->bind_param("iiisdsssssssss", $brand_id, $model_id, $sellerId, $phoneName, $phonePrice, $phoneStorage, $phoneColor, $phoneCondition, $phoneDetails, $imagePathsStr, $sellerName, $sellerEmail, $sellerPhone, $sellerLocation);


    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Your phone listing has been successfully created!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to store data: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
    exit;
}


?>

