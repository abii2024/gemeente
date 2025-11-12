/**
 * Gemeente Chatbot Button
 * Simple decorative chat button (no functionality)
 */
class GemeenteChatbot {
    constructor() {
        this.init();
    }

    init() {
        this.createChatButton();
        this.bindEvents();
    }

    createChatButton() {
        // Check if button already exists
        if (document.getElementById('gemeente-chat-button')) {
            return;
        }

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

        // Add styles
        const styles = document.createElement('style');
        styles.textContent = `
            .gemeente-chat-button {
                position: fixed;
                bottom: 24px;
                right: 24px;
                background: linear-gradient(135deg, #2563eb, #1d4ed8);
                color: white;
                border-radius: 50px;
                box-shadow: 0 4px 16px rgba(37, 99, 235, 0.3);
                cursor: pointer;
                z-index: 9998;
                display: flex;
                align-items: center;
                padding: 14px 24px;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                font-family: system-ui, -apple-system, sans-serif;
                font-size: 15px;
                font-weight: 600;
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
        `;

        document.head.appendChild(styles);
        document.body.appendChild(chatButton);

        this.chatButton = chatButton;
    }

    bindEvents() {
        // Button is decorative - no functionality
        if (this.chatButton) {
            this.chatButton.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
            });
        }
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    if (!window.gemeenteChatbot) {
        window.gemeenteChatbot = new GemeenteChatbot();
    }
});
