# 🗂️ GEMEENTE KLACHTEN SYSTEEM - ENTITY RELATIONSHIP DIAGRAM (ERD)

**Datum:** 29 september 2025  
**Project:** Gemeente Klachtenportaal  
**Team:** Groep van 2-4 studenten  

## 📊 DATABASE OVERZICHT

### Hoofdentiteiten
Het systeem bestaat uit de volgende hoofdentiteiten en hun onderlinge relaties:

```mermaid
erDiagram
    USERS {
        bigint id PK
        varchar name
        varchar email UK
        timestamp email_verified_at
        varchar password
        varchar remember_token
        timestamps created_at_updated_at
    }
    
    COMPLAINTS {
        bigint id PK
        varchar title
        text description
        enum status
        varchar category
        enum priority
        decimal lat
        decimal lng
        varchar location
        varchar reporter_name
        varchar reporter_email
        varchar reporter_phone
        text internal_notes
        timestamp resolved_at
        bigint assigned_to FK
        timestamps created_at_updated_at
    }
    
    ATTACHMENTS {
        bigint id PK
        bigint complaint_id FK
        varchar path
        varchar mime
        integer size
        timestamps created_at_updated_at
    }
    
    NOTES {
        bigint id PK
        bigint complaint_id FK
        bigint user_id FK
        text body
        timestamps created_at_updated_at
    }
    
    STATUS_HISTORIES {
        bigint id PK
        bigint complaint_id FK
        bigint user_id FK
        varchar from_status
        varchar to_status
        timestamps created_at_updated_at
    }
    
    ROLES {
        bigint id PK
        varchar name UK
        varchar guard_name
        timestamps created_at_updated_at
    }
    
    PERMISSIONS {
        bigint id PK
        varchar name UK
        varchar guard_name
        timestamps created_at_updated_at
    }
    
    MODEL_HAS_ROLES {
        bigint role_id FK
        varchar model_type
        bigint model_id
        composite_pk role_id_model_type_model_id
    }
    
    ROLE_HAS_PERMISSIONS {
        bigint permission_id FK
        bigint role_id FK
        composite_pk permission_id_role_id
    }
    
    SETTINGS {
        bigint id PK
        varchar key UK
        json value
        varchar type
        text description
        timestamps created_at_updated_at
    }

    %% Relationships
    USERS ||--o{ COMPLAINTS : "assigned_to"
    USERS ||--o{ NOTES : "creates"
    USERS ||--o{ STATUS_HISTORIES : "performs"
    
    COMPLAINTS ||--o{ ATTACHMENTS : "has"
    COMPLAINTS ||--o{ NOTES : "receives"
    COMPLAINTS ||--o{ STATUS_HISTORIES : "tracks"
    
    ROLES ||--o{ MODEL_HAS_ROLES : "assigned_to"
    PERMISSIONS ||--o{ ROLE_HAS_PERMISSIONS : "belongs_to"
    ROLES ||--o{ ROLE_HAS_PERMISSIONS : "has"
    
    USERS ||--o{ MODEL_HAS_ROLES : "has_role"
```

## 🔗 RELATIE DETAILS

### 1. Users ↔ Complaints
- **Relatie:** One-to-Many (1:N)
- **Type:** `assigned_to` (optionele toewijzing)
- **Constraint:** `FOREIGN KEY assigned_to REFERENCES users(id)`
- **Cascade:** `ON DELETE SET NULL`

### 2. Complaints ↔ Attachments  
- **Relatie:** One-to-Many (1:N)
- **Type:** Compositie (een klacht kan meerdere bestanden hebben)
- **Constraint:** `FOREIGN KEY complaint_id REFERENCES complaints(id)`
- **Cascade:** `ON DELETE CASCADE`

### 3. Complaints ↔ Notes
- **Relatie:** One-to-Many (1:N)  
- **Type:** Compositie (een klacht kan meerdere notities hebben)
- **Constraint:** `FOREIGN KEY complaint_id REFERENCES complaints(id)`
- **Cascade:** `ON DELETE CASCADE`

### 4. Users ↔ Notes
- **Relatie:** One-to-Many (1:N)
- **Type:** Aggregatie (een gebruiker kan meerdere notities schrijven)
- **Constraint:** `FOREIGN KEY user_id REFERENCES users(id)`
- **Cascade:** `ON DELETE CASCADE`

### 5. Complaints ↔ Status Histories
- **Relatie:** One-to-Many (1:N)
- **Type:** Compositie (audit trail van statuswijzigingen)
- **Constraint:** `FOREIGN KEY complaint_id REFERENCES complaints(id)`
- **Cascade:** `ON DELETE CASCADE`

### 6. Users ↔ Status Histories  
- **Relatie:** One-to-Many (1:N)
- **Type:** Aggregatie (wie de statuswijziging heeft uitgevoerd)
- **Constraint:** `FOREIGN KEY user_id REFERENCES users(id)`
- **Cascade:** `ON DELETE SET NULL`

## 🔐 SPATIE PERMISSION SYSTEEM

### Role-Based Access Control (RBAC)
Het systeem gebruikt het Spatie Permission package voor role-based toegangscontrole:

#### Users ↔ Roles (Many-to-Many)
- **Tabel:** `model_has_roles`
- **Polymorphic:** Ja (`model_type` = 'App\Models\User')
- **Constraint:** Composite primary key

#### Roles ↔ Permissions (Many-to-Many)  
- **Tabel:** `role_has_permissions`
- **Direct:** role_id ↔ permission_id
- **Constraint:** Composite primary key

## 📋 DOMEIN WAARDEN

### Complaint Status (ENUM)
```sql
status IN ('open', 'in_behandeling', 'opgelost')
```

### Priority Levels (ENUM)  
```sql
priority IN ('low', 'medium', 'high', 'urgent')
```

### File Types (Attachments)
- **Images:** JPEG, PNG, GIF
- **Documents:** PDF
- **Max Size:** 10MB per bestand
- **Max Count:** 5 bestanden per klacht

## 🎯 INDEXING STRATEGIE

### Primary Keys
- Alle tabellen hebben `bigint AUTO_INCREMENT` primary keys

### Foreign Keys  
- Alle foreign key relaties hebben indexes voor performance
- Cascade regels gedefinieerd voor data integriteit

### Unique Constraints
- `users.email` - Uniek email adres
- `settings.key` - Unieke configuratie sleutels
- `roles.name + guard_name` - Unieke rol namen per guard
- `permissions.name + guard_name` - Unieke permissie namen per guard

## 💾 DATA TYPES

### Spatial Data
- **Latitude:** `DECIMAL(10,8)` - Precisie tot ~1 meter
- **Longitude:** `DECIMAL(11,8)` - Precisie tot ~1 meter

### Text Fields
- **Korte teksten:** `VARCHAR(255)` (titels, namen)
- **Lange teksten:** `TEXT` (beschrijvingen, notities)  
- **JSON Data:** `JSON` (settings values)

### Timestamps
- **Laravel Timestamps:** `created_at`, `updated_at` 
- **Custom Timestamps:** `resolved_at`, `email_verified_at`

## 🔒 BEVEILIGING & PRIVACY

### Data Minimalisatie (GDPR)
- Alleen noodzakelijke persoonsgegevens opgeslagen
- `internal_notes` voor gevoelige informatie
- Automatische data retentie na configureerbare periode

### Cascade Gedrag
- **CASCADE:** Attachments, Notes, Status Histories (onderdeel van complaint)
- **SET NULL:** User referenties (behoud data, anonimiseer relatie)

---

**📝 Opmerking:** Dit ERD toont de huidige productie database structuur van het gemeente klachten systeem zoals geïmplementeerd in Laravel 12 met SQLite als development database.
