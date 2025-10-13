<?php
session_start();
include __DIR__ . '/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    exit;
}

$user_id = $_SESSION['user_id'];

// Check if conversation ID is provided
if (!isset($_GET['conversation_id'])) {
    exit;
}

$conversation_id = intval($_GET['conversation_id']);

// Verify user is part of this conversation
$check_query = "SELECT * FROM conversations 
               WHERE id = $conversation_id 
               AND (user1_id = $user_id OR user2_id = $user_id)";
$check_result = $conn->query($check_query);

if ($check_result->num_rows == 0) {
    exit;
}

// Mark messages as read
$update_query = "UPDATE messages 
                SET is_read = 1 
                WHERE conversation_id = $conversation_id 
                AND sender_id != $user_id 
                AND is_read = 0";
$conn->query($update_query);

// Get messages
$messages_query = "SELECT m.*, u.name as sender_name, u.profile_picture 
                  FROM messages m
                  JOIN users u ON m.sender_id = u.user_id
                  WHERE m.conversation_id = $conversation_id
                  ORDER BY m.created_at ASC";
$messages_result = $conn->query($messages_query);

// Function to format time
function formatTime($datetime) {
    $now = new DateTime();
    $time = new DateTime($datetime);
    $diff = $now->diff($time);
    
    if ($diff->d == 0) {
        return date('h:i A', strtotime($datetime));
    } elseif ($diff->d == 1) {
        return 'Yesterday';
    } else {
        return date('M j', strtotime($datetime));
    }
}

if ($messages_result->num_rows == 0) {
    echo '<div class="no-messages"><p>No messages yet. Start the conversation!</p></div>';
} else {
    $messages = [];
    while ($message = $messages_result->fetch_assoc()) {
        $messages[] = $message;
    }
    
    $current_date = null;
    foreach ($messages as $message) {
        // Add date divider if date changes
        $message_date = date('Y-m-d', strtotime($message['created_at']));
        if ($message_date != $current_date) {
            $current_date = $message_date;
            $date_display = date('F j, Y', strtotime($message['created_at']));
            if ($message_date == date('Y-m-d')) {
                $date_display = 'Today';
            } elseif ($message_date == date('Y-m-d', strtotime('-1 day'))) {
                $date_display = 'Yesterday';
            }
            echo '<div class="date-divider"><span>' . $date_display . '</span></div>';
        }
        
        $message_class = $message['sender_id'] == $user_id ? 'message-sent' : 'message-received';
        
        echo "<div class='message {$message_class}'>";
        echo "<div class='message-content'>";
        echo nl2br(htmlspecialchars($message['message']));
        echo "<div class='message-time'>" . formatTime($message['created_at']) . "</div>";
        echo "</div>";
        echo "</div>";
    }
}
?>