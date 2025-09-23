# Gemeente Klachten Systeem - Beveiliging en Privacy Documentatie

## Overzicht
Dit gemeente klachten management systeem is volledig geconfigureerd volgens moderne beveiligings- en privacy-standaarden, inclusief GDPR/AVG compliance.

## Beveiligingsfeatures

### 1. Authenticatie & Autorisatie
- **Laravel Breeze**: Veilige authenticatie met bcrypt password hashing
- **Spatie Permission**: Role-based access control (RBAC)
- **Policies**: Granulaire toegangscontrole per resource
  - `AdminPolicy`: Alleen admins hebben toegang tot dashboard
  - `ComplaintPolicy`: Gebruikers kunnen alleen eigen klachten bekijken

### 2. Input Validatie & CSRF Bescherming
- **Form Request Validatie**: `StoreComplaintRequest` met uitgebreide validatie
- **CSRF Tokens**: Automatische CSRF bescherming op alle formulieren
- **XSS Preventie**: Blade templating met automatische escaping
- **SQL Injection Preventie**: Eloquent ORM gebruikt prepared statements

### 3. Rate Limiting
- **API Rate Limiting**: Geïmplementeerd op alle API endpoints
- **Login Rate Limiting**: Bescherming tegen brute force aanvallen
- **Throttling**: Configureerbaar per route groep

### 4. Security Headers
- **No-Index Middleware**: Admin pagina's zijn uitgesloten van zoekmachines
- **X-Frame-Options**: Clickjacking bescherming
- **X-Robots-Tag**: SEO uitsluitingen voor admin gebied
- **Cache-Control**: Gevoelige pagina's worden niet gecached

## Privacy & GDPR/AVG Compliance

### 1. Data Minimalisatie
- **Minimale Vereiste Velden**: Alleen noodzakelijke data wordt opgeslagen
- **Optionele Contactgegevens**: Gebruikers kiezen zelf wat ze delen
- **Geen Tracking**: Geen analytics of tracking cookies

### 2. Privacy-Safe Logging
- **PII Filtering**: Persoonlijke data wordt automatisch gefilterd uit logs
- **Hash-based Logging**: IP-adressen en user agents worden gehashed
- **Gestructureerde Logs**: Aparte kanalen voor privacy, security en audit
- **Logging Kanalen**:
  - `privacy_safe`: Algemene acties zonder PII
  - `security`: Beveiligingsgebeurtenissen met gehashte data
  - `audit`: Compliance tracking

### 3. Data Retentie & Verwijdering
- **Automatische Purge**: `complaints:purge` command voor oude data
- **Configureerbare Retentie**: Standaard 365 dagen (instelbaar)
- **Dry-Run Modus**: Test purge operaties zonder data te verwijderen
- **Attachment Cleanup**: Bestanden worden mee verwijderd
- **Audit Trail**: Alle verwijderingen worden gelogd

### 4. Beveiliging Event Logging
- **Login Monitoring**: Successful en failed login events
- **Admin Access Logging**: Alle admin pagina toegang wordt gelogd
- **Complaint Actions**: Status wijzigingen en verwijderingen
- **Data Export/Deletion**: GDPR verzoeken worden getracked

## Technische Implementatie

### Security Middleware Stack
```php
// Admin routes
Route::middleware(['auth', 'admin', 'noindex', 'log.admin'])

// API routes  
Route::middleware(['auth:sanctum', 'throttle:api'])

// Web routes
Route::middleware(['auth', 'verified', 'throttle:web'])
```

### Privacy Logger Usage
```php
// Complaint actie zonder PII
PrivacyLogger::logComplaintAction('created', $complaint->id, [
    'category' => $complaint->category,
    'has_location' => !empty($complaint->latitude)
]);

// Security event met gehashte data
PrivacyLogger::logSecurityEvent('failed_login_attempt', [
    'email_domain' => Str::after($email, '@')
]);
```

### Data Purge Command
```bash
# Test run (geen data verwijdering)
php artisan complaints:purge --dry-run

# Live run (verwijdert daadwerkelijk oude data)
php artisan complaints:purge

# Met custom retentie periode
php artisan complaints:purge --days=180
```

## Compliance Checklist

### ✅ GDPR/AVG Vereisten
- [x] **Data Minimalisatie**: Alleen noodzakelijke velden worden opgeslagen
- [x] **Transparantie**: Duidelijke privacy informatie in forms
- [x] **Data Retentie**: Automatische verwijdering van oude data
- [x] **Privacy by Design**: PII wordt niet in logs opgeslagen
- [x] **Audit Trail**: Alle data-wijzigingen worden gelogd
- [x] **Security**: Encryptie, hashing en toegangscontrole

### ✅ Security Best Practices
- [x] **Input Validatie**: Uitgebreide form validation
- [x] **Output Encoding**: XSS preventie via Blade templating
- [x] **CSRF Bescherming**: Tokens op alle formulieren
- [x] **SQL Injection Preventie**: Eloquent ORM prepared statements
- [x] **Rate Limiting**: API en login bescherming
- [x] **Secure Headers**: Anti-clickjacking en cache headers
- [x] **Access Control**: Role-based permissions
- [x] **Security Monitoring**: Event logging en monitoring

## Configuratie

### Logging Configuratie
Zie `config/logging.php` voor de volledige logging setup met privacy, security en audit kanalen.

### Permissions Configuratie
```php
// Rollen en permissies
'admin' => ['access admin', 'manage complaints', 'delete complaints']
'user' => ['create complaints', 'view own complaints']
```

### Data Retentie Configuratie
```php
// In config/app.php of .env
'complaint_retention_days' => env('COMPLAINT_RETENTION_DAYS', 365)
```

## Monitoring & Maintenance

### Dagelijkse Taken
- Check security logs voor verdachte activiteiten
- Monitor failed login attempts
- Controleer disk space voor log bestanden

### Wekelijkse Taken
- Review admin access logs
- Test backup en restore procedures
- Update security patches

### Maandelijkse Taken
- Draai data purge command (als niet geautomatiseerd)
- Security audit van gebruikers en rollen
- Review en update security policies

## Contact
Voor security gerelateerde vragen of incidenten, neem contact op met de systeembeheerder.

---
*Laatste update: September 2025*
*Compliance: GDPR/AVG, NIS2 Directive voorbereid*