# Beheerdersdashboard Documentatie

## Overzicht

Het Beheerdersdashboard biedt een centraal punt voor het beheren van alle klachten met geavanceerde functionaliteiten zoals interactieve kaartweergave, zoeken, en filteren.

## Functionaliteiten

### 1. Statistieken Dashboard
- **Totaal Klachten**: Overzicht van alle klachten in het systeem
- **Open Klachten**: Aantal klachten die nog niet behandeld zijn
- **In Behandeling**: Klachten die momenteel worden verwerkt
- **Opgelost**: Aantal afgehandelde klachten

### 2. Interactieve Kaart (OpenStreetMap)
De kaart toont alle klachten met GPS-coördinaten als gekleurde pins:

#### Pin Kleuren
- 🔵 **Blauw**: Open klachten
- 🟡 **Geel**: In behandeling
- 🟢 **Groen**: Opgelost
- ⚫ **Grijs**: Gesloten

#### Pin Interactie
Wanneer een beheerder op een pin klikt, verschijnt een popup met:
- Klacht ID en titel
- Korte beschrijving (max 100 karakters)
- Status en prioriteit badges
- Categorie en locatie informatie
- Aanmaakdatum
- "Bekijk Details" knop naar de volledige klacht

### 3. Zoekfunctionaliteit
**Zoeken op ID**: Direct zoeken naar een specifieke klacht door het ID in te voeren.

```
Gebruik: Voer klacht ID in → Klik op "Zoek" → Wordt doorgestuurd naar klacht details
```

### 4. Filter Opties
- **Status Filter**: Filter klachten op status (Open, In behandeling, Opgelost, Gesloten)
- **Prioriteit Filter**: Filter op prioriteit (Laag, Middel, Hoog, Urgent)
- **Reset Filters**: Herstel alle filters en toon alle klachten

Filters werken in realtime en updaten zowel de kaart als de tabel.

### 5. Recente Klachten Tabel
Toont de 5 meest recente klachten met:
- Klacht ID
- Titel
- Categorie
- Prioriteit (met gekleurde badge)
- Status (met gekleurde badge)
- Locatie
- Aanmaakdatum
- Actieknoppen:
  - "Bekijk" → Ga naar klacht details
  - "📍 Kaart" → Zoom naar klacht op kaart

## API Endpoints

Het dashboard gebruikt de volgende API endpoints:

### GET `/admin/api/dashboard/recent-complaints`
Haalt de 5 meest recente klachten op.

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Kapotte stoep",
      "category": "Onderhoud",
      "priority": "high",
      "status": "open",
      "location": "Dam Square, Amsterdam",
      "created_at": "2025-10-02T10:30:00.000000Z"
    }
  ]
}
```

### GET `/admin/api/dashboard/search?id={id}`
Zoekt een klacht op basis van ID.

**Parameters:**
- `id` (required): Klacht ID

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Kapotte stoep",
    "description": "Er ligt een gevaarlijke kapotte stoep...",
    "attachments": [...],
    "notes": [...]
  }
}
```

### GET `/admin/api/dashboard/map-data`
Haalt alle klachten met GPS-coördinaten op voor kaartweergave.

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Kapotte stoep",
      "description": "Er ligt een gevaarlijke kapotte...",
      "category": "Onderhoud",
      "priority": "high",
      "status": "open",
      "location": "Dam Square, Amsterdam",
      "lat": 52.3730,
      "lng": 4.8924,
      "created_at": "02-10-2025 10:30",
      "statusColor": "blue",
      "priorityColor": "orange"
    }
  ]
}
```

### GET `/admin/api/dashboard/complaint/{id}`
Haalt details van een specifieke klacht op voor popup weergave.

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Kapotte stoep",
    "description": "Volledige beschrijving...",
    "category": "Onderhoud",
    "priority": "high",
    "status": "open",
    "location": "Dam Square, Amsterdam",
    "reporter_name": "Jan Jansen",
    "attachments_count": 3,
    "created_at": "02-10-2025 10:30",
    "url": "/admin/complaints/1"
  }
}
```

### POST `/admin/api/dashboard/filter`
Filtert klachten op basis van criteria.

**Request Body:**
```json
{
  "status": "open",
  "priority": "high",
  "category": "Onderhoud",
  "date_from": "2025-10-01",
  "date_to": "2025-10-31"
}
```

**Response:**
Zelfde formaat als `/map-data` endpoint, maar gefilterd.

## Technische Implementatie

### Frontend Technologieën
- **Leaflet.js 1.9.4**: JavaScript bibliotheek voor interactieve kaarten
- **OpenStreetMap**: Open source kaart tile provider
- **Tailwind CSS**: Styling en responsive design
- **Vanilla JavaScript**: AJAX calls en DOM manipulatie

### Backend Technologieën
- **Laravel 12**: PHP framework
- **Eloquent ORM**: Database queries en relaties
- **JSON API**: RESTful API responses

### Security
- **CSRF Protection**: Alle POST requests zijn beveiligd met CSRF tokens
- **Authentication**: Alle dashboard routes vereisen authenticatie
- **Authorization**: Alleen admins hebben toegang (middleware: 'admin')
- **Input Validation**: Alle user input wordt gevalideerd

## Gebruik

### Toegang
1. Log in als beheerder
2. Navigeer naar `/admin/dashboard`

### Klacht zoeken
1. Voer klacht ID in het zoekveld in
2. Klik op "Zoek" knop
3. U wordt doorgestuurd naar de klacht details pagina

### Klachten filteren
1. Selecteer gewenste filters (status, prioriteit)
2. Filters worden automatisch toegepast
3. Kaart en tabel worden bijgewerkt
4. Klik "Reset Filters" om alle filters te verwijderen

### Kaart gebruiken
1. **Navigeren**: Sleep de kaart om te verplaatsen
2. **Zoomen**: Gebruik scroll wheel of +/- knoppen
3. **Pin klikken**: Klik op een pin voor klacht details
4. **📍 Kaart knop**: Klik in tabel om naar klacht te zoomen

### Klacht details bekijken
1. Klik op pin in kaart voor snelle preview
2. Klik "Bekijk Details" in popup voor volledige pagina
3. Of klik "Bekijk" in recente klachten tabel

## Troubleshooting

### Kaart laadt niet
- Controleer internetverbinding (OpenStreetMap vereist internet)
- Controleer browser console voor JavaScript errors
- Refresh de pagina

### Geen pins op kaart
- Controleer of klachten GPS-coördinaten hebben
- Run: `php artisan db:seed --class=ComplaintCoordinatesSeeder`

### Zoeken werkt niet
- Controleer of klacht ID bestaat
- Controleer of u bent ingelogd als admin

### Filters werken niet
- Check browser console voor errors
- Controleer CSRF token validity
- Refresh de pagina

## Database Requirements

Klachten moeten de volgende velden hebben voor volledige functionaliteit:
- `lat` (decimal): Latitude coördinaat
- `lng` (decimal): Longitude coördinaat
- `location` (string): Leesbare locatie naam
- `title`, `description`, `category`, `priority`, `status`

## Performance Overwegingen

- **Lazy Loading**: Kaart data wordt alleen geladen wanneer nodig
- **Efficient Queries**: Alleen benodigde velden worden opgehaald
- **Caching**: Overweeg Redis cache voor frequente queries
- **Pagination**: Overweeg paginering bij > 1000 klachten

## Toekomstige Verbeteringen

- [ ] Clustering van pins bij veel klachten op één locatie
- [ ] Heatmap weergave voor klacht dichtheid
- [ ] Export functionaliteit (CSV, Excel)
- [ ] Geavanceerde datum range filters
- [ ] Bulk acties op gefilterde klachten
- [ ] Realtime updates via WebSockets
- [ ] Geocoding API voor automatische coördinaten
- [ ] Route planning voor medewerkers

## Licentie

Dit dashboard maakt gebruik van:
- **Leaflet.js**: BSD 2-Clause License
- **OpenStreetMap**: ODbL (Open Database License)
