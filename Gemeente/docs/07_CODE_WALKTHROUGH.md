# 🎓 Code Walkthrough - Begrijp Hoe Alles Werkt

**Onderwerp:** Praktische uitleg van code, logica en architectuur  
**Datum:** 6 oktober 2025  
**Voor:** Developers die de code willen begrijpen

---

## 📋 Inhoudsopgave

1. [Hoe een Request Werkt](#hoe-een-request-werkt)
2. [Admin Views Uitgelegd](#admin-views-uitgelegd)
3. [Database Relaties](#database-relaties)
4. [Authentication Flow](#authentication-flow)
5. [File Upload Mechanisme](#file-upload-mechanisme)
6. [API vs Web Routes](#api-vs-web-routes)
7. [Middleware Werking](#middleware-werking)
8. [Map Functionaliteit](#map-functionaliteit)
9. [Common Patterns](#common-patterns)

---

## 🌊 Hoe een Request Werkt

### Van URL naar Response - Complete Flow

**Scenario:** Gebruiker gaat naar `http://gemeente.test/klachten/1`

```
🌐 Browser Request
    ↓
📝 routes/web.php - Match route
    ↓
🛡️ Middleware (auth, csrf, etc.)
    ↓
🎮 Controller Method
    ↓
📊 Database Query via Model
    ↓
🎨 View (Blade Template)
    ↓
✨ HTML Response naar Browser
```

### Praktisch Voorbeeld

**1. Route Definitie (`routes/web.php`):**
```php
// Deze route matcht: /klachten/1, /klachten/2, etc.
Route::get('/klachten/{id}', [ComplaintController::class, 'show'])
    ->name('complaints.show');

// {id} = route parameter
// name('complaints.show') = route naam voor gebruik in views
```

**2. Controller Method (`app/Http/Controllers/ComplaintController.php`):**
```php
class ComplaintController extends Controller
{
    public function show($id)  // $id komt van {id} in route
    {
        // 1. Haal klacht op uit database
        $complaint = Complaint::with(['photos', 'notes', 'user'])
            ->findOrFail($id);  // findOrFail = 404 als niet gevonden
        
        // 2. Return view met data
        return view('complaints.show', [
            'complaint' => $complaint
        ]);
        
        // Dit zoekt: resources/views/complaints/show.blade.php
    }
}
```

**3. Model Query (`app/Models/Complaint.php`):**
```php
// Eloquent maakt dit:
Complaint::with(['photos', 'notes'])->findOrFail(1);

// Naar deze SQL:
SELECT * FROM complaints WHERE id = 1;
SELECT * FROM photos WHERE complaint_id = 1;
SELECT * FROM notes WHERE complaint_id = 1;
```

**4. View Template (`resources/views/complaints/show.blade.php`):**
```blade
@extends('layouts.app')  {{-- Gebruik de base layout --}}

@section('content')
    <h1>{{ $complaint->title }}</h1>  {{-- Data van controller --}}
    <p>{{ $complaint->description }}</p>
    
    {{-- Loop door foto's --}}
    @foreach($complaint->photos as $photo)
        <img src="{{ $photo->url }}" alt="Foto">
    @endforeach
@endsection
```

**5. Final HTML:**
```html
<!DOCTYPE html>
<html>
<head>
    <title>Gemeente Portal</title>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>
    <h1>Kapotte straatlantaarn</h1>
    <p>De lantaarn bij Hoofdstraat 123 werkt niet...</p>
    <img src="/storage/complaints/photo1.jpg" alt="Foto">
</body>
</html>
```

---

## 👑 Admin Views Uitgelegd

### Wat zijn Admin Views?

Admin views zijn **speciale pagina's alleen voor beheerders** om het systeem te beheren.

### Directory Structuur

```
resources/views/
├── layouts/
│   ├── app.blade.php          # Normale gebruikers layout
│   └── admin.blade.php         # Admin layout (sidebar, admin menu)
├── complaints/
│   ├── index.blade.php         # Publieke klachten lijst
│   └── show.blade.php          # Publieke klacht detail
└── admin/
    ├── dashboard.blade.php     # Admin dashboard (grafieken, stats)
    ├── complaints/
    │   ├── index.blade.php     # Admin klachten beheer
    │   ├── edit.blade.php      # Klacht bewerken
    │   └── assign.blade.php    # Klacht toewijzen
    └── users/
        ├── index.blade.php     # Gebruikers beheer
        └── edit.blade.php      # Gebruiker bewerken
```

### Admin Layout vs Normale Layout

**Normale Layout (`layouts/app.blade.php`):**
```blade
<!DOCTYPE html>
<html>
<head>
    <title>Gemeente Portal</title>
</head>
<body>
    {{-- Simpele header --}}
    <header>
        <nav>
            <a href="/">Home</a>
            <a href="/klachten">Klachten</a>
            <a href="/contact">Contact</a>
        </nav>
    </header>
    
    {{-- Content van child views --}}
    <main>
        @yield('content')
    </main>
</body>
</html>
```

**Admin Layout (`layouts/admin.blade.php`):**
```blade
<!DOCTYPE html>
<html>
<head>
    <title>Admin - Gemeente Portal</title>
</head>
<body class="admin-panel">
    {{-- Sidebar met admin menu --}}
    <aside class="sidebar">
        <div class="logo">Admin Panel</div>
        <nav>
            <a href="/admin/dashboard">📊 Dashboard</a>
            <a href="/admin/klachten">📝 Klachten Beheren</a>
            <a href="/admin/gebruikers">👥 Gebruikers</a>
            <a href="/admin/statistieken">📈 Statistieken</a>
            <a href="/admin/instellingen">⚙️ Instellingen</a>
        </nav>
    </aside>
    
    {{-- Main content area --}}
    <main class="admin-content">
        {{-- Breadcrumbs --}}
        <div class="breadcrumbs">
            @yield('breadcrumbs')
        </div>
        
        {{-- Page title --}}
        <h1>@yield('title')</h1>
        
        {{-- Content --}}
        @yield('content')
    </main>
</body>
</html>
```

### Admin Dashboard Voorbeeld

**Route (`routes/admin.php`):**
```php
// Alleen toegankelijk voor admins
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])
        ->name('admin.dashboard');
});
```

**Controller (`app/Http/Controllers/AdminController.php`):**
```php
class AdminController extends Controller
{
    public function dashboard()
    {
        // Verzamel statistieken
        $stats = [
            'total_complaints' => Complaint::count(),
            'open_complaints' => Complaint::where('status', 'open')->count(),
            'today_complaints' => Complaint::whereDate('created_at', today())->count(),
            'avg_resolution_time' => Complaint::where('status', 'afgerond')
                ->avg(DB::raw('DATEDIFF(updated_at, created_at)'))
        ];
        
        // Recent complaints
        $recent = Complaint::with('user')
            ->latest()
            ->take(10)
            ->get();
        
        return view('admin.dashboard', compact('stats', 'recent'));
    }
}
```

**View (`resources/views/admin/dashboard.blade.php`):**
```blade
@extends('layouts.admin')  {{-- Gebruik admin layout --}}

@section('title', 'Dashboard')

@section('breadcrumbs')
    Home / Dashboard
@endsection

@section('content')
    {{-- Statistics Cards --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">{{ $stats['total_complaints'] }}</div>
            <div class="stat-label">Totaal Klachten</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value">{{ $stats['open_complaints'] }}</div>
            <div class="stat-label">Open Klachten</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value">{{ $stats['today_complaints'] }}</div>
            <div class="stat-label">Vandaag</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value">{{ number_format($stats['avg_resolution_time'], 1) }} dagen</div>
            <div class="stat-label">Gem. Oplostijd</div>
        </div>
    </div>
    
    {{-- Recent Complaints Table --}}
    <div class="card">
        <h2>Recente Klachten</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Titel</th>
                    <th>Status</th>
                    <th>Ingediend door</th>
                    <th>Datum</th>
                    <th>Acties</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recent as $complaint)
                    <tr>
                        <td>#{{ $complaint->id }}</td>
                        <td>{{ $complaint->title }}</td>
                        <td>
                            <span class="badge badge-{{ $complaint->status }}">
                                {{ $complaint->status_label }}
                            </span>
                        </td>
                        <td>{{ $complaint->user->name }}</td>
                        <td>{{ $complaint->created_at->format('d-m-Y H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.complaints.show', $complaint) }}" 
                               class="btn btn-sm">
                                Bekijken
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
```

### Hoe Routes Protected Worden

**Middleware Check (`app/Http/Middleware/AdminMiddleware.php`):**
```php
class AdminMiddleware
{
    public function handle($request, Closure $next)
    {
        // 1. Check of gebruiker ingelogd is
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Log eerst in');
        }
        
        // 2. Check of gebruiker admin is
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Je hebt geen toegang tot deze pagina');
        }
        
        // 3. Alles OK, ga door
        return $next($request);
    }
}
```

**Gebruik in Routes:**
```php
// Deze routes zijn protected
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard']);
    Route::get('/klachten', [AdminController::class, 'complaints']);
    Route::post('/klachten/{id}/assign', [AdminController::class, 'assign']);
});
```

---

## 🔗 Database Relaties

### Hoe Relaties Werken

**Database Structuur:**
```
complaints (klachten)
├── id
├── title
├── user_id  ← foreign key naar users
└── assigned_to  ← foreign key naar users

photos (foto's)
├── id
├── complaint_id  ← foreign key naar complaints
└── path

notes (notities)
├── id
├── complaint_id  ← foreign key naar complaints
├── user_id  ← foreign key naar users
└── note
```

### Model Relaties

**Complaint Model:**
```php
class Complaint extends Model
{
    // 1. Één klacht heeft één melder (user)
    // complaints.user_id → users.id
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    // 2. Één klacht kan aan één admin toegewezen zijn
    // complaints.assigned_to → users.id
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
    
    // 3. Één klacht kan meerdere foto's hebben
    // photos.complaint_id → complaints.id
    public function photos()
    {
        return $this->hasMany(Photo::class);
    }
    
    // 4. Één klacht kan meerdere notities hebben
    // notes.complaint_id → complaints.id
    public function notes()
    {
        return $this->hasMany(Note::class);
    }
}
```

**Gebruik in Code:**
```php
// Haal klacht op met alles
$complaint = Complaint::with(['user', 'photos', 'notes', 'assignedUser'])
    ->find(1);

// Toegang tot relaties
echo $complaint->user->name;              // "Jan Jansen"
echo $complaint->assignedUser->name;       // "Admin User"
echo $complaint->photos->count();          // 3
echo $complaint->photos->first()->url;     // "http://..."

// Loop door relaties
foreach ($complaint->notes as $note) {
    echo $note->note;                      // "Monteur onderweg"
    echo $note->user->name;                // "Admin"
}
```

### SQL Queries Achter de Schermen

**Zonder Eager Loading (N+1 Problem):**
```php
$complaints = Complaint::all();  // 1 query

foreach ($complaints as $complaint) {
    echo $complaint->user->name;  // +1 query per complaint!
}
// Total: 1 + 100 = 101 queries! 😱
```

**Met Eager Loading (Efficient):**
```php
$complaints = Complaint::with('user')->get();  // 2 queries

foreach ($complaints as $complaint) {
    echo $complaint->user->name;  // Geen extra queries!
}
// Total: 2 queries! ✅
```

**SQL Queries:**
```sql
-- Query 1: Haal alle klachten
SELECT * FROM complaints;

-- Query 2: Haal alle users voor deze klachten
SELECT * FROM users 
WHERE id IN (1, 2, 3, 4, 5, ...);

-- Laravel matcht ze automatisch!
```

---

## 🔐 Authentication Flow

### Hoe Login Werkt

**Login Form → Authentication → Session → Protected Routes**

**1. Login Form (`resources/views/auth/login.blade.php`):**
```blade
<form method="POST" action="{{ route('login') }}">
    @csrf  {{-- CSRF token voor beveiliging --}}
    
    <input type="email" name="email" required>
    <input type="password" name="password" required>
    <button type="submit">Login</button>
</form>
```

**2. Login Route (`routes/auth.php`):**
```php
Route::post('/login', [AuthController::class, 'login'])->name('login');
```

**3. Login Controller:**
```php
class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. Valideer input
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        
        // 2. Probeer in te loggen
        if (Auth::attempt($credentials)) {
            // Success! Regenereer session voor security
            $request->session()->regenerate();
            
            // 3. Check rol en redirect
            if (Auth::user()->hasRole('admin')) {
                return redirect('/admin/dashboard');
            }
            
            return redirect('/dashboard');
        }
        
        // 4. Fout: verkeerde credentials
        return back()->withErrors([
            'email' => 'Deze combinatie is niet correct.'
        ]);
    }
}
```

**4. Wat gebeurt er achter de schermen:**
```php
// Laravel doet dit automatisch:

// A. Hash password check
$user = User::where('email', $request->email)->first();

if (Hash::check($request->password, $user->password)) {
    // B. Start session
    session(['auth_user_id' => $user->id]);
    
    // C. Zet Auth facade
    Auth::setUser($user);
    
    // D. Success!
}
```

**5. Protected Route Check:**
```php
// In middleware
if (Auth::check()) {
    // User is ingelogd, ga door
    $user = Auth::user();
} else {
    // Niet ingelogd, redirect naar login
    return redirect('/login');
}
```

### Session Werking

```
1. Login → Laravel genereert session ID
2. Session ID wordt opgeslagen in cookie
3. Bij volgende request: Browser stuurt cookie mee
4. Laravel herkent session → Auth::user() werkt!
```

**Session Data:**
```php
// Opslaan
session(['key' => 'value']);

// Ophalen
$value = session('key');

// Auth user in session
$userId = session('auth_user_id');
$user = User::find($userId);
```

---

## 📤 File Upload Mechanisme

### Hoe Photo Upload Werkt

**Complete Flow:**
```
📁 User selecteert bestand
    ↓
📤 Form POST naar server
    ↓
🔍 Validatie (type, grootte)
    ↓
💾 Opslaan in storage/
    ↓
📊 Record in database
    ↓
🖼️ Weergeven in view
```

### Upload Form

```blade
<form method="POST" action="{{ route('complaints.store') }}" enctype="multipart/form-data">
    @csrf
    
    <input type="text" name="title" required>
    <textarea name="description" required></textarea>
    
    {{-- Multiple file upload --}}
    <input type="file" 
           name="photos[]" 
           multiple 
           accept="image/*">
    
    <button type="submit">Indienen</button>
</form>
```

**Let op:**
- `enctype="multipart/form-data"` = **verplicht voor file uploads**
- `name="photos[]"` = array voor multiple files
- `accept="image/*"` = alleen images

### Controller Logic

```php
class ComplaintController extends Controller
{
    public function store(Request $request)
    {
        // 1. Valideer files
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'photos' => 'nullable|array|max:5',  // Max 5 foto's
            'photos.*' => 'image|mimes:jpg,jpeg,png|max:5120'  // Max 5MB per foto
        ]);
        
        // 2. Maak klacht aan
        $complaint = Complaint::create([
            'title' => $request->title,
            'description' => $request->description,
            'user_id' => Auth::id(),
        ]);
        
        // 3. Verwerk foto's
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                // A. Opslaan in storage/app/public/complaints/
                $path = $photo->store('complaints', 'public');
                
                // B. Database record
                Photo::create([
                    'complaint_id' => $complaint->id,
                    'path' => $path,
                    'original_name' => $photo->getClientOriginalName()
                ]);
            }
        }
        
        return redirect()->route('complaints.show', $complaint)
            ->with('success', 'Klacht ingediend!');
    }
}
```

### Wat gebeurt er bij `$photo->store()`?

```php
$photo->store('complaints', 'public');

// Dit doet Laravel:
// 1. Genereer unieke filename
$filename = Str::random(40) . '.' . $photo->extension();
// Bijv: "aB3dF7h9J2k4M6n8P0q1R3s5T7u9.jpg"

// 2. Sla op in storage/app/public/complaints/
$fullPath = storage_path('app/public/complaints/' . $filename);
move_uploaded_file($photo->getRealPath(), $fullPath);

// 3. Return path voor database
return 'complaints/' . $filename;
```

### Symlink voor Public Access

```bash
# Maak symlink: public/storage → storage/app/public
php artisan storage:link
```

**Nu werken deze URLs:**
```
storage/app/public/complaints/abc123.jpg
↓
public/storage/complaints/abc123.jpg
↓
http://gemeente.test/storage/complaints/abc123.jpg ✅
```

### Photo Model

```php
class Photo extends Model
{
    protected $fillable = ['complaint_id', 'path', 'original_name'];
    
    // Accessor: krijg volledige URL
    public function getUrlAttribute()
    {
        return asset('storage/' . $this->path);
        // asset() genereert: http://gemeente.test/storage/...
    }
    
    // Relatie naar klacht
    public function complaint()
    {
        return $this->belongsTo(Complaint::class);
    }
}
```

### Weergeven in View

```blade
@foreach($complaint->photos as $photo)
    <div class="photo">
        <img src="{{ $photo->url }}" 
             alt="{{ $photo->original_name }}">
             
        {{-- Delete button --}}
        <form method="POST" action="{{ route('photos.destroy', $photo) }}">
            @csrf
            @method('DELETE')
            <button type="submit">Verwijderen</button>
        </form>
    </div>
@endforeach
```

---

## 🛣️ API vs Web Routes

### Verschil tussen Web en API Routes

**Web Routes (`routes/web.php`):**
- Voor browser requests
- Sessie gebaseerd
- CSRF protection
- Return HTML views

**API Routes (`routes/api.php`):**
- Voor externe applicaties (Mobile apps, JavaScript)
- Stateless (geen sessies)
- Token authentication
- Return JSON

### Web Route Voorbeeld

```php
// routes/web.php
Route::get('/klachten', [ComplaintController::class, 'index'])
    ->name('complaints.index');
```

**Controller:**
```php
public function index()
{
    $complaints = Complaint::paginate(20);
    
    // Return Blade view (HTML)
    return view('complaints.index', compact('complaints'));
}
```

**Response:**
```html
<!DOCTYPE html>
<html>
<body>
    <h1>Klachten</h1>
    <ul>
        <li>Klacht 1</li>
        <li>Klacht 2</li>
    </ul>
</body>
</html>
```

### API Route Voorbeeld

```php
// routes/api.php (automatisch /api/ prefix)
Route::get('/complaints', [ComplaintApiController::class, 'index']);
```

**Controller:**
```php
public function index()
{
    $complaints = Complaint::paginate(20);
    
    // Return JSON
    return response()->json([
        'success' => true,
        'data' => $complaints
    ]);
}
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Kapotte lantaarn",
      "status": "open"
    },
    {
      "id": 2,
      "title": "Losliggende tegel",
      "status": "in_behandeling"
    }
  ]
}
```

### CSRF Protection

**Web Routes:**
```blade
{{-- CSRF token VERPLICHT bij POST/PUT/PATCH/DELETE --}}
<form method="POST" action="/klachten">
    @csrf  {{-- Genereert hidden input met token --}}
    <button type="submit">Opslaan</button>
</form>
```

**API Routes:**
```javascript
// Geen CSRF token nodig!
fetch('http://gemeente.test/api/complaints', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Authorization': 'Bearer ' + token  // Token auth in plaats van session
  },
  body: JSON.stringify(data)
});
```

---

## 🛡️ Middleware Werking

### Wat is Middleware?

Middleware = **filter tussen request en controller**

```
Request → Middleware 1 → Middleware 2 → Controller
                ↓               ↓
             Check 1         Check 2
```

### Built-in Middleware

```php
// app/Http/Kernel.php
protected $middlewareGroups = [
    'web' => [
        \App\Http\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \App\Http\Middleware\VerifyCsrfToken::class,  // CSRF check
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
    ],
    
    'api' => [
        'throttle:60,1',  // Rate limiting: 60 per minute
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
    ],
];
```

### Custom Middleware: AdminMiddleware

```php
class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // 1. Check voor middleware logica
        if (!Auth::check()) {
            // Niet ingelogd
            return redirect('/login');
        }
        
        if (!Auth::user()->hasRole('admin')) {
            // Geen admin
            abort(403, 'Geen toegang');
        }
        
        // 2. Alles OK, ga door naar volgende middleware/controller
        return $next($request);
        
        // 3. Na response kun je ook dingen doen:
        // $response = $next($request);
        // Log::info('Admin accessed: ' . $request->path());
        // return $response;
    }
}
```

### Middleware Toepassen

**Op specifieke route:**
```php
Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])
    ->middleware('admin');
```

**Op route group:**
```php
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
    Route::get('/admin/users', [AdminController::class, 'users']);
    Route::get('/admin/settings', [AdminController::class, 'settings']);
});
```

**In controller constructor:**
```php
class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
        $this->middleware('verified')->only('sensitive');
    }
}
```

### Middleware met Parameters

```php
// Middleware
class CheckRole
{
    public function handle($request, Closure $next, $role)
    {
        if (!Auth::user()->hasRole($role)) {
            abort(403);
        }
        return $next($request);
    }
}

// Route
Route::get('/admin', [AdminController::class, 'index'])
    ->middleware('role:admin');
    
Route::get('/moderator', [ModeratorController::class, 'index'])
    ->middleware('role:moderator');
```

---

## 🗺️ Map Functionaliteit

### Hoe de Interactive Map Werkt

**Leaflet.js Integration:**

```javascript
// 1. Initialize map
const map = L.map('map').setView([52.3777, 4.9010], 12);

// 2. Add OpenStreetMap tiles
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(map);

// 3. Fetch complaints data
async function loadComplaints() {
    const response = await fetch('/api/complaints/map?status=open');
    const data = await response.json();
    
    // 4. Add markers
    data.data.forEach(complaint => {
        const marker = L.marker([complaint.latitude, complaint.longitude], {
            icon: getMarkerIcon(complaint.status, complaint.priority)
        }).addTo(map);
        
        // 5. Add popup
        marker.bindPopup(createPopupContent(complaint));
    });
}

// 6. Custom marker icons based on status
function getMarkerIcon(status, priority) {
    const colors = {
        'open': '#EF4444',           // Rood
        'in_behandeling': '#F59E0B', // Oranje
        'afgerond': '#10B981',       // Groen
        'gesloten': '#6B7280'        // Grijs
    };
    
    const sizes = {
        'urgent': 40,
        'hoog': 35,
        'normaal': 30,
        'laag': 25
    };
    
    return L.divIcon({
        className: 'custom-marker',
        html: `<div style="
            background: ${colors[status]};
            width: ${sizes[priority]}px;
            height: ${sizes[priority]}px;
            border-radius: 50%;
            border: 3px solid white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        "></div>`,
        iconSize: [sizes[priority], sizes[priority]],
        iconAnchor: [sizes[priority]/2, sizes[priority]/2]
    });
}

// 7. Popup content
function createPopupContent(complaint) {
    return `
        <div class="complaint-popup">
            <h3>${complaint.title}</h3>
            <p><strong>Status:</strong> ${complaint.status}</p>
            <p><strong>Prioriteit:</strong> ${complaint.priority}</p>
            <p><strong>Adres:</strong> ${complaint.address}</p>
            <a href="/klachten/${complaint.id}">Bekijk Details →</a>
        </div>
    `;
}

// 8. Auto-refresh every 30 seconds
setInterval(loadComplaints, 30000);
```

### Backend: Map Data Endpoint

```php
// Controller
public function mapData(Request $request)
{
    // Start query
    $query = Complaint::query()
        ->whereNotNull('latitude')
        ->whereNotNull('longitude');
    
    // Filter op status
    if ($request->has('status')) {
        $query->where('status', $request->status);
    }
    
    // Filter op bounds (geografisch gebied)
    if ($request->has('bounds')) {
        [$latMin, $lngMin, $latMax, $lngMax] = explode(',', $request->bounds);
        $query->whereBetween('latitude', [$latMin, $latMax])
              ->whereBetween('longitude', [$lngMin, $lngMax]);
    }
    
    // Get data
    $complaints = $query->get([
        'id', 'title', 'category', 'status', 'priority',
        'latitude', 'longitude', 'address', 'created_at'
    ]);
    
    return response()->json([
        'success' => true,
        'data' => $complaints,
        'count' => $complaints->count()
    ]);
}
```

### Geocoding: Address → GPS

```javascript
// Gebruik OpenStreetMap Nominatim API
async function geocodeAddress(address) {
    const response = await fetch(
        `https://nominatim.openstreetmap.org/search?` +
        `format=json&q=${encodeURIComponent(address)}&countrycodes=nl`
    );
    
    const results = await response.json();
    
    if (results.length > 0) {
        return {
            latitude: results[0].lat,
            longitude: results[0].lon
        };
    }
    
    return null;
}

// Gebruik in form
document.getElementById('address').addEventListener('blur', async function() {
    const address = this.value;
    const coords = await geocodeAddress(address);
    
    if (coords) {
        document.getElementById('latitude').value = coords.latitude;
        document.getElementById('longitude').value = coords.longitude;
        
        // Update preview map
        updateMapPreview(coords.latitude, coords.longitude);
    }
});
```

---

## 🔄 Common Patterns

### Pattern 1: Flash Messages

```php
// Controller
return redirect()->route('complaints.index')
    ->with('success', 'Klacht succesvol ingediend!');
```

```blade
{{-- View --}}
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
```

### Pattern 2: Form Validation Error Display

```blade
{{-- Show all errors --}}
@if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Show specific field error --}}
<input type="email" name="email" value="{{ old('email') }}">
@error('email')
    <span class="error">{{ $message }}</span>
@enderror
```

### Pattern 3: Pagination

```php
// Controller
$complaints = Complaint::paginate(20);

// View
{{ $complaints->links() }}  // Generates pagination UI
```

### Pattern 4: Query Scopes

```php
// Model
public function scopeOpen($query)
{
    return $query->where('status', 'open');
}

public function scopeUrgent($query)
{
    return $query->where('priority', 'urgent');
}

// Usage
Complaint::open()->urgent()->get();
// SELECT * FROM complaints WHERE status = 'open' AND priority = 'urgent'
```

### Pattern 5: Resource Controllers

```php
Route::resource('complaints', ComplaintController::class);
```

**Genereert automatisch:**
```php
GET     /complaints              index()   - Lijst
GET     /complaints/create       create()  - Form voor nieuwe
POST    /complaints              store()   - Opslaan nieuwe
GET     /complaints/{id}         show()    - Detail view
GET     /complaints/{id}/edit    edit()    - Edit form
PUT     /complaints/{id}         update()  - Update opslaan
DELETE  /complaints/{id}         destroy() - Verwijderen
```

---

## 🎯 Quick Reference

### Vaak Gebruikte Blade Directives

```blade
{{-- Output --}}
{{ $variable }}              Escaped output (veilig)
{!! $html !!}               Raw output (opgelet: XSS!)
@json($array)               JSON output voor JavaScript

{{-- Control Structures --}}
@if($condition)             If statement
@elseif($condition)         Else if
@else                       Else
@endif

@foreach($items as $item)   Loop
@endforeach

@forelse($items as $item)   Loop met empty check
@empty
@endforelse

{{-- Authentication --}}
@auth                       Als ingelogd
@guest                      Als niet ingelogd
@can('edit', $post)         Als permission

{{-- Includes --}}
@extends('layouts.app')     Use layout
@section('content')         Define section
@yield('content')           Show section
@include('partials.menu')   Include file

{{-- Forms --}}
@csrf                       CSRF token
@method('PUT')              HTTP method spoofing
```

### Vaak Gebruikte Eloquent Queries

```php
// Find
Model::find(1)                          // By ID
Model::findOrFail(1)                    // By ID or 404
Model::where('status', 'open')->get()   // With condition

// Create
Model::create(['title' => 'Test'])

// Update
$model->update(['title' => 'New'])

// Delete
$model->delete()

// Relations
Model::with('relation')->get()          // Eager load
$model->relation()->create([...])       // Create related

// Aggregate
Model::count()
Model::sum('column')
Model::avg('column')
Model::max('column')
```

---

**✨ Nu snap je hoe alles werkt! Voor meer details, check de andere documentatie bestanden.**
