<?php
// profile.php
// User profile management
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
include 'db.php';
 
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
 
if ($_POST) {
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $stmt = $pdo->prepare("UPDATE users SET full_name = ?, phone = ? WHERE id = ?");
    $stmt->execute([$full_name, $phone, $user_id]);
    $user['full_name'] = $full_name;
    $user['phone'] = $phone;
    echo "<script>alert('Profile updated!');</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Call Annie</title>
    <style>
        /* Internal CSS: Clean profile page with card layout */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            min-height: 100vh; padding: 20px; 
        }
        .container { max-width: 600px; margin: 0 auto; background: rgba(255,255,255,0.95); border-radius: 20px; padding: 40px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); }
        h2 { text-align: center; margin-bottom: 30px; color: #333; }
        .profile-info { display: grid; gap: 15px; margin-bottom: 30px; }
        label { font-weight: bold; color: #555; }
        input { width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px; transition: border-color 0.3s; }
        input:focus { border-color: #764ba2; outline: none; }
        .btn { padding: 12px 24px; background: linear-gradient(45deg, #667eea, #764ba2); color: white; border: none; border-radius: 8px; cursor: pointer; margin: 10px 5px; transition: all 0.3s; }
        .btn:hover { transform: translateY(-2px); }
        a.btn { display: inline-block; text-align: center; text-decoration: none; }
        @media (max-width: 480px) { .container { margin: 10px; padding: 20px; } }
    </style>
</head>
<body>
    <div class="container">
        <h2>Profile</h2>
        <div class="profile-info">
            <div><label>Username:</label> <span><?php echo htmlspecialchars($user['username']); ?></span></div>
            <div><label>Email:</label> <span><?php echo htmlspecialchars($user['email']); ?></span></div>
            <form method="POST">
                <div>
                    <label>Full Name:</label>
                    <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>">
                </div>
                <div>
                    <label>Phone:</label>
                    <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" placeholder="For voice calls">
                </div>
                <button type="submit" class="btn">Update Profile</button>
            </form>
        </div>
        <a href="chat.php" class="btn">Go to Chat</a>
        <a href="logout.php" class="btn" style="background: linear-gradient(45deg, #ff6b6b, #ee5a24);">Logout</a>
        <a href="index.php" class="btn" style="background: linear-gradient(45deg, #4ecdc4, #44a08d);">Home</a>
    </div>
 
    <script>
        // Internal JS: Redirect helper
        function redirectTo(url) {
            window.location.href = url;
        }
    </script>
</body>
</html>
