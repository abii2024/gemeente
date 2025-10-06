# Browser Automation Voorbeelden

Deze guide laat zien hoe je de Gemeente Portal MCP Server kunt gebruiken om taken uit te voeren op de website via browser automation.

## 🎯 Basis Navigatie

### Voorbeeld 1: Homepage Bezoeken en Screenshot Maken

**Vraag aan Claude/Copilot:**
```
Ga naar de homepage van gemeente.test en maak een screenshot
```

**Wat gebeurt er:**
1. Browser opent gemeente.test
2. Wacht tot pagina volledig geladen is
3. Maakt een screenshot
4. Toont screenshot informatie

### Voorbeeld 2: Pagina Tekst Lezen

**Vraag:**
```
Ga naar de over ons pagina en lees alle tekst
```

**Wat gebeurt er:**
1. Navigeert naar /over-ons
2. Extraheert alle tekst van de pagina
3. Toont de content

## 🔐 Inloggen en Dashboard

### Voorbeeld 3: Inloggen en Dashboard Bekijken

**Vraag:**
```
Log in als admin en haal de dashboard statistieken op
```

**Wat gebeurt er:**
1. Navigeert naar /login
2. Vult email en password in (uit .env)
3. Klikt op login button
4. Navigeert naar /dashboard
5. Extraheert statistieken
6. Toont resultaten

### Voorbeeld 4: Dashboard Tabel Data Extracten

**Vraag:**
```
Log in en geef me alle klachten uit de dashboard tabel
```

**Wat gebeurt er:**
1. Logt in als admin
2. Gaat naar dashboard
3. Vindt de klachten tabel (.complaints-table)
4. Extraheert alle rijen en kolommen
5. Returned gestructureerde JSON data

## 📝 Formulieren Invullen

### Voorbeeld 5: Simpel Formulier Invullen

**Vraag:**
```
Ga naar de contact pagina en vul het formulier in met:
- Naam: Jan Jansen
- Email: jan@example.com
- Bericht: Graag contact opnemen
```

**Wat gebeurt er:**
1. Navigeert naar /contact
2. Vult de velden in:
   - `input[name="name"]` = "Jan Jansen"
   - `input[name="email"]` = "jan@example.com"
   - `textarea[name="message"]` = "Graag contact opnemen"
3. Klaar voor submit (je kunt daarna browser_click gebruiken)

### Voorbeeld 6: Klacht Indienen via UI

**Vraag:**
```
Dien een klacht in over kapotte straatverlichting op de Hoofdstraat via het formulier
```

**Wat gebeurt er:**
1. Navigeert naar /klachten/nieuw
2. Vult alle velden in:
   - Titel: "Kapotte straatverlichting"
   - Beschrijving: "Straatverlichting op Hoofdstraat is defect"
   - Categorie: "infrastructuur"
   - Locatie: "Hoofdstraat"
   - Naam: (uit .env of meegegeven)
   - Email: (uit .env of meegegeven)
3. Klikt op submit button
4. Wacht op navigatie naar success pagina
5. Toont bevestigingsmelding

## 🔍 Data Extractie

### Voorbeeld 7: Alle Links Verzamelen

**Vraag:**
```
Ga naar de homepage en geef me alle links
```

**Wat gebeurt er:**
1. Navigeert naar homepage
2. Vindt alle `<a>` tags
3. Extraheert text en href
4. Toont lijst met alle links

### Voorbeeld 8: Specifieke Element Tekst

**Vraag:**
```
Ga naar de homepage en lees de tekst van het welkom bericht
```

**Wat gebeurt er:**
1. Navigeert naar homepage
2. Zoekt element met selector (bijv. `.welcome-message` of `#hero-title`)
3. Extraheert alleen de tekst van dat element
4. Toont resultaat

## 🎨 Screenshots en Visuele Verificatie

### Voorbeeld 9: Full Page Screenshot

**Vraag:**
```
Maak een full-page screenshot van de klachten pagina en sla het op als klachten.png
```

**Wat gebeurt er:**
1. Navigeert naar /klachten
2. Scrollt door hele pagina
3. Maakt screenshot van begin tot eind
4. Slaat op in klachten.png
5. Toont bestandsgrootte en locatie

### Voorbeeld 10: Viewport Screenshot

**Vraag:**
```
Maak een screenshot van alleen het zichtbare deel van het dashboard
```

**Wat gebeurt er:**
1. Gebruikt huidige pagina (moet al op dashboard zijn)
2. Maakt screenshot van alleen viewport (1920x1080)
3. Returned base64 encoded image

## 🔄 Complete Workflows

### Voorbeeld 11: End-to-End Klacht Test

**Vraag:**
```
Test het complete klachten proces:
1. Ga naar de homepage
2. Klik op "Klacht indienen"
3. Vul het formulier in met test data
4. Submit het formulier
5. Verifieer de success message
6. Maak een screenshot
```

**Wat gebeurt er:**
Sequentie van commando's:
1. `browser_goto` → /
2. `browser_click` → .klacht-button
3. `browser_fill_form` → vul alle velden
4. `browser_click` → button[type="submit"]
5. `browser_extract_text` → .success-message
6. `browser_screenshot` → bewijs
7. Toont alle resultaten

### Voorbeeld 12: Dashboard Monitoring

**Vraag:**
```
Log in, ga naar dashboard, haal statistieken op, extract tabel data, en maak een screenshot voor rapportage
```

**Wat gebeurt er:**
1. `browser_login` → inloggen
2. `browser_goto` → /dashboard
3. `browser_extract_text` → .statistics
4. `browser_extract_table` → .complaints-table
5. `browser_screenshot` → dashboard.png
6. Combineert alle data in rapport

### Voorbeeld 13: Multi-Page Data Scraping

**Vraag:**
```
Verzamel alle klachten van pagina 1 tot 3:
1. Ga naar klachten pagina
2. Extract tabel data
3. Klik op "Volgende"
4. Herhaal voor elke pagina
```

**Wat gebeurt er:**
Loop van commando's:
1. `browser_goto` → /klachten
2. `browser_extract_table` → .complaints-table
3. `browser_click` → .pagination .next
4. Repeat stap 2-3
5. Combineert alle data

## 🛠️ Geavanceerde Technieken

### Voorbeeld 14: Conditional Actions

**Vraag:**
```
Ga naar de klachten pagina, als er een filters sectie is, filter op status "open", anders haal gewoon alle data op
```

**Wat gebeurt er:**
1. `browser_goto` → /klachten
2. Check if element exists met page inspection
3. Conditional:
   - Als filters exist: `browser_click` + `browser_select`
   - Anders: direct `browser_extract_table`

### Voorbeeld 15: Form Validation Test

**Vraag:**
```
Test het klachten formulier door het in te dienen zonder verplichte velden en verifieer de foutmeldingen
```

**Wat gebeurt er:**
1. `browser_goto` → /klachten/nieuw
2. `browser_click` → submit (zonder velden in te vullen)
3. `browser_extract_text` → .error-messages
4. Verifieert dat errors worden getoond
5. Screenshot van errors

### Voorbeeld 16: Authentication Flow Test

**Vraag:**
```
Test de login functionaliteit:
1. Probeer in te loggen met foute credentials
2. Verifieer error message
3. Log in met correcte credentials
4. Verifieer redirect naar dashboard
```

**Wat gebeurt er:**
1. `browser_goto` → /login
2. `browser_fill_form` → foute credentials
3. `browser_click` → submit
4. `browser_extract_text` → .error
5. `browser_fill_form` → correcte credentials
6. `browser_click` → submit
7. Verify URL = /dashboard
8. Screenshot van resultaat

## 💡 Tips en Best Practices

### Tip 1: Gebruik Selectors Consistent
```
// Goed
browser_extract_text("selector": ".welcome-message")

// Vermijd
browser_extract_text("selector": "div > div > p")  // Te specifiek, breekt makkelijk
```

### Tip 2: Wacht op Elements
```
// Gebruik waitForNavigation bij clicks die navigeren
browser_click("selector": "a.link", "waitForNavigation": true)

// Gebruik networkidle voor dynamische content
browser_goto("url": "/klachten", "waitUntil": "networkidle")
```

### Tip 3: Sluit Browser
```
// Aan het einde van je workflow
browser_close()
```

### Tip 4: Screenshots voor Debugging
```
// Bij elke belangrijke stap
Maak een screenshot om te verifiëren dat de pagina correct is
```

### Tip 5: Extract vs API
```
// Browser: Als je UI wilt testen of geen API endpoint bestaat
browser_extract_table(".complaints-table")

// API: Als je alleen data nodig hebt en performance belangrijk is
get_complaints({"status": "open"})
```

## 🚨 Error Handling

### Element Niet Gevonden
Als een selector niet bestaat:
```
Try: browser_get_page_info()
Dit toont alle beschikbare links en structuur
```

### Login Faalt
```
Verifieer:
1. Is de website bereikbaar? (browser_goto("/"))
2. Zijn credentials correct? (check .env)
3. Is CSRF protection actief? (Laravel configuratie)
```

### Timeout Errors
```
Verhoog timeout in browser-automation.ts:
- goto: 30000ms default
- waitForSelector: 30000ms default
```

## 🎓 Leerpad

1. **Beginner**: Start met simpele navigatie en screenshots
2. **Intermediate**: Formulieren invullen en data extracten
3. **Advanced**: Complete workflows en conditional logic
4. **Expert**: Multi-page scraping en automated testing

## 📞 Support

Bij problemen:
1. Check console output voor errors
2. Gebruik `browser_screenshot` voor visuele debugging
3. Gebruik `browser_get_page_info` om pagina structuur te begrijpen
4. Check Laravel logs voor backend errors

Happy automating! 🎉
