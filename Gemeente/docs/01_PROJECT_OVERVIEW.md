# 🏛️ Gemeente Klachtensysteem - Complete Project Overzicht

**Datum:** 6 oktober 2025  
**Versie:** 1.0  
**Auteur:** Gemeente Development Team

---

## 📋 Inhoudsopgave

1. [Project Beschrijving](#project-beschrijving)
2. [Technologie Stack](#technologie-stack)
3. [Project Architectuur](#project-architectuur)
4. [Belangrijkste Features](#belangrijkste-features)
5. [Directory Structuur](#directory-structuur)
6. [Installatie & Setup](#installatie--setup)

---

## 🎯 Project Beschrijving

Het **Gemeente Klachtensysteem** is een modern webapplicatie gebouwd voor het beheren van burgerklachten. Het systeem biedt:

- **Publieke Interface**: Burgers kunnen klachten indienen via een gebruiksvriendelijke website
- **Admin Dashboard**: Beheerders kunnen klachten beheren, statistieken bekijken en rapporten genereren
- **RESTful API**: Complete API voor integratie met externe systemen
- **MCP Server**: Model Context Protocol server voor AI-gestuurde automatisering
- **Interactive Map**: GPS-gebaseerde visualisatie van klachten op een interactieve kaart

---

## 🛠️ Technologie Stack

### Backend
- **Laravel 12.29.0** - Modern PHP framework met Eloquent ORM
- **PHP 8.3+** - Nieuwste PHP versie met type safety en performance
- **MySQL/SQLite** - Relationele database
- **Spatie Permissions** - Role-based access control (RBAC)

### Frontend
- **Blade Templates** - Server-side rendering
- **Tailwind CSS 3.x** - Utility-first CSS framework
- **Alpine.js** - Lightweight JavaScript framework
- **Vite** - Modern build tool voor assets
- **Modern CSS** - Custom animations, glassmorphism, gradients

### API & Integratie
- **Laravel Sanctum** - API authentication
- **RESTful API** - JSON-based endpoints
- **CORS Support** - Cross-origin resource sharing

### MCP Server (Node.js)
- **TypeScript 5.3.2** - Type-safe JavaScript
- **Model Context Protocol SDK ^0.5.0** - MCP framework
- **Playwright ^1.40.0** - Browser automation
- **Axios 1.6.2** - HTTP client
- **Node.js 18+** - Runtime environment

### Mapping & Visualization
- **OpenStreetMap** - Open-source mapping
- **Leaflet.js** - Interactive map library
- **GPS Coordinates** - Real-time location tracking

---

## 🏗️ Project Architectuur

### High-Level Architectuur

```
┌─────────────────────────────────────────────────────────────┐
│                    GEBRUIKERS INTERFACE                      │
├──────────────┬────────────────┬──────────────┬──────────────┤
│  Publieke    │     Admin      │   API        │   MCP Tools  │
│  Website     │   Dashboard    │  Endpoints   │  (AI/Auto)   │
└──────┬───────┴────────┬───────┴──────┬───────┴──────┬───────┘
       │                │              │              │
       └────────────────┴──────────────┴──────────────┘
                         │
                    ┌────┴────┐
                    │ LARAVEL │
                    │ BACKEND │
                    └────┬────┘
                         │
        ┌────────────────┼────────────────┐
        │                │                │
   ┌────┴────┐     ┌────┴────┐     ┌────┴────┐
   │ Models  │     │  API    │     │ Services│
   │(Eloquent)│     │Controllers│   │         │
   └────┬────┘     └────┬────┘     └────┬────┘
        │               │               │
        └───────────────┴───────────────┘
                       │
                  ┌────┴────┐
                  │DATABASE │
                  │ MySQL/  │
                  │ SQLite  │
                  └─────────┘

┌─────────────────────────────────────────────────────────────┐
│              MCP SERVER (Separate Node.js App)               │
├──────────────┬────────────────┬──────────────────────────────┤
│  API Tools   │ Browser Tools  │    Playwright Automation     │
│  (8 tools)   │  (11 tools)    │    (Headless/Headful)        │
└──────────────┴────────────────┴──────────────────────────────┘
```

### MVC Pattern (Laravel)

```
Request → Route → Controller → Model → Database
                      ↓
                    View (Blade)
                      ↓
                   Response
```

### API Flow

```
HTTP Request → Middleware → API Controller → Service Layer → Model → Database
                  ↓              ↓
            Authentication   Validation
                  ↓              ↓
              JSON Response ← Transform/Format
```

---

## ✨ Belangrijkste Features

### 1. **Klachten Management System**
- ✅ Indienen van klachten door burgers
- ✅ Categorisatie (Openbare Ruimte, Verkeer, Overlast, etc.)
- ✅ Prioriteit levels (Laag, Normaal, Hoog, Urgent)
- ✅ Status tracking (Open, In behandeling, Afgerond, Gesloten)
- ✅ GPS locatie met adres
- ✅ Foto uploads (tot 5 per klacht)
- ✅ Notes/Comments systeem

### 2. **Admin Dashboard**
- 📊 Real-time statistieken
- 🗺️ Interactive map view
- 📈 Grafieken en analytics
- 👥 User management
- 🔐 Role-based permissions
- 📝 Bulk operations

### 3. **RESTful API (8 Endpoints)**
```
GET    /api/complaints              - Lijst van klachten (met filters)
GET    /api/complaints/{id}         - Specifieke klacht details
POST   /api/complaints              - Nieuwe klacht aanmaken
PATCH  /api/complaints/{id}/status  - Status updaten
POST   /api/complaints/{id}/notes   - Note toevoegen
GET    /api/complaints/search       - Full-text search
GET    /api/complaints/map          - GPS data voor kaart
GET    /api/statistics              - Dashboard statistieken
```

### 4. **MCP Server (19 Tools)**

#### API Tools (8)
1. `get_complaints` - Klachten ophalen met filters
2. `get_complaint_by_id` - Specifieke klacht details
3. `create_complaint` - Nieuwe klacht via API
4. `update_complaint_status` - Status wijzigen
5. `add_complaint_note` - Notitie toevoegen
6. `get_statistics` - Analytics data
7. `search_complaints` - Zoeken in klachten
8. `get_complaint_map_data` - GPS data

#### Browser Automation Tools (11)
1. `browser_goto` - Navigeer naar URL
2. `browser_login` - Auto-login op dashboard
3. `browser_screenshot` - Screenshot maken
4. `browser_extract_text` - Text scraping
5. `browser_extract_table` - Table data extractie
6. `browser_fill_form` - Formulier invullen
7. `browser_click` - Element klikken
8. `browser_submit_complaint` - E2E klacht indienen
9. `browser_get_dashboard_stats` - Live stats ophalen
10. `browser_get_page_info` - Page analysis
11. `browser_close` - Browser cleanup

### 5. **Modern UI/UX Features**
- 🎨 Glassmorphism design
- ✨ Smooth animations & transitions
- 📱 Responsive mobile design
- 🌙 Modern color schemes
- 🎭 Floating label forms
- 📤 Drag & drop file upload
- 💬 Modern chatbot styling
- 🗺️ Interactive OpenStreetMap

### 6. **Security Features**
- 🔒 CSRF Protection
- 🔑 Authentication & Authorization
- 🛡️ Input validation & sanitization
- 🚫 SQL Injection prevention (Eloquent)
- 🔐 Password hashing (bcrypt)
- 📜 Rate limiting
- 🔗 Secure file uploads

---

## 📁 Directory Structuur

```
Gemeente/
├── app/                          # Laravel Application Code
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/
│   │   │   │   ├── ComplaintApiController.php    # API endpoints
│   │   │   │   └── StatisticsController.php      # Statistics API
│   │   │   ├── Admin/
│   │   │   │   ├── ComplaintController.php       # Admin CRUD
│   │   │   │   └── DashboardController.php       # Admin dashboard
│   │   │   ├── Auth/
│   │   │   │   └── LoginController.php           # Authentication
│   │   │   └── ComplaintController.php           # Public complaints
│   │   └── Middleware/
│   │       ├── AdminMiddleware.php               # Admin access check
│   │       └── Authenticate.php                  # Auth check
│   ├── Models/
│   │   ├── Complaint.php                         # Complaint model
│   │   ├── ComplaintNote.php                     # Notes model
│   │   ├── User.php                              # User model
│   │   └── ComplaintPhoto.php                    # Photos model
│   ├── Services/                                 # Business logic
│   └── Policies/                                 # Authorization policies
│
├── database/
│   ├── migrations/                               # Database schema
│   │   ├── create_complaints_table.php
│   │   ├── create_complaint_notes_table.php
│   │   └── create_complaint_photos_table.php
│   └── seeders/                                  # Sample data
│
├── resources/
│   ├── views/                                    # Blade templates
│   │   ├── admin/                                # Admin views
│   │   ├── complaints/                           # Public complaint views
│   │   ├── auth/                                 # Login/register views
│   │   └── layouts/                              # Layout templates
│   ├── css/
│   │   ├── app.css                               # Main styles
│   │   └── gemeente-modern.css                   # Modern features
│   └── js/
│       ├── app.js                                # Main JavaScript
│       └── moderne-animations.js                 # Animations
│
├── routes/
│   ├── web.php                                   # Web routes
│   ├── api.php                                   # API routes
│   ├── admin.php                                 # Admin routes
│   └── auth.php                                  # Auth routes
│
├── public/
│   ├── css/                                      # Compiled CSS
│   ├── js/                                       # Compiled JS
│   ├── images/                                   # Static images
│   ├── storage/                                  # Uploaded files
│   ├── map-demo.html                             # Interactive map demo
│   └── demo-features.html                        # Features showcase
│
├── mcp-server/                                   # Node.js MCP Server
│   ├── src/
│   │   ├── index.ts                              # Main MCP server (975 lines)
│   │   └── browser-automation.ts                 # Playwright wrapper (350+ lines)
│   ├── dist/                                     # Compiled JavaScript
│   ├── package.json                              # Node dependencies
│   ├── tsconfig.json                             # TypeScript config
│   └── .env                                      # Environment variables
│
├── storage/
│   ├── app/
│   │   ├── public/                               # Public storage
│   │   └── private/                              # Private files
│   ├── logs/                                     # Application logs
│   └── framework/                                # Framework cache
│
├── tests/
│   ├── Feature/                                  # Feature tests
│   └── Unit/                                     # Unit tests
│
├── docs/                                         # Documentation (NEW)
│   ├── 01_PROJECT_OVERVIEW.md                    # This file
│   ├── 02_BACKEND_LARAVEL.md                     # Laravel backend details
│   ├── 03_MCP_SERVER.md                          # MCP server details
│   ├── 04_FRONTEND.md                            # Frontend details
│   ├── 05_API_DOCUMENTATION.md                   # API reference
│   └── 06_DEPLOYMENT.md                          # Deployment guide
│
├── .vscode/
│   └── settings.json                             # VSCode MCP config
│
├── composer.json                                 # PHP dependencies
├── package.json                                  # Frontend dependencies
├── vite.config.js                                # Vite configuration
├── tailwind.config.js                            # Tailwind configuration
├── phpunit.xml                                   # Testing configuration
└── README.md                                     # Project README
```

---

## 🚀 Installatie & Setup

### Prerequisites
- PHP 8.3+
- Composer 2.x
- Node.js 18+
- MySQL 8+ of SQLite
- Git

### 1. Clone Repository
```bash
git clone https://github.com/abii2024/gemeente.git
cd gemeente
```

### 2. Install PHP Dependencies
```bash
composer install
```

### 3. Install Node Dependencies
```bash
npm install
```

### 4. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

**Configure `.env`:**
```env
APP_NAME="Gemeente Klachtensysteem"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://gemeente.test

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gemeente
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Database Setup
```bash
# Run migrations
php artisan migrate

# Seed sample data (optional)
php artisan db:seed
```

### 6. Storage Setup
```bash
php artisan storage:link
chmod -R 775 storage bootstrap/cache
```

### 7. Build Frontend Assets
```bash
npm run build
# OR for development with hot reload:
npm run dev
```

### 8. Start Laravel Server
```bash
php artisan serve
# OR with Laravel Herd:
# Just visit http://gemeente.test
```

### 9. MCP Server Setup (Optional)
```bash
cd mcp-server
npm install
npm run build

# Test MCP server
npm run dev
```

### 10. Create Admin User
```bash
php artisan tinker
```
```php
$user = App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@gemeente.nl',
    'password' => bcrypt('password'),
]);
$user->assignRole('admin');
```

---

## 🔗 Important URLs

| Service | URL | Description |
|---------|-----|-------------|
| **Website** | http://gemeente.test | Publieke website |
| **Admin** | http://gemeente.test/admin | Admin dashboard |
| **API** | http://gemeente.test/api | RESTful API |
| **Map Demo** | http://gemeente.test/map-demo.html | Interactive map |
| **Features Demo** | http://gemeente.test/demo-features.html | UI showcase |

---

## 📚 Volgende Stappen

Lees de volgende documenten voor gedetailleerde uitleg:

1. **[02_BACKEND_LARAVEL.md](02_BACKEND_LARAVEL.md)** - Laravel backend architectuur en code uitleg
2. **[03_MCP_SERVER.md](03_MCP_SERVER.md)** - MCP server implementatie details
3. **[04_FRONTEND.md](04_FRONTEND.md)** - Frontend technologieën en UI/UX
4. **[05_API_DOCUMENTATION.md](05_API_DOCUMENTATION.md)** - Complete API reference
5. **[06_DEPLOYMENT.md](06_DEPLOYMENT.md)** - Production deployment guide

---

## 💡 Tips & Best Practices

### Development
- Use `php artisan serve` voor local development
- Run `npm run dev` voor hot module reloading
- Check `storage/logs/laravel.log` voor errors

### Testing
```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter ComplaintTest
```

### Code Quality
```bash
# PHP Static Analysis
./vendor/bin/phpstan analyse

# Code Style Fixing
./vendor/bin/pint
```

### Database
```bash
# Fresh migration with seeding
php artisan migrate:fresh --seed

# Create new migration
php artisan make:migration create_table_name
```

---

## 📞 Support & Contact

- **GitHub**: https://github.com/abii2024/gemeente
- **Issues**: https://github.com/abii2024/gemeente/issues
- **Documentation**: `/docs` folder

---

**Made with ❤️ by Gemeente Development Team**
