# Gemeente Portal MCP Server

Model Context Protocol server voor interactie met de Gemeente Portal Laravel applicatie.

## 🚀 Features

### 🔌 API Tools
- **Klachten Beheer**: CRUD operaties voor klachten
- **Zoeken & Filteren**: Geavanceerd zoeken in klachten
- **Statistieken**: Real-time statistieken en analytics
- **Kaart Data**: GPS coördinaten voor kaartweergave
- **Notities**: Interne notities aan klachten toevoegen
- **Status Updates**: Klacht status beheren

### 🌐 Browser Automation Tools
- **Website Navigatie**: Bezoek pagina's alsof je een echte gebruiker bent
- **Automatisch Inloggen**: Log in op het admin dashboard
- **Formulieren Invullen**: Vul klachten in via de UI
- **Screenshots**: Maak visuele verificaties van pagina's
- **Data Extractie**: Haal data van de website zoals een web scraper
- **Dashboard Stats**: Extract real-time statistieken van het dashboard
- **Interactieve Taken**: Klik, hover, scroll, upload bestanden

## 📋 Available Tools

### API Tools

### 1. `get_complaints`
Haal alle klachten op met optionele filters.

```typescript
{
  status?: "open" | "in_progress" | "resolved" | "closed",
  priority?: "low" | "medium" | "high" | "urgent",
  limit?: number
}
```

### 2. `get_complaint_by_id`
Haal een specifieke klacht op.

```typescript
{
  id: number
}
```

### 3. `create_complaint`
Maak een nieuwe klacht aan.

```typescript
{
  title: string,
  description: string,
  category: string,
  priority: string,
  reporter_name: string,
  reporter_email: string,
  reporter_phone?: string,
  location?: string,
  lat?: number,
  lng?: number
}
```

### 4. `update_complaint_status`
Update de status van een klacht.

```typescript
{
  id: number,
  status: "open" | "in_progress" | "resolved" | "closed"
}
```

### 5. `add_complaint_note`
Voeg een notitie toe aan een klacht.

```typescript
{
  complaint_id: number,
  note: string
}
```

### 6. `get_statistics`
Haal statistieken op.

```typescript
{
  period?: "today" | "week" | "month" | "year" | "all"
}
```

### 7. `search_complaints`
Zoek klachten op basis van een zoekterm.

```typescript
{
  query: string,
  limit?: number
}
```

### 8. `get_complaint_map_data`
Haal klachten op voor kaartweergave.

```typescript
{
  status?: string
}
```

### Browser Automation Tools

### 9. `browser_goto`
Navigeer naar een pagina op de website.

```typescript
{
  url: string,  // bijv. '/dashboard', '/klachten', of volledige URL
  waitUntil?: "load" | "domcontentloaded" | "networkidle"
}
```

**Voorbeeld:**
- "Ga naar de homepage"
- "Open het dashboard"
- "Navigeer naar de klachten pagina"

### 10. `browser_login`
Log automatisch in op de Gemeente Portal.

```typescript
{
  email?: string,     // optioneel, gebruikt admin credentials
  password?: string   // optioneel
}
```

**Voorbeeld:**
- "Log in als admin"
- "Log in met email test@example.com"

### 11. `browser_screenshot`
Maak een screenshot van de huidige pagina.

```typescript
{
  fullPage?: boolean,  // hele pagina of alleen viewport
  path?: string        // optioneel: pad om op te slaan
}
```

**Voorbeeld:**
- "Maak een screenshot van de homepage"
- "Maak een full-page screenshot"

### 12. `browser_extract_text`
Haal tekst van de pagina of een specifiek element.

```typescript
{
  selector?: string  // CSS selector, leeg = hele pagina
}
```

**Voorbeeld:**
- "Haal alle tekst van de pagina"
- "Lees de tekst van .welcome-message"

### 13. `browser_extract_table`
Extract gestructureerde data uit een tabel.

```typescript
{
  tableSelector: string  // CSS selector voor de tabel
}
```

**Voorbeeld:**
- "Haal de data uit de klachten tabel"
- "Extract table data from .complaints-table"

### 14. `browser_fill_form`
Vul formulier velden in.

```typescript
{
  fields: {
    "selector": "value",
    "input[name='title']": "Straatverlichting kapot",
    "#description": "Beschrijving..."
  }
}
```

**Voorbeeld:**
- "Vul het contactformulier in"
- "Vul het zoek veld in met 'afval'"

### 15. `browser_click`
Klik op een element.

```typescript
{
  selector: string,           // CSS selector
  waitForNavigation?: boolean // wacht op pagina navigatie
}
```

**Voorbeeld:**
- "Klik op de submit button"
- "Klik op het eerste resultaat"

### 16. `browser_submit_complaint`
Vul het klachten formulier in en dien het in via de UI.

```typescript
{
  title: string,
  description: string,
  category: string,
  location: string,
  name: string,
  email: string,
  phone?: string
}
```

**Voorbeeld:**
- "Dien een klacht in over kapotte straatverlichting"
- "Maak een nieuwe klacht via het formulier"

### 17. `browser_get_dashboard_stats`
Ga naar het dashboard en haal alle statistieken op.

```typescript
{
  loginFirst?: boolean  // eerst inloggen (default: true)
}
```

**Voorbeeld:**
- "Haal de dashboard statistieken op"
- "Wat zijn de huidige stats op het dashboard?"

### 18. `browser_get_page_info`
Haal algemene informatie op over de huidige pagina.

```typescript
{}  // geen parameters
```

**Voorbeeld:**
- "Wat is er op deze pagina?"
- "Geef me info over de huidige pagina"
- "Welke links staan op deze pagina?"

### 19. `browser_close`
Sluit de browser.

```typescript
{}  // geen parameters
```

**Voorbeeld:**
- "Sluit de browser"
- "Browser sluiten"

## 🔧 Installation

```bash
cd mcp-server
npm install
npx playwright install chromium  # Installeer browser voor automation
npm run build
```

## ⚙️ Configuration

Maak een `.env` bestand aan:

```env
APP_URL=http://gemeente.test
API_BASE_URL=http://gemeente.test/api
ADMIN_EMAIL=admin@gemeente.nl
ADMIN_PASSWORD=admin123
```

## 🎯 Usage

### In Claude Desktop

Voeg toe aan je Claude Desktop config (`~/Library/Application Support/Claude/claude_desktop_config.json`):

```json
{
  "mcpServers": {
    "gemeente-portal": {
      "command": "node",
      "args": ["/Users/abdisamadabdulle/Herd/Gemeente/mcp-server/dist/index.js"],
      "env": {
        "APP_URL": "http://gemeente.test",
        "API_BASE_URL": "http://gemeente.test/api"
      }
    }
  }
}
```

### Direct Uitvoeren

```bash
npm start
```

## 📝 Examples

### API Examples

**Vraag naar Claude:**
- "Haal alle open klachten op uit de gemeente portal"
- "Maak een nieuwe klacht aan over kapotte straatverlichting"
- "Geef me statistieken van afgelopen maand"
- "Zoek naar klachten over afval in Amsterdam"
- "Laat de kaart met alle urgente klachten zien"

### Browser Automation Examples

**Website Taken:**
- "Ga naar de homepage en maak een screenshot"
- "Log in op het dashboard en haal de statistieken op"
- "Open de klachten pagina en lees alle titels"
- "Vul het contactformulier in met mijn gegevens"

**Complex Workflows:**
- "Log in, ga naar dashboard, extract de tabel data, en maak een screenshot"
- "Dien een klacht in via het formulier over afval ophaling"
- "Navigeer door alle pagina's en verzamel alle links"
- "Test het klachten formulier door het in te vullen en te submitten"

**Data Extractie:**
- "Haal alle klachten van de tabel op de dashboard pagina"
- "Extract de statistieken van het dashboard"
- "Lees alle tekst van de over ons pagina"
- "Geef me alle links op de homepage"

## 🔒 Security

De MCP server communiceert met de Laravel API. Zorg ervoor dat:
- De Laravel API endpoints beschikbaar zijn
- CORS correct is geconfigureerd
- API authenticatie optioneel is of tokens worden gebruikt

## 🛠️ Development

```bash
# Watch mode
npm run dev

# Build
npm run build

# Start
npm start
```

## 📂 File Structure

```
mcp-server/
├── src/
│   └── index.ts          # Main server implementation
├── dist/                 # Compiled JavaScript
├── package.json
├── tsconfig.json
├── .env
└── README.md
```

## 🤝 Integration met Laravel

### API Endpoints
De MCP server verwacht de volgende Laravel API endpoints:

- `GET /api/complaints` - Lijst van klachten
- `GET /api/complaints/{id}` - Specifieke klacht
- `POST /api/complaints` - Nieuwe klacht
- `PATCH /api/complaints/{id}/status` - Update status
- `POST /api/complaints/{id}/notes` - Voeg notitie toe
- `GET /api/statistics` - Statistieken
- `GET /api/complaints/search` - Zoeken
- `GET /api/complaints/map` - Kaart data

### Web Routes (voor Browser Automation)
De browser automation tools verwachten deze pagina's:

- `GET /` - Homepage
- `GET /login` - Login pagina
- `POST /login` - Login form handler
- `GET /dashboard` - Admin dashboard
- `GET /klachten` - Klachten overzicht
- `GET /klachten/nieuw` - Nieuwe klacht formulier
- `POST /klachten` - Klacht form handler
- `GET /contact` - Contact pagina

### Formulier Selectors
Browser automation gebruikt deze CSS selectors (pas aan indien nodig):

```css
/* Login Form */
input[name="email"]
input[name="password"]
button[type="submit"]

/* Klachten Form */
input[name="title"]
textarea[name="description"]
select[name="category"]
input[name="location"]
input[name="reporter_name"]
input[name="reporter_email"]
input[name="reporter_phone"]

/* Dashboard */
.statistics, .stats-grid
.complaints-table
.success-message, .alert
```

## 📊 Response Formats

Alle responses zijn in JSON format en bevatten:

```json
{
  "content": [
    {
      "type": "text",
      "text": "..."
    }
  ],
  "isError": false
}
```

## ⚡ Performance

- Timeout: 10 seconden
- Automatic retry voor failed requests
- Caching mogelijk via Laravel

## 🐛 Troubleshooting

**Server start niet:**
```bash
# Check Node version (vereist Node 18+)
node --version

# Rebuild
npm run build
```

**API errors:**
```bash
# Check of Laravel draait
curl http://gemeente.test/api/complaints

# Check .env configuratie
cat .env
```

**Claude Desktop verbinding:**
```bash
# Check logs
tail -f ~/Library/Logs/Claude/mcp*.log
```

## 📜 License

MIT

## 👨‍💻 Author

Gemeente Portal Team
