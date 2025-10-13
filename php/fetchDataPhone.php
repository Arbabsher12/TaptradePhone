<?php
// Only set header if not already sent
if (!headers_sent()) {
    header("Content-Type: application/json");
}

include __DIR__ . '/db.php';

// Initialize variables
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
$price_ranges = isset($_GET['price_range']) ? explode(',', $_GET['price_range']) : [];
$brands = isset($_GET['brands']) ? explode(',', $_GET['brands']) : [];
$conditions = isset($_GET['conditions']) ? explode(',', $_GET['conditions']) : [];
$storage = isset($_GET['storage']) ? explode(',', $_GET['storage']) : [];
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 12;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Build query
$query = "SELECT p.*, 
          (SELECT COUNT(*) FROM phones WHERE id = p.id) as image_count
          FROM phones p
          WHERE 1=1";

// Add search condition
if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $query .= " AND (p.phone_name LIKE '%$search%' OR p.phone_details LIKE '%$search%')";
}

// Add price range conditions
if (!empty($price_ranges)) {
    $price_conditions = [];
    foreach ($price_ranges as $range) {
        $range_parts = explode('-', $range);
        if (count($range_parts) == 2) {
            $min = (float)$range_parts[0];
            $max = (float)$range_parts[1];
            $price_conditions[] = "(p.phone_price BETWEEN $min AND $max)";
        }
    }
    if (!empty($price_conditions)) {
        $query .= " AND (" . implode(" OR ", $price_conditions) . ")";
    }
}

// Add brand conditions
if (!empty($brands)) {
    $brand_conditions = [];
    foreach ($brands as $brand) {
        $brand = $conn->real_escape_string($brand);
        $brand_conditions[] = "p.phone_name LIKE '%$brand%'";
    }
    if (!empty($brand_conditions)) {
        $query .= " AND (" . implode(" OR ", $brand_conditions) . ")";
    }
}

// Add condition filters
if (!empty($conditions)) {
    $condition_ranges = [];
    foreach ($conditions as $condition) {
        switch ($condition) {
            case 'new':
                $condition_ranges[] = "p.phone_condition = 10";
                break;
            case 'like_new':
                $condition_ranges[] = "p.phone_condition BETWEEN 9 AND 10";
                break;
            case 'excellent':
                $condition_ranges[] = "p.phone_condition BETWEEN 7 AND 8";
                break;
            case 'good':
                $condition_ranges[] = "p.phone_condition BETWEEN 5 AND 6";
                break;
            case 'fair':
                $condition_ranges[] = "p.phone_condition < 5";
                break;
        }
    }
    if (!empty($condition_ranges)) {
        $query .= " AND (" . implode(" OR ", $condition_ranges) . ")";
    }
}

// Add storage filters
if (!empty($storage)) {
    $storage_conditions = [];
    foreach ($storage as $storage_size) {
        $storage_size = $conn->real_escape_string($storage_size);
        $storage_conditions[] = "p.phone_storage LIKE '%$storage_size%'";
    }
    if (!empty($storage_conditions)) {
        $query .= " AND (" . implode(" OR ", $storage_conditions) . ")";
    }
}

// Add sorting
switch ($sort) {
    case 'price_high':
        $query .= " ORDER BY p.phone_price DESC";
        break;
    case 'price_low':
        $query .= " ORDER BY p.phone_price ASC";
        break;
    case 'popularity':
        $query .= " ORDER BY p.views DESC";
        break;
    case 'oldest':
        $query .= " ORDER BY p.created_at ASC";
        break;
    case 'newest':
    default:
        $query .= " ORDER BY p.created_at DESC";
        break;
}

// Add limit and offset for pagination
$query .= " LIMIT $limit OFFSET $offset";

// Execute query
$result = $conn->query($query);

$phones = [];

// Debug information (remove in production)
error_log("Query: " . $query);
error_log("Page: $page, Limit: $limit, Offset: $offset");

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Process image paths properly
        $firstImage = '/uploads/none.jpg'; // Default image path
        
        if (!empty($row["image_paths"])) {
            // Remove quotes and get the first image
            $image_paths = str_replace('"', '', $row["image_paths"]);
            $images = explode(',', $image_paths);
            
            if (!empty($images[0])) {
                $firstImage = trim($images[0]);
            }
        }

        $phones[] = [
            "id" => $row["id"],
            "name" => $row["phone_name"],
            "price" => $row["phone_price"],
            "condition" => $row["phone_condition"],
            "details" => $row["phone_details"],
            "image" => $firstImage,
            "created_at" => $row["created_at"],
            "views" => $row["views"]
        ];
    }
}

// Return JSON response with metadata
$response = [
    'phones' => $phones,
    'total' => count($phones),
    'page' => $page,
    'hasMore' => count($phones) >= $limit
];

echo json_encode($response);
$conn->close();
?>
