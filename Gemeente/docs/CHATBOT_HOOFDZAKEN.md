# 🤖 Chatbot - Hoofdzaken Uitgelegd

**Gemeente Portal AI Chatbot**  
**Versie:** Samenvatting voor beginners  
**Datum:** 10 November 2025

---

## 📌 Wat je moet weten in 1 minuut

De chatbot is een **AI assistent** die burgers helpt met vragen over de gemeente.

**3 Hoofdonderdelen:**
1. **Frontend** (Alpine.js) - Chat interface die gebruiker ziet
2. **Backend** (Laravel) - Stuurt berichten naar AI
3. **AI** (OpenAI GPT-4) - Beantwoordt de vragen

---

## 1️⃣ Frontend - Wat de Gebruiker Ziet

### Bestand: `resources/views/welcome.blade.php`

**Wat zit erin:**
```html
<div x-data="chatbotWidget()">
    <!-- Chat button -->
    <button @click="toggleChat()">💬</button>
    
    <!-- Chat window -->
    <div x-show="isOpen">
        <div class="messages">
            <!-- Berichten hier -->
        </div>
        <input x-model="userInput">
        <button @click="sendMessage()">Verstuur</button>
    </div>
</div>
```

**Wat doet het:**
- ✅ Toont chat knop rechtsonder
- ✅ Opent chat window bij klikken
- ✅ Laat berichten zien
- ✅ Gebruiker kan typen en versturen

**JavaScript (Alpine.js):**
```javascript
function chatbotWidget() {
    return {
        isOpen: false,      // Chat open/dicht
        userInput: '',      // Wat gebruiker typt
        messages: [],       // Alle berichten
        
        sendMessage() {
            // 1. Toon bericht van gebruiker
            this.messages.push({
                role: 'user',
                content: this.userInput
            });
            
            // 2. Roep Laravel API aan
            fetch('/api/chatbot', {
                method: 'POST',
                body: JSON.stringify({
                    message: this.userInput
                })
            })
            .then(response => response.json())
            .then(data => {
                // 3. Toon antwoord van AI
                this.messages.push({
                    role: 'bot',
                    content: data.reply
                });
            });
        }
    }
}
```

**Belangrijke punten:**
- 🎨 **Alpine.js** = Makkelijke reactivity (zoals Vue.js maar kleiner)
- 📱 **Responsive** = Werkt op mobiel en desktop
- 💾 **LocalStorage** = Bewaart berichten na refresh

---

## 2️⃣ Backend - Laravel Controller

### Bestand: `app/Http/Controllers/ChatbotController.php`

**Wat doet het:**
```php
class ChatbotController extends Controller
{
    public function chat(Request $request)
    {
        // 1. Haal bericht op
        $message = $request->input('message');
        
        // 2. Roep OpenAI API aan
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY')
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Je bent een gemeente assistent...'
                ],
                [
                    'role' => 'user',
                    'content' => $message
                ]
            ]
        ]);
        
        // 3. Stuur antwoord terug
        return response()->json([
            'reply' => $response->json()['choices'][0]['message']['content']
        ]);
    }
}
```

**Belangrijke onderdelen:**

1. **Validatie** - Check of bericht klopt
2. **System Prompt** - Vertelt AI hoe te reageren
3. **API Call** - Stuurt naar OpenAI
4. **Response** - Geeft antwoord terug als JSON

---

## 3️⃣ OpenAI API - De AI Hersenen

### Wat is OpenAI?

**OpenAI** is een bedrijf dat AI modellen maakt zoals **GPT-4**.

**Hoe werkt het:**

```
Laravel stuurt:
{
    "model": "gpt-4",
    "messages": [
        {
            "role": "system",
            "content": "Je bent een gemeente assistent"
        },
        {
            "role": "user", 
            "content": "Hoe vraag ik paspoort aan?"
        }
    ]
}

OpenAI antwoordt:
{
    "choices": [
        {
            "message": {
                "content": "Om een paspoort aan te vragen..."
            }
        }
    ]
}
```

### System Prompt (Belangrijk!)

Dit vertelt de AI **HOE** te reageren:

```
Je bent een behulpzame AI-assistent voor het Gemeente Portal.

REGELS:
1. Antwoord altijd in het Nederlands
2. Houd antwoorden kort (max 3-4 zinnen)
3. Wees vriendelijk en professioneel
4. Als je iets niet weet, geef dat toe

DIENSTEN DIE JE KENT:
- Klachten indienen
- Paspoort aanvragen
- Rijbewijs aanvragen
- Vergunningen
- Parkeervergunningen
```

**Waarom belangrijk?**
- 🎯 Bepaalt karakter van chatbot
- 📏 Controleert lengte antwoorden
- 🇳🇱 Zorgt voor Nederlandse antwoorden
- 🎓 Geeft domein kennis

---

## 4️⃣ De Complete Flow

### Stap-voor-stap:

```
┌─────────────────────────────────────────────────────────┐
│ 1. GEBRUIKER                                            │
│    Typt: "Hoe dien ik een klacht in?"                  │
└─────────────────┬───────────────────────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────────────────────┐
│ 2. ALPINE.JS (Frontend JavaScript)                      │
│    - Voegt bericht toe aan chat                         │
│    - Toont "typing..." indicator                        │
│    - Roept fetch('/api/chatbot') aan                    │
└─────────────────┬───────────────────────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────────────────────┐
│ 3. LARAVEL ROUTE (routes/web.php)                       │
│    Route::post('/api/chatbot', ChatbotController)       │
└─────────────────┬───────────────────────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────────────────────┐
│ 4. CHATBOT CONTROLLER (app/Http/Controllers)            │
│    - Valideert input                                    │
│    - Bouwt conversatie context                          │
│    - Roept OpenAI API aan                               │
└─────────────────┬───────────────────────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────────────────────┐
│ 5. OPENAI API (api.openai.com)                          │
│    - GPT-4 verwerkt vraag                               │
│    - Genereert Nederlands antwoord                      │
│    - Stuurt JSON terug                                  │
└─────────────────┬───────────────────────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────────────────────┐
│ 6. TERUG NAAR GEBRUIKER                                 │
│    - Laravel geeft JSON terug                           │
│    - Alpine.js toont antwoord                           │
│    - "Typing..." verdwijnt                              │
│    - Antwoord verschijnt in chat                        │
└─────────────────────────────────────────────────────────┘
```

**Tijdsduur:** ~1-3 seconden per bericht

---

## 5️⃣ Belangrijke Bestanden

### Frontend (Wat gebruiker ziet)

```
resources/views/welcome.blade.php
└── HTML structuur van chatbot

public/css/chatbot.css
└── Styling (kleuren, animaties)

resources/js/chatbot.js
└── Alpine.js logica
```

### Backend (Server logica)

```
app/Http/Controllers/ChatbotController.php
└── Hoofdlogica (API calls)

routes/web.php
└── POST /api/chatbot route

.env
└── OPENAI_API_KEY (geheim!)
```

### Dependencies

```
composer.json
└── guzzlehttp/guzzle (HTTP requests)

package.json
└── alpinejs (frontend reactivity)
```

---

## 6️⃣ Kosten & Limieten

### OpenAI Pricing

**GPT-4 Turbo** (aanbevolen):
- Input: $0.01 per 1K tokens
- Output: $0.03 per 1K tokens

**Wat is een token?**
- 1 token ≈ 0.75 woorden
- "Hoe vraag ik paspoort aan?" = ~7 tokens

**Voorbeeld kosten:**

```
10 berichten per gesprek
= ~150 tokens input + ~300 tokens output per bericht
= 1500 + 3000 = 4500 tokens totaal

Kosten:
- Input: 1500/1000 × $0.01 = $0.015
- Output: 3000/1000 × $0.03 = $0.09
TOTAAL: ~$0.11 per gesprek

100 gesprekken/dag = $11/dag = ~$330/maand
```

### Rate Limiting

**Belangrijk!** Voorkom misbruik:

```php
// In routes/web.php
Route::post('/api/chatbot', [ChatbotController::class, 'chat'])
    ->middleware('throttle:10,1'); // Max 10 requests per minuut
```

---

## 7️⃣ Testing

### Test het werkt:

1. **Start Laravel:**
```bash
php artisan serve
```

2. **Open browser:**
```
http://localhost:8000
```

3. **Klik chatbot button** (rechtsonder)

4. **Test vragen:**
- "Hallo"
- "Hoe dien ik een klacht in?"
- "Openingstijden?"

### Checklist:

- [ ] Chat button verschijnt
- [ ] Window opent bij klikken
- [ ] Typing indicator toont
- [ ] Antwoord komt binnen 3 sec
- [ ] Berichten blijven na refresh (localStorage)

---

## 8️⃣ Veelvoorkomende Problemen

### "API Key Invalid"

**Probleem:** OpenAI accepteert key niet

**Oplossing:**
```bash
# Check .env
cat .env | grep OPENAI

# Clear cache
php artisan config:clear
```

### "Timeout Error"

**Probleem:** AI reageert niet binnen 30 sec

**Oplossing:**
```php
// Increase timeout in controller
Http::timeout(60)->post(...)
```

### "CORS Error"

**Probleem:** Browser blokkeert request

**Oplossing:** 
```php
// Add to ChatbotController
return response()->json($data)
    ->header('Access-Control-Allow-Origin', '*');
```

---

## 9️⃣ Conclusie

### Samenvatting in 3 Punten:

1. **Alpine.js** = Frontend chat interface
2. **Laravel** = Backend die OpenAI API aanroept
3. **GPT-4** = AI die vragen beantwoordt

### Wat heb je geleerd:

✅ Hoe Alpine.js werkt voor reactivity  
✅ Hoe Laravel met externe APIs praat  
✅ Hoe OpenAI GPT-4 te gebruiken  
✅ Hoe conversatie context te beheren  
✅ Hoe kosten te berekenen

### Volgende Stappen:

1. Test de chatbot grondig
2. Pas system prompt aan voor jouw gebruik
3. Monitor kosten met OpenAI dashboard
4. Voeg meer features toe (voice, multi-language)

---

## 🔗 Belangrijke Links

**Documentatie:**
- OpenAI API: https://platform.openai.com/docs
- Alpine.js: https://alpinejs.dev
- Laravel HTTP: https://laravel.com/docs/http-client

**Krijg Hulp:**
- OpenAI Community: https://community.openai.com
- Laravel Discord: https://discord.gg/laravel

---

**Klaar!** 🎉

Je begrijpt nu de **hoofdzaken** van hoe de chatbot werkt. Voor gedetailleerde code, zie de volledige bestanden in het project.

**Contact:**
- Email: abdisamad.abdulle@gemeente.nl
- GitHub: @abii2024

**Versie:** 1.0 Samenvatting  
**Laatst bijgewerkt:** 10 November 2025
