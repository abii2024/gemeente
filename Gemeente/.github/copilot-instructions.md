# Gemeente Portal - GitHub Copilot Instructions

Je bent een expert assistent voor het **Gemeente Portal** project - een Laravel 12 klachten management systeem.

## 🎯 Project Context

**Naam:** Gemeente Portal
**Type:** Laravel 12.x Web Applicatie  
**Doel:** Klachten beheer systeem voor gemeentes
**Talen:** PHP 8.4, JavaScript, Blade Templates
**Database:** SQLite (dev), PostgreSQL (prod)
**Locatie:** `/Users/abdisamadabdulle/Herd/Gemeente`

## 📁 Project Structuur

### Backend (Laravel)
- **Controllers:** `app/Http/Controllers/`
  - `ComplaintController.php` - Klachten (publiek)
  - `Admin/ComplaintAdminController.php` - Admin klachtenbeheer
  - `Admin/UserController.php` - Gebruikersbeheer
  - `ProfileController.php` - Profiel management
  
- **Models:** `app/Models/`
  - `User.php` - Gebruikers met Spatie roles
  - `Complaint.php` - Klachten
  - `ComplaintImage.php` - Foto uploads
  
- **Routes:**
  - `routes/web.php` - Publieke routes
  - `routes/admin.php` - Admin routes (middleware protected)
  - `routes/auth.php` - Authentication

### Frontend
- **Views:** `resources/views/`
  - `welcome.blade.php` - Homepage
  - `pages/complaint-create.blade.php` - Klacht indienen
  - `admin/` - Admin dashboard en beheer
  - `profile/` - Profiel pagina's
  
- **CSS:** `public/css/`
  - `gemeente-modern.css` - Hoofdstyles
  - Kleurenschema: Sky Blue (#0ea5e9) → Cyan (#06b6d4)
  
- **JavaScript:** `resources/js/`
  - `chatbot.js` - Chatbot button (decoratief)
  - Vite voor bundling

### Database
**Belangrijkste tabellen:**
- `users` - Gebruikers met roles
- `complaints` - Klachten met tracking
- `complaint_images` - Foto bijlagen
- `roles` & `model_has_roles` - Spatie permissions

## 🔑 Kernfunctionaliteiten

### Voor Burgers
1. **Klacht Indienen** - Formulier met foto upload en locatie op kaart
2. **Tracking** - Publiek zoeken met tracking nummer
3. **Dashboard** - Eigen klachten bekijken
4. **Profiel** - Account beheren

### Voor Admins
1. **Dashboard** - Statistieken en overzichten
2. **Klachten Beheer** - CRUD operaties, status updates
3. **Gebruikersbeheer** - Users aanmaken, rollen toewijzen
4. **Kaart Weergave** - Leaflet.js integratie

## 🛡️ Security Features
- CSRF protection (alle forms)
- XSS filtering
- SQL injection prevention (Eloquent ORM)
- Password hashing (bcrypt)
- File upload validation
- Role-based access control (Spatie)

## 📊 Belangrijke Files

### Documentatie
- `README.md` - Project overzicht & setup
- `WAAR_IS_ALLES.md` - Complete navigatie gids
- `docs/ONTWERPEN.md` - ERD, Klassendiagram, User Stories
- `ADMIN_CREDENTIALS.md` - Test accounts
- `API_ENDPOINTS_COMPLETE.md` - API documentatie

### Config
- `.env` - Environment variabelen
- `config/database.php` - Database configuratie
- `config/permission.php` - Spatie roles config

## 💡 Development Commands

```bash
# Server starten
php artisan serve --host=0.0.0.0 --port=8000

# Of via Herd
# Website: https://gemeente.test

# Database
php artisan migrate
php artisan db:seed

# Cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Assets
npm run build    # Production
npm run dev      # Development
```

## 🎨 Design System

**Kleurenschema:**
- Primary: Sky Blue (#0ea5e9)
- Secondary: Cyan (#06b6d4)
- Gradient: `linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%)`

**Components:**
- Modern card-based layout
- Smooth animations
- Responsive breakpoints: 640px, 1024px
- Tailwind-inspired utility classes

## 🗺️ Routing Structuur

### Publiek
- `/` - Homepage
- `/login` - Login
- `/register` - Registratie
- `/complaints/create` - Klacht indienen
- `/complaints/track` - Klacht zoeken

### Auth Required
- `/dashboard` - User dashboard
- `/profile` - Profiel
- `/complaints` - Eigen klachten

### Admin Only
- `/admin/dashboard` - Admin dashboard
- `/admin/complaints` - Klachten beheer
- `/admin/users` - Gebruikersbeheer

## 📝 Code Conventies

### Laravel Best Practices
- MVC pattern strikt volgen
- Eloquent ORM voor database
- Form Request Validation
- Resource Controllers
- Route Model Binding

### Naming
- Controllers: `{Model}Controller.php`
- Models: Singular, PascalCase
- Views: kebab-case
- Routes: kebab-case
- Database: snake_case

### Security
- Altijd `@csrf` in forms
- Input escaping met `{{ }}` (niet `{!! !!}`)
- File upload validation
- Authorization checks voor admin routes

## 🎯 Veelgestelde Vragen

**Q: Waar staat de klacht indienen code?**
A: `app/Http/Controllers/Web/ComplaintController.php` (store method) en `resources/views/pages/complaint-create.blade.php`

**Q: Hoe werkt de foto upload?**
A: Via `ComplaintController@store`, files worden opgeslagen in `storage/app/public/complaints/` en gekoppeld via `ComplaintImage` model.

**Q: Waar is de admin dashboard?**
A: Controller: `app/Http/Controllers/Admin/DashboardController.php`, View: `resources/views/admin/dashboard.blade.php`

**Q: Hoe wijzig ik de kleuren?**
A: Hoofdfile: `public/css/gemeente-modern.css`, zoek naar `#0ea5e9` en `#06b6d4`

**Q: Test accounts?**
A: Zie `ADMIN_CREDENTIALS.md` - Admin: admin@example.com / password

## 🚀 Tips voor Development

1. **Database Reset:**
   ```bash
   php artisan migrate:fresh --seed
   ```

2. **Nieuwe Feature Toevoegen:**
   - Maak migration
   - Update model
   - Maak controller method
   - Voeg route toe
   - Maak view

3. **Debugging:**
   - `dd()` voor var_dump
   - `storage/logs/laravel.log` voor errors
   - Chrome DevTools voor frontend

4. **Testing:**
   - Tests in `tests/Feature/`
   - Run: `php artisan test`

## 📚 Externe Dependencies

**PHP:**
- Laravel 12.x
- Spatie Laravel Permission
- Laravel Breeze (auth)

**JavaScript:**
- Vite
- Leaflet.js (kaarten)

**CSS:**
- Moderne CSS (geen framework)
- Custom design system

## 🔗 Belangrijke Links

- **GitHub:** github.com/abii2024/gemeente
- **Local:** https://gemeente.test
- **Documentatie:** `/docs/` directory

---

## 💬 Hoe Te Gebruiken

Wanneer een gebruiker een vraag stelt:

1. **Begrijp de context** - Is het over backend, frontend, database, of deployment?
2. **Geef specifieke locaties** - Verwijs naar exacte bestanden en line numbers waar mogelijk
3. **Code voorbeelden** - Toon relevante code snippets
4. **Best practices** - Adviseer volgens Laravel conventies
5. **Links naar docs** - Verwijs naar `WAAR_IS_ALLES.md` voor meer info

Wees altijd behulpzaam, duidelijk, en specifiek in je antwoorden over het Gemeente Portal project!
