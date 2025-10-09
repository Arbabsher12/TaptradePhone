<?php
session_start();
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([]);
    exit();
}

$brand_id = isset($_GET['brand_id']) ? intval($_GET['brand_id']) : 0;

if (!$brand_id) {
    echo json_encode([]);
    exit();
}

// Fetch models for the selected brand
$query = "SELECT id, model_name FROM phone_models WHERE brand_id = ? ORDER BY model_name";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $brand_id);
$stmt->execute();
$result = $stmt->get_result();
$models = $result->fetch_all(MYSQLI_ASSOC);

// Return models as JSON
header('Content-Type: application/json');
echo json_encode($models);
