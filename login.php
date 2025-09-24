<?php
// login.php - Fixed version
session_start();
require_once 'db.php';
 
$success_message = '';
$error_message = '';
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $login_input = trim($_POST['login_input'] ?? '');
        $password = $_POST['password'] ?? '';
 
        if (empty($login_input) || empty($password)) {
            $error_message = 'Please fill in all fields.';
        } else {
            // Check if login_input is username or email
            $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$login_input, $login_input]);
            $user = $stmt->fetch();
 
            if ($user && verifyPassword($password, $user['password'])) {
                // Login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $success_message = 'Login successful! Redirecting...';
 
                echo "<script>
                    setTimeout(function() {
                        window.location.href = 'chat.php';
                    }, 1500);
                </script>";
            } else {
                $error_message = 'Invalid username/email or password.';
            }
        }
    } catch (Exception $e) {
        $error_message = 'Login failed. Please try again.';
        error_log('Login error: ' . $e->getMessage());
    }
}
 
// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: chat.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Call Annie</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', sans-serif; 
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); 
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
        input:focus { border-color: #a8edea; outline: none; box-shadow: 0 0 8px rgba(168, 237, 234, 0.2); }
        .btn { 
            width: 100%; padding: 15px; background: linear-gradient(45deg, #a8edea, #fed6e3); 
            color: #333; border: none; border-radius: 8px; font-size: 18px; cursor: pointer; 
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
            color: #a8edea; text-decoration: none; display: block; margin: 10px 0; 
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
        <h2>🔐 Login</h2>
 
        <?php if ($success_message): ?>
            <div class="message success"><?php echo $success_message; ?></div>
        <?php endif; ?>
 
        <?php if ($error_message): ?>
            <div class="message error"><?php echo $error_message; ?></div>
        <?php endif; ?>
 
        <form method="POST" id="loginForm">
            <div class="form-group">
                <label for="login_input">Username or Email</label>
                <input type="text" id="login_input" name="login_input" required>
            </div>
 
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
 
            <button type="submit" class="btn">Login</button>
        </form>
 
        <div class="links">
            <a href="signup.php">Don't have an account? Sign Up</a>
            <a href="index.php">← Back to Home</a>
        </div>
    </div>
 
    <script>
        // Client-side validation
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const loginInput = document.getElementById('login_input').value.trim();
            const password = document.getElementById('password').value;
 
            if (!loginInput || !password) {
                alert('Please fill in all fields');
                e.preventDefault();
                return false;
            }
        });
 
        // Show password toggle (bonus feature)
        const passwordInput = document.getElementById('password');
        const toggleBtn = document.createElement('button');
        toggleBtn.type = 'button';
        toggleBtn.textContent = '👁';
        toggleBtn.style.cssText = `
            position: absolute; right: 15px; top: 50%; transform: translateY(-50%);
            background: none; border: none; cursor: pointer; font-size: 16px;
        `;
        passwordInput.parentElement.style.position = 'relative';
        passwordInput.parentElement.appendChild(toggleBtn);
 
        let showPassword = false;
        toggleBtn.addEventListener('click', function() {
            showPassword = !showPassword;
            passwordInput.type = showPassword ? 'text' : 'password';
            toggleBtn.textContent = showPassword ? '🙈' : '👁';
        });
    </script>
</body>
</html>
 
