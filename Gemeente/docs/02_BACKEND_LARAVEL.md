# 🔧 Laravel Backend - Complete Code Uitleg

**Onderwerp:** Laravel Backend Architectuur & Implementatie  
**Datum:** 6 oktober 2025

---

## 📋 Inhoudsopgave

1. [Laravel MVC Pattern](#laravel-mvc-pattern)
2. [Models (Eloquent ORM)](#models-eloquent-orm)
3. [Controllers](#controllers)
4. [Routes](#routes)
5. [Middleware](#middleware)
6. [Database Migrations](#database-migrations)
7. [Validation & Security](#validation--security)

---

## 🎯 Laravel MVC Pattern

Laravel volgt het **Model-View-Controller (MVC)** pattern:

```
┌─────────────┐
│   Browser   │
└──────┬──────┘
       │ HTTP Request
       ↓
┌─────────────┐
│   Routes    │  web.php, api.php
└──────┬──────┘
       ↓
┌─────────────┐
│ Middleware  │  Authentication, Authorization
└──────┬──────┘
       ↓
┌─────────────┐
│ Controller  │  Business Logic Entry Point
└──────┬──────┘
       │
       ├→ Model ──→ Database (CRUD Operations)
       │
       └→ View (Blade Template)
          │
          ↓
       Response (HTML/JSON)
```

---

## 📦 Models (Eloquent ORM)

### 1. **Complaint Model** (`app/Models/Complaint.php`)

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Complaint extends Model
{
    /**
     * Mass assignable attributes
     * Deze velden kunnen via create() of update() gevuld worden
     */
    protected $fillable = [
        'user_id',           // Foreign key naar users table
        'title',             // Titel van klacht
        'description',       // Beschrijving
        'category',          // Categorie (Openbare Ruimte, etc.)
        'priority',          // Prioriteit (laag, normaal, hoog, urgent)
        'status',            // Status (open, in_behandeling, afgerond, gesloten)
        'address',           // Adres
        'latitude',          // GPS coördinaat (breed)
        'longitude',         // GPS coördinaat (lengte)
        'contact_name',      // Naam melder
        'contact_email',     // Email melder
        'contact_phone',     // Telefoon melder
        'assigned_to',       // Toegewezen aan (user_id)
    ];

    /**
     * Attribute casting
     * Converteer database types naar PHP types
     */
    protected $casts = [
        'latitude' => 'decimal:8',      // 8 decimalen precisie
        'longitude' => 'decimal:8',     // 8 decimalen precisie
        'created_at' => 'datetime',     // Carbon datetime object
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship: Complaint belongs to User (melder)
     * 
     * Een klacht hoort bij één gebruiker
     * Foreign key: user_id
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Complaint has many Photos
     * 
     * Een klacht kan meerdere foto's hebben
     * Foreign key in complaint_photos: complaint_id
     */
    public function photos(): HasMany
    {
        return $this->hasMany(ComplaintPhoto::class);
    }

    /**
     * Relationship: Complaint has many Notes
     * 
     * Een klacht kan meerdere notities hebben
     * Foreign key in complaint_notes: complaint_id
     */
    public function notes(): HasMany
    {
        return $this->hasMany(ComplaintNote::class);
    }

    /**
     * Relationship: Complaint belongs to assigned User
     * 
     * Een klacht kan toegewezen zijn aan een beheerder
     * Foreign key: assigned_to
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Query Scope: Filter by status
     * 
     * Usage: Complaint::status('open')->get()
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Query Scope: Filter by category
     * 
     * Usage: Complaint::category('verkeer')->get()
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Query Scope: Filter by priority
     * 
     * Usage: Complaint::priority('urgent')->get()
     */
    public function scopePriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Accessor: Get full address with GPS
     * 
     * Usage: $complaint->full_location
     * Returns: "Address (52.3777, 4.901)"
     */
    public function getFullLocationAttribute(): string
    {
        if ($this->latitude && $this->longitude) {
            return "{$this->address} ({$this->latitude}, {$this->longitude})";
        }
        return $this->address;
    }

    /**
     * Accessor: Get status label in Dutch
     * 
     * Usage: $complaint->status_label
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'open' => 'Open',
            'in_behandeling' => 'In Behandeling',
            'afgerond' => 'Afgerond',
            'gesloten' => 'Gesloten',
            default => 'Onbekend',
        };
    }

    /**
     * Accessor: Get priority label with emoji
     * 
     * Usage: $complaint->priority_label
     */
    public function getPriorityLabelAttribute(): string
    {
        return match($this->priority) {
            'laag' => '🟢 Laag',
            'normaal' => '🟡 Normaal',
            'hoog' => '🟠 Hoog',
            'urgent' => '🔴 Urgent',
            default => 'Onbekend',
        };
    }
}
```

#### **Uitleg van Eloquent Concepten:**

**1. Mass Assignment:**
```php
$complaint = Complaint::create([
    'title' => 'Kapotte straatlantaarn',
    'description' => 'De lantaarn is kapot',
    'status' => 'open',
]);
```
- `$fillable` array bepaalt welke velden mass-assignable zijn
- Voorkomt security issues (mass assignment vulnerability)

**2. Relationships:**
```php
// Eager loading (vermijd N+1 query probleem)
$complaint = Complaint::with(['photos', 'notes', 'user'])->find(1);

// Lazy loading (queries pas bij toegang)
$photos = $complaint->photos; // Query wordt nu uitgevoerd
```

**3. Query Scopes:**
```php
// Chain scopes voor complexe queries
$urgentOpen = Complaint::status('open')
    ->priority('urgent')
    ->category('verkeer')
    ->get();
```

**4. Accessors (Virtual Attributes):**
```php
// Auto-computed properties
echo $complaint->status_label;    // "Open"
echo $complaint->priority_label;  // "🔴 Urgent"
echo $complaint->full_location;   // "Dam 1, Amsterdam (52.3777, 4.901)"
```

---

### 2. **ComplaintNote Model** (`app/Models/ComplaintNote.php`)

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplaintNote extends Model
{
    protected $fillable = [
        'complaint_id',    // Foreign key naar complaints
        'user_id',         // Wie schreef de notitie
        'note',            // Inhoud van notitie
        'is_internal',     // Intern (true) of publiek zichtbaar (false)
    ];

    protected $casts = [
        'is_internal' => 'boolean',
        'created_at' => 'datetime',
    ];

    /**
     * Relationship: Note belongs to Complaint
     */
    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class);
    }

    /**
     * Relationship: Note belongs to User (auteur)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
```

---

## 🎮 Controllers

### 1. **ComplaintApiController** (`app/Http/Controllers/Api/ComplaintApiController.php`)

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ComplaintApiController extends Controller
{
    /**
     * GET /api/complaints
     * 
     * Haal lijst van klachten op met optionele filters
     * 
     * Query Parameters:
     * - status: Filter op status (open, in_behandeling, etc.)
     * - priority: Filter op prioriteit (laag, normaal, hoog, urgent)
     * - category: Filter op categorie
     * - limit: Limiteer aantal resultaten (default: 50)
     * - page: Paginatie
     * 
     * Response:
     * {
     *   "data": [...],
     *   "total": 100,
     *   "page": 1,
     *   "per_page": 50
     * }
     */
    public function index(Request $request): JsonResponse
    {
        // Start query builder
        $query = Complaint::query()
            ->with(['user', 'photos']); // Eager load relaties (voorkomt N+1)
        
        // Filter op status (bijv. ?status=open)
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter op prioriteit (bijv. ?priority=urgent)
        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }
        
        // Filter op categorie (bijv. ?category=verkeer)
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }
        
        // Limiteer resultaten (default: 50)
        $limit = $request->input('limit', 50);
        
        // Sorteer op nieuwste eerst
        $query->orderBy('created_at', 'desc');
        
        // Paginatie
        $complaints = $query->paginate($limit);
        
        return response()->json([
            'success' => true,
            'data' => $complaints->items(),
            'total' => $complaints->total(),
            'page' => $complaints->currentPage(),
            'per_page' => $complaints->perPage(),
        ]);
    }

    /**
     * GET /api/complaints/{id}
     * 
     * Haal specifieke klacht op met alle details
     * 
     * Path Parameter:
     * - id: Complaint ID
     * 
     * Response: Complaint object met relaties
     */
    public function show(int $id): JsonResponse
    {
        // findOrFail gooit 404 als niet gevonden
        $complaint = Complaint::with(['user', 'photos', 'notes.user'])
            ->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $complaint,
        ]);
    }

    /**
     * POST /api/complaints
     * 
     * Creëer nieuwe klacht
     * 
     * Request Body (JSON):
     * {
     *   "title": "string (required, max:255)",
     *   "description": "string (required)",
     *   "category": "string (required)",
     *   "address": "string (required)",
     *   "latitude": "decimal (optional)",
     *   "longitude": "decimal (optional)",
     *   "contact_name": "string (required)",
     *   "contact_email": "email (required)",
     *   "contact_phone": "string (optional)"
     * }
     * 
     * Response: Created complaint object (201)
     */
    public function store(Request $request): JsonResponse
    {
        // Valideer input data
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|in:openbare_ruimte,verkeer,overlast,afval,verlichting,groen,anders',
            'address' => 'required|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'contact_name' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
        ]);
        
        // Voeg default waarden toe
        $validated['status'] = 'open';
        $validated['priority'] = 'normaal';
        
        // Maak klacht aan
        $complaint = Complaint::create($validated);
        
        // Return 201 Created status
        return response()->json([
            'success' => true,
            'message' => 'Klacht succesvol aangemaakt',
            'data' => $complaint,
        ], 201);
    }

    /**
     * PATCH /api/complaints/{id}/status
     * 
     * Update status van klacht
     * 
     * Request Body:
     * {
     *   "status": "in_behandeling" | "afgerond" | "gesloten"
     * }
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $complaint = Complaint::findOrFail($id);
        
        // Valideer nieuwe status
        $validated = $request->validate([
            'status' => 'required|in:open,in_behandeling,afgerond,gesloten',
        ]);
        
        // Update en sla op
        $complaint->update($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Status bijgewerkt',
            'data' => $complaint,
        ]);
    }

    /**
     * POST /api/complaints/{id}/notes
     * 
     * Voeg notitie toe aan klacht
     * 
     * Request Body:
     * {
     *   "note": "string (required)",
     *   "is_internal": "boolean (optional, default: false)"
     * }
     */
    public function addNote(Request $request, int $id): JsonResponse
    {
        $complaint = Complaint::findOrFail($id);
        
        $validated = $request->validate([
            'note' => 'required|string',
            'is_internal' => 'boolean',
        ]);
        
        // Voeg user_id toe (current authenticated user)
        $validated['user_id'] = auth()->id() ?? 1; // Fallback naar admin
        
        // Maak notitie aan
        $note = $complaint->notes()->create($validated);
        
        // Reload met user relatie
        $note->load('user');
        
        return response()->json([
            'success' => true,
            'message' => 'Notitie toegevoegd',
            'data' => $note,
        ], 201);
    }

    /**
     * GET /api/complaints/search
     * 
     * Full-text search in klachten
     * 
     * Query Parameters:
     * - q: Zoekterm (required)
     * - limit: Aantal resultaten (default: 20)
     * 
     * Zoekt in: title, description, address, contact_name
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:3',
        ]);
        
        $searchTerm = $request->q;
        $limit = $request->input('limit', 20);
        
        // Search in meerdere velden met OR conditions
        $complaints = Complaint::where(function($query) use ($searchTerm) {
            $query->where('title', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('address', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('contact_name', 'LIKE', "%{$searchTerm}%");
        })
        ->with(['user', 'photos'])
        ->orderBy('created_at', 'desc')
        ->limit($limit)
        ->get();
        
        return response()->json([
            'success' => true,
            'data' => $complaints,
            'count' => $complaints->count(),
            'query' => $searchTerm,
        ]);
    }

    /**
     * GET /api/complaints/map
     * 
     * Haal klachten op voor kaart visualisatie
     * Alleen klachten met GPS coördinaten
     * 
     * Query Parameters:
     * - status: Filter op status (optional)
     * - bounds: Geografische bounds (optional)
     *   Format: ?bounds=lat_min,lng_min,lat_max,lng_max
     */
    public function mapData(Request $request): JsonResponse
    {
        $query = Complaint::query()
            ->whereNotNull('latitude')    // Alleen met GPS
            ->whereNotNull('longitude')
            ->with(['user']);
        
        // Filter op status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter op geografische bounds (voor zoom levels)
        if ($request->has('bounds')) {
            $bounds = explode(',', $request->bounds);
            if (count($bounds) === 4) {
                $query->whereBetween('latitude', [$bounds[0], $bounds[2]])
                      ->whereBetween('longitude', [$bounds[1], $bounds[3]]);
            }
        }
        
        $complaints = $query->get();
        
        // Transform data voor map markers
        $mapData = $complaints->map(function($complaint) {
            return [
                'id' => $complaint->id,
                'title' => $complaint->title,
                'category' => $complaint->category,
                'status' => $complaint->status,
                'priority' => $complaint->priority,
                'latitude' => (float) $complaint->latitude,
                'longitude' => (float) $complaint->longitude,
                'address' => $complaint->address,
                'created_at' => $complaint->created_at->format('Y-m-d H:i'),
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => $mapData,
            'count' => $mapData->count(),
        ]);
    }
}
```

#### **Belangrijke Laravel Concepten in Controllers:**

**1. Dependency Injection:**
```php
public function store(Request $request)
{
    // Laravel injecteert automatisch Request object
    // Je hoeft niet global $_POST te gebruiken
}
```

**2. Validation:**
```php
$validated = $request->validate([
    'email' => 'required|email|unique:users',
    'password' => 'required|min:8|confirmed',
]);
// Als validatie faalt, wordt automatisch 422 response gegeven
```

**3. Eloquent Queries:**
```php
// Method chaining voor queries
Complaint::where('status', 'open')
    ->where('priority', 'urgent')
    ->with('photos')
    ->orderBy('created_at', 'desc')
    ->paginate(20);
```

**4. JSON Responses:**
```php
return response()->json($data, 200);  // Success
return response()->json($error, 404); // Not found
return response()->json($error, 422); // Validation error
```

---

### 2. **StatisticsController** (`app/Http/Controllers/Api/StatisticsController.php`)

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatisticsController extends Controller
{
    /**
     * GET /api/statistics
     * 
     * Haal dashboard statistieken op
     * 
     * Query Parameters:
     * - period: today|week|month|year|all (default: all)
     * 
     * Response:
     * {
     *   "totals": {...},
     *   "by_status": {...},
     *   "by_priority": {...},
     *   "by_category": {...},
     *   "trends": {...},
     *   "top_categories": [...]
     * }
     */
    public function index(Request $request): JsonResponse
    {
        $period = $request->input('period', 'all');
        
        // Bepaal datum range op basis van period
        $query = Complaint::query();
        
        switch ($period) {
            case 'today':
                $query->whereDate('created_at', Carbon::today());
                break;
            case 'week':
                $query->where('created_at', '>=', Carbon::now()->subWeek());
                break;
            case 'month':
                $query->where('created_at', '>=', Carbon::now()->subMonth());
                break;
            case 'year':
                $query->where('created_at', '>=', Carbon::now()->subYear());
                break;
            case 'all':
            default:
                // Geen filter
                break;
        }
        
        // Totaal aantal klachten
        $total = $query->count();
        
        // Group by status
        $byStatus = (clone $query)->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
        
        // Group by priority
        $byPriority = (clone $query)->select('priority', DB::raw('count(*) as count'))
            ->groupBy('priority')
            ->pluck('count', 'priority')
            ->toArray();
        
        // Group by category
        $byCategory = (clone $query)->select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->orderBy('count', 'desc')
            ->pluck('count', 'category')
            ->toArray();
        
        // Top 5 categorieën
        $topCategories = (clone $query)->select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get()
            ->map(function($item) {
                return [
                    'category' => $item->category,
                    'count' => $item->count,
                    'percentage' => 0, // Berekend later
                ];
            });
        
        // Trend data (laatste 7 dagen)
        $trends = Complaint::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
        
        // Gemiddelde afhandeltijd (in dagen)
        $avgResolutionTime = Complaint::whereNotNull('updated_at')
            ->where('status', 'afgerond')
            ->selectRaw('AVG(DATEDIFF(updated_at, created_at)) as avg_days')
            ->value('avg_days');
        
        return response()->json([
            'success' => true,
            'period' => $period,
            'data' => [
                'totals' => [
                    'all' => $total,
                    'open' => $byStatus['open'] ?? 0,
                    'in_behandeling' => $byStatus['in_behandeling'] ?? 0,
                    'afgerond' => $byStatus['afgerond'] ?? 0,
                    'gesloten' => $byStatus['gesloten'] ?? 0,
                ],
                'by_status' => $byStatus,
                'by_priority' => $byPriority,
                'by_category' => $byCategory,
                'top_categories' => $topCategories,
                'trends' => $trends,
                'avg_resolution_days' => round($avgResolutionTime ?? 0, 1),
            ],
        ]);
    }
}
```

#### **SQL & Eloquent Aggregatie:**

**1. COUNT Queries:**
```php
// Simple count
$total = Complaint::count();

// Count met filter
$openCount = Complaint::where('status', 'open')->count();

// Group by count
$statusCounts = Complaint::select('status', DB::raw('count(*) as count'))
    ->groupBy('status')
    ->get();
```

**2. Aggregatie Functies:**
```php
// AVG (gemiddelde)
$avgDays = Complaint::avg('resolution_days');

// SUM (totaal)
$totalCost = Complaint::sum('estimated_cost');

// MIN/MAX
$oldest = Complaint::min('created_at');
$newest = Complaint::max('created_at');
```

**3. Date Filtering:**
```php
use Carbon\Carbon;

// Vandaag
$today = Complaint::whereDate('created_at', Carbon::today())->get();

// Laatste week
$week = Complaint::where('created_at', '>=', Carbon::now()->subWeek())->get();

// Tussen twee datums
$range = Complaint::whereBetween('created_at', [$start, $end])->get();
```

---

## 🛣️ Routes

### **API Routes** (`routes/api.php`)

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ComplaintApiController;
use App\Http\Controllers\Api\StatisticsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Alle routes hebben automatisch /api prefix
| Voorbeeld: /api/complaints
|
| Middleware 'api' is automatisch actief:
| - Accept: application/json header vereist
| - Rate limiting (60 requests per minuut)
| - JSON responses
|
*/

// Klachten endpoints
Route::prefix('complaints')->group(function () {
    // GET /api/complaints - Lijst van klachten
    Route::get('/', [ComplaintApiController::class, 'index']);
    
    // GET /api/complaints/search - Zoeken
    Route::get('/search', [ComplaintApiController::class, 'search']);
    
    // GET /api/complaints/map - Map data
    Route::get('/map', [ComplaintApiController::class, 'mapData']);
    
    // POST /api/complaints - Nieuwe klacht
    Route::post('/', [ComplaintApiController::class, 'store']);
    
    // GET /api/complaints/{id} - Specifieke klacht
    Route::get('/{id}', [ComplaintApiController::class, 'show']);
    
    // PATCH /api/complaints/{id}/status - Update status
    Route::patch('/{id}/status', [ComplaintApiController::class, 'updateStatus']);
    
    // POST /api/complaints/{id}/notes - Notitie toevoegen
    Route::post('/{id}/notes', [ComplaintApiController::class, 'addNote']);
});

// Statistieken endpoint
Route::get('/statistics', [StatisticsController::class, 'index']);

// Protected routes (authentication vereist)
Route::middleware('auth:sanctum')->group(function () {
    // Admin only routes
    Route::middleware('admin')->group(function () {
        // DELETE /api/complaints/{id}
        Route::delete('/complaints/{id}', [ComplaintApiController::class, 'destroy']);
    });
});
```

#### **Route Patterns:**

**1. Resource Routes:**
```php
// Volledige CRUD routes in één regel
Route::apiResource('complaints', ComplaintApiController::class);

// Genereert:
// GET    /complaints          index()
// POST   /complaints          store()
// GET    /complaints/{id}     show()
// PUT    /complaints/{id}     update()
// DELETE /complaints/{id}     destroy()
```

**2. Route Groups:**
```php
// Prefix groep
Route::prefix('api/v1')->group(function () {
    // Routes hier hebben /api/v1 prefix
});

// Middleware groep
Route::middleware(['auth', 'admin'])->group(function () {
    // Routes hier vereisen auth + admin
});
```

**3. Route Parameters:**
```php
// Required parameter
Route::get('/complaints/{id}', ...);

// Optional parameter
Route::get('/complaints/{id?}', ...);

// Regex constraint
Route::get('/complaints/{id}', ...)->where('id', '[0-9]+');
```

---

## 🔒 Middleware

### **AdminMiddleware** (`app/Http/Middleware/AdminMiddleware.php`)

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     * 
     * Controleert of gebruiker admin rol heeft
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check of gebruiker ingelogd is
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Je moet ingelogd zijn');
        }
        
        // Check of gebruiker admin rol heeft (Spatie Permission)
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Toegang geweigerd. Admin rechten vereist.');
        }
        
        // Gebruiker is admin, ga door naar volgende middleware/controller
        return $next($request);
    }
}
```

#### **Middleware Flow:**

```
Request → Web Middleware → Authentication → AdminMiddleware → Controller
            │                    │                  │
            ├─ CSRF Protection   ├─ Session Check   ├─ Role Check
            ├─ Cookie Encrypt    ├─ Auth Check      └─ Authorize
            └─ StartSession      └─ Guest Check
```

**Registratie in `app/Http/Kernel.php`:**
```php
protected $middlewareAliases = [
    'admin' => \App\Http\Middleware\AdminMiddleware::class,
];
```

**Gebruik:**
```php
Route::middleware('admin')->group(function () {
    Route::get('/admin/dashboard', ...);
});
```

---

## 🗄️ Database Migrations

### **Complaints Table Migration**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Voer uit met: php artisan migrate
     */
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
            // Primary key (auto-increment)
            $table->id();
            
            // Foreign key naar users table (optioneel, voor ingelogde users)
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            
            // Basis informatie
            $table->string('title');                    // VARCHAR(255)
            $table->text('description');                // TEXT
            $table->string('category');                 // openbare_ruimte, verkeer, etc.
            $table->enum('priority', ['laag', 'normaal', 'hoog', 'urgent'])
                  ->default('normaal');
            $table->enum('status', ['open', 'in_behandeling', 'afgerond', 'gesloten'])
                  ->default('open');
            
            // Locatie informatie
            $table->string('address');
            $table->decimal('latitude', 10, 8)->nullable();   // GPS: -90 tot 90
            $table->decimal('longitude', 11, 8)->nullable();  // GPS: -180 tot 180
            
            // Contact informatie
            $table->string('contact_name');
            $table->string('contact_email');
            $table->string('contact_phone')->nullable();
            
            // Toewijzing aan beheerder
            $table->foreignId('assigned_to')->nullable()->constrained('users');
            
            // Timestamps (created_at, updated_at)
            $table->timestamps();
            
            // Soft deletes (deleted_at)
            $table->softDeletes();
            
            // Indexes voor snellere queries
            $table->index('status');
            $table->index('category');
            $table->index('priority');
            $table->index('created_at');
            $table->index(['latitude', 'longitude']);  // Composite index voor GPS
        });
    }

    /**
     * Reverse the migrations.
     * 
     * Voer uit met: php artisan migrate:rollback
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
```

#### **Blueprint Column Types:**

```php
// Veelgebruikte types
$table->id();                           // BIGINT UNSIGNED AUTO_INCREMENT
$table->string('name', 100);            // VARCHAR(100)
$table->text('description');            // TEXT
$table->integer('count');               // INT
$table->decimal('price', 8, 2);         // DECIMAL(8,2) bijv. 999999.99
$table->boolean('is_active');           // TINYINT(1) 0 of 1
$table->date('birth_date');             // DATE
$table->datetime('appointment');        // DATETIME
$table->timestamp('verified_at');       // TIMESTAMP
$table->enum('status', ['a', 'b']);     // ENUM('a','b')
$table->json('metadata');               // JSON (MySQL 5.7+)

// Special columns
$table->timestamps();                   // created_at + updated_at
$table->softDeletes();                  // deleted_at (soft delete)
$table->foreignId('user_id')           // BIGINT UNSIGNED + foreign key
      ->constrained()
      ->onDelete('cascade');
```

---

## ✅ Validation & Security

### **Request Validation Rules**

```php
// In Controller
$validated = $request->validate([
    // String validatie
    'name' => 'required|string|max:255',
    'slug' => 'required|string|unique:posts|regex:/^[a-z0-9-]+$/',
    
    // Numeriek
    'age' => 'required|integer|min:18|max:100',
    'price' => 'required|numeric|between:0,9999.99',
    
    // Email & URL
    'email' => 'required|email:rfc,dns|unique:users',
    'website' => 'nullable|url|active_url',
    
    // Dates
    'birth_date' => 'required|date|before:today',
    'appointment' => 'required|date|after:now',
    
    // Files
    'photo' => 'required|image|mimes:jpeg,png|max:2048', // 2MB
    'document' => 'required|file|mimes:pdf|max:10240',   // 10MB
    
    // Arrays
    'tags' => 'required|array|min:1|max:5',
    'tags.*' => 'string|max:50',
    
    // Boolean
    'is_active' => 'required|boolean',
    
    // In database
    'category_id' => 'required|exists:categories,id',
    
    // Conditionals
    'other_category' => 'required_if:category,other',
    'phone' => 'required_without:email',
    
    // Password
    'password' => 'required|string|min:8|confirmed', // password_confirmation veld vereist
]);
```

### **Security Best Practices**

**1. CSRF Protection (automatisch actief voor POST/PUT/DELETE):**
```blade
<form method="POST" action="/complaints">
    @csrf  <!-- Genereert hidden token veld -->
    <!-- form fields -->
</form>
```

**2. SQL Injection Prevention (Eloquent bindt parameters):**
```php
// ✅ VEILIG - Eloquent bindt automatisch
Complaint::where('title', $userInput)->get();

// ❌ ONVEILIG - Nooit doen!
DB::select("SELECT * FROM complaints WHERE title = '$userInput'");

// ✅ VEILIG - Met parameter binding
DB::select("SELECT * FROM complaints WHERE title = ?", [$userInput]);
```

**3. XSS Prevention (Blade escaped automatisch):**
```blade
<!-- ✅ Auto-escaped, veilig -->
<p>{{ $complaint->title }}</p>

<!-- ❌ Niet escaped, alleen voor trusted HTML -->
<div>{!! $complaint->description !!}</div>
```

**4. Mass Assignment Protection:**
```php
// $fillable whitelist in Model
protected $fillable = ['title', 'description'];

// ❌ Dit werkt NIET (niet in $fillable)
Complaint::create(['is_admin' => true]);
```

**5. File Upload Validation:**
```php
$request->validate([
    'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
]);

// Veilig opslaan met random naam
$path = $request->file('photo')->store('complaints', 'public');
```

---

## 🎓 Laravel Best Practices

### 1. **Query Optimization**

```php
// ❌ N+1 Query Problem (1 + N queries)
$complaints = Complaint::all();
foreach ($complaints as $complaint) {
    echo $complaint->user->name; // Elke iteratie = nieuwe query!
}

// ✅ Eager Loading (2 queries totaal)
$complaints = Complaint::with('user')->get();
foreach ($complaints as $complaint) {
    echo $complaint->user->name; // Geen extra query!
}

// ✅ Lazy Eager Loading
$complaints = Complaint::all();
$complaints->load('user');
```

### 2. **Use Service Layer voor Complex Logic**

```php
// app/Services/ComplaintService.php
class ComplaintService
{
    public function createWithPhotos(array $data, array $photos): Complaint
    {
        DB::beginTransaction();
        try {
            $complaint = Complaint::create($data);
            
            foreach ($photos as $photo) {
                $complaint->photos()->create([
                    'path' => $photo->store('complaints'),
                ]);
            }
            
            // Send notification
            // Log activity
            // Update statistics
            
            DB::commit();
            return $complaint;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
```

### 3. **Use Events & Listeners**

```php
// app/Events/ComplaintCreated.php
class ComplaintCreated
{
    public function __construct(public Complaint $complaint) {}
}

// app/Listeners/SendComplaintNotification.php
class SendComplaintNotification
{
    public function handle(ComplaintCreated $event)
    {
        Mail::to($event->complaint->contact_email)
            ->send(new ComplaintConfirmation($event->complaint));
    }
}

// In Controller
Complaint::create($data);
event(new ComplaintCreated($complaint));
```

---

**Volgende document:** [03_MCP_SERVER.md](03_MCP_SERVER.md) - MCP Server implementatie details
