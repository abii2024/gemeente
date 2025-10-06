# 🚀 Hoe de MCP Server te Gebruiken

## ✨ Simpelste Manier: In VSCode

De MCP server is al geconfigureerd en draait automatisch in VSCode!

### Stap 1: Reload VSCode
```
⌘ + Shift + P  →  Type: "Developer: Reload Window"
```

### Stap 2: Gebruik via Copilot Chat
Open Copilot Chat en vraag:

**Voorbeelden:**
- `"Ga naar gemeente.test en maak een screenshot"`
- `"Log in op het dashboard"`
- `"Haal alle klachten op met status open"`
- `"Dien een klacht in via het formulier over straatverlichting"`

**Dat is alles! 🎉**

---

## 🔧 Handmatig Draaien (Voor Testing)

Als je de server handmatig wilt testen:

```bash
cd /Users/abdisamadabdulle/Herd/Gemeente/mcp-server

# Start de server
npm start
```

De server wacht dan op MCP commando's via stdin (JSON-RPC format).

---

## 🛠️ Development Commands

```bash
# Build na wijzigingen
npm run build

# Watch mode (auto rebuild)
npm run dev

# Start server
npm start
```

---

## 📊 Beschikbare Tools

### 🔌 API Tools (8)
1. `get_complaints` - Klachten ophalen
2. `get_complaint_by_id` - Specifieke klacht
3. `create_complaint` - Nieuwe klacht
4. `update_complaint_status` - Status wijzigen
5. `add_complaint_note` - Notitie toevoegen
6. `get_statistics` - Statistieken
7. `search_complaints` - Zoeken
8. `get_complaint_map_data` - Kaart data

### 🌐 Browser Tools (11)
9. `browser_goto` - Navigeer naar pagina
10. `browser_login` - Automatisch inloggen
11. `browser_screenshot` - Screenshot maken
12. `browser_extract_text` - Tekst ophalen
13. `browser_extract_table` - Tabel data
14. `browser_fill_form` - Formulier invullen
15. `browser_click` - Klikken
16. `browser_submit_complaint` - Klacht indienen
17. `browser_get_dashboard_stats` - Dashboard stats
18. `browser_get_page_info` - Pagina info
19. `browser_close` - Browser sluiten

---

## 💬 Voorbeeld Vragen voor Copilot

### Basis Navigatie
```
"Ga naar de homepage"
"Open het dashboard"
"Maak een screenshot van de huidige pagina"
```

### Inloggen & Dashboard
```
"Log in als admin en haal statistieken op"
"Ga naar het dashboard en extract de tabel data"
"Toon me wat er op het dashboard staat"
```

### Formulieren & Data
```
"Dien een klacht in over kapotte straatverlichting"
"Vul het contactformulier in"
"Haal alle open klachten op"
```

### Complex Workflows
```
"Log in, ga naar dashboard, haal stats op, en maak een screenshot"
"Test het klachten formulier door het in te vullen en te submitten"
"Verzamel alle data van de klachten pagina"
```

---

## 🔍 Check of het Werkt

**Test 1: Quick Check**
```bash
cd /Users/abdisamadabdulle/Herd/Gemeente/mcp-server
echo '{"jsonrpc":"2.0","id":1,"method":"tools/list"}' | node dist/index.js 2>&1 | head -5
```

Je zou moeten zien:
```
🚀 Gemeente Portal MCP Server gestart!
{"result":{"tools":[...
```

**Test 2: In VSCode**
1. Reload Window
2. Open Copilot Chat
3. Type: "Ga naar gemeente.test"
4. Als het werkt zie je dat de MCP server de browser tool gebruikt

---

## ⚙️ Configuratie

De server gebruikt deze configuratie uit `.env`:

```env
APP_URL=http://gemeente.test
API_BASE_URL=http://gemeente.test/api
ADMIN_EMAIL=admin@gemeente.nl
ADMIN_PASSWORD=admin123
```

**VSCode configuratie** in `.vscode/settings.json`:
```json
{
  "mcp.servers": {
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

---

## 🐛 Troubleshooting

### Server start niet in VSCode?
1. Check of `dist/` folder bestaat: `ls dist/`
2. Rebuild: `npm run build`
3. Reload VSCode Window
4. Check VSCode logs: View → Output → GitHub Copilot

### "Module not found" errors?
```bash
npm install
npm run build
```

### Browser errors?
```bash
npx playwright install chromium
```

### Kan geen verbinding maken met gemeente.test?
Zorg dat Herd/Laravel Valet draait en gemeente.test bereikbaar is:
```bash
curl http://gemeente.test
```

---

## 📚 Meer Informatie

- **README.md** - Complete documentatie
- **BROWSER_AUTOMATION_EXAMPLES.md** - 16 voorbeelden
- **COMPLETED_INSTALLATION.md** - Installatie overzicht

---

## 🎯 Quick Start Checklist

- ✅ Server geïnstalleerd (npm install uitgevoerd)
- ✅ Playwright browser geïnstalleerd
- ✅ TypeScript gecompileerd (dist/ bestaat)
- ✅ VSCode configuratie klaar (.vscode/settings.json)
- ✅ Laravel draait op gemeente.test

**Nu kun je:**
1. Reload VSCode Window (`⌘ + Shift + P`)
2. Open Copilot Chat
3. Vraag: **"Ga naar gemeente.test en laat me zien wat er staat"**

Geniet van je geautomatiseerde Gemeente Portal! 🚀
