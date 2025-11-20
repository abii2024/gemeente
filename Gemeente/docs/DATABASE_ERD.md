# Database ERD - Gemeente Portal

## Entity Relationship Diagram

```
┌─────────────────────────────────────┐
│           USERS                     │
├─────────────────────────────────────┤
│ PK  id                 : bigint     │
│     name               : string     │
│     email              : string     │
│     email_verified_at  : timestamp  │
│     password           : string     │
│     remember_token     : string     │
│     created_at         : timestamp  │
│     updated_at         : timestamp  │
└─────────────────────────────────────┘
           │                │
           │                │
           │                └────────────────────────┐
           │                                         │
           │ (1:N)                          (1:N)   │
           │                                         │
           ▼                                         ▼
┌─────────────────────────────────────┐   ┌─────────────────────────────────────┐
│         COMPLAINTS                  │   │           NOTES                     │
├─────────────────────────────────────┤   ├─────────────────────────────────────┤
│ PK  id                 : bigint     │   │ PK  id                 : bigint     │
│     title              : string     │   │ FK  complaint_id       : bigint     │
│     description        : text       │   │ FK  user_id            : bigint     │
│     category           : string     │   │     body               : text       │
│     priority           : enum       │   │     created_at         : timestamp  │
│     status             : enum       │   │     updated_at         : timestamp  │
│     lat                : decimal    │   └─────────────────────────────────────┘
│     lng                : decimal    │
│     location           : string     │
│     reporter_name      : string     │
│     reporter_email     : string     │
│     reporter_phone     : string     │
│     internal_notes     : text       │
│     resolved_at        : timestamp  │
│ FK  assigned_to        : bigint     │
│     created_at         : timestamp  │
│     updated_at         : timestamp  │
└─────────────────────────────────────┘
           │                │
           │                │
           │                └────────────────────────┐
           │                                         │
           │ (1:N)                          (1:N)   │
           │                                         │
           ▼                                         ▼
┌─────────────────────────────────────┐   ┌─────────────────────────────────────┐
│        ATTACHMENTS                  │   │      STATUS_HISTORIES               │
├─────────────────────────────────────┤   ├─────────────────────────────────────┤
│ PK  id                 : bigint     │   │ PK  id                 : bigint     │
│ FK  complaint_id       : bigint     │   │ FK  complaint_id       : bigint     │
│     path               : string     │   │ FK  user_id            : bigint     │
│     mime               : string     │   │     from               : string     │
│     size               : integer    │   │     to                 : string     │
│     created_at         : timestamp  │   │     created_at         : timestamp  │
│     updated_at         : timestamp  │   │     updated_at         : timestamp  │
└─────────────────────────────────────┘   └─────────────────────────────────────┘


┌─────────────────────────────────────┐
│           ROLES                     │
│        (Spatie Package)             │
├─────────────────────────────────────┤
│ PK  id                 : bigint     │
│     name               : string     │
│     guard_name         : string     │
│     created_at         : timestamp  │
│     updated_at         : timestamp  │
└─────────────────────────────────────┘
           │
           │ (N:M via model_has_roles)
           │
           ▼
┌─────────────────────────────────────┐
│      MODEL_HAS_ROLES                │
│        (Pivot Table)                │
├─────────────────────────────────────┤
│ FK  role_id            : bigint     │
│     model_type         : string     │
│ FK  model_id           : bigint     │
└─────────────────────────────────────┘
           │
           │
           ▼
┌─────────────────────────────────────┐
│           USERS                     │
│        (Referenced)                 │
└─────────────────────────────────────┘


┌─────────────────────────────────────┐
│        PERMISSIONS                  │
│        (Spatie Package)             │
├─────────────────────────────────────┤
│ PK  id                 : bigint     │
│     name               : string     │
│     guard_name         : string     │
│     created_at         : timestamp  │
│     updated_at         : timestamp  │
└─────────────────────────────────────┘
```

---

## 📊 Tabel Details

### **USERS** (Gebruikers)
Bevat alle gebruikers van het systeem (burgers en admin medewerkers).

**Velden:**
- `id` - Primary key
- `name` - Volledige naam gebruiker
- `email` - Uniek email adres (login)
- `password` - Gehashed wachtwoord (bcrypt)
- `email_verified_at` - Email verificatie timestamp
- `remember_token` - Onthoud-me token
- `created_at` / `updated_at` - Laravel timestamps

**Relaties:**
- **1:N** met `complaints` (via `assigned_to`) - Toegewezen klachten
- **1:N** met `notes` - Notities van medewerker
- **1:N** met `status_histories` - Status wijzigingen uitgevoerd
- **N:M** met `roles` (via `model_has_roles`) - Gebruikersrollen

---

### **COMPLAINTS** (Klachten)
Kernentiteit - bevat alle ingediende klachten van burgers.

**Velden:**
- `id` - Primary key (tracking nummer)
- `title` - Korte titel van klacht
- `description` - Uitgebreide beschrijving
- `category` - Categorie (wegen_onderhoud, afval, etc.)
- `priority` - Prioriteit: low, medium, high, urgent
- `status` - Status: open, in_behandeling, opgelost, closed
- `lat` / `lng` - GPS coördinaten locatie
- `location` - Tekstuele locatie beschrijving
- `reporter_name` - Naam melder
- `reporter_email` - Email melder (voor tracking)
- `reporter_phone` - Telefoonnummer melder (optioneel)
- `internal_notes` - Interne admin notities
- `resolved_at` - Timestamp wanneer opgelost
- `assigned_to` - FK naar `users` (toegewezen medewerker)
- `created_at` / `updated_at` - Laravel timestamps

**Relaties:**
- **N:1** met `users` (via `assigned_to`) - Toegewezen aan medewerker
- **1:N** met `attachments` - Foto's/bijlagen
- **1:N** met `notes` - Administratieve notities
- **1:N** met `status_histories` - Status wijzigingsgeschiedenis

**Constraints:**
- `status` ENUM: open, in_behandeling, opgelost, closed
- `priority` ENUM: low, medium, high, urgent
- `assigned_to` FOREIGN KEY → users.id (ON DELETE SET NULL)

---

### **ATTACHMENTS** (Bijlagen)
Bevat foto's en documenten die bij klachten zijn geüpload.

**Velden:**
- `id` - Primary key
- `complaint_id` - FK naar complaints
- `path` - Storage pad naar bestand
- `mime` - MIME type (image/jpeg, etc.)
- `size` - Bestandsgrootte in bytes
- `created_at` / `updated_at` - Laravel timestamps

**Relaties:**
- **N:1** met `complaints` - Hoort bij klacht

**Constraints:**
- `complaint_id` FOREIGN KEY → complaints.id (ON DELETE CASCADE)

**Storage:**
- Bestanden opgeslagen in `storage/app/public/complaints/`
- Max 5 bestanden per klacht
- Max 10MB per bestand

---

### **NOTES** (Notities)
Interne administratieve notities van medewerkers bij klachten.

**Velden:**
- `id` - Primary key
- `complaint_id` - FK naar complaints
- `user_id` - FK naar users (auteur)
- `body` - Inhoud van notitie
- `created_at` / `updated_at` - Laravel timestamps

**Relaties:**
- **N:1** met `complaints` - Bij welke klacht
- **N:1** met `users` - Wie schreef de notitie

**Constraints:**
- `complaint_id` FOREIGN KEY → complaints.id (ON DELETE CASCADE)
- `user_id` FOREIGN KEY → users.id (ON DELETE CASCADE)

---

### **STATUS_HISTORIES** (Status Geschiedenis)
Audit trail van alle status wijzigingen van klachten.

**Velden:**
- `id` - Primary key
- `complaint_id` - FK naar complaints
- `user_id` - FK naar users (wie wijzigde)
- `from` - Oude status
- `to` - Nieuwe status
- `created_at` / `updated_at` - Laravel timestamps

**Relaties:**
- **N:1** met `complaints` - Welke klacht gewijzigd
- **N:1** met `users` - Wie wijzigde (kan NULL zijn)

**Constraints:**
- `complaint_id` FOREIGN KEY → complaints.id (ON DELETE CASCADE)
- `user_id` FOREIGN KEY → users.id (ON DELETE SET NULL)

**Use Case:**
- Timeline tonen aan melder
- Audit logging voor compliance

---

### **ROLES** (Rollen - Spatie)
Gebruikersrollen voor autorisatie (via Spatie Laravel Permission).

**Velden:**
- `id` - Primary key
- `name` - Rolnaam (admin, user)
- `guard_name` - Guard naam (web)
- `created_at` / `updated_at` - Laravel timestamps

**Relaties:**
- **N:M** met `users` (via `model_has_roles`)

**Standaard Rollen:**
- `admin` - Volledige toegang tot dashboard
- `user` - Normale burger, eigen klachten

---

### **MODEL_HAS_ROLES** (Pivot Tabel)
Koppeltabel tussen users en roles.

**Velden:**
- `role_id` - FK naar roles
- `model_type` - Eloquent model class (App\Models\User)
- `model_id` - FK naar users

**Constraints:**
- Composite primary key: (role_id, model_type, model_id)
- `role_id` FOREIGN KEY → roles.id (ON DELETE CASCADE)
- `model_id` FOREIGN KEY → users.id (ON DELETE CASCADE)

---

## 🔗 Relatie Overzicht

### One-to-Many (1:N)
- **users** → **complaints** (assigned_to)
  - Één medewerker kan meerdere klachten toegewezen krijgen
  
- **complaints** → **attachments**
  - Eén klacht kan meerdere foto's hebben
  
- **complaints** → **notes**
  - Eén klacht kan meerdere notities hebben
  
- **complaints** → **status_histories**
  - Eén klacht kan meerdere status wijzigingen hebben
  
- **users** → **notes**
  - Eén gebruiker kan meerdere notities schrijven
  
- **users** → **status_histories**
  - Eén gebruiker kan meerdere status wijzigingen uitvoeren

### Many-to-Many (N:M)
- **users** ↔ **roles** (via model_has_roles)
  - Eén gebruiker kan meerdere rollen hebben
  - Eén rol kan aan meerdere gebruikers toegekend zijn

---

## 🔐 Data Integriteit

### Cascade Deletes (ON DELETE CASCADE)
- `attachments.complaint_id` → complaints
- `notes.complaint_id` → complaints
- `status_histories.complaint_id` → complaints

**Betekenis:** Als een klacht wordt verwijderd, worden automatisch alle bijlagen, notities en historie verwijderd.

### Set Null (ON DELETE SET NULL)
- `complaints.assigned_to` → users
- `status_histories.user_id` → users

**Betekenis:** Als een gebruiker wordt verwijderd, blijft de klacht bestaan maar wordt `assigned_to` op NULL gezet.

---

## 📈 Indexen

### Primaire Sleutels
Alle tabellen hebben `id` als auto-increment primary key.

### Foreign Keys (Automatische Indexen)
- `attachments.complaint_id`
- `notes.complaint_id`
- `notes.user_id`
- `status_histories.complaint_id`
- `status_histories.user_id`
- `complaints.assigned_to`

### Unieke Constraints
- `users.email` - UNIQUE

### Aanbevolen Extra Indexen
```sql
-- Voor snelle status filtering
CREATE INDEX idx_complaints_status ON complaints(status);

-- Voor locatie zoeken
CREATE INDEX idx_complaints_location ON complaints(lat, lng);

-- Voor datum filtering
CREATE INDEX idx_complaints_created_at ON complaints(created_at);

-- Voor melder tracking
CREATE INDEX idx_complaints_reporter_email ON complaints(reporter_email);
```

---

## 📊 Database Statistieken

### Huidige Data (Productie)
- **Users:** ~100 gebruikers (1 admin, 99 burgers)
- **Complaints:** ~50 klachten
- **Attachments:** ~120 foto's (gem. 2-3 per klacht)
- **Notes:** ~30 interne notities
- **Status Histories:** ~80 status wijzigingen

### Verwachte Groei
- **Klachten:** ~500 per jaar
- **Bijlagen:** ~1000 per jaar
- **Storage:** ~2GB per jaar (met 10MB max per foto)

---

## 🛡️ Security Overwegingen

1. **Password Hashing:** Bcrypt (Laravel default)
2. **Email Uniqueness:** Voorkomt duplicate accounts
3. **Cascade Deletes:** Orphaned records prevention
4. **Soft Deletes:** NIET geïmplementeerd (hard deletes)
5. **Audit Trail:** Via `status_histories` tabel
6. **Role-Based Access:** Via Spatie Permission package

---

## 🔄 Migratie Volgorde

1. `0001_01_01_000000_create_users_table.php`
2. `0001_01_01_000001_create_cache_table.php`
3. `0001_01_01_000002_create_jobs_table.php`
4. `2025_09_22_091228_create_complaints_table.php`
5. `2025_09_22_091335_create_attachments_table.php`
6. `2025_09_22_091410_create_notes_table.php`
7. `2025_09_22_092030_create_status_histories_table.php`
8. `2025_09_22_122719_create_permission_tables.php` (Spatie)
9. `2025_09_22_134511_add_fields_to_complaints_table.php`

---

## 📝 Voorbeeld Queries

### Alle open klachten met locatie
```sql
SELECT id, title, status, location, created_at 
FROM complaints 
WHERE status = 'open' 
  AND lat IS NOT NULL 
  AND lng IS NOT NULL
ORDER BY created_at DESC;
```

### Klachten per medewerker
```sql
SELECT u.name, COUNT(c.id) as total_assigned
FROM users u
LEFT JOIN complaints c ON u.id = c.assigned_to
GROUP BY u.id, u.name
ORDER BY total_assigned DESC;
```

### Status geschiedenis van een klacht
```sql
SELECT sh.from, sh.to, sh.created_at, u.name as changed_by
FROM status_histories sh
LEFT JOIN users u ON sh.user_id = u.id
WHERE sh.complaint_id = 24
ORDER BY sh.created_at ASC;
```

---

**Gegenereerd op:** 20 November 2025  
**Database:** SQLite (dev), PostgreSQL (prod)  
**Laravel Versie:** 12.x  
**Spatie Permission:** 6.x
