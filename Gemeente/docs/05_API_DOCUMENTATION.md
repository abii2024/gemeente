# 📡 API Documentation - Complete Reference

**Onderwerp:** RESTful API Endpoints met Voorbeelden  
**Datum:** 6 oktober 2025  
**Base URL:** `http://gemeente.test/api`

---

## 📋 Inhoudsopgave

1. [API Overzicht](#api-overzicht)
2. [Authentication](#authentication)
3. [Complaints Endpoints](#complaints-endpoints)
4. [Statistics Endpoint](#statistics-endpoint)
5. [Error Handling](#error-handling)
6. [Rate Limiting](#rate-limiting)
7. [Examples](#examples)

---

## 🎯 API Overzicht

### Base Information

```yaml
Protocol: HTTPS (Production) / HTTP (Development)
Base URL: http://gemeente.test/api
Format: JSON
Authentication: Optional (Sanctum tokens)
Rate Limit: 60 requests per minute
```

### Available Endpoints

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/complaints` | Lijst van klachten | ❌ |
| GET | `/complaints/{id}` | Specifieke klacht | ❌ |
| POST | `/complaints` | Nieuwe klacht | ❌ |
| PATCH | `/complaints/{id}/status` | Update status | ✅ |
| POST | `/complaints/{id}/notes` | Voeg notitie toe | ✅ |
| GET | `/complaints/search` | Zoek klachten | ❌ |
| GET | `/complaints/map` | GPS data voor kaart | ❌ |
| GET | `/statistics` | Dashboard statistieken | ❌ |

---

## 🔐 Authentication

### Sanctum Token Authentication (Optional)

Voor protected endpoints (status updates, notes):

**1. Login om token te krijgen:**
```bash
curl -X POST http://gemeente.test/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@gemeente.nl",
    "password": "password"
  }'
```

**Response:**
```json
{
  "success": true,
  "token": "1|AbCdEfGhIjKlMnOpQrStUvWxYz...",
  "user": {
    "id": 1,
    "name": "Admin",
    "email": "admin@gemeente.nl"
  }
}
```

**2. Gebruik token in requests:**
```bash
curl -X PATCH http://gemeente.test/api/complaints/1/status \
  -H "Authorization: Bearer 1|AbCdEfGhIjKlMnOpQrStUvWxYz..." \
  -H "Content-Type: application/json" \
  -d '{"status": "in_behandeling"}'
```

---

## 📝 Complaints Endpoints

### 1. GET /api/complaints

Haal lijst van klachten op met optionele filters.

**Query Parameters:**

| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `status` | string | ❌ | Filter op status | `open`, `in_behandeling`, `afgerond`, `gesloten` |
| `priority` | string | ❌ | Filter op prioriteit | `laag`, `normaal`, `hoog`, `urgent` |
| `category` | string | ❌ | Filter op categorie | `verkeer`, `openbare_ruimte`, etc. |
| `limit` | integer | ❌ | Aantal resultaten | `1-100` (default: 50) |
| `page` | integer | ❌ | Pagina nummer | `1, 2, 3...` |

**Request:**
```bash
curl -X GET "http://gemeente.test/api/complaints?status=open&limit=5" \
  -H "Accept: application/json"
```

**Response (200 OK):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Kapotte straatlantaarn",
      "description": "De lantaarn bij de Hoofdstraat 123 doet het niet meer",
      "category": "verlichting",
      "priority": "normaal",
      "status": "open",
      "address": "Hoofdstraat 123, Amsterdam",
      "latitude": "52.3777",
      "longitude": "4.9010",
      "contact_name": "Jan Jansen",
      "contact_email": "jan@example.com",
      "contact_phone": "+31612345678",
      "created_at": "2025-10-05T14:30:00.000000Z",
      "updated_at": "2025-10-05T14:30:00.000000Z",
      "user": {
        "id": 1,
        "name": "Jan Jansen",
        "email": "jan@example.com"
      },
      "photos": [
        {
          "id": 1,
          "path": "complaints/photo1.jpg",
          "url": "http://gemeente.test/storage/complaints/photo1.jpg"
        }
      ]
    }
  ],
  "total": 45,
  "page": 1,
  "per_page": 5
}
```

**cURL Voorbeelden:**

```bash
# Alle klachten
curl http://gemeente.test/api/complaints

# Alleen open klachten
curl http://gemeente.test/api/complaints?status=open

# Urgente klachten
curl http://gemeente.test/api/complaints?priority=urgent

# Verkeer klachten, eerste 10
curl http://gemeente.test/api/complaints?category=verkeer&limit=10

# Combinatie filters
curl "http://gemeente.test/api/complaints?status=open&priority=hoog&limit=20"
```

---

### 2. GET /api/complaints/{id}

Haal specifieke klacht op met alle details.

**Path Parameters:**
- `id` (integer, required) - Complaint ID

**Request:**
```bash
curl -X GET http://gemeente.test/api/complaints/1 \
  -H "Accept: application/json"
```

**Response (200 OK):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Kapotte straatlantaarn",
    "description": "De lantaarn bij de Hoofdstraat 123 doet het niet meer. Dit veroorzaakt onveilige situatie 's avonds.",
    "category": "verlichting",
    "priority": "hoog",
    "status": "in_behandeling",
    "address": "Hoofdstraat 123, Amsterdam",
    "latitude": "52.3777",
    "longitude": "4.9010",
    "contact_name": "Jan Jansen",
    "contact_email": "jan@example.com",
    "contact_phone": "+31612345678",
    "assigned_to": 2,
    "created_at": "2025-10-05T14:30:00.000000Z",
    "updated_at": "2025-10-05T16:45:00.000000Z",
    "user": {
      "id": 1,
      "name": "Jan Jansen",
      "email": "jan@example.com"
    },
    "assigned_user": {
      "id": 2,
      "name": "Admin User",
      "email": "admin@gemeente.nl"
    },
    "photos": [
      {
        "id": 1,
        "path": "complaints/abc123.jpg",
        "url": "http://gemeente.test/storage/complaints/abc123.jpg",
        "created_at": "2025-10-05T14:30:00.000000Z"
      },
      {
        "id": 2,
        "path": "complaints/def456.jpg",
        "url": "http://gemeente.test/storage/complaints/def456.jpg",
        "created_at": "2025-10-05T14:30:00.000000Z"
      }
    ],
    "notes": [
      {
        "id": 1,
        "note": "Monteur is ingeschakeld, verwachte reparatie binnen 2 dagen.",
        "is_internal": false,
        "created_at": "2025-10-05T16:45:00.000000Z",
        "user": {
          "id": 2,
          "name": "Admin User"
        }
      }
    ]
  }
}
```

**Error Response (404 Not Found):**
```json
{
  "success": false,
  "message": "Complaint not found"
}
```

---

### 3. POST /api/complaints

Maak nieuwe klacht aan.

**Request Body:**

| Field | Type | Required | Description | Validation |
|-------|------|----------|-------------|------------|
| `title` | string | ✅ | Titel van klacht | max:255 |
| `description` | string | ✅ | Beschrijving | - |
| `category` | string | ✅ | Categorie | Zie categorie lijst |
| `address` | string | ✅ | Adres | - |
| `latitude` | decimal | ❌ | GPS breedtegraad | -90 to 90 |
| `longitude` | decimal | ❌ | GPS lengtegraad | -180 to 180 |
| `contact_name` | string | ✅ | Naam melder | max:255 |
| `contact_email` | string | ✅ | Email melder | valid email |
| `contact_phone` | string | ❌ | Telefoon melder | max:20 |

**Categorieën:**
- `openbare_ruimte` - Openbare Ruimte
- `verkeer` - Verkeer
- `overlast` - Overlast
- `afval` - Afval
- `verlichting` - Verlichting
- `groen` - Groen
- `anders` - Anders

**Request:**
```bash
curl -X POST http://gemeente.test/api/complaints \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "title": "Kapotte stoeptegel",
    "description": "Op de hoek van de straat ligt een gevaarlijke losse stoeptegel waar mensen over kunnen struikelen.",
    "category": "openbare_ruimte",
    "address": "Kalverstraat 45, Amsterdam",
    "latitude": 52.3702,
    "longitude": 4.8952,
    "contact_name": "Maria de Vries",
    "contact_email": "maria@example.com",
    "contact_phone": "+31687654321"
  }'
```

**Response (201 Created):**
```json
{
  "success": true,
  "message": "Klacht succesvol aangemaakt",
  "data": {
    "id": 46,
    "title": "Kapotte stoeptegel",
    "description": "Op de hoek van de straat ligt een gevaarlijke losse stoeptegel waar mensen over kunnen struikelen.",
    "category": "openbare_ruimte",
    "priority": "normaal",
    "status": "open",
    "address": "Kalverstraat 45, Amsterdam",
    "latitude": "52.3702",
    "longitude": "4.8952",
    "contact_name": "Maria de Vries",
    "contact_email": "maria@example.com",
    "contact_phone": "+31687654321",
    "created_at": "2025-10-06T10:15:00.000000Z",
    "updated_at": "2025-10-06T10:15:00.000000Z"
  }
}
```

**Validation Error (422 Unprocessable Entity):**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "title": ["The title field is required."],
    "contact_email": ["The contact email must be a valid email address."]
  }
}
```

**JavaScript Fetch Voorbeeld:**
```javascript
async function createComplaint(data) {
  try {
    const response = await fetch('http://gemeente.test/api/complaints', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify(data)
    });
    
    const result = await response.json();
    
    if (response.ok) {
      console.log('Klacht aangemaakt:', result.data);
    } else {
      console.error('Validation errors:', result.errors);
    }
  } catch (error) {
    console.error('Network error:', error);
  }
}

// Gebruik
createComplaint({
  title: 'Kapot bankje',
  description: 'Het bankje in het park is kapot',
  category: 'openbare_ruimte',
  address: 'Vondelpark, Amsterdam',
  contact_name: 'Test User',
  contact_email: 'test@example.com'
});
```

---

### 4. PATCH /api/complaints/{id}/status

Update status van klacht. **⚠️ Vereist authenticatie**

**Path Parameters:**
- `id` (integer, required) - Complaint ID

**Request Body:**

| Field | Type | Required | Description | Values |
|-------|------|----------|-------------|--------|
| `status` | string | ✅ | Nieuwe status | `open`, `in_behandeling`, `afgerond`, `gesloten` |

**Request:**
```bash
curl -X PATCH http://gemeente.test/api/complaints/1/status \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "status": "in_behandeling"
  }'
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Status bijgewerkt",
  "data": {
    "id": 1,
    "title": "Kapotte straatlantaarn",
    "status": "in_behandeling",
    "updated_at": "2025-10-06T11:20:00.000000Z"
  }
}
```

**Error (401 Unauthorized):**
```json
{
  "message": "Unauthenticated."
}
```

---

### 5. POST /api/complaints/{id}/notes

Voeg notitie toe aan klacht. **⚠️ Vereist authenticatie**

**Path Parameters:**
- `id` (integer, required) - Complaint ID

**Request Body:**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `note` | string | ✅ | Notitie tekst |
| `is_internal` | boolean | ❌ | Intern of publiek (default: false) |

**Request:**
```bash
curl -X POST http://gemeente.test/api/complaints/1/notes \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "note": "Monteur is onderweg naar locatie",
    "is_internal": false
  }'
```

**Response (201 Created):**
```json
{
  "success": true,
  "message": "Notitie toegevoegd",
  "data": {
    "id": 5,
    "complaint_id": 1,
    "note": "Monteur is onderweg naar locatie",
    "is_internal": false,
    "created_at": "2025-10-06T11:30:00.000000Z",
    "user": {
      "id": 2,
      "name": "Admin User",
      "email": "admin@gemeente.nl"
    }
  }
}
```

---

### 6. GET /api/complaints/search

Full-text search in klachten.

**Query Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `q` | string | ✅ | Zoekterm (min 3 karakters) |
| `limit` | integer | ❌ | Aantal resultaten (default: 20) |

**Request:**
```bash
curl -X GET "http://gemeente.test/api/complaints/search?q=lantaarn&limit=10" \
  -H "Accept: application/json"
```

**Response (200 OK):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Kapotte straatlantaarn",
      "description": "De lantaarn doet het niet",
      "status": "open",
      "address": "Hoofdstraat 123, Amsterdam"
    },
    {
      "id": 15,
      "title": "Defecte lantaarn",
      "description": "Lantaarn bij het park werkt niet",
      "status": "in_behandeling",
      "address": "Parkstraat 7, Amsterdam"
    }
  ],
  "count": 2,
  "query": "lantaarn"
}
```

**Zoekt in:**
- `title` - Titel van klacht
- `description` - Beschrijving
- `address` - Adres
- `contact_name` - Naam melder

---

### 7. GET /api/complaints/map

Haal klachten op met GPS coördinaten voor kaart visualisatie.

**Query Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `status` | string | ❌ | Filter op status |
| `bounds` | string | ❌ | Geografische bounds (lat_min,lng_min,lat_max,lng_max) |

**Request:**
```bash
curl -X GET "http://gemeente.test/api/complaints/map?status=open" \
  -H "Accept: application/json"
```

**Response (200 OK):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Kapotte straatlantaarn",
      "category": "verlichting",
      "status": "open",
      "priority": "normaal",
      "latitude": 52.3777,
      "longitude": 4.901,
      "address": "Hoofdstraat 123, Amsterdam",
      "created_at": "2025-10-05 14:30"
    },
    {
      "id": 2,
      "title": "Losliggende stoeptegel",
      "category": "openbare_ruimte",
      "status": "open",
      "priority": "hoog",
      "latitude": 52.3702,
      "longitude": 4.8952,
      "address": "Kalverstraat 45, Amsterdam",
      "created_at": "2025-10-06 10:15"
    }
  ],
  "count": 2
}
```

**Met geografische bounds:**
```bash
# Alleen klachten binnen Amsterdam centrum
curl "http://gemeente.test/api/complaints/map?bounds=52.35,4.85,52.40,4.95"
```

---

## 📊 Statistics Endpoint

### GET /api/statistics

Haal dashboard statistieken op.

**Query Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `period` | string | ❌ | Tijdsperiode | `today`, `week`, `month`, `year`, `all` (default) |

**Request:**
```bash
curl -X GET "http://gemeente.test/api/statistics?period=month" \
  -H "Accept: application/json"
```

**Response (200 OK):**
```json
{
  "success": true,
  "period": "month",
  "data": {
    "totals": {
      "all": 124,
      "open": 45,
      "in_behandeling": 32,
      "afgerond": 38,
      "gesloten": 9
    },
    "by_status": {
      "open": 45,
      "in_behandeling": 32,
      "afgerond": 38,
      "gesloten": 9
    },
    "by_priority": {
      "laag": 15,
      "normaal": 67,
      "hoog": 34,
      "urgent": 8
    },
    "by_category": {
      "openbare_ruimte": 42,
      "verkeer": 28,
      "verlichting": 18,
      "overlast": 15,
      "afval": 12,
      "groen": 7,
      "anders": 2
    },
    "top_categories": [
      {
        "category": "openbare_ruimte",
        "count": 42
      },
      {
        "category": "verkeer",
        "count": 28
      },
      {
        "category": "verlichting",
        "count": 18
      }
    ],
    "trends": [
      {
        "date": "2025-09-30",
        "count": 12
      },
      {
        "date": "2025-10-01",
        "count": 15
      },
      {
        "date": "2025-10-02",
        "count": 8
      }
    ],
    "avg_resolution_days": 3.5
  }
}
```

---

## ⚠️ Error Handling

### Standard Error Response Format

```json
{
  "success": false,
  "message": "Error description",
  "errors": {
    "field_name": ["Error message 1", "Error message 2"]
  }
}
```

### HTTP Status Codes

| Code | Status | Description |
|------|--------|-------------|
| 200 | OK | Request successful |
| 201 | Created | Resource created successfully |
| 400 | Bad Request | Invalid request format |
| 401 | Unauthorized | Authentication required |
| 403 | Forbidden | Insufficient permissions |
| 404 | Not Found | Resource not found |
| 422 | Unprocessable Entity | Validation failed |
| 429 | Too Many Requests | Rate limit exceeded |
| 500 | Internal Server Error | Server error |

### Example Error Responses

**Validation Error (422):**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": [
      "The email field is required.",
      "The email must be a valid email address."
    ],
    "title": [
      "The title field is required."
    ]
  }
}
```

**Not Found (404):**
```json
{
  "success": false,
  "message": "Complaint not found"
}
```

**Rate Limit (429):**
```json
{
  "message": "Too Many Attempts.",
  "retry_after": 60
}
```

---

## 🚦 Rate Limiting

### Limits

- **Default:** 60 requests per minute per IP
- **Authenticated:** 100 requests per minute per user

### Headers

Response headers bevatten rate limit informatie:

```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 45
X-RateLimit-Reset: 1696600800
```

### Handling Rate Limits

```javascript
async function apiRequest(url) {
  const response = await fetch(url);
  
  if (response.status === 429) {
    const retryAfter = response.headers.get('Retry-After');
    console.log(`Rate limit exceeded. Retry after ${retryAfter} seconds`);
    
    // Wait and retry
    await new Promise(resolve => setTimeout(resolve, retryAfter * 1000));
    return apiRequest(url); // Retry
  }
  
  return response.json();
}
```

---

## 💻 Complete Examples

### PHP (Laravel HTTP Client)

```php
use Illuminate\Support\Facades\Http;

// Get complaints
$response = Http::get('http://gemeente.test/api/complaints', [
    'status' => 'open',
    'limit' => 10
]);

$complaints = $response->json()['data'];

// Create complaint
$response = Http::post('http://gemeente.test/api/complaints', [
    'title' => 'Kapot bankje',
    'description' => 'Het bankje is kapot',
    'category' => 'openbare_ruimte',
    'address' => 'Parkstraat 1, Amsterdam',
    'contact_name' => 'Jan',
    'contact_email' => 'jan@example.com'
]);

if ($response->successful()) {
    $complaint = $response->json()['data'];
    echo "Klacht aangemaakt met ID: {$complaint['id']}";
}
```

### JavaScript (Fetch API)

```javascript
// Get complaints with filters
async function getComplaints(filters = {}) {
  const params = new URLSearchParams(filters);
  const response = await fetch(`http://gemeente.test/api/complaints?${params}`);
  const data = await response.json();
  return data.data;
}

// Usage
const openComplaints = await getComplaints({ status: 'open', limit: 20 });
console.log(openComplaints);

// Create complaint
async function createComplaint(complaintData) {
  const response = await fetch('http://gemeente.test/api/complaints', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    },
    body: JSON.stringify(complaintData)
  });
  
  if (!response.ok) {
    const error = await response.json();
    throw new Error(error.message);
  }
  
  return await response.json();
}

// Usage
try {
  const result = await createComplaint({
    title: 'Kapotte straatlantaarn',
    description: 'De lantaarn werkt niet',
    category: 'verlichting',
    address: 'Hoofdstraat 123',
    contact_name: 'Jan Jansen',
    contact_email: 'jan@example.com'
  });
  console.log('Klacht aangemaakt:', result.data);
} catch (error) {
  console.error('Error:', error.message);
}
```

### Python (Requests)

```python
import requests

BASE_URL = 'http://gemeente.test/api'

# Get complaints
response = requests.get(f'{BASE_URL}/complaints', params={
    'status': 'open',
    'limit': 10
})
complaints = response.json()['data']

# Create complaint
complaint_data = {
    'title': 'Kapot bankje',
    'description': 'Het bankje is beschadigd',
    'category': 'openbare_ruimte',
    'address': 'Parkstraat 1',
    'contact_name': 'Jan',
    'contact_email': 'jan@example.com'
}

response = requests.post(f'{BASE_URL}/complaints', json=complaint_data)
if response.status_code == 201:
    result = response.json()
    print(f"Klacht aangemaakt: {result['data']['id']}")

# Search complaints
response = requests.get(f'{BASE_URL}/complaints/search', params={
    'q': 'lantaarn',
    'limit': 5
})
results = response.json()['data']
```

### cURL Complete Workflow

```bash
#!/bin/bash

# 1. Get all open complaints
echo "=== Open Klachten ==="
curl -s http://gemeente.test/api/complaints?status=open | jq '.data[] | {id, title, address}'

# 2. Create new complaint
echo -e "\n=== Nieuwe Klacht Aanmaken ==="
RESPONSE=$(curl -s -X POST http://gemeente.test/api/complaints \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Test klacht via API",
    "description": "Dit is een test",
    "category": "openbare_ruimte",
    "address": "Teststraat 1",
    "contact_name": "Test User",
    "contact_email": "test@example.com"
  }')

COMPLAINT_ID=$(echo $RESPONSE | jq -r '.data.id')
echo "Aangemaakt met ID: $COMPLAINT_ID"

# 3. Get complaint details
echo -e "\n=== Klacht Details ==="
curl -s http://gemeente.test/api/complaints/$COMPLAINT_ID | jq '.data'

# 4. Search complaints
echo -e "\n=== Zoeken ==="
curl -s "http://gemeente.test/api/complaints/search?q=test" | jq '.data[] | {id, title}'

# 5. Get statistics
echo -e "\n=== Statistieken ==="
curl -s http://gemeente.test/api/statistics?period=week | jq '.data.totals'
```

---

## 🧪 Testing with Postman

### Import Collection

**Create Postman Collection:**

1. Open Postman
2. Click "Import"
3. Paste this JSON:

```json
{
  "info": {
    "name": "Gemeente API",
    "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
  },
  "variable": [
    {
      "key": "base_url",
      "value": "http://gemeente.test/api"
    }
  ],
  "item": [
    {
      "name": "Get Complaints",
      "request": {
        "method": "GET",
        "url": "{{base_url}}/complaints?status=open&limit=10"
      }
    },
    {
      "name": "Create Complaint",
      "request": {
        "method": "POST",
        "url": "{{base_url}}/complaints",
        "body": {
          "mode": "raw",
          "raw": "{\n  \"title\": \"Test Klacht\",\n  \"description\": \"Dit is een test\",\n  \"category\": \"openbare_ruimte\",\n  \"address\": \"Teststraat 1\",\n  \"contact_name\": \"Test User\",\n  \"contact_email\": \"test@example.com\"\n}",
          "options": {
            "raw": {
              "language": "json"
            }
          }
        }
      }
    }
  ]
}
```

---

## 📚 Additional Resources

- **Laravel API Docs:** https://laravel.com/docs/11.x/controllers#api-resource-routes
- **HTTP Status Codes:** https://httpstatuses.com/
- **REST API Best Practices:** https://restfulapi.net/

---

**Need help?** Check de [02_BACKEND_LARAVEL.md](02_BACKEND_LARAVEL.md) voor implementatie details.
