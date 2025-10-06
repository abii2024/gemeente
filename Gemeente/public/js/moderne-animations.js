/**
 * Gemeente Portal - Modern Animations & Interactions
 * Scroll animations, chatbot, and micro-interactions
 */

// ============================================
// SCROLL ANIMATIONS WITH INTERSECTION OBSERVER
// ============================================

class ScrollAnimations {
    constructor() {
        this.observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        this.init();
    }

    init() {
        // Create observer for fade-in animations
        const fadeObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, this.observerOptions);

        // Observe all animated elements
        const animatedElements = document.querySelectorAll(
            '.fade-in, .slide-in-left, .slide-in-right, .scale-in, .stagger-children'
        );

        animatedElements.forEach(el => fadeObserver.observe(el));
    }
}

// ============================================
// MODERN CHATBOT
// ============================================

class ModernChatbot {
    constructor() {
        this.isOpen = false;
        this.messages = [];
        this.init();
    }

    init() {
        this.createChatbotHTML();
        this.attachEventListeners();
        this.addWelcomeMessage();
    }

    createChatbotHTML() {
        const chatbotHTML = `
            <button class="chatbot-toggle" id="chatbotToggle" aria-label="Open chatbot">
                💬
            </button>

            <div class="chatbot-window" id="chatbotWindow">
                <div class="chatbot-header">
                    <div class="chatbot-title">
                        <span>🤖</span>
                        <span>Gemeente Assistent</span>
                    </div>
                    <button class="chatbot-close" id="chatbotClose" aria-label="Close chatbot">
                        ✕
                    </button>
                </div>

                <div class="chatbot-messages" id="chatbotMessages">
                    <!-- Messages will be added here -->
                </div>

                <div class="chatbot-input">
                    <input
                        type="text"
                        id="chatbotInput"
                        placeholder="Typ je vraag..."
                        aria-label="Chat message input"
                    />
                    <button class="chatbot-send" id="chatbotSend" aria-label="Send message">
                        Verzend
                    </button>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', chatbotHTML);
    }

    attachEventListeners() {
        const toggle = document.getElementById('chatbotToggle');
        const close = document.getElementById('chatbotClose');
        const send = document.getElementById('chatbotSend');
        const input = document.getElementById('chatbotInput');

        toggle.addEventListener('click', () => this.toggleChatbot());
        close.addEventListener('click', () => this.closeChatbot());
        send.addEventListener('click', () => this.sendMessage());
        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') this.sendMessage();
        });
    }

    toggleChatbot() {
        this.isOpen = !this.isOpen;
        const window = document.getElementById('chatbotWindow');
        window.classList.toggle('active');

        if (this.isOpen) {
            document.getElementById('chatbotInput').focus();
        }
    }

    closeChatbot() {
        this.isOpen = false;
        document.getElementById('chatbotWindow').classList.remove('active');
    }

    addWelcomeMessage() {
        this.addMessage('bot', 'Hallo! 👋 Ik ben je gemeente assistent. Hoe kan ik je vandaag helpen?');
    }

    sendMessage() {
        const input = document.getElementById('chatbotInput');
        const message = input.value.trim();

        if (!message) return;

        // Add user message
        this.addMessage('user', message);
        input.value = '';

        // Show typing indicator
        this.showTypingIndicator();

        // Simulate bot response
        setTimeout(() => {
            this.hideTypingIndicator();
            this.handleBotResponse(message);
        }, 1500);
    }

    addMessage(type, text) {
        const messagesContainer = document.getElementById('chatbotMessages');
        const messageHTML = `
            <div class="chat-message ${type}">
                <div class="chat-avatar">${type === 'bot' ? '🤖' : '👤'}</div>
                <div class="chat-bubble">${this.escapeHtml(text)}</div>
            </div>
        `;

        messagesContainer.insertAdjacentHTML('beforeend', messageHTML);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    showTypingIndicator() {
        const messagesContainer = document.getElementById('chatbotMessages');
        const indicatorHTML = `
            <div class="chat-message bot typing-message">
                <div class="chat-avatar">🤖</div>
                <div class="chat-bubble typing-indicator">
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                </div>
            </div>
        `;
        messagesContainer.insertAdjacentHTML('beforeend', indicatorHTML);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    hideTypingIndicator() {
        const indicator = document.querySelector('.typing-message');
        if (indicator) indicator.remove();
    }

    handleBotResponse(userMessage) {
        const lowerMessage = userMessage.toLowerCase();
        let response = '';

        // Simple keyword-based responses
        if (lowerMessage.includes('hallo') || lowerMessage.includes('hoi')) {
            response = 'Hallo! Fijn dat je er bent. Waarmee kan ik je helpen? Je kunt me vragen stellen over klachten melden, openingstijden, of contactgegevens.';
        } else if (lowerMessage.includes('klacht') || lowerMessage.includes('melding')) {
            response = 'Je kunt een klacht melden door op de "Meld een klacht" knop te klikken in het hoofdmenu. Vergeet niet je contactgegevens en een duidelijke beschrijving van het probleem toe te voegen.';
        } else if (lowerMessage.includes('openingstijd') || lowerMessage.includes('open')) {
            response = 'Ons gemeentehuis is geopend van maandag tot vrijdag, 9:00 - 17:00 uur. Voor spoedeisende zaken kun je ons ook buiten kantooruren bereiken via de noodlijn.';
        } else if (lowerMessage.includes('contact') || lowerMessage.includes('telefoon') || lowerMessage.includes('email')) {
            response = 'Je kunt ons bereiken via email op info@gemeente.nl of bel ons op 020-1234567. Ook kun je langskomen op ons kantoor tijdens openingstijden.';
        } else if (lowerMessage.includes('status') || lowerMessage.includes('opvolging')) {
            response = 'Om de status van je klacht te bekijken, log je in op je account en ga naar "Mijn Klachten". Daar zie je alle updates en statuswijzigingen.';
        } else if (lowerMessage.includes('dank') || lowerMessage.includes('bedankt')) {
            response = 'Graag gedaan! Is er nog iets anders waarmee ik je kan helpen? 😊';
        } else if (lowerMessage.includes('doei') || lowerMessage.includes('dag')) {
            response = 'Tot ziens! Fijne dag verder! 👋';
        } else {
            response = 'Bedankt voor je vraag! Voor specifieke informatie kun je ons contactformulier gebruiken of bellen met 020-1234567. Onze medewerkers helpen je graag verder!';
        }

        this.addMessage('bot', response);
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// ============================================
// FORM ENHANCEMENTS
// ============================================

class FormEnhancements {
    constructor() {
        this.init();
    }

    init() {
        this.setupFloatingLabels();
        this.setupFileUpload();
        this.setupFormValidation();
    }

    setupFloatingLabels() {
        // Auto-focus handling for floating labels
        const inputs = document.querySelectorAll('.form-input, .form-textarea, .form-select');

        inputs.forEach(input => {
            // Check if input has value on page load
            if (input.value) {
                input.classList.add('has-value');
            }

            // Update on input change
            input.addEventListener('input', () => {
                if (input.value) {
                    input.classList.add('has-value');
                } else {
                    input.classList.remove('has-value');
                }
            });
        });
    }

    setupFileUpload() {
        const fileUploads = document.querySelectorAll('.file-upload');

        fileUploads.forEach(upload => {
            const input = upload.querySelector('input[type="file"]');

            // Drag & Drop handlers
            upload.addEventListener('dragover', (e) => {
                e.preventDefault();
                upload.classList.add('drag-over');
            });

            upload.addEventListener('dragleave', () => {
                upload.classList.remove('drag-over');
            });

            upload.addEventListener('drop', (e) => {
                e.preventDefault();
                upload.classList.remove('drag-over');

                const files = e.dataTransfer.files;
                if (files.length > 0 && input) {
                    input.files = files;
                    this.updateFileUploadText(upload, files);
                }
            });

            // File input change handler
            if (input) {
                input.addEventListener('change', () => {
                    this.updateFileUploadText(upload, input.files);
                });
            }
        });
    }

    updateFileUploadText(upload, files) {
        const textElement = upload.querySelector('.file-upload-text');
        if (textElement && files.length > 0) {
            const fileNames = Array.from(files).map(f => f.name).join(', ');
            textElement.textContent = `${files.length} bestand(en) geselecteerd`;
        }
    }

    setupFormValidation() {
        const forms = document.querySelectorAll('form[data-validate]');

        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                if (!this.validateForm(form)) {
                    e.preventDefault();
                }
            });

            // Real-time validation
            const inputs = form.querySelectorAll('input, textarea, select');
            inputs.forEach(input => {
                input.addEventListener('blur', () => {
                    this.validateField(input);
                });
            });
        });
    }

    validateForm(form) {
        let isValid = true;
        const inputs = form.querySelectorAll('[required]');

        inputs.forEach(input => {
            if (!this.validateField(input)) {
                isValid = false;
            }
        });

        return isValid;
    }

    validateField(field) {
        const value = field.value.trim();
        let isValid = true;
        let errorMessage = '';

        // Required check
        if (field.hasAttribute('required') && !value) {
            isValid = false;
            errorMessage = 'Dit veld is verplicht';
        }

        // Email validation
        if (field.type === 'email' && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                isValid = false;
                errorMessage = 'Voer een geldig e-mailadres in';
            }
        }

        // Phone validation (Dutch format)
        if (field.type === 'tel' && value) {
            const phoneRegex = /^(\+31|0)[1-9][0-9]{8}$/;
            if (!phoneRegex.test(value.replace(/\s/g, ''))) {
                isValid = false;
                errorMessage = 'Voer een geldig telefoonnummer in';
            }
        }

        // Update UI
        this.updateFieldValidation(field, isValid, errorMessage);
        return isValid;
    }

    updateFieldValidation(field, isValid, errorMessage) {
        const errorElement = field.parentElement.querySelector('.error-message');

        if (isValid) {
            field.classList.remove('error');
            field.classList.add('success');
            if (errorElement) errorElement.remove();
        } else {
            field.classList.add('error');
            field.classList.remove('success');

            if (!errorElement && errorMessage) {
                const errorHTML = `<div class="error-message">⚠️ ${errorMessage}</div>`;
                field.parentElement.insertAdjacentHTML('beforeend', errorHTML);
            }
        }
    }
}

// ============================================
// HAMBURGER MENU
// ============================================

class MobileMenu {
    constructor() {
        this.init();
    }

    init() {
        const hamburger = document.querySelector('.hamburger');
        const nav = document.querySelector('.header-nav');

        if (!hamburger) {
            // Create hamburger if it doesn't exist
            this.createHamburger();
        }

        this.attachEventListeners();
    }

    createHamburger() {
        const header = document.querySelector('.header .nav');
        if (!header) return;

        const hamburgerHTML = `
            <button class="hamburger" aria-label="Toggle menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
        `;

        header.insertAdjacentHTML('beforeend', hamburgerHTML);
    }

    attachEventListeners() {
        const hamburger = document.querySelector('.hamburger');
        const nav = document.querySelector('.header-nav');

        if (!hamburger || !nav) return;

        hamburger.addEventListener('click', () => {
            hamburger.classList.toggle('active');
            nav.classList.toggle('active');
            document.body.style.overflow = nav.classList.contains('active') ? 'hidden' : '';
        });

        // Close menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!hamburger.contains(e.target) && !nav.contains(e.target) && nav.classList.contains('active')) {
                hamburger.classList.remove('active');
                nav.classList.remove('active');
                document.body.style.overflow = '';
            }
        });

        // Close menu when clicking a nav link
        const navLinks = nav.querySelectorAll('.nav-item');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                hamburger.classList.remove('active');
                nav.classList.remove('active');
                document.body.style.overflow = '';
            });
        });
    }
}

// ============================================
// SMOOTH PAGE TRANSITIONS
// ============================================

class PageTransitions {
    constructor() {
        this.init();
    }

    init() {
        // Add fade-in animation to page
        document.body.style.opacity = '0';
        window.addEventListener('load', () => {
            document.body.style.transition = 'opacity 0.3s ease-in';
            document.body.style.opacity = '1';
        });

        // Smooth transitions for internal links
        const links = document.querySelectorAll('a[href^="/"]:not([target="_blank"])');
        links.forEach(link => {
            link.addEventListener('click', (e) => {
                // Skip if it's a download or external link
                if (link.hasAttribute('download') || link.href.includes('#')) return;

                e.preventDefault();
                const href = link.href;

                document.body.style.opacity = '0';
                setTimeout(() => {
                    window.location.href = href;
                }, 300);
            });
        });
    }
}

// ============================================
// INITIALIZE ALL MODULES
// ============================================

document.addEventListener('DOMContentLoaded', () => {
    // Initialize scroll animations
    new ScrollAnimations();

    // Initialize chatbot
    new ModernChatbot();

    // Initialize form enhancements
    new FormEnhancements();

    // Initialize mobile menu
    new MobileMenu();

    // Initialize page transitions
    new PageTransitions();

    console.log('✨ Gemeente Portal - Modern features loaded!');
});

// ============================================
// UTILITY FUNCTIONS
// ============================================

// Debounce function for performance
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Throttle function for scroll events
function throttle(func, limit) {
    let inThrottle;
    return function(...args) {
        if (!inThrottle) {
            func.apply(this, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// Export for use in other scripts if needed
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        ScrollAnimations,
        ModernChatbot,
        FormEnhancements,
        MobileMenu,
        PageTransitions
    };
}
