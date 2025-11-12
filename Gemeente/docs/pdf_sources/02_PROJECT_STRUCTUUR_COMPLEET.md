# PDF 2: Complete Projectstructuur

**Gemeente Portal - Alle Bestanden & Code Uitleg**  
**Datum:** 10 November 2025  
**Auteur:** Abdisamad Abdulle

---

## Inhoudsopgave

1. Project Overzicht
2. Database & Migrations
3. Models (Eloquent)
4. Controllers
5. Views (Blade Templates)
6. Routes
7. Middleware & Policies
8. Services & Helpers
9. Frontend Assets
10. Tests

---

## 1. Project Overzicht

### Folder Structuur

```
Gemeente/
├── app/                    # Laravel core applicatie code
│   ├── Console/           # Artisan commands
│   ├── Events/            # Event classes
│   ├── Http/              # Controllers, Middleware, Requests
│   │   ├── Controllers/   # ★ Alle controllers
│   │   ├── Middleware/    # ★ Custom middleware
│   │   └── Requests/      # Form validatie
│   ├── Jobs/              # Queue jobs
│   ├── Models/            # ★ Eloquent models
│   ├── Notifications/     # Email/SMS notificaties
│   ├── Policies/          # ★ Authorization policies
│   ├── Providers/         # Service providers
│   ├── Services/          # ★ Business logic services
│   └── View/              # View composers
│
├── bootstrap/             # Laravel bootstrap
├── config/                # ★ Configuratie bestanden
├── database/              # ★ Migrations, Seeders, Factories
│   ├── factories/         # Model factories voor testing
│   ├── migrations/        # ★ Database schema
│   └── seeders/           # ★ Test data
│
├── public/                # ★ Public webroot
│   ├── css/               # ★ Compiled CSS
│   ├── js/                # ★ Compiled JavaScript
│   ├── images/            # Afbeeldingen
│   └── storage/           # Public storage link
│
├── resources/             # ★ Frontend resources
│   ├── css/               # ★ Source CSS
│   ├── js/                # ★ Source JavaScript
│   └── views/             # ★ Blade templates
│       ├── admin/         # Admin dashboard views
│       ├── auth/          # Login/Register views
│       ├── complaints/    # Klachten views
│       ├── diensten/      # Diensten formulieren
│       ├── layouts/       # Layout templates
│       └── components/    # Reusable components
│
├── routes/                # ★ Route definitie
│   ├── web.php            # ★ Web routes
│   ├── api.php            # API routes
│   ├── admin.php          # ★ Admin routes
│   └── auth.php           # ★ Auth routes
│
├── storage/               # File storage
│   ├── app/               # Private files
│   ├── framework/         # Framework cache/sessions
│   └── logs/              # Application logs
│
├── tests/                 # ★ PHPUnit & Pest tests
│   ├── Feature/           # ★ Feature tests
│   └── Unit/              # Unit tests
│
└── vendor/                # Composer dependencies

★ = Belangrijke folders waar je vaak werkt
```

---

## 2. Database & Migrations

### 2.1 Migration: Users Table

**Bestand:** `database/migrations/2014_10_12_000000_create_users_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('role')->default('user');  // user, admin
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
```

**Wat doet het:**
- Maakt `users` tabel aan
- Basis authenticatie velden
- `role` voor user/admin onderscheid
- `timestamps` voor created_at/updated_at

**Gebruik:**
```bash
php artisan migrate
```

---

### 2.2 Migration: Complaints Table

**Bestand:** `database/migrations/2024_01_15_create_complaints_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            
            // Klacht informatie
            $table->string('title');
            $table->text('description');
            $table->enum('category', [
                'afval',
                'wegen',
                'openbare_ruimte',
                'openbare_verlichting',
                'groen',
                'overlast',
                'overig'
            ]);
            $table->enum('status', [
                'open',
                'in_behandeling',
                'opgelost',
                'gesloten'
            ])->default('open');
            
            // Locatie (GPS)
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);
            $table->string('address')->nullable();
            
            // Melder informatie
            $table->string('reporter_name');
            $table->string('reporter_email');
            $table->string('reporter_phone')->nullable();
            
            // User relatie (optioneel als ingelogd)
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            
            // Timestamps
            $table->timestamps();
            $table->timestamp('resolved_at')->nullable();
            
            // Indexes voor snelheid
            $table->index('status');
            $table->index('category');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
```

**Velden Uitleg:**

| Veld | Type | Wat Het Doet |
|------|------|--------------|
| `title` | string | Korte samenvatting klacht |
| `description` | text | Uitgebreide beschrijving |
| `category` | enum | Type klacht (dropdown opties) |
| `status` | enum | Huidige status (open/in behandeling/etc) |
| `lat`/`lng` | decimal | GPS coördinaten voor kaart |
| `reporter_name` | string | Naam van melder |
| `reporter_email` | string | Email voor updates |
| `user_id` | foreignId | Link naar user (null als guest) |

**Waarom indexes?**
```php
$table->index('status');  // Snelle filtering op status
$table->index('category'); // Snelle filtering op categorie
```
→ Maakt queries sneller bij veel data

---

### 2.3 Migration: Attachments Table

**Bestand:** `database/migrations/2024_01_16_create_attachments_table.php`

```php
<?php

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->constrained()->onDelete('cascade');
            $table->string('file_path');
            $table->string('file_name');
            $table->string('mime_type');
            $table->integer('file_size');  // in bytes
            $table->timestamps();
        });
    }
};
```

**Relatie:**
- 1 Complaint → Many Attachments
- `onDelete('cascade')` = Als complaint wordt verwijderd, ook attachments

**Gebruik in code:**
```php
$complaint = Complaint::find(1);
$complaint->attachments;  // Haalt alle foto's op
```

---

### 2.4 Migration: Notes Table

**Bestand:** `database/migrations/2024_01_17_create_notes_table.php`

```php
<?php

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('body');
            $table->boolean('is_internal')->default(true);
            $table->timestamps();
        });
    }
};
```

**Wat doet het:**
- Admin kan notities toevoegen aan klacht
- `is_internal` = true → Alleen admins zien
- `is_internal` = false → Ook melder kan zien

---

### 2.5 Migration: Afspraken Table

**Bestand:** `database/migrations/2025_11_06_112208_create_afspraken_table.php`

```php
<?php

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('afspraken', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('dienst');  // Paspoort, Rijbewijs, etc
            $table->date('datum');
            $table->time('tijd');
            $table->text('opmerking')->nullable();
            $table->enum('status', [
                'gepland',
                'bevestigd',
                'afgerond',
                'geannuleerd'
            ])->default('gepland');
            $table->timestamps();
            
            $table->index(['user_id', 'datum']);
        });
    }
};
```

**Gebruik:**
- User maakt afspraak voor paspoort
- Admin bevestigt afspraak
- User ziet afspraak in dashboard

---

## 3. Models (Eloquent)

### 3.1 User Model

**Bestand:** `app/Models/User.php`

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable, HasRoles;

    /**
     * Welke velden zijn mass-assignable
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * Verberg deze velden in JSON
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Cast attributes naar juiste type
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relaties
     */
    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    public function afspraken()
    {
        return $this->hasMany(Afspraak::class);
    }

    /**
     * Helper methods
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }
}
```

**Belangrijke Onderdelen:**

1. **$fillable** - Welke velden mag je mass-assign
```php
User::create([
    'name' => 'Jan',
    'email' => 'jan@example.com',
    'password' => bcrypt('password')
]);
```

2. **$hidden** - Verberg password in JSON responses
```php
return response()->json($user);
// password veld wordt automatisch verwijderd
```

3. **Relaties** - Eloquent relaties
```php
$user = User::find(1);
$user->complaints;  // Alle klachten van deze user
$user->afspraken;   // Alle afspraken van deze user
```

4. **Helper Methods** - Custom functionaliteit
```php
if ($user->isAdmin()) {
    // Admin-only code
}
```

---

### 3.2 Complaint Model

**Bestand:** `app/Models/Complaint.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'category',
        'status',
        'lat',
        'lng',
        'address',
        'reporter_name',
        'reporter_email',
        'reporter_phone',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'lat' => 'decimal:7',
            'lng' => 'decimal:7',
            'resolved_at' => 'datetime',
        ];
    }

    /**
     * Relaties
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    public function statusHistory()
    {
        return $this->hasMany(StatusHistory::class);
    }

    /**
     * Scopes - Herbruikbare queries
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Accessors - Formatteer data
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'open' => 'Open',
            'in_behandeling' => 'In Behandeling',
            'opgelost' => 'Opgelost',
            'gesloten' => 'Gesloten',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'open' => 'blue',
            'in_behandeling' => 'yellow',
            'opgelost' => 'green',
            'gesloten' => 'gray',
        };
    }

    /**
     * Helper Methods
     */
    public function markAsResolved(): void
    {
        $this->update([
            'status' => 'opgelost',
            'resolved_at' => now()
        ]);
    }

    public function assignToUser(User $user): void
    {
        $this->update(['user_id' => $user->id]);
    }
}
```

**Scopes Gebruik:**
```php
// Haal alle open klachten op, recent eerst
$openComplaints = Complaint::open()->recent()->get();

// Klachten in specifieke categorie
$afvalComplaints = Complaint::byCategory('afval')->get();

// Combineer scopes
$recentOpenAfval = Complaint::open()
    ->byCategory('afval')
    ->recent()
    ->limit(10)
    ->get();
```

**Accessors Gebruik:**
```php
$complaint = Complaint::find(1);
echo $complaint->status_label;  // "In Behandeling"
echo $complaint->status_color;  // "yellow"

// In Blade:
<span class="badge-{{ $complaint->status_color }}">
    {{ $complaint->status_label }}
</span>
```

---

### 3.3 Attachment Model

**Bestand:** `app/Models/Attachment.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
    protected $fillable = [
        'complaint_id',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
    ];

    /**
     * Relaties
     */
    public function complaint()
    {
        return $this->belongsTo(Complaint::class);
    }

    /**
     * Get full URL van bestand
     */
    public function getUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    /**
     * Get human-readable file size
     */
    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size;
        
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        
        return $bytes . ' bytes';
    }

    /**
     * Check of bestand een afbeelding is
     */
    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    /**
     * Delete bestand van disk bij model delete
     */
    protected static function booted(): void
    {
        static::deleting(function (Attachment $attachment) {
            Storage::delete($attachment->file_path);
        });
    }
}
```

**Gebruik:**
```php
$attachment = Attachment::find(1);

// Get URL
echo $attachment->url;  // /storage/complaints/photo.jpg

// Check type
if ($attachment->isImage()) {
    echo "<img src='{$attachment->url}'>";
}

// Human readable size
echo $attachment->file_size_human;  // "2.5 MB"

// Delete (also deletes file)
$attachment->delete();
```

---

## 4. Controllers

### 4.1 ComplaintController

**Bestand:** `app/Http/Controllers/ComplaintController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class ComplaintController extends Controller
{
    /**
     * Toon formulier voor nieuwe klacht
     */
    public function create()
    {
        return view('complaints.create');
    }

    /**
     * Sla nieuwe klacht op
     */
    public function store(Request $request)
    {
        // Validatie
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'category' => 'required|in:afval,wegen,openbare_ruimte,openbare_verlichting,groen,overlast,overig',
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'reporter_name' => 'required|string|max:255',
            'reporter_email' => 'required|email|max:255',
            'reporter_phone' => 'nullable|string|max:20',
            'attachments.*' => 'nullable|image|max:5120',  // Max 5MB
        ]);

        // Maak klacht aan
        $complaint = Complaint::create([
            ...$validated,
            'user_id' => auth()->id(),  // null als guest
            'status' => 'open',
        ]);

        // Upload foto's
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $this->uploadAttachment($complaint, $file);
            }
        }

        // Redirect met succes melding
        return redirect()
            ->route('complaint.thanks')
            ->with('complaint_id', $complaint->id);
    }

    /**
     * Upload en verwerk foto
     */
    private function uploadAttachment(Complaint $complaint, $file)
    {
        // Genereer unique filename
        $filename = uniqid() . '.' . $file->extension();
        $path = "complaints/{$complaint->id}";

        // Resize image (max 1920x1080)
        $image = Image::read($file);
        $image->scale(width: 1920);

        // Save to storage
        $fullPath = "{$path}/{$filename}";
        Storage::put($fullPath, $image->encode());

        // Save to database
        $complaint->attachments()->create([
            'file_path' => $fullPath,
            'file_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => Storage::size($fullPath),
        ]);
    }

    /**
     * Bedankt pagina
     */
    public function thanks()
    {
        $complaintId = session('complaint_id');
        
        return view('complaints.thanks', [
            'complaint_id' => $complaintId
        ]);
    }
}
```

**Code Uitleg:**

1. **Validatie Rules:**
```php
'title' => 'required|string|max:255',
```
- `required`: Moet ingevuld zijn
- `string`: Moet tekst zijn
- `max:255`: Max 255 karakters

2. **Image Upload + Resize:**
```php
$image = Image::read($file);
$image->scale(width: 1920);
Storage::put($fullPath, $image->encode());
```
- Voorkomt enorme bestanden
- Behoudt aspect ratio
- Optimaliseer voor web

3. **Relationship Create:**
```php
$complaint->attachments()->create([...]);
```
- Automatisch `complaint_id` ingevuld
- Eloquent relatie magic!

---

### 4.2 ComplaintAdminController

**Bestand:** `app/Http/Controllers/Admin/ComplaintAdminController.php`

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;

class ComplaintAdminController extends Controller
{
    /**
     * Constructor - alleen admins
     */
    public function __construct()
    {
        $this->middleware(['auth', 'can:admin']);
    }

    /**
     * Dashboard met statistieken
     */
    public function dashboard()
    {
        $stats = [
            'total' => Complaint::count(),
            'open' => Complaint::where('status', 'open')->count(),
            'in_progress' => Complaint::where('status', 'in_behandeling')->count(),
            'resolved' => Complaint::where('status', 'opgelost')->count(),
        ];

        $recentComplaints = Complaint::recent()->limit(5)->get();

        return view('admin.dashboard', compact('stats', 'recentComplaints'));
    }

    /**
     * Lijst alle klachten
     */
    public function index(Request $request)
    {
        $query = Complaint::query()->with(['user', 'attachments']);

        // Filter op status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter op categorie
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Zoeken
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('id', $search);
            });
        }

        $complaints = $query->paginate(20);

        return view('admin.complaints.index', compact('complaints'));
    }

    /**
     * Toon individuele klacht
     */
    public function show(Complaint $complaint)
    {
        $complaint->load(['user', 'attachments', 'notes.user', 'statusHistory']);

        return view('admin.complaints.show', compact('complaint'));
    }

    /**
     * Update status
     */
    public function updateStatus(Request $request, Complaint $complaint)
    {
        $validated = $request->validate([
            'status' => 'required|in:open,in_behandeling,opgelost,gesloten',
        ]);

        $oldStatus = $complaint->status;
        $complaint->update(['status' => $validated['status']]);

        // Log status change
        $complaint->statusHistory()->create([
            'old_status' => $oldStatus,
            'new_status' => $validated['status'],
            'user_id' => auth()->id(),
        ]);

        return redirect()
            ->back()
            ->with('success', 'Status bijgewerkt');
    }

    /**
     * Voeg notitie toe
     */
    public function addNote(Request $request, Complaint $complaint)
    {
        $validated = $request->validate([
            'body' => 'required|string|max:5000',
            'is_internal' => 'boolean',
        ]);

        $complaint->notes()->create([
            ...$validated,
            'user_id' => auth()->id(),
        ]);

        return redirect()->back();
    }

    /**
     * Verwijder klacht
     */
    public function destroy(Complaint $complaint)
    {
        $complaint->delete();

        return redirect()
            ->route('admin.complaints.index')
            ->with('success', 'Klacht verwijderd');
    }

    /**
     * Kaart weergave
     */
    public function map()
    {
        $complaints = Complaint::open()
            ->select('id', 'title', 'category', 'lat', 'lng', 'status')
            ->get();

        return view('admin.complaints.map', compact('complaints'));
    }
}
```

**Belangrijke Patterns:**

1. **Middleware in Constructor:**
```php
$this->middleware(['auth', 'can:admin']);
```
→ Alle methods zijn admin-only

2. **Eager Loading:**
```php
$query->with(['user', 'attachments']);
```
→ Voorkomt N+1 query probleem

3. **Search Queries:**
```php
$q->where('title', 'like', "%{$search}%")
```
→ Zoek in meerdere velden

---

### 4.3 DienstenController

**Bestand:** `app/Http/Controllers/DienstenController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Afspraak;
use Illuminate\Http\Request;

class DienstenController extends Controller
{
    /**
     * Constructor - alleen ingelogde users
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Paspoort aanvraag formulier
     */
    public function paspoort()
    {
        return view('diensten.paspoort');
    }

    /**
     * Rijbewijs aanvraag formulier
     */
    public function rijbewijs()
    {
        return view('diensten.rijbewijs');
    }

    /**
     * Vergunning aanvraag formulier
     */
    public function vergunning()
    {
        return view('diensten.vergunning');
    }

    /**
     * Parkeervergunning aanvraag formulier
     */
    public function parkeren()
    {
        return view('diensten.parkeren');
    }

    /**
     * Subsidie aanvraag formulier
     */
    public function subsidie()
    {
        return view('diensten.subsidie');
    }

    /**
     * Sla afspraak op
     */
    public function storeAfspraak(Request $request)
    {
        $validated = $request->validate([
            'dienst' => 'required|string|max:255',
            'datum' => 'required|date|after:today',
            'tijd' => 'required|date_format:H:i',
            'opmerking' => 'nullable|string|max:5000',
        ]);

        Afspraak::create([
            ...$validated,
            'user_id' => auth()->id(),
            'status' => 'gepland',
        ]);

        return redirect()
            ->route('home')
            ->with('success', 'Afspraak succesvol aangevraagd!');
    }
}
```

**Simpele CRUD Controller:**
- Elk method toont een view
- `storeAfspraak` valideert en slaat op
- Middleware zorgt voor auth check

---

## 5. Routes

### 5.1 Web Routes

**Bestand:** `routes/web.php`

```php
<?php

use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\DienstenController;
use App\Http\Controllers\ChatbotController;
use Illuminate\Support\Facades\Route;

// Home page
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Klachten - Publiek toegankelijk
Route::prefix('klachten')->name('complaint.')->group(function () {
    Route::get('/nieuw', [ComplaintController::class, 'create'])->name('create');
    Route::post('/nieuw', [ComplaintController::class, 'store'])->name('store');
    Route::get('/bedankt', [ComplaintController::class, 'thanks'])->name('thanks');
});

// Diensten - Alleen ingelogde users
Route::middleware('auth')->prefix('diensten')->name('diensten.')->group(function () {
    Route::get('/paspoort', [DienstenController::class, 'paspoort'])->name('paspoort');
    Route::get('/rijbewijs', [DienstenController::class, 'rijbewijs'])->name('rijbewijs');
    Route::get('/vergunning', [DienstenController::class, 'vergunning'])->name('vergunning');
    Route::get('/parkeren', [DienstenController::class, 'parkeren'])->name('parkeren');
    Route::get('/subsidie', [DienstenController::class, 'subsidie'])->name('subsidie');
    Route::post('/afspraak', [DienstenController::class, 'storeAfspraak'])->name('afspraak.store');
});

// Chatbot API
Route::post('/api/chatbot', [ChatbotController::class, 'chat'])
    ->middleware('throttle:10,1')
    ->name('chatbot.chat');

// Auth routes (van Laravel Breeze)
require __DIR__.'/auth.php';

// Admin routes (aparte bestand)
require __DIR__.'/admin.php';
```

**Route Patterns:**

1. **Named Routes:**
```php
Route::get('/foo', ...)->name('foo.bar');

// Gebruik in code:
route('foo.bar');  // Generates /foo
```

2. **Route Groups:**
```php
Route::prefix('admin')->group(function() {
    // /admin/users
    // /admin/settings
});
```

3. **Middleware:**
```php
Route::middleware('auth')->group(...);
```

---

### 5.2 Admin Routes

**Bestand:** `routes/admin.php`

```php
<?php

use App\Http\Controllers\Admin\ComplaintAdminController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'can:admin'])
    ->group(function () {
        
        // Dashboard
        Route::get('/dashboard', [ComplaintAdminController::class, 'dashboard'])
            ->name('dashboard');
        
        // Klachten beheer
        Route::prefix('complaints')->name('complaints.')->group(function () {
            Route::get('/', [ComplaintAdminController::class, 'index'])->name('index');
            Route::get('/map', [ComplaintAdminController::class, 'map'])->name('map');
            Route::get('/{complaint}', [ComplaintAdminController::class, 'show'])->name('show');
            Route::patch('/{complaint}/status', [ComplaintAdminController::class, 'updateStatus'])->name('update-status');
            Route::post('/{complaint}/notes', [ComplaintAdminController::class, 'addNote'])->name('add-note');
            Route::delete('/{complaint}', [ComplaintAdminController::class, 'destroy'])->name('destroy');
        });
    });
```

**URL Examples:**
```
/admin/dashboard
/admin/complaints
/admin/complaints/1
/admin/complaints/map
```

---

## 6. Views (Blade Templates)

### 6.1 Layout Structure

**Bestand:** `resources/views/layouts/app.blade.php`

```blade
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Gemeente Portal')</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/gemeente-modern.css') }}">
    @stack('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <a href="{{ route('home') }}" class="logo">Gemeente Portal</a>
            
            <div class="nav-links">
                @auth
                    <a href="{{ route('diensten.paspoort') }}">Diensten</a>
                    <span>{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit">Uitloggen</button>
                    </form>
                @else
                    <a href="{{ route('login') }}">Inloggen</a>
                    <a href="{{ route('register') }}">Registreren</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 Gemeente Portal</p>
    </footer>

    <!-- JavaScript -->
    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>
```

**Blade Directives:**

| Directive | Wat Het Doet |
|-----------|--------------|
| `@yield('content')` | Placeholder voor child content |
| `@section('content')` | Definieer content in child |
| `@extends('layouts.app')` | Inherit van parent layout |
| `@auth` / `@guest` | Check of user ingelogd is |
| `@if` / `@else` / `@endif` | Conditional rendering |
| `@foreach` / `@endforeach` | Loop over array |
| `{{ $var }}` | Echo escaped variabele |
| `{!! $html !!}` | Echo unescaped HTML |
| `@csrf` | CSRF token voor forms |
| `@stack('scripts')` | Push/pop scripts |

---

### 6.2 Complaint Form

**Bestand:** `resources/views/complaints/create.blade.php`

```blade
@extends('layouts.app')

@section('title', 'Klacht Indienen')

@section('content')
<div class="container">
    <h1>Klacht of Melding Indienen</h1>

    <form method="POST" action="{{ route('complaint.store') }}" enctype="multipart/form-data">
        @csrf

        <!-- Titel -->
        <div class="form-group">
            <label for="title">Titel *</label>
            <input type="text" 
                   name="title" 
                   id="title" 
                   value="{{ old('title') }}" 
                   required>
            @error('title')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <!-- Beschrijving -->
        <div class="form-group">
            <label for="description">Beschrijving *</label>
            <textarea name="description" 
                      id="description" 
                      rows="5" 
                      required>{{ old('description') }}</textarea>
            @error('description')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <!-- Categorie -->
        <div class="form-group">
            <label for="category">Categorie *</label>
            <select name="category" id="category" required>
                <option value="">Selecteer categorie</option>
                <option value="afval" {{ old('category') == 'afval' ? 'selected' : '' }}>
                    Afval
                </option>
                <option value="wegen" {{ old('category') == 'wegen' ? 'selected' : '' }}>
                    Wegen
                </option>
                <!-- etc -->
            </select>
        </div>

        <!-- Locatie (GPS) - Hidden fields -->
        <input type="hidden" name="lat" id="lat" value="{{ old('lat') }}">
        <input type="hidden" name="lng" id="lng" value="{{ old('lng') }}">

        <!-- Kaart voor locatie kiezen -->
        <div id="map" style="height: 400px;"></div>

        <!-- Foto's -->
        <div class="form-group">
            <label for="attachments">Foto's (optioneel)</label>
            <input type="file" 
                   name="attachments[]" 
                   id="attachments" 
                   multiple 
                   accept="image/*">
            <small>Max 5MB per foto</small>
        </div>

        <!-- Melder info -->
        <div class="form-group">
            <label for="reporter_name">Uw Naam *</label>
            <input type="text" 
                   name="reporter_name" 
                   value="{{ old('reporter_name', auth()->user()?->name) }}" 
                   required>
        </div>

        <div class="form-group">
            <label for="reporter_email">Uw Email *</label>
            <input type="email" 
                   name="reporter_email" 
                   value="{{ old('reporter_email', auth()->user()?->email) }}" 
                   required>
        </div>

        <!-- Submit -->
        <button type="submit" class="btn btn-primary">
            Klacht Indienen
        </button>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // Initialize Leaflet map
    const map = L.map('map').setView([52.3676, 4.9041], 13);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    
    let marker;
    
    map.on('click', function(e) {
        if (marker) map.removeLayer(marker);
        
        marker = L.marker(e.latlng).addTo(map);
        
        document.getElementById('lat').value = e.latlng.lat;
        document.getElementById('lng').value = e.latlng.lng;
    });
</script>
@endpush
```

**Belangrijke Blade Features:**

1. **Old Input:**
```blade
value="{{ old('title') }}"
```
→ Behoudt input bij validation errors

2. **Error Display:**
```blade
@error('title')
    <span class="error">{{ $message }}</span>
@enderror
```
→ Toont validation errors

3. **Optional Chaining:**
```blade
{{ auth()->user()?->name }}
```
→ Geen error als user null is

4. **Scripts Pushen:**
```blade
@push('scripts')
    <script>...</script>
@endpush
```
→ Adds to stack in layout

---

## 7. Frontend Assets

### 7.1 CSS Structure

**Bestand:** `resources/css/gemeente-modern.css`

```css
/* Design Tokens */
:root {
    --color-primary: #3b82f6;
    --color-success: #10b981;
    --color-error: #ef4444;
    --color-warning: #f59e0b;
    
    --font-sans: system-ui, -apple-system, sans-serif;
    --space-4: 1rem;
    --radius-md: 0.5rem;
}

/* Base Styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: var(--font-sans);
    line-height: 1.6;
    color: #1f2937;
}

/* Components */
.btn {
    padding: 0.75rem 1.5rem;
    border-radius: var(--radius-md);
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-primary {
    background: var(--color-primary);
    color: white;
}

.btn-primary:hover {
    background: #2563eb;
}

.form-group {
    margin-bottom: var(--space-4);
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
}

.form-group input,
.form-group textarea,
.form-group select {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: var(--radius-md);
}

.alert {
    padding: var(--space-4);
    border-radius: var(--radius-md);
    margin-bottom: var(--space-4);
}

.alert-success {
    background: #d1fae5;
    color: #065f46;
}

.alert-error {
    background: #fee2e2;
    color: #991b1b;
}
```

---

### 7.2 JavaScript

**Bestand:** `resources/js/app.js`

```javascript
// Import dependencies
import Alpine from 'alpinejs';

// Make Alpine available globally
window.Alpine = Alpine;

// Start Alpine
Alpine.start();

// Chatbot widget (from chatbot.js)
import './chatbot';

// Form utilities
document.addEventListener('DOMContentLoaded', function() {
    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });

    // Confirm before delete
    const deleteButtons = document.querySelectorAll('[data-confirm]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm(this.dataset.confirm)) {
                e.preventDefault();
            }
        });
    });
});
```

---

## 8. Tests

### 8.1 Feature Test

**Bestand:** `tests/Feature/ComplaintTest.php`

```php
<?php

use App\Models\Complaint;
use App\Models\User;

test('guest can submit complaint', function () {
    $response = $this->post(route('complaint.store'), [
        'title' => 'Test Klacht',
        'description' => 'Test beschrijving',
        'category' => 'afval',
        'lat' => 52.3676,
        'lng' => 4.9041,
        'reporter_name' => 'Jan de Vries',
        'reporter_email' => 'jan@example.com',
    ]);

    $response->assertRedirect(route('complaint.thanks'));
    
    $this->assertDatabaseHas('complaints', [
        'title' => 'Test Klacht',
        'status' => 'open',
    ]);
});

test('admin can view dashboard', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get('/admin/dashboard');

    $response->assertStatus(200);
});

test('regular user cannot access admin', function () {
    $user = User::factory()->create(['role' => 'user']);

    $response = $this->actingAs($user)->get('/admin/dashboard');

    $response->assertStatus(403);
});
```

**Run tests:**
```bash
php artisan test
```

---

## Conclusie

Dit document bevat **ALLE** belangrijke bestanden en code van het project met uitleg.

**Gebruik dit om:**
- ✅ Te begrijpen hoe alles werkt
- ✅ Code te kunnen uitleggen bij presentatie
- ✅ Bugs op te lossen
- ✅ Nieuwe features toe te voegen

**Volgende PDF:** Testing & Deployment

---

**Einde PDF 2**
