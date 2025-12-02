# Laravel Database & Eloquent - Gemeente Portal

## 📋 Inhoudsopgave

1. [Database Configuratie](#database-configuratie)
2. [Migrations](#migrations)
3. [Eloquent ORM](#eloquent-orm)
4. [Query Builder](#query-builder)
5. [Relationships](#relationships)
6. [Seeders & Factories](#seeders--factories)

---

## ⚙️ Database Configuratie

### Database Connection

```php
// config/database.php
'connections' => [
    'sqlite' => [
        'driver' => 'sqlite',
        'url' => env('DATABASE_URL'),
        'database' => env('DB_DATABASE', database_path('database.sqlite')),
        'prefix' => '',
        'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
    ],
    
    'mysql' => [
        'driver' => 'mysql',
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', '3306'),
        'database' => env('DB_DATABASE', 'forge'),
        'username' => env('DB_USERNAME', 'forge'),
        'password' => env('DB_PASSWORD', ''),
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'strict' => true',
        'engine' => null,
    ],
];
```

### Environment Variables

```bash
# .env file
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite

# Of voor MySQL
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gemeente
DB_USERNAME=root
DB_PASSWORD=secret
```

---

## 🔄 Migrations

### Wat zijn Migrations?

Migrations zijn "version control" voor je database. Elke migration file definieert een schema wijziging.

### Migration Maken

```bash
# Maak nieuwe migration
php artisan make:migration create_complaints_table

# Met model
php artisan make:model Complaint -m

# Alle opties
php artisan make:model Complaint -mfsc
# -m = migration
# -f = factory
# -s = seeder  
# -c = controller
```

### Complaint Migration Voorbeeld

```php
<?php
// database/migrations/2025_09_22_091228_create_complaints_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
            // Primary Key
            $table->id(); 
            // BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
            
            // String columns
            $table->string('title'); 
            // VARCHAR(255) NOT NULL
            
            $table->string('category')->nullable(); 
            // VARCHAR(255) NULL
            
            $table->string('reporter_email', 100);
            // VARCHAR(100) NOT NULL
            
            // Text columns
            $table->text('description');
            // TEXT NOT NULL
            
            $table->text('internal_notes')->nullable();
            // TEXT NULL
            
            // Enum column
            $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])
                  ->default('open');
            // ENUM(...) DEFAULT 'open'
            
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])
                  ->default('medium');
            
            // Numeric columns
            $table->decimal('lat', 10, 8)->nullable();
            // DECIMAL(10,8) NULL - Precision 10 digits, 8 after decimal
            
            $table->decimal('lng', 11, 8)->nullable();
            // DECIMAL(11,8) NULL
            
            // Foreign Key
            $table->foreignId('assigned_to')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            // BIGINT UNSIGNED NULL
            // FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
            
            // Timestamps
            $table->timestamp('resolved_at')->nullable();
            // TIMESTAMP NULL
            
            $table->timestamps();
            // created_at TIMESTAMP NULL
            // updated_at TIMESTAMP NULL
            
            // Indexes
            $table->index('status'); // Voor filtering
            $table->index('reporter_email'); // Voor tracking
            $table->index(['lat', 'lng']); // Composite index voor locatie queries
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
```

### Beschikbare Column Types

```php
// Integers
$table->id();                      // BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
$table->bigInteger('votes');       // BIGINT
$table->integer('votes');          // INT
$table->smallInteger('votes');     // SMALLINT
$table->tinyInteger('votes');      // TINYINT

// Strings
$table->string('name', 100);       // VARCHAR(100)
$table->text('description');       // TEXT
$table->char('code', 4);          // CHAR(4)
$table->enum('status', [...]);    // ENUM

// Decimals
$table->decimal('amount', 8, 2);  // DECIMAL(8,2) - 8 digits, 2 decimals
$table->float('amount', 8, 2);    // FLOAT
$table->double('amount', 8, 2);   // DOUBLE

// Booleans
$table->boolean('confirmed');      // BOOLEAN (TINYINT 0/1)

// Dates
$table->date('created_date');      // DATE
$table->datetime('created_at');    // DATETIME
$table->timestamp('added_on');     // TIMESTAMP
$table->timestamps();              // created_at + updated_at
$table->softDeletes();            // deleted_at TIMESTAMP

// JSON
$table->json('options');          // JSON column

// Binary
$table->binary('data');           // BLOB
```

### Column Modifiers

```php
$table->string('email')->nullable();           // NULL allowed
$table->string('name')->default('Guest');      // DEFAULT value
$table->integer('votes')->unsigned();          // UNSIGNED
$table->string('title')->unique();            // UNIQUE constraint
$table->string('email')->index();             // INDEX
$table->decimal('amount', 8, 2)->after('id'); // Column position
$table->string('name')->comment('User name'); // Column comment
```

### Foreign Keys

```php
// Method 1: foreignId (modern, clean)
$table->foreignId('user_id')
      ->constrained()           // References users(id)
      ->onDelete('cascade');    // ON DELETE CASCADE

// Method 2: Explicit foreign key
$table->unsignedBigInteger('user_id');
$table->foreign('user_id')
      ->references('id')
      ->on('users')
      ->onDelete('cascade');

// Delete actions
->onDelete('cascade')    // Delete related records
->onDelete('set null')   // Set FK to NULL
->onDelete('restrict')   // Prevent deletion (default)
->nullOnDelete()         // Shorthand voor set null
->cascadeOnDelete()      // Shorthand voor cascade
```

### Modifying Columns

```php
// Add column to existing table
php artisan make:migration add_phone_to_complaints_table

public function up(): void
{
    Schema::table('complaints', function (Blueprint $table) {
        $table->string('reporter_phone')->nullable()->after('reporter_email');
    });
}

// Modify existing column (requires doctrine/dbal)
public function up(): void
{
    Schema::table('complaints', function (Blueprint $table) {
        $table->string('title', 500)->change(); // Increase length
        $table->text('description')->nullable()->change(); // Make nullable
    });
}

// Rename column
public function up(): void
{
    Schema::table('complaints', function (Blueprint $table) {
        $table->renameColumn('lat', 'latitude');
    });
}

// Drop column
public function up(): void
{
    Schema::table('complaints', function (Blueprint $table) {
        $table->dropColumn('internal_notes');
        // Of multiple
        $table->dropColumn(['lat', 'lng']);
    });
}

// Drop foreign key
$table->dropForeign(['user_id']);
$table->dropForeign('complaints_user_id_foreign'); // By name
```

### Running Migrations

```bash
# Run alle pending migrations
php artisan migrate

# Rollback laatste batch
php artisan migrate:rollback

# Rollback alle migrations
php artisan migrate:reset

# Drop all + re-run
php artisan migrate:fresh

# Fresh + seed data
php artisan migrate:fresh --seed

# Check migration status
php artisan migrate:status

# Run specific migration
php artisan migrate --path=/database/migrations/2025_create_complaints_table.php
```

---

## 🎯 Eloquent ORM

### Model Basis

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Complaint extends Model
{
    // Tabel naam (auto: complaints, pluralized)
    protected $table = 'complaints';
    
    // Primary key (auto: id)
    protected $primaryKey = 'id';
    
    // Auto-increment (auto: true)
    public $incrementing = true;
    
    // Key type (auto: int)
    protected $keyType = 'int';
    
    // Timestamps (auto: true)
    public $timestamps = true;
    
    // Datetime format
    protected $dateFormat = 'Y-m-d H:i:s';
    
    // Database connection (multi-database)
    protected $connection = 'mysql';
    
    // Soft deletes
    use SoftDeletes;
}
```

### Mass Assignment Protection

```php
class Complaint extends Model
{
    // Whitelist - ALLEEN deze velden mogen mass-assigned worden
    protected $fillable = [
        'title',
        'description',
        'category',
        'status',
        'reporter_email',
    ];
    
    // Of Blacklist - ALLES behalve deze velden
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];
    
    // Of alles toestaan (UNSAFE!)
    protected $guarded = [];
}

// Gebruik:
Complaint::create([
    'title' => 'Test',
    'description' => 'Test beschrijving',
    'category' => 'wegen',
    'id' => 999, // IGNORED als niet in $fillable!
]);
```

**Waarom Mass Assignment Protection?**
```php
// Zonder protection:
$user = User::create($request->all());

// Hacker stuurt:
// {
//   "name": "John",
//   "email": "john@example.com", 
//   "is_admin": true  // ← Oops!
// }
```

### Type Casting

```php
class Complaint extends Model
{
    protected $casts = [
        // Automatische type conversie
        'lat' => 'decimal:8',           // String → Decimal
        'lng' => 'decimal:8',
        'priority' => 'string',
        'resolved_at' => 'datetime',    // String → Carbon instance
        'created_at' => 'datetime:Y-m-d H:i:s',
        'options' => 'array',           // JSON → Array
        'is_urgent' => 'boolean',       // 0/1 → true/false
    ];
}

// Gebruik:
$complaint = Complaint::find(1);
$complaint->resolved_at->format('d-m-Y'); // Carbon method
$complaint->options['key']; // Array access
$complaint->is_urgent === true; // Boolean
```

### Accessors & Mutators

```php
class Complaint extends Model
{
    // Accessor - get virtual attribute
    public function getTrackingNumberAttribute(): string
    {
        return sprintf('GEM-%05d', $this->id);
    }
    
    // Gebruik:
    $complaint->tracking_number; // "GEM-00123"
    
    // Accessor - modify existing attribute
    public function getTitleAttribute($value): string
    {
        return ucwords($value); // Capitalize each word
    }
    
    // Mutator - modify before saving
    public function setReporterEmailAttribute($value): void
    {
        $this->attributes['reporter_email'] = strtolower($value);
    }
    
    // Gebruik:
    $complaint->reporter_email = 'TEST@EXAMPLE.COM';
    $complaint->save(); // Saved as: test@example.com
}
```

### Scopes

```php
class Complaint extends Model
{
    // Local Scope - reusable query filter
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }
    
    public function scopeUrgent($query)
    {
        return $query->where('priority', 'urgent');
    }
    
    public function scopeWithLocation($query)
    {
        return $query->whereNotNull('lat')
                     ->whereNotNull('lng');
    }
    
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}

// Gebruik:
Complaint::open()->get(); // WHERE status = 'open'
Complaint::open()->urgent()->get(); // Chain scopes
Complaint::withLocation()->get();
Complaint::category('wegen')->get();
```

### Events

```php
class Complaint extends Model
{
    // Boot method - register events
    protected static function boot()
    {
        parent::boot();
        
        // Before creating
        static::creating(function ($complaint) {
            // Generate tracking number
            $complaint->tracking_number = uniqid('GEM-');
        });
        
        // After creating
        static::created(function ($complaint) {
            // Send notification email
            Mail::to($complaint->reporter_email)->send(
                new ComplaintReceived($complaint)
            );
        });
        
        // Before updating
        static::updating(function ($complaint) {
            if ($complaint->isDirty('status')) {
                // Status changed - log it
                $complaint->statusHistories()->create([
                    'from' => $complaint->getOriginal('status'),
                    'to' => $complaint->status,
                ]);
            }
        });
        
        // After deleting
        static::deleted(function ($complaint) {
            // Delete related files
            Storage::deleteDirectory("complaints/{$complaint->id}");
        });
    }
}
```

---

## 🔍 Query Builder

### Basic Queries

```php
// Retrieve all
Complaint::all();
// SELECT * FROM complaints

// Where clause
Complaint::where('status', 'open')->get();
// SELECT * FROM complaints WHERE status = 'open'

// Multiple where (AND)
Complaint::where('status', 'open')
         ->where('priority', 'high')
         ->get();
// SELECT * FROM complaints WHERE status = 'open' AND priority = 'high'

// Or where
Complaint::where('status', 'open')
         ->orWhere('status', 'in_progress')
         ->get();
// SELECT * FROM complaints WHERE status = 'open' OR status = 'in_progress'

// Where In
Complaint::whereIn('status', ['open', 'in_progress'])->get();
// SELECT * FROM complaints WHERE status IN ('open', 'in_progress')

// Where Not In
Complaint::whereNotIn('priority', ['low'])->get();

// Where Null
Complaint::whereNull('assigned_to')->get();
// SELECT * FROM complaints WHERE assigned_to IS NULL

// Where Not Null
Complaint::whereNotNull('lat')->get();

// Where Between
Complaint::whereBetween('created_at', [$start, $end])->get();

// Where Date
Complaint::whereDate('created_at', '2025-11-20')->get();

// Where operators
Complaint::where('priority', '!=', 'low')->get();
Complaint::where('created_at', '>', now()->subDays(7))->get();
```

### Advanced Queries

```php
// Group conditions
Complaint::where('status', 'open')
         ->where(function($query) {
             $query->where('priority', 'high')
                   ->orWhere('priority', 'urgent');
         })
         ->get();
// WHERE status = 'open' AND (priority = 'high' OR priority = 'urgent')

// When - conditional query
Complaint::when($request->status, function($query, $status) {
    return $query->where('status', $status);
})->get();

// Select specific columns
Complaint::select('id', 'title', 'status')->get();
// SELECT id, title, status FROM complaints

// Distinct
Complaint::select('status')->distinct()->get();
// SELECT DISTINCT status FROM complaints

// Order By
Complaint::orderBy('created_at', 'desc')->get();
Complaint::latest()->get(); // Shorthand for orderBy created_at desc
Complaint::oldest()->get(); // Shorthand for orderBy created_at asc

// Limit & Offset
Complaint::take(10)->get(); // LIMIT 10
Complaint::skip(20)->take(10)->get(); // OFFSET 20 LIMIT 10

// Count
Complaint::count(); // SELECT COUNT(*) FROM complaints
Complaint::where('status', 'open')->count();

// Other aggregates
Complaint::max('priority');
Complaint::min('created_at');
Complaint::avg('rating');
Complaint::sum('amount');

// Exists
if (Complaint::where('status', 'open')->exists()) {
    // At least one open complaint
}

// First or fail
$complaint = Complaint::where('id', 123)->firstOrFail();
// Throws ModelNotFoundException if not found (404)

// Find or fail
$complaint = Complaint::findOrFail(123);

// First or create
$complaint = Complaint::firstOrCreate(
    ['reporter_email' => 'test@example.com'], // Search criteria
    ['title' => 'Test', 'description' => '...'] // Additional data if creating
);

// Update or create
Complaint::updateOrCreate(
    ['id' => 123], // Search
    ['status' => 'resolved'] // Update/Create data
);
```

### Joins

```php
// Inner Join
$complaints = Complaint::join('users', 'complaints.assigned_to', '=', 'users.id')
    ->select('complaints.*', 'users.name as assigned_name')
    ->get();
// SELECT complaints.*, users.name as assigned_name
// FROM complaints
// INNER JOIN users ON complaints.assigned_to = users.id

// Left Join
Complaint::leftJoin('users', 'complaints.assigned_to', '=', 'users.id')
    ->get();

// Join with closure
Complaint::join('users', function ($join) {
    $join->on('complaints.assigned_to', '=', 'users.id')
         ->where('users.active', '=', 1);
})->get();
```

### Subqueries

```php
// Subquery select
$complaints = Complaint::select('*')
    ->selectSub(function ($query) {
        $query->selectRaw('COUNT(*)')
              ->from('notes')
              ->whereColumn('complaints.id', 'notes.complaint_id');
    }, 'notes_count')
    ->get();

// Where subquery
Complaint::where('id', function ($query) {
    $query->select('complaint_id')
          ->from('notes')
          ->where('user_id', auth()->id());
})->get();
```

### Raw Queries

```php
// Raw expression
Complaint::selectRaw('COUNT(*) as count, status')
         ->groupBy('status')
         ->get();

// Where raw
Complaint::whereRaw('DATE(created_at) = CURDATE()')->get();

// Order by raw
Complaint::orderByRaw('FIELD(status, "urgent", "high", "medium", "low")')->get();

// Raw query (avoid if possible!)
$complaints = DB::select('SELECT * FROM complaints WHERE status = ?', ['open']);
```

---

## 🔗 Relationships

### One-to-Many (1:N)

```php
// Complaint Model (parent)
class Complaint extends Model
{
    public function attachments(): HasMany
    {
        return $this->hasMany(ComplaintImage::class);
    }
    
    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }
}

// ComplaintImage Model (child)
class ComplaintImage extends Model
{
    protected $table = 'attachments'; // Custom table name
    
    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class);
    }
}

// Usage:
$complaint = Complaint::find(1);

// Get all attachments
$photos = $complaint->attachments; // Collection

// Count
$count = $complaint->attachments()->count(); // Niet ->attachments->count()!

// Query
$jpgs = $complaint->attachments()
    ->where('mime', 'image/jpeg')
    ->get();

// Reverse
$photo = ComplaintImage::find(1);
$complaint = $photo->complaint; // Single model
```

### Belongs To (N:1)

```php
// Complaint Model
class Complaint extends Model
{
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
        // foreign_key: assigned_to (auto: user_id)
        // owner_key: id (auto)
    }
}

// User Model
class User extends Model
{
    public function assignedComplaints(): HasMany
    {
        return $this->hasMany(Complaint::class, 'assigned_to');
    }
}

// Usage:
$complaint = Complaint::find(1);
$user = $complaint->assignedTo; // Kan NULL zijn!

if ($complaint->assignedTo) {
    echo $complaint->assignedTo->name;
}

// Reverse
$user = User::find(1);
$complaints = $user->assignedComplaints;
```

### Many-to-Many (N:M)

```php
// User Model
class User extends Model
{
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
        // Pivot table: role_user (alphabetical)
        // foreign_key: user_id
        // related_key: role_id
    }
}

// Role Model
class Role extends Model
{
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}

// Custom pivot table name
public function roles(): BelongsToMany
{
    return $this->belongsToMany(Role::class, 'user_roles');
}

// Custom keys
public function roles(): BelongsToMany
{
    return $this->belongsToMany(
        Role::class,
        'user_roles',      // table
        'user_id',         // foreign key
        'role_id'          // related key
    );
}

// Usage:
$user = User::find(1);
$roles = $user->roles; // Collection

// Attach (add relationship)
$user->roles()->attach($roleId);
$user->roles()->attach([1, 2, 3]);

// Detach (remove relationship)
$user->roles()->detach($roleId);
$user->roles()->detach(); // Remove all

// Sync (replace all relationships)
$user->roles()->sync([1, 2, 3]);

// Toggle
$user->roles()->toggle([1, 2]);

// Check
$user->roles->contains($roleId);
```

### Eager Loading

```php
// ❌ N+1 Query Problem
$complaints = Complaint::all(); // 1 query
foreach ($complaints as $complaint) {
    echo $complaint->assignedTo->name; // N queries! (1 per complaint)
}
// Total: 1 + N queries

// ✅ Eager Loading (2 queries)
$complaints = Complaint::with('assignedTo')->get(); // 2 queries
foreach ($complaints as $complaint) {
    echo $complaint->assignedTo->name; // Geen extra queries!
}

// Multiple relationships
$complaint = Complaint::with(['attachments', 'notes', 'assignedTo'])->find(1);

// Nested relationships
$complaint = Complaint::with('notes.user')->find(1);
// Load complaints, their notes, and the user who created each note

// Conditional eager loading
Complaint::with(['assignedTo' => function ($query) {
    $query->select('id', 'name', 'email');
}])->get();

// Lazy eager loading (after model loaded)
$complaints = Complaint::all();
$complaints->load('attachments');
```

### Relationship Queries

```php
// Has relationship
Complaint::has('attachments')->get();
// Complaints with at least 1 attachment

// Has with count
Complaint::has('attachments', '>=', 3)->get();
// Complaints with 3+ attachments

// Where Has (filter by related model)
Complaint::whereHas('assignedTo', function ($query) {
    $query->where('name', 'like', '%John%');
})->get();
// Complaints assigned to user with 'John' in name

// Doesn't have
Complaint::doesntHave('attachments')->get();
// Complaints without any attachments

// Count relationships
$complaints = Complaint::withCount('attachments')->get();
foreach ($complaints as $complaint) {
    echo $complaint->attachments_count; // Auto-added attribute
}
```

---

## 🌱 Seeders & Factories

### Database Seeder

```php
<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Call specific seeders
        $this->call([
            UserSeeder::class,
            ComplaintSeeder::class,
        ]);
    }
}
```

### User Seeder

```php
<?php
// database/seeders/UserSeeder.php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles first
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);
        
        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@gemeente.nl',
            'password' => bcrypt('admin123'),
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');
        
        // Create regular user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
        $user->assignRole('user');
        
        // Create 10 random users with factory
        User::factory()->count(10)->create();
    }
}
```

### Complaint Seeder

```php
<?php
// database/seeders/ComplaintSeeder.php

namespace Database\Seeders;

use App\Models\Complaint;
use App\Models\User;
use Illuminate\Database\Seeder;

class ComplaintSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@gemeente.nl')->first();
        
        $complaints = [
            [
                'title' => 'Kapotte straatlamp',
                'description' => 'De straatlamp op de hoek is kapot en geeft geen licht meer.',
                'category' => 'straatverlichting',
                'priority' => 'high',
                'status' => 'open',
                'lat' => 52.370216,
                'lng' => 4.895168,
                'location' => 'Dam, Amsterdam',
                'reporter_name' => 'Jan Jansen',
                'reporter_email' => 'jan@example.com',
                'assigned_to' => $admin->id,
            ],
            [
                'title' => 'Gat in de weg',
                'description' => 'Er zit een groot gat in de Hoofdstraat dat gevaarlijk is voor fietsers.',
                'category' => 'wegen_onderhoud',
                'priority' => 'urgent',
                'status' => 'in_progress',
                'lat' => 52.367600,
                'lng' => 4.904100,
                'location' => 'Hoofdstraat 123',
                'reporter_name' => 'Marie de Vries',
                'reporter_email' => 'marie@example.com',
                'assigned_to' => $admin->id,
            ],
        ];
        
        foreach ($complaints as $data) {
            $complaint = Complaint::create($data);
            
            // Create status history
            $complaint->statusHistories()->create([
                'from' => 'new',
                'to' => $data['status'],
                'user_id' => $admin->id,
            ]);
        }
        
        // Create 20 random complaints with factory
        Complaint::factory()->count(20)->create();
    }
}
```

### Factory

```php
<?php
// database/factories/ComplaintFactory.php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ComplaintFactory extends Factory
{
    public function definition(): array
    {
        $statuses = ['open', 'in_progress', 'resolved', 'closed'];
        $priorities = ['low', 'medium', 'high', 'urgent'];
        $categories = [
            'wegen_onderhoud', 'straatverlichting', 'afval_ophaling',
            'graffiti', 'parken', 'riool_verstopt', 'geluidsoverlast'
        ];
        
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'category' => fake()->randomElement($categories),
            'priority' => fake()->randomElement($priorities),
            'status' => fake()->randomElement($statuses),
            'lat' => fake()->latitude(52.3, 52.4),
            'lng' => fake()->longitude(4.8, 4.9),
            'location' => fake()->streetAddress(),
            'reporter_name' => fake()->name(),
            'reporter_email' => fake()->safeEmail(),
            'reporter_phone' => fake()->phoneNumber(),
            'assigned_to' => User::role('admin')->inRandomOrder()->first()?->id,
            'created_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ];
    }
}
```

### Running Seeders

```bash
# Run all seeders
php artisan db:seed

# Run specific seeder
php artisan db:seed --class=ComplaintSeeder

# Fresh migrate + seed
php artisan migrate:fresh --seed
```

---

## 🎯 Best Practices Samenvatting

### ✅ DO's
1. **Eager load relationships** - Voorkom N+1 queries
2. **Use scopes** - Herbruikbare query filters
3. **Type cast attributes** - Automatische conversie
4. **Protect mass assignment** - `$fillable` of `$guarded`
5. **Use transactions** - Bij multiple DB operations
6. **Index frequently queried columns**
7. **Use migrations** - Never modify database manually
8. **Validate before saving** - Use Form Requests

### ❌ DON'Ts
1. **Queries in loops** - Use eager loading
2. **Raw SQL without bindings** - SQL injection risk
3. **Skip migrations** - Always use version control
4. **Manual timestamps** - Let Eloquent handle it
5. **Forget indexes** - Slow queries
6. **Over-eager load** - Only load what you need

---

**Next:** [Laravel Security & Auth](LARAVEL_SECURITY.md)
