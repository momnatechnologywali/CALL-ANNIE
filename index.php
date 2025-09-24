<?php
// index.php - Fixed version
session_start();
require_once 'db.php'; // Safe to include, won't break if DB fails
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Call Annie - AI Chatbot with CNN News Integration</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            min-height: 100vh; 
            color: #333; 
        }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        header { 
            text-align: center; padding: 50px 20px; 
            background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); 
            border-radius: 20px; margin-bottom: 50px; 
            box-shadow: 0 8px 32px rgba(31,38,135,0.37); 
        }
        h1 { 
            color: white; font-size: 3em; margin-bottom: 10px; 
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3); 
            background: linear-gradient(45deg, #fff, #f0f0f0); 
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }
        .subtitle { color: rgba(255,255,255,0.9); font-size: 1.2em; }
        .features { 
            display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); 
            gap: 30px; margin: 50px 0; 
        }
        .feature-card { 
            background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); 
            border-radius: 15px; padding: 30px; text-align: center; color: white; 
            transition: all 0.3s ease; box-shadow: 0 4px 20px rgba(0,0,0,0.1); 
            border: 1px solid rgba(255,255,255,0.2);
        }
        .feature-card:hover { 
            transform: translateY(-10px); box-shadow: 0 8px 40px rgba(0,0,0,0.2); 
            border-color: rgba(255,255,255,0.4);
        }
        .feature-icon { font-size: 3em; margin-bottom: 15px; }
        .btn { 
            display: inline-block; padding: 15px 30px; 
            background: linear-gradient(45deg, #ff6b6b, #ee5a24); color: white; 
            text-decoration: none; border-radius: 50px; margin: 10px; 
            transition: all 0.3s ease; font-weight: bold; 
            box-shadow: 0 4px 15px rgba(255,107,107,0.4); 
            border: none; cursor: pointer;
        }
        .btn:hover { transform: scale(1.05); box-shadow: 0 6px 20px rgba(255,107,107,0.6); }
        .btn-secondary { 
            background: linear-gradient(45deg, #4ecdc4, #44a08d); 
            box-shadow: 0 4px 15px rgba(78,205,196,0.4); 
        }
        .btn-secondary:hover { box-shadow: 0 6px 20px rgba(78,205,196,0.6); }
        .btn-guest { 
            background: linear-gradient(45deg, #feca57, #ff9ff3); 
            box-shadow: 0 4px 15px rgba(254,202,87,0.4); 
        }
        .btn-guest:hover { box-shadow: 0 6px 20px rgba(254,202,87,0.6); }
        footer { 
            text-align: center; padding: 20px; color: rgba(255,255,255,0.8); 
            margin-top: 50px;
        }
        .status { 
            background: rgba(255,255,255,0.2); padding: 10px; 
            border-radius: 10px; margin: 20px 0; text-align: center; 
        }
        @media (max-width: 768px) { 
            h1 { font-size: 2em; } 
            .features { grid-template-columns: 1fr; } 
            .btn { display: block; width: 100%; text-align: center; margin: 10px 0; }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>🤖 Call Annie</h1>
            <p class="subtitle">Your AI Companion for Real-Time Conversations & CNN News Updates</p>
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="status" style="color: #4ecdc4;">
                    👋 Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>!
                </div>
            <?php endif; ?>
        </header>
 
        <section class="features">
            <div class="feature-card">
                <div class="feature-icon">💬</div>
                <h3>Text & Voice Chat</h3>
                <p>Engage naturally via typing or speaking. Powered by advanced NLP for contextual responses.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📰</div>
                <h3>CNN Integration</h3>
                <p>Get real-time news briefs, summaries, and answers to current events from CNN sources.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📱</div>
                <h3>Multi-Platform</h3>
                <p>Access on web, mobile, or even phone calls for seamless experience anywhere.</p>
            </div>
        </section>
 
        <div style="text-align: center; margin: 50px 0;">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="chat.php" class="btn">💬 Start Chatting</a>
                <a href="profile.php" class="btn btn-secondary">👤 Profile</a>
                <a href="logout.php" class="btn" style="background: linear-gradient(45deg, #6c5ce7, #a29bfe);">🚪 Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn">🔐 Login</a>
                <a href="signup.php" class="btn btn-secondary">📝 Sign Up</a>
                <a href="chat.php" class="btn btn-guest">🎉 Try Demo (Guest)</a>
            <?php endif; ?>
        </div>
    </div>
 
    <footer>
        <p>&copy; 2025 Call Annie. Built with ❤️ for awesome chats.</p>
    </footer>
 
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Animate cards on load
            const cards = document.querySelectorAll('.feature-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 200 + 300);
            });
 
            // Parallax effect on header
            window.addEventListener('scroll', () => {
                const scrolled = window.pageYOffset;
                const header = document.querySelector('header');
                const speed = scrolled * 0.5;
                header.style.transform = `translateY(${speed}px)`;
            });
        });
 
        function redirectTo(url) {
            window.location.href = url;
        }
    </script>
</body>
</html>
 
