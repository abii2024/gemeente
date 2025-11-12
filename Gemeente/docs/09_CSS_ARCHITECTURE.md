# CSS Architecture Documentation

**Gemeente Portal - Modern CSS Design System (2025)**

Versie: 1.0  
Datum: Januari 2025  
Auteur: Gemeente Development Team

---

## 📋 Inhoudsopgave

1. [Overzicht](#overzicht)
2. [Architectuur & Cascade Layers](#architectuur--cascade-layers)
3. [Design Tokens](#design-tokens)
4. [Component Library](#component-library)
5. [Layout System](#layout-system)
6. [Animaties & Transities](#animaties--transities)
7. [Dark Mode](#dark-mode)
8. [Accessibility](#accessibility)
9. [Browser Support](#browser-support)
10. [Migratie Guide](#migratie-guide)
11. [Best Practices](#best-practices)

---

## 🎯 Overzicht

Het Gemeente Portal gebruikt een moderne CSS architectuur gebaseerd op **2025 best practices**:

- **OKLCH kleuren** voor betere perceptuele uniformiteit
- **Fluid typography** met `clamp()` voor responsieve text
- **Container queries** voor component-level responsiveness
- **Cascade layers** voor voorspelbare specificity
- **View Transitions API** voor smooth page transitions
- **Automatische dark mode** met `prefers-color-scheme`
- **Accessibility-first** met WCAG 2.1 Level AA compliance

### Voordelen van deze aanpak

✅ **Maintainable**: Single source of truth via design tokens  
✅ **Performant**: Optimized cascade, geen onnodige specificity  
✅ **Accessible**: Built-in WCAG compliance  
✅ **Modern**: Gebruikt nieuwste CSS features  
✅ **Future-proof**: Compatible met Tailwind v4 benadering  
✅ **Consistent**: Design tokens zorgen voor uniformiteit

---

## 🏗️ Architectuur & Cascade Layers

### Bestandsstructuur

```
resources/css/
├── app.css                 # Main entry point
├── design-tokens.css       # Kleuren, typography, spacing
├── modern-components.css   # Reusable UI components
├── layout-system.css       # Grid, Flex, containers
├── animations.css          # Transitions & view transitions
└── dark-mode.css          # Dark mode specifieke styling
```

### Cascade Layer Volgorde

```css
@layer reset, tokens, base, components, utilities, overrides;
```

**Waarom layers?**

- Expliciete controle over specificity
- Utilities kunnen altijd components overriden
- Geen `!important` nodig
- Voorspelbaar gedrag

**Layer uitleg:**

1. **reset**: CSS normalization
2. **tokens**: Design variabelen (colors, spacing, etc.)
3. **base**: HTML element defaults
4. **components**: `.btn`, `.card`, `.form-input`, etc.
5. **utilities**: `.flex`, `.grid`, `.p-4`, etc.
6. **overrides**: Project-specifieke overrides

---

## 🎨 Design Tokens

### Kleuren - OKLCH Color Space

**Waarom OKLCH?**

- Perceptueel uniform (gelijke L values = gelijke helderheid)
- Betere dark mode (consistente contrast ratios)
- Bredere gamut support
- Toekomstbestendig

**Format:**

```css
oklch(Lightness% Chroma Hue)
```

**Voorbeeld:**

```css
--color-brand-primary: oklch(60% 0.15 260); /* Blauw */
--color-success: oklch(65% 0.18 150);       /* Groen */
--color-error: oklch(60% 0.20 25);          /* Rood */
```

### Beschikbare Color Tokens

#### Neutrals

```css
--color-bg-primary      /* Hoofd achtergrond */
--color-bg-secondary    /* Secundaire achtergrond */
--color-bg-tertiary     /* Cards, inputs */

--color-fg-primary      /* Hoofdtekst */
--color-fg-secondary    /* Subtekst */
--color-fg-tertiary     /* Disabled text */
```

#### Brand Colors

```css
--color-brand-primary   /* Hoofd brand kleur */
--color-brand-hover     /* Hover state */
--color-brand-active    /* Active state */
--color-brand-contrast  /* Tekst op brand kleur */
--color-brand-light     /* Lichte backgrounds */
--color-brand-lighter   /* Subtiele highlights */
```

#### Semantic Colors

```css
--color-success         /* Succes acties */
--color-warning         /* Waarschuwingen */
--color-error          /* Fouten */
--color-info           /* Informatief */
```

Elke semantic kleur heeft ook `-contrast` en `-light` varianten.

### Typography - Fluid Scales

**Waarom fluid typography?**

- Geen media queries nodig
- Smooth scaling tussen viewports
- Betere leesbaarheid op alle schermen

**Format:**

```css
clamp(MIN, PREFERRED, MAX)
```

**Beschikbare sizes:**

```css
--font-size-xs    /* 12-13px */
--font-size-sm    /* 14-15px */
--font-size-base  /* 16-17px */
--font-size-lg    /* 18-20px */
--font-size-xl    /* 20-24px */
--font-size-2xl   /* 24-30px */
--font-size-3xl   /* 30-36px */
--font-size-4xl   /* 36-48px */
--font-size-5xl   /* 48-60px */
```

**Gebruik:**

```css
h1 {
  font-size: var(--font-size-4xl);
}

.subtitle {
  font-size: var(--font-size-lg);
}
```

### Spacing - 8px Base Grid

**Scale:**

```css
--space-0: 0;
--space-1: 0.25rem;   /* 4px */
--space-2: 0.5rem;    /* 8px */
--space-3: 0.75rem;   /* 12px */
--space-4: 1rem;      /* 16px */
--space-6: 1.5rem;    /* 24px */
--space-8: 2rem;      /* 32px */
--space-12: 3rem;     /* 48px */
--space-16: 4rem;     /* 64px */
/* etc. */
```

**Gebruik:**

```css
.card {
  padding: var(--space-6);
  margin-bottom: var(--space-4);
}
```

### Border Radius

```css
--radius-sm: 0.25rem;    /* 4px */
--radius-md: 0.5rem;     /* 8px */
--radius-lg: 0.75rem;    /* 12px */
--radius-xl: 1rem;       /* 16px */
--radius-2xl: 1.25rem;   /* 20px */
--radius-full: 9999px;   /* Volledig rond */
```

### Shadows

```css
--shadow-sm    /* Subtiele elevatie */
--shadow-md    /* Standaard elevatie */
--shadow-lg    /* Duidelijke elevatie */
--shadow-xl    /* Sterke elevatie */
--shadow-2xl   /* Maximale elevatie */
```

---

## 🧩 Component Library

Alle components gebruiken **low-specificity selectors** (`:where()`) waardoor ze makkelijk te overriden zijn.

### Buttons

#### Varianten

```html
<!-- Primary action -->
<button class="btn btn-primary">Opslaan</button>

<!-- Secondary action -->
<button class="btn btn-secondary">Annuleren</button>

<!-- Outline style -->
<button class="btn btn-outline">Details</button>

<!-- Ghost/minimal -->
<button class="btn btn-ghost">Sluiten</button>

<!-- Semantic -->
<button class="btn btn-success">Goedkeuren</button>
<button class="btn btn-warning">Waarschuwing</button>
<button class="btn btn-error">Verwijderen</button>
```

#### Sizes

```html
<button class="btn btn-primary btn-sm">Klein</button>
<button class="btn btn-primary">Normaal</button>
<button class="btn btn-primary btn-lg">Groot</button>
<button class="btn btn-primary btn-xl">Extra groot</button>
```

#### Modifiers

```html
<!-- Full width -->
<button class="btn btn-primary btn-block">Volledige breedte</button>

<!-- Icon only -->
<button class="btn btn-icon">
  <svg>...</svg>
</button>

<!-- Disabled -->
<button class="btn btn-primary" disabled>Uitgeschakeld</button>
```

### Cards

```html
<!-- Basic card -->
<div class="card">
  <div class="card-header">
    <h3>Card Titel</h3>
  </div>
  <div class="card-body">
    <p>Card content hier...</p>
  </div>
  <div class="card-footer">
    <button class="btn btn-primary">Actie</button>
  </div>
</div>

<!-- Interactive card (clickable) -->
<a href="#" class="card card-interactive">
  <div class="card-body">
    <h3>Klikbare Card</h3>
    <p>Hele card is klikbaar</p>
  </div>
</a>

<!-- Gradient card -->
<div class="card card-gradient">
  <div class="card-body">
    <p>Card met gradient achtergrond</p>
  </div>
</div>
```

**Container Queries:**

Cards passen automatisch hun padding aan op basis van container breedte:

```css
@container card (max-width: 400px) {
  /* Compacte padding voor smalle containers */
}
```

### Forms

#### Input Fields

```html
<div class="form-group">
  <label for="email" class="form-label">E-mail *</label>
  <input 
    type="email" 
    id="email" 
    class="form-input"
    placeholder="naam@voorbeeld.nl"
  >
  <p class="form-help">We delen uw e-mail niet met anderen</p>
</div>

<!-- Met error -->
<div class="form-group">
  <label class="form-label">Naam</label>
  <input type="text" class="form-input form-input-error">
  <p class="form-error">Dit veld is verplicht</p>
</div>

<!-- Met success -->
<input type="text" class="form-input form-input-success">
```

#### Select Dropdown

```html
<select class="form-select">
  <option>Selecteer een optie</option>
  <option>Optie 1</option>
  <option>Optie 2</option>
</select>
```

#### Textarea

```html
<textarea class="form-textarea" rows="4"></textarea>
```

#### Checkbox & Radio

```html
<input type="checkbox" class="form-checkbox">
<input type="radio" class="form-radio">
```

#### File Upload

```html
<input type="file" class="form-file">
```

### Badges

```html
<span class="badge badge-primary">Nieuw</span>
<span class="badge badge-success">Actief</span>
<span class="badge badge-warning">Wachtend</span>
<span class="badge badge-error">Gesloten</span>
<span class="badge badge-info">Info</span>
```

### Alerts

```html
<div class="alert alert-info">
  <p>Dit is een informatieve melding</p>
</div>

<div class="alert alert-success">
  <p>Actie succesvol uitgevoerd!</p>
</div>

<div class="alert alert-warning">
  <p>Let op: dit vraagt om aandacht</p>
</div>

<div class="alert alert-error">
  <p>Er is een fout opgetreden</p>
</div>
```

### Navigation

```html
<nav class="nav">
  <a href="#" class="nav-link nav-link-active">Dashboard</a>
  <a href="#" class="nav-link">Meldingen</a>
  <a href="#" class="nav-link">Instellingen</a>
</nav>
```

### Modal

```html
<div class="modal-backdrop"></div>
<div class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Modal Titel</h3>
      <button>×</button>
    </div>
    <div class="modal-body">
      <p>Modal content...</p>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary">Annuleren</button>
      <button class="btn btn-primary">Bevestigen</button>
    </div>
  </div>
</div>
```

### Loading States

```html
<!-- Spinner -->
<div class="spinner"></div>

<!-- Skeleton loader -->
<div class="skeleton" style="height: 20px; width: 200px;"></div>

<!-- Dots loader -->
<div class="dots-loader">
  <span></span>
  <span></span>
  <span></span>
</div>
```

---

## 📐 Layout System

### Container

```html
<div class="container">
  <!-- Content met max-width en auto margin -->
</div>

<!-- Size variants -->
<div class="container-sm">  <!-- 640px max -->
<div class="container-md">  <!-- 768px max -->
<div class="container-lg">  <!-- 1024px max -->
<div class="container-xl">  <!-- 1280px max -->
<div class="container-2xl"> <!-- 1536px max -->
```

### CSS Grid

```html
<!-- Auto-fit: Responsive zonder media queries -->
<div class="grid grid-auto-fit">
  <div>Item 1</div>
  <div>Item 2</div>
  <div>Item 3</div>
</div>

<!-- Explicit columns -->
<div class="grid grid-cols-3 gap-6">
  <div>Col 1</div>
  <div>Col 2</div>
  <div>Col 3</div>
</div>

<!-- Container query responsive -->
<div class="container grid grid-cols-1 grid-cols-md-2 grid-cols-lg-3">
  <!-- Automatisch responsive op basis van container breedte -->
</div>
```

### Flexbox

```html
<div class="flex items-center justify-between">
  <div>Links</div>
  <div>Rechts</div>
</div>

<div class="flex flex-col gap-4">
  <div>Item 1</div>
  <div>Item 2</div>
</div>
```

### Stack Layout

```html
<div class="stack-4">
  <!-- Automatische vertical spacing tussen children -->
  <p>Paragraaf 1</p>
  <p>Paragraaf 2</p>
</div>
```

### Sidebar Layout

```html
<div class="sidebar-layout">
  <aside>Sidebar</aside>
  <main>Main content</main>
</div>
<!-- Automatisch responsive met container queries -->
```

---

## 🎬 Animaties & Transities

### View Transitions

**Automatische page transitions:**

```css
@view-transition {
  navigation: auto;
}
```

Dit werkt automatisch bij navigatie tussen pagina's (Chrome 111+).

### Scroll-Driven Animations

```html
<div class="fade-in-on-scroll">
  <!-- Fade in bij scrollen -->
</div>

<div class="slide-up-on-scroll">
  <!-- Slide up bij scrollen -->
</div>

<div class="stagger-on-scroll">
  <!-- Children staggered animation -->
  <div>Item 1</div>
  <div>Item 2</div>
  <div>Item 3</div>
</div>
```

### Hover Animaties

```html
<div class="hover-lift">Lift on hover</div>
<div class="hover-scale">Scale on hover</div>
<div class="hover-glow">Glow on hover</div>
```

### State Animaties

```html
<div class="shake">Error shake</div>
<div class="bounce">Success bounce</div>
<div class="pulse">Attention pulse</div>
```

### Entrance Animaties

```html
<div class="fade-in">Fade in</div>
<div class="fade-in-up">Fade in from below</div>
<div class="fade-in-down">Fade in from above</div>
<div class="zoom-in">Zoom in</div>
```

---

## 🌙 Dark Mode

### Automatische Dark Mode

Dark mode wordt automatisch geactiveerd op basis van OS voorkeur:

```css
@media (prefers-color-scheme: dark) {
  /* Dark mode kleuren */
}
```

### Handmatige Toggle

Als je een dark mode toggle wilt toevoegen:

```html
<html data-theme="dark">
  <!-- Dark mode geforceerd -->
</html>

<html data-theme="light">
  <!-- Light mode geforceerd -->
</html>
```

### Dark Mode Features

- Automatische kleur inversie
- Image brightness aanpassing
- Custom scrollbar styling
- Map filters voor Leaflet
- Selection colors
- Focus states met hogere visibility

---

## ♿ Accessibility

### WCAG 2.1 Level AA Compliance

✅ **Contrast ratios**: Minimaal 4.5:1 voor normale tekst  
✅ **Focus states**: Duidelijke 2px outlines  
✅ **Keyboard navigation**: Alle interactieve elementen  
✅ **Screen reader support**: Semantische HTML  
✅ **Motion preferences**: `prefers-reduced-motion` support  
✅ **High contrast mode**: `forced-colors` support

### Focus States

Alle interactieve elementen hebben WCAG-compliant focus states:

```css
:focus-visible {
  outline: 2px solid var(--color-border-focus);
  outline-offset: 2px;
}
```

### Reduced Motion

Gebruikers met motion sensitivity krijgen geen animaties:

```css
@media (prefers-reduced-motion: reduce) {
  * {
    animation-duration: 0.01ms !important;
    transition-duration: 0.01ms !important;
  }
}
```

### Screen Reader Only

```html
<span class="sr-only">Alleen voor screen readers</span>
```

---

## 🌐 Browser Support

### Minimum Versies

| Browser | Versie | Support |
|---------|--------|---------|
| Chrome | 111+ | ✅ Volledig |
| Edge | 111+ | ✅ Volledig |
| Firefox | 113+ | ✅ Volledig |
| Safari | 16.4+ | ✅ Volledig |

### Feature Support

| Feature | Support | Fallback |
|---------|---------|----------|
| OKLCH colors | Chrome 111+ | rgb() fallback |
| Container queries | Baseline 2023 | Media queries fallback |
| View Transitions | Chrome 111+ | Instant transitions |
| Scroll-driven animations | Chrome 115+ | Static position |
| Cascade layers | Baseline 2022 | Normal cascade |

### Progressive Enhancement

Alle features zijn **progressively enhanced**:

- Oudere browsers krijgen functionele styling
- Moderne browsers krijgen enhanced experience
- Geen breaking changes

---

## 🔄 Migratie Guide

### Van Inline Tailwind naar Component Classes

**Voor:**

```html
<button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg">
  Klikken
</button>
```

**Na:**

```html
<button class="btn btn-primary btn-lg w-full">
  Klikken
</button>
```

### Input Fields

**Voor:**

```html
<input 
  type="text" 
  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2"
>
```

**Na:**

```html
<input type="text" class="form-input">
```

### Stap-voor-stap Migratie

1. **Identificeer herhalende patterns**
   - Zoek naar buttons, inputs, cards die vaker voorkomen
   
2. **Vervang met component classes**
   ```bash
   # Voorbeeld: Vervang alle button classes
   sed -i '' 's/class="w-full bg-blue-600.../class="btn btn-primary"/g' *.blade.php
   ```

3. **Test functionaliteit**
   - Controleer of styling gelijk blijft
   - Test hover states, focus states
   
4. **Valideer accessibility**
   - Run Lighthouse audit
   - Test keyboard navigation

---

## 📚 Best Practices

### 1. Gebruik Design Tokens

❌ **Slecht:**

```css
.my-component {
  color: #3b82f6;
  padding: 16px;
}
```

✅ **Goed:**

```css
.my-component {
  color: var(--color-brand-primary);
  padding: var(--space-4);
}
```

### 2. Gebruik Semantic Classes

❌ **Slecht:**

```html
<div class="bg-red-500 text-white p-4 rounded">Fout!</div>
```

✅ **Goed:**

```html
<div class="alert alert-error">Fout!</div>
```

### 3. Container Queries boven Media Queries

❌ **Slecht:**

```css
@media (min-width: 768px) {
  .sidebar { display: block; }
}
```

✅ **Goed:**

```css
@container (min-width: 768px) {
  .sidebar { display: block; }
}
```

### 4. Low-Specificity Selectors

❌ **Slecht:**

```css
div.card > div.card-body > p {
  color: red;
}
```

✅ **Goed:**

```css
:where(.card-body) p {
  color: var(--color-fg-primary);
}
```

### 5. Accessibility First

Altijd checken:

- [ ] Contrast ratio ≥ 4.5:1
- [ ] Focus states zichtbaar
- [ ] Keyboard navigatie werkt
- [ ] Screen reader labels correct
- [ ] Motion preferences gerespecteerd

---

## 🛠️ Troubleshooting

### CSS Build Errors

**Probleem:** `@tailwind` unknown at-rule warning

**Oplossing:** Dit is normaal - CSS validators kennen Tailwind directives niet maar het werkt wel.

### Dark Mode werkt niet

**Check:**

1. Is `color-scheme: light dark;` in `:root`?
2. Zijn tokens in `@media (prefers-color-scheme: dark)` correct?
3. Browser DevTools > Rendering > Emulate prefers-color-scheme

### Container Queries werken niet

**Check:**

1. Browser versie (minimum Chrome 105+)
2. `container-type: inline-size` op parent element
3. `@container` query syntax correct

---

## 📞 Support & Resources

### Interne Docs

- [Project Overview](./01_PROJECT_OVERVIEW.md)
- [Backend Laravel](./02_BACKEND_LARAVEL.md)
- [Frontend Docs](./04_FRONTEND.md)

### Externe Resources

- [Josh Comeau - CSS Blog](https://www.joshwcomeau.com/)
- [State of CSS 2025](https://2025.stateofcss.com/)
- [Tailwind CSS v4 Blog](https://tailwindcss.com/blog)
- [MDN Web Docs](https://developer.mozilla.org/)

### Browser Testing

- [Can I Use](https://caniuse.com/) - Feature support
- [Baseline](https://web.dev/baseline/2024) - Browser compatibility
- [Chrome DevTools](https://developer.chrome.com/docs/devtools/)

---

## 📝 Changelog

### Version 1.0.0 (Januari 2025)

- Initial release met complete CSS modernization
- OKLCH color system
- Fluid typography
- Container queries
- View Transitions API
- Complete component library
- Dark mode support
- WCAG 2.1 AA compliance

---

**Vragen? Problemen?** Maak een issue aan in het project repository of neem contact op met het development team.
