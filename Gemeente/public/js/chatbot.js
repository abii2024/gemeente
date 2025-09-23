/**
 * Gemeente Chatbot Widget
 * Modern floating chat interface for gemeente website
 */
class GemeenteChatbot {
    constructor() {
        console.log('🚀 GemeenteChatbot: Constructor called');
        
        this.isOpen = false;
        this.sessionId = this.generateSessionId();
        this.messageHistory = [];
        this.isTyping = false;
        
        this.init();
    }

    init() {
        console.log('🔧 GemeenteChatbot: Initializing widget...');
        
        this.createChatWidget();
        this.bindEvents();
        this.loadWelcomeMessage();
        
        console.log('✨ GemeenteChatbot: Widget created successfully!');
    }

    generateSessionId() {
        return 'chat_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }

    createChatWidget() {
        // Create chat button
        const chatButton = document.createElement('div');
        chatButton.id = 'gemeente-chat-button';
        chatButton.className = 'gemeente-chat-button';
        chatButton.innerHTML = `
            <div class="chat-button-icon">
                <img src="/images/chatbot-logo-small.svg" width="24" height="24" alt="Gemeente Chat" style="border-radius: 50%;">
            </div>
            <div class="chat-button-text">Chat met gemeente</div>
        `;

        // Create chat window
        const chatWindow = document.createElement('div');
        chatWindow.id = 'gemeente-chat-window';
        chatWindow.className = 'gemeente-chat-window gemeente-chat-hidden';
        chatWindow.innerHTML = `
            <div class="chat-header">
                <div class="chat-header-info">
                    <div class="chat-avatar">
                        <img src="/images/chatbot-logo-small.svg" width="20" height="20" alt="Gemeente" style="border-radius: 50%;">
                    </div>
                    <div class="chat-info">
                        <div class="chat-title">Gemeente Assistent</div>
                        <div class="chat-status">Online</div>
                    </div>
                </div>
                <button class="chat-close" id="gemeente-chat-close">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18 6L6 18M6 6L18 18" stroke="white" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>
            </div>
            
            <div class="chat-messages" id="gemeente-chat-messages">
                <div class="chat-loading" id="gemeente-chat-loading">
                    <div class="loading-dots">
                        <div></div>
                        <div></div>
                        <div></div>
                    </div>
                    <span>Gemeente assistent laden...</span>
                </div>
            </div>
            
            <div class="chat-quick-replies" id="gemeente-chat-quick-replies"></div>
            
            <div class="chat-input">
                <div class="input-container">
                    <input type="text" id="gemeente-chat-input" placeholder="Typ uw vraag..." maxlength="500">
                    <button id="gemeente-chat-send" disabled>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M22 2L11 13M22 2L15 22L11 13M22 2L2 9L11 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>
                <div class="input-info">
                    <span id="gemeente-char-count">0/500</span>
                    <span class="powered-by">Powered by Gemeente AI</span>
                </div>
            </div>
        `;

        // Add styles
        const styles = document.createElement('style');
        styles.textContent = this.getChatStyles();
        document.head.appendChild(styles);

        // Add to page
        document.body.appendChild(chatButton);
        document.body.appendChild(chatWindow);

        // Store references
        this.chatButton = chatButton;
        this.chatWindow = chatWindow;
        this.messagesContainer = document.getElementById('gemeente-chat-messages');
        this.quickRepliesContainer = document.getElementById('gemeente-chat-quick-replies');
        this.chatInput = document.getElementById('gemeente-chat-input');
        this.sendButton = document.getElementById('gemeente-chat-send');
        this.charCount = document.getElementById('gemeente-char-count');
        this.loadingElement = document.getElementById('gemeente-chat-loading');
    }

    getChatStyles() {
        return `
            .gemeente-chat-button {
                position: fixed;
                bottom: 20px;
                right: 20px;
                background: linear-gradient(135deg, #2563eb, #1d4ed8);
                color: white;
                border-radius: 50px;
                box-shadow: 0 4px 20px rgba(37, 99, 235, 0.3);
                cursor: pointer;
                z-index: 9999;
                display: flex;
                align-items: center;
                padding: 12px 20px;
                transition: all 0.3s ease;
                font-family: system-ui, -apple-system, sans-serif;
                font-size: 14px;
                font-weight: 500;
            }

            .gemeente-chat-button:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 25px rgba(37, 99, 235, 0.4);
            }

            .chat-button-icon {
                margin-right: 8px;
                display: flex;
                align-items: center;
            }

            .gemeente-chat-window {
                position: fixed;
                bottom: 90px;
                right: 20px;
                width: 380px;
                height: 500px;
                background: white;
                border-radius: 16px;
                box-shadow: 0 10px 50px rgba(0, 0, 0, 0.15);
                z-index: 10000;
                display: flex;
                flex-direction: column;
                font-family: system-ui, -apple-system, sans-serif;
                transition: all 0.3s ease;
                transform-origin: bottom right;
            }

            .gemeente-chat-hidden {
                opacity: 0;
                visibility: hidden;
                transform: scale(0.8) translateY(20px);
            }

            .chat-header {
                background: linear-gradient(135deg, #2563eb, #1d4ed8);
                color: white;
                padding: 16px 20px;
                border-radius: 16px 16px 0 0;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .chat-header-info {
                display: flex;
                align-items: center;
            }

            .chat-avatar {
                width: 36px;
                height: 36px;
                background: rgba(255, 255, 255, 0.2);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-right: 12px;
            }

            .chat-info {
                display: flex;
                flex-direction: column;
            }

            .chat-title {
                font-weight: 600;
                font-size: 14px;
            }

            .chat-status {
                font-size: 12px;
                opacity: 0.9;
            }

            .chat-close {
                background: none;
                border: none;
                color: white;
                cursor: pointer;
                padding: 4px;
                border-radius: 4px;
                transition: background 0.2s;
            }

            .chat-close:hover {
                background: rgba(255, 255, 255, 0.1);
            }

            .chat-messages {
                flex: 1;
                overflow-y: auto;
                padding: 20px;
                display: flex;
                flex-direction: column;
                gap: 16px;
            }

            .chat-loading {
                display: flex;
                align-items: center;
                justify-content: center;
                flex-direction: column;
                gap: 12px;
                color: #6b7280;
                font-size: 14px;
            }

            .loading-dots {
                display: flex;
                gap: 4px;
            }

            .loading-dots div {
                width: 8px;
                height: 8px;
                background: #2563eb;
                border-radius: 50%;
                animation: loading-bounce 1.4s ease-in-out infinite both;
            }

            .loading-dots div:nth-child(1) { animation-delay: -0.32s; }
            .loading-dots div:nth-child(2) { animation-delay: -0.16s; }

            @keyframes loading-bounce {
                0%, 80%, 100% { transform: scale(0); }
                40% { transform: scale(1); }
            }

            .message {
                display: flex;
                align-items: flex-start;
                margin-bottom: 12px;
                gap: 8px;
            }

            .message.user {
                justify-content: flex-end;
            }

            .message.user .message-avatar {
                order: 2;
            }

            .message-avatar {
                flex-shrink: 0;
                width: 24px;
                height: 24px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-top: 4px;
            }

            .message-bubble {
                max-width: 80%;
                padding: 12px 16px;
                border-radius: 18px;
                font-size: 14px;
                line-height: 1.4;
                word-wrap: break-word;
            }

            .message.user .message-bubble {
                background: #2563eb;
                color: white;
                border-bottom-right-radius: 6px;
            }

            .message.bot .message-bubble {
                background: #f3f4f6;
                color: #374151;
                border-bottom-left-radius: 6px;
            }

            .action-button {
                display: inline-block;
                background: #2563eb;
                color: white;
                padding: 8px 16px;
                border-radius: 20px;
                text-decoration: none;
                font-size: 13px;
                font-weight: 500;
                margin-top: 8px;
                transition: background 0.2s;
            }

            .action-button:hover {
                background: #1d4ed8;
            }

            .chat-quick-replies {
                padding: 0 20px 16px;
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
            }

            .quick-reply {
                background: #f3f4f6;
                border: 1px solid #e5e7eb;
                color: #374151;
                padding: 8px 12px;
                border-radius: 16px;
                font-size: 13px;
                cursor: pointer;
                transition: all 0.2s;
                white-space: nowrap;
            }

            .quick-reply:hover {
                background: #e5e7eb;
                border-color: #d1d5db;
            }

            .chat-input {
                padding: 16px 20px;
                border-top: 1px solid #e5e7eb;
            }

            .input-container {
                display: flex;
                gap: 8px;
                margin-bottom: 8px;
            }

            #gemeente-chat-input {
                flex: 1;
                border: 1px solid #e5e7eb;
                border-radius: 20px;
                padding: 10px 16px;
                font-size: 14px;
                outline: none;
                transition: border-color 0.2s;
            }

            #gemeente-chat-input:focus {
                border-color: #2563eb;
            }

            #gemeente-chat-send {
                background: #2563eb;
                color: white;
                border: none;
                border-radius: 50%;
                width: 40px;
                height: 40px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.2s;
            }

            #gemeente-chat-send:disabled {
                background: #d1d5db;
                cursor: not-allowed;
            }

            #gemeente-chat-send:not(:disabled):hover {
                background: #1d4ed8;
                transform: scale(1.05);
            }

            .input-info {
                display: flex;
                justify-content: space-between;
                align-items: center;
                font-size: 12px;
                color: #6b7280;
            }

            .powered-by {
                font-size: 11px;
                opacity: 0.7;
            }

            @media (max-width: 440px) {
                .gemeente-chat-window {
                    width: calc(100vw - 40px);
                    right: 20px;
                    left: 20px;
                    height: calc(100vh - 140px);
                    bottom: 90px;
                }
                
                .gemeente-chat-button {
                    bottom: 20px;
                    right: 20px;
                }
            }

            /* Scrollbar styling */
            .chat-messages::-webkit-scrollbar {
                width: 4px;
            }

            .chat-messages::-webkit-scrollbar-track {
                background: #f1f1f1;
            }

            .chat-messages::-webkit-scrollbar-thumb {
                background: #c1c1c1;
                border-radius: 2px;
            }

            .chat-messages::-webkit-scrollbar-thumb:hover {
                background: #a1a1a1;
            }

            /* Typing indicator styles */
            .typing-indicator {
                opacity: 0;
                animation: fadeIn 0.3s ease forwards;
            }

            .typing-bubble {
                background: #f3f4f6 !important;
                padding: 12px 16px !important;
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 20px;
            }

            .typing-dots {
                display: flex;
                gap: 4px;
                align-items: center;
            }

            .typing-dots span {
                width: 8px;
                height: 8px;
                border-radius: 50%;
                background: #9ca3af;
                animation: typingDots 1.4s ease-in-out infinite;
            }

            .typing-dots span:nth-child(1) {
                animation-delay: 0s;
            }

            .typing-dots span:nth-child(2) {
                animation-delay: 0.2s;
            }

            .typing-dots span:nth-child(3) {
                animation-delay: 0.4s;
            }

            /* Typing text animation */
            .typing-text {
                overflow: hidden;
            }

            /* Animations */
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }

            @keyframes typingDots {
                0%, 60%, 100% {
                    transform: translateY(0);
                    opacity: 0.4;
                }
                30% {
                    transform: translateY(-10px);
                    opacity: 1;
                }
            }

            @keyframes messageSlideIn {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .message {
                animation: messageSlideIn 0.3s ease forwards;
            }
        `;
    }

    bindEvents() {
        // Toggle chat window
        this.chatButton.addEventListener('click', () => this.toggleChat());
        document.getElementById('gemeente-chat-close').addEventListener('click', () => this.closeChat());

        // Input handling
        this.chatInput.addEventListener('input', (e) => this.handleInput(e));
        this.chatInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.sendMessage();
            }
        });

        // Send button
        this.sendButton.addEventListener('click', () => this.sendMessage());

        // Click outside to close (optional)
        document.addEventListener('click', (e) => {
            if (this.isOpen && 
                !this.chatWindow.contains(e.target) && 
                !this.chatButton.contains(e.target)) {
                // Optional: auto-close when clicking outside
                // this.closeChat();
            }
        });
    }

    async loadWelcomeMessage() {
        try {
            // Show typing indicator for welcome message
            this.showTypingIndicator();
            
            // Add small delay to simulate loading
            await new Promise(resolve => setTimeout(resolve, 1000));
            
            const response = await fetch('/api/chat/welcome');
            const data = await response.json();
            
            this.hideTypingIndicator();
            
            if (data.success) {
                await this.addBotMessageWithTyping(data.response);
            }
        } catch (error) {
            console.error('Failed to load welcome message:', error);
            this.hideTypingIndicator();
            await this.addBotMessageWithTyping({
                type: 'error',
                message: 'Welkom bij de gemeente chatbot! 🏛️\n\nWaarmee kan ik u helpen?',
                quick_replies: ['Klacht indienen', 'Contact informatie', 'Paspoort info', 'Afval informatie']
            });
        }
    }

    toggleChat() {
        if (this.isOpen) {
            this.closeChat();
        } else {
            this.openChat();
        }
    }

    openChat() {
        this.isOpen = true;
        this.chatWindow.classList.remove('gemeente-chat-hidden');
        this.chatInput.focus();
        
        // Update button text
        this.chatButton.querySelector('.chat-button-text').textContent = 'Sluit chat';
    }

    closeChat() {
        this.isOpen = false;
        this.chatWindow.classList.add('gemeente-chat-hidden');
        
        // Update button text
        this.chatButton.querySelector('.chat-button-text').textContent = 'Chat met gemeente';
    }

    hideLoading() {
        if (this.loadingElement) {
            this.loadingElement.style.display = 'none';
        }
    }

    handleInput(e) {
        const value = e.target.value;
        const length = value.length;
        
        // Update character count
        this.charCount.textContent = `${length}/500`;
        
        // Enable/disable send button
        this.sendButton.disabled = length === 0 || length > 500;
        
        // Color coding for character count
        if (length > 450) {
            this.charCount.style.color = '#ef4444';
        } else if (length > 400) {
            this.charCount.style.color = '#f59e0b';
        } else {
            this.charCount.style.color = '#6b7280';
        }
    }

    async sendMessage() {
        const message = this.chatInput.value.trim();
        if (!message) return;

        // Add user message
        this.addUserMessage(message);
        
        // Clear input
        this.chatInput.value = '';
        this.handleInput({ target: { value: '' } });
        
        // Show typing indicator
        this.showTypingIndicator();

        try {
            const response = await fetch('/api/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: JSON.stringify({
                    message: message,
                    session_id: this.sessionId
                })
            });

            const data = await response.json();
            
            this.hideTypingIndicator();

            if (data.success) {
                // Add delay to simulate thinking time
                await new Promise(resolve => setTimeout(resolve, 800));
                await this.addBotMessageWithTyping(data.response);
            } else {
                await this.addBotMessageWithTyping(data.fallback_response || {
                    type: 'error',
                    message: 'Sorry, er ging iets mis. Probeer het opnieuw.',
                    quick_replies: ['Contact opnemen', 'Probeer opnieuw']
                });
            }
        } catch (error) {
            console.error('Chat error:', error);
            this.hideTypingIndicator();
            await this.addBotMessageWithTyping({
                type: 'error',
                message: 'Verbinding mislukt. Controleer uw internetverbinding.',
                quick_replies: ['Probeer opnieuw', 'Contact opnemen']
            });
        }
    }

    addUserMessage(message) {
        const messageElement = document.createElement('div');
        messageElement.className = 'message user';
        messageElement.innerHTML = `
            <div class="message-bubble">${this.escapeHtml(message)}</div>
            <div class="message-avatar">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="8" r="4" fill="#2563eb"/>
                    <path d="M12 14c-4 0-8 2-8 6v2h16v-2c0-4-4-6-8-6z" fill="#2563eb"/>
                </svg>
            </div>
        `;
        
        this.messagesContainer.appendChild(messageElement);
        this.scrollToBottom();
        
        // Store in history
        this.messageHistory.push({ type: 'user', message, timestamp: Date.now() });
    }

    async addBotMessageWithTyping(response) {
        const messageElement = document.createElement('div');
        messageElement.className = 'message bot';
        
        messageElement.innerHTML = `
            <div class="message-avatar">
                <img src="/images/chatbot-logo-small.svg" width="24" height="24" alt="Gemeente" style="border-radius: 50%;">
            </div>
            <div class="message-bubble">
                <div class="typing-text"></div>
            </div>
        `;
        
        this.messagesContainer.appendChild(messageElement);
        this.scrollToBottom();
        
        const typingContainer = messageElement.querySelector('.typing-text');
        let messageContent = this.formatMessage(response.message);
        
        // Type out the message character by character
        await this.typeMessage(typingContainer, messageContent);
        
        // Add action button if present
        if (response.action_button) {
            const buttonHtml = `<br><a href="${response.action_button.url}" class="action-button" target="_blank">${response.action_button.text}</a>`;
            typingContainer.innerHTML += buttonHtml;
        }
        
        // Add quick replies if present
        if (response.quick_replies && response.quick_replies.length > 0) {
            await new Promise(resolve => setTimeout(resolve, 500)); // Small delay before showing quick replies
            this.showQuickReplies(response.quick_replies);
        } else {
            this.clearQuickReplies();
        }
        
        // Store in history
        this.messageHistory.push({ type: 'bot', response, timestamp: Date.now() });
    }

    async typeMessage(container, message) {
        // Strip HTML tags for character counting, but keep them for display
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = message;
        const plainText = tempDiv.textContent || tempDiv.innerText || '';
        
        // Split message into words for better typing effect
        const words = plainText.split(' ');
        container.innerHTML = '';
        
        for (let i = 0; i < words.length; i++) {
            // Add word with appropriate formatting
            const wordElement = document.createElement('span');
            wordElement.textContent = words[i] + (i < words.length - 1 ? ' ' : '');
            
            // Find corresponding HTML formatting for this word
            const wordStart = plainText.indexOf(words.slice(0, i + 1).join(' '));
            const wordEnd = wordStart + words.slice(0, i + 1).join(' ').length;
            
            container.appendChild(wordElement);
            
            // Scroll to bottom after each word
            this.scrollToBottom();
            
            // Wait between words (faster typing)
            await new Promise(resolve => setTimeout(resolve, 50 + Math.random() * 30));
        }
        
        // Replace with properly formatted HTML
        container.innerHTML = message;
        this.scrollToBottom();
    }

    showTypingIndicator() {
        // Remove existing typing indicator
        this.hideTypingIndicator();
        
        const typingElement = document.createElement('div');
        typingElement.className = 'message bot typing-indicator';
        typingElement.id = 'typing-indicator';
        typingElement.innerHTML = `
            <div class="message-avatar">
                <img src="/images/chatbot-logo-small.svg" width="24" height="24" alt="Gemeente" style="border-radius: 50%;">
            </div>
            <div class="message-bubble typing-bubble">
                <div class="typing-dots">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        `;
        
        this.messagesContainer.appendChild(typingElement);
        this.scrollToBottom();
    }

    hideTypingIndicator() {
        const existingIndicator = document.getElementById('typing-indicator');
        if (existingIndicator) {
            existingIndicator.remove();
        }
    }

    addBotMessage(response) {
        const messageElement = document.createElement('div');
        messageElement.className = 'message bot';
        
        let messageContent = this.formatMessage(response.message);
        
        // Add action button if present
        if (response.action_button) {
            messageContent += `<br><a href="${response.action_button.url}" class="action-button" target="_blank">${response.action_button.text}</a>`;
        }
        
        messageElement.innerHTML = `
            <div class="message-avatar">
                <img src="/images/chatbot-logo-small.svg" width="24" height="24" alt="Gemeente" style="border-radius: 50%;">
            </div>
            <div class="message-bubble">${messageContent}</div>
        `;
        
        this.messagesContainer.appendChild(messageElement);
        
        // Add quick replies if present
        if (response.quick_replies && response.quick_replies.length > 0) {
            this.showQuickReplies(response.quick_replies);
        } else {
            this.clearQuickReplies();
        }
        
        this.scrollToBottom();
        
        // Store in history
        this.messageHistory.push({ type: 'bot', response, timestamp: Date.now() });
    }

    showQuickReplies(replies) {
        this.clearQuickReplies();
        
        replies.forEach(reply => {
            const button = document.createElement('button');
            button.className = 'quick-reply';
            button.textContent = reply;
            button.addEventListener('click', () => {
                this.handleQuickReply(reply);
            });
            this.quickRepliesContainer.appendChild(button);
        });
    }

    clearQuickReplies() {
        this.quickRepliesContainer.innerHTML = '';
    }

    handleQuickReply(reply) {
        // Send as regular message
        this.chatInput.value = reply;
        this.sendMessage();
    }

    showTypingIndicator() {
        // Remove existing typing indicator
        this.hideTypingIndicator();
        
        this.isTyping = true;
        const typingElement = document.createElement('div');
        typingElement.className = 'message bot typing-indicator';
        typingElement.id = 'typing-indicator';
        typingElement.innerHTML = `
            <div class="message-avatar">
                <img src="/images/chatbot-logo-small.svg" width="24" height="24" alt="Gemeente" style="border-radius: 50%;">
            </div>
            <div class="message-bubble typing-bubble">
                <div class="typing-dots">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        `;
        
        this.messagesContainer.appendChild(typingElement);
        this.scrollToBottom();
    }

    hideTypingIndicator() {
        const existingIndicator = document.getElementById('typing-indicator');
        if (existingIndicator) {
            existingIndicator.remove();
        }
        this.isTyping = false;
    }

    formatMessage(message) {
        if (!message) return '';
        
        return message
            .replace(/\n\n/g, '<br><br>')
            .replace(/\n/g, '<br>')
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.*?)\*/g, '<em>$1</em>')
            .replace(/📋|📞|🌐|📧|🕒|📍|💬|🏛️|🔍|ℹ️|🗓️|🚗|🅿️|🆓|♻️|🗑️|🌱|💧|🏠|💰|🏦|📮|👋|😊|📝|💡|🎯|🔵|🟡|🟢|❓|•/g, '<span style="font-size: 16px;">$&</span>');
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    scrollToBottom() {
        setTimeout(() => {
            this.messagesContainer.scrollTop = this.messagesContainer.scrollHeight;
        }, 100);
    }
}

// Initialize chatbot when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('🤖 Gemeente Chatbot: DOM loaded, initializing...');
    
    try {
        // Initialize chatbot on all pages
        window.gemeenteChatbot = new GemeenteChatbot();
        console.log('✅ Gemeente Chatbot: Successfully initialized!');
    } catch (error) {
        console.error('❌ Gemeente Chatbot: Failed to initialize', error);
    }
});

// Export for manual initialization
window.GemeenteChatbot = GemeenteChatbot;