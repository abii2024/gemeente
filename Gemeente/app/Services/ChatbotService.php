<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class ChatbotService
{
    /**
     * @var array<string, array<string, mixed>>
     */
    private array $knowledgeBase;

    /**
     * @var array<string, array<string>>
     */
    private array $intents;

    public function __construct()
    {
        $this->loadKnowledgeBase();
        $this->setupIntents();
    }

    /**
     * Process user message and return chatbot response
     *
     * @return array<string, mixed>
     */
    public function processMessage(string $message): array
    {
        $message = $this->normalizeMessage($message);
        $intent = $this->detectIntent($message);

        $response = $this->generateResponse($intent, $message);

        // Log conversation for improvement (without PII)
        Log::channel('privacy_safe')->info('Chatbot interaction', [
            'intent' => $intent,
            'message_length' => strlen($message),
            'response_type' => $response['type'] ?? 'text',
        ]);

        return $response;
    }

    /**
     * Get welcome message for new chat sessions
     *
     * @return array<string, mixed>
     */
    public function getWelcomeMessage(): array
    {
        return [
            'type' => 'welcome',
            'message' => "Welkom bij de gemeente chatbot! 🏛️\n\n".
                        "Ik kan u helpen met:\n".
                        "📋 Klachten indienen en status opzoeken\n".
                        "👤 Burgerzaken (paspoort, uittreksel, verhuizen)\n".
                        "🚗 Verkeer en parkeren\n".
                        "🗑️ Afval en recycling\n".
                        "💰 Belastingen en kwijtschelding\n".
                        "🔍 Uw klacht-ID terugvinden\n".
                        "📞 Contact informatie en openingstijden\n".
                        "ℹ️ Algemene vragen over gemeente diensten\n\n".
                        'Waarmee kan ik u vandaag helpen?',
            'quick_replies' => [
                'Klacht indienen',
                'Paspoort info',
                'Status opzoeken',
                'Afval informatie',
                'Contact informatie',
            ],
        ];
    }

    /**
     * Get FAQ overview
     */
    public function getFAQOverview(): array
    {
        return [
            'type' => 'faq',
            'message' => "Veelgestelde vragen: ❓\n\n".
                        "**👤 Burgerzaken:**\n".
                        "• Hoe vraag ik een paspoort aan?\n".
                        "• Waar vind ik mijn uittreksel?\n".
                        "• Hoe meld ik een verhuizing?\n\n".
                        "**📋 Klacht gerelateerd:**\n".
                        "• Hoe dien ik een klacht in?\n".
                        "• Waar vind ik mijn klacht-ID?\n".
                        "• Wat betekenen de verschillende statussen?\n\n".
                        "**🚗 Verkeer & Parkeren:**\n".
                        "• Hoe krijg ik een parkeervergunning?\n".
                        "• Wat kost parkeren?\n\n".
                        "**🗑️ Afval & Milieu:**\n".
                        "• Wanneer wordt mijn afval opgehaald?\n".
                        "• Waar kan ik grof vuil kwijt?\n\n".
                        "**📞 Contact & Service:**\n".
                        "• Wat zijn de openingstijden?\n".
                        "• Hoe kan ik contact opnemen?\n".
                        '• Welke diensten zijn online beschikbaar?',
            'quick_replies' => [
                'Paspoort aanvragen',
                'Klacht indienen uitleg',
                'Afval ophaaldata',
                'Parkeervergunning',
                'Contact informatie',
            ],
        ];
    }

    /**
     * Normalize user message for processing
     */
    private function normalizeMessage(string $message): string
    {
        return strtolower(trim($message));
    }

    /**
     * Detect user intent from message
     */
    private function detectIntent(string $message): string
    {
        foreach ($this->intents as $intent => $patterns) {
            foreach ($patterns as $pattern) {
                if (str_contains($message, $pattern)) {
                    return $intent;
                }
            }
        }

        return 'general';
    }

    /**
     * Generate response based on intent and message
     */
    private function generateResponse(string $intent, string $message): array
    {
        switch ($intent) {
            // Basis interacties
            case 'groeting':
                return $this->getGreeting();
            case 'bedankt':
                return $this->getThankYou();

                // Klachten
            case 'klacht_status':
                return $this->getStatusInfo();
            case 'klacht_id_zoeken':
                return $this->getKlachtIdHelp();
            case 'klacht_indienen':
                return $this->getComplaintSubmissionInfo();

                // Burgerzaken
            case 'paspoort':
                return $this->getPassportInfo();
            case 'uittreksel':
                return $this->getExtractInfo();
            case 'verhuizen':
                return $this->getMovingInfo();
            case 'trouwen':
                return $this->getMarriageInfo();

                // Contact & Service
            case 'contact_info':
                return $this->getContactInfo();
            case 'openingstijden':
                return $this->getOpeningHours();
            case 'locatie':
                return $this->getLocationInfo();

                // Verkeer & Parkeren
            case 'parking':
                return $this->getParkingInfo();
            case 'verkeer':
                return $this->getTrafficInfo();

                // Afval & Milieu
            case 'afval':
                return $this->getWasteInfo();
            case 'milieupark':
                return $this->getRecyclingCenterInfo();

                // Belastingen
            case 'belastingen':
                return $this->getTaxInfo();
            case 'kwijtschelding':
                return $this->getTaxReliefInfo();

                // Sociale Zaken
            case 'bijstand':
                return $this->getSocialBenefitsInfo();
            case 'schuldhulp':
                return $this->getDebtHelpInfo();

                // Evenementen
            case 'evenementen':
                return $this->getEventsInfo();

                // Digitale Services
            case 'mijn_gemeente':
                return $this->getMyMunicipalityInfo();
            case 'app':
                return $this->getAppsInfo();

                // Noodsituaties
            case 'calamiteit':
                return $this->getEmergencyInfo();

            default:
                return $this->getGeneralHelp();
        }
    }

    private function setupIntents(): void
    {
        $this->intents = [
            // Basis interacties
            'groeting' => ['hallo', 'hoi', 'goedemiddag', 'goedemorgen', 'hey', 'hi', 'dag'],
            'bedankt' => ['bedankt', 'dank je', 'dankjewel', 'thanks', 'dank u', 'top'],

            // Klachten gerelateerd
            'klacht_status' => ['status', 'stand van zaken', 'hoe staat het', 'voortgang', 'behandeling'],
            'klacht_id_zoeken' => ['klacht nummer', 'klacht id', 'klaagt-id', 'referentienummer', 'waar vind ik'],
            'klacht_indienen' => ['klacht indienen', 'melding doen', 'probleem melden', 'nieuwe klacht'],

            // Contact & Service
            'contact_info' => ['contact', 'bellen', 'telefoonnummer', 'email', 'bereikbaar'],
            'openingstijden' => ['openingstijden', 'open', 'gesloten', 'bereikbaar', 'kantoor'],
            'locatie' => ['waar', 'adres', 'locatie', 'gemeentehuis', 'route'],

            // Burgerzaken
            'paspoort' => ['paspoort', 'identiteitskaart', 'id kaart', 'reisdocument'],
            'uittreksel' => ['uittreksel', 'gba', 'basisregistratie', 'personen'],
            'verhuizen' => ['verhuizen', 'adreswijziging', 'inschrijven', 'uitschrijven'],
            'trouwen' => ['trouwen', 'huwelijk', 'partnerschap', 'ceremonie'],

            // Verkeer & Parkeren
            'parking' => ['parkeren', 'parkeervergunning', 'parkeerplaats', 'betaald parkeren'],
            'verkeer' => ['verkeer', 'ontheffing', 'verkeersbord', 'snelheid'],

            // Afval & Milieu
            'afval' => ['afval', 'grofvuil', 'container', 'ophalen', 'recycling'],
            'milieupark' => ['milieupark', 'stort', 'chemisch afval', 'elektro'],

            // Belastingen
            'belastingen' => ['belasting', 'ozb', 'rioolheffing', 'onroerendezaak', 'betalen'],
            'kwijtschelding' => ['kwijtschelding', 'financiële problemen', 'niet betalen'],

            // Sociale Zaken
            'bijstand' => ['bijstand', 'uitkering', 'sociale dienst', 'aanvraag'],
            'schuldhulp' => ['schulden', 'schuldhulp', 'financiële problemen'],

            // Evenementen
            'evenementen' => ['evenement', 'activiteit', 'festival', 'markt'],

            // Digitale Services
            'mijn_gemeente' => ['mijn gemeente', 'inloggen', 'digid', 'account'],
            'app' => ['app', 'meldapp', 'afvalapp', 'parkeerapp'],

            // Noodsituaties
            'calamiteit' => ['calamiteit', 'noodsituatie', 'storm', 'overstroming'],
        ];
    }

    private function loadKnowledgeBase(): void
    {
        $this->knowledgeBase = [
            'status_info' => [
                'open' => 'Uw klacht is ontvangen en wordt binnenkort opgepakt door onze medewerkers.',
                'in_behandeling' => 'Uw klacht wordt momenteel behandeld. We houden u op de hoogte van de voortgang.',
                'opgelost' => 'Uw klacht is afgerond. Bedankt voor uw melding!',
            ],
            'contact' => [
                'phone' => '14 020',
                'email' => 'info@gemeente.nl',
                'hours' => 'Maandag t/m vrijdag: 08:00 - 17:00',
                'address' => 'Gemeentehuis, Grote Markt 1, 1000 AA Gemeente',
            ],
            'services' => [
                'burgerzaken' => [
                    'name' => 'Burgerzaken',
                    'location' => 'Gemeentehuis, balie 1-5',
                    'hours' => 'Ma-Vr: 08:30-17:00, Do tot 19:00',
                    'appointment' => 'Verplicht - online reserveren via gemeente.nl/afspraak',
                    'costs' => 'Paspoort: €76,41, ID-kaart: €64,85',
                ],
                'parking' => [
                    'center_rate' => '€2,50 per uur',
                    'other_rate' => '€1,50 per uur',
                    'permits' => 'Bewoners: €120/jaar, Bezoekers: €3/dag',
                    'blue_zone' => 'Gratis max 2 uur met parkeerschijf',
                ],
                'waste' => [
                    'household' => 'Dinsdag',
                    'gft' => 'Vrijdag',
                    'pmd' => 'Om de week woensdag',
                    'bulky' => '€25 per m³ op afspraak',
                ],
                'taxes' => [
                    'ozb' => '0,1234% van WOZ-waarde',
                    'sewer' => '€156 per jaar',
                    'waste_fee' => '€234 per jaar',
                ],
            ],
            'locations' => [
                'gemeentehuis' => [
                    'address' => 'Grote Markt 1, 1000 AA Gemeente',
                    'parking' => 'Betaald parkeren, eerste 30 min gratis',
                    'accessibility' => 'Rolstoeltoegankelijk, lift aanwezig',
                ],
                'milieupark' => [
                    'address' => 'Industrieweg 45, 1000 BB Gemeente',
                    'hours' => 'Di/Do/Za: 09:00-17:00',
                    'costs' => 'Eerste 2m³ gratis, daarna €25/m³',
                ],
            ],
            'events' => [
                'Sinterklaasintocht 2025 - 16 november 14:00',
                'Kerstmarkt - 14-15 december op de Grote Markt',
                'Nieuwjaarsreceptie - 12 januari 2026 15:00-17:00',
                'Koningsdag viering - 27 april 2026 hele dag',
            ],
        ];
    }

    // === RESPONSE FUNCTIONS ===

    private function getGreeting(): array
    {
        return [
            'type' => 'greeting',
            'message' => "Hallo! 👋 Fijn dat u contact opneemt met de gemeente.\n\n".
                        "Ik ben hier om u te helpen met al uw vragen over gemeente diensten, klachten, burgerzaken en meer.\n\n".
                        'Waar kan ik u mee helpen?',
            'quick_replies' => ['Klacht indienen', 'Paspoort info', 'Contact informatie', 'Afval informatie'],
        ];
    }

    private function getThankYou(): array
    {
        return [
            'type' => 'thanks',
            'message' => "Graag gedaan! 😊\n\n".
                        "Heeft u nog andere vragen? Ik help graag verder!\n\n".
                        "U kunt ook altijd contact opnemen via:\n".
                        '📞 '.$this->knowledgeBase['contact']['phone']."\n".
                        '📧 '.$this->knowledgeBase['contact']['email'],
            'quick_replies' => ['Andere vraag', 'Contact informatie', 'Openingstijden'],
        ];
    }

    private function getStatusInfo(): array
    {
        return [
            'type' => 'status_help',
            'message' => "Status van uw klacht opzoeken 📋\n\n".
                        "**Klacht statussen betekenen:**\n".
                        '🔵 **Open** - '.$this->knowledgeBase['status_info']['open']."\n".
                        '🟡 **In behandeling** - '.$this->knowledgeBase['status_info']['in_behandeling']."\n".
                        '🟢 **Opgelost** - '.$this->knowledgeBase['status_info']['opgelost']."\n\n".
                        'Heeft u uw klacht-ID? Dan kunt u de status online opzoeken.',
            'action_button' => [
                'text' => 'Status Opzoeken',
                'url' => '/klacht/status',
            ],
            'quick_replies' => ['Waar vind ik mijn klacht-ID?', 'Contact opnemen', 'Nieuwe klacht indienen'],
        ];
    }

    private function getKlachtIdHelp(): array
    {
        return [
            'type' => 'klacht_id_help',
            'message' => "Uw klacht-ID terugvinden 🔍\n\n".
                        "**Waar vindt u uw klacht-ID:**\n".
                        "📧 **Email bevestiging** - Direct na indienen ontvangt u een email\n".
                        "🌐 **Bevestigingspagina** - Na het indienen wordt uw klacht-ID getoond\n".
                        "📱 **Formaat** - Uw klacht-ID ziet er zo uit: #123456\n\n".
                        "**Email niet ontvangen?**\n".
                        "• Controleer uw spam/ongewenst folder\n".
                        '• Bel ons op '.$this->knowledgeBase['contact']['phone'].' met uw naam en tijdstip van indienen',
            'quick_replies' => ['Bel gemeentehuis', 'Status opzoeken', 'Nieuwe klacht indienen'],
        ];
    }

    private function getComplaintSubmissionInfo(): array
    {
        return [
            'type' => 'complaint_submission',
            'message' => "Nieuwe klacht indienen 📝\n\n".
                        "**Stap voor stap:**\n".
                        "1️⃣ Klik op 'Klacht Indienen' op onze website\n".
                        "2️⃣ Vul titel en beschrijving in\n".
                        "3️⃣ Kies de juiste categorie\n".
                        "4️⃣ Voeg locatie toe (GPS mogelijk)\n".
                        "5️⃣ Upload eventuele foto's\n".
                        "6️⃣ Vul uw contactgegevens in\n".
                        "7️⃣ Verstuur en ontvang uw klacht-ID\n\n".
                        "💡 **Tip:** Zorg voor een duidelijke beschrijving en voeg foto's toe!",
            'action_button' => [
                'text' => 'Klacht Indienen',
                'url' => '/klacht/indienen',
            ],
            'quick_replies' => ['Contact informatie', 'Status uitleg', 'Openingstijden'],
        ];
    }

    private function getContactInfo(): array
    {
        return [
            'type' => 'contact',
            'message' => "Contact & Bereikbaarheid - Alle Kanalen 📞\n\n".
                        "**🏢 ALGEMEEN CONTACT:**\n".
                        '• **Telefoon:** '.$this->knowledgeBase['contact']['phone']." (lokaal tarief)\n".
                        '• **Email:** '.$this->knowledgeBase['contact']['email']."\n".
                        '• **Adres:** '.$this->knowledgeBase['contact']['address']."\n".
                        "• **Website:** www.gemeente.nl\n\n".
                        "**🕒 ALGEMENE OPENINGSTIJDEN:**\n".
                        '• **Telefoon:** '.$this->knowledgeBase['contact']['hours']."\n".
                        '• **Balie:** '.$this->knowledgeBase['services']['burgerzaken']['hours']."\n".
                        '• **Chat:** '.$this->knowledgeBase['contact']['hours']."\n".
                        "• **Email:** Reactie binnen 24 uur\n\n".
                        "**📍 SPECIALISTISCHE AFDELINGEN:**\n".
                        "• **Burgerzaken:** burgerzaken@gemeente.nl | 14 020 tst 1\n".
                        "• **Belastingen:** belastingen@gemeente.nl | 14 020 tst 2\n".
                        "• **Sociale Zaken:** socialewerk@gemeente.nl | 14 020 tst 3\n".
                        "• **Vergunningen:** vergunningen@gemeente.nl | 14 020 tst 4\n".
                        "• **Parkeren:** parkeren@gemeente.nl | 14 020 tst 5\n".
                        "• **Afval:** afval@gemeente.nl | 14 020 tst 6\n".
                        "• **Klachten:** klachten@gemeente.nl | 14 020 tst 7\n\n".
                        "**🚨 NOODSITUATIES:**\n".
                        "• **Spoedeisend:** 112 (brand, politie, ambulance)\n".
                        "• **Politie niet-spoed:** 0900-8844\n".
                        "• **Storingen:** 0800-1020 (gas, water, elektra)\n".
                        '• **Gemeente calamiteiten:** '.$this->knowledgeBase['contact']['phone']."\n\n".
                        "**💬 DIGITALE KANALEN:**\n".
                        "• **WhatsApp:** 06-12345678 (werkdagen 9-17u)\n".
                        "• **Facebook:** @GemeenteOfficieel\n".
                        "• **Twitter/X:** @gemeente_nl\n".
                        "• **LinkedIn:** Gemeente Nederland\n".
                        "• **YouTube:** Gemeente Kanaal\n\n".
                        "**🏠 BEZOEK GEMEENTEHUIS:**\n".
                        '• **Adres:** '.$this->knowledgeBase['locations']['gemeentehuis']['address']."\n".
                        '• **Parkeren:** '.$this->knowledgeBase['locations']['gemeentehuis']['parking']."\n".
                        '• **Toegankelijkheid:** '.$this->knowledgeBase['locations']['gemeentehuis']['accessibility']."\n".
                        "• **OV:** Bus 12, 34 halte Gemeentehuis\n".
                        "• **Fiets:** Gratis stalling achter gebouw\n\n".
                        "**📱 AFSPRAKEN MAKEN:**\n".
                        "• **Online:** gemeente.nl/afspraak (24/7)\n".
                        '• **Telefonisch:** '.$this->knowledgeBase['contact']['phone']."\n".
                        "• **App:** Download GemeenteApp\n".
                        "• **Annuleren:** Tot 2 uur van tevoren\n\n".
                        "**🌐 ONLINE DIENSTEN:**\n".
                        "• **Mijn Gemeente:** Persoonlijke omgeving (DigiD)\n".
                        "• **Uittreksel aanvragen:** Direct downloadbaar\n".
                        "• **Verhuizing melden:** 24/7 beschikbaar\n".
                        "• **Belastingen betalen:** Online banking\n".
                        "• **Status klachten:** Real-time updates\n\n".
                        "**🗣️ TAALONDERSTEUNING:**\n".
                        "• **Nederlands:** Alle kanalen\n".
                        "• **Engels:** Telefonisch en email\n".
                        "• **Arabisch:** Dinsdag 10-12u (telefonisch)\n".
                        "• **Turks:** Woensdag 14-16u (telefonisch)\n".
                        "• **Tolk:** Op afspraak beschikbaar\n\n".
                        "**♿ TOEGANKELIJKHEID:**\n".
                        "• **Rolstoel:** Volledig toegankelijk\n".
                        "• **Slechtziend:** Braille documenten\n".
                        "• **Slechthorend:** Doventolk op afspraak\n".
                        "• **Begeleiding:** Familie mag meekomen\n\n".
                        "**💡 TIPS VOOR CONTACT:**\n".
                        "• Houd uw BSN of klacht-ID bij de hand\n".
                        "• Email voor niet-urgente zaken\n".
                        "• Chat voor snelle vragen\n".
                        "• Telefoon voor complexe zaken\n".
                        '• Afspraak voor persoonlijke begeleiding',
            'quick_replies' => ['Afspraak maken', 'WhatsApp contact', 'Noodsituaties', 'Route gemeentehuis'],
        ];
    }

    private function getOpeningHours(): array
    {
        return [
            'type' => 'opening_hours',
            'message' => "Openingstijden 🕒\n\n".
                        '**Algemeen:** '.$this->knowledgeBase['contact']['hours']."\n\n".
                        '**Burgerzaken:** '.$this->knowledgeBase['services']['burgerzaken']['hours']."\n".
                        '**Let op:** '.$this->knowledgeBase['services']['burgerzaken']['appointment']."\n\n".
                        '**Milieupark:** '.$this->knowledgeBase['locations']['milieupark']['hours']."\n\n".
                        '💡 **Tip:** Veel diensten zijn 24/7 online beschikbaar!',
            'action_button' => [
                'text' => 'Afspraak Maken',
                'url' => '/afspraak',
            ],
            'quick_replies' => ['Contact informatie', 'Online diensten', 'Route plannen'],
        ];
    }

    private function getPassportInfo(): array
    {
        return [
            'type' => 'passport_info',
            'message' => "Paspoort & ID-kaart - Volledige Gids 🛂\n\n".
                        "**💰 KOSTEN 2025:**\n".
                        "• Nederlandse paspoort: €76,41\n".
                        "• Nederlandse identiteitskaart: €64,85\n".
                        "• Spoedprocedure (+€51,50): Klaar binnen 3 werkdagen\n".
                        "• Vervangingsdocument verlies/diefstal: +€51,50\n\n".
                        "**📋 BENODIGDE DOCUMENTEN:**\n".
                        "• Geldig identiteitsbewijs (huidige paspoort/ID)\n".
                        "• Recente pasfoto (biometrisch, 35x45mm)\n".
                        "• Uittreksel GBA/BRP (max 6 maanden oud)\n".
                        "• Bij verlies/diefstal: aangiftebewijs politie\n".
                        "• Minderjarigen: toestemming beide ouders\n\n".
                        "**📸 PASFOTO EISEN:**\n".
                        "• Formaat: 35x45 mm\n".
                        "• Hoofd: 26-36 mm hoog\n".
                        "• Achtergrondskleur: lichtgrijs\n".
                        "• Scherp en in kleur\n".
                        "• Recht vooraanzicht, neutrale gezichtsuitdrukking\n".
                        "• Geen hoofddeksel (behalve religieus)\n\n".
                        "**⏰ PROCEDURE & TIJDEN:**\n".
                        "• Afspraak maken: VERPLICHT - online of telefonisch\n".
                        "• Aanvraag duur: 15-20 minuten\n".
                        "• Vingerafdrukken: Worden afgenomen (10 jaar geldig)\n".
                        "• Normale procedure: 5-6 werkdagen\n".
                        "• Spoed: 3 werkdagen (+€51,50)\n".
                        "• Ophalen: Binnen 3 maanden\n\n".
                        "**🏢 LOCATIE & OPENINGSTIJDEN:**\n".
                        '• Waar: '.$this->knowledgeBase['services']['burgerzaken']['location']."\n".
                        '• Wanneer: '.$this->knowledgeBase['services']['burgerzaken']['hours']."\n".
                        "• Parkeren: Eerste 30 min gratis bij gemeentehuis\n\n".
                        "**👶 KINDEREN & MINDERJARIGEN:**\n".
                        "• 0-11 jaar: Gratis Nederlandse identiteitskaart\n".
                        "• 12-17 jaar: €32,43 voor Nederlandse identiteitskaart\n".
                        "• Beide ouders aanwezig of machtiging\n".
                        "• Vingerafdrukken vanaf 12 jaar\n\n".
                        "**🆔 GELDIGHEIDSDUUR:**\n".
                        "• Paspoort: 10 jaar (vanaf 18 jaar)\n".
                        "• Paspoort: 5 jaar (12-18 jaar)\n".
                        "• ID-kaart: 10 jaar (vanaf 18 jaar)\n".
                        "• ID-kaart: 5 jaar (12-18 jaar)\n\n".
                        "**💡 HANDIGE TIPS:**\n".
                        "• Controleer vervaldatum 6 maanden voor reis\n".
                        "• EU-landen accepteren Nederlandse ID-kaart\n".
                        "• Buiten EU meestal paspoort verplicht\n".
                        "• Digitale foto mogelijk bij gemeentehuis\n".
                        "• Betaling: Pin, contant of online vooraf\n\n".
                        "**📱 ONLINE SERVICES:**\n".
                        "• Afspraak maken: gemeente.nl/afspraak\n".
                        "• Status opvragen: met aanvraagnummer\n".
                        '• Uittreksel bestellen: 24/7 online',
            'action_button' => [
                'text' => 'Afspraak Maken',
                'url' => '/burgerzaken/afspraak',
            ],
            'quick_replies' => ['Pasfoto eisen', 'Spoedprocedure', 'Kosten overzicht', 'Voor kinderen'],
        ];
    }

    private function getExtractInfo(): array
    {
        return [
            'type' => 'extract_info',
            'message' => "Uittreksel GBA/BRP aanvragen 📄\n\n".
                        "**Online:** Via gemeente.nl/uittreksel (24/7 beschikbaar)\n".
                        "**Balie:** Met geldig identiteitsbewijs\n".
                        "**Kosten:** €14,20 per uittreksel\n".
                        "**Levertijd:** Direct online, balie 5 minuten\n\n".
                        "**Soorten uittreksels:**\n".
                        "• Basis uittreksel (meest gebruikt)\n".
                        "• Historisch uittreksel\n".
                        "• Uittreksel met burgerlijke staat\n\n".
                        '💡 **Tip:** Online is sneller en goedkoper!',
            'action_button' => [
                'text' => 'Online Aanvragen',
                'url' => '/uittreksel/aanvragen',
            ],
            'quick_replies' => ['Paspoort info', 'Contact burgerzaken', 'Verhuizen melden'],
        ];
    }

    private function getParkingInfo(): array
    {
        return [
            'type' => 'parking_info',
            'message' => "Parkeren - Complete Informatie 🚗\n\n".
                        "**💰 PARKEERTARIEVEN 2025:**\n".
                        "• **Centrum (rood):** €2,50 per uur\n".
                        "• **Middengebied (geel):** €1,50 per uur\n".
                        "• **Buitengebied (groen):** €1,00 per uur\n".
                        "• **Blauwe zone:** Gratis, max 2 uur met parkeerschijf\n".
                        "• **Avond (19:00-09:00):** Gratis\n".
                        "• **Weekend:** Centrum €1,50/uur, rest gratis\n\n".
                        "**🎫 PARKEERVERGUNNINGEN:**\n".
                        "• **Bewonersvergunning:** €120 per jaar\n".
                        "• **Bezoekersvergunning:** €3 per dag (max 30 dagen)\n".
                        "• **Bedrijfsvergunning:** €250 per jaar\n".
                        "• **Thuiszorg vergunning:** €50 per jaar\n".
                        "• **Gehandicapten parkeerkaart:** Gratis\n\n".
                        "**📱 BETAALMOGELIJKHEDEN:**\n".
                        "• ParkeerApp (iOS/Android)\n".
                        "• SMS naar 3210 (€0,35 kosten)\n".
                        "• Parkeerautomaat (pin/contant)\n".
                        "• Online via gemeente.nl\n\n".
                        "**🕒 PARKEERTIJDEN:**\n".
                        "• **Maandag-Vrijdag:** 09:00-19:00\n".
                        "• **Zaterdag:** 09:00-18:00\n".
                        "• **Zondag:** Gratis (behalve centrum 12:00-18:00)\n".
                        "• **Feestdagen:** Gratis\n\n".
                        "**🔵 BLAUWE ZONE REGELS:**\n".
                        "• Parkeerschijf verplicht\n".
                        "• Maximum 2 uur\n".
                        "• Maandag-Zaterdag 09:00-18:00\n".
                        "• Aankomsttijd op schijf zetten\n".
                        "• Duidelijk zichtbaar achter voorruit\n\n".
                        "**🏥 SPECIALE REGELINGEN:**\n".
                        "• **Ziekenhuis:** Eerste uur gratis\n".
                        "• **Stations:** P+R €1 per dag\n".
                        "• **Evenementen:** Aangepaste tarieven\n".
                        "• **Marktdagen:** Centrum afgesloten\n\n".
                        "**📋 VERGUNNING AANVRAGEN:**\n".
                        "**Bewoners benodigde documenten:**\n".
                        "• Uittreksel GBA (adres bewijs)\n".
                        "• Kentekenbewijs\n".
                        "• Geldig identiteitsbewijs\n".
                        "• Bankafschrift (betaling)\n\n".
                        "**💳 GEHANDICAPTENPARKEREN:**\n".
                        "• Europese parkeerkaart\n".
                        "• Gratis parkeren in betaalzones\n".
                        "• Aanvragen via gemeente\n".
                        "• Medische keuring vereist\n\n".
                        "**⚠️ BOETES & HANDHAVING:**\n".
                        "• Geen/verkeerd parkeerticket: €60\n".
                        "• Overschrijden parkeertijd: €60\n".
                        "• Parkeren in blauwe zone zonder schijf: €60\n".
                        "• Gehandicaptenplek onterecht: €390\n".
                        "• Bezwaar mogelijk binnen 6 weken\n\n".
                        "**🛣️ SPECIALE PARKEERLOCATIES:**\n".
                        "• **P1 Centrum:** 200 plaatsen, €2,50/uur\n".
                        "• **P2 Station:** 150 plaatsen, €1,50/uur\n".
                        "• **P3 Winkelcentrum:** 300 plaatsen, 2u gratis\n".
                        "• **P+R Transferium:** €1/dag + OV gratis\n\n".
                        "**📞 CONTACT PARKEREN:**\n".
                        '• Algemeen: '.$this->knowledgeBase['contact']['phone']."\n".
                        "• Handhaving: handhaving@gemeente.nl\n".
                        '• Vergunningen: parkeren@gemeente.nl',
            'action_button' => [
                'text' => 'Vergunning Aanvragen',
                'url' => '/parkeren/vergunning',
            ],
            'quick_replies' => ['ParkeerApp download', 'Boete bezwaar', 'Blauwe zone', 'Gehandicaptenparkeren'],
        ];
    }

    private function getWasteInfo(): array
    {
        return [
            'type' => 'waste_info',
            'message' => "Afval & Recycling - Complete Gids 🗑️\n\n".
                        "**📅 OPHAALSCHEMA 2025:**\n".
                        "• **Restafval (grijze bak):** Elke dinsdag\n".
                        "• **GFT (groene bak):** Elke vrijdag\n".
                        "• **PMD (oranje bak):** Om de week woensdag (even weken)\n".
                        "• **Papier (blauwe bak):** 1e zaterdag van de maand\n".
                        "• **Glas:** Glasbakken in de wijk\n\n".
                        "**🗑️ WAT HOORT WAAR:**\n".
                        "**Restafval (grijs):**\n".
                        "• Luiers, kattenbak, stofzuigerzakken\n".
                        "• Kapotte speelgoed, oude schoenen\n".
                        "• Asbakinhoud, kauwgom\n".
                        "• Medicijnen (in verpakking)\n\n".
                        "**GFT (groen):**\n".
                        "• Groente- en fruitresten\n".
                        "• Koffiedik, theezakjes\n".
                        "• Tuinafval, bloemen\n".
                        "• Schillen, etensresten\n".
                        "• GEEN plastic zakken!\n\n".
                        "**PMD (oranje):**\n".
                        "• Plastic flessen en bakjes\n".
                        "• Metalen verpakkingen\n".
                        "• Drankkartons (melk, sap)\n".
                        "• Lege spuitbussen\n\n".
                        "**Papier (blauw):**\n".
                        "• Kranten, tijdschriften\n".
                        "• Karton (klein)\n".
                        "• Folders, boeken\n".
                        "• GEEN plastic folie!\n\n".
                        "**🏠 EXTRA AFVALSTROMEN:**\n".
                        "• **Textiel:** Textielcontainers in de wijk\n".
                        "• **Klein chemisch afval:** Chemokar (1x per maand)\n".
                        "• **Elektronica:** Gratis inleveren bij winkels\n".
                        "• **Batterijen:** Inzamelpunten bij winkels\n\n".
                        "**📦 GROF VUIL:**\n".
                        "• **Kosten:** €25 per m³\n".
                        "• **Aanmelden:** Online of telefonisch\n".
                        "• **Wanneer:** Minimaal 1 week van tevoren\n".
                        "• **Buiten zetten:** Avond voor ophaling\n".
                        "• **Maximum:** 5 m³ per keer\n".
                        "• **Wat:** Meubels, matrassen, tapijt, grote apparaten\n\n".
                        "**♻️ MILIEUPARK:**\n".
                        '• **Adres:** '.$this->knowledgeBase['locations']['milieupark']['address']."\n".
                        '• **Openingstijden:** '.$this->knowledgeBase['locations']['milieupark']['hours']."\n".
                        '• **Kosten:** '.$this->knowledgeBase['locations']['milieupark']['costs']."\n".
                        "• **Wat kunt u kwijt:**\n".
                        "  - Grof tuinafval\n".
                        "  - Puin, tegels, stenen\n".
                        "  - Hout, metaal\n".
                        "  - Elektrische apparaten\n".
                        "  - Chemisch afval (verf, oplosmiddelen)\n".
                        "  - Autobanden (max 4 per bezoek)\n\n".
                        "**📱 AFVAL APPS & SERVICES:**\n".
                        "• **AfvalApp:** Persoonlijke ophaalkalender\n".
                        "• **Meldapp:** Volle containers melden\n".
                        "• **SMS-service:** Herinneringen ophaaldag\n".
                        "• **Online:** Grof vuil aanmelden\n\n".
                        "**⚠️ BELANGRIJKE REGELS:**\n".
                        "• Container buiten zetten: 07:00 op ophaaldag\n".
                        "• Na leging: Container binnen 3 uur terug\n".
                        "• Deksels dicht voor voorkomen vervuiling\n".
                        "• Niet overvullen (deksel moet dicht)\n".
                        "• Zakken naast container: €90 boete\n\n".
                        "**🎄 SPECIALE ACTIES:**\n".
                        "• **Kerstbomen:** Gratis ophaling 2e week januari\n".
                        "• **Tuinafval:** Extra inzameling voorjaar\n".
                        "• **Textielactie:** 2x per jaar huis-aan-huis\n\n".
                        "**💡 AFVAL VERMINDEREN:**\n".
                        "• Reparatie Café: 1e zaterdag van de maand\n".
                        "• Kringloopwinkel: Marktplein 15\n".
                        "• Composteren: Subsidie €50 voor compostvat\n".
                        "• Luierservice: Wasbare luiers huren\n\n".
                        "**📞 CONTACT AFVAL:**\n".
                        '• Algemeen: '.$this->knowledgeBase['contact']['phone']."\n".
                        "• Grof vuil: grofvuil@gemeente.nl\n".
                        "• Milieupark: milieupark@gemeente.nl\n".
                        '• Spoed (volle containers): 24/7 meldlijn',
            'action_button' => [
                'text' => 'Grof Vuil Aanmelden',
                'url' => '/afval/grofvuil',
            ],
            'quick_replies' => ['Ophaalkalender', 'Wat in welke bak?', 'Milieupark info', 'AfvalApp download'],
        ];
    }

    private function getTaxInfo(): array
    {
        return [
            'type' => 'tax_info',
            'message' => "Gemeentebelastingen - Volledige Informatie 💰\n\n".
                        "**💵 TARIEVEN 2025:**\n".
                        "• **OZB eigenaar:** 0,1234% van WOZ-waarde\n".
                        "• **OZB gebruiker:** 0,0987% van WOZ-waarde\n".
                        "• **Rioolheffing:** €156 per jaar per aansluiting\n".
                        "• **Afvalstoffenheffing:** €234 per jaar per huishouden\n".
                        "• **Hondenbelasting:** €85 per hond per jaar\n".
                        "• **Precariobelasting:** €3,50 per m² terras\n\n".
                        "**📅 BETALINGSSCHEMA:**\n".
                        "• **Automatische incasso:** 15e van de maand\n".
                        "• **Termijnen:** 8 keer per jaar (feb-sept)\n".
                        "• **Jaarlijkse aanslag:** Begin februari\n".
                        "• **Nieuwe eigenaar:** Pro rata vanaf overdracht\n\n".
                        "**🏠 ONROERENDE ZAAK BELASTING (OZB):**\n".
                        "**Voor eigenaren:**\n".
                        "• Alle woningen en bedrijfspanden\n".
                        "• Berekening: WOZ-waarde × tarief\n".
                        "• Voorbeeld: €300.000 WOZ = €370 per jaar\n\n".
                        "**Voor gebruikers (huurders):**\n".
                        "• Alleen niet-woningen (kantoren, winkels)\n".
                        "• Vaak doorberekend in huurprijs\n\n".
                        "**💧 RIOOLHEFFING:**\n".
                        "• Voor alle aangesloten panden\n".
                        "• Onderhoud riolering en waterzuivering\n".
                        "• Tarief per aansluiting, niet per persoon\n".
                        "• Bij meerdere aansluitingen: per aansluiting betalen\n\n".
                        "**🗑️ AFVALSTOFFENHEFFING:**\n".
                        "• Voor alle huishoudens\n".
                        "• Inzameling en verwerking huishoudelijk afval\n".
                        "• Eenpersoonshuishouden: korting mogelijk\n".
                        "• Composteerkorting: €25 per jaar\n\n".
                        "**🐕 HONDENBELASTING:**\n".
                        "• Per hond aanmelden binnen 2 weken\n".
                        "• Eerste hond: €85 per jaar\n".
                        "• Tweede hond: €120 per jaar\n".
                        "• Geleidehonden: vrijgesteld\n".
                        "• Boete bij niet aanmelden: €250\n\n".
                        "**💳 BETAALMOGELIJKHEDEN:**\n".
                        "• **Automatische incasso:** (aanbevolen)\n".
                        "• **Online:** gemeente.nl/belastingen\n".
                        "• **Bankieren:** Met acceptgironummer\n".
                        "• **Balie gemeentehuis:** Pin of contant\n".
                        "• **Betalingsregeling:** Mogelijk bij problemen\n\n".
                        "**📉 KWIJTSCHELDING:**\n".
                        "**Voorwaarden:**\n".
                        "• Inkomen tot 110% van bijstandsnorm\n".
                        "• Vermogen onder €1.240 (alleenstaand)\n".
                        "• Vermogen onder €1.785 (gezin)\n".
                        "• Eigen woning toegestaan\n\n".
                        "**Mogelijke kwijtschelding:**\n".
                        "• Afvalstoffenheffing: 100%\n".
                        "• Rioolheffing: 100%\n".
                        "• OZB: Gedeeltelijk (afhankelijk van inkomen)\n\n".
                        "**⚖️ BEZWAAR & BEROEP:**\n".
                        "• **Bezwaar:** Binnen 6 weken na aanslag\n".
                        "• **Gratis:** Geen kosten voor bezwaarprocedure\n".
                        "• **WOZ-bezwaar:** Vaak meest voorkomend\n".
                        "• **Beroep:** Bij rechtbank binnen 6 weken\n\n".
                        "**🏘️ WOZ-WAARDE INFORMATIE:**\n".
                        "• **Peildatum:** 1 januari van het jaar ervoor\n".
                        "• **Herwaardering:** Jaarlijks\n".
                        "• **Bezwaar mogelijk:** Bij onjuiste waardeschatting\n".
                        "• **Vergelijken:** Met vergelijkbare woningen\n\n".
                        "**💰 BETALINGSREGELING:**\n".
                        "• **Aanvragen:** Voor aanmaningsdatum\n".
                        "• **Voorwaarden:** Reëel voorstel doen\n".
                        "• **Rente:** 4% per jaar\n".
                        "• **Maximum looptijd:** 24 maanden\n\n".
                        "**📞 CONTACT BELASTINGEN:**\n".
                        '• **Algemeen:** '.$this->knowledgeBase['contact']['phone']."\n".
                        "• **Email:** belastingen@gemeente.nl\n".
                        "• **WOZ-vragen:** woz@gemeente.nl\n".
                        "• **Kwijtschelding:** kwijtschelding@gemeente.nl\n".
                        "• **Spreekuur:** Dinsdag 13:30-16:30 (zonder afspraak)\n\n".
                        "**⏰ BELANGRIJKE TERMIJNEN:**\n".
                        "• Bezwaar aanslag: 6 weken\n".
                        "• Kwijtschelding aanvragen: Voor vervaldatum\n".
                        "• Hond aanmelden: Binnen 2 weken\n".
                        '• Verhuizing doorgeven: Voor verhuisdatum',
            'action_button' => [
                'text' => 'Online Betalen',
                'url' => '/belastingen/betalen',
            ],
            'quick_replies' => ['Kwijtschelding', 'WOZ bezwaar', 'Betalingsregeling', 'Hond aanmelden'],
        ];
    }

    private function getLocationInfo(): array
    {
        return [
            'type' => 'location_info',
            'message' => "Gemeentehuis locatie 📍\n\n".
                        '**Adres:** '.$this->knowledgeBase['locations']['gemeentehuis']['address']."\n".
                        '**Parkeren:** '.$this->knowledgeBase['locations']['gemeentehuis']['parking']."\n".
                        '**Toegankelijkheid:** '.$this->knowledgeBase['locations']['gemeentehuis']['accessibility']."\n\n".
                        '**Openingstijden:** '.$this->knowledgeBase['contact']['hours'],
            'action_button' => [
                'text' => 'Route Plannen',
                'url' => 'https://maps.google.com/?q='.urlencode($this->knowledgeBase['locations']['gemeentehuis']['address']),
            ],
            'quick_replies' => ['Openingstijden', 'Parkeren info', 'Contact'],
        ];
    }

    private function getEventsInfo(): array
    {
        return [
            'type' => 'events_info',
            'message' => "Aankomende evenementen 🎉\n\n".
                        "**Binnenkort:**\n".
                        '• '.implode("\n• ", $this->knowledgeBase['events'])."\n\n".
                        '💡 **Tip:** Volg ons op sociale media voor updates!',
            'quick_replies' => ['Sinterklaas info', 'Kerstmarkt', 'Koningsdag', 'Alle evenementen'],
        ];
    }

    private function getAppsInfo(): array
    {
        return [
            'type' => 'apps_info',
            'message' => "Gemeente Apps 📱\n\n".
                        "**Beschikbare apps:**\n".
                        "📋 **MeldApp** - Meldingen maken met foto en locatie\n".
                        "🗑️ **AfvalApp** - Ophaaldata en grof vuil afspraken\n".
                        "🚗 **ParkeerApp** - Betalen en tijd verlengen\n\n".
                        "**Download:** App Store / Google Play\n".
                        "**Gratis:** Alle apps zijn gratis te gebruiken\n\n".
                        '💡 **Online portal:** Mijn Gemeente voor DigiD gebruikers',
            'quick_replies' => ['MeldApp info', 'AfvalApp', 'ParkeerApp', 'Mijn Gemeente'],
        ];
    }

    private function getEmergencyInfo(): array
    {
        return [
            'type' => 'emergency_info',
            'message' => "Noodsituaties & Calamiteiten 🚨\n\n".
                        "**Spoedeisende hulp:** 112\n".
                        "**Politie niet-spoed:** 0900-8844\n".
                        '**Gemeente calamiteiten:** '.$this->knowledgeBase['contact']['phone']."\n\n".
                        "**Bij wateroverlast:**\n".
                        '1. Bel '.$this->knowledgeBase['contact']['phone']."\n".
                        "2. Maak foto's voor schade\n".
                        "3. Bewaar bonnen\n\n".
                        '**Altijd eerst 112 bij direct gevaar!**',
            'quick_replies' => ['Wateroverlast melden', 'Boom omgevallen', 'Stroomstoring'],
        ];
    }

    private function getGeneralHelp(): array
    {
        return [
            'type' => 'general_help',
            'message' => "Gemeente Assistent - Alle Informatie 🏛️\n\n".
                        "**Ik ben uw digitale gemeente assistent en kan u helpen met ALLE gemeente diensten!**\n\n".
                        "**📋 KLACHTEN & MELDINGEN**\n".
                        "• Nieuwe klacht indienen met foto's\n".
                        "• Status realtime opvolgen\n".
                        "• Klacht-ID terugvinden\n".
                        "• Verschillende categorieën (wegen, groen, overlast)\n\n".
                        "**👤 BURGERZAKEN - COMPLETE SERVICE**\n".
                        "• Paspoort €76,41 | ID €64,85 | Spoed +€51,50\n".
                        "• Uittreksels online €14,20 (24/7)\n".
                        "• Verhuizing gratis online melden\n".
                        "• Trouwen €500 + planning\n".
                        "• Afspraken maken verplicht\n\n".
                        "**🚗 VERKEER & PARKEREN - ALLES OP EEN RIJ**\n".
                        "• Centrum €2,50/u | Overig €1,50/u\n".
                        "• Bewonersvergunning €120/jaar\n".
                        "• Blauwe zone gratis 2u met schijf\n".
                        "• ParkeerApp voor makkelijk betalen\n".
                        "• Boetes €60 | Bezwaar binnen 6 weken\n\n".
                        "**🗑️ AFVAL & RECYCLING - COMPLETE GIDS**\n".
                        "• Restafval: dinsdag | GFT: vrijdag\n".
                        "• PMD: om de week woensdag\n".
                        "• Grof vuil €25/m³ op afspraak\n".
                        "• Milieupark: Di/Do/Za - eerste 2m³ gratis\n".
                        "• AfvalApp voor persoonlijke kalender\n\n".
                        "**💰 BELASTINGEN - ALLE TARIEVEN**\n".
                        "• OZB 0,1234% WOZ | Riool €156 | Afval €234\n".
                        "• Automatische incasso 15e van maand\n".
                        "• Kwijtschelding bij laag inkomen\n".
                        "• WOZ bezwaar binnen 6 weken\n".
                        "• Betalingsregeling mogelijk\n\n".
                        "**🤝 SOCIALE ZAKEN - VOLLEDIGE ONDERSTEUNING**\n".
                        "• Bijstand alleenstaand €1.489/maand\n".
                        "• Re-integratie & sollicitatieondersteuning\n".
                        "• Schuldhulpverlening gratis\n".
                        "• Minimavoorzieningen\n".
                        "• Budgetbeheer en coaching\n\n".
                        "**🎉 EVENEMENTEN & CULTUUR**\n".
                        "• Sinterklaas 16 nov | Kerstmarkt 14-15 dec\n".
                        "• Koningsdag 27 april | Nieuwjaarsreceptie 12 jan\n".
                        "• Evenementenvergunning aanvragen\n".
                        "• Culturele activiteiten agenda\n\n".
                        "**📱 DIGITALE SERVICES - 24/7 BESCHIKBAAR**\n".
                        "• Mijn Gemeente portal (DigiD login)\n".
                        "• MeldApp | AfvalApp | ParkeerApp\n".
                        "• Online verhuizen & uittreksel\n".
                        "• WhatsApp contact 06-12345678\n\n".
                        "**🚨 NOODSITUATIES & CALAMITEITEN**\n".
                        "• Spoed: 112 | Politie: 0900-8844\n".
                        '• Gemeente calamiteiten: '.$this->knowledgeBase['contact']['phone']."\n".
                        "• Wateroverlast | Stormschade\n".
                        "• 24/7 meldlijn beschikbaar\n\n".
                        "**📞 SPECIALISTISCHE HULP**\n".
                        "• Burgerzaken: 14 020 tst 1\n".
                        "• Belastingen: 14 020 tst 2\n".
                        "• Sociale zaken: 14 020 tst 3\n".
                        "• Vergunningen: 14 020 tst 4\n\n".
                        "**💡 PROBEER DEZE VOORBEELDEN:**\n".
                        "• \"Paspoort aanvragen\"\n".
                        "• \"Wie heeft recht op bijstand\"\n".
                        "• \"Afval ophaaldata\"\n".
                        "• \"Parkeervergunning kosten\"\n".
                        "• \"Hoe verhuizing melden\"\n".
                        "• \"Belastingen kwijtschelding\"\n".
                        "• \"Klacht indienen\"\n\n".
                        "**🎯 MIJN SPECIALITEITEN:**\n".
                        "✅ Exacte kosten en tarieven\n".
                        "✅ Stap-voor-stap procedures\n".
                        "✅ Benodigde documenten\n".
                        "✅ Openingstijden en contactinfo\n".
                        "✅ Online services en apps\n".
                        "✅ Deadlines en termijnen\n".
                        "✅ Rechten en plichten\n".
                        "✅ Bezwaar en beroepsmogelijkheden\n\n".
                        '**Stel gerust uw vraag - ik heb alle gemeente informatie paraat!**',
            'quick_replies' => [
                'Burgerzaken info',
                'Belastingen uitleg',
                'Afval kalender',
                'Bijstand informatie',
                'Parkeren tarieven',
                'Contact opnemen',
            ],
        ];
    }

    // === ADDITIONAL RESPONSE FUNCTIONS (placeholders for future expansion) ===

    private function getMovingInfo(): array
    {
        return [
            'type' => 'moving_info',
            'message' => "Verhuizing - Volledige Checklist 📦\n\n".
                        "**📅 WANNEER AANMELDEN:**\n".
                        "• **Uiterlijk:** 5 werkdagen vóór verhuisdatum\n".
                        "• **Liefst:** 1-2 weken van tevoren\n".
                        "• **Te laat:** Mogelijk €325 boete\n".
                        "• **24/7 online:** gemeente.nl/verhuizen\n\n".
                        "**💰 KOSTEN:**\n".
                        "• **Online verhuizen:** Gratis\n".
                        "• **Balie/telefoon:** €15,50\n".
                        "• **Te laat aanmelden:** €325 boete\n".
                        "• **Nieuwe documenten:** Zie paspoort/ID kosten\n\n".
                        "**📋 BINNEN NEDERLAND VERHUIZEN:**\n".
                        "**Benodigde gegevens:**\n".
                        "• BSN van alle verhuizers\n".
                        "• Nieuw adres (postcode + huisnummer)\n".
                        "• Verhuisdatum\n".
                        "• Vorig adres (indien van buitenland)\n\n".
                        "**👨‍👩‍👧‍👦 GEZIN MET KINDEREN:**\n".
                        "• Alle gezinsleden tegelijk aanmelden\n".
                        "• Kinderen onder 18: Ouderlijk gezag meeverhuist\n".
                        "• Eenouder gezin: Toestemmingsverklaring andere ouder\n\n".
                        "**🌍 VERHUIZEN VANUIT BUITENLAND:**\n".
                        "**Extra documenten:**\n".
                        "• Geldig paspoort of ID\n".
                        "• Uittreksel buitenlandse basisregistratie\n".
                        "• Apostille of legalisatie\n".
                        "• Vertaling in het Nederlands\n".
                        "• Bewijs adres (huurcontract/koopakte)\n\n".
                        "**🏢 WAT REGELEN BIJ VERHUIZING:**\n".
                        "**Gemeente gerelateerd:**\n".
                        "• GBA/BRP wijziging (verplicht)\n".
                        "• Nieuwe documenten aanvragen\n".
                        "• Parkeervergunning wijzigen\n".
                        "• Afvalkalender nieuwe adres\n".
                        "• Kiesrecht/stembureauvindplaats\n\n".
                        "**Overige instellingen:**\n".
                        "• Energieleveranciers\n".
                        "• Water & internet\n".
                        "• Ziektekostenverzekering\n".
                        "• Bank & creditcard\n".
                        "• Werkgever & belastingdienst\n".
                        "• School kinderen\n".
                        "• Huisarts & tandarts\n\n".
                        "**📞 HANDIG OM TE WETEN:**\n".
                        "• **Verhuiskaart:** Online downloaden na aanmelding\n".
                        "• **Post doorsturen:** Regel bij PostNL\n".
                        "• **Kiesrecht:** Automatisch overgedragen\n".
                        "• **Uitschrijving:** Automatisch bij nieuwe inschrijving\n\n".
                        "**� DIGITALE HULPMIDDELEN:**\n".
                        "• **Verhuisplanner:** gemeente.nl/verhuisplanner\n".
                        "• **Checklist:** Download PDF met alle stappen\n".
                        "• **MijnOverheid:** Automatische doorgifte mogelijk\n\n".
                        "**🚨 SPECIALE SITUATIES:**\n".
                        "**Scheiding/relatiebreuk:**\n".
                        "• Beide ex-partners moeten zich apart aanmelden\n".
                        "• Kinderen bij één ouder ingeschreven\n".
                        "• Mogelijk mediation bij geschillen\n\n".
                        "**Studenten:**\n".
                        "• Tijdelijke inschrijving mogelijk\n".
                        "• Studieadres vs thuisadres\n".
                        "• Uitschrijving bij einde studie\n\n".
                        "**⚠️ GEVOLGEN NIET/TE LAAT AANMELDEN:**\n".
                        "• €325 boete van gemeente\n".
                        "• Problemen met uitkeringen\n".
                        "• Niet kunnen stemmen\n".
                        "• Problemen met documenten\n".
                        "• Moeilijkheden met zorgverzekering\n\n".
                        "**📞 CONTACT VERHUIZEN:**\n".
                        "• **Online:** 24/7 beschikbaar\n".
                        '• **Telefoon:** '.$this->knowledgeBase['contact']['phone']."\n".
                        '• **Balie:** '.$this->knowledgeBase['services']['burgerzaken']['hours']."\n".
                        "• **Afspraak:** Voor complexe situaties\n\n".
                        "**✅ NA DE VERHUIZING:**\n".
                        "• Controleer binnen 1 week of alles correct staat\n".
                        "• Download verhuiskaart als bewijs\n".
                        '• Vraag nieuwe documenten aan indien nodig',
            'action_button' => [
                'text' => 'Online Verhuizen',
                'url' => '/verhuizen',
            ],
            'quick_replies' => ['Verhuischecklist', 'Vanuit buitenland', 'Kosten overzicht', 'Contact burgerzaken'],
        ];
    }

    private function getMarriageInfo(): array
    {
        return [
            'type' => 'marriage_info',
            'message' => "Trouwen in de gemeente 💒\n\n".
                        "**Reservering:** Minimaal 6 weken van tevoren\n".
                        "**Kosten:** €500 voor ceremonie + €50 per extra uur\n".
                        "**Locaties:** Gemeentehuis of externe locatie\n\n".
                        "**Vereisten:**\n".
                        "• Geldig identiteitsbewijs\n".
                        "• Uittreksel GBA (max 6 maanden oud)\n".
                        "• Beiden 18 jaar of ouder\n\n".
                        '💡 Contact voor uitgebreide informatie!',
            'quick_replies' => ['Trouwlocaties', 'Kosten overzicht', 'Contact burgerzaken'],
        ];
    }

    private function getTrafficInfo(): array
    {
        return [
            'type' => 'traffic_info',
            'message' => "Verkeer & Ontheffingen 🚦\n\n".
                        "**Verkeersmaatregelen:** Zie verkeer.gemeente.nl\n".
                        "**Ontheffingen:** Voor vrachtwagens, evenementen\n".
                        "**Kosten:** €50-200 afhankelijk van type\n\n".
                        "**Aanvragen:** Minimaal 2 weken van tevoren\n\n".
                        '💡 Voor meer info: verkeer@gemeente.nl',
            'quick_replies' => ['Parkeren', 'Evenement ontheffing', 'Contact verkeer'],
        ];
    }

    private function getRecyclingCenterInfo(): array
    {
        return [
            'type' => 'recycling_center_info',
            'message' => "Milieupark 🌱\n\n".
                        '**Adres:** '.$this->knowledgeBase['locations']['milieupark']['address']."\n".
                        '**Openingstijden:** '.$this->knowledgeBase['locations']['milieupark']['hours']."\n".
                        '**Kosten:** '.$this->knowledgeBase['locations']['milieupark']['costs']."\n\n".
                        "**Wat kunt u kwijt:**\n".
                        "• Grof vuil • Elektrische apparaten\n".
                        "• Chemisch afval • Tuinafval\n".
                        "• Metaal • Hout • Glas\n\n".
                        '💡 **Tip:** Neem een identiteitsbewijs mee!',
            'quick_replies' => ['Route milieupark', 'Wat mag er in?', 'Grof vuil thuis'],
        ];
    }

    private function getTaxReliefInfo(): array
    {
        return [
            'type' => 'tax_relief_info',
            'message' => "Kwijtschelding belastingen 💸\n\n".
                        "**Voor wie:** Mensen met laag inkomen\n".
                        "**Aanvragen:** Online of bij de balie\n".
                        "**Beoordeling:** Op basis van inkomen en vermogen\n\n".
                        "**Wat meenemen:**\n".
                        "• Inkomensgegevens\n".
                        "• Bankafschriften\n".
                        "• Huur-/hypotheekgegevens\n\n".
                        '💡 **Geen schaamte:** Iedereen heeft recht op ondersteuning!',
            'action_button' => [
                'text' => 'Aanvragen',
                'url' => '/kwijtschelding',
            ],
            'quick_replies' => ['Voorwaarden', 'Benodigde documenten', 'Contact belastingen'],
        ];
    }

    private function getSocialBenefitsInfo(): array
    {
        return [
            'type' => 'social_benefits_info',
            'message' => "Bijstand & Uitkeringen - Volledige Informatie 🤝\n\n".
                        "**💰 WIE HEEFT RECHT OP BIJSTAND:**\n".
                        "• 18 jaar of ouder\n".
                        "• Legaal in Nederland\n".
                        "• Geen of onvoldoende inkomen\n".
                        "• Vermogen onder €6.675 (alleenstaand) / €13.350 (gezin)\n".
                        "• Eigen woning mag tot €254.000 waard zijn\n\n".
                        "**💵 BEDRAGEN 2025:**\n".
                        "• Alleenstaand: €1.489,- per maand\n".
                        "• Alleenstaande ouder: €1.915,- per maand\n".
                        "• Gehuwd/samenwonend: €2.128,- per maand (samen)\n".
                        "• 21-26 jaar: €1.191,- per maand\n".
                        "• 18-20 jaar bij ouders: €298,- per maand\n\n".
                        "**📋 BENODIGDE DOCUMENTEN:**\n".
                        "• Geldig identiteitsbewijs\n".
                        "• Bankafschriften (3 maanden)\n".
                        "• Huurcontract of hypotheekgegevens\n".
                        "• Inkomensgegevens partner\n".
                        "• Uittreksel GBA\n".
                        "• Eventueel: arbeidscontract, ontslagbrief\n\n".
                        "**⚖️ RECHTEN & PLICHTEN:**\n".
                        "**Rechten:**\n".
                        "• Maandelijkse uitkering\n".
                        "• Zorgtoeslag mogelijk\n".
                        "• Huurtoeslag mogelijk\n".
                        "• Kinderopvangtoeslag mogelijk\n".
                        "• Kindgebonden budget\n".
                        "• Minimavoorzieningen\n\n".
                        "**Plichten:**\n".
                        "• Sollicitatieplicht (4 banen per maand)\n".
                        "• Meewerken aan re-integratie\n".
                        "• Veranderingen direct melden\n".
                        "• Beschikbaar zijn voor werk\n\n".
                        "**🎯 RE-INTEGRATIE ONDERSTEUNING:**\n".
                        "• Sollicitatietraining\n".
                        "• CV hulp en LinkedIn profiel\n".
                        "• Werkervaring opdoen\n".
                        "• Cursussen en diploma's\n".
                        "• Coaching en begeleiding\n".
                        "• Vrijwilligerswerk\n\n".
                        "**💡 EXTRA ONDERSTEUNING:**\n".
                        "• Budgetbeheer en schuldhulp\n".
                        "• Voedselbank verwijzing\n".
                        "• Kleding en meubels via kringloop\n".
                        "• Gratis rechtsbijstand\n".
                        "• Energiearmoede hulp\n\n".
                        "**📞 CONTACT & AFSPRAAK:**\n".
                        '• Tel: '.$this->knowledgeBase['contact']['phone']."\n".
                        "• Email: socialewerk@gemeente.nl\n".
                        "• Locatie: Sociale Dienst, 1e verdieping\n".
                        "• Spoed: Binnen 24 uur contact\n".
                        "• Beslissing: Binnen 8 weken\n\n".
                        "**🚨 BELANGRIJKE TERMIJNEN:**\n".
                        "• Aanvraag zo snel mogelijk\n".
                        "• Terugwerkende kracht: max 4 weken\n".
                        "• Heronderzoek: jaarlijks\n".
                        '• Verandering melden: binnen 1 week',
            'action_button' => [
                'text' => 'Online Aanvragen',
                'url' => '/bijstand/aanvragen',
            ],
            'quick_replies' => ['Afspraak maken', 'Benodigde documenten', 'Re-integratie info', 'Minimavoorzieningen'],
        ];
    }

    private function getDebtHelpInfo(): array
    {
        return [
            'type' => 'debt_help_info',
            'message' => "Schuldhulpverlening 📊\n\n".
                        "**Gratis hulp:** Voor alle inwoners\n".
                        "**Eerste stap:** Telefonisch intake gesprek\n".
                        "**Vertrouwelijk:** Alle gesprekken zijn privé\n\n".
                        "**Wij helpen bij:**\n".
                        "• Schuldenregeling • Budgetbeheer\n".
                        "• Onderhandeling crediteuren\n".
                        "• Beschermingsbewind\n\n".
                        '📞 Bel direct: '.$this->knowledgeBase['contact']['phone'],
            'quick_replies' => ['Direct bellen', 'Afspraak maken', 'Budgetadvies'],
        ];
    }

    private function getMyMunicipalityInfo(): array
    {
        return [
            'type' => 'my_municipality_info',
            'message' => "Mijn Gemeente Portal 💻\n\n".
                        "**Inloggen:** Met DigiD\n".
                        "**24/7 beschikbaar:** Voor vele diensten\n\n".
                        "**Wat kunt u doen:**\n".
                        "• Uittreksels aanvragen\n".
                        "• Belastingen betalen\n".
                        "• Verhuizing doorgeven\n".
                        "• Klachten opvolgen\n".
                        "• Documenten downloaden\n\n".
                        '💡 **Veilig:** Beveiligd met DigiD!',
            'action_button' => [
                'text' => 'Naar Mijn Gemeente',
                'url' => '/mijn-gemeente',
            ],
            'quick_replies' => ['DigiD problemen', 'Wat kan ik online?', 'Contact'],
        ];
    }
}
