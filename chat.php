<?php
// chat.php
// Main chat interface: Text + Voice, AI integration (OpenAI placeholder), CNN news via API simulation
session_start();
include 'db.php';
 
$user_id = $_SESSION['user_id'] ?? null;  // Guest if not logged in
$session_id = session_id();  // Simple session for context
 
// Handle message post (for text)
if ($_POST['message'] ?? false) {
    $user_message = trim($_POST['message']);
    $message_type = 'text';
 
    // Save to DB if logged in
    if ($user_id) {
        $stmt = $pdo->prepare("INSERT INTO conversations (user_id, session_id, message_type, user_message) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $session_id, $message_type, $user_message]);
    }
 
    // AI Response: Placeholder for OpenAI GPT-3 integration
    // In production: Use curl to https://api.openai.com/v1/chat/completions with API key
    // For CNN: Query news API or scrape, but here simulate
    $ai_response = "This is a simulated response from GPT-3. You said: '$user_message'. For CNN news, ask about current events!";
 
    // Check for CNN news
    if (stripos($user_message, 'cnn') !== false || stripos($user_message, 'news') !== false) {
        $ai_response = "CNN Headline: Breaking news - [Simulated from API]. Full story: Imagine a link here.";
    }
 
    // Save response
    if ($user_id) {
        $stmt = $pdo->prepare("UPDATE conversations SET ai_response = ? WHERE user_id = ? AND session_id = ? AND user_message = ? ORDER BY id DESC LIMIT 1");
        $stmt->execute([$ai_response, $user_id, $session_id, $user_message]);
    }
 
    // Echo for JS to append
    echo json_encode(['message' => $user_message, 'response' => $ai_response]);
    exit;
}
 
// Fetch history if logged in
$history = [];
if ($user_id) {
    $stmt = $pdo->prepare("SELECT user_message, ai_response, timestamp FROM conversations WHERE user_id = ? AND session_id = ? ORDER BY timestamp DESC LIMIT 10");
    $stmt->execute([$user_id, $session_id]);
    $history = $stmt->fetchAll();
    $history = array_reverse($history);  // Chronological
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with Annie - Call Annie</title>
    <style>
        /* Internal CSS: Modern chat UI like WhatsApp/Messenger - bubbles, responsive, dark mode option */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', sans-serif; 
            background: #f0f0f0; 
            height: 100vh; display: flex; flex-direction: column; 
        }
        .header { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
        }
        .chat-container { flex: 1; overflow-y: auto; padding: 20px; background: #e5ddd5 url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="a" patternUnits="userSpaceOnUse" width="100" height="100" patternTransform="rotate(45)"><circle cx="50" cy="50" r="1" fill="%23fff"/></pattern></defs><rect width="100" height="100" fill="url(%23a)"/></svg>') repeat; }
        .message { margin: 10px 0; display: flex; }
        .user-message { justify-content: flex-end; }
        .ai-message { justify-content: flex-start; }
        .bubble { max-width: 70%; padding: 12px 16px; border-radius: 20px; position: relative; }
        .user-bubble { background: linear-gradient(45deg, #667eea, #764ba2); color: white; }
        .ai-bubble { background: white; color: #333; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .input-area { 
            display: flex; padding: 20px; background: white; border-top: 1px solid #ddd; 
            box-shadow: 0 -2px 10px rgba(0,0,0,0.05); 
        }
        input[type="text"] { flex: 1; padding: 15px; border: 1px solid #ddd; border-radius: 25px; font-size: 16px; outline: none; }
        .voice-btn, .send-btn { 
            background: linear-gradient(45deg, #4ecdc4, #44a08d); color: white; border: none; 
            width: 50px; height: 50px; border-radius: 50%; margin-left: 10px; cursor: pointer; 
            display: flex; align-items: center; justify-content: center; transition: all 0.3s; 
        }
        .voice-btn:hover, .send-btn:hover { transform: scale(1.1); }
        .voice-btn { font-size: 20px; }
        .send-btn { background: linear-gradient(45deg, #ff6b6b, #ee5a24); }
        #messages { display: flex; flex-direction: column-reverse; }  /* For auto-scroll */
        @media (max-width: 768px) { .chat-container { padding: 10px; } .bubble { max-width: 80%; } }
    </style>
</head>
<body>
    <div class="header">
        <h2>💬 Chat with Annie</h2>
        <p>Voice or Text | CNN News Ready</p>
        <?php if ($user_id): ?>
            <small>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> | <a href="profile.php" style="color: white;">Profile</a> | <a href="logout.php" style="color: white;">Logout</a></small>
        <?php else: ?>
            <small><a href="login.php" style="color: white;">Login for History</a></small>
        <?php endif; ?>
    </div>
 
    <div class="chat-container">
        <div id="messages">
            <?php foreach ($history as $msg): ?>
                <div class="message ai-message">
                    <div class="bubble ai-bubble"><?php echo htmlspecialchars($msg['user_message']); ?></div>
                </div>
                <div class="message user-message">
                    <div class="bubble user-bubble"><?php echo htmlspecialchars($msg['ai_response']); ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
 
    <div class="input-area">
        <input type="text" id="messageInput" placeholder="Type your message... (or use voice)" onkeypress="if(event.key==='Enter') sendMessage();">
        <button class="voice-btn" onclick="toggleVoice();" title="Voice Input">🎤</button>
        <button class="send-btn" onclick="sendMessage();" title="Send">➤</button>
    </div>
 
    <script>
        // Internal JS: Real-time chat, Web Speech API for voice, AJAX for AI (simulates post)
        let recognition;
        let isListening = false;
 
        // Web Speech API for voice
        if ('webkitSpeechRecognition' in window) {
            recognition = new webkitSpeechRecognition();
            recognition.continuous = false;
            recognition.interimResults = false;
            recognition.lang = 'en-US';
            recognition.onresult = (event) => {
                const transcript = event.results[0][0].transcript;
                document.getElementById('messageInput').value = transcript;
                sendMessage();
            };
            recognition.onerror = (event) => console.error('Voice error:', event.error);
        }
 
        function toggleVoice() {
            if (!recognition) {
                alert('Voice recognition not supported in this browser.');
                return;
            }
            if (isListening) {
                recognition.stop();
                isListening = false;
            } else {
                recognition.start();
                isListening = true;
            }
        }
 
        function sendMessage() {
            const input = document.getElementById('messageInput');
            const message = input.value.trim();
            if (!message) return;
 
            // Append user message
            appendMessage(message, 'user');
            input.value = '';
 
            // Simulate AJAX to PHP (use fetch for real-time)
            fetch('', {  // Same page POST
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'message=' + encodeURIComponent(message)
            })
            .then(response => response.json())
            .then(data => {
                appendMessage(data.response, 'ai');
            })
            .catch(err => console.error('Error:', err));
        }
 
        function appendMessage(text, sender) {
            const messages = document.getElementById('messages');
            const div = document.createElement('div');
            div.className = `message ${sender === 'user' ? 'user-message' : 'ai-message'}`;
            div.innerHTML = `<div class="bubble ${sender === 'user' ? 'user-bubble' : 'ai-bubble'}">${text}</div>`;
            messages.appendChild(div);
            messages.scrollTop = 0;  // Since flex-reverse, scroll to bottom
        }
 
        // Auto-scroll
        document.getElementById('messages').addEventListener('DOMNodeInserted', () => {
            document.querySelector('.chat-container').scrollTop = document.querySelector('.chat-container').scrollHeight;
        });
 
        function redirectTo(url) {
            window.location.href = url;
        }
    </script>
</body>
</html>
