# Use Case Diagram - Gemeente Portal

## 🎯 System Overview

Het Gemeente Portal systeem ondersteunt twee hoofdactoren: **Burgers** en **Admin Medewerkers**. Hieronder staat het complete use case diagram met alle functionaliteiten.

---

## 📊 Use Case Diagram

```
                    GEMEENTE PORTAL SYSTEEM
                    
┌─────────────────────────────────────────────────────────────────────┐
│                                                                     │
│                                                                     │
│  ┌────────┐                                                         │
│  │ Burger │                                                         │
│  │  👤    │                                                         │
│  └────┬───┘                                                         │
│       │                                                             │
│       │                                                             │
│       ├──────────► (Registreren)                                   │
│       │                                                             │
│       ├──────────► (Inloggen)                                      │
│       │                                                             │
│       ├──────────► (Klacht Indienen)                               │
│       │                  │                                          │
│       │                  ├────► (Locatie Kiezen op Kaart)          │
│       │                  ├────► (Foto's Uploaden)                  │
│       │                  └────► (Tracking Nummer Ontvangen)        │
│       │                                                             │
│       ├──────────► (Klacht Opvolgen)                               │
│       │                  │                                          │
│       │                  ├────► (Status Bekijken)                  │
│       │                  ├────► (Timeline Bekijken)                │
│       │                  └────► (Foto's Bekijken)                  │
│       │                                                             │
│       ├──────────► (Mijn Meldingen Bekijken)                       │
│       │                  │                                          │
│       │                  └────► (Tracking met ID/Email)            │
│       │                                                             │
│       ├──────────► (Profiel Beheren)                               │
│       │                  │                                          │
│       │                  ├────► (Naam/Email Wijzigen)              │
│       │                  └────► (Wachtwoord Wijzigen)              │
│       │                                                             │
│       └──────────► (Chatbot Gebruiken)                             │
│                          │                                          │
│                          └────► (Hulp Krijgen)                     │
│                                                                     │
│  ┌────────┐                                                         │
│  │ Admin  │                                                         │
│  │ 👨‍💼    │                                                         │
│  └────┬───┘                                                         │
│       │                                                             │
│       ├──────────► (Inloggen met Admin Account)                    │
│       │                                                             │
│       ├──────────► (Dashboard Bekijken)                            │
│       │                  │                                          │
│       │                  ├────► (Statistieken Bekijken)            │
│       │                  ├────► (5 Recente Klachten Zien)          │
│       │                  └────► (Kaart met Pins Bekijken)          │
│       │                                                             │
│       ├──────────► (Klachten Beheren)                              │
│       │                  │                                          │
│       │                  ├────► (Alle Klachten Bekijken)           │
│       │                  ├────► (Klacht Details Bekijken)          │
│       │                  ├────► (Status Wijzigen)                  │
│       │                  ├────► (Prioriteit Aanpassen)             │
│       │                  ├────► (Klacht Toewijzen)                 │
│       │                  ├────► (Interne Notities Toevoegen)       │
│       │                  └────► (Klacht Verwijderen)               │
│       │                                                             │
│       ├──────────► (Zoeken op ID)                                  │
│       │                  │                                          │
│       │                  └────► (Direct naar Details)              │
│       │                                                             │
│       ├──────────► (Filteren)                                      │
│       │                  │                                          │
│       │                  ├────► (Op Status)                        │
│       │                  └────► (Op Prioriteit)                    │
│       │                                                             │
│       ├──────────► (Kaart Gebruiken)                               │
│       │                  │                                          │
│       │                  ├────► (Alle Klachten Zien als Pins)     │
│       │                  ├────► (Pin Klikken voor Details)         │
│       │                  └────► (Zoom naar Specifieke Klacht)      │
│       │                                                             │
│       ├──────────► (Gebruikers Beheren)                            │
│       │                  │                                          │
│       │                  ├────► (Gebruikers Toevoegen)             │
│       │                  ├────► (Rollen Toewijzen)                 │
│       │                  └────► (Gebruikers Verwijderen)           │
│       │                                                             │
│       └──────────► (Uitloggen)                                     │
│                                                                     │
│                                                                     │
│  «system»                                                           │
│  ┌──────────────────┐                                              │
│  │ Email Systeem    │◄───────────────────────────────────────────┐ │
│  └──────────────────┘                                            │ │
│         │                                                         │ │
│         └────► (Tracking Nummer Versturen)                       │ │
│                                                                   │ │
│  «system»                                                         │ │
│  ┌──────────────────┐                                            │ │
│  │ File Storage     │◄───────────────────────────────────────────┘ │
│  └──────────────────┘                                              │
│         │                                                           │
│         └────► (Foto's Opslaan)                                    │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 👤 Actoren

### 1. **Burger (Primary Actor)**
Inwoners van de gemeente die problemen willen melden.

**Karakteristieken:**
- Geen technische kennis vereist
- Sporadisch gebruik (bij problemen)
- Mobiel en desktop toegang
- Anoniem of geregistreerd

**Goals:**
- Snel en eenvoudig problemen melden
- Status van meldingen kunnen opvolgen
- Bevestiging ontvangen van gemeente

---

### 2. **Admin Medewerker (Primary Actor)**
Gemeente medewerkers die klachten behandelen en beheren.

**Karakteristieken:**
- Dagelijks gebruik
- Desktop toegang (kantoor)
- Bevoegd voor klacht behandeling
- Verantwoordelijk voor follow-up

**Goals:**
- Overzicht behouden van alle klachten
- Efficiënt klachten afhandelen
- Communiceren met burgers
- Rapportage genereren

---

### 3. **Email Systeem (Supporting System)**
Geautomatiseerd systeem voor communicatie.

**Functionaliteit:**
- Tracking nummer versturen na indienen
- Status updates communiceren
- Herinneringen sturen

---

### 4. **File Storage Systeem (Supporting System)**
Bestandsbeheer voor uploads.

**Functionaliteit:**
- Foto's opslaan (max 10MB)
- Veilige opslag (storage/app/public)
- Bestand validatie

---

## 📋 Use Cases - Gedetailleerd

### **UC-001: Registreren**

**Actor:** Burger  
**Precondities:** Gebruiker heeft geen account  
**Postcondities:** Account is aangemaakt, gebruiker kan inloggen

**Main Flow:**
1. Burger navigeert naar registratie pagina
2. Systeem toont registratie formulier
3. Burger vult naam, email en wachtwoord in
4. Systeem valideert gegevens
5. Systeem maakt account aan
6. Systeem logt gebruiker automatisch in
7. Systeem toont dashboard

**Alternatieve Flows:**
- 4a. Email bestaat al → Foutmelding, terug naar stap 3
- 4b. Wachtwoord te zwak → Foutmelding, terug naar stap 3

---

### **UC-002: Klacht Indienen**

**Actor:** Burger (geregistreerd of anoniem)  
**Precondities:** Gebruiker heeft toegang tot website  
**Postcondities:** Klacht is opgeslagen, tracking nummer gegenereerd

**Main Flow:**
1. Burger klikt op "Melding Doen"
2. Systeem toont klacht formulier (Sectie A-D)
3. Burger vult contactgegevens in (Sectie A)
4. Burger vult klacht details in (Sectie B)
   - Titel
   - Beschrijving
   - Categorie (dropdown)
   - Prioriteit (dropdown)
5. Burger kiest locatie (Sectie C)
   - GPS locatie op kaart
   - Of handmatige lat/lng invoer
6. Burger uploadt foto's (Sectie D) (optioneel, max 5)
7. Systeem valideert alle velden
8. Burger klikt "Indienen"
9. Systeem slaat klacht op in database
10. Systeem genereert uniek tracking nummer
11. Systeem toont bevestigingspagina met tracking nummer
12. Systeem stuurt email met tracking nummer (indien email opgegeven)

**Alternatieve Flows:**
- 7a. Verplichte velden ontbreken → Foutmelding met rode borders
- 7b. Foto's te groot (>10MB) → Foutmelding, verwijder foto
- 8a. Geen locatie ingevuld → Prompt om locatie te kiezen
- 12a. Email ongeldig → Geen email versturen, wel opslaan

**Business Rules:**
- Max 5 foto's per klacht
- Max 10MB per foto
- Verplichte velden: naam, email, titel, beschrijving, categorie
- Tracking nummer = ID van klacht record

---

### **UC-003: Klacht Opvolgen (Tracking)**

**Actor:** Burger  
**Precondities:** Burger heeft tracking nummer of email  
**Postcondities:** Status van klacht is zichtbaar

**Main Flow:**
1. Burger navigeert naar "Track je Klacht"
2. Systeem toont zoekformulier
3. Burger vult tracking ID en email in
4. Systeem zoekt klacht in database
5. Systeem toont klacht details pagina met:
   - Status badge (Open/In Behandeling/Verwerkt/Gesloten)
   - Titel en beschrijving
   - Categorie en prioriteit
   - Locatie
   - Foto's (indien aanwezig)
   - Timeline met statusgeschiedenis
6. Burger bekijkt informatie

**Alternatieve Flows:**
- 4a. Klacht niet gevonden → "Klacht niet gevonden" melding
- 4b. Email komt niet overeen → "Ongeldige combinatie" melding

**Extended Use Cases:**
- **UC-003.1:** Status Bekijken
  - Grote gekleurde badge toont huidige status
  - Groen = Verwerkt (✅ VERWERKT)
  - Oranje = In Behandeling (🟠)
  - Rood = Open (🔴)

- **UC-003.2:** Timeline Bekijken
  - Chronologische lijst van statuswijzigingen
  - "Klacht Ingediend" altijd eerste item
  - "Verwerkt" met groene highlight als afgerond

---

### **UC-004: Admin Dashboard Bekijken**

**Actor:** Admin Medewerker  
**Precondities:** Admin is ingelogd  
**Postcondities:** Dashboard is geladen met actuele data

**Main Flow:**
1. Admin logt in met admin account
2. Systeem herkent admin rol
3. Systeem redirect naar admin dashboard
4. Systeem toont dashboard met:
   - Statistieken cards (Totaal, Open, In Behandeling, Opgelost)
   - Zoek op ID input veld
   - Status en prioriteit filters
   - Interactieve Leaflet kaart met alle klachten als pins
   - Tabel met 5 meest recente klachten
   - Legend met pin kleuren
5. Admin bekijkt overzicht

**Included Use Cases:**
- **UC-004.1:** Statistieken bekijken (automatisch)
- **UC-004.2:** Kaart laden met pins (automatisch)
- **UC-004.3:** Recente klachten tonen (automatisch)

---

### **UC-005: Klacht Status Wijzigen**

**Actor:** Admin Medewerker  
**Precondities:** Admin bekijkt klacht details  
**Postcondities:** Status is gewijzigd, history record toegevoegd

**Main Flow:**
1. Admin opent klacht details pagina
2. Systeem toont huidige status
3. Admin selecteert nieuwe status uit dropdown
   - Open
   - In Behandeling (in_progress)
   - Opgelost (resolved/opgelost)
   - Gesloten (closed)
4. Admin klikt "Status Updaten"
5. Systeem valideert statuswijziging
6. Systeem update complaints.status
7. Systeem maakt record in status_histories tabel
8. Systeem toont success melding
9. Admin ziet nieuwe status

**Business Rules:**
- Alle status transitions zijn toegestaan
- Status history wordt altijd gelogd
- Melder ziet status update bij tracking

---

### **UC-006: Zoeken op ID**

**Actor:** Admin Medewerker  
**Precondities:** Admin is op dashboard  
**Postcondities:** Specifieke klacht is geopend

**Main Flow:**
1. Admin vult klacht ID in zoekbalk
2. Admin klikt "Zoek op ID" button
3. Systeem zoekt klacht met opgegeven ID
4. Systeem redirect naar `/admin/complaints/{id}`
5. Systeem toont klacht details pagina

**Alternatieve Flows:**
- 3a. ID bestaat niet → Alert "Klacht niet gevonden"
- 3b. Leeg ID veld → Alert "Voer een klacht ID in"

---

### **UC-007: Kaart Interactie**

**Actor:** Admin Medewerker  
**Precondities:** Dashboard is geladen, klachten hebben GPS coords  
**Postcondities:** Admin ziet klacht details via kaart

**Main Flow:**
1. Systeem laadt Leaflet kaart met OpenStreetMap tiles
2. Systeem haalt alle klachten op via `/admin/api/dashboard/map-data`
3. Systeem plaatst custom pin markers op kaart voor elke klacht
   - Pin kleur gebaseerd op status:
     - Rood (#EF4444) = Open
     - Oranje (#F59E0B) = In Progress
     - Groen (#10B981) = Resolved
     - Grijs (#6B7280) = Closed
   - Pin toont klacht ID nummer in witte cirkel
4. Admin klikt op pin
5. Systeem toont popup met:
   - Klacht #ID (header met status kleur)
   - Titel
   - Beschrijving (80 chars)
   - Status badge
   - Prioriteit
   - Categorie
   - Locatie
   - Datum
   - "Bekijk Volledige Details" button
6. Admin klikt popup button
7. Systeem opent klacht details pagina

**Alternatieve Flows:**
- Admin kan filteren op status/prioriteit
- Kaart zoom past aan om alle pins te tonen
- Admin kan handmatig zoomen en pannen

---

### **UC-008: Gebruikers Beheren**

**Actor:** Admin Medewerker  
**Precondities:** Admin heeft admin rol  
**Postcondities:** Gebruiker is toegevoegd/gewijzigd/verwijderd

**Main Flow:**
1. Admin navigeert naar gebruikersbeheer
2. Systeem toont lijst van alle gebruikers
3. Admin kiest actie:
   - Nieuwe gebruiker toevoegen
   - Rol toewijzen (admin/user)
   - Gebruiker verwijderen
4. Systeem voert actie uit
5. Systeem update database
6. Systeem toont success melding

**Business Rules:**
- Alleen admins kunnen gebruikers beheren
- Admin kan zichzelf niet verwijderen
- Tenminste 1 admin moet blijven bestaan

---

## 🔄 Use Case Relaties

### **«include»** Relaties
- **Klacht Indienen** includes **Locatie Kiezen**
- **Klacht Indienen** includes **Foto Uploaden**
- **Dashboard Bekijken** includes **Statistieken Laden**
- **Dashboard Bekijken** includes **Kaart Laden**

### **«extend»** Relaties
- **Foto Uploaden** extends **Klacht Indienen** (optioneel)
- **Interne Notities Toevoegen** extends **Klacht Beheren**
- **Klacht Toewijzen** extends **Klacht Beheren**

### **Generalization** Relaties
- **Inloggen** ← **Inloggen met Admin Account** (specialisatie)
- **Klacht Beheren** is generalisatie van:
  - Status Wijzigen
  - Prioriteit Aanpassen
  - Klacht Toewijzen

---

## 🎯 Use Case Prioriteiten

### **Must Have (Kritisch)** ✅
- UC-001: Registreren
- UC-002: Klacht Indienen
- UC-003: Klacht Opvolgen
- UC-004: Admin Dashboard
- UC-005: Status Wijzigen

### **Should Have (Belangrijk)** ✅
- UC-006: Zoeken op ID
- UC-007: Kaart Interactie
- UC-008: Gebruikers Beheren

### **Could Have (Nice to Have)** 🚧
- Email notificaties bij status update
- PDF export van klacht
- Bulk status wijzigingen
- Rapportage dashboard

### **Won't Have (Niet in Scope)** ❌
- SMS notificaties
- Mobile app
- Real-time chat
- Payment processing

---

## 📊 Use Case Statistieken

### Geïmplementeerd
- **Totaal Use Cases:** 15
- **Afgerond:** 15 (100%)
- **Burger Use Cases:** 6
- **Admin Use Cases:** 9
- **System Use Cases:** 2

### Complexiteit
- **Eenvoudig:** 5 (Inloggen, Uitloggen, Profiel, etc.)
- **Middel:** 7 (Klacht Indienen, Dashboard, Zoeken)
- **Complex:** 3 (Kaart Interactie, Status Management, Gebruikersbeheer)

---

## 🔐 Security Use Cases

### **SEC-001: Authenticatie**
- Alle admin functies vereisen login
- Session management via Laravel
- CSRF protection op alle forms

### **SEC-002: Autorisatie**
- Role-based access (Spatie Permission)
- Admin middleware op protected routes
- User kan alleen eigen klachten zien

### **SEC-003: Input Validatie**
- Server-side validatie op alle input
- File upload restrictie (10MB, image types)
- XSS preventie via Blade escaping

---

**Laatste Update:** 20 November 2025  
**Status:** Alle use cases geïmplementeerd en getest  
**Versie:** 1.0 (Production Ready)
