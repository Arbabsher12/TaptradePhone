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
    // Debug: Log all POST data
    error_log("POST data: " . print_r($_POST, true));
    
    // Handle brand_id - convert "other" to NULL
    $brand_id_raw = $_POST["brand_id"];
    error_log("brand_id_raw: " . $brand_id_raw);
    $brand_id = ($brand_id_raw === "other") ? null : intval($brand_id_raw);
    
    // Handle model_id - convert "not_listed" to NULL
    // Note: When brand is "other", model_id might not be submitted at all (disabled field)
    $model_id_raw = isset($_POST["model_id"]) ? $_POST["model_id"] : null;
    error_log("model_id_raw: " . var_export($model_id_raw, true));
    if (empty($model_id_raw) || $model_id_raw === "not_listed") {
        $model_id = null;
    } else {
        $model_id = intval($model_id_raw);
    }
    
    // Get custom_model - this should be set when using custom brand or model
    $custom_model = isset($_POST["custom_model"]) ? trim($_POST["custom_model"]) : null;
    error_log("custom_model: " . var_export($custom_model, true));
    error_log("custom_model raw POST: " . var_export($_POST["custom_model"] ?? 'NOT SET', true));
    
    // Validate that we have either a model_id or custom_model
    if (empty($custom_model) && $model_id === null) {
        error_log("ERROR: No model_id and no custom_model provided");
        die(json_encode(["status" => "error", "message" => "Please select or enter a phone model."]));
    }
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
    error_log("Initial phoneName from custom_model: " . var_export($phoneName, true));
    
    if (empty($custom_model) && !empty($model_id)) {
        // Fetch model name from database
        $modelStmt = $conn->prepare("SELECT model_name FROM phone_models WHERE id = ?");
        $modelStmt->bind_param("i", $model_id);
        $modelStmt->execute();
        $modelResult = $modelStmt->get_result();
        if ($modelRow = $modelResult->fetch_assoc()) {
            $phoneName = $modelRow['model_name'];
        }
        $modelStmt->close();
    }
    
    // If we still don't have a phone name, create a default one
    if (empty($phoneName)) {
        if (!empty($brand_id)) {
            // Fetch brand name
            $brandStmt = $conn->prepare("SELECT name FROM brands WHERE id = ?");
            $brandStmt->bind_param("i", $brand_id);
            $brandStmt->execute();
            $brandResult = $brandStmt->get_result();
            if ($brandRow = $brandResult->fetch_assoc()) {
                $phoneName = $brandRow['name'] . " Phone";
            } else {
                $phoneName = "Phone";
            }
            $brandStmt->close();
        } else {
            $phoneName = "Phone";
        }
    }
    
    error_log("Final phoneName before insert: " . var_export($phoneName, true));

    // Build dynamic SQL based on whether brand_id and model_id are NULL
    // Since MySQL doesn't support variable types in prepare, we need separate queries
    if ($brand_id === null && $model_id === null) {
        $stmt = $conn->prepare("INSERT INTO phones (brand_id, model_id, sellerId, phone_name, phone_price, phone_storage, phone_color, phone_condition, phone_details, image_paths, seller_name, seller_email, seller_phone, seller_location, created_at)
        VALUES (NULL, NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        
        if (!$stmt) {
            die(json_encode(["status" => "error", "message" => "SQL Prepare failed: " . $conn->error]));
        }

        $stmt->bind_param("isdsisssssss", $sellerId, $phoneName, $phonePrice, $phoneStorage, $phoneColor, $phoneCondition, $phoneDetails, $imagePathsStr, $sellerName, $sellerEmail, $sellerPhone, $sellerLocation);
    } elseif ($brand_id === null) {
        $stmt = $conn->prepare("INSERT INTO phones (brand_id, model_id, sellerId, phone_name, phone_price, phone_storage, phone_color, phone_condition, phone_details, image_paths, seller_name, seller_email, seller_phone, seller_location, created_at)
        VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        
        if (!$stmt) {
            die(json_encode(["status" => "error", "message" => "SQL Prepare failed: " . $conn->error]));
        }

        $stmt->bind_param("iisdsisssssss", $model_id, $sellerId, $phoneName, $phonePrice, $phoneStorage, $phoneColor, $phoneCondition, $phoneDetails, $imagePathsStr, $sellerName, $sellerEmail, $sellerPhone, $sellerLocation);
    } elseif ($model_id === null) {
        $stmt = $conn->prepare("INSERT INTO phones (brand_id, model_id, sellerId, phone_name, phone_price, phone_storage, phone_color, phone_condition, phone_details, image_paths, seller_name, seller_email, seller_phone, seller_location, created_at)
        VALUES (?, NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        
        if (!$stmt) {
            die(json_encode(["status" => "error", "message" => "SQL Prepare failed: " . $conn->error]));
        }

        $stmt->bind_param("iisdsisssssss", $brand_id, $sellerId, $phoneName, $phonePrice, $phoneStorage, $phoneColor, $phoneCondition, $phoneDetails, $imagePathsStr, $sellerName, $sellerEmail, $sellerPhone, $sellerLocation);
    } else {
        $stmt = $conn->prepare("INSERT INTO phones (brand_id, model_id, sellerId, phone_name, phone_price, phone_storage, phone_color, phone_condition, phone_details, image_paths, seller_name, seller_email, seller_phone, seller_location, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        
        if (!$stmt) {
            die(json_encode(["status" => "error", "message" => "SQL Prepare failed: " . $conn->error]));
        }

        $stmt->bind_param("iiisdsisssssss", $brand_id, $model_id, $sellerId, $phoneName, $phonePrice, $phoneStorage, $phoneColor, $phoneCondition, $phoneDetails, $imagePathsStr, $sellerName, $sellerEmail, $sellerPhone, $sellerLocation);
    }


    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Your phone listing has been successfully created!"]);
    } else {
        error_log("SQL Error: " . $stmt->error);
        error_log("brand_id: " . var_export($brand_id, true));
        error_log("model_id: " . var_export($model_id, true));
        error_log("custom_model: " . var_export($custom_model, true));
        echo json_encode(["status" => "error", "message" => "Failed to store data: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
    exit;
}


?>

