# Laravel Architectuur - Gemeente Portal

## 📋 Inhoudsopgave

1. [Project Overzicht](#project-overzicht)
2. [MVC Architectuur](#mvc-architectuur)
3. [Database Layer](#database-layer)
4. [Request Lifecycle](#request-lifecycle)
5. [Security & Middleware](#security--middleware)
6. [File Storage](#file-storage)

---

## 🏗️ Project Overzicht

### Laravel Versie
**Laravel 12.x** - Nieuwste LTS release met moderne features

### Project Structuur
```
app/
├── Http/
│   ├── Controllers/        # Request handlers
│   │   ├── Web/           # Publieke controllers
│   │   └── Admin/         # Admin controllers
│   ├── Requests/          # Form validation
│   └── Middleware/        # Request filters
├── Models/                # Eloquent ORM models
├── Policies/              # Authorization logic
└── Providers/             # Service providers

routes/
├── web.php               # Publieke routes
├── admin.php             # Admin routes
├── auth.php              # Authenticatie routes
└── api.php               # API endpoints

resources/
├── views/                # Blade templates
│   ├── layouts/         # Master layouts
│   ├── components/      # Herbruikbare componenten
│   ├── pages/           # Pagina views
│   └── admin/           # Admin views
└── js/                   # Frontend JavaScript

database/
├── migrations/           # Database schema
├── seeders/             # Data seeding
└── factories/           # Test data factories

config/
├── app.php              # App configuratie
├── database.php         # Database config
├── permission.php       # Spatie roles config
└── filesystems.php      # Storage config
```

---

## 🎯 MVC Architectuur

### Model-View-Controller Pattern

Laravel implementeert het MVC pattern voor scheiding van concerns:

```
Browser Request
      ↓
   Routes
      ↓
 Controller ←→ Model ←→ Database
      ↓
    View
      ↓
Browser Response
```

### 1. **Models** (Data Layer)

**Locatie:** `app/Models/`

Models vertegenwoordigen database tabellen en bevatten business logic.

#### Complaint Model
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Complaint extends Model
{
    // Mass-assignable velden (beveiliging tegen mass-assignment)
    protected $fillable = [
        'title',
        'description',
        'category',
        'priority',
        'status',
        'lat',
        'lng',
        'location',
        'reporter_name',
        'reporter_email',
        'reporter_phone',
        'internal_notes',
        'resolved_at',
        'assigned_to',
    ];

    // Type casting voor database velden
    protected $casts = [
        'resolved_at' => 'datetime',  // Automatisch Carbon instance
        'lat' => 'decimal:8',
        'lng' => 'decimal:8',
    ];

    // Relationships (Eloquent ORM magic)
    public function attachments(): HasMany
    {
        // 1 complaint heeft meerdere attachments
        return $this->hasMany(ComplaintImage::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(StatusHistory::class);
    }

    public function assignedTo(): BelongsTo
    {
        // Complaint wordt toegewezen aan 1 user
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // Accessor - automatisch beschikbaar als $complaint->tracking_number
    public function getTrackingNumberAttribute(): string
    {
        return sprintf('GEM-%05d', $this->id);
    }

    // Scope - herbruikbare query filters
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeWithLocation($query)
    {
        return $query->whereNotNull('lat')
                     ->whereNotNull('lng');
    }
}
```

**Waarom dit belangrijk is:**
- **Mass Assignment Protection:** `$fillable` voorkomt dat gebruikers onverwachte velden kunnen wijzigen
- **Type Casting:** `$casts` converteert automatisch strings naar juiste types (datetime, decimal)
- **Relationships:** Eloquent maakt joins en eager loading super eenvoudig
- **Accessors:** Virtuele attributen zonder extra database kolom
- **Scopes:** Herbruikbare query logica

### 2. **Controllers** (Application Logic)

**Locatie:** `app/Http/Controllers/`

Controllers ontvangen requests, roepen models aan en retourneren views.

#### ComplaintController (Publiek)
```php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Http\Requests\StoreComplaintRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ComplaintController extends Controller
{
    /**
     * Toon klacht formulier
     * Route: GET /complaints/create
     */
    public function create(): View
    {
        return view('pages.complaint-create');
    }

    /**
     * Verwerk nieuwe klacht
     * Route: POST /complaints
     */
    public function store(StoreComplaintRequest $request): RedirectResponse
    {
        // Request is al gevalideerd door StoreComplaintRequest
        
        // 1. Maak complaint in database
        $complaint = Complaint::create([
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'priority' => $request->priority ?? 'medium',
            'status' => 'open',
            'lat' => $request->lat,
            'lng' => $request->lng,
            'location' => $request->location,
            'reporter_name' => $request->reporter_name,
            'reporter_email' => $request->reporter_email,
            'reporter_phone' => $request->reporter_phone,
        ]);

        // 2. Upload en koppel foto's
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                // Store in storage/app/public/complaints/
                $path = $file->store('complaints', 'public');
                
                // Maak database record
                $complaint->attachments()->create([
                    'path' => $path,
                    'mime' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ]);
            }
        }

        // 3. Maak eerste status history record
        $complaint->statusHistories()->create([
            'from' => 'new',
            'to' => 'open',
            'user_id' => null, // Geen gebruiker (publiek)
        ]);

        // 4. Redirect met success message
        return redirect()
            ->route('complaint.track', [
                'id' => $complaint->id,
                'email' => $complaint->reporter_email
            ])
            ->with('success', 'Klacht succesvol ingediend!');
    }

    /**
     * Toon tracking pagina
     * Route: GET /klacht/track?id={id}&email={email}
     */
    public function track(): View
    {
        $id = request('id');
        $email = request('email');

        // Eager loading - haalt attachments in 1 query op (niet N+1)
        $complaint = Complaint::with('attachments')
            ->where('id', $id)
            ->where('reporter_email', $email)
            ->firstOrFail(); // 404 als niet gevonden

        return view('pages.complaint-track', compact('complaint'));
    }
}
```

**Controller Best Practices:**
- ✅ Gebruik Form Requests voor validatie (niet in controller)
- ✅ Gebruik Eloquent relationships (niet raw queries)
- ✅ Eager loading met `with()` om N+1 queries te voorkomen
- ✅ Named routes voor flexibiliteit
- ✅ Flash messages met `with()` voor gebruikersfeedback

#### Admin Dashboard Controller
```php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Middleware: alleen admins
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Dashboard home
     * Route: GET /admin/dashboard
     */
    public function index(): View
    {
        // Eager load relationships in 1 query
        $recentComplaints = Complaint::with(['assignedTo', 'attachments'])
            ->latest()
            ->take(5)
            ->get();

        // Statistieken met query aggregation
        $stats = [
            'total' => Complaint::count(),
            'open' => Complaint::where('status', 'open')->count(),
            'in_progress' => Complaint::where('status', 'in_progress')->count(),
            'resolved' => Complaint::whereIn('status', ['resolved', 'opgelost'])->count(),
        ];

        return view('admin.dashboard', compact('recentComplaints', 'stats'));
    }

    /**
     * AJAX: Haal kaart data op
     * Route: GET /admin/api/dashboard/map-data
     */
    public function mapData(Request $request): JsonResponse
    {
        $query = Complaint::query()
            ->whereNotNull('lat')
            ->whereNotNull('lng');

        // Filters (optioneel)
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        // Selecteer alleen nodige velden (performance)
        $complaints = $query->select([
            'id', 'title', 'description', 'status', 
            'priority', 'category', 'location', 
            'lat', 'lng', 'created_at'
        ])->get();

        return response()->json($complaints);
    }

    /**
     * AJAX: Zoek klacht op ID
     * Route: GET /admin/api/dashboard/search?id={id}
     */
    public function searchById(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id' => 'required|integer|exists:complaints,id',
        ]);

        $complaint = Complaint::with(['attachments', 'notes'])
            ->findOrFail($validated['id']);

        return response()->json([
            'success' => true,
            'data' => $complaint,
        ]);
    }
}
```

**API Best Practices:**
- ✅ Return `JsonResponse` voor API endpoints
- ✅ Valideer altijd input (zelfs van admin)
- ✅ Gebruik `select()` om alleen nodige velden op te halen
- ✅ Consistente response structuur (`success`, `data`, `error`)

### 3. **Views** (Presentation Layer)

**Locatie:** `resources/views/`

Blade templates met server-side rendering.

#### Master Layout
```blade
{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'Gemeente Portal') }}</title>
    
    {{-- Vite asset bundling --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- Custom CSS --}}
    <link rel="stylesheet" href="{{ asset('css/gemeente-modern.css') }}">
</head>
<body>
    {{-- Header component --}}
    @include('layouts.navigation')
    
    {{-- Flash messages --}}
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    
    {{-- Main content --}}
    <main>
        @yield('content')
    </main>
    
    {{-- Footer --}}
    @include('layouts.footer')
    
    {{-- Page-specific scripts --}}
    @stack('scripts')
</body>
</html>
```

#### Blade Syntax Uitleg
```blade
{{-- Variabelen escapen (XSS protection) --}}
<p>{{ $complaint->title }}</p>
{{-- Output: <p>Kapotte weg</p> --}}

{{-- Raw HTML (gebruik NOOIT met user input!) --}}
<div>{!! $trustedHtml !!}</div>

{{-- Conditionals --}}
@if($complaint->status === 'open')
    <span class="badge-red">Open</span>
@elseif($complaint->status === 'in_progress')
    <span class="badge-orange">In Behandeling</span>
@else
    <span class="badge-green">Opgelost</span>
@endif

{{-- Loops --}}
@foreach($complaints as $complaint)
    <tr>
        <td>{{ $complaint->id }}</td>
        <td>{{ $complaint->title }}</td>
    </tr>
@endforeach

{{-- Loop met empty check --}}
@forelse($complaints as $complaint)
    <li>{{ $complaint->title }}</li>
@empty
    <p>Geen klachten gevonden</p>
@endforelse

{{-- Authentication checks --}}
@auth
    <a href="{{ route('dashboard') }}">Dashboard</a>
@endauth

@guest
    <a href="{{ route('login') }}">Inloggen</a>
@endguest

{{-- Role checks (Spatie) --}}
@role('admin')
    <a href="{{ route('admin.dashboard') }}">Admin</a>
@endrole

{{-- CSRF token (altijd in forms!) --}}
<form method="POST" action="{{ route('complaint.store') }}">
    @csrf
    {{-- Genereert: <input type="hidden" name="_token" value="..."> --}}
</form>

{{-- Method spoofing (PUT, DELETE in forms) --}}
<form method="POST" action="{{ route('complaint.destroy', $complaint) }}">
    @csrf
    @method('DELETE')
    <button type="submit">Verwijderen</button>
</form>

{{-- Old input (na validatie errors) --}}
<input type="text" name="title" value="{{ old('title') }}">

{{-- Validation errors --}}
@error('title')
    <span class="error">{{ $message }}</span>
@enderror

{{-- Named routes (flexibel, wijzigt URL structuur niet) --}}
<a href="{{ route('complaint.show', $complaint->id) }}">Bekijk</a>
{{-- Genereert: /complaints/123 --}}

{{-- Route parameters --}}
<a href="{{ route('complaint.track', ['id' => $complaint->id, 'email' => $email]) }}">
    Track
</a>
{{-- Genereert: /klacht/track?id=123&email=test@example.com --}}

{{-- Asset URLs --}}
<img src="{{ asset('images/logo.png') }}">
{{-- Genereert: /images/logo.png --}}

<img src="{{ Storage::url($attachment->path) }}">
{{-- Genereert: /storage/complaints/abc123.jpg --}}

{{-- Include sub-views --}}
@include('partials.header', ['title' => 'Dashboard'])

{{-- Components (modern Blade) --}}
<x-alert type="success" message="Opgeslagen!" />

{{-- Slots voor flexible components --}}
<x-card>
    <x-slot:header>
        <h2>Titel</h2>
    </x-slot>
    
    <p>Card body content</p>
</x-card>
```

---

## 🗄️ Database Layer

### Eloquent ORM

Laravel's Eloquent is een ActiveRecord ORM - elk model komt overeen met 1 tabel.

#### Relationships Uitgelegd

**1. One-to-Many (1:N)**
```php
// Complaint Model
public function attachments(): HasMany
{
    return $this->hasMany(ComplaintImage::class);
}

// ComplaintImage Model  
public function complaint(): BelongsTo
{
    return $this->belongsTo(Complaint::class);
}

// Gebruik:
$complaint = Complaint::find(1);
$photos = $complaint->attachments; // Collection van ComplaintImage

$photo = ComplaintImage::find(1);
$complaint = $photo->complaint; // Single Complaint object
```

**2. Many-to-Many (N:M) - via Spatie**
```php
// User Model (via Spatie)
use Spatie\Permission\Traits\HasRoles;

class User extends Model
{
    use HasRoles;
}

// Gebruik:
$user->assignRole('admin');
$user->hasRole('admin'); // true
$user->roles; // Collection van Role models
```

**3. Eager Loading (Performance!)**
```php
// ❌ BAD - N+1 Query Problem
$complaints = Complaint::all(); // 1 query
foreach ($complaints as $complaint) {
    echo $complaint->assignedTo->name; // N extra queries!
}
// Totaal: 1 + N queries 😱

// ✅ GOOD - Eager Loading
$complaints = Complaint::with('assignedTo')->get(); // 2 queries
foreach ($complaints as $complaint) {
    echo $complaint->assignedTo->name; // Geen extra queries!
}
// Totaal: 2 queries 🎉
```

### Query Builder

```php
// Basic selects
Complaint::all(); // SELECT * FROM complaints
Complaint::where('status', 'open')->get();
Complaint::find(1); // SELECT * WHERE id = 1
Complaint::findOrFail(1); // 404 als niet gevonden

// Aggregates
Complaint::count(); // SELECT COUNT(*)
Complaint::where('status', 'open')->count();
Complaint::avg('priority');
Complaint::max('created_at');

// Ordering
Complaint::latest()->get(); // ORDER BY created_at DESC
Complaint::oldest()->get(); // ORDER BY created_at ASC
Complaint::orderBy('title', 'asc')->get();

// Limiting
Complaint::take(5)->get(); // LIMIT 5
Complaint::skip(10)->take(5)->get(); // OFFSET 10 LIMIT 5

// Joins
Complaint::join('users', 'complaints.assigned_to', '=', 'users.id')
    ->select('complaints.*', 'users.name as assigned_name')
    ->get();

// Where clauses
Complaint::where('status', 'open')->get();
Complaint::where('priority', '!=', 'low')->get();
Complaint::whereBetween('created_at', [$start, $end])->get();
Complaint::whereIn('status', ['open', 'in_progress'])->get();
Complaint::whereNull('assigned_to')->get();
Complaint::whereNotNull('lat')->get();

// Multiple wheres
Complaint::where('status', 'open')
    ->where('priority', 'high')
    ->whereNotNull('lat')
    ->get();

// Or conditions
Complaint::where('status', 'open')
    ->orWhere('status', 'in_progress')
    ->get();

// Advanced where
Complaint::where(function($query) {
    $query->where('priority', 'high')
          ->orWhere('priority', 'urgent');
})->where('status', 'open')->get();

// Select specific columns
Complaint::select('id', 'title', 'status')->get();

// Raw expressions (gebruik met voorzichtigheid!)
Complaint::selectRaw('COUNT(*) as count, status')
    ->groupBy('status')
    ->get();
```

### Migrations

Migrations zijn version control voor je database schema.

```php
// database/migrations/2025_09_22_091228_create_complaints_table.php
public function up(): void
{
    Schema::create('complaints', function (Blueprint $table) {
        $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
        $table->string('title'); // VARCHAR(255)
        $table->text('description'); // TEXT
        $table->string('category')->nullable(); // VARCHAR(255) NULL
        $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])
              ->default('open');
        $table->decimal('lat', 10, 8)->nullable(); // DECIMAL(10,8) NULL
        $table->decimal('lng', 11, 8)->nullable();
        $table->string('reporter_email'); // VARCHAR(255)
        $table->foreignId('assigned_to') // BIGINT UNSIGNED
              ->nullable()
              ->constrained('users') // FOREIGN KEY references users(id)
              ->nullOnDelete(); // ON DELETE SET NULL
        $table->timestamps(); // created_at, updated_at TIMESTAMP
    });
}

// Run migrations
php artisan migrate // Run alle pending migrations
php artisan migrate:fresh --seed // Drop alles en herstart
php artisan migrate:rollback // Undo laatste batch
```

---

## 🔄 Request Lifecycle

### Van Browser tot Response

```
1. Browser → HTTP Request
   ↓
2. public/index.php (Entry point)
   ↓
3. bootstrap/app.php (Laravel bootstrap)
   ↓
4. Kernel (HTTP or Console)
   ↓
5. Service Providers (register & boot)
   ↓
6. Routes → Match URL to Controller
   ↓
7. Middleware Stack (auth, csrf, etc.)
   ↓
8. Controller Action
   ↓
9. Model/Database Query
   ↓
10. View Rendering (Blade)
    ↓
11. Response → Browser
```

### Middleware Pipeline

Middleware zijn filters die requests verwerken.

```php
// app/Http/Kernel.php
protected $middleware = [
    \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
    \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
    \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
    \Illuminate\Session\Middleware\StartSession::class,
    \Illuminate\View\Middleware\ShareErrorsFromSession::class,
    \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
];

protected $routeMiddleware = [
    'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
    'role' => \Spatie\Permission\Middlewares\RoleMiddleware::class,
];
```

**Gebruik in routes:**
```php
// web.php
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
});

// admin.php
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index']);
});
```

### Form Request Validation

```php
// app/Http/Requests/StoreComplaintRequest.php
class StoreComplaintRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Iedereen mag klacht indienen
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255|min:3',
            'description' => 'required|string|max:2000|min:10',
            'category' => 'required|string|in:wegen_onderhoud,...',
            'reporter_email' => 'required|email:rfc|max:255',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|mimes:jpeg,jpg,png|max:10240',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Titel is verplicht.',
            'title.min' => 'Titel moet minimaal 3 tekens bevatten.',
            'attachments.*.max' => 'Elk bestand mag maximaal 10MB zijn.',
        ];
    }
}
```

**In Controller:**
```php
public function store(StoreComplaintRequest $request)
{
    // Request is automatisch gevalideerd!
    // Als validatie faalt → redirect met errors
    // Als validatie slaagt → code hieronder wordt uitgevoerd
    
    $complaint = Complaint::create($request->validated());
}
```

---

## 🔒 Security & Middleware

### CSRF Protection

Alle POST/PUT/DELETE requests vereisen CSRF token:

```blade
<form method="POST" action="/complaints">
    @csrf {{-- Generates hidden _token field --}}
    <!-- form fields -->
</form>
```

### XSS Protection

Blade escapet automatisch output:

```blade
{{ $complaint->title }} {{-- Safe: HTML escaped --}}
{!! $complaint->title !!} {{-- UNSAFE: Raw HTML --}}
```

### SQL Injection Protection

Eloquent en Query Builder gebruiken prepared statements:

```php
// ✅ SAFE - Parameter binding
Complaint::where('id', $id)->first();
DB::select('SELECT * FROM complaints WHERE id = ?', [$id]);

// ❌ UNSAFE - String concatenation
DB::select("SELECT * FROM complaints WHERE id = $id");
```

### Mass Assignment Protection

```php
class Complaint extends Model
{
    // Whitelist - alleen deze velden mogen mass-assigned worden
    protected $fillable = [
        'title',
        'description',
        'status',
    ];
    
    // Of blacklist
    protected $guarded = ['id', 'created_at'];
}

// ✅ SAFE - alleen fillable velden worden gezet
Complaint::create($request->all());

// ❌ UNSAFE zonder $fillable
// User zou 'admin' => true kunnen sturen!
```

### Authentication

```php
// Login gebruiker
Auth::login($user);

// Check of ingelogd
if (Auth::check()) {
    $user = Auth::user();
}

// Logout
Auth::logout();

// Middleware
Route::middleware('auth')->group(function () {
    // Alleen voor ingelogde gebruikers
});
```

### Authorization (Spatie Roles)

```php
// Assign role
$user->assignRole('admin');

// Check role
$user->hasRole('admin'); // true/false

// Check permission
$user->can('edit complaints');

// Middleware
Route::middleware('role:admin')->group(function () {
    // Alleen voor admins
});

// In Blade
@role('admin')
    <a href="/admin">Admin Panel</a>
@endrole
```

---

## 📁 File Storage

### Storage Disk Configuration

```php
// config/filesystems.php
'disks' => [
    'public' => [
        'driver' => 'local',
        'root' => storage_path('app/public'),
        'url' => env('APP_URL').'/storage',
        'visibility' => 'public',
    ],
],
```

### File Uploads

```php
// Controller
public function store(Request $request)
{
    if ($request->hasFile('photo')) {
        $file = $request->file('photo');
        
        // Store in storage/app/public/complaints/
        $path = $file->store('complaints', 'public');
        // Returns: complaints/abc123def456.jpg
        
        // Get file info
        $file->getClientOriginalName(); // user-uploaded-name.jpg
        $file->getSize(); // bytes
        $file->getMimeType(); // image/jpeg
        
        // Save to database
        $complaint->attachments()->create([
            'path' => $path,
            'mime' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);
    }
}
```

### Symlink Setup

```bash
# Create symbolic link: public/storage → storage/app/public
php artisan storage:link
```

### Retrieve Files

```php
// Get URL for stored file
Storage::url('complaints/abc123.jpg');
// Returns: /storage/complaints/abc123.jpg

// In Blade
<img src="{{ Storage::url($attachment->path) }}">

// Download file
return Storage::download($attachment->path);

// Delete file
Storage::delete($attachment->path);
```

---

## 🎯 Best Practices Samenvatting

### ✅ DO's
1. **Gebruik Form Requests** voor validatie
2. **Eager load relationships** om N+1 queries te voorkomen
3. **Named routes** gebruiken (niet hardcoded URLs)
4. **CSRF tokens** in alle forms
5. **Escape output** met `{{ }}` (niet `{!! !!}`)
6. **Mass assignment protection** met `$fillable`
7. **Middleware** voor authenticatie en autorisatie
8. **Eloquent relationships** (niet manual joins)
9. **Database transactions** voor multi-step operations
10. **Environment variables** voor configuratie

### ❌ DON'Ts
1. **Geen raw SQL** zonder parameter binding
2. **Geen business logic in views**
3. **Geen user input in raw queries**
4. **Geen sensitive data in Git**
5. **Geen database queries in loops** (N+1)
6. **Geen hardcoded configuration**
7. **Geen mass assignment zonder guards**

---

**Volgende documenten:**
- [Laravel Routes & Controllers](LARAVEL_ROUTES_CONTROLLERS.md)
- [Laravel Database & Eloquent](LARAVEL_DATABASE.md)
- [Laravel Security & Auth](LARAVEL_SECURITY.md)
