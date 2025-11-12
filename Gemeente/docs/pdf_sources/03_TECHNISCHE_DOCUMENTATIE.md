# PDF 3: Technische Documentatie

**Gemeente Portal - Architectuur & Design Patterns**  
**Datum:** 10 November 2025  
**Auteur:** Abdisamad Abdulle

---

## Inhoudsopgave

1. Systeem Architectuur
2. Tech Stack & Dependencies
3. Design Patterns
4. Database Schema & ERD
5. Security Implementaties
6. API Endpoints
7. Modern CSS Architecture
8. Performance & Optimalisatie
9. Testing Strategie
10. Deployment

---

## 1. Systeem Architectuur

### 1.1 High-Level Architectuur

```
┌─────────────────────────────────────────────────────────────┐
│                        GEBRUIKER                            │
│                    (Browser: Chrome/Safari)                 │
└────────────────┬────────────────────────────────────────────┘
                 │
                 │ HTTPS
                 ▼
┌─────────────────────────────────────────────────────────────┐
│                     FRONTEND LAYER                          │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐     │
│  │ Blade Views  │  │  Alpine.js   │  │  Modern CSS  │     │
│  │  Templates   │  │  Reactive UI │  │  Animations  │     │
│  └──────────────┘  └──────────────┘  └──────────────┘     │
└────────────────┬────────────────────────────────────────────┘
                 │
                 │ HTTP POST/GET
                 ▼
┌─────────────────────────────────────────────────────────────┐
│                   APPLICATION LAYER                         │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐     │
│  │  Controllers │  │  Middleware  │  │   Policies   │     │
│  │              │  │              │  │              │     │
│  │ - Complaint  │  │ - Auth       │  │ - AdminPolicy│     │
│  │ - Diensten   │  │ - CSRF       │  │              │     │
│  │ - Chatbot    │  │ - Throttle   │  │              │     │
│  └──────┬───────┘  └──────────────┘  └──────────────┘     │
│         │                                                   │
│         ▼                                                   │
│  ┌──────────────┐  ┌──────────────┐                       │
│  │   Services   │  │    Models    │                       │
│  │              │  │              │                       │
│  │ - OpenAI     │  │ - User       │                       │
│  │ - Email      │  │ - Complaint  │                       │
│  │ - Image      │  │ - Afspraak   │                       │
│  └──────────────┘  └──────┬───────┘                       │
└────────────────────────────┼────────────────────────────────┘
                             │
                             │ Eloquent ORM
                             ▼
┌─────────────────────────────────────────────────────────────┐
│                     DATABASE LAYER                          │
│  ┌──────────────────────────────────────────────────────┐  │
│  │                   MySQL Database                      │  │
│  │                                                        │  │
│  │  ┌──────┐  ┌──────────┐  ┌────────────┐  ┌────────┐ │  │
│  │  │Users │  │Complaints│  │Attachments │  │ Notes  │ │  │
│  │  └──────┘  └──────────┘  └────────────┘  └────────┘ │  │
│  │                                                        │  │
│  │  ┌──────────┐  ┌───────────────┐                     │  │
│  │  │Afspraken │  │StatusHistory  │                     │  │
│  │  └──────────┘  └───────────────┘                     │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                             │
                             │
                             ▼
┌─────────────────────────────────────────────────────────────┐
│                   EXTERNAL SERVICES                         │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐     │
│  │  OpenAI API  │  │  Email SMTP  │  │  File Storage│     │
│  │  GPT-4 Turbo │  │  Mailtrap    │  │  Local Disk  │     │
│  └──────────────┘  └──────────────┘  └──────────────┘     │
└─────────────────────────────────────────────────────────────┘
```

### 1.2 Request Flow (Voorbeeld: Klacht Indienen)

```
1. User klikt "Klacht Indienen"
   └─> Browser: GET /klachten/nieuw

2. Laravel Router (routes/web.php)
   └─> Route::get('/klachten/nieuw', [ComplaintController::class, 'create'])

3. Controller Method
   └─> ComplaintController@create()
       └─> return view('complaints.create')

4. Blade Template Engine
   └─> Compileert complaints/create.blade.php
       └─> Extends layouts/app.blade.php
       └─> Injects CSRF token, form helpers

5. Browser ontvangt HTML + CSS + JS
   └─> Alpine.js initialiseert
   └─> Leaflet map initialiseert

6. User vult formulier in en submit
   └─> Browser: POST /klachten/nieuw

7. Middleware Stack
   ├─> CSRF Verificatie
   ├─> Input Sanitization
   └─> Throttle Check

8. Controller Method
   └─> ComplaintController@store(Request $request)
       ├─> Validatie van input
       ├─> Image upload & resize
       ├─> Database insert (Eloquent)
       └─> Redirect met success message

9. Database
   └─> INSERT INTO complaints (...)
   └─> INSERT INTO attachments (...)

10. Response
    └─> HTTP 302 Redirect naar /bedankt
    └─> Flash success message in sessie
```

---

## 2. Tech Stack & Dependencies

### 2.1 Backend

| Technologie | Versie | Waarom Gekozen |
|------------|--------|----------------|
| **PHP** | 8.3+ | Modern syntax, performance, type safety |
| **Laravel** | 11.x | Best PHP framework, Eloquent ORM, Blade templates |
| **MySQL** | 8.0+ | Betrouwbaar, relations support, goed met Laravel |
| **Composer** | 2.x | PHP dependency management |

**Belangrijke Laravel Packages:**

```json
{
    "laravel/framework": "^11.0",
    "laravel/breeze": "^2.0",          // Auth scaffolding
    "spatie/laravel-permission": "^6.0", // Role & permission management
    "intervention/image": "^3.0",       // Image resize/manipulation
    "openai-php/client": "^0.10"        // OpenAI API client
}
```

### 2.2 Frontend

| Technologie | Versie | Waarom Gekozen |
|------------|--------|----------------|
| **Alpine.js** | 3.13.0 | Lightweight (15KB), reactive, geen build step |
| **Leaflet** | 1.9.4 | Open-source maps, gratis, geen API key |
| **Modern CSS** | - | No framework, pure CSS, sneller dan Tailwind |
| **Vite** | 5.x | Super fast build tool, HMR |

**Alpine.js vs React:**
```javascript
// Alpine.js (simpel, klein)
<div x-data="{ open: false }">
    <button @click="open = !open">Toggle</button>
    <div x-show="open">Content</div>
</div>

// React (complex, groot, build step)
function Component() {
    const [open, setOpen] = useState(false);
    return (
        <div>
            <button onClick={() => setOpen(!open)}>Toggle</button>
            {open && <div>Content</div>}
        </div>
    );
}
```

### 2.3 Development Tools

```json
{
    "phpstan/phpstan": "^1.11",      // Static analysis
    "pestphp/pest": "^2.34",         // Testing framework
    "laravel/pint": "^1.0",          // Code formatting
    "biomejs/biome": "^1.8"          // JS/CSS linting
}
```

---

## 3. Design Patterns

### 3.1 MVC Pattern

**Model-View-Controller** is het basis pattern van Laravel:

```
┌──────────────┐
│   Browser    │
└──────┬───────┘
       │ HTTP Request
       ▼
┌──────────────┐
│  CONTROLLER  │ ← Ontvangt request, valideert, roept Model aan
│              │
│ - Complaint  │
│   Controller │
└──────┬───────┘
       │
       ├─────────────────┐
       │                 │
       ▼                 ▼
┌──────────────┐  ┌──────────────┐
│    MODEL     │  │     VIEW     │
│              │  │              │
│ - Complaint  │  │ - Blade      │
│   Model      │  │   Template   │
│              │  │              │
│ Database     │  │ HTML/CSS/JS  │
│ Logic        │  │              │
└──────────────┘  └──────────────┘
```

**Voorbeeld:**

```php
// CONTROLLER (app/Http/Controllers/ComplaintController.php)
public function store(Request $request)
{
    // Validatie
    $validated = $request->validate([...]);
    
    // MODEL aanroepen
    $complaint = Complaint::create($validated);
    
    // VIEW returnen
    return view('complaints.thanks', compact('complaint'));
}

// MODEL (app/Models/Complaint.php)
class Complaint extends Model
{
    protected $fillable = ['title', 'description', ...];
    
    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }
}

// VIEW (resources/views/complaints/thanks.blade.php)
<h1>Bedankt voor uw melding!</h1>
<p>Melding #{{ $complaint->id }} is ontvangen.</p>
```

**Voordelen:**
- ✅ Scheiding van concerns (separation of concerns)
- ✅ Testbaar (elke laag apart testen)
- ✅ Herbruikbaar (Model kan door meerdere Controllers gebruikt worden)

---

### 3.2 Repository Pattern (Implicitly via Eloquent)

Laravel's Eloquent ORM is eigenlijk een Repository Pattern:

```php
// Zonder Repository Pattern (direct database query)
$complaints = DB::table('complaints')
    ->where('status', 'open')
    ->orderBy('created_at', 'desc')
    ->get();

// Met Eloquent (Repository Pattern)
$complaints = Complaint::open()->recent()->get();
```

**Voordelen:**
- ✅ Database queries zijn geabstraheerd
- ✅ Makkelijk switchen van database (MySQL → PostgreSQL)
- ✅ Query hergebruik via scopes

---

### 3.3 Service Pattern

Voor complexe business logic gebruiken we Services:

```php
// BAD: Alles in Controller
class ComplaintController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([...]);
        $complaint = Complaint::create($validated);
        
        // Foto upload
        foreach ($request->file('attachments') as $file) {
            $path = $file->store('complaints');
            $image = Image::read($path)->scale(1920);
            $image->save($path);
            $complaint->attachments()->create([...]);
        }
        
        // Email versturen
        Mail::to($complaint->reporter_email)->send(new ComplaintReceived($complaint));
        
        // Slack notificatie
        Slack::send("Nieuwe klacht: {$complaint->title}");
        
        return redirect()->route('complaint.thanks');
    }
}

// GOOD: Service Pattern
class ComplaintController extends Controller
{
    public function __construct(
        private ComplaintService $complaintService
    ) {}
    
    public function store(Request $request)
    {
        $validated = $request->validate([...]);
        
        $complaint = $this->complaintService->createComplaint(
            $validated,
            $request->file('attachments')
        );
        
        return redirect()->route('complaint.thanks');
    }
}

// app/Services/ComplaintService.php
class ComplaintService
{
    public function createComplaint(array $data, array $files): Complaint
    {
        $complaint = Complaint::create($data);
        
        $this->uploadAttachments($complaint, $files);
        $this->sendNotifications($complaint);
        
        return $complaint;
    }
    
    private function uploadAttachments(Complaint $complaint, array $files): void
    {
        foreach ($files as $file) {
            // Upload logic
        }
    }
    
    private function sendNotifications(Complaint $complaint): void
    {
        Mail::to($complaint->reporter_email)->send(new ComplaintReceived($complaint));
        Slack::send("Nieuwe klacht: {$complaint->title}");
    }
}
```

**Voordelen:**
- ✅ Controller blijft simpel
- ✅ Business logic is herbruikbaar
- ✅ Makkelijk te testen

---

### 3.4 Observer Pattern

Laravel gebruikt Observers voor model events:

```php
// app/Observers/ComplaintObserver.php
class ComplaintObserver
{
    public function created(Complaint $complaint): void
    {
        // Send email when complaint created
        Mail::to($complaint->reporter_email)
            ->send(new ComplaintReceived($complaint));
    }
    
    public function updated(Complaint $complaint): void
    {
        // Log status change
        if ($complaint->isDirty('status')) {
            StatusHistory::create([
                'complaint_id' => $complaint->id,
                'old_status' => $complaint->getOriginal('status'),
                'new_status' => $complaint->status,
            ]);
        }
    }
    
    public function deleting(Complaint $complaint): void
    {
        // Delete attachments from disk
        foreach ($complaint->attachments as $attachment) {
            Storage::delete($attachment->file_path);
        }
    }
}

// Register in AppServiceProvider
public function boot(): void
{
    Complaint::observe(ComplaintObserver::class);
}
```

**Voordelen:**
- ✅ Automatisch uitgevoerd bij model events
- ✅ DRY (Don't Repeat Yourself)
- ✅ Side effects geïsoleerd

---

### 3.5 Strategy Pattern (Policy)

Laravel's Policies voor authorization:

```php
// app/Policies/ComplaintPolicy.php
class ComplaintPolicy
{
    public function view(User $user, Complaint $complaint): bool
    {
        // Admin kan alles zien
        if ($user->isAdmin()) {
            return true;
        }
        
        // User kan alleen eigen klachten zien
        return $complaint->user_id === $user->id;
    }
    
    public function update(User $user, Complaint $complaint): bool
    {
        // Alleen admin kan updaten
        return $user->isAdmin();
    }
    
    public function delete(User $user, Complaint $complaint): bool
    {
        // Alleen admin kan verwijderen
        return $user->isAdmin();
    }
}

// Gebruik in Controller
public function show(Complaint $complaint)
{
    $this->authorize('view', $complaint);
    
    return view('complaints.show', compact('complaint'));
}
```

**Voordelen:**
- ✅ Authorization logic gecentraliseerd
- ✅ Herbruikbaar
- ✅ Declarative (`$this->authorize()`)

---

## 4. Database Schema & ERD

### 4.1 Entity Relationship Diagram

```
┌──────────────────────┐
│       USERS          │
│──────────────────────│
│ • id (PK)            │
│ • name               │
│ • email (UNIQUE)     │
│ • password           │
│ • role               │
│ • created_at         │
│ • updated_at         │
└─────────┬────────────┘
          │
          │ 1:N
          │
          ▼
┌──────────────────────┐         ┌──────────────────────┐
│    COMPLAINTS        │ 1:N     │    ATTACHMENTS       │
│──────────────────────│◄────────│──────────────────────│
│ • id (PK)            │         │ • id (PK)            │
│ • title              │         │ • complaint_id (FK)  │
│ • description        │         │ • file_path          │
│ • category (ENUM)    │         │ • file_name          │
│ • status (ENUM)      │         │ • mime_type          │
│ • lat                │         │ • file_size          │
│ • lng                │         │ • created_at         │
│ • address            │         │ • updated_at         │
│ • reporter_name      │         └──────────────────────┘
│ • reporter_email     │
│ • reporter_phone     │
│ • user_id (FK, NULL) │
│ • created_at         │         ┌──────────────────────┐
│ • updated_at         │ 1:N     │       NOTES          │
│ • resolved_at        │◄────────│──────────────────────│
└─────────┬────────────┘         │ • id (PK)            │
          │                       │ • complaint_id (FK)  │
          │                       │ • user_id (FK)       │
          │                       │ • body               │
          │                       │ • is_internal (BOOL) │
          │                       │ • created_at         │
          │                       │ • updated_at         │
          │                       └──────────────────────┘
          │
          │ 1:N
          │
          ▼
┌──────────────────────┐
│   STATUS_HISTORY     │
│──────────────────────│
│ • id (PK)            │
│ • complaint_id (FK)  │
│ • old_status         │
│ • new_status         │
│ • user_id (FK)       │
│ • created_at         │
└──────────────────────┘


┌──────────────────────┐
│      AFSPRAKEN       │
│──────────────────────│
│ • id (PK)            │
│ • user_id (FK)       │───┐
│ • dienst             │   │
│ • datum              │   │ N:1
│ • tijd               │   │
│ • opmerking          │   │
│ • status (ENUM)      │   │
│ • created_at         │   │
│ • updated_at         │   ▼
└──────────────────────┘   USERS
```

### 4.2 Relationships Uitleg

**1-to-Many (1:N):**
```php
// 1 User heeft meerdere Complaints
User → hasMany(Complaint)
Complaint → belongsTo(User)

// 1 Complaint heeft meerdere Attachments
Complaint → hasMany(Attachment)
Attachment → belongsTo(Complaint)
```

**Foreign Keys:**
```sql
-- Attachments tabel
FOREIGN KEY (complaint_id) REFERENCES complaints(id) ON DELETE CASCADE
-- Als complaint verwijderd wordt, ook attachments verwijderen

-- Complaints tabel
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
-- Als user verwijderd wordt, user_id wordt NULL (niet complaint verwijderen)
```

---

## 5. Security Implementaties

### 5.1 Authentication (Laravel Breeze)

**Login Flow:**

```
1. User gaat naar /login
   └─> Laravel Breeze login form

2. User submit credentials
   └─> POST /login
       ├─> CSRF token verificatie
       ├─> Email + password validatie
       ├─> Attempt login: Auth::attempt()
       │   └─> Password hash verificatie (bcrypt)
       │
       ├─> Success?
       │   ├─> Session aanmaken
       │   ├─> Remember token (optioneel)
       │   └─> Redirect naar /dashboard
       │
       └─> Failure?
           └─> Redirect terug met error
```

**Password Hashing:**
```php
// Registration
$user = User::create([
    'password' => Hash::make($request->password)
]);

// Login verificatie
if (Auth::attempt($credentials)) {
    // Success
}
```

### 5.2 Authorization (Policies & Gates)

**Middleware Protection:**

```php
// routes/admin.php
Route::middleware(['auth', 'can:admin'])->group(function () {
    // Alleen admins kunnen hier komen
});

// Controller
public function __construct()
{
    $this->middleware('auth');
    $this->middleware('can:admin')->except(['index', 'show']);
}
```

**Policy Authorization:**

```php
// Check in Controller
$this->authorize('update', $complaint);

// Check in Blade
@can('update', $complaint)
    <button>Bewerken</button>
@endcan

// Check in code
if ($user->can('update', $complaint)) {
    // ...
}
```

### 5.3 CSRF Protection

Elk formulier heeft CSRF token:

```blade
<form method="POST" action="/klachten/nieuw">
    @csrf
    <!-- Form fields -->
</form>
```

**Hoe werkt het:**

```
1. Laravel genereert uniek token per sessie
2. Token wordt in form hidden field geplaatst
3. Bij POST request verificeert Laravel token
4. Als token niet match → 419 error
```

**Disable voor API (in app/Http/Middleware/VerifyCsrfToken.php):**
```php
protected $except = [
    'api/*',
];
```

### 5.4 SQL Injection Prevention

**BAD (Vulnerable):**
```php
$id = $_GET['id'];
$user = DB::select("SELECT * FROM users WHERE id = $id");
// SQL Injection mogelijk!
```

**GOOD (Safe met Eloquent):**
```php
$user = User::find($id);
// Eloquent gebruikt prepared statements

// Of met Query Builder:
$user = DB::table('users')->where('id', $id)->first();
// Ook safe, Laravel bindt parameters
```

### 5.5 XSS Prevention

**Blade automatic escaping:**
```blade
{{ $user->name }}
<!-- Automatisch escaped, XSS safe -->

{!! $user->name !!}
<!-- NIET escaped, gebruik alleen voor trusted HTML -->
```

**Purify user input:**
```php
use Illuminate\Support\Str;

$clean = Str::of($input)->stripTags();
```

### 5.6 Rate Limiting (Throttle)

**Prevent brute force & DDoS:**

```php
// routes/web.php
Route::post('/api/chatbot', [ChatbotController::class, 'chat'])
    ->middleware('throttle:10,1');  // 10 requests per 1 minuut

// Custom rate limiter
RateLimiter::for('api', function (Request $request) {
    return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
});
```

### 5.7 File Upload Security

```php
public function uploadAttachment($file)
{
    // Validatie
    $validated = $request->validate([
        'file' => 'required|image|max:5120|mimes:jpeg,png,jpg,gif'
    ]);
    
    // Genereer veilige filename (niet original name!)
    $filename = uniqid() . '.' . $file->extension();
    
    // Store buiten public_html
    $path = $file->storeAs('complaints', $filename, 'private');
    
    // Resize image (voorkomt upload van 50MB foto)
    $image = Image::read($file)->scale(1920);
    
    return $path;
}
```

**Waarom veilig:**
- ✅ MIME type verificatie
- ✅ File size limiet
- ✅ Unieke filename (geen collisions)
- ✅ Stored buiten public folder
- ✅ Image resize (memory attack prevention)

---

## 6. API Endpoints

### 6.1 REST API Structuur

**Convention: RESTful URLs**

```
GET    /api/complaints         → Lijst alle klachten
POST   /api/complaints         → Maak nieuwe klacht
GET    /api/complaints/1       → Toon klacht #1
PUT    /api/complaints/1       → Update klacht #1
DELETE /api/complaints/1       → Verwijder klacht #1

GET    /api/complaints/1/notes → Lijst notes van klacht #1
POST   /api/complaints/1/notes → Voeg note toe aan klacht #1
```

### 6.2 Chatbot API

**Bestand:** `routes/api.php`

```php
Route::post('/chatbot', [ChatbotController::class, 'chat'])
    ->middleware('throttle:10,1')
    ->name('chatbot.chat');
```

**Request:**
```json
POST /api/chatbot
Content-Type: application/json

{
    "message": "Hoe kan ik een paspoort aanvragen?"
}
```

**Response (Success):**
```json
HTTP/1.1 200 OK
Content-Type: application/json

{
    "response": "Om een paspoort aan te vragen, kunt u...",
    "timestamp": "2025-11-10T14:30:00Z"
}
```

**Response (Error):**
```json
HTTP/1.1 429 Too Many Requests

{
    "message": "Too many requests. Please try again in 60 seconds.",
    "retry_after": 60
}
```

### 6.3 Response Format

**Success Response Structure:**
```json
{
    "status": "success",
    "data": {
        "id": 1,
        "title": "Klacht titel",
        "created_at": "2025-11-10T14:30:00Z"
    },
    "meta": {
        "version": "1.0",
        "timestamp": "2025-11-10T14:30:00Z"
    }
}
```

**Error Response Structure:**
```json
{
    "status": "error",
    "message": "Validation failed",
    "errors": {
        "title": ["The title field is required."],
        "email": ["The email must be a valid email address."]
    }
}
```

---

## 7. Modern CSS Architecture

### 7.1 CSS File Structure

```
resources/css/
├── design-tokens.css       # Variables (colors, spacing, fonts)
├── modern-components.css   # Reusable components
├── layout-system.css       # Grid, Flexbox layouts
├── animations.css          # View transitions, animations
├── dark-mode.css           # Dark mode support
└── gemeente-modern.css     # Entry point (imports all)
```

### 7.2 Design Tokens (CSS Variables)

**Bestand:** `resources/css/design-tokens.css`

```css
:root {
    /* Colors - OKLCH color space (better than RGB) */
    --color-primary: oklch(62% 0.25 264);      /* Blue */
    --color-success: oklch(70% 0.20 150);      /* Green */
    --color-error: oklch(60% 0.25 25);         /* Red */
    --color-warning: oklch(75% 0.20 75);       /* Orange */
    
    /* Grays */
    --color-gray-50: oklch(98% 0 0);
    --color-gray-900: oklch(20% 0 0);
    
    /* Typography */
    --font-sans: system-ui, -apple-system, sans-serif;
    --font-mono: 'SF Mono', Monaco, monospace;
    
    /* Fluid Typography (scales with viewport) */
    --font-size-xs: clamp(0.75rem, 0.7rem + 0.25vw, 0.875rem);
    --font-size-base: clamp(1rem, 0.9rem + 0.5vw, 1.125rem);
    --font-size-xl: clamp(1.25rem, 1.1rem + 0.75vw, 1.5rem);
    
    /* Spacing Scale (1.5 ratio) */
    --space-1: 0.25rem;  /* 4px */
    --space-2: 0.5rem;   /* 8px */
    --space-3: 0.75rem;  /* 12px */
    --space-4: 1rem;     /* 16px */
    --space-6: 1.5rem;   /* 24px */
    --space-8: 2rem;     /* 32px */
    
    /* Border Radius */
    --radius-sm: 0.25rem;
    --radius-md: 0.5rem;
    --radius-lg: 1rem;
    
    /* Shadows */
    --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
    --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
}
```

**Waarom OKLCH?**
- ✅ Perceptual uniform (hue blijft consistent bij brightness changes)
- ✅ Wider color gamut
- ✅ Makkelijker om color variants te maken

```css
/* Easy color variants */
--color-primary: oklch(62% 0.25 264);
--color-primary-light: oklch(72% 0.25 264);  /* Just increase lightness */
--color-primary-dark: oklch(52% 0.25 264);   /* Just decrease lightness */
```

### 7.3 Component Architecture

**Bestand:** `resources/css/modern-components.css`

```css
/* Button Component */
.btn {
    /* Base styles */
    padding: var(--space-3) var(--space-6);
    border-radius: var(--radius-md);
    font-weight: 600;
    border: none;
    cursor: pointer;
    
    /* Transitions */
    transition: all 0.2s ease;
    
    /* Hover state */
    &:hover {
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }
    
    /* Active state */
    &:active {
        transform: translateY(0);
    }
    
    /* Disabled state */
    &:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
}

/* Button variants */
.btn-primary {
    background: var(--color-primary);
    color: white;
    
    &:hover {
        background: oklch(from var(--color-primary) calc(l - 0.1) c h);
    }
}

.btn-secondary {
    background: transparent;
    color: var(--color-primary);
    border: 2px solid var(--color-primary);
}
```

**Modern CSS Features:**

1. **Nesting:**
```css
.btn {
    color: blue;
    
    &:hover {
        color: red;
    }
}
```

2. **Relative Colors:**
```css
.card {
    background: var(--color-primary);
    border: 1px solid oklch(from var(--color-primary) calc(l - 0.2) c h);
}
```

3. **Container Queries:**
```css
.card {
    container-type: inline-size;
}

@container (min-width: 400px) {
    .card-title {
        font-size: var(--font-size-xl);
    }
}
```

### 7.4 Layout System

**Bestand:** `resources/css/layout-system.css`

```css
/* Auto-responsive Grid */
.grid-auto {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(min(300px, 100%), 1fr));
    gap: var(--space-6);
}

/* Flexbox Utilities */
.flex-center {
    display: flex;
    align-items: center;
    justify-content: center;
}

.flex-between {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

/* Container */
.container {
    width: min(1200px, 100% - 2rem);
    margin-inline: auto;
}
```

**Waarom Modern?**
- ✅ `min()` functie → Responsive zonder media queries
- ✅ `auto-fit` grid → Automatisch wrap
- ✅ Logical properties (`margin-inline` vs `margin-left/right`)

### 7.5 Animations

**Bestand:** `resources/css/animations.css`

```css
/* View Transitions API */
@view-transition {
    navigation: auto;
}

::view-transition-old(root) {
    animation: fade-out 0.3s ease-out;
}

::view-transition-new(root) {
    animation: fade-in 0.3s ease-in;
}

@keyframes fade-out {
    to { opacity: 0; }
}

@keyframes fade-in {
    from { opacity: 0; }
}

/* Scroll animations */
@keyframes slide-up {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
}

.animate-on-scroll {
    animation: slide-up 0.6s ease-out;
    animation-timeline: view();
    animation-range: entry 0% cover 30%;
}
```

**View Transitions:**
- ✅ Native browser animations tussen page navigations
- ✅ Geen JavaScript nodig
- ✅ Smooth page transitions

---

## 8. Performance & Optimalisatie

### 8.1 Database Optimalisaties

**1. Indexes:**

```php
// Migration
Schema::create('complaints', function (Blueprint $table) {
    $table->index('status');      // Veel queries op status
    $table->index('category');    // Veel queries op category
    $table->index('created_at');  // Sorting op datum
});
```

**Impact:**
```
Without index: 1000ms query time (full table scan)
With index:    5ms query time (index lookup)
```

**2. Eager Loading (N+1 probleem oplossen):**

```php
// BAD: N+1 queries
$complaints = Complaint::all();  // 1 query
foreach ($complaints as $complaint) {
    echo $complaint->user->name;  // N queries (1 per complaint)
}
// Total: 1 + N queries

// GOOD: Eager loading
$complaints = Complaint::with('user')->get();  // 2 queries
foreach ($complaints as $complaint) {
    echo $complaint->user->name;  // No extra queries
}
// Total: 2 queries
```

**3. Pagination:**

```php
// BAD: Load all complaints
$complaints = Complaint::all();  // Could be 10,000 rows!

// GOOD: Paginate
$complaints = Complaint::paginate(20);  // Only 20 rows
```

### 8.2 Image Optimalisatie

```php
use Intervention\Image\Laravel\Facades\Image;

public function optimizeImage($file)
{
    $image = Image::read($file);
    
    // Resize (max 1920px width)
    $image->scale(width: 1920);
    
    // Optimize quality
    $image->encodeByMediaType(quality: 80);
    
    // Save
    $image->save($path);
}
```

**Impact:**
```
Original: 8MB (4000x3000px, quality 100)
Optimized: 500KB (1920x1440px, quality 80)
→ 94% kleiner, visueel identiek
```

### 8.3 Caching

```php
// Cache database query results
$stats = Cache::remember('dashboard.stats', now()->addHour(), function () {
    return [
        'total' => Complaint::count(),
        'open' => Complaint::where('status', 'open')->count(),
        'resolved' => Complaint::where('status', 'opgelost')->count(),
    ];
});

// Cache expensive computations
$categories = Cache::remember('complaint.categories', now()->addDay(), function () {
    return Complaint::select('category', DB::raw('count(*) as count'))
        ->groupBy('category')
        ->get();
});
```

**Cache invalidation:**
```php
// Wanneer nieuwe complaint wordt aangemaakt
Complaint::created(function () {
    Cache::forget('dashboard.stats');
});
```

### 8.4 Asset Optimalisatie

**Vite Build:**
```bash
npm run build
```

**Output:**
```
public/build/
├── assets/
│   ├── app.js (minified, tree-shaken)
│   └── app.css (minified, purged)
└── manifest.json
```

**Modern CSS Benefits:**
- ✅ Geen Tailwind utility classes in HTML (kleiner HTML)
- ✅ Reusable CSS components (kleiner CSS file)
- ✅ Native CSS features (geen polyfills)

---

## 9. Testing Strategie

### 9.1 Test Piramide

```
                   ┌──────┐
                   │  E2E │  ←少 (Browser tests)
                   └──────┘
                 ┌──────────┐
                 │ Feature  │  ← 中 (HTTP tests)
                 └──────────┘
               ┌──────────────┐
               │    Unit      │  ← 多 (Model/Service tests)
               └──────────────┘
```

### 9.2 Unit Tests

**Bestand:** `tests/Unit/ComplaintTest.php`

```php
<?php

use App\Models\Complaint;

test('complaint can mark as resolved', function () {
    $complaint = Complaint::factory()->create(['status' => 'open']);
    
    $complaint->markAsResolved();
    
    expect($complaint->status)->toBe('opgelost');
    expect($complaint->resolved_at)->not->toBeNull();
});

test('complaint status label is correct', function () {
    $complaint = Complaint::factory()->create(['status' => 'in_behandeling']);
    
    expect($complaint->status_label)->toBe('In Behandeling');
});
```

### 9.3 Feature Tests

**Bestand:** `tests/Feature/ComplaintSubmissionTest.php`

```php
<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('guest can submit complaint', function () {
    Storage::fake('public');
    
    $response = $this->post(route('complaint.store'), [
        'title' => 'Test Klacht',
        'description' => 'Test beschrijving',
        'category' => 'afval',
        'lat' => 52.3676,
        'lng' => 4.9041,
        'reporter_name' => 'Jan de Vries',
        'reporter_email' => 'jan@example.com',
        'attachments' => [
            UploadedFile::fake()->image('photo.jpg')
        ]
    ]);
    
    $response->assertRedirect(route('complaint.thanks'));
    
    $this->assertDatabaseHas('complaints', [
        'title' => 'Test Klacht',
        'status' => 'open',
    ]);
    
    $this->assertDatabaseHas('attachments', [
        'file_name' => 'photo.jpg'
    ]);
});

test('complaint requires valid data', function () {
    $response = $this->post(route('complaint.store'), [
        'title' => '',  // Empty (should fail)
        'description' => '',
    ]);
    
    $response->assertSessionHasErrors(['title', 'description']);
});
```

### 9.4 Test Coverage

**Run tests:**
```bash
php artisan test
php artisan test --coverage  # Show coverage
```

**Target coverage:**
- Controllers: 80%+
- Models: 90%+
- Services: 90%+

---

## 10. Deployment

### 10.1 Production Checklist

```bash
# 1. Environment
cp .env.example .env
php artisan key:generate

# 2. Dependencies
composer install --no-dev --optimize-autoloader
npm ci
npm run build

# 3. Database
php artisan migrate --force
php artisan db:seed --class=AdminSeeder

# 4. Cache & Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# 5. Storage
php artisan storage:link
chmod -R 775 storage bootstrap/cache

# 6. Queue Worker (optioneel)
php artisan queue:work --daemon
```

### 10.2 .env Production Settings

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://gemeente.nl

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gemeente_prod
DB_USERNAME=gemeente_user
DB_PASSWORD=strong_password_here

SESSION_DRIVER=database  # NOT file in production
QUEUE_CONNECTION=database

OPENAI_API_KEY=sk-proj-xxx
OPENAI_MODEL=gpt-4-turbo
```

### 10.3 Server Requirements

**Minimum:**
- PHP 8.3+
- MySQL 8.0+
- Composer 2.x
- Node.js 20+
- Nginx / Apache

**Recommended:**
- 2 CPU cores
- 4GB RAM
- 20GB SSD
- SSL Certificate (Let's Encrypt)

### 10.4 Nginx Config

```nginx
server {
    listen 80;
    server_name gemeente.nl;
    root /var/www/gemeente/public;
    
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    
    index index.php;
    
    charset utf-8;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## Conclusie

Dit document bevat **ALLE technische aspecten** van het Gemeente Portal project:

✅ **Architectuur** - MVC, Service Pattern, Observer Pattern
✅ **Database** - ERD, relaties, migrations
✅ **Security** - Auth, CSRF, XSS, SQL Injection prevention
✅ **API** - RESTful endpoints, Chatbot API
✅ **Modern CSS** - Design tokens, components, animations
✅ **Performance** - Database indexes, caching, image optimization
✅ **Testing** - Unit, Feature, Coverage strategie
✅ **Deployment** - Production setup, server config

**Gebruik dit voor:**
- 🎓 Technische uitleg bij presentatie
- 🐛 Troubleshooting & debugging
- 📈 Performance optimalisatie
- 🚀 Deployment naar productie

---

**Einde PDF 3 - Technische Documentatie**
