<?php

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$profile_picture = "/Components/noDp.png"; // Default image

if ($isLoggedIn) {
    $user_id = $_SESSION['user_id'];

    // Database connection
    include __DIR__ . '/../php/db.php';
    
    // Fetch user details 
    $query = "SELECT profile_picture , name FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($profile_picture, $name);
    $stmt->fetch();
    $stmt->close();

    // Set default profile picture if none is found
    if (empty($profile_picture)) {
        $profile_picture = "/Components/noDp.png";
    }
    
    // Fetch unread messages count
    $unreadCount = 0;
    $unreadCountQuery = "SELECT COUNT(*) FROM messages WHERE conversation_id IN (SELECT id FROM conversations WHERE user1_id = ? OR user2_id = ?) AND is_read = 0 AND sender_id != ?";
    $stmt = $conn->prepare($unreadCountQuery);
    $stmt->bind_param("iii", $user_id, $user_id, $user_id);
    $stmt->execute();
    $stmt->bind_result($unreadCount);
    $stmt->fetch();
    $stmt->close();
} else {
    $unreadCount = 0;
}

?>

<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="nav-container container">
               
        <a class="navbar-brand" href="/">
            <i class="fas fa-mobile-alt me-2"></i>Phone Marketplace
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="/">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="/conversations">Messages <sup><?php if($unreadCount!==0) {echo  $unreadCount; }?><sup></a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        Buy
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="allPhones?conditions=new">New Phones</a></li>
                        <li><a class="dropdown-item" href="allPhones?conditions=like_new,excellent,good,fair">Used Phones</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="allPhones">All Phones</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        Sell
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/sellYourPhone">Sell New Phone</a></li>
                        <li><a class="dropdown-item" href="/sellYourPhone">Sell Your Phone</a></li>
                    </ul>
                </li>
            </ul>
            
            <div class="d-flex align-items-center">
                <?php if ($isLoggedIn): ?>
                    <div class="dropdown me-3">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="<?php echo htmlspecialchars($profile_picture);?>" onerror=" this.src='/Components/noDp.png'; this.onerror=null;" alt="Profile" class="profile-pic me-2">
                            <span class="d-none d-sm-inline text-light"><?php echo htmlspecialchars($name);?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="/profile"><i class="fas fa-user me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item" href="/mylisting"><i class="fas fa-list me-2"></i>My Listings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/logout"><i class="fas fa-sign-out-alt me-2"></i>Log Out</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="/login" class="btn btn-primary me-2">Log In</a>
                    <a href="/signup" class="btn btn-success">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav> 
