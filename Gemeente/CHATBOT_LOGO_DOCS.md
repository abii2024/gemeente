# Gemeente Chatbot Logo Documentatie 🎨

## Overzicht
Custom ontworpen logo's voor de gemeente chatbot met Nederlandse symboliek en moderne uitstraling.

## Logo Bestanden

### 1. `/public/images/chatbot-logo.svg` (64x64px)
- **Gebruik**: Standaard logo voor medium weergaves
- **Kenmerken**: 
  - Gemeente gebouw met Nederlandse vlag
  - Chat bubble met typing dots
  - Blauwe gradient achtergrond
  - Nederlandse rood-wit-blauw kleurenschema

### 2. `/public/images/chatbot-logo-small.svg` (48x48px)  
- **Gebruik**: Chat button en header avatar
- **Kenmerken**:
  - Vereenvoudigde versie voor kleine weergaves
  - Compacte gemeente gebouw representatie
  - Duidelijke chat bubble
  - Optimaal voor 24px weergave

### 3. `/public/images/chatbot-logo-large.svg` (128x128px)
- **Gebruik**: Hero sections, over pagina's, documentatie
- **Kenmerken**:
  - Uitgebreide details en schaduwen
  - Gradient effecten
  - Sparkle accenten op chat bubble
  - Premium uitstraling voor prominente plaatsing

## Design Elementen

### Kleurenschema
```css
Primair Blauw: #1e40af (Nederland thema)
Secundair Blauw: #3b82f6 (Moderne accent)
Nederlandse Rood: #dc2626 (Vlag en deur)
Wit: #ffffff (Gebouw en contrast)
```

### Symboliek
- **🏛️ Gemeente Gebouw**: Vertegenwoordigt lokale overheid
- **🇳🇱 Nederlandse Vlag**: Nationale identiteit
- **💬 Chat Bubble**: Communicatie en hulp
- **⚫ Typing Dots**: Actieve conversatie
- **🏢 Ramen/Deur**: Toegankelijkheid en openheid

## Implementatie

### Chatbot Widget
```javascript
// Chat button logo
<img src="/images/chatbot-logo-small.svg" width="24" height="24" alt="Gemeente Chat">

// Header avatar  
<img src="/images/chatbot-logo-small.svg" width="20" height="20" alt="Gemeente">

// Bot message avatar
<img src="/images/chatbot-logo-small.svg" width="24" height="24" alt="Gemeente">
```

### CSS Styling
```css
.chatbot-logo {
    border-radius: 50%; /* Ronde weergave */
    box-shadow: 0 2px 8px rgba(30, 64, 175, 0.2); /* Subtiele schaduw */
}

.chatbot-logo:hover {
    transform: scale(1.05); /* Hover effect */
    transition: transform 0.2s ease;
}
```

## Brand Guidelines

### Do's ✅
- Gebruik originele kleuren (Nederlandse thema)
- Behoud aspect ratio bij schaling
- Voeg border-radius toe voor ronde weergave
- Gebruik juiste maat voor context (small/medium/large)

### Don'ts ❌  
- Wijzig de Nederlandse vlag kleuren niet
- Vervormt het logo niet (behoud proporties)
- Gebruik geen achtergronden die met blauw clashen
- Plaats het logo niet op te drukke achtergronden

## Technische Specificaties

### SVG Voordelen
- **Schaalbaar**: Perfect op alle schermresoluties
- **Klein bestand**: Snelle laadtijden
- **Scherp**: Crisp weergave op Retina displays
- **Aanpasbaar**: Kleuren via CSS wijzigbaar

### Browser Support
- ✅ Chrome/Safari/Firefox/Edge (moderne browsers)
- ✅ IE11+ (met fallback)
- ✅ Mobile browsers (iOS/Android)

## Gebruik Voorbeelden

### 1. Chat Button (Primair gebruik)
```html
<div class="chat-button">
    <img src="/images/chatbot-logo-small.svg" width="24" height="24" alt="Chat met gemeente">
    <span>Chat met gemeente</span>
</div>
```

### 2. Header/Avatar
```html
<div class="chat-header">
    <img src="/images/chatbot-logo-small.svg" width="20" height="20" alt="Gemeente Assistent">
    <span>Gemeente Assistent</span>
</div>
```

### 3. Marketing/Hero
```html
<div class="hero-section">
    <img src="/images/chatbot-logo-large.svg" width="128" height="128" alt="Gemeente Chatbot Service">
    <h1>24/7 Gemeente Hulp</h1>
</div>
```

## Toegankelijkheid

### Alt Teksten
- **Nederlandse tekst**: "Gemeente Chat", "Gemeente Assistent"
- **Beschrijvend**: Vermeldt functie en organisatie
- **Kort en bondig**: Makkelijk voor screenreaders

### Contrast
- **WCAG AA Compliant**: Voldoende contrast met achtergronden
- **Kleurenblind vriendelijk**: Geen afhankelijkheid van alleen kleur
- **Duidelijke vormgeving**: Herkenbaar zonder kleur

## Updates & Onderhoud

### Versie Controle
- Huidige versie: 1.0 (September 2025)
- Formaat: SVG (vector)
- Licentie: Gemeente eigendom

### Toekomstige Updates
- Animaties toevoegen (CSS/SVG)
- Seizoensgebonden varianten
- Toegankelijkheid verbeteringen
- Performance optimalisaties

De gemeente chatbot logo's zijn ontworpen voor herkenning, professionaliteit en Nederlandse identiteit! 🇳🇱