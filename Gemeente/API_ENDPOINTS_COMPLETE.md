# ✅ Laravel API Endpoints - COMPLEET!

## 🎉 Alle API endpoints zijn nu werkend!

Je kunt nu **ALLE** 8 MCP API tools gebruiken!

## 📋 Beschikbare Endpoints

### 1. GET `/api/complaints`
Haal klachten op met filters

**Parameters:**
- `status` (optional): open, in_progress, resolved, closed
- `priority` (optional): low, medium, high, urgent  
- `limit` (optional): aantal resultaten (default: 10)

**Voorbeelden:**
```bash
curl 'http://gemeente.test/api/complaints?status=open&limit=5'
curl 'http://gemeente.test/api/complaints?priority=urgent'
curl 'http://gemeente.test/api/complaints?status=in_progress&priority=high'
```

**MCP Tool:** `get_complaints`

---

### 2. GET `/api/complaints/{id}`
Haal specifieke klacht op

**Voorbeeld:**
```bash
curl 'http://gemeente.test/api/complaints/1'
```

**MCP Tool:** `get_complaint_by_id`

---

### 3. POST `/api/complaints`
Maak nieuwe klacht aan

**Body (JSON):**
```json
{
  "title": "Kapotte straatverlichting",
  "description": "De straatverlichting op Hoofdstraat 123 is defect",
  "category": "infrastructuur",
  "priority": "medium",
  "reporter_name": "Jan Jansen",
  "reporter_email": "jan@example.com",
  "reporter_phone": "0612345678",
  "location": "Hoofdstraat 123, Amsterdam",
  "lat": 52.3676,
  "lng": 4.9041
}
```

**Vereiste velden:**
- title
- description
- category
- reporter_name
- reporter_email

**MCP Tool:** `create_complaint`

---

### 4. PATCH `/api/complaints/{id}/status`
Update klacht status

**Body (JSON):**
```json
{
  "status": "in_progress",
  "notes": "Technici ingeschakeld"
}
```

**Status opties:**
- open
- in_progress
- resolved
- closed

**MCP Tool:** `update_complaint_status`

---

### 5. POST `/api/complaints/{id}/notes`
Voeg notitie toe aan klacht

**Body (JSON):**
```json
{
  "note": "Extra informatie over de klacht",
  "body": "Alternatief veld naam"
}
```

**MCP Tool:** `add_complaint_note`

---

### 6. GET `/api/statistics`
Haal statistieken op

**Parameters:**
- `period` (optional): today, week, month, year, all (default: all)

**Voorbeelden:**
```bash
curl 'http://gemeente.test/api/statistics?period=month'
curl 'http://gemeente.test/api/statistics?period=week'
```

**Response bevat:**
- Total aantal klachten
- Verdeling per status
- Verdeling per prioriteit
- Verdeling per categorie
- Gemiddelde oplostijd
- Trends laatste 7 dagen
- Top 5 categorieën

**MCP Tool:** `get_statistics`

---

### 7. GET `/api/complaints/search`
Zoek klachten

**Parameters:**
- `q` of `query`: zoekterm (required)
- `limit` (optional): aantal resultaten (default: 10)

**Voorbeelden:**
```bash
curl 'http://gemeente.test/api/complaints/search?q=afval'
curl 'http://gemeente.test/api/complaints/search?q=straatverlichting&limit=5'
```

**Zoekt in:**
- Titel
- Beschrijving
- Locatie

**MCP Tool:** `search_complaints`

---

### 8. GET `/api/complaints/map`
Haal klachten op voor kaartweergave (alleen klachten met GPS)

**Parameters:**
- `status` (optional): filter op status

**Voorbeelden:**
```bash
curl 'http://gemeente.test/api/complaints/map'
curl 'http://gemeente.test/api/complaints/map?status=open'
```

**Response bevat alleen klachten met lat/lng coordinates**

**MCP Tool:** `get_complaint_map_data`

---

## 🚀 MCP Server Gebruik

Nu kun je in VSCode via Copilot Chat vragen:

### ✅ Werkt Nu!
```
"Haal alle open klachten op"
"Geef me statistieken van deze maand"
"Zoek naar klachten over afval"
"Maak een nieuwe klacht over straatverlichting"
"Update status van klacht 5 naar in_progress"
"Voeg een notitie toe aan klacht 3"
"Laat me alle klachten op de kaart zien"
```

## 📊 Response Format

Alle endpoints retourneren JSON in dit format:

```json
{
  "success": true,
  "data": { ... },
  "count": 10
}
```

Bij errors:

```json
{
  "success": false,
  "errors": { ... },
  "message": "Error beschrijving"
}
```

## 🔒 Security

- Alle endpoints hebben rate limiting (`throttle:api`)
- Status updates en notities kunnen user_id tracken
- Validation op alle input velden
- SQL injection bescherming via Eloquent

## 🧪 Quick Test

Test alle endpoints:

```bash
# 1. Get complaints
curl 'http://gemeente.test/api/complaints?status=open&limit=3'

# 2. Get one complaint
curl 'http://gemeente.test/api/complaints/1'

# 3. Statistics
curl 'http://gemeente.test/api/statistics?period=week'

# 4. Search
curl 'http://gemeente.test/api/complaints/search?q=test'

# 5. Map data
curl 'http://gemeente.test/api/complaints/map?status=open'

# 6. Create complaint
curl -X POST 'http://gemeente.test/api/complaints' \
  -H 'Content-Type: application/json' \
  -d '{
    "title": "Test klacht via API",
    "description": "Dit is een test",
    "category": "infrastructuur",
    "priority": "medium",
    "reporter_name": "Test User",
    "reporter_email": "test@example.com",
    "location": "Amsterdam"
  }'

# 7. Update status
curl -X PATCH 'http://gemeente.test/api/complaints/1/status' \
  -H 'Content-Type: application/json' \
  -d '{"status": "in_progress"}'

# 8. Add note
curl -X POST 'http://gemeente.test/api/complaints/1/notes' \
  -H 'Content-Type: application/json' \
  -d '{"note": "Dit is een test notitie"}'
```

## 🎯 Ready to Use!

**In VSCode:**
1. Reload Window (`⌘ + Shift + P` → "Developer: Reload Window")
2. Open Copilot Chat
3. Vraag: **"Haal alle open klachten op met status open"**

**De MCP server gebruikt nu de volledige Laravel API!** 🚀

## 📝 Created Files

1. **ComplaintApiController.php** - Alle complaint endpoints
2. **StatisticsController.php** - Statistics endpoint
3. **routes/api.php** - Updated met alle routes

## ✨ Features

- ✅ Filtering op status en prioriteit
- ✅ Limit op resultaten
- ✅ Volledige CRUD voor klachten
- ✅ Status history tracking
- ✅ Notities systeem
- ✅ Full-text search
- ✅ GPS-based map data
- ✅ Uitgebreide statistieken
- ✅ Trends en analytics
- ✅ Validation en error handling
- ✅ JSON Resources voor consistente output

Alles werkt nu perfect! 🎉
