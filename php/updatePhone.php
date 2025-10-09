<?php
session_start();
require_once 'db.php'; // Changed from config.php to db.php to match your code

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

// Get phone ID and validate ownership
$phone_id = isset($_POST['id']) ? intval($_POST['id']) : 0;

if (!$phone_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid phone ID']);
    exit();
}

// Check if the phone belongs to the user
$check_query = "SELECT id, image_paths FROM phones WHERE id = ? AND sellerid = ?";
$check_stmt = $conn->prepare($check_query);
$check_stmt->bind_param("ii", $phone_id, $user_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Phone not found or you don\'t have permission to edit it']);
    exit();
}

$phone = $check_result->fetch_assoc();
$current_image_paths = $phone['image_paths'];

// Get form data
$phone_name = $_POST['phone_name'];
$brand_id = intval($_POST['brand_id']);
$model_id = intval($_POST['model_id']);
$phone_price = floatval($_POST['phone_price']);
$phone_condition = intval($_POST['phone_condition']);
$phone_storage = $_POST['phone_storage'] ?? null;
$phone_color = $_POST['phone_color'] ?? null;
$phone_details = $_POST['phone_details'] ?? null;

// Handle removed images
$removed_images = [];
if (!empty($_POST['removed_images'])) {
    $removed_images = json_decode($_POST['removed_images'], true);
}

// Process current images
$current_images = [];
if ($current_image_paths && $current_image_paths != '"/uploads/none.jpg"') {
    // Remove quotes and get images
    $current_image_paths = str_replace('"', '', $current_image_paths);
    $images = explode(',', $current_image_paths);
    
    foreach ($images as $image) {
        $image = trim($image);
        if (!in_array($image, $removed_images)) {
            $current_images[] = $image;
        }
    }
}

// Handle new image uploads
$new_images = [];
$upload_errors = [];

if (!empty($_FILES['new_images']['name'][0])) {
    // Define upload directory - use a more reliable path determination
    $upload_dir = dirname(__FILE__) . '/../uploads/';
    
    // Debug information
    $debug_info = [
        'script_path' => dirname(__FILE__),
        'upload_dir' => $upload_dir,
        'dir_exists' => file_exists($upload_dir) ? 'Yes' : 'No',
        'dir_writable' => is_writable($upload_dir) ? 'Yes' : 'No',
        'php_version' => PHP_VERSION,
        'max_file_size' => ini_get('upload_max_filesize'),
        'post_max_size' => ini_get('post_max_size')
    ];
    
    // Make sure the upload directory exists with proper permissions
    if (!file_exists($upload_dir)) {
        $mkdir_result = mkdir($upload_dir, 0777, true);
        $debug_info['mkdir_result'] = $mkdir_result ? 'Success' : 'Failed';
        $debug_info['mkdir_error'] = $mkdir_result ? '' : error_get_last()['message'];
    } else if (!is_writable($upload_dir)) {
        // Try to make the directory writable
        $chmod_result = chmod($upload_dir, 0777);
        $debug_info['chmod_result'] = $chmod_result ? 'Success' : 'Failed';
    }
    
    // Process each uploaded file
    foreach ($_FILES['new_images']['name'] as $key => $name) {
        $tmp_name = $_FILES['new_images']['tmp_name'][$key];
        $error = $_FILES['new_images']['error'][$key];
        
        if ($error === UPLOAD_ERR_OK) {
            // Get file info
            $file_info = [
                'name' => $name,
                'tmp_name' => $tmp_name,
                'size' => $_FILES['new_images']['size'][$key],
                'type' => $_FILES['new_images']['type'][$key],
                'error_code' => $error
            ];
            
            // Generate a unique filename
            $file_extension = pathinfo($name, PATHINFO_EXTENSION);
            $new_filename = uniqid() . '.' . $file_extension;
            $destination = $upload_dir . $new_filename;
            
            // Try to move the uploaded file
            $move_result = move_uploaded_file($tmp_name, $destination);
            
            if ($move_result) {
                $new_images[] = '' . $new_filename; // Store relative path
                $debug_info['successful_uploads'][] = [
                    'original' => $name,
                    'new' => $new_filename,
                    'path' => $destination
                ];
            } else {
                // Get detailed error information
                $upload_errors[] = [
                    'file' => $name,
                    'error' => 'Failed to move uploaded file',
                    'tmp_name' => $tmp_name,
                    'destination' => $destination,
                    'file_exists' => file_exists($tmp_name) ? 'Yes' : 'No',
                    'is_uploaded_file' => is_uploaded_file($tmp_name) ? 'Yes' : 'No',
                    'php_error' => error_get_last()
                ];
            }
        } else {
            // Map error code to message
            $error_message = '';
            switch ($error) {
                case UPLOAD_ERR_INI_SIZE:
                    $error_message = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    $error_message = 'The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form';
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $error_message = 'The uploaded file was only partially uploaded';
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $error_message = 'No file was uploaded';
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $error_message = 'Missing a temporary folder';
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $error_message = 'Failed to write file to disk';
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $error_message = 'A PHP extension stopped the file upload';
                    break;
                default:
                    $error_message = 'Unknown upload error';
            }
            
            $upload_errors[] = [
                'file' => $name,
                'error_code' => $error,
                'error_message' => $error_message
            ];
        }
    }
}

// Combine current and new images
$all_images = array_merge($current_images, $new_images);
$image_paths = !empty($all_images) ? implode(',', $all_images) : '/uploads/none.jpg';

// Update phone data in the database
$update_query = "UPDATE phones SET 
                phone_name = ?, 
                brand_id = ?, 
                model_id = ?, 
                phone_price = ?, 
                phone_condition = ?, 
                phone_storage = ?, 
                phone_color = ?, 
                phone_details = ?,
                image_paths = ?
                WHERE id = ? AND sellerid = ?";

$update_stmt = $conn->prepare($update_query);
$update_stmt->bind_param("siidissssii", 
    $phone_name, 
    $brand_id, 
    $model_id, 
    $phone_price, 
    $phone_condition, 
    $phone_storage, 
    $phone_color, 
    $phone_details,
    $image_paths,
    $phone_id,
    $user_id
);
$result = $update_stmt->execute();

if ($result) {
    // Delete removed image files
    foreach ($removed_images as $image) {
        $file_path = $_SERVER['DOCUMENT_ROOT'] . $image; // Use document root for absolute path
        if (file_exists($file_path) && $image != '/uploads/none.jpg') {
            unlink($file_path);
        }
    }
    
    $response = [
        'success' => true, 
        'message' => 'Phone listing updated successfully',
        'debug' => [
            'new_images' => $new_images,
            'upload_errors' => $upload_errors,
            'upload_info' => $debug_info ?? null,
            'image_paths' => $image_paths
        ]
    ];
    
    echo json_encode($response);
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Error updating phone: ' . $conn->error,
        'debug' => [
            'new_images' => $new_images,
            'upload_errors' => $upload_errors,
            'upload_info' => $debug_info ?? null,
            'image_paths' => $image_paths
        ]
    ]);
}
