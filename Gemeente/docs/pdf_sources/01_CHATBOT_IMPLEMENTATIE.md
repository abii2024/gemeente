# PDF 1: Chatbot Implementatie

**Gemeente Portal - AI Chatbot**  
**Datum:** 10 November 2025  
**Auteur:** Abdisamad Abdulle

---

## Inhoudsopgave

1. Wat is de Chatbot?
2. Hoe Werkt Het?
3. Frontend (Alpine.js)
4. Backend (Laravel)
5. OpenAI API
6. Complete Flow
7. Code Uitleg
8. Testing

---

## 1. Wat is de Chatbot?

### Doel
Een AI-assistent die burgers helpt met:
- Vragen over gemeentediensten
- Klachten indienen uitleg
- Paspoort/Rijbewijs informatie
- Openingstijden en contact

### Technologieën
- **Frontend:** Alpine.js + HTML/CSS
- **Backend:** Laravel 11
- **AI:** OpenAI GPT-4 Turbo
- **Database:** Geen (stateless chat)

---

## 2. Hoe Werkt Het? (Simpel)

```
┌──────────────┐
│  GEBRUIKER   │ Typt: "Hoe vraag ik paspoort aan?"
└──────┬───────┘
       │
       ▼
┌──────────────────────┐
│  ALPINE.JS (Browser) │ Toont bericht, stuurt naar server
└──────┬───────────────┘
       │
       ▼
┌──────────────────┐
│ LARAVEL (Server) │ Ontvangt bericht, roept OpenAI aan
└──────┬───────────┘
       │
       ▼
┌──────────────┐
│  OPENAI API  │ GPT-4 denkt na, geeft antwoord
└──────┬───────┘
       │
       ▼
┌──────────────┐
│  GEBRUIKER   │ Ziet antwoord: "Om een paspoort aan..."
└──────────────┘
```

**Tijdsduur:** 1-3 seconden per bericht

---

## 3. Frontend - Alpine.js

### Bestand: `resources/views/welcome.blade.php`

**HTML Structuur:**
```html
<div x-data="chatbotWidget()" x-init="init()">
    
    <!-- Chat Button (Gesloten) -->
    <button @click="toggleChat()" x-show="!isOpen">
        💬 <span class="badge" x-show="hasNewMessage">1</span>
    </button>

    <!-- Chat Window (Open) -->
    <div x-show="isOpen" x-transition>
        
        <!-- Header -->
        <div class="header">
            <h3>Gemeente Assistent</h3>
            <button @click="toggleChat()">✕</button>
        </div>

        <!-- Berichten -->
        <div class="messages" x-ref="messagesContainer">
            <template x-for="msg in messages">
                <div :class="'message ' + msg.role">
                    <p x-text="msg.content"></p>
                </div>
            </template>
            
            <!-- Typing Indicator -->
            <div x-show="isTyping" class="typing">
                <span></span><span></span><span></span>
            </div>
        </div>

        <!-- Input -->
        <form @submit.prevent="sendMessage()">
            <input x-model="userInput" placeholder="Typ je vraag...">
            <button type="submit">Verstuur</button>
        </form>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <button @click="quickQuestion('Hoe dien ik klacht in?')">
                📝 Klacht
            </button>
            <button @click="quickQuestion('Paspoort info?')">
                🛂 Paspoort
            </button>
        </div>
    </div>
</div>
```

**JavaScript Logica:**
```javascript
function chatbotWidget() {
    return {
        // State
        isOpen: false,
        isTyping: false,
        userInput: '',
        messages: [],
        conversationHistory: [],

        // Initialize
        init() {
            this.loadConversation();
            setTimeout(() => {
                this.hasNewMessage = true;
            }, 3000);
        },

        // Toggle chat window
        toggleChat() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.$nextTick(() => this.scrollToBottom());
            }
        },

        // Send message
        async sendMessage() {
            const message = this.userInput.trim();
            if (!message) return;

            // Add user message
            this.addMessage({
                role: 'user',
                content: message
            });

            this.userInput = '';
            this.isTyping = true;

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

                // Add bot response
                if (data.success) {
                    this.addMessage({
                        role: 'bot',
                        content: data.reply
                    });
                }
            } catch (error) {
                console.error('Error:', error);
                this.addMessage({
                    role: 'bot',
                    content: 'Sorry, er ging iets mis.'
                });
            } finally {
                this.isTyping = false;
            }
        },

        // Add message to conversation
        addMessage(message) {
            message.time = new Date().toLocaleTimeString('nl-NL', {
                hour: '2-digit',
                minute: '2-digit'
            });
            
            this.messages.push(message);
            
            this.conversationHistory.push({
                role: message.role === 'user' ? 'user' : 'assistant',
                content: message.content
            });

            this.saveConversation();
            this.scrollToBottom();
        },

        // Quick question
        quickQuestion(question) {
            this.userInput = question;
            this.sendMessage();
        },

        // Scroll to bottom
        scrollToBottom() {
            this.$nextTick(() => {
                const container = this.$refs.messagesContainer;
                container.scrollTop = container.scrollHeight;
            });
        },

        // Save to localStorage
        saveConversation() {
            localStorage.setItem('chatbot_messages', JSON.stringify(this.messages));
            localStorage.setItem('chatbot_history', JSON.stringify(this.conversationHistory));
        },

        // Load from localStorage
        loadConversation() {
            const messages = localStorage.getItem('chatbot_messages');
            const history = localStorage.getItem('chatbot_history');
            
            if (messages) this.messages = JSON.parse(messages);
            if (history) this.conversationHistory = JSON.parse(history);
        }
    };
}
```

**Belangrijke Alpine.js Directives:**

| Directive | Wat Het Doet | Voorbeeld |
|-----------|--------------|-----------|
| `x-data` | Definieert component state | `x-data="chatbotWidget()"` |
| `x-show` | Toon/verberg element | `x-show="isOpen"` |
| `x-model` | Two-way data binding | `x-model="userInput"` |
| `@click` | Click event handler | `@click="toggleChat()"` |
| `x-for` | Loop over array | `x-for="msg in messages"` |
| `x-ref` | Reference element | `x-ref="messagesContainer"` |
| `x-transition` | Smooth animations | `x-transition` |
| `x-init` | Run on initialize | `x-init="init()"` |

---

## 4. Backend - Laravel Controller

### Bestand: `app/Http/Controllers/ChatbotController.php`

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    /**
     * Handle chatbot conversation
     */
    public function chat(Request $request)
    {
        // 1. VALIDATIE
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'conversation_history' => 'nullable|array'
        ]);

        try {
            // 2. BOUW CONTEXT
            $messages = $this->buildMessages(
                $validated['message'],
                $validated['conversation_history'] ?? []
            );

            // 3. ROEP OPENAI AAN
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                'Content-Type' => 'application/json',
            ])
            ->timeout(30)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4-turbo',
                'messages' => $messages,
                'max_tokens' => 500,
                'temperature' => 0.7,
            ]);

            // 4. CHECK RESPONSE
            if (!$response->successful()) {
                throw new \Exception('API Error');
            }

            $data = $response->json();

            // 5. LOG GEBRUIK (voor monitoring)
            Log::info('Chatbot query', [
                'tokens' => $data['usage']['total_tokens'] ?? 0,
                'message_length' => strlen($validated['message'])
            ]);

            // 6. RETURN RESPONSE
            return response()->json([
                'success' => true,
                'reply' => $data['choices'][0]['message']['content'],
                'usage' => $data['usage']
            ]);

        } catch (\Exception $e) {
            Log::error('Chatbot error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Er is een fout opgetreden.'
            ], 500);
        }
    }

    /**
     * Build messages array for OpenAI
     */
    private function buildMessages(string $userMessage, array $history): array
    {
        $messages = [
            ['role' => 'system', 'content' => $this->getSystemPrompt()]
        ];

        // Add conversation history (max 10 messages)
        $recentHistory = array_slice($history, -10);
        foreach ($recentHistory as $msg) {
            $messages[] = [
                'role' => $msg['role'],
                'content' => $msg['content']
            ];
        }

        // Add current message
        $messages[] = [
            'role' => 'user',
            'content' => $userMessage
        ];

        return $messages;
    }

    /**
     * System prompt - defines AI behavior
     */
    private function getSystemPrompt(): string
    {
        return <<<PROMPT
Je bent een behulpzame AI-assistent voor het Gemeente Portal.

JOUW ROL:
- Beantwoord vragen over gemeentelijke diensten
- Help gebruikers met het indienen van klachten
- Geef informatie over paspoorten, rijbewijzen, vergunningen, etc.
- Wees vriendelijk, professioneel en behulpzaam

BELANGRIJKE REGELS:
1. Antwoord altijd in het Nederlands
2. Houd antwoorden kort en bondig (max 3-4 zinnen)
3. Als je iets niet weet, geef dat eerlijk toe
4. Gebruik geen markdown formatting
5. Wees altijd beleefd en professioneel

DIENSTEN DIE JE KENT:
- Klachten/Meldingen indienen (vuilnis, kapotte straatverlichting, etc.)
- Paspoort aanvragen (kosten €71,55, levertijd 3-5 dagen)
- Rijbewijs aanvragen (kosten €41,85, levertijd 5-7 dagen)
- Vergunningen (bouw, evenement, horeca)
- Parkeervergunningen (bewoners €120/jaar)
- Subsidies (energie, verenigingen, evenementen)

VOORBEELD GESPREK:
Gebruiker: "Hoe dien ik een klacht in?"
Jij: "Je kunt een klacht indienen via het formulier op onze website. 
Klik op 'Melding Doen', vul het formulier in en voeg eventueel een foto toe. 
Je ontvangt binnen 24 uur een bevestiging met een meldingsnummer."

Begin elk gesprek vriendelijk en vraag hoe je kunt helpen.
PROMPT;
    }
}
```

**Code Uitleg Stap-voor-Stap:**

### 1. Validatie
```php
$validated = $request->validate([
    'message' => 'required|string|max:1000',
    'conversation_history' => 'nullable|array'
]);
```
**Wat gebeurt hier?**
- Check of `message` aanwezig is
- Max 1000 karakters (voorkomt misbruik)
- `conversation_history` is optioneel

### 2. Context Building
```php
$messages = $this->buildMessages($validated['message'], $history);
```
**Wat gebeurt hier?**
- Combineert system prompt + historie + nieuwe vraag
- Limiteert tot laatste 10 berichten (memory management)
- Bouwt array die OpenAI verwacht

### 3. API Call
```php
$response = Http::withHeaders([...])
    ->timeout(30)
    ->post('https://api.openai.com/v1/chat/completions', [...]);
```
**Wat gebeurt hier?**
- Stuurt HTTP POST naar OpenAI
- Bearer token voor authenticatie
- 30 seconden timeout
- Gebruikt Laravel HTTP client (Guzzle wrapper)

### 4. Response Handling
```php
return response()->json([
    'success' => true,
    'reply' => $data['choices'][0]['message']['content']
]);
```
**Wat gebeurt hier?**
- Haalt AI antwoord uit response
- Wrapped in JSON voor frontend
- Inclusief success flag voor error handling

---

## 5. OpenAI API - Details

### API Endpoint
```
POST https://api.openai.com/v1/chat/completions
```

### Request Format
```json
{
    "model": "gpt-4-turbo",
    "messages": [
        {
            "role": "system",
            "content": "Je bent een gemeente assistent..."
        },
        {
            "role": "user",
            "content": "Hoe vraag ik paspoort aan?"
        }
    ],
    "max_tokens": 500,
    "temperature": 0.7
}
```

### Response Format
```json
{
    "id": "chatcmpl-123",
    "object": "chat.completion",
    "created": 1699000000,
    "model": "gpt-4-turbo",
    "choices": [
        {
            "index": 0,
            "message": {
                "role": "assistant",
                "content": "Om een paspoort aan te vragen..."
            },
            "finish_reason": "stop"
        }
    ],
    "usage": {
        "prompt_tokens": 150,
        "completion_tokens": 80,
        "total_tokens": 230
    }
}
```

### Parameters Uitleg

**Model:**
- `gpt-4-turbo`: Sneller en goedkoper dan GPT-4
- `gpt-4`: Hoogste kwaliteit maar duurder
- `gpt-3.5-turbo`: Snelst en goedkoopst

**Temperature (0.0 - 2.0):**
- `0.0`: Deterministisch (altijd zelfde antwoord)
- `0.7`: **Balanced** (aanbevolen voor chatbot)
- `1.5`: Zeer creatief (kan onvoorspelbaar zijn)

**Max Tokens:**
- 1 token ≈ 0.75 woorden (Nederlands)
- 500 tokens = ~375 woorden = 2-3 paragrafen

### Kosten Berekening

**GPT-4 Turbo Pricing:**
- Input: $0.01 per 1K tokens
- Output: $0.03 per 1K tokens

**Voorbeeld:**
```
Vraag: "Hoe vraag ik paspoort aan?" (10 tokens)
System Prompt: (200 tokens)
Antwoord: (150 tokens)

Input: 210 tokens → 210/1000 × $0.01 = $0.0021
Output: 150 tokens → 150/1000 × $0.03 = $0.0045
TOTAAL: $0.0066 per vraag

100 vragen/dag = $0.66/dag = ~$20/maand
```

---

## 6. Complete Data Flow

### Request Flow (Frontend → Backend → AI)

```
1. GEBRUIKER KLIKT "VERSTUUR"
   ↓
2. ALPINE.JS sendMessage()
   - Voegt bericht toe aan UI
   - Toont typing indicator
   ↓
3. FETCH API CALL
   fetch('/api/chatbot', {
       method: 'POST',
       body: JSON.stringify({
           message: "Hoe vraag ik paspoort aan?",
           conversation_history: [...]
       })
   })
   ↓
4. LARAVEL ROUTE
   POST /api/chatbot → ChatbotController@chat
   ↓
5. CONTROLLER VALIDATIE
   - Check message aanwezig
   - Max 1000 characters
   ↓
6. BUILD CONTEXT
   [
       {role: 'system', content: 'Je bent...'},
       {role: 'user', content: 'Vorige vraag'},
       {role: 'assistant', content: 'Vorig antwoord'},
       {role: 'user', content: 'Nieuwe vraag'}
   ]
   ↓
7. OPENAI API CALL
   POST https://api.openai.com/v1/chat/completions
   Headers: {
       Authorization: 'Bearer sk-...'
   }
   Body: {
       model: 'gpt-4-turbo',
       messages: [...],
       max_tokens: 500,
       temperature: 0.7
   }
   ↓
8. GPT-4 PROCESSING
   - Leest system prompt
   - Analyseert conversatie historie
   - Genereert Nederlands antwoord
   ↓
9. OPENAI RESPONSE
   {
       choices: [{
           message: {
               content: "Om een paspoort aan..."
           }
       }],
       usage: {total_tokens: 230}
   }
   ↓
10. LARAVEL RESPONSE
    return json({
        success: true,
        reply: "Om een paspoort aan..."
    })
    ↓
11. ALPINE.JS ONTVANGT
    - Hide typing indicator
    - Add bot message to UI
    - Save to localStorage
    - Scroll to bottom
    ↓
12. GEBRUIKER ZIET ANTWOORD
```

**Totale tijd:** ~1-3 seconden

---

## 7. Environment Setup

### .env Configuratie

```env
# OpenAI API
OPENAI_API_KEY=sk-proj-xxxxxxxxxxxxxxxxxxxxxxxxxx
OPENAI_MODEL=gpt-4-turbo
OPENAI_MAX_TOKENS=500
OPENAI_TEMPERATURE=0.7

# Rate Limiting
CHATBOT_RATE_LIMIT=10  # requests per minute per user
```

### Route Configuratie

**Bestand:** `routes/web.php`

```php
use App\Http\Controllers\ChatbotController;

Route::post('/api/chatbot', [ChatbotController::class, 'chat'])
    ->middleware(['throttle:10,1'])  // Max 10 per minuut
    ->name('chatbot.chat');
```

### CSS Bestand

**Bestand:** `public/css/chatbot.css`

Belangrijkste styling:
- Chat button met gradient achtergrond
- Smooth transities en animaties
- Responsive design (mobiel + desktop)
- Typing indicator animatie
- Message bubbles styling

---

## 8. Testing

### Manual Testing Checklist

**Functionaliteit:**
- [ ] Chat button verschijnt rechtsonder
- [ ] Window opent bij klikken
- [ ] Berichten versturen werkt
- [ ] Bot reageert binnen 3 seconden
- [ ] Typing indicator toont correct
- [ ] Quick actions werken
- [ ] Berichten blijven na page refresh

**UI/UX:**
- [ ] Animaties zijn smooth
- [ ] Auto-scroll naar nieuwe berichten
- [ ] Input krijgt focus na openen
- [ ] Badge toont bij nieuwe bot berichten
- [ ] Responsive op mobiel

**Edge Cases:**
- [ ] Lange berichten (>500 karakters)
- [ ] Snelle opeenvolgende berichten
- [ ] Emoji in berichten
- [ ] API timeout handling
- [ ] Offline modus

### Unit Tests

**Bestand:** `tests/Feature/ChatbotTest.php`

```php
public function test_chatbot_endpoint_exists()
{
    $response = $this->postJson('/api/chatbot', [
        'message' => 'Test'
    ]);

    $response->assertStatus(200);
}

public function test_requires_message()
{
    $response = $this->postJson('/api/chatbot', []);
    $response->assertStatus(422);
}

public function test_message_max_length()
{
    $longMessage = str_repeat('a', 1001);
    
    $response = $this->postJson('/api/chatbot', [
        'message' => $longMessage
    ]);

    $response->assertStatus(422);
}
```

---

## Conclusie

### Wat je Hebt Geleerd

✅ **Frontend:** Alpine.js voor reactive UI  
✅ **Backend:** Laravel controller met API calls  
✅ **AI Integration:** OpenAI GPT-4 gebruiken  
✅ **Data Flow:** Complete request/response cycle  
✅ **Best Practices:** Error handling, rate limiting, caching

### Belangrijkste Bestanden

```
Frontend:
├── resources/views/welcome.blade.php (HTML)
├── public/css/chatbot.css (Styling)
└── resources/js/chatbot.js (Alpine.js)

Backend:
├── app/Http/Controllers/ChatbotController.php
├── routes/web.php
└── .env (API keys)

Tests:
└── tests/Feature/ChatbotTest.php
```

### Volgende Stappen

1. ✅ Test de chatbot grondig
2. ✅ Monitor API kosten via OpenAI dashboard
3. ✅ Pas system prompt aan voor specifieke use cases
4. ✅ Voeg analytics toe (Google Analytics events)
5. ✅ Implementeer feedback mechanisme

---

**Einde PDF 1**
