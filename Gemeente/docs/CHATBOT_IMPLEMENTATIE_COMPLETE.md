# Chatbot Implementatie - Hoofdzaken

**Gemeente Portal AI Chatbot**  
**Datum:** 10 November 2025  
**Versie:** 1.0 - Samenvatting  
**Auteur:** Abdisamad Abdulle

---

## 📋 Wat je moet weten

Dit document legt uit **HOE** de chatbot werkt in 4 simpele stappen:

1. **Frontend** - Wat de gebruiker ziet (HTML/JavaScript)
2. **Backend** - Laravel die OpenAI API aanroept
3. **API** - OpenAI GPT-4 die vragen beantwoordt
4. **Flow** - Hoe het allemaal samenwerkt

---

## 1. Overzicht (Simpel)

**Wat doet de chatbot?**

De chatbot helpt burgers met:
- ❓ Vragen beantwoorden over gemeentediensten
- 📝 Uitleg over klachten indienen
- 🛂 Info over paspoort/rijbewijs
- 🏛️ Openingstijden en contactgegevens

**Hoe werkt het?**

```
Gebruiker typt vraag 
    ↓
Alpine.js stuurt naar Laravel
    ↓
Laravel stuurt naar OpenAI API
    ↓
AI geeft antwoord terug
    ↓
Gebruiker ziet antwoord in chat
```

---

## 2. Welke Technologie Wordt Gebruikt?

### 2.1 Frontend Technologieën

```
Alpine.js v3.13.0
├── Reactive state management
├── Event handling
└── DOM manipulation

Tailwind CSS v3.4
├── Utility-first styling
├── Responsive design
└── Dark mode support

JavaScript ES6+
├── Async/await for API calls
├── Template literals
└── Arrow functions
```

### 2.2 Backend Technologieën

```
Laravel 11.x
├── Route handling
├── Controller logic
└── API responses

OpenAI API
├── GPT-4 model
├── Chat completion endpoint
└── Streaming responses

PHP 8.2+
├── Type hints
├── Named arguments
└── Match expressions
```

---

## 3. Implementatie Stappen

### Stap 1: API Controller Setup

**Bestand:** `app/Http/Controllers/ChatbotController.php`

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    /**
     * Chat with the AI assistant
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function chat(Request $request)
    {
        // Valideer input
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'conversation_history' => 'nullable|array'
        ]);

        try {
            // Bouw conversatie context
            $messages = $this->buildConversationContext(
                $validated['message'],
                $validated['conversation_history'] ?? []
            );

            // Roep OpenAI API aan
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                'Content-Type' => 'application/json',
            ])->timeout(30)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4',
                'messages' => $messages,
                'max_tokens' => 500,
                'temperature' => 0.7,
            ]);

            // Check voor fouten
            if (!$response->successful()) {
                throw new \Exception('OpenAI API error: ' . $response->body());
            }

            $data = $response->json();
            
            return response()->json([
                'success' => true,
                'reply' => $data['choices'][0]['message']['content'] ?? 'Sorry, ik kon geen antwoord genereren.',
                'usage' => $data['usage'] ?? null,
            ]);

        } catch (\Exception $e) {
            Log::error('Chatbot error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Er is een fout opgetreden. Probeer het later opnieuw.'
            ], 500);
        }
    }

    /**
     * Build conversation context for AI
     * 
     * @param string $userMessage
     * @param array $history
     * @return array
     */
    private function buildConversationContext(string $userMessage, array $history): array
    {
        $systemPrompt = $this->getSystemPrompt();
        
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt]
        ];

        // Voeg conversatie geschiedenis toe (max 10 berichten)
        $recentHistory = array_slice($history, -10);
        foreach ($recentHistory as $msg) {
            $messages[] = [
                'role' => $msg['role'],
                'content' => $msg['content']
            ];
        }

        // Voeg huidige vraag toe
        $messages[] = [
            'role' => 'user',
            'content' => $userMessage
        ];

        return $messages;
    }

    /**
     * Get system prompt for AI behavior
     * 
     * @return string
     */
    private function getSystemPrompt(): string
    {
        return <<<PROMPT
Je bent een behulpzame AI-assistent voor het Gemeente Portal.

JOUW ROL:
- Beantwoord vragen over gemeentelijke diensten
- Help gebruikers met het indienen van klachten
- Geef informatie over paspoorten, rijbewijzen, vergunningen, etc.
- Wees vriendelijk, professioneel en kort

BELANGRIJKE REGELS:
1. Antwoord altijd in het Nederlands
2. Houd antwoorden kort (max 3-4 zinnen)
3. Als je iets niet weet, geef dat eerlijk toe
4. Verwijs naar de juiste pagina's als dat relevant is
5. Gebruik geen markdown, alleen platte tekst

DIENSTEN DIE JE KENT:
- Klachten/Meldingen indienen
- Paspoort aanvragen
- Rijbewijs aanvragen
- Vergunningen (bouw, evenement, horeca)
- Parkeervergunningen
- Subsidies aanvragen

Begin elk gesprek met een korte begroeting en vraag hoe je kunt helpen.
PROMPT;
    }
}
```

**Belangrijke onderdelen uitgelegd:**

1. **Validatie** (`$request->validate()`):
   - Controleert of bericht aanwezig is
   - Maximaal 1000 karakters
   - Optionele conversatie geschiedenis

2. **Context Building** (`buildConversationContext()`):
   - Combineert system prompt met geschiedenis
   - Beperkt tot laatste 10 berichten (memory management)
   - Voegt huidige vraag toe aan einde

3. **API Call** (`Http::post()`):
   - Gebruikt Laravel HTTP client
   - 30 seconden timeout
   - Bearer token authenticatie
   - GPT-4 model met temperature 0.7

4. **Error Handling**:
   - Try-catch block voor alle fouten
   - Logging naar Laravel logs
   - User-friendly foutmeldingen

---

### Stap 2: Route Configuratie

**Bestand:** `routes/web.php`

```php
use App\Http\Controllers\ChatbotController;

// Chatbot API route
Route::post('/api/chatbot', [ChatbotController::class, 'chat'])
    ->name('chatbot.chat');
```

**Waarom deze setup:**
- POST method voor veiligheid
- CSRF token bescherming (automatisch door Laravel)
- Named route voor makkelijke referentie
- Prefix `/api/` voor duidelijke API structuur

---

### Stap 3: Frontend HTML Structuur

**Bestand:** `resources/views/welcome.blade.php`

```html
<!-- Chatbot Container -->
<div x-data="chatbotWidget()" 
     x-init="init()"
     class="fixed bottom-6 right-6 z-50">
    
    <!-- Chat Button (Closed State) -->
    <button @click="toggleChat()"
            x-show="!isOpen"
            x-transition
            class="chatbot-button">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
        </svg>
        <span class="chatbot-badge" x-show="hasNewMessage">1</span>
    </button>

    <!-- Chat Window (Open State) -->
    <div x-show="isOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95"
         class="chatbot-window">
        
        <!-- Header -->
        <div class="chatbot-header">
            <div class="flex items-center gap-3">
                <div class="chatbot-avatar">AI</div>
                <div>
                    <h3 class="font-bold text-white">Gemeente Assistent</h3>
                    <p class="text-xs text-blue-100">
                        <span class="chatbot-status-dot"></span>
                        Online
                    </p>
                </div>
            </div>
            <button @click="toggleChat()" class="chatbot-close-btn">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Messages Container -->
        <div class="chatbot-messages" x-ref="messagesContainer">
            <!-- Welcome Message -->
            <div class="chatbot-message bot" x-show="messages.length === 0">
                <div class="chatbot-message-avatar">AI</div>
                <div class="chatbot-message-content">
                    <p>Hallo! 👋 Ik ben je Gemeente Assistent. Hoe kan ik je vandaag helpen?</p>
                </div>
            </div>

            <!-- Dynamic Messages -->
            <template x-for="(message, index) in messages" :key="index">
                <div :class="'chatbot-message ' + message.role">
                    <div class="chatbot-message-avatar" x-show="message.role === 'bot'">AI</div>
                    <div class="chatbot-message-content">
                        <p x-text="message.content"></p>
                        <span class="chatbot-message-time" x-text="message.time"></span>
                    </div>
                </div>
            </template>

            <!-- Typing Indicator -->
            <div x-show="isTyping" class="chatbot-message bot">
                <div class="chatbot-message-avatar">AI</div>
                <div class="chatbot-typing">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>

        <!-- Input Area -->
        <form @submit.prevent="sendMessage()" class="chatbot-input-area">
            <input type="text"
                   x-model="userInput"
                   placeholder="Typ je vraag..."
                   :disabled="isTyping"
                   class="chatbot-input"
                   maxlength="500">
            
            <button type="submit"
                    :disabled="!userInput.trim() || isTyping"
                    class="chatbot-send-btn">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
            </button>
        </form>

        <!-- Quick Actions -->
        <div class="chatbot-quick-actions">
            <button @click="quickQuestion('Hoe dien ik een klacht in?')" 
                    class="chatbot-quick-btn">
                📝 Klacht indienen
            </button>
            <button @click="quickQuestion('Hoe vraag ik een paspoort aan?')" 
                    class="chatbot-quick-btn">
                🛂 Paspoort info
            </button>
            <button @click="quickQuestion('Openingstijden gemeente?')" 
                    class="chatbot-quick-btn">
                🕐 Openingstijden
            </button>
        </div>
    </div>
</div>
```

**HTML Structuur Uitleg:**

1. **Alpine.js Data Binding** (`x-data`, `x-model`):
   - Reactieve state management
   - Automatische DOM updates
   - Event handling

2. **Transities** (`x-transition`):
   - Smooth open/close animaties
   - Fade + scale effect
   - 200ms enter, 150ms leave

3. **Conditional Rendering** (`x-show`):
   - Toon/verberg chat window
   - Typing indicator
   - Badge notificatie

4. **Template Loop** (`x-for`):
   - Dynamisch berichten renderen
   - Unieke key per bericht
   - Automatisch updaten bij nieuwe berichten

---

### Stap 4: JavaScript Logica

**Bestand:** `resources/js/chatbot.js`

```javascript
/**
 * Chatbot Widget - Alpine.js Component
 */
function chatbotWidget() {
    return {
        // State
        isOpen: false,
        isTyping: false,
        hasNewMessage: false,
        userInput: '',
        messages: [],
        conversationHistory: [],

        /**
         * Initialize chatbot
         */
        init() {
            console.log('Chatbot initialized');
            
            // Load conversation from localStorage
            this.loadConversation();
            
            // Show welcome badge after 3 seconds
            setTimeout(() => {
                if (!this.isOpen && this.messages.length === 0) {
                    this.hasNewMessage = true;
                }
            }, 3000);
        },

        /**
         * Toggle chat window
         */
        toggleChat() {
            this.isOpen = !this.isOpen;
            this.hasNewMessage = false;
            
            if (this.isOpen) {
                this.$nextTick(() => {
                    this.scrollToBottom();
                    // Focus input after animation
                    setTimeout(() => {
                        const input = this.$el.querySelector('.chatbot-input');
                        if (input) input.focus();
                    }, 250);
                });
            }
        },

        /**
         * Send user message
         */
        async sendMessage() {
            const message = this.userInput.trim();
            
            if (!message || this.isTyping) return;

            // Add user message to UI
            this.addMessage({
                role: 'user',
                content: message,
                time: this.getCurrentTime()
            });

            // Clear input
            this.userInput = '';

            // Show typing indicator
            this.isTyping = true;
            this.scrollToBottom();

            try {
                // Call API
                const response = await fetch('/api/chatbot', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        message: message,
                        conversation_history: this.conversationHistory
                    })
                });

                const data = await response.json();

                // Simulate typing delay (more natural)
                await this.delay(800);

                if (data.success) {
                    // Add bot response
                    this.addMessage({
                        role: 'bot',
                        content: data.reply,
                        time: this.getCurrentTime()
                    });
                } else {
                    throw new Error(data.error || 'Unknown error');
                }

            } catch (error) {
                console.error('Chatbot error:', error);
                
                // Show error message
                this.addMessage({
                    role: 'bot',
                    content: 'Sorry, er ging iets mis. Probeer het opnieuw.',
                    time: this.getCurrentTime()
                });
            } finally {
                this.isTyping = false;
                this.scrollToBottom();
            }
        },

        /**
         * Quick question shortcuts
         */
        quickQuestion(question) {
            this.userInput = question;
            this.sendMessage();
        },

        /**
         * Add message to conversation
         */
        addMessage(message) {
            this.messages.push(message);
            
            // Add to conversation history for API
            this.conversationHistory.push({
                role: message.role === 'user' ? 'user' : 'assistant',
                content: message.content
            });

            // Save to localStorage
            this.saveConversation();

            // Show badge if window closed
            if (!this.isOpen && message.role === 'bot') {
                this.hasNewMessage = true;
            }
        },

        /**
         * Scroll messages to bottom
         */
        scrollToBottom() {
            this.$nextTick(() => {
                const container = this.$refs.messagesContainer;
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            });
        },

        /**
         * Get current time formatted
         */
        getCurrentTime() {
            const now = new Date();
            return now.toLocaleTimeString('nl-NL', { 
                hour: '2-digit', 
                minute: '2-digit' 
            });
        },

        /**
         * Delay helper
         */
        delay(ms) {
            return new Promise(resolve => setTimeout(resolve, ms));
        },

        /**
         * Save conversation to localStorage
         */
        saveConversation() {
            try {
                localStorage.setItem('chatbot_messages', JSON.stringify(this.messages));
                localStorage.setItem('chatbot_history', JSON.stringify(this.conversationHistory));
            } catch (e) {
                console.warn('Could not save conversation:', e);
            }
        },

        /**
         * Load conversation from localStorage
         */
        loadConversation() {
            try {
                const messages = localStorage.getItem('chatbot_messages');
                const history = localStorage.getItem('chatbot_history');
                
                if (messages) this.messages = JSON.parse(messages);
                if (history) this.conversationHistory = JSON.parse(history);
            } catch (e) {
                console.warn('Could not load conversation:', e);
            }
        },

        /**
         * Clear conversation (for testing)
         */
        clearConversation() {
            this.messages = [];
            this.conversationHistory = [];
            localStorage.removeItem('chatbot_messages');
            localStorage.removeItem('chatbot_history');
        }
    };
}

// Make globally available
window.chatbotWidget = chatbotWidget;
```

**JavaScript Functionaliteit Uitleg:**

1. **State Management**:
   - `isOpen`: Chat window status
   - `isTyping`: API call in progress
   - `messages`: UI berichten array
   - `conversationHistory`: API context array

2. **Async/Await Pattern**:
   - Modern JavaScript promises
   - Error handling met try-catch
   - Clean code zonder callback hell

3. **LocalStorage Persistentie**:
   - Berichten bewaren tussen sessies
   - Automatisch laden bij refresh
   - Graceful fallback bij errors

4. **User Experience**:
   - Typing indicator (800ms delay)
   - Auto-scroll naar nieuwe berichten
   - Input focus management
   - Quick action buttons

---

### Stap 5: CSS Styling

**Bestand:** `public/css/chatbot.css`

```css
/* ===========================================
   GEMEENTE CHATBOT STYLING
   =========================================== */

/* Chat Button */
.chatbot-button {
    position: relative;
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

.chatbot-button:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
}

/* Notification Badge */
.chatbot-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    width: 24px;
    height: 24px;
    background: #ef4444;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: bold;
    color: white;
    border: 2px solid white;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

/* Chat Window */
.chatbot-window {
    width: 380px;
    height: 600px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* Header */
.chatbot-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.chatbot-avatar {
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    color: white;
    font-size: 14px;
}

.chatbot-status-dot {
    display: inline-block;
    width: 8px;
    height: 8px;
    background: #10b981;
    border-radius: 50%;
    margin-right: 6px;
    animation: blink 2s infinite;
}

@keyframes blink {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.chatbot-close-btn {
    background: rgba(255, 255, 255, 0.1);
    border: none;
    color: white;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background 0.2s;
}

.chatbot-close-btn:hover {
    background: rgba(255, 255, 255, 0.2);
}

/* Messages Container */
.chatbot-messages {
    flex: 1;
    padding: 20px;
    overflow-y: auto;
    background: #f9fafb;
}

/* Scrollbar Styling */
.chatbot-messages::-webkit-scrollbar {
    width: 6px;
}

.chatbot-messages::-webkit-scrollbar-track {
    background: transparent;
}

.chatbot-messages::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 3px;
}

.chatbot-messages::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}

/* Message Bubble */
.chatbot-message {
    display: flex;
    gap: 12px;
    margin-bottom: 16px;
    animation: fadeInUp 0.3s ease;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.chatbot-message.user {
    flex-direction: row-reverse;
}

.chatbot-message-avatar {
    width: 32px;
    height: 32px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: bold;
    color: white;
    flex-shrink: 0;
}

.chatbot-message-content {
    max-width: 70%;
    padding: 12px 16px;
    border-radius: 16px;
    font-size: 14px;
    line-height: 1.5;
}

.chatbot-message.bot .chatbot-message-content {
    background: white;
    color: #1f2937;
    border-bottom-left-radius: 4px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.chatbot-message.user .chatbot-message-content {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-bottom-right-radius: 4px;
}

.chatbot-message-time {
    display: block;
    font-size: 11px;
    margin-top: 4px;
    opacity: 0.6;
}

/* Typing Indicator */
.chatbot-typing {
    padding: 12px 16px;
    background: white;
    border-radius: 16px;
    border-bottom-left-radius: 4px;
    display: flex;
    gap: 4px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.chatbot-typing span {
    width: 8px;
    height: 8px;
    background: #9ca3af;
    border-radius: 50%;
    animation: typing 1.4s infinite;
}

.chatbot-typing span:nth-child(2) {
    animation-delay: 0.2s;
}

.chatbot-typing span:nth-child(3) {
    animation-delay: 0.4s;
}

@keyframes typing {
    0%, 60%, 100% {
        transform: translateY(0);
    }
    30% {
        transform: translateY(-10px);
    }
}

/* Input Area */
.chatbot-input-area {
    padding: 16px;
    background: white;
    border-top: 1px solid #e5e7eb;
    display: flex;
    gap: 12px;
}

.chatbot-input {
    flex: 1;
    padding: 12px 16px;
    border: 1px solid #d1d5db;
    border-radius: 24px;
    font-size: 14px;
    outline: none;
    transition: border-color 0.2s;
}

.chatbot-input:focus {
    border-color: #667eea;
}

.chatbot-input:disabled {
    background: #f3f4f6;
    cursor: not-allowed;
}

.chatbot-send-btn {
    width: 44px;
    height: 44px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 50%;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
}

.chatbot-send-btn:hover:not(:disabled) {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.chatbot-send-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* Quick Actions */
.chatbot-quick-actions {
    padding: 12px 16px;
    background: white;
    border-top: 1px solid #e5e7eb;
    display: flex;
    gap: 8px;
    overflow-x: auto;
}

.chatbot-quick-btn {
    padding: 8px 12px;
    background: #f3f4f6;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    font-size: 12px;
    white-space: nowrap;
    cursor: pointer;
    transition: all 0.2s;
}

.chatbot-quick-btn:hover {
    background: #e5e7eb;
    border-color: #d1d5db;
}

/* Mobile Responsive */
@media (max-width: 640px) {
    .chatbot-window {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        width: 100%;
        height: 100%;
        border-radius: 0;
    }
    
    .chatbot-button {
        bottom: 20px;
        right: 20px;
    }
}
```

**CSS Belangrijke Features:**

1. **Gradient Backgrounds**:
   - Linear gradient voor moderne uitstraling
   - Consistente kleuren door hele UI

2. **Animaties**:
   - `fadeInUp`: Berichten slides in
   - `typing`: Dot animatie voor laden
   - `pulse`: Badge notificatie
   - `blink`: Status indicator

3. **Responsive Design**:
   - Desktop: 380x600px floating window
   - Mobile: Full screen overlay
   - Media query breakpoint bij 640px

4. **Accessibility**:
   - Focus states voor keyboard navigation
   - Disabled states duidelijk zichtbaar
   - Color contrast WCAG compliant

---

## 4. Frontend Implementatie

### 4.1 Alpine.js Setup

**Waarom Alpine.js?**

✅ **Lightweight** - Slechts 15KB gzipped  
✅ **Declaratief** - HTML-first benadering  
✅ **Geen build step** - Direct in browser  
✅ **Vue-achtige syntax** - Gemakkelijk te leren  

**Installatie:**

```html
<!-- In welcome.blade.php -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
```

### 4.2 Component Structuur

```
Chatbot Widget
├── State (Alpine.js data)
│   ├── isOpen: boolean
│   ├── isTyping: boolean
│   ├── messages: array
│   └── conversationHistory: array
│
├── Methods (Alpine.js functions)
│   ├── init()
│   ├── toggleChat()
│   ├── sendMessage()
│   ├── addMessage()
│   └── scrollToBottom()
│
└── UI Components
    ├── Button (closed state)
    ├── Window (open state)
    │   ├── Header
    │   ├── Messages Container
    │   ├── Input Area
    │   └── Quick Actions
    └── Animations (transitions)
```

---

## 5. Backend Implementatie

### 5.1 Laravel Controller

**Verantwoordelijkheden:**

1. **Input Validatie** - Controleer bericht format
2. **Context Management** - Bouw conversatie geschiedenis
3. **API Communication** - Roep OpenAI aan
4. **Response Formatting** - Geef JSON terug
5. **Error Handling** - Log en toon fouten

### 5.2 System Prompt Engineering

**Belangrijke elementen:**

```
1. ROL DEFINITIE
   - Wie ben je?
   - Wat is je doel?

2. GEDRAGSREGELS
   - Hoe moet je reageren?
   - Wat mag/mag niet?

3. DOMEIN KENNIS
   - Welke diensten ken je?
   - Wat kan je helpen?

4. OUTPUT FORMAT
   - Hoe lang zijn antwoorden?
   - Welke taal gebruik je?
```

**Voorbeeld Prompt Optimalisatie:**

❌ **Slecht:**
```
Je bent een chatbot voor de gemeente. Help gebruikers.
```

✅ **Goed:**
```
Je bent een behulpzame AI-assistent voor het Gemeente Portal.

JOUW ROL:
- Beantwoord vragen over gemeentelijke diensten
- Help gebruikers met het indienen van klachten

BELANGRIJKE REGELS:
1. Antwoord altijd in het Nederlands
2. Houd antwoorden kort (max 3-4 zinnen)
3. Als je iets niet weet, geef dat eerlijk toe

DIENSTEN DIE JE KENT:
- Klachten/Meldingen indienen
- Paspoort aanvragen
[etc...]
```

---

## 6. API Integratie

### 6.1 OpenAI API Setup

**Stap 1: API Key verkrijgen**

1. Ga naar https://platform.openai.com
2. Maak account of log in
3. Navigeer naar API Keys sectie
4. Genereer nieuwe key
5. Kopieer key (eenmalig zichtbaar!)

**Stap 2: Environment Configuratie**

```env
# .env bestand
OPENAI_API_KEY=sk-proj-xxxxxxxxxxxxxxxxxxxxxxxxxx
OPENAI_MODEL=gpt-4
OPENAI_MAX_TOKENS=500
OPENAI_TEMPERATURE=0.7
```

**Stap 3: Config Bestand**

```php
// config/services.php
return [
    'openai' => [
        'key' => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_MODEL', 'gpt-4'),
        'max_tokens' => env('OPENAI_MAX_TOKENS', 500),
        'temperature' => env('OPENAI_TEMPERATURE', 0.7),
    ],
];
```

### 6.2 API Parameters Uitleg

**Model Selection:**

| Model | Kosten | Speed | Kwaliteit | Use Case |
|-------|--------|-------|-----------|----------|
| gpt-3.5-turbo | $ | Snel | Goed | Basic vragen |
| gpt-4 | $$$ | Langzaam | Excellent | Complex vragen |
| gpt-4-turbo | $$ | Medium | Zeer goed | **Aanbevolen** |

**Temperature (0.0 - 2.0):**

- **0.0 - 0.3**: Deterministisch, consistent
- **0.4 - 0.7**: **Balanced** (aanbevolen voor chatbot)
- **0.8 - 1.0**: Creatief, variërend
- **1.1 - 2.0**: Zeer creatief, soms onvoorspelbaar

**Max Tokens:**

```
1 token ≈ 0.75 woorden (Nederlands)

Voorbeelden:
- 100 tokens = ~75 woorden
- 500 tokens = ~375 woorden (2-3 paragrafen)
- 1000 tokens = ~750 woorden (halve A4)
```

**Best Practice:**
- Chatbot: 300-500 tokens
- Lange uitleg: 800-1000 tokens
- Code generatie: 1500-2000 tokens

---

## 7. Testing & Validatie

### 7.1 Unit Tests

**Test Bestand:** `tests/Feature/ChatbotTest.php`

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;

class ChatbotTest extends TestCase
{
    /**
     * Test chatbot endpoint exists
     */
    public function test_chatbot_endpoint_exists()
    {
        $response = $this->postJson('/api/chatbot', [
            'message' => 'Test'
        ]);

        $response->assertStatus(200);
    }

    /**
     * Test message validation
     */
    public function test_requires_message()
    {
        $response = $this->postJson('/api/chatbot', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['message']);
    }

    /**
     * Test message max length
     */
    public function test_message_max_length()
    {
        $longMessage = str_repeat('a', 1001);
        
        $response = $this->postJson('/api/chatbot', [
            'message' => $longMessage
        ]);

        $response->assertStatus(422);
    }

    /**
     * Test successful chat
     */
    public function test_successful_chat_response()
    {
        Http::fake([
            'api.openai.com/*' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => 'Dit is een test antwoord'
                        ]
                    ]
                ],
                'usage' => [
                    'total_tokens' => 50
                ]
            ], 200)
        ]);

        $response = $this->postJson('/api/chatbot', [
            'message' => 'Hoe dien ik een klacht in?'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'reply' => 'Dit is een test antwoord'
        ]);
    }

    /**
     * Test API error handling
     */
    public function test_handles_api_errors()
    {
        Http::fake([
            'api.openai.com/*' => Http::response([], 500)
        ]);

        $response = $this->postJson('/api/chatbot', [
            'message' => 'Test'
        ]);

        $response->assertStatus(500);
        $response->assertJson([
            'success' => false
        ]);
    }

    /**
     * Test conversation history
     */
    public function test_includes_conversation_history()
    {
        Http::fake();

        $this->postJson('/api/chatbot', [
            'message' => 'Hallo',
            'conversation_history' => [
                ['role' => 'user', 'content' => 'Vorige vraag'],
                ['role' => 'assistant', 'content' => 'Vorig antwoord']
            ]
        ]);

        Http::assertSent(function ($request) {
            $body = json_decode($request->body(), true);
            return count($body['messages']) >= 3; // System + history + new
        });
    }
}
```

### 7.2 Browser Testing

**Manual Testing Checklist:**

✅ **Functionaliteit:**
- [ ] Chat window opent/sluit correct
- [ ] Berichten worden verstuurd
- [ ] Bot reageert binnen 3 seconden
- [ ] Typing indicator toont tijdens wachten
- [ ] Quick actions werken
- [ ] Conversatie blijft bewaard na refresh

✅ **UI/UX:**
- [ ] Animaties zijn smooth
- [ ] Scrolling werkt automatisch
- [ ] Input focus na openen
- [ ] Badge verschijnt bij nieuwe berichten
- [ ] Mobile responsive layout

✅ **Edge Cases:**
- [ ] Lange berichten (>500 karakters)
- [ ] Emoji in berichten
- [ ] Snelle opeenvolgende berichten
- [ ] API timeout (>30 sec)
- [ ] Offline modus

---

## 8. Deployment

### 8.1 Pre-deployment Checklist

```bash
# 1. Environment variabelen check
php artisan config:clear
php artisan config:cache

# 2. Assets compileren
npm run build

# 3. Cache optimaliseren
php artisan optimize

# 4. Tests draaien
php artisan test

# 5. Security check
composer audit
```

### 8.2 Production Environment

**`.env` configuratie:**

```env
APP_ENV=production
APP_DEBUG=false

# OpenAI
OPENAI_API_KEY=sk-proj-PRODUCTION-KEY-HERE
OPENAI_MODEL=gpt-4-turbo
OPENAI_MAX_TOKENS=500
OPENAI_TEMPERATURE=0.7

# Rate limiting
OPENAI_RATE_LIMIT=60  # requests per minute

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=error
```

### 8.3 Monitoring & Analytics

**Belangrijke metrics:**

1. **Usage Metrics:**
   - Aantal gesprekken per dag
   - Gemiddelde berichtlengte
   - Meest gestelde vragen

2. **Performance Metrics:**
   - API response tijd
   - Error rate
   - Token usage (kosten)

3. **User Metrics:**
   - Gebruikerstevredenheid
   - Succesvolle resoluties
   - Escalaties naar mens

**Logging implementatie:**

```php
// In ChatbotController.php
Log::info('Chatbot query', [
    'user_message' => $message,
    'response_time' => $responseTime,
    'tokens_used' => $data['usage']['total_tokens'] ?? 0,
    'ip' => $request->ip(),
    'timestamp' => now()
]);
```

---

## 9. Kosten & Limieten

### 9.1 OpenAI Pricing (December 2024)

| Model | Input | Output | Per 1K tokens |
|-------|-------|--------|---------------|
| GPT-4 | $0.03 | $0.06 | |
| GPT-4 Turbo | $0.01 | $0.03 | **Aanbevolen** |
| GPT-3.5 | $0.0005 | $0.0015 | Budget optie |

**Geschatte kosten per gesprek:**

```
Gemiddeld gesprek: 10 berichten
Tokens per bericht: ~150 (user) + ~300 (assistant)
Totaal: ~4500 tokens

Kosten met GPT-4 Turbo:
- Input: (10 * 150) / 1000 * $0.01 = $0.015
- Output: (10 * 300) / 1000 * $0.03 = $0.09
- Totaal: ~$0.11 per gesprek

100 gesprekken per dag = $11/dag = ~$330/maand
```

### 9.2 Rate Limiting

**Laravel Rate Limiter:**

```php
// In app/Providers/RouteServiceProvider.php
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

protected function configureRateLimiting()
{
    RateLimiter::for('chatbot', function (Request $request) {
        return Limit::perMinute(10)
            ->by($request->user()?->id ?: $request->ip())
            ->response(function () {
                return response()->json([
                    'success' => false,
                    'error' => 'Te veel verzoeken. Probeer over 1 minuut opnieuw.'
                ], 429);
            });
    });
}

// In routes/web.php
Route::post('/api/chatbot', [ChatbotController::class, 'chat'])
    ->middleware('throttle:chatbot');
```

---

## 10. Troubleshooting

### 10.1 Veelvoorkomende Problemen

**Probleem: "API Key Invalid"**

```
Error: Incorrect API key provided
```

**Oplossing:**
```bash
# Check .env bestand
cat .env | grep OPENAI

# Clear config cache
php artisan config:clear

# Test API key
curl https://api.openai.com/v1/models \
  -H "Authorization: Bearer $OPENAI_API_KEY"
```

**Probleem: "CORS Error"**

```
Access to fetch blocked by CORS policy
```

**Oplossing:**
```php
// In app/Http/Middleware/Cors.php
public function handle($request, Closure $next)
{
    return $next($request)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'POST')
        ->header('Access-Control-Allow-Headers', 'Content-Type, X-CSRF-TOKEN');
}
```

**Probleem: "Timeout Errors"**

```
cURL error 28: Operation timed out
```

**Oplossing:**
```php
// Increase timeout in controller
Http::withHeaders([...])->timeout(60)->post(...)

// Or in config/http.php
'timeout' => env('HTTP_TIMEOUT', 60),
```

**Probleem: "Memory Limit"**

```
Allowed memory size exhausted
```

**Oplossing:**
```php
// In controller, limit conversation history
$recentHistory = array_slice($history, -10); // Max 10 berichten

// In php.ini
memory_limit = 256M
```

---

## 11. Best Practices

### 11.1 Security

✅ **DO:**
- Rate limiting implementeren
- Input sanitizen
- API keys in .env
- HTTPS gebruiken
- CSRF tokens valideren

❌ **DON'T:**
- API keys in code
- Ongelimiteerde requests
- Gebruikersinput direct doorsturen
- Debug mode in productie

### 11.2 Performance

✅ **Optimalisaties:**
```php
// Cache frequent responses
Cache::remember("chatbot_faq_{$question}", 3600, function() {
    return $this->getAIResponse($question);
});

// Async processing voor lange queries
dispatch(new ProcessChatbotQuery($message))->onQueue('chatbot');

// Compress responses
return response()->json($data)->withHeaders([
    'Content-Encoding' => 'gzip'
]);
```

### 11.3 User Experience

**Goede UX praktijken:**

1. **Typing Indicator**: Toon altijd tijdens wachten
2. **Quick Replies**: Suggesties voor volgende vraag
3. **Error Recovery**: Duidelijke foutmeldingen + retry optie
4. **Context Awareness**: Onthoud vorige vragen
5. **Graceful Degradation**: Fallback als API down is

---

## 12. Toekomstige Verbeteringen

### 12.1 Roadmap

**Fase 2 (Q1 2025):**
- [ ] Voice input/output
- [ ] Meerdere talen (Engels, Arabisch)
- [ ] Sentiment analyse
- [ ] Chat export functie

**Fase 3 (Q2 2025):**
- [ ] AI training op gemeente data
- [ ] Integratie met ticketing systeem
- [ ] Proactieve suggesties
- [ ] Analytics dashboard

**Fase 4 (Q3 2025):**
- [ ] Mobile app versie
- [ ] WhatsApp integratie
- [ ] Video chat optie
- [ ] Multi-agent support

### 12.2 Geavanceerde Features

**Embeddings voor Context Search:**

```php
// Zoek relevante documenten voordat AI vraag beantwoordt
$embedding = OpenAI::embeddings()->create([
    'model' => 'text-embedding-ada-002',
    'input' => $userMessage
]);

$relevantDocs = $this->findSimilarDocuments($embedding);

// Voeg docs toe aan context
$context = "Relevante informatie:\n" . implode("\n", $relevantDocs);
```

**Function Calling:**

```php
// Laat AI functies aanroepen
$functions = [
    [
        'name' => 'zoek_klacht',
        'description' => 'Zoek klacht op nummer',
        'parameters' => [
            'type' => 'object',
            'properties' => [
                'klacht_nummer' => ['type' => 'string']
            ]
        ]
    ]
];
```

---

## 13. Conclusie

### 13.1 Samenvatting

Je hebt nu een volledig werkende AI chatbot geïmplementeerd met:

✅ **Frontend**: Alpine.js + HTML/CSS  
✅ **Backend**: Laravel + OpenAI API  
✅ **Features**: Context, typing, quick actions  
✅ **Testing**: Unit tests + manual testing  
✅ **Deployment**: Production ready  

### 13.2 Geleerde Lessen

**Technisch:**
- Alpine.js is perfect voor kleine interactive componenten
- OpenAI API is krachtig maar kost geld
- Rate limiting is essentieel
- Goede system prompts maken groot verschil

**UX:**
- Typing indicators verbeteren perceived performance
- Quick actions verhogen engagement
- Conversatie geschiedenis is belangrijk
- Foutmeldingen moeten begrijpelijk zijn

### 13.3 Resources

**Documentatie:**
- OpenAI: https://platform.openai.com/docs
- Alpine.js: https://alpinejs.dev
- Laravel HTTP: https://laravel.com/docs/http-client
- Tailwind CSS: https://tailwindcss.com

**Community:**
- OpenAI Forum: https://community.openai.com
- Laravel Discord: https://discord.gg/laravel
- GitHub Issues: Voor specifieke bugs

---

**Einde Document**

*Deze documentatie is compleet en production-ready. Gebruik het als referentie voor toekomstige projecten of om het team in te werken.*

**Contact:**
- Email: abdisamad.abdulle@gemeente.nl
- GitHub: @abii2024

**Versie:** 1.0  
**Laatst bijgewerkt:** 10 November 2025
