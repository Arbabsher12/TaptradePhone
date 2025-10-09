<?php
session_start();
include __DIR__ . '/../db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page with return URL
    header("Location: /login?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

$user_id = $_SESSION['user_id'];

// Get conversation ID or phone/seller details
if (isset($_GET['conversation_id'])) {
    // Existing conversation
    $conversation_id = intval($_GET['conversation_id']);
    
    // Get conversation details
    $conv_query = "SELECT c.*, p.phone_name, p.phone_price, p.image_paths,
                  CASE WHEN c.user1_id = $user_id THEN c.user2_id ELSE c.user1_id END as other_user_id
                  FROM conversations c
                  JOIN phones p ON c.phone_id = p.id
                  WHERE c.id = $conversation_id 
                  AND (c.user1_id = $user_id OR c.user2_id = $user_id)";
    $conv_result = $conn->query($conv_query);
    
    if ($conv_result->num_rows == 0) {
        // Invalid conversation or not authorized
        header("Location: /conversations");
        exit;
    }
    
    $conversation = $conv_result->fetch_assoc();
    $phone_id = $conversation['phone_id'];
    $other_user_id = $conversation['other_user_id'];
    
} elseif (isset($_GET['phone_id']) && isset($_GET['seller_id'])) {
    // New conversation
    $phone_id = intval($_GET['phone_id']);
    $seller_id = intval($_GET['seller_id']);

    
    
    // Don't allow messaging yourself
    if ($seller_id == $user_id) {
        header("Location: /phoneDetail?id=$phone_id");
        exit;
    }
    
    // Check if conversation already exists
    $check_query = "SELECT id FROM conversations 
                   WHERE ((user1_id = $user_id AND user2_id = $seller_id) 
                   OR (user1_id = $seller_id AND user2_id = $user_id))
                   AND phone_id = $phone_id";
    $check_result = $conn->query($check_query);
    
    if ($check_result->num_rows > 0) {
        // Conversation exists, redirect to it
        $conversation_id = $check_result->fetch_assoc()['id'];
        header("Location: /chats?conversation_id=$conversation_id");
        exit;
    }
    
    // Create new conversation
    $insert_query = "INSERT INTO conversations (user1_id, user2_id, phone_id, created_at) 
                    VALUES ($user_id, $seller_id, $phone_id, NOW())";
    $conn->query($insert_query);
    $conversation_id = $conn->insert_id;
    $other_user_id = $seller_id;
    
} else {
    // No conversation specified
    header("Location: /conversations");
    exit;
}

// Get phone details
$phone_query = "SELECT p.*, u.name AS seller_name
               FROM phones p
               JOIN users u ON p.sellerId = u.user_id
               WHERE p.id = $phone_id";
$phone_result = $conn->query($phone_query);
$phone = $phone_result->fetch_assoc();

// Get other user details
$user_query = "SELECT * FROM users WHERE user_id = $other_user_id";
$user_result = $conn->query($user_query);
$other_user = $user_result->fetch_assoc();

// Parse image paths
$images = [];
if (!empty($phone['image_paths'])) {
    $decoded = json_decode($phone['image_paths'], true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
        $images = $decoded;
    } else {
        $images = explode(',', $phone['image_paths']);
    }
}

// If no images, use a placeholder
if (empty($images)) {
    $images[] = '../uploads/none.jpg';
}

// Get messages
$messages_query = "SELECT m.*, u.name as sender_name, u.profile_picture 
                  FROM messages m
                  JOIN users u ON m.sender_id = u.user_id
                  WHERE m.conversation_id = $conversation_id
                  ORDER BY m.created_at ASC";
$messages_result = $conn->query($messages_query);
$messages = [];

if ($messages_result->num_rows > 0) {
    while ($message = $messages_result->fetch_assoc()) {
        $messages[] = $message;
    }
}

// Mark messages as read
$update_query = "UPDATE messages 
                SET is_read = 1 
                WHERE conversation_id = $conversation_id 
                AND sender_id != $user_id 
                AND is_read = 0";
$conn->query($update_query);

// Handle message submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = trim($_POST['message']);
    
    if (!empty($message)) {
        $message = $conn->real_escape_string($message);
        $insert_message = "INSERT INTO messages (conversation_id, sender_id, message, created_at, is_read) 
                          VALUES ($conversation_id, $user_id, '$message', NOW(), 0)";
        $conn->query($insert_message);
        
        // Redirect to prevent form resubmission
        header("Location: /chats?conversation_id=$conversation_id");
        exit;
    }
}

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat - Phone Marketplace</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/phoneDetail.css">
    <link rel="stylesheet" href="../css/footer.css">
    <style>
        :root {
            --primary-color: #232f3e;
            --secondary-color: #ff9900;
            --accent-color: #146eb4;
            --text-primary: #0f1111;
            --text-secondary: #565959;
            --text-muted: #767676;
            --border-color: #d5d9d9;
            --background-light: #f7f8fa;
            --background-white: #ffffff;
            --shadow-light: 0 1px 3px rgba(0,0,0,0.1);
            --shadow-medium: 0 2px 8px rgba(0,0,0,0.15);
            --border-radius: 8px;
            --border-radius-lg: 12px;
            --message-sent: #146eb4;
            --message-received: #f0f0f0;
        }

        body {
            background-color: var(--background-light);
            font-family: 'Amazon Ember', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: var(--text-primary);
        }

        .navbar {
            background-color: var(--primary-color);
            box-shadow: var(--shadow-light);
        }

        .navbar-brand {
            font-weight: 600;
            font-size: 1.25rem;
        }

        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .chat-container {
            background: var(--background-white);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-light);
            border: 1px solid var(--border-color);
            overflow: hidden;
            height: calc(100vh - 200px);
            min-height: 600px;
            display: flex;
            flex-direction: column;
        }

        .chat-header {
            display: flex;
            align-items: center;
            padding: 20px 24px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-bottom: 1px solid var(--border-color);
        }

        .back-btn {
            background: none;
            border: none;
            color: var(--accent-color);
            font-size: 18px;
            margin-right: 16px;
            cursor: pointer;
            padding: 8px;
            border-radius: 50%;
            transition: all 0.2s ease;
        }

        .back-btn:hover {
            background-color: rgba(20, 110, 180, 0.1);
            transform: scale(1.1);
        }

        .user-info {
            display: flex;
            align-items: center;
            flex: 1;
        }

        .user-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            margin-right: 16px;
            object-fit: cover;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: white;
            font-size: 18px;
            box-shadow: var(--shadow-light);
            border: 3px solid var(--background-white);
        }

        .user-details h5 {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0 0 4px 0;
        }

        .user-details small {
            color: var(--text-muted);
            font-size: 13px;
        }

        .phone-preview {
            display: flex;
            align-items: center;
            padding: 16px 24px;
            background: var(--background-white);
            border-bottom: 1px solid var(--border-color);
            transition: all 0.2s ease;
        }

        .phone-preview:hover {
            background-color: #f8f9fa;
        }

        .phone-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: var(--border-radius);
            margin-right: 16px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-light);
        }

        .phone-details h6 {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0 0 4px 0;
        }

        .phone-price {
            font-size: 18px;
            font-weight: 700;
            color: var(--secondary-color);
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 20px 24px;
            background: linear-gradient(180deg, #fafbfc 0%, #f7f8fa 100%);
            scroll-behavior: smooth;
        }

        .message {
            display: flex;
            margin-bottom: 16px;
            animation: fadeInUp 0.3s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .message-sent {
            justify-content: flex-end;
        }

        .message-received {
            justify-content: flex-start;
        }

        .message-content {
            max-width: 70%;
            padding: 12px 16px;
            border-radius: 18px;
            position: relative;
            word-wrap: break-word;
            box-shadow: var(--shadow-light);
        }

        .message-sent .message-content {
            background: linear-gradient(135deg, var(--message-sent) 0%, #0d6efd 100%);
            color: white;
            border-bottom-right-radius: 6px;
        }

        .message-received .message-content {
            background: var(--message-received);
            color: var(--text-primary);
            border-bottom-left-radius: 6px;
            border: 1px solid var(--border-color);
        }

        .message-time {
            font-size: 11px;
            margin-top: 6px;
            text-align: right;
            opacity: 0.8;
        }

        .message-sent .message-time {
            color: rgba(255, 255, 255, 0.8);
        }

        .message-received .message-time {
            color: var(--text-muted);
        }

        .date-divider {
            text-align: center;
            margin: 24px 0;
            position: relative;
        }

        .date-divider span {
            background: var(--background-white);
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-light);
        }

        .date-divider:before {
            content: "";
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: var(--border-color);
            z-index: -1;
        }

        .chat-input {
            display: flex;
            align-items: center;
            padding: 20px 24px;
            background: var(--background-white);
            border-top: 1px solid var(--border-color);
        }

        .chat-input form {
            display: flex;
            width: 100%;
            align-items: center;
            gap: 12px;
        }

        .chat-input input {
            flex: 1;
            padding: 14px 20px;
            border: 2px solid var(--border-color);
            border-radius: 24px;
            font-size: 15px;
            outline: none;
            transition: all 0.2s ease;
            background: var(--background-light);
        }

        .chat-input input:focus {
            border-color: var(--accent-color);
            background: var(--background-white);
            box-shadow: 0 0 0 3px rgba(20, 110, 180, 0.1);
        }

        .send-btn {
            background: linear-gradient(135deg, var(--accent-color) 0%, #0d6efd 100%);
            color: white;
            border: none;
            border-radius: 50%;
            width: 48px;
            height: 48px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            transition: all 0.2s ease;
            box-shadow: var(--shadow-light);
        }

        .send-btn:hover {
            transform: scale(1.05);
            box-shadow: var(--shadow-medium);
        }

        .send-btn:active {
            transform: scale(0.95);
        }

        .no-messages {
            text-align: center;
            padding: 60px 40px;
            color: var(--text-secondary);
        }

        .no-messages i {
            font-size: 48px;
            color: var(--border-color);
            margin-bottom: 16px;
            opacity: 0.6;
        }

        .no-messages p {
            font-size: 16px;
            margin: 0;
        }

        .breadcrumb {
            background: transparent;
            padding: 0;
            margin-bottom: 20px;
        }

        .breadcrumb-item a {
            color: var(--accent-color);
            text-decoration: none;
            font-weight: 500;
        }

        .breadcrumb-item.active {
            color: var(--text-secondary);
        }

        /* Scrollbar Styling */
        .chat-messages::-webkit-scrollbar {
            width: 6px;
        }

        .chat-messages::-webkit-scrollbar-track {
            background: transparent;
        }

        .chat-messages::-webkit-scrollbar-thumb {
            background: var(--border-color);
            border-radius: 3px;
        }

        .chat-messages::-webkit-scrollbar-thumb:hover {
            background: var(--text-muted);
        }

        @media (max-width: 768px) {
            .main-container {
                padding: 12px;
            }

            .chat-container {
                height: calc(100vh - 150px);
            }

            .chat-header {
                padding: 16px;
            }

            .chat-messages {
                padding: 16px;
            }

            .chat-input {
                padding: 16px;
            }

            .message-content {
                max-width: 85%;
            }

            .phone-preview {
                padding: 12px 16px;
            }

            .phone-image {
                width: 48px;
                height: 48px;
            }
        }

        @media (max-width: 480px) {
            .user-avatar {
                width: 40px;
                height: 40px;
                font-size: 16px;
            }

            .user-details h5 {
                font-size: 16px;
            }

            .phone-details h6 {
                font-size: 14px;
            }

            .phone-price {
                font-size: 16px;
            }

            .chat-input input {
                padding: 12px 16px;
                font-size: 14px;
            }

            .send-btn {
                width: 44px;
                height: 44px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="/">Phone Marketplace</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/">Browse</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="sellYourPhone">Sell Phone</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="conversations">Messages</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a href="/sellYourPhone" class="btn btn-outline-light">+ Sell Your Phone</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="main-container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/home">Home</a></li>
                <li class="breadcrumb-item"><a href="/conversations">Messages</a></li>
                <li class="breadcrumb-item active">Chat with <?php echo htmlspecialchars($other_user['name']); ?></li>
            </ol>
        </nav>

        <div class="chat-container">
            <!-- Chat Header -->
            <div class="chat-header">
                <a href="/conversations" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div class="user-info">
                    <?php if (!empty($other_user['profile_picture'])): ?>
                        <img src="<?php echo htmlspecialchars($other_user['profile_picture']); ?>" class="user-avatar" alt="<?php echo htmlspecialchars($other_user['name']); ?>">
                    <?php else: ?>
                        <div class="user-avatar">
                            <?php echo substr($other_user['name'], 0, 1); ?>
                        </div>
                    <?php endif; ?>
                    <div class="user-details">
                        <h5><?php echo htmlspecialchars($other_user['name']); ?></h5>
                        <small>
                            <?php echo !empty($other_user['created_at']) ? 'Member since ' . date('F Y', strtotime($other_user['created_at'])) : ''; ?>
                        </small>
                    </div>
                </div>
            </div>
            
            <!-- Phone Preview -->
            <div class="phone-preview">
                <a href="/phoneDetail?id=<?php echo $phone_id; ?>" class="text-decoration-none d-flex align-items-center">
                    <img src="../uploads/<?php echo htmlspecialchars($images[0]); ?>" class="phone-image" alt="<?php echo htmlspecialchars($phone['phone_name']); ?>">
                    <div class="phone-details">
                        <h6><?php echo isset($phone['phone_name']) ? htmlspecialchars($phone['phone_name']) : 'Unknown Phone'; ?></h6>
                        <div class="phone-price">$<?php echo isset($phone['phone_price']) ? number_format($phone['phone_price'], 2) : '0.00'; ?></div>
                    </div>
                </a>
            </div>
                    
            <!-- Chat Messages -->
            <div class="chat-messages" id="chat-messages">
                <?php if (empty($messages)): ?>
                    <div class="no-messages">
                        <i class="fas fa-comment-dots"></i>
                        <p>No messages yet. Start the conversation!</p>
                    </div>
                <?php else: ?>
                    <?php 
                    $current_date = null;
                    foreach ($messages as $message): 
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
                    ?>
                        <div class="message <?php echo $message['sender_id'] == $user_id ? 'message-sent' : 'message-received'; ?>">
                            <div class="message-content">
                                <?php echo nl2br(htmlspecialchars($message['message'])); ?>
                                <div class="message-time">
                                    <?php echo formatTime($message['created_at']); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <!-- Chat Input -->
            <div class="chat-input">
                <form method="post" action="">
                    <input type="text" name="message" placeholder="Type a message..." required autofocus>
                    <button type="submit" class="send-btn">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="footer mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>About Us</h5>
                    <p>Your trusted marketplace for buying and selling phones. We connect buyers and sellers in a secure environment.</p>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul>
                        <li><a href="/home">Home</a></li>
                        <li><a href="/home">Browse Phones</a></li>
                        <li><a href="/sellYourPhone">Sell Your Phone</a></li>
                        <li><a href="/home">Contact Us</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Connect With Us</h5>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
            <hr class="mt-4 mb-4" style="border-color: rgba(255,255,255,0.1);">
            <div class="text-center">
                <p class="mb-0">&copy; <?php echo date('Y'); ?> Phone Marketplace. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Scroll to bottom of chat messages on page load
        document.addEventListener('DOMContentLoaded', function() {
            const chatMessages = document.getElementById('chat-messages');
            chatMessages.scrollTop = chatMessages.scrollHeight;
        });
        
        // Auto-refresh chat every 5 seconds to get new messages
        setInterval(function() {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', '../php/get_messages.php?conversation_id=<?php echo $conversation_id; ?>', true);
            xhr.onload = function() {
                if (this.status === 200) {
                    document.getElementById('chat-messages').innerHTML = this.responseText;
                    const chatMessages = document.getElementById('chat-messages');
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }
            };
            xhr.send();
        }, 5000);
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>