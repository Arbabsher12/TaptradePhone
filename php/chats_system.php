<?php
// Database connection
function getDbConnection() {
    $host = 'localhost';
    $dbname = 'buy_sell_phone'; // Change to your actual database name
    $username = 'root'; // Change to your database username
    $password = ''; // Change to your database password

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

// Get user details by ID
function getUserDetails($userId) {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get phone listing details
function getPhoneDetails($phoneId) {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("SELECT p.*, u.name as seller_name, u.id as seller_id 
                          FROM phones p 
                          JOIN users u ON p.user_id = u.id 
                          WHERE p.id = ?");
    $stmt->execute([$phoneId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get or create conversation between two users about a specific phone
function getOrCreateConversation($userId1, $userId2, $phoneId) {
    $pdo = getDbConnection();
    
    // Check if conversation already exists
    $stmt = $pdo->prepare("SELECT * FROM conversations 
                          WHERE ((user1_id = ? AND user2_id = ?) OR (user1_id = ? AND user2_id = ?))
                          AND phone_id = ?");
    $stmt->execute([$userId1, $userId2, $userId2, $userId1, $phoneId]);
    $conversation = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($conversation) {
        return $conversation['id'];
    }
    
    // Create new conversation
    $stmt = $pdo->prepare("INSERT INTO conversations (user1_id, user2_id, phone_id, created_at) 
                          VALUES (?, ?, ?, NOW())");
    $stmt->execute([$userId1, $userId2, $phoneId]);
    return $pdo->lastInsertId();
}

// Get messages for a conversation
function getMessages($conversationId) {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("SELECT m.*, u.name as sender_name 
                          FROM messages m
                          JOIN users u ON m.sender_id = u.id
                          WHERE m.conversation_id = ?
                          ORDER BY m.created_at ASC");
    $stmt->execute([$conversationId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Send a message
function sendMessage($conversationId, $senderId, $message) {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("INSERT INTO messages (conversation_id, sender_id, message, created_at) 
                          VALUES (?, ?, ?, NOW())");
    return $stmt->execute([$conversationId, $senderId, $message]);
}

// Get all conversations for a user
function getUserConversations($userId) {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("
        SELECT 
            c.id, c.phone_id, c.created_at,
            p.title as phone_title, p.image as phone_image,
            CASE 
                WHEN c.user1_id = ? THEN c.user2_id
                ELSE c.user1_id
            END as other_user_id,
            (SELECT message FROM messages 
             WHERE conversation_id = c.id 
             ORDER BY created_at DESC LIMIT 1) as last_message,
            (SELECT created_at FROM messages 
             WHERE conversation_id = c.id 
             ORDER BY created_at DESC LIMIT 1) as last_message_time
        FROM conversations c
        JOIN phones p ON c.phone_id = p.id
        WHERE c.user1_id = ? OR c.user2_id = ?
        ORDER BY last_message_time DESC
    ");
    $stmt->execute([$userId, $userId, $userId]);
    
    $conversations = [];
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($results as $result) {
        $otherUser = getUserDetails($result['other_user_id']);
        
        $conversations[] = [
            'conversation_id' => $result['id'],
            'phone_id' => $result['phone_id'],
            'phone_title' => $result['phone_title'],
            'phone_image' => $result['phone_image'],
            'other_user_id' => $result['other_user_id'],
            'other_user_name' => $otherUser['name'],
            'last_message' => $result['last_message'],
            'last_message_time' => $result['last_message_time']
        ];
    }
    
    return $conversations;
}

// Mark messages as read
function markMessagesAsRead($conversationId, $userId) {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("UPDATE messages 
                          SET is_read = 1 
                          WHERE conversation_id = ? AND sender_id != ? AND is_read = 0");
    return $stmt->execute([$conversationId, $userId]);
}

// Count unread messages
function countUnreadMessages($userId) {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as count FROM messages m
        JOIN conversations c ON m.conversation_id = c.id
        WHERE (c.user1_id = ? OR c.user2_id = ?)
        AND m.sender_id != ?
        AND m.is_read = 0
    ");
    $stmt->execute([$userId, $userId, $userId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'];
}

// Format time ago
function getTimeAgo($datetime) {
    $now = new DateTime();
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    
    if ($diff->y > 0) {
        return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
    } elseif ($diff->m > 0) {
        return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
    } elseif ($diff->d > 0) {
        return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
    } elseif ($diff->h > 0) {
        return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
    } elseif ($diff->i > 0) {
        return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
    } else {
        return 'Just now';
    }
}
?>