<?php
session_start();
include __DIR__ . '/../php/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /login?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

$user_id = $_SESSION['user_id'];

// Get all conversations for this user
$conversations_query = "SELECT c.id, c.phone_id, c.created_at, c.last_message_time,
                       CASE WHEN c.user1_id = $user_id THEN c.user2_id ELSE c.user1_id END as other_user_id,
                       p.phone_name, p.phone_price, p.image_paths,
                       (SELECT message FROM messages WHERE conversation_id = c.id ORDER BY created_at DESC LIMIT 1) as last_message,
                       (SELECT COUNT(*) FROM messages WHERE conversation_id = c.id AND sender_id != $user_id AND is_read = 0) as unread_count
                       FROM conversations c
                       JOIN phones p ON c.phone_id = p.id
                       WHERE c.user1_id = $user_id OR c.user2_id = $user_id
                       ORDER BY c.last_message_time DESC";
$conversations_result = $conn->query($conversations_query);

$conversations = [];
if ($conversations_result->num_rows > 0) {
    while ($conversation = $conversations_result->fetch_assoc()) {
        // Get other user details
        $user_query = "SELECT * FROM users WHERE user_id = " . $conversation['other_user_id'];
        $user_result = $conn->query($user_query);
        $other_user = $user_result->fetch_assoc();
        
        // Parse image paths
        $images = [];
        if (!empty($conversation['image_paths'])) {
            $decoded = json_decode($conversation['image_paths'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $images = $decoded;
            } else {
                $images = explode(',', $conversation['image_paths']);
            }
        }
        
        // If no images, use a placeholder
        if (empty($images)) {
            $images[] = '../uploads/none.jpg';
        }
        
        $conversation['other_user'] = $other_user;
        $conversation['phone_image'] = $images[0];
        $conversations[] = $conversation;
    }
}

// Function to format time ago
function timeAgo($datetime) {
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Conversations - Phone Marketplace</title>
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

        .page-header {
            background: var(--background-white);
            border-radius: var(--border-radius-lg);
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: var(--shadow-light);
            border: 1px solid var(--border-color);
        }

        .page-title {
            font-size: 28px;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .page-title i {
            color: var(--accent-color);
            font-size: 24px;
        }

        .conversations-container {
            background: var(--background-white);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-light);
            border: 1px solid var(--border-color);
            overflow: hidden;
        }

        .conversations-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border-color);
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .conversations-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
        }

        .conversation-item {
            display: flex;
            align-items: center;
            padding: 20px 24px;
            border-bottom: 1px solid var(--border-color);
            text-decoration: none;
            color: inherit;
            transition: all 0.2s ease;
            position: relative;
        }

        .conversation-item:last-child {
            border-bottom: none;
        }

        .conversation-item:hover {
            background-color: #f8f9fa;
            transform: translateX(2px);
        }

        .conversation-item.unread {
            background-color: #f0f7ff;
            border-left: 4px solid var(--accent-color);
        }

        .conversation-item.unread:hover {
            background-color: #e6f3ff;
        }

        .conversation-avatar {
            position: relative;
            margin-right: 16px;
        }

        .user-avatar {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            object-fit: cover;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: white;
            font-size: 20px;
            box-shadow: var(--shadow-light);
            border: 3px solid var(--background-white);
        }

        .online-indicator {
            position: absolute;
            bottom: 2px;
            right: 2px;
            width: 16px;
            height: 16px;
            background-color: #28a745;
            border: 2px solid var(--background-white);
            border-radius: 50%;
        }

        .conversation-content {
            flex: 1;
            min-width: 0;
        }

        .conversation-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 8px;
        }

        .user-name {
            font-weight: 600;
            font-size: 16px;
            color: var(--text-primary);
            margin: 0;
        }

        .conversation-time {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 500;
            white-space: nowrap;
        }

        .phone-info {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            gap: 8px;
        }

        .phone-image {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            object-fit: cover;
            border: 1px solid var(--border-color);
        }

        .phone-title {
            font-size: 13px;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .phone-price {
            font-size: 13px;
            color: var(--secondary-color);
            font-weight: 600;
        }

        .last-message {
            color: var(--text-secondary);
            font-size: 14px;
            line-height: 1.4;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 400px;
        }

        .unread-badge {
            background: linear-gradient(135deg, var(--accent-color) 0%, #0d6efd 100%);
            color: white;
            border-radius: 12px;
            min-width: 24px;
            height: 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 600;
            margin-left: 12px;
            box-shadow: var(--shadow-light);
        }

        .empty-state {
            text-align: center;
            padding: 60px 40px;
            color: var(--text-secondary);
        }

        .empty-state i {
            font-size: 64px;
            color: var(--border-color);
            margin-bottom: 24px;
            opacity: 0.6;
        }

        .empty-state h3 {
            font-size: 24px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 12px;
        }

        .empty-state p {
            font-size: 16px;
            margin-bottom: 32px;
            color: var(--text-secondary);
        }

        .empty-state .btn {
            background: linear-gradient(135deg, var(--accent-color) 0%, #0d6efd 100%);
            border: none;
            padding: 12px 24px;
            font-weight: 600;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-light);
        }

        .empty-state .btn:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-medium);
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

        @media (max-width: 768px) {
            .main-container {
                padding: 12px;
            }

            .page-header {
                padding: 16px;
            }

            .page-title {
                font-size: 24px;
            }

            .conversation-item {
                padding: 16px;
            }

            .user-avatar {
                width: 48px;
                height: 48px;
                font-size: 18px;
            }

            .last-message {
                max-width: 200px;
            }

            .conversations-header {
                padding: 16px;
            }
        }

        @media (max-width: 480px) {
            .conversation-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 4px;
            }

            .conversation-time {
                align-self: flex-end;
            }

            .phone-info {
                flex-wrap: wrap;
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
                        <a class="nav-link active" href="/conversations">Messages</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a href="/sellYourphone" class="btn btn-outline-light">+ Sell Your Phone</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="main-container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item active">My Conversations</li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-comments"></i>
                My Conversations
            </h1>
        </div>

        <!-- Conversations Container -->
        <div class="conversations-container">
            <div class="conversations-header">
                <h2 class="conversations-title">Recent Conversations</h2>
            </div>
            
            <?php if (empty($conversations)): ?>
                <div class="empty-state">
                    <i class="fas fa-comments"></i>
                    <h3>No conversations yet</h3>
                    <p>Start a conversation by messaging a seller about a phone you're interested in.</p>
                    <a href="/" class="btn btn-primary">Browse Phones</a>
                </div>
            <?php else: ?>
                <?php foreach ($conversations as $conversation): ?>
                    <a href="/chats?conversation_id=<?php echo $conversation['id']; ?>" 
                       class="conversation-item <?php echo $conversation['unread_count'] > 0 ? 'unread' : ''; ?>">
                        
                        <div class="conversation-avatar">
                            <?php if (!empty($conversation['other_user']['profile_picture'])): ?>
                                <img src="<?php echo htmlspecialchars($conversation['other_user']['profile_picture']); ?>" 
                                     class="user-avatar" 
                                     alt="<?php echo htmlspecialchars($conversation['other_user']['name']); ?>">
                            <?php else: ?>
                                <div class="user-avatar">
                                    <?php echo substr($conversation['other_user']['name'], 0, 1); ?>
                                </div>
                            <?php endif; ?>
                            <div class="online-indicator"></div>
                        </div>
                        
                        <div class="conversation-content">
                            <div class="conversation-header">
                                <h3 class="user-name">
                                    <?php echo htmlspecialchars($conversation['other_user']['name']); ?>
                                    <?php if ($conversation['unread_count'] > 0): ?>
                                        <span class="unread-badge"><?php echo $conversation['unread_count']; ?></span>
                                    <?php endif; ?>
                                </h3>
                                <div class="conversation-time">
                                    <?php echo timeAgo($conversation['last_message_time'] ?? $conversation['created_at']); ?>
                                </div>
                            </div>
                            
                            <div class="phone-info">
                                <img src="../uploads/<?php echo htmlspecialchars($conversation['phone_image']); ?>" 
                                     class="phone-image" 
                                     alt="<?php echo htmlspecialchars($conversation['phone_name']); ?>">
                                <span class="phone-title"><?php echo htmlspecialchars($conversation['phone_name']); ?></span>
                                <span class="phone-price">$<?php echo number_format($conversation['phone_price'], 2); ?></span>
                            </div>
                            
                            <div class="last-message">
                                <?php 
                                if (!empty($conversation['last_message'])) {
                                    echo htmlspecialchars($conversation['last_message']);
                                } else {
                                    echo '<i>No messages yet</i>';
                                }
                                ?>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
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
                        <li><a href="/">Home</a></li>
                        <li><a href="/">Browse Phones</a></li>
                        <li><a href="/sellYourPhone">Sell Your Phone</a></li>
                        <li><a href="/">Contact Us</a></li>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>