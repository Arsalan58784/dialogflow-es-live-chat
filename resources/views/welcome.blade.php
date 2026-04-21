<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>InteractCX | AI Chatbot</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --primary-color: #6366f1;
            --primary-light: #818cf8;
            --bg-gradient: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            --glass-bg: rgba(255, 255, 255, 0.8);
            --user-bubble: #6366f1;
            --bot-bubble: #ffffff;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg-gradient);
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .chat-wrapper {
            width: 100%;
            max-width: 900px;
            height: 85vh;
            background: var(--glass-bg);
            backdrop-filter: blur(15px);
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.4);
            display: flex;
            flex-direction: column;
            position: relative;
            margin: 20px;
        }

        .chat-header {
            padding: 20px 30px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .chat-header .status {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .status-dot {
            width: 10px;
            height: 10px;
            background: #10b981;
            border-radius: 50%;
            box-shadow: 0 0 10px rgba(16, 185, 129, 0.5);
        }

        #chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 30px;
            display: flex;
            flex-direction: column;
            gap: 20px;
            scroll-behavior: smooth;
        }

        /* Custom Scrollbar */
        #chat-messages::-webkit-scrollbar {
            width: 6px;
        }
        #chat-messages::-webkit-scrollbar-track {
            background: transparent;
        }
        #chat-messages::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .message {
            max-width: 75%;
            display: flex;
            flex-direction: column;
        }

        .message.user {
            align-self: flex-end;
        }

        .message.bot {
            align-self: flex-start;
        }

        .bubble {
            padding: 12px 20px;
            border-radius: 18px;
            font-size: 15px;
            line-height: 1.5;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            position: relative;
        }

        .user .bubble {
            background: var(--user-bubble);
            color: white;
            border-bottom-right-radius: 4px;
        }

        .bot .bubble {
            background: var(--bot-bubble);
            color: #1e293b;
            border-bottom-left-radius: 4px;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .time {
            font-size: 11px;
            color: #94a3b8;
            margin-top: 5px;
        }

        .user .time {
            text-align: right;
        }

        .chat-input-area {
            padding: 25px 30px;
            background: rgba(255, 255, 255, 0.5);
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            border-bottom-left-radius: 24px;
            border-bottom-right-radius: 24px;
        }

        .input-group {
            background: white;
            border-radius: 16px;
            padding: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .chat-input-area input {
            border: none;
            padding: 10px 15px;
            font-size: 15px;
            box-shadow: none !important;
            flex: 1;
        }

        .send-btn {
            background: var(--primary-color);
            color: white;
            border: none;
            width: 45px;
            height: 45px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .send-btn:hover {
            background: var(--primary-light);
            transform: scale(1.05);
        }

        .typing-indicator {
            display: none;
            align-self: flex-start;
            padding: 10px 15px;
            background: white;
            border-radius: 15px;
            border-bottom-left-radius: 4px;
            margin-bottom: 20px;
        }

        .dot {
            height: 8px;
            width: 8px;
            background: #cbd5e1;
            border-radius: 50%;
            display: inline-block;
            animation: bounce 1.3s linear infinite;
        }

        .dot:nth-child(2) { animation-delay: -1.1s; }
        .dot:nth-child(3) { animation-delay: -0.9s; }

        @keyframes bounce {
            0%, 60%, 100% { transform: translateY(0); }
            30% { transform: translateY(-4px); }
        }

        .clear-chat {
            cursor: pointer;
            color: #94a3b8;
            transition: color 0.2s;
        }

        .clear-chat:hover {
            color: #ef4444;
        }
    </style>
</head>
<body>
    <div class="chat-wrapper">
        <div class="chat-header">
            <div>
                <h5 class="m-0 fw-bold">InteractCX AI</h5>
                <div class="status">
                    <div class="status-dot"></div>
                    <small class="text-muted">Online</small>
                </div>
            </div>
            <div class="clear-chat" title="Clear History" id="clear-btn">
                <i class="bi bi-trash3 fs-5"></i>
            </div>
        </div>

        <div id="chat-messages">
            <!-- Messages go here -->
        </div>

        <div class="typing-indicator" id="typing">
            <span class="dot"></span>
            <span class="dot"></span>
            <span class="dot"></span>
        </div>

        <div class="chat-input-area">
            <form id="chat-form">
                <div class="input-group">
                    <input type="text" id="message-input" placeholder="Type your message..." autocomplete="off">
                    <button type="submit" class="send-btn">
                        <i class="bi bi-send-fill"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        /**
         * Chatbot Application Logic
         * Handles: Local Storage, UI Updates, API Communication (Axios), and WebSocket Events (Echo).
         */
        document.addEventListener('DOMContentLoaded', () => {
            const chatMessages = document.getElementById('chat-messages');
            const chatForm = document.getElementById('chat-form');
            const messageInput = document.getElementById('message-input');
            const typingIndicator = document.getElementById('typing');
            const clearBtn = document.getElementById('clear-btn');

            // Load chat history from LocalStorage or initialize as empty array
            let history = JSON.parse(localStorage.getItem('chat_history')) || [];

            /**
             * Initialize Laravel Echo Listeners
             * Monitors 'chat-channel' for real-time updates.
             */
            if (window.Echo) {
                console.log('Echo initialized, subscribing to chat-channel...');
                
                window.Echo.channel('chat-channel')
                    .on('connection.error', (e) => console.error('Echo connection error:', e))
                    .listen('MessageProcessed', (e) => {
                        console.log('Broadcast message received:', e);
                        // Append the broadcasted message (response property from MessageProcessed event)
                        appendMessage('bot', e.response);
                    });
            } else {
                console.warn('Laravel Echo is not available. Real-time updates will be disabled.');
            }

            // Render existing history on page load
            history.forEach(msg => {
                appendMessage(msg.type, msg.text, false);
            });

            /**
             * Appends a message bubble to the chat container.
             * 
             * @param {string} type - 'user' or 'bot'
             * @param {string} text - The message content
             * @param {boolean} save - Whether to persist this message to LocalStorage
             */
            function appendMessage(type, text, save = true) {
                const time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                const messageDiv = document.createElement('div');
                messageDiv.className = `message ${type}`;
                messageDiv.innerHTML = `
                    <div class="bubble">${text}</div>
                    <div class="time">${time}</div>
                `;
                chatMessages.appendChild(messageDiv);
                
                // Auto-scroll to the latest message
                chatMessages.scrollTop = chatMessages.scrollHeight;

                if (save) {
                    history.push({ type, text, time });
                    localStorage.setItem('chat_history', JSON.stringify(history));
                }
            }

            /**
             * Handle Chat Form Submission
             */
            chatForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const message = messageInput.value.trim();
                
                // Basic validation: prevent empty submissions
                if (!message) return;

                // 1. Update UI and Clear Input
                appendMessage('user', message);
                messageInput.value = '';
                
                // 2. Show Typing Indicator
                typingIndicator.style.display = 'block';
                chatMessages.scrollTop = chatMessages.scrollHeight;

                try {
                    // 3. Send AJAX Request to Server
                    const response = await axios.post('/chat/send', {
                        message: message,
                        session_id: 'user-session' // Shared session key for the user
                    });

                    typingIndicator.style.display = 'none';

                    // 4. Handle Success Response
                    if (response.data && response.data.reply) {
                        appendMessage('bot', response.data.reply);
                    }
                } catch (error) {
                    // 5. Improved Error Handling
                    typingIndicator.style.display = 'none';
                    console.error('API Request Failed:', error);

                    const errorMsg = error.response?.data?.message || 'Sorry, I am having trouble connecting. Please check your internet or try again later.';
                    appendMessage('bot', errorMsg);
                }
            });

            /**
             * Clear Chat History
             */
            clearBtn.addEventListener('click', () => {
                if (confirm('Are you sure you want to clear your chat history?')) {
                    localStorage.removeItem('chat_history');
                    history = [];
                    chatMessages.innerHTML = '';
                }
            });
        });
    </script>
</body>
</html>