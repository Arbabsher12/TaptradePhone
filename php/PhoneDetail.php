<?php
    
    include __DIR__ . '/../db.php';

    // Get phone ID from URL
    $phone_id = isset($_GET['id']) ? intval($_GET['id']) : 1;

    if ($phone_id <= 0) {
        // Redirect to home page if no valid ID
        header("Location: home.html");
        exit;
    }

    // Get phone details
    $phone_query = "SELECT p.*, u.name as seller_name, u.profile_picture, u.created_at as user_created_at, u.phone as seller_phone 
                    FROM phones p 
                    LEFT JOIN users u ON p.sellerId = u.user_id 
                    WHERE p.id = $phone_id";
    $phone_result = $conn->query($phone_query);

    if ($phone_result->num_rows == 0) {
        // Phone not found
        header("Location: /home.html");
        exit;
    }

    $phone = $phone_result->fetch_assoc();
    
    // Update the image handling code to ensure proper path formatting
    $images = [];
    if (!empty($phone['image_paths'])) {
        // Try to decode as JSON first
        $decoded = json_decode($phone['image_paths'], true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $images = $decoded;
        } else {
            // If not JSON, try comma-separated
            $image_paths = str_replace('"', '', $phone['image_paths']);
            $images = explode(',', $image_paths);
            // Trim each image path
            $images = array_map('trim', $images);
        }
    }

    // Store whether we have actual images or just using placeholder
    $has_actual_images = !empty($images);
    
    // If no images, use a placeholder
    if (empty($images)) {
        $images[] = 'none.jpg';
    }


    // Get similar phones (same price range)
    $similar_query = "SELECT p.*, 
                      (SELECT SUBSTRING_INDEX(image_paths, ',', 1) FROM phones WHERE id = p.id) as first_image 
                      FROM phones p 
                      WHERE (p.phone_name LIKE '%" . $conn->real_escape_string(explode(' ', $phone['phone_name'])[0]) . "%' 
                      OR (p.phone_price BETWEEN " . ($phone['phone_price'] * 0.8) . " AND " . ($phone['phone_price'] * 1.2) . ")) 
                      AND p.id != $phone_id 
                      LIMIT 4";
    $similar_result = $conn->query($similar_query);
    $similar_phones = [];

    if ($similar_result && $similar_result->num_rows > 0) {
        while ($similar = $similar_result->fetch_assoc()) {
            $similar_phones[] = $similar;
        }
    }

    // Function to calculate time ago
    function time_elapsed_string($datetime) {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);
        
        if ($diff->y > 0) {
            return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
        }
        if ($diff->m > 0) {
            return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
        }
        if ($diff->d > 0) {
            return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
        }
        if ($diff->h > 0) {
            return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
        }
        if ($diff->i > 0) {
            return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
        }
        return 'just now';
    }

    // Get condition text based on rating
    function getConditionText($condition) {
        if ($condition >= 9) return "Like New";
        if ($condition >= 7) return "Excellent";
        if ($condition >= 5) return "Good";
        if ($condition >= 3) return "Fair";
        return "Poor";
    }

    // Update view count
    $update_views = "UPDATE phones SET views = views + 1 WHERE id = $phone_id";
    $conn->query($update_views);
    ?>
