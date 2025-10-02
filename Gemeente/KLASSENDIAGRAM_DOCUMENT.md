# Gemeente Klachtensysteem - Klassendiagram

## 📋 Overzicht

Dit document beschrijft de object-georiënteerde structuur van het Gemeente Klachtensysteem. Het klassendiagram toont de relaties tussen models, controllers, services, policies en andere klassen binnen de Laravel applicatie.

## 🎯 Doel

Het klassendiagram helpt bij:
- **Architectuur begrip**: Visualisatie van de applicatie structuur
- **Code navigatie**: Snelle oriëntatie in de codebase
- **Onderhoud**: Identificatie van afhankelijkheden en relaties
- **Uitbreidingen**: Planning van nieuwe functionaliteiten
- **Documentatie**: Technische communicatie met developers

## 🏗️ UML Klassendiagram

```mermaid
classDiagram
    %% === MODELS (Data Layer) ===
    class User {
        +Long id
        +String name
        +String email
        +DateTime email_verified_at
        +String password
        +String remember_token
        +DateTime created_at
        +DateTime updated_at
        --
        +notes() HasMany
        +hasRole(role) bool
        +hasAnyRole(roles) bool
        +can(permission) bool
    }

    class Complaint {
        +Long id
        +String title
        +String description
        +String category
        +String priority
        +String status
        +String location
        +Decimal lat
        +Decimal lng
        +String reporter_name
        +String reporter_email
        +String reporter_phone
        +String internal_notes
        +DateTime resolved_at
        +Long assigned_to
        +DateTime created_at
        +DateTime updated_at
        --
        +attachments() HasMany
        +notes() HasMany
        +statusHistories() HasMany
        +scopeOpen(query)
        +scopeInBehandeling(query)
        +scopeOpgelost(query)
        +scopeRecent(query, days)
        +isOpen() bool
        +isInBehandeling() bool
        +isOpgelost() bool
    }

    class Attachment {
        +Long id
        +Long complaint_id
        +String filename
        +String original_name
        +String mime_type
        +Long size
        +DateTime created_at
        +DateTime updated_at
        --
        +complaint() BelongsTo
    }

    class Note {
        +Long id
        +Long complaint_id
        +Long user_id
        +String body
        +DateTime created_at
        +DateTime updated_at
        --
        +complaint() BelongsTo
        +user() BelongsTo
    }

    class StatusHistory {
        +Long id
        +Long complaint_id
        +String old_status
        +String new_status
        +Long changed_by
        +String reason
        +DateTime created_at
        +DateTime updated_at
        --
        +complaint() BelongsTo
        +user() BelongsTo
    }

    class Setting {
        +Long id
        +String key
        +String value
        +String type
        +DateTime created_at
        +DateTime updated_at
    }

    %% === SPATIE PERMISSION MODELS ===
    class Role {
        +Long id
        +String name
        +String guard_name
        +DateTime created_at
        +DateTime updated_at
        --
        +permissions() BelongsToMany
        +users() BelongsToMany
    }

    class Permission {
        +Long id
        +String name
        +String guard_name
        +DateTime created_at
        +DateTime updated_at
        --
        +roles() BelongsToMany
    }

    %% === CONTROLLERS (Application Layer) ===
    class Controller {
        <<abstract>>
    }

    class DashboardController {
        +index() View
    }

    class ComplaintController {
        +create() View
        +store(StoreComplaintRequest) RedirectResponse
        +thanks() View
    }

    class ComplaintAdminController {
        +index() View
        +show(Complaint) View
        +edit(Complaint) View
        +update(Request, Complaint) RedirectResponse
        +destroy(Complaint) RedirectResponse
        +map() View
        +updateStatus(Request, Complaint) RedirectResponse
    }

    class NoteController {
        +store(Request, Complaint) RedirectResponse
        +destroy(Note) RedirectResponse
    }

    class DatabaseController {
        +index() View
        +table(string) View
    }

    class UploadController {
        +store(Request) JsonResponse
        +delete(Request) JsonResponse
    }

    class PageController {
        +home() View
        +about() View
        +contact() View
    }

    %% === AUTH CONTROLLERS ===
    class RegisteredUserController {
        +create() View
        +store(Request) RedirectResponse
    }

    class AuthenticatedSessionController {
        +create() View
        +store(LoginRequest) RedirectResponse
        +destroy(Request) RedirectResponse
    }

    %% === API CONTROLLERS ===
    class ComplaintApiController {
        +index() JsonResponse
        +show(Complaint) JsonResponse
    }

    class SearchApiController {
        +search(Request) JsonResponse
    }

    class ChatController {
        +chat(Request) JsonResponse
        +welcome() JsonResponse
        +faq() JsonResponse
    }

    %% === SERVICES (Business Logic Layer) ===
    class ChatbotService {
        +handleMessage(string) array
        +getWelcomeMessage() array
        +getFaqCategories() array
        +searchComplaints(string) array
        +generateResponse(string, array) string
    }

    class PrivacyLogger {
        +logComplaintAction(string, id, array) void
        +logAdminAction(string, array) void
        +logAuthAction(string, array) void
    }

    %% === POLICIES (Authorization Layer) ===
    class AdminPolicy {
        +viewDashboard(User) bool
        +manageComplaints(User) bool
        +deleteComplaints(User) bool
        +manageDatabases(User) bool
        +manageSettings(User) bool
        +viewLogs(User) bool
    }

    class ComplaintPolicy {
        +viewAny(User) bool
        +view(User, Complaint) bool
        +create(User) bool
        +update(User, Complaint) bool
        +delete(User, Complaint) bool
    }

    %% === REQUESTS (Validation Layer) ===
    class StoreComplaintRequest {
        +authorize() bool
        +rules() array
        +messages() array
    }

    class LoginRequest {
        +authenticate() void
        +rules() array
    }

    %% === MIDDLEWARE ===
    class EnsureUserIsAdmin {
        +handle(Request, Closure) Response
    }

    class NoIndexMiddleware {
        +handle(Request, Closure) Response
    }

    class LogAdminAccess {
        +handle(Request, Closure) Response
    }

    %% === LISTENERS ===
    class LogSuccessfulLogin {
        +handle(Login) void
    }

    class LogFailedLogin {
        +handle(Failed) void
    }

    %% === SEEDERS ===
    class DatabaseSeeder {
        +run() void
    }

    class RoleSeeder {
        +run() void
    }

    class AdminUserSeeder {
        +run() void
    }

    class ComplaintSeeder {
        +run() void
    }

    class SettingSeeder {
        +run() void
    }

    %% === RELATIONSHIPS ===
    
    %% Model Relationships
    User ||--o{ Note : creates
    User }o--|| Role : has
    Role }o--o{ Permission : has
    Complaint ||--o{ Attachment : has
    Complaint ||--o{ Note : has
    Complaint ||--o{ StatusHistory : has
    
    %% Controller Inheritance
    Controller <|-- DashboardController
    Controller <|-- ComplaintController
    Controller <|-- ComplaintAdminController
    Controller <|-- NoteController
    Controller <|-- DatabaseController
    Controller <|-- UploadController
    Controller <|-- PageController
    Controller <|-- RegisteredUserController
    Controller <|-- AuthenticatedSessionController
    Controller <|-- ComplaintApiController
    Controller <|-- SearchApiController
    Controller <|-- ChatController
    
    %% Service Dependencies
    ComplaintController ..> PrivacyLogger : uses
    ComplaintAdminController ..> ComplaintPolicy : uses
    ChatController ..> ChatbotService : uses
    
    %% Policy Dependencies
    AdminPolicy ..> User : authorizes
    ComplaintPolicy ..> User : authorizes
    ComplaintPolicy ..> Complaint : authorizes
    
    %% Request Validation
    ComplaintController ..> StoreComplaintRequest : validates
    AuthenticatedSessionController ..> LoginRequest : validates
    
    %% Seeder Dependencies
    DatabaseSeeder ..> RoleSeeder : calls
    DatabaseSeeder ..> AdminUserSeeder : calls
    DatabaseSeeder ..> ComplaintSeeder : calls
    DatabaseSeeder ..> SettingSeeder : calls
    RoleSeeder ..> Role : creates
    RoleSeeder ..> Permission : creates
```

## 📊 Architectuur Layers

### 1. **Data Layer (Models)**
- **User**: Authenticatie en autorisatie
- **Complaint**: Hoofdentiteit voor klachten
- **Attachment**: Bestandsbijlagen
- **Note**: Interne notities
- **StatusHistory**: Statusveranderingen tracking
- **Setting**: Configuratie opslag
- **Role/Permission**: RBAC via Spatie Permission

### 2. **Application Layer (Controllers)**

#### Web Controllers
- **DashboardController**: Admin dashboard
- **ComplaintController**: Publieke klacht indienen
- **ComplaintAdminController**: Admin klacht beheer
- **NoteController**: Notitie beheer
- **DatabaseController**: Database overzicht
- **UploadController**: Bestand uploads

#### Auth Controllers  
- **RegisteredUserController**: Registratie
- **AuthenticatedSessionController**: Login/Logout

#### API Controllers
- **ComplaintApiController**: REST API voor klachten
- **SearchApiController**: Zoek functionaliteit
- **ChatController**: Chatbot endpoints

### 3. **Business Logic Layer (Services)**
- **ChatbotService**: AI chatbot logica
- **PrivacyLogger**: Privacy-compliant logging

### 4. **Authorization Layer (Policies)**
- **AdminPolicy**: Admin rechten controle
- **ComplaintPolicy**: Klacht toegangscontrole

### 5. **Validation Layer (Requests)**
- **StoreComplaintRequest**: Klacht validatie
- **LoginRequest**: Login validatie

### 6. **Infrastructure Layer**
- **Middleware**: Authenticatie, logging, SEO
- **Listeners**: Event handling
- **Seeders**: Database initialisatie

## 🔄 Data Flow Patterns

### 1. **Publieke Klacht Flow**
```
Request → ComplaintController → StoreComplaintRequest → Complaint Model → PrivacyLogger
```

### 2. **Admin Beheer Flow**
```
Request → Middleware → ComplaintAdminController → ComplaintPolicy → Complaint Model
```

### 3. **Chatbot Flow**
```
API Request → ChatController → ChatbotService → ComplaintApiController
```

### 4. **Authenticatie Flow**
```
LoginRequest → AuthenticatedSessionController → User Model → LogSuccessfulLogin
```

## 🎨 Design Patterns

### 1. **MVC Pattern**
- **Models**: Data en business rules
- **Views**: Presentatie layer (Blade templates)
- **Controllers**: Request handling en flow control

### 2. **Service Pattern**
- **ChatbotService**: Complexe business logica
- **PrivacyLogger**: Cross-cutting concerns

### 3. **Repository Pattern** (via Eloquent)
- **Model classes**: Data access abstraction
- **Eloquent ORM**: Database abstraction

### 4. **Policy Pattern**
- **Authorization policies**: Toegangscontrole
- **Gate system**: Centralized authorization

### 5. **Request/Response Pattern**
- **Form Requests**: Input validatie
- **API Resources**: Output formatting

## 🔗 Key Dependencies

### External Packages
- **Spatie Permission**: Role-based access control
- **Laravel Breeze**: Authentication scaffolding
- **Intervention Image**: Image processing

### Internal Dependencies
- **Models**: Foundation voor alle andere lagen
- **Services**: Business logic voor controllers
- **Policies**: Authorization voor alle actions
- **Middleware**: Cross-cutting concerns

## 📝 Implementatie Notes

### 1. **Naming Conventions**
- **Controllers**: `{Entity}Controller` pattern
- **Models**: Singular noun (User, Complaint)
- **Policies**: `{Model}Policy` pattern
- **Services**: `{Domain}Service` pattern

### 2. **Inheritance Structure**
- Alle controllers erven van base `Controller`
- Models gebruiken Laravel's base `Model`
- Policies implementeren Laravel's authorization

### 3. **Dependency Injection**
- Controllers ontvangen dependencies via constructor
- Services worden geregistreerd in `AppServiceProvider`
- Policies worden automatisch resolved

---

*Dit klassendiagram is gegenereerd op basis van de huidige implementatie van het Gemeente Klachtensysteem en wordt bijgewerkt bij structurele wijzigingen.*
