# 📝 PERSOONLIJK REFLECTIEVERSLAG - GEMEENTE KLACHTENSYSTEEM

**Naam Student:** Abdisamad Abdulle  
**Studentnummer:** [in te vullen]  
**Project:** Gemeente Klachtenportaal  
**Datum:** 7 november 2025  
**Periode:** 2024-2025 (blok 1)  

---

## 📋 INLEIDING

### Project Context
Het Gemeente Klachtensysteem is een Laravel-applicatie waarmee burgers problemen in de openbare ruimte kunnen melden en admins deze meldingen efficiënt kunnen afhandelen. Binnen het tweekoppige team nam ik de rol van lead backend developer en quality owner op mij. Ik beheerde de database- en beveiligingsarchitectuur, schreef de meeste feature tests en bewaakte dat we voor elke user story concreet bewijs konden tonen (documentatie + implementatie + test).

Belangrijkste doelstellingen:
- Een stabiele end-to-end workflow voor klachtindiening, inclusief GPS, foto’s en bevestiging.
- Een veilig beheerdersdashboard met kaartweergave, statusbeheer, notities en zoekfunctie.
- Volledige dekking van de onderwijs-eisen: user stories, ontwerpdocumenten, Definition of Fun/Done, planning en reflectie.

---

## ✅ WAT HEB IK GOED GEDAAN?

### 1. Technische Vaardigheden

**Laravel Development**
- ✨ Optionele GPS- en uploadfunctionaliteit gebouwd via `StoreComplaintRequest`, Alpine components en Intervention Image.
- ✨ Admin-dashboard en kaartcontroller opgezet met filters, zoekfunctie en Leaflet-integratie.
- ✨ Statusgeschiedenis, notities en scheduled commands (overdue + purge) uitgerust met privacy logging.

**Database & Security Design**
- ✨ ERD + migraties ontworpen met duidelijke relaties (complaints, attachments, notes, status_histories).
- ✨ Spatie Permission toegepast voor echte rollen i.p.v. losse `role`-kolommen.
- ✨ AVG-compliant retention + logging gerealiseerd via artisan commands en `PrivacyLogger`.

**Testing**
- ✨ Featuretests opgebouwd die zowel hoofd- als alternatieve scenario’s verifiëren.
- ✨ Mocking van uploads en geolocatievalidatie toegevoegd.
- ✨ Admin-toegangscontrole expliciet getest met RBAC helpers.

### 2. Samenwerking & Communicatie

**Teamwork**
- 🤝 Dagelijkse check-ins gehouden en blockers vroeg gedeeld.
- 🤝 PR’s en commit messages voorzien van context en koppeling met user stories.
- 🤝 Design- en securitybeslissingen vastgelegd in markdown zodat front-end taken zelfstandig verder konden.

**Communicatie**
- 💬 Stakeholders wekelijks geüpdatet over scope en risico’s (bijv. tijd voor kaartintegratie vs. chatbot).
- 💬 Definition of Fun en Definition of Done opgesteld samen met teammaat zodat verwachtingen helder bleven.

---

## 🤔 WAT KON BETER?

### Technisch
- ⚠️ Te laat begonnen met het genereren van model factories, waardoor tests aanvankelijk kapot liepen.
- ⚠️ Sommige statuswaarden (bijv. `resolved` vs `opgelost`) bleken inconsistent tussen views en tests; naming convention eerder aligneren.
- ⚠️ Documentatie groeide sneller dan gepland; structuur vooraf bepalen had tijd gescheeld.

### Persoonlijk & Samenwerking
- ⚠️ In drukke weken te veel tegelijk op mijn bord genomen (backend + infra + docs). Hierdoor duurden code reviews voor mijn teamgenoot soms langer dan ideaal.
- ⚠️ Had eerder om validatie van de planning moeten vragen; aannames over oplevermomenten veroorzaakten onnodige stress vlak voor de demo.

---

## 🚀 STAPPEN VOOR EEN NOG BETER RESULTAAT

1. **Factory- en seedingsjabloon klaarzetten vóórdat ik aan feature-tests begin**, zodat TDD echt vlekkeloos werkt.
2. **Terminologie-board introduceren** (statussen, rollen, veldnamen) en die koppelen aan zowel code als ontwerpdocumenten.
3. **Wekelijkse retro van 15 minuten verplicht stellen**, ook al lijkt alles soepel te lopen; kleine fricties vang je dan sneller.
4. **Documentatie modulair opzetten** (docs/tech, docs/process, docs/ux) zodat groei beheersbaar blijft en reviewers sneller de juiste info vinden.
5. **Persoonlijke timeboxing**: maximaal twee grote focusblokken per dag plannen en rest reserveren voor reviews/support.

---

## 📎 CONCLUSIE

Ik ben trots op het feit dat we de volledige eisenlijst (functioneel, ontwerp, beheer en reflectie) daadwerkelijk aantoonbaar hebben afgerond. De applicatie voelt production-ready en de documentatie maakt het voor derden eenvoudig om de oplossing te begrijpen of uit te breiden. Door meer upfront alignment op naming en door eerder tooling klaar te zetten kan ik de volgende keer de kwaliteit nog verder verhogen terwijl de stress richting deadline omlaag gaat.

> **Belangrijkste inzicht:** kwaliteit waarborgen is niet alleen code schrijven, maar vooral consistentie creëren tussen code, tests, documentatie en teamafspraken.

---

_Laatste update: 7 november 2025_
