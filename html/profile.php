<?php
// Start session to maintain user state
session_start();

 if (!isset($_SESSION['user_id'])) {
     header('Location: login.php');
     exit;
 }

// Database connection
include __DIR__ . '/../php/db.php';
$user_id = $_SESSION['user_id'];

// Fetch user data
$sql = "SELECT user_id, name, email, phone, profile_picture, created_at FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
     
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "User not found";
    exit;
}

$stmt->close();
$conn->close();

// Format the created_at date
$created_at = new DateTime($user['created_at']);
$formatted_date = $created_at->format('F j, Y');

// Set default profile picture if none exists
$profile_picture = !empty($user['profile_picture']) ? $user['profile_picture'] : '../Components/Nodp.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="../css/profile.css">
</head>
<body>
    <div class="container">
        <div class="profile-card">
            <div class="profile-header">
                <h1>Profile</h1>
                <p>View and manage your account details</p>
            </div>
            <div class="profile-content">
                <div class="profile-sidebar">
                    <div class="profile-avatar">
                      <img src="../<?php echo htmlspecialchars($profile_picture);?>" alt="<?php echo htmlspecialchars($user['name']); ?>" onerror="this.src='../Components/Nodp.png'; this.onerror=null;">
                    </div>
                    <button id="edit-profile-btn" class="btn btn-outline">Edit Profile</button>
                </div>
                <div class="profile-details">
                    <div class="profile-field">
                        <h3>Name</h3>
                        <p><?php echo htmlspecialchars($user['name']); ?></p>
                    </div>
                    <div class="profile-field">
                        <h3>Email</h3>
                        <p><?php echo htmlspecialchars($user['email']); ?></p>
                    </div>
                    <div class="profile-field">
                        <h3>Phone</h3>
                        <p><?php echo htmlspecialchars($user['phone'] ?? 'Not provided'); ?></p>
                    </div>
                    <div class="profile-field">
                        <h3>Member since</h3>
                        <p><?php echo $formatted_date; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="profile-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Profile</h2>
                <span class="close">&times;</span>
                <p>Make changes to your profile here. Click save when you're done.</p>
            </div>
            <form id="profile-form" action="/updateProfile" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="profile_picture">Profile Picture</label>
                    <div class="profile-picture-upload">
                        <img src="../<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Preview" id="profile-preview" onerror="this.src='../Components/Nodp.png'; this.onerror=null;">
                        <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../js/profile.js"></script>
</body>
</html>
