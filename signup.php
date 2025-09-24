<?php
// signup.php - Fixed version
session_start();
require_once 'db.php';
 
$success_message = '';
$error_message = '';
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $full_name = trim($_POST['full_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
 
        $errors = [];
 
        // Validation
        if (empty($username) || strlen($username) < 3) {
            $errors[] = 'Username must be at least 3 characters.';
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Valid email is required.';
        }
        if (empty($password) || strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters.';
        }
        if (empty($full_name)) {
            $errors[] = 'Full name is required.';
        }
 
        if (empty($errors)) {
            // Check if user exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
 
            if ($stmt->rowCount() > 0) {
                $errors[] = 'Username or email already exists.';
            } else {
                // Hash password
                $hashed_password = hashPassword($password);
 
                // Insert user
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password, full_name, phone) VALUES (?, ?, ?, ?, ?)");
                $result = $stmt->execute([$username, $email, $hashed_password, $full_name, $phone]);
 
                if ($result) {
                    $success_message = 'Signup successful! Redirecting to login...';
                    $_SESSION['signup_success'] = true;
                    echo "<script>
                        setTimeout(function() {
                            window.location.href = 'login.php';
                        }, 2000);
                    </script>";
                } else {
                    $errors[] = 'Signup failed. Please try again.';
                }
            }
        }
 
        if (!empty($errors)) {
            $error_message = implode('<br>', $errors);
        }
 
    } catch (Exception $e) {
        $error_message = 'An error occurred during signup. Please try again.';
        error_log('Signup error: ' . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Call Annie</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', sans-serif; 
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); 
            min-height: 100vh; display: flex; align-items: center; justify-content: center; 
            padding: 20px;
        }
        .form-container { 
            background: rgba(255,255,255,0.95); 
            padding: 40px; border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); 
            width: 100%; max-width: 400px; 
        }
        h2 { text-align: center; margin-bottom: 30px; color: #333; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: 500; color: #555; }
        input { 
            width: 100%; padding: 12px 15px; border: 2px solid #ddd; border-radius: 8px; 
            font-size: 16px; transition: all 0.3s ease; 
        }
        input:focus { border-color: #f5576c; outline: none; box-shadow: 0 0 8px rgba(245, 87, 108, 0.2); }
        .btn { 
            width: 100%; padding: 15px; background: linear-gradient(45deg, #f093fb, #f5576c); 
            color: white; border: none; border-radius: 8px; font-size: 18px; cursor: pointer; 
            transition: transform 0.3s ease; margin-top: 10px;
        }
        .btn:hover { transform: translateY(-2px); }
        .message { 
            text-align: center; margin: 20px 0; padding: 12px; border-radius: 8px; 
            font-weight: 500;
        }
        .error { background: #fee; color: #c33; border: 1px solid #fcc; }
        .success { background: #efe; color: #060; border: 1px solid #cfc; }
        .links { text-align: center; margin-top: 20px; }
        .links a { 
            color: #f5576c; text-decoration: none; display: block; margin: 10px 0; 
            font-weight: 500;
        }
        .links a:hover { text-decoration: underline; }
        @media (max-width: 480px) { 
            .form-container { margin: 10px; padding: 30px 20px; } 
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>🎯 Sign Up</h2>
 
        <?php if ($success_message): ?>
            <div class="message success"><?php echo $success_message; ?></div>
        <?php endif; ?>
 
        <?php if ($error_message): ?>
            <div class="message error"><?php echo $error_message; ?></div>
        <?php endif; ?>
 
        <form method="POST" id="signupForm">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required minlength="3">
            </div>
 
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
            </div>
 
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required minlength="6">
            </div>
 
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>" required>
            </div>
 
            <div class="form-group">
                <label for="phone">Phone (Optional)</label>
                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" placeholder="+1234567890">
            </div>
 
            <button type="submit" class="btn">Create Account</button>
        </form>
 
        <div class="links">
            <a href="login.php">Already have an account? Login</a>
            <a href="index.php">← Back to Home</a>
        </div>
    </div>
 
    <script>
        // Client-side validation
        document.getElementById('signupForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const username = document.getElementById('username').value;
 
            if (username.length < 3) {
                alert('Username must be at least 3 characters');
                e.preventDefault();
                return false;
            }
 
            if (password.length < 6) {
                alert('Password must be at least 6 characters');
                e.preventDefault();
                return false;
            }
        });
 
        // Smooth input focus
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });
    </script>
</body>
</html>
