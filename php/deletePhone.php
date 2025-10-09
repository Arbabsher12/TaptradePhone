<?php
session_start();
require_once 'db.php';

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

// Get phone ID
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
    echo json_encode(['success' => false, 'message' => 'Phone not found or you don\'t have permission to delete it']);
    exit();
}

$phone = $check_result->fetch_assoc();

// Delete the phone from the database
$delete_query = "DELETE FROM phones WHERE id = ? AND sellerid = ?";
$delete_stmt = $conn->prepare($delete_query);
$delete_stmt->bind_param("ii", $phone_id, $user_id);
$result = $delete_stmt->execute();

if ($result) {
    // Optionally, delete associated images
    $image_paths = $phone['image_paths'];
    if ($image_paths && $image_paths != '"/uploads/none.jpg"') {
        // Remove quotes and get images
        $image_paths = str_replace('"', '', $image_paths);
        $images = explode(',', $image_paths);
        
        foreach ($images as $image) {
            $image = trim($image);
            if (file_exists($image) && $image != '/uploads/none.jpg') {
                unlink($image);
            }
        }
    }
    
    echo json_encode(['success' => true, 'message' => 'Phone listing deleted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error deleting phone: ' . $conn->error]);
}
