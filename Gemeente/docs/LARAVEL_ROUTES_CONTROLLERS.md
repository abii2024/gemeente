# Laravel Routes & Controllers - Gemeente Portal

## 📋 Inhoudsopgave

1. [Routing Systeem](#routing-systeem)
2. [Controller Architectuur](#controller-architectuur)
3. [Resource Controllers](#resource-controllers)
4. [API Endpoints](#api-endpoints)
5. [Route Model Binding](#route-model-binding)

---

## 🛣️ Routing Systeem

### Route Files Structuur

```
routes/
├── web.php      # Publieke webpagina's (session, CSRF, cookies)
├── admin.php    # Admin panel routes
├── auth.php     # Breeze authenticatie routes  
└── api.php      # RESTful API (stateless, token auth)
```

### web.php - Publieke Routes

```php
<?php

use App\Http\Controllers\Web\ComplaintController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Routes voor publieke website. Automatisch session, CSRF en cookie middleware.
*/

// Homepage
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Klachten (guests)
Route::controller(ComplaintController::class)->group(function () {
    // GET /complaints/create - Toon formulier
    Route::get('/complaints/create', 'create')
        ->name('complaint.create');
    
    // POST /complaints - Verwerk formulier
    Route::post('/complaints', 'store')
        ->name('complaint.store');
    
    // GET /klacht/track?id=123&email=test@example.com - Tracking pagina
    Route::get('/klacht/track', 'track')
        ->name('complaint.track');
    
    // GET /complaints/search - Zoekpagina
    Route::get('/complaints/search', 'search')
        ->name('complaint.search');
});

// Dashboard (vereist login)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    // Profiel routes (van Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

// Diensten pages (voorbeeld)
Route::prefix('diensten')->name('diensten.')->group(function () {
    Route::get('/paspoort', fn() => view('pages.paspoort'))
        ->name('paspoort');
    Route::get('/rijbewijs', fn() => view('pages.rijbewijs'))
        ->name('rijbewijs');
    Route::get('/vergunning', fn() => view('pages.vergunning'))
        ->name('vergunning');
});
```

**Route Methods Uitgelegd:**

```php
// HTTP Methods
Route::get($uri, $callback);      // GET (retrieve)
Route::post($uri, $callback);     // POST (create)
Route::put($uri, $callback);      // PUT (full update)
Route::patch($uri, $callback);    // PATCH (partial update)
Route::delete($uri, $callback);   // DELETE (remove)
Route::options($uri, $callback);  // OPTIONS (CORS)

// Match multiple methods
Route::match(['get', 'post'], '/search', $callback);

// Match any method
Route::any('/webhook', $callback);
```

**Route Parameters:**

```php
// Required parameter
Route::get('/complaints/{id}', function ($id) {
    return Complaint::findOrFail($id);
});

// Optional parameter
Route::get('/users/{name?}', function ($name = 'Guest') {
    return "Hello, $name!";
});

// Parameter constraints
Route::get('/complaints/{id}', function ($id) {
    //
})->where('id', '[0-9]+'); // Alleen nummers

Route::get('/users/{name}', function ($name) {
    //
})->where('name', '[A-Za-z]+'); // Alleen letters

// Multiple constraints
Route::get('/posts/{id}/{slug}', function ($id, $slug) {
    //
})->where(['id' => '[0-9]+', 'slug' => '[a-z-]+']);
```

**Named Routes:**

```php
// Define named route
Route::get('/complaints/create', [ComplaintController::class, 'create'])
    ->name('complaint.create');

// Generate URL
route('complaint.create'); 
// Returns: http://gemeente.test/complaints/create

// With parameters
route('complaint.show', ['id' => 123]);
// Returns: http://gemeente.test/complaints/123

// In controller (redirect)
return redirect()->route('complaint.create');

// In Blade
<a href="{{ route('complaint.create') }}">Klacht indienen</a>
```

**Waarom Named Routes?**
- ✅ Flexibiliteit - URL structuur kan wijzigen zonder views aan te passen
- ✅ Type safety - IDE kan route names auto-completen
- ✅ Refactoring - Easy search & replace

### admin.php - Admin Routes

```php
<?php

use App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
| Alle routes vereisen auth + admin role.
*/

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])
        ->name('dashboard');
    
    // Complaints CRUD
    Route::resource('complaints', Admin\ComplaintAdminController::class);
    // Dit genereert automatisch 7 routes:
    // GET    /admin/complaints           index   - Lijst
    // GET    /admin/complaints/create    create  - Create form
    // POST   /admin/complaints           store   - Save new
    // GET    /admin/complaints/{id}      show    - View single
    // GET    /admin/complaints/{id}/edit edit    - Edit form
    // PUT    /admin/complaints/{id}      update  - Save changes
    // DELETE /admin/complaints/{id}      destroy - Delete
    
    // Users CRUD
    Route::resource('users', Admin\UserController::class)
        ->except(['show']); // Skip show method
    
    // API Endpoints (AJAX)
    Route::prefix('api/dashboard')->name('api.dashboard.')->group(function () {
        Route::get('/recent', [Admin\DashboardController::class, 'recentComplaints'])
            ->name('recent');
        Route::get('/map-data', [Admin\DashboardController::class, 'mapData'])
            ->name('map-data');
        Route::get('/search', [Admin\DashboardController::class, 'searchById'])
            ->name('search');
        Route::get('/filter', [Admin\DashboardController::class, 'filter'])
            ->name('filter');
        Route::get('/complaint/{id}', [Admin\DashboardController::class, 'complaintDetails'])
            ->name('complaint-details');
    });
});
```

**Route Grouping Benefits:**

```php
// Zonder grouping (repetitief)
Route::get('/admin/dashboard', ...)->middleware(['auth', 'role:admin']);
Route::get('/admin/complaints', ...)->middleware(['auth', 'role:admin']);
Route::get('/admin/users', ...)->middleware(['auth', 'role:admin']);

// Met grouping (DRY)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', ...);
    Route::get('/complaints', ...);
    Route::get('/users', ...);
});
```

### auth.php - Authenticatie Routes

```php
<?php

use App\Http\Controllers\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
| Laravel Breeze authenticatie routes.
*/

// Login
Route::middleware('guest')->group(function () {
    Route::get('/login', [Auth\AuthenticatedSessionController::class, 'create'])
        ->name('login');
    Route::post('/login', [Auth\AuthenticatedSessionController::class, 'store']);
});

// Register
Route::middleware('guest')->group(function () {
    Route::get('/register', [Auth\RegisteredUserController::class, 'create'])
        ->name('register');
    Route::post('/register', [Auth\RegisteredUserController::class, 'store']);
});

// Password Reset
Route::middleware('guest')->group(function () {
    Route::get('/forgot-password', [Auth\PasswordResetLinkController::class, 'create'])
        ->name('password.request');
    Route::post('/forgot-password', [Auth\PasswordResetLinkController::class, 'store'])
        ->name('password.email');
    Route::get('/reset-password/{token}', [Auth\NewPasswordController::class, 'create'])
        ->name('password.reset');
    Route::post('/reset-password', [Auth\NewPasswordController::class, 'store'])
        ->name('password.store');
});

// Email Verification
Route::middleware('auth')->group(function () {
    Route::get('/verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');
    Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});

// Logout
Route::post('/logout', [Auth\AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');
```

---

## 🎮 Controller Architectuur

### Single Action Controllers

Voor simpele acties - 1 controller, 1 method `__invoke()`:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class ShowDashboard extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): View
    {
        return view('dashboard');
    }
}

// In routes
Route::get('/dashboard', ShowDashboard::class);
```

### Resource Controllers

Voor CRUD operaties - volgt RESTful conventies:

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Http\Requests\StoreComplaintRequest;
use App\Http\Requests\UpdateComplaintRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ComplaintAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     * GET /admin/complaints
     */
    public function index(): View
    {
        // Pagination (15 per pagina)
        $complaints = Complaint::with(['assignedTo', 'attachments'])
            ->latest()
            ->paginate(15);
        
        return view('admin.complaints.index', compact('complaints'));
    }

    /**
     * Show the form for creating a new resource.
     * GET /admin/complaints/create
     */
    public function create(): View
    {
        return view('admin.complaints.create');
    }

    /**
     * Store a newly created resource in storage.
     * POST /admin/complaints
     */
    public function store(StoreComplaintRequest $request): RedirectResponse
    {
        $complaint = Complaint::create($request->validated());
        
        return redirect()
            ->route('admin.complaints.index')
            ->with('success', 'Klacht aangemaakt!');
    }

    /**
     * Display the specified resource.
     * GET /admin/complaints/{id}
     */
    public function show(Complaint $complaint): View
    {
        // Route Model Binding - $complaint is auto-loaded
        $complaint->load(['attachments', 'notes', 'statusHistories']);
        
        return view('admin.complaints.show', compact('complaint'));
    }

    /**
     * Show the form for editing the specified resource.
     * GET /admin/complaints/{id}/edit
     */
    public function edit(Complaint $complaint): View
    {
        return view('admin.complaints.edit', compact('complaint'));
    }

    /**
     * Update the specified resource in storage.
     * PUT/PATCH /admin/complaints/{id}
     */
    public function update(UpdateComplaintRequest $request, Complaint $complaint): RedirectResponse
    {
        $oldStatus = $complaint->status;
        
        // Update complaint
        $complaint->update($request->validated());
        
        // Log status change
        if ($oldStatus !== $complaint->status) {
            $complaint->statusHistories()->create([
                'from' => $oldStatus,
                'to' => $complaint->status,
                'user_id' => auth()->id(),
            ]);
        }
        
        return redirect()
            ->route('admin.complaints.show', $complaint)
            ->with('success', 'Klacht bijgewerkt!');
    }

    /**
     * Remove the specified resource from storage.
     * DELETE /admin/complaints/{id}
     */
    public function destroy(Complaint $complaint): RedirectResponse
    {
        // Cascade delete werkt automatisch via foreign keys
        $complaint->delete();
        
        return redirect()
            ->route('admin.complaints.index')
            ->with('success', 'Klacht verwijderd!');
    }
}
```

**RESTful Conventies:**

| HTTP Verb | URI | Action | Route Name | Purpose |
|-----------|-----|--------|------------|---------|
| GET | /complaints | index | complaints.index | Lijst tonen |
| GET | /complaints/create | create | complaints.create | Create form |
| POST | /complaints | store | complaints.store | Nieuwe opslaan |
| GET | /complaints/{id} | show | complaints.show | Eén tonen |
| GET | /complaints/{id}/edit | edit | complaints.edit | Edit form |
| PUT/PATCH | /complaints/{id} | update | complaints.update | Update opslaan |
| DELETE | /complaints/{id} | destroy | complaints.destroy | Verwijderen |

### Controller Best Practices

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Constructor - apply middleware
     */
    public function __construct()
    {
        // Middleware alleen voor specifieke methods
        $this->middleware('auth');
        $this->middleware('role:admin')->except('publicStats');
    }
    
    /**
     * Return type hints - altijd gebruiken!
     */
    public function index(): View
    {
        return view('admin.dashboard');
    }
    
    /**
     * Dependency Injection - Laravel resolved automatisch
     */
    public function store(StoreComplaintRequest $request): RedirectResponse
    {
        // $request is al gevalideerd + geïnjecteerd
    }
    
    /**
     * Route Model Binding - auto query
     */
    public function show(Complaint $complaint): View
    {
        // Laravel doet automatisch: Complaint::findOrFail($id)
    }
    
    /**
     * API response - consistent format
     */
    public function apiData(Request $request): JsonResponse
    {
        $data = Complaint::all();
        
        return response()->json([
            'success' => true,
            'data' => $data,
            'meta' => [
                'count' => $data->count(),
                'timestamp' => now(),
            ]
        ]);
    }
    
    /**
     * Error handling - gebruik try-catch voor critical operations
     */
    public function criticalOperation(): RedirectResponse
    {
        try {
            DB::transaction(function () {
                // Multiple database operations
                $complaint = Complaint::create([...]);
                $complaint->attachments()->create([...]);
                $complaint->statusHistories()->create([...]);
            });
            
            return redirect()->back()->with('success', 'Success!');
            
        } catch (\Exception $e) {
            Log::error('Critical operation failed: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Er ging iets mis. Probeer opnieuw.')
                ->withInput();
        }
    }
    
    /**
     * Authorize - check permissions
     */
    public function edit(Complaint $complaint): View
    {
        // Policy check
        $this->authorize('update', $complaint);
        
        // Of manual
        if (!auth()->user()->can('edit complaints')) {
            abort(403, 'Geen toegang');
        }
        
        return view('admin.complaints.edit', compact('complaint'));
    }
}
```

---

## 🔗 Route Model Binding

### Implicit Binding

Laravel resolved automatisch model parameter:

```php
// Route definition
Route::get('/complaints/{complaint}', [ComplaintController::class, 'show']);

// Controller
public function show(Complaint $complaint)
{
    // Laravel doet automatisch: Complaint::findOrFail($id)
    // Als niet gevonden → 404
    return view('complaint.show', compact('complaint'));
}

// In Blade - genereer URL met model
<a href="{{ route('complaint.show', $complaint) }}">
    Bekijk Klacht #{{ $complaint->id }}
</a>
// Genereert: /complaints/123
```

### Explicit Binding

Custom resolution logic:

```php
// In RouteServiceProvider boot()
use App\Models\Complaint;
use Illuminate\Support\Facades\Route;

public function boot(): void
{
    // Custom route key (niet 'id' maar 'slug')
    Route::model('complaint', Complaint::class);
    
    // Of met custom query
    Route::bind('complaint', function ($value) {
        return Complaint::where('slug', $value)
            ->orWhere('id', $value)
            ->firstOrFail();
    });
}
```

### Soft Delete Binding

```php
// Include soft deleted models
Route::get('/complaints/{complaint}', function (Complaint $complaint) {
    //
})->withTrashed();

// Only soft deleted
Route::get('/complaints/{complaint}', function (Complaint $complaint) {
    //
})->onlyTrashed();
```

---

## 🌐 API Endpoints

### RESTful API Design

```php
// routes/api.php - Automatisch /api prefix
Route::prefix('v1')->group(function () {
    
    // Public endpoints
    Route::get('/complaints', [Api\ComplaintController::class, 'index']);
    Route::get('/complaints/{complaint}', [Api\ComplaintController::class, 'show']);
    
    // Protected endpoints (token auth)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/complaints', [Api\ComplaintController::class, 'store']);
        Route::put('/complaints/{complaint}', [Api\ComplaintController::class, 'update']);
        Route::delete('/complaints/{complaint}', [Api\ComplaintController::class, 'destroy']);
    });
});
```

### API Controller

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    /**
     * GET /api/v1/complaints
     */
    public function index(Request $request): JsonResponse
    {
        $complaints = Complaint::query()
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->when($request->category, fn($q, $cat) => $q->where('category', $cat))
            ->paginate($request->per_page ?? 15);
        
        return response()->json([
            'success' => true,
            'data' => $complaints->items(),
            'meta' => [
                'current_page' => $complaints->currentPage(),
                'total' => $complaints->total(),
                'per_page' => $complaints->perPage(),
            ],
        ]);
    }
    
    /**
     * GET /api/v1/complaints/{id}
     */
    public function show(Complaint $complaint): JsonResponse
    {
        $complaint->load(['attachments', 'statusHistories']);
        
        return response()->json([
            'success' => true,
            'data' => $complaint,
        ]);
    }
    
    /**
     * POST /api/v1/complaints
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string',
        ]);
        
        $complaint = Complaint::create($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Klacht aangemaakt',
            'data' => $complaint,
        ], 201); // 201 Created status
    }
    
    /**
     * PUT /api/v1/complaints/{id}
     */
    public function update(Request $request, Complaint $complaint): JsonResponse
    {
        $this->authorize('update', $complaint);
        
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'status' => 'sometimes|in:open,in_progress,resolved',
        ]);
        
        $complaint->update($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Klacht bijgewerkt',
            'data' => $complaint->fresh(),
        ]);
    }
    
    /**
     * DELETE /api/v1/complaints/{id}
     */
    public function destroy(Complaint $complaint): JsonResponse
    {
        $this->authorize('delete', $complaint);
        
        $complaint->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Klacht verwijderd',
        ], 204); // 204 No Content
    }
}
```

### HTTP Status Codes

```php
// Success
return response()->json($data, 200); // OK
return response()->json($data, 201); // Created
return response()->json(null, 204);  // No Content

// Client Errors
return response()->json(['error' => 'Not found'], 404);
return response()->json(['error' => 'Unauthorized'], 401);
return response()->json(['error' => 'Forbidden'], 403);
return response()->json(['error' => 'Validation failed'], 422);

// Server Errors
return response()->json(['error' => 'Internal error'], 500);
```

### API Response Helper

```php
<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    protected function success($data = null, string $message = '', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }
    
    protected function error(string $message, int $code = 400, $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];
        
        if ($errors) {
            $response['errors'] = $errors;
        }
        
        return response()->json($response, $code);
    }
}

// In controller
use ApiResponse;

public function store(Request $request)
{
    $complaint = Complaint::create($request->all());
    return $this->success($complaint, 'Created successfully', 201);
}
```

---

## 📊 Route Debugging

### Lijst alle routes

```bash
# Toon alle geregistreerde routes
php artisan route:list

# Filter op naam
php artisan route:list --name=complaint

# Filter op method
php artisan route:list --method=GET

# Filter op path
php artisan route:list --path=admin

# Compact weergave
php artisan route:list --compact

# Toon middleware
php artisan route:list --columns=name,uri,middleware
```

### Route Caching

```bash
# Cache routes voor snellere loading (production)
php artisan route:cache

# Clear cache
php artisan route:clear
```

---

## 🎯 Praktische Voorbeelden

### File Downloads

```php
public function downloadAttachment(ComplaintImage $attachment)
{
    return Storage::download(
        $attachment->path,
        $attachment->original_name ?? 'attachment.jpg'
    );
}
```

### Redirects

```php
// Named route redirect
return redirect()->route('admin.dashboard');

// Back to previous page
return redirect()->back();

// With flash data
return redirect()->route('dashboard')
    ->with('success', 'Opgeslagen!');

// With input (na validatie error)
return redirect()->back()->withInput();

// External redirect
return redirect()->away('https://google.com');
```

### JSON Responses

```php
// Basic JSON
return response()->json(['name' => 'John']);

// With status code
return response()->json(['error' => 'Not Found'], 404);

// JSONP
return response()->jsonp('callback', ['name' => 'John']);
```

### File Responses

```php
// Display file in browser
return response()->file($pathToFile);

// Force download
return response()->download($pathToFile, $name);

// Streamed download (large files)
return response()->streamDownload(function () {
    echo 'File contents...';
}, 'filename.pdf');
```

---

**Volgende documenten:**
- [Laravel Database & Eloquent](LARAVEL_DATABASE.md)
- [Laravel Security & Auth](LARAVEL_SECURITY.md)
- [Laravel Testing](LARAVEL_TESTING.md)
