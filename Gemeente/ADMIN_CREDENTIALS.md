# Admin Login Credentials

## Admin Gebruiker

Voor toegang tot het beheerdersdashboard:

**Email:** `admin@gemeente.nl`  
**Wachtwoord:** `admin123`

**URL:** `http://localhost:8000/admin/dashboard`

---

## Medewerker Gebruiker (Optioneel)

Voor medewerkers met beperkte rechten:

**Email:** `medewerker@gemeente.nl`  
**Wachtwoord:** `medewerker123`

---

## Rechten Overzicht

### Admin Rechten
- ✅ View admin dashboard
- ✅ Manage complaints (CRUD)
- ✅ View complaints
- ✅ Edit complaint status
- ✅ Delete complaints
- ✅ Add notes
- ✅ Delete notes
- ✅ View map
- ✅ **Alle permissies**

### Medewerker Rechten
- ✅ View admin dashboard
- ✅ View complaints
- ✅ Edit complaint status
- ✅ Add notes
- ✅ View map
- ❌ Delete complaints
- ❌ Delete notes

---

## Nieuwe Admin Aanmaken

Om een nieuwe admin gebruiker aan te maken:

```bash
php artisan tinker
```

Voer dan uit:

```php
$user = App\Models\User::create([
    'name' => 'Nieuwe Admin',
    'email' => 'nieuw@gemeente.nl',
    'password' => Hash::make('jouw-wachtwoord'),
    'email_verified_at' => now(),
]);

$user->assignRole('admin');
```

---

## Wachtwoord Resetten

Als je het wachtwoord wilt wijzigen:

```bash
php artisan tinker
```

Voer dan uit:

```php
$user = App\Models\User::where('email', 'admin@gemeente.nl')->first();
$user->password = Hash::make('nieuw-wachtwoord');
$user->save();
```

---

## Login Flow

1. **Start de applicatie:**
   ```bash
   php artisan serve
   ```

2. **Open browser:**
   ```
   http://localhost:8000/login
   ```

3. **Log in met:**
   - Email: `admin@gemeente.nl`
   - Wachtwoord: `admin123`

4. **Je wordt doorgestuurd naar:**
   ```
   http://localhost:8000/admin/dashboard
   ```

---

## Dashboard Features

Na inloggen als admin heb je toegang tot:

- 📊 **Statistieken Dashboard**
  - Totaal klachten
  - Open klachten
  - In behandeling
  - Opgelost

- 🗺️ **Interactieve Kaart**
  - Alle klachten met GPS-coördinaten
  - Gekleurde pins per status
  - Klikbare popups met klacht details

- 🔍 **Zoeken & Filteren**
  - Zoek op klacht ID
  - Filter op status
  - Filter op prioriteit
  - Reset filters

- 📋 **5 Meest Recente Klachten**
  - Volledige tabel met alle details
  - Direct naar klacht of kaart

- 🛠️ **Klachten Beheer**
  - CRUD operaties
  - Status updates
  - Notities toevoegen
  - Bijlagen bekijken

---

## Security Notities

⚠️ **BELANGRIJK VOOR PRODUCTIE:**

1. **Wijzig standaard wachtwoorden** voordat je live gaat
2. **Gebruik sterke wachtwoorden** (minimaal 12 karakters)
3. **Activeer 2FA** indien mogelijk
4. **Review gebruikers regelmatig**
5. **Log admin acties** (al geïmplementeerd)

---

## Troubleshooting

### "These credentials do not match our records"
- Controleer of email correct is: `admin@gemeente.nl`
- Controleer of wachtwoord correct is: `admin123`
- Run: `php artisan cache:clear`

### "Unauthorized" na inloggen
- Controleer of admin rol is toegewezen:
  ```bash
  php artisan tinker --execute="App\Models\User::where('email', 'admin@gemeente.nl')->first()->assignRole('admin');"
  ```

### Database errors
- Run migrations:
  ```bash
  php artisan migrate:fresh
  ```
- Seed database:
  ```bash
  php artisan db:seed --class=RoleSeeder
  php artisan db:seed --class=AdminUserSeeder
  ```

---

## Database Seeders Uitvoeren

Voor een complete setup:

```bash
# 1. Reset database (WAARSCHUWING: verwijdert alle data!)
php artisan migrate:fresh

# 2. Rollen en permissies aanmaken
php artisan db:seed --class=RoleSeeder

# 3. Admin gebruiker aanmaken
php artisan db:seed --class=AdminUserSeeder

# 4. Test klachten met coördinaten
php artisan db:seed --class=ComplaintCoordinatesSeeder

# OF alles in één keer:
php artisan migrate:fresh --seed
```

---

## Contact

Voor vragen of problemen, neem contact op met de systeembeheerder.

**Ontwikkeld met ❤️ voor Gemeente Klachtensysteem**
