<x-guest-layout>
    <div class="min-h-screen bg-grad                            <input type="text"
                                   id="title"
                                   name="title"
                                   value="{{ old('title') }}"
                                   required
                                   maxlength="100"
                                   class="form-input"
                                   placeholder="Bijvoorbeeld: Kapotte straatlantaarn Hoofdstraat of Gat in fietspad Parkweg">br from-bl                            <select id="category"
                                    name="category"
                                    required
                                    class="form-select">0 via-indigo-50 to-purple-50 py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="text-center mb-12">
                <div class="inline-block mb-4">
                    <span class="text-6xl">📝</span>
                </div>
                <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-4">
                    Klacht of Melding Indienen
                </h1>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Help ons uw buurt beter te maken. Meld problemen in de openbare ruimte of met gemeentelijke diensten.
                </p>
            </div>

            <!-- Info boxes -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                <div class="bg-white p-6 rounded-xl border border-blue-100 shadow-lg hover:shadow-xl transition-shadow duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full mb-4 mx-auto">
                        <span class="text-3xl">⚡</span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2 text-center">Snelle Afhandeling</h3>
                    <p class="text-sm text-gray-600 text-center">Gemiddelde behandeltijd: 3-5 werkdagen</p>
                </div>
                <div class="bg-white p-6 rounded-xl border border-blue-100 shadow-lg hover:shadow-xl transition-shadow duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-full mb-4 mx-auto">
                        <span class="text-3xl">📧</span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2 text-center">Automatische Updates</h3>
                    <p class="text-sm text-gray-600 text-center">U ontvangt e-mail bij statuswijzigingen</p>
                </div>
                <div class="bg-white p-6 rounded-xl border border-blue-100 shadow-lg hover:shadow-xl transition-shadow duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-green-400 to-emerald-500 rounded-full mb-4 mx-auto">
                        <span class="text-3xl">🔒</span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2 text-center">Privacy Gegarandeerd</h3>
                    <p class="text-sm text-gray-600 text-center">Uw gegevens worden veilig behandeld</p>
                </div>
            </div>

            <!-- Form Container -->
            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
                <div class="p-8 md:p-10">

                    <form action="{{ route('complaint.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <!-- Title -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                Titel van de klacht * <span class="text-gray-500 text-xs">(Kort en duidelijk)</span>
                            </label>
                            <input type="text"
                                   id="title"
                                   name="title"
                                   value="{{ old('title') }}"
                                   required
                                   maxlength="100"
                                   class="form-input"
                                   placeholder="Bijvoorbeeld: Kapotte straatlantaarn Hoofdstraat of Gat in fietspad Parkweg">
                            @error('title')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                                Categorie * <span class="text-gray-500 text-xs">(Selecteer het type dienst)</span>
                            </label>
                            <select id="category"
                                    name="category"
                                    required
                                    class="form-input">
                                <option value="">🔽 Selecteer een categorie...</option>

                                <optgroup label="🚗 Wegen & Verkeer">
                                    <option value="wegen_onderhoud" {{ old('category') == 'wegen_onderhoud' ? 'selected' : '' }}>🛣️ Wegen - Gaten, barsten, verzakkingen</option>
                                    <option value="stoepen" {{ old('category') == 'stoepen' ? 'selected' : '' }}>🚶 Stoepen - Losse tegels, obstakels</option>
                                    <option value="fietspaden" {{ old('category') == 'fietspaden' ? 'selected' : '' }}>🚴 Fietspaden - Slecht onderhoud</option>
                                    <option value="verkeersborden" {{ old('category') == 'verkeersborden' ? 'selected' : '' }}>🚦 Verkeersborden - Beschadigd, ontbreekt</option>
                                    <option value="wegmarkeringen" {{ old('category') == 'wegmarkeringen' ? 'selected' : '' }}>〰️ Wegmarkeringen - Vervaagd, onduidelijk</option>
                                    <option value="parkeren" {{ old('category') == 'parkeren' ? 'selected' : '' }}>🅿️ Parkeren - Illegaal, verkeerd geparkeerd</option>
                                </optgroup>

                                <optgroup label="💡 Openbare Verlichting">
                                    <option value="straatverlichting" {{ old('category') == 'straatverlichting' ? 'selected' : '' }}>🔦 Straatverlichting - Kapot, defect</option>
                                    <option value="parkverlichting" {{ old('category') == 'parkverlichting' ? 'selected' : '' }}>🌳 Parkverlichting - Niet werkend</option>
                                    <option value="tunnel_verlichting" {{ old('category') == 'tunnel_verlichting' ? 'selected' : '' }}>🚇 Tunnelverlichting - Gevaarlijk donker</option>
                                </optgroup>

                                <optgroup label="♻️ Afval & Reiniging">
                                    <option value="afval_ophaling" {{ old('category') == 'afval_ophaling' ? 'selected' : '' }}>🗑️ Afvalophaaldienst - Niet opgehaald</option>
                                    <option value="afval_container" {{ old('category') == 'afval_container' ? 'selected' : '' }}>🚮 Container - Vol, beschadigd</option>
                                    <option value="zwerfvuil" {{ old('category') == 'zwerfvuil' ? 'selected' : '' }}>🗑️ Zwerfvuil - Rommel op straat</option>
                                    <option value="graffiti" {{ old('category') == 'graffiti' ? 'selected' : '' }}>🎨 Graffiti - Vandalisme</option>
                                    <option value="hondenpoep" {{ old('category') == 'hondenpoep' ? 'selected' : '' }}>🐕 Hondenpoep - Niet opgeruimd</option>
                                    <option value="straat_reiniging" {{ old('category') == 'straat_reiniging' ? 'selected' : '' }}>🧹 Straatvuil - Vuile straten</option>
                                </optgroup>

                                <optgroup label="🌳 Groen & Natuur">
                                    <option value="bomen" {{ old('category') == 'bomen' ? 'selected' : '' }}>🌲 Bomen - Gevaarlijk, ziek, omgevallen</option>
                                    <option value="onkruid" {{ old('category') == 'onkruid' ? 'selected' : '' }}>🌿 Onkruid - Woekert, overlast</option>
                                    <option value="parken" {{ old('category') == 'parken' ? 'selected' : '' }}>🏞️ Parken - Onderhoud nodig</option>
                                    <option value="speeltuinen" {{ old('category') == 'speeltuinen' ? 'selected' : '' }}>🛝 Speeltuinen - Kapot speeltoestel</option>
                                    <option value="groenstroken" {{ old('category') == 'groenstroken' ? 'selected' : '' }}>🌱 Groenstroken - Verwaarloosd</option>
                                </optgroup>

                                <optgroup label="💧 Water & Riolering">
                                    <option value="riool_verstopt" {{ old('category') == 'riool_verstopt' ? 'selected' : '' }}>🚰 Riool - Verstopt, stank</option>
                                    <option value="wateroverlast" {{ old('category') == 'wateroverlast' ? 'selected' : '' }}>💦 Wateroverlast - Overstroming</option>
                                    <option value="put_deksel" {{ old('category') == 'put_deksel' ? 'selected' : '' }}>⭕ Putdeksel - Kapot, ontbreekt</option>
                                    <option value="lekkage" {{ old('category') == 'lekkage' ? 'selected' : '' }}>💧 Lekkage - Waterleiding lek</option>
                                </optgroup>

                                <optgroup label="🏠 Openbare Ruimte & Gebouwen">
                                    <option value="straatmeubilair" {{ old('category') == 'straatmeubilair' ? 'selected' : '' }}>🪑 Straatmeubilair - Bankjes, prullenbakken</option>
                                    <option value="fietsenrekken" {{ old('category') == 'fietsenrekken' ? 'selected' : '' }}>🚲 Fietsenrekken - Kapot, vol</option>
                                    <option value="bushokje" {{ old('category') == 'bushokje' ? 'selected' : '' }}>🚏 Bushokje - Beschadigd, vies</option>
                                    <option value="openbaar_toiletten" {{ old('category') == 'openbaar_toiletten' ? 'selected' : '' }}>🚻 Openbare toiletten - Vies, defect</option>
                                    <option value="bruggen" {{ old('category') == 'bruggen' ? 'selected' : '' }}>🌉 Bruggen - Beschadigd</option>
                                    <option value="gebouwen" {{ old('category') == 'gebouwen' ? 'selected' : '' }}>🏛️ Gemeentelijke gebouwen - Onderhoud</option>
                                </optgroup>

                                <optgroup label="😤 Overlast">
                                    <option value="geluidsoverlast" {{ old('category') == 'geluidsoverlast' ? 'selected' : '' }}>🔊 Geluidsoverlast - Herrie, lawaai</option>
                                    <option value="stankoverlast" {{ old('category') == 'stankoverlast' ? 'selected' : '' }}>👃 Stankoverlast - Vieze lucht</option>
                                    <option value="zwerfkatten" {{ old('category') == 'zwerfkatten' ? 'selected' : '' }}>🐱 Zwerfkatten - Overlast dieren</option>
                                    <option value="ratten" {{ old('category') == 'ratten' ? 'selected' : '' }}>🐀 Ratten/ongedierte - Plaag</option>
                                    <option value="hanggroepen" {{ old('category') == 'hanggroepen' ? 'selected' : '' }}>👥 Hanggroepen - Jeugdoverlast</option>
                                    <option value="wildplassen" {{ old('category') == 'wildplassen' ? 'selected' : '' }}>🚽 Wildplassen - Openbare dronkenschap</option>
                                </optgroup>

                                <optgroup label="🚨 Veiligheid">
                                    <option value="verlichting_onveilig" {{ old('category') == 'verlichting_onveilig' ? 'selected' : '' }}>⚠️ Onveilige situatie - Geen verlichting</option>
                                    <option value="losliggende_kabels" {{ old('category') == 'losliggende_kabels' ? 'selected' : '' }}>⚡ Losliggende kabels - Gevaarlijk</option>
                                    <option value="glad_wegdek" {{ old('category') == 'glad_wegdek' ? 'selected' : '' }}>❄️ Glad wegdek - Sneeuw, ijs</option>
                                    <option value="gevaarlijk_object" {{ old('category') == 'gevaarlijk_object' ? 'selected' : '' }}>⛔ Gevaarlijk object - Risico</option>
                                </optgroup>

                                <optgroup label="📋 Administratie & Dienstverlening">
                                    <option value="documentenaanvraag" {{ old('category') == 'documentenaanvraag' ? 'selected' : '' }}>📄 Documenten - Paspoort, rijbewijs</option>
                                    <option value="vergunningen" {{ old('category') == 'vergunningen' ? 'selected' : '' }}>📝 Vergunningen - Bouw, evenementen</option>
                                    <option value="belastingen" {{ old('category') == 'belastingen' ? 'selected' : '' }}>💰 Belastingen - OZB, rioolheffing</option>
                                    <option value="subsidies" {{ old('category') == 'subsidies' ? 'selected' : '' }}>💵 Subsidies - Aanvragen</option>
                                </optgroup>

                                <optgroup label="🏗️ Bouw & Ontwikkeling">
                                    <option value="bouwoverlast" {{ old('category') == 'bouwoverlast' ? 'selected' : '' }}>🏗️ Bouwoverlast - Werkzaamheden</option>
                                    <option value="illegale_bouw" {{ old('category') == 'illegale_bouw' ? 'selected' : '' }}>🚧 Illegale bouw - Zonder vergunning</option>
                                    <option value="sloopwerkzaamheden" {{ old('category') == 'sloopwerkzaamheden' ? 'selected' : '' }}>💥 Sloop - Overlast, onveilig</option>
                                </optgroup>

                                <optgroup label="🎉 Evenementen">
                                    <option value="evenement_overlast" {{ old('category') == 'evenement_overlast' ? 'selected' : '' }}>🎊 Evenementenvergunning - Overlast</option>
                                    <option value="markt" {{ old('category') == 'markt' ? 'selected' : '' }}>🛒 Markt - Standplaats, klachten</option>
                                    <option value="terras" {{ old('category') == 'terras' ? 'selected' : '' }}>☕ Terras - Overlast horeca</option>
                                </optgroup>

                                <optgroup label="🔍 Anders">
                                    <option value="overig" {{ old('category') == 'overig' ? 'selected' : '' }}>📌 Overig - Past niet in andere categorieën</option>
                                </optgroup>
                            </select>
                            @error('category')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Priority -->
                        <div>
                            <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
                                Urgentie
                            </label>
                            <select id="priority"
                                    name="priority"
                                    class="form-input">
                                <option value="low" {{ old('priority', 'medium') == 'low' ? 'selected' : '' }}>Laag</option>
                                <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Normaal</option>
                                <option value="high" {{ old('priority', 'medium') == 'high' ? 'selected' : '' }}>Hoog</option>
                                <option value="urgent" {{ old('priority', 'medium') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                            </select>
                            @error('priority')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Reporter Phone -->
                        <div>
                            <label for="reporter_phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Telefoonnummer (optioneel)
                            </label>
                            <input type="tel"
                                   id="reporter_phone"
                                   name="reporter_phone"
                                   value="{{ old('reporter_phone') }}"
                                   class="form-input"
                                   placeholder="06-12345678">
                            @error('reporter_phone')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                Beschrijving * <span class="text-gray-500 text-xs">(Geef zoveel mogelijk details)</span>
                            </label>
                            <textarea id="description"
                                      name="description"
                                      rows="6"
                                      required
                                      class="form-textarea"
                                      placeholder="Beschrijf het probleem zo uitgebreid mogelijk:&#10;- Wat is er aan de hand?&#10;- Waar bevindt het zich precies?&#10;- Sinds wanneer is dit een probleem?&#10;- Is het gevaarlijk?&#10;&#10;Bijvoorbeeld: De straatlantaarn voor huisnummer 123 aan de Hoofdstraat werkt al een week niet meer. Dit zorgt voor een onveilige situatie 's avonds.">{{ old('description') }}</textarea>
                            <p class="text-xs text-gray-500 mt-1">💡 Tip: Hoe meer details u geeft, hoe sneller we u kunnen helpen!</p>
                            @error('description')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Location Section -->
                        <div x-data="locationPicker()" class="space-y-4">
                            <div class="bg-gradient-to-r from-blue-50 to-cyan-50 p-6 rounded-xl border-2 border-blue-200 shadow-md">
                                <div class="flex items-start gap-4">
                                    <div class="flex-shrink-0 w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="font-bold text-blue-900 mb-2 text-lg">📍 Locatie toevoegen (Aanbevolen)</h3>
                                        <p class="text-blue-800">Met een precieze locatie kunnen we uw melding sneller in behandeling nemen. Gebruik de knop "Gebruik mijn locatie" of vul handmatig het adres in.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <label class="block text-sm font-medium text-gray-700">
                                    Locatie (optioneel maar aanbevolen)
                                </label>
                                <button type="button"
                                        @click="getCurrentLocation()"
                                        :disabled="gettingLocation"
                                        class="inline-flex items-center px-4 py-2.5 border border-transparent text-sm font-semibold rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                                    <svg x-show="!gettingLocation" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <svg x-show="gettingLocation" class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span x-text="gettingLocation ? 'Locatie ophalen...' : 'Gebruik mijn locatie'"></span>
                                </button>
                            </div>

                            <!-- Location Inputs -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="lat" class="block text-sm font-medium text-gray-700 mb-1">
                                        Latitude
                                    </label>
                                    <input type="number"
                                           id="lat"
                                           name="lat"
                                           step="any"
                                           x-model="latitude"
                                           value="{{ old('lat') }}"
                                           class="form-input"
                                           placeholder="52.3676">
                                </div>
                                <div>
                                    <label for="lng" class="block text-sm font-medium text-gray-700 mb-1">
                                        Longitude
                                    </label>
                                    <input type="number"
                                           id="lng"
                                           name="lng"
                                           step="any"
                                           x-model="longitude"
                                           value="{{ old('lng') }}"
                                           class="form-input"
                                           placeholder="4.9041">
                                </div>
                            </div>

                            <!-- Location Address -->
                            <div>
                                <label for="location" class="block text-sm font-medium text-gray-700 mb-1">
                                    📍 Adres of beschrijving van de locatie
                                </label>
                                <input type="text"
                                       id="location"
                                       name="location"
                                       value="{{ old('location') }}"
                                       class="form-input"
                                       placeholder="Bijvoorbeeld: Damrak 1, Amsterdam of tegenover Albert Heijn bij het station">
                                <p class="text-xs text-gray-500 mt-1">💡 Geef zoveel mogelijk details: straatnaam, huisnummer, nabij welk gebouw, etc.</p>
                                @error('location')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Location Status Messages -->
                            <div x-show="locationError" class="bg-red-50 border border-red-200 rounded-md p-3">
                                <div class="flex">
                                    <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div class="ml-3">
                                        <p class="text-sm text-red-700" x-text="locationError"></p>
                                    </div>
                                </div>
                            </div>

                            <div x-show="locationSuccess" class="bg-green-50 border border-green-200 rounded-md p-3">
                                <div class="flex">
                                    <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <div class="ml-3">
                                        <p class="text-sm text-green-700">Locatie succesvol opgehaald!</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Mini Map Preview -->
                            <div x-show="latitude && longitude" class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Locatie Preview</label>
                                <div id="miniMap" class="w-full h-48 rounded-md border border-gray-300"></div>
                            </div>
                        </div>

                        <!-- Reporter Info -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="reporter_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Uw naam *
                                </label>
                                <input type="text"
                                       id="reporter_name"
                                       name="reporter_name"
                                       value="{{ old('reporter_name') }}"
                                       required
                                       class="form-input">
                                @error('reporter_name')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="reporter_email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Uw e-mailadres *
                                </label>
                                <input type="email"
                                       id="reporter_email"
                                       name="reporter_email"
                                       value="{{ old('reporter_email') }}"
                                       required
                                       class="form-input">
                                @error('reporter_email')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- File Attachments -->
                        <div x-data="fileUploader()" class="space-y-4">
                            <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-6 rounded-xl border-2 border-green-200 shadow-md">
                                <div class="flex items-start gap-4">
                                    <div class="flex-shrink-0 w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="font-bold text-green-900 mb-2 text-lg">📸 Foto's toevoegen (Aanbevolen)</h3>
                                        <p class="text-green-800">Een foto zegt meer dan duizend woorden! Upload foto's van het probleem zodat we beter kunnen begrijpen wat er aan de hand is.</p>
                                    </div>
                                </div>
                            </div>

                            <label class="block text-sm font-medium text-gray-700">
                                Foto's (optioneel maar zeer aanbevolen)
                            </label>

                            <!-- File Drop Zone -->
                            <div @click="$refs.fileInput.click()"
                                 @dragover.prevent
                                 @drop.prevent="handleDrop($event)"
                                 class="border-3 border-dashed border-gray-300 rounded-xl p-8 text-center cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition-all duration-200 shadow-sm hover:shadow-lg"
                                 :class="{ 'border-blue-500 bg-blue-50 shadow-lg': isDragging }">

                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>

                                <div class="mt-4">
                                    <p class="text-sm text-gray-600">
                                        <span class="font-medium text-blue-600">Klik om bestanden te selecteren</span> of sleep ze hierheen
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        PNG, JPG, GIF tot 10MB per bestand (max 5 bestanden)
                                    </p>
                                </div>
                            </div>

                            <!-- Hidden File Input -->
                            <input type="file"
                                   x-ref="fileInput"
                                   @change="handleFiles($event.target.files)"
                                   name="attachments[]"
                                   multiple
                                   accept="image/*,.pdf"
                                   class="hidden">

                            <!-- File Previews -->
                            <div x-show="files.length > 0" class="space-y-3">
                                <h4 class="text-sm font-medium text-gray-700">Geselecteerde bestanden:</h4>
                                <div class="space-y-2">
                                    <template x-for="(file, index) in files" :key="index">
                                        <div class="flex items-center justify-between bg-gray-50 p-3 rounded-lg">
                                            <div class="flex items-center space-x-3">
                                                <!-- Image Preview -->
                                                <div x-show="file.type.startsWith('image/')" class="w-12 h-12 rounded overflow-hidden bg-gray-200">
                                                    <img :src="file.preview" :alt="file.name" class="w-full h-full object-cover">
                                                </div>
                                                <!-- File Icon for non-images -->
                                                <div x-show="!file.type.startsWith('image/')" class="w-12 h-12 rounded bg-gray-200 flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                </div>

                                                <div>
                                                    <p class="text-sm font-medium text-gray-900" x-text="file.name"></p>
                                                    <p class="text-xs text-gray-500" x-text="formatFileSize(file.size)"></p>
                                                </div>
                                            </div>

                                            <button type="button"
                                                    @click="removeFile(index)"
                                                    class="text-red-600 hover:text-red-800 p-1">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Upload Progress -->
                            <div x-show="uploading" class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" :style="`width: ${uploadProgress}%`"></div>
                            </div>

                            <!-- Error Messages -->
                            <div x-show="errors.length > 0" class="space-y-1">
                                <template x-for="error in errors" :key="error">
                                    <p class="text-red-500 text-sm" x-text="error"></p>
                                </template>
                            </div>

                            @error('attachments')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-8 rounded-xl border-2 border-green-200 shadow-lg">
                            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-3">
                                        <span class="text-3xl">✅</span>
                                        <h3 class="text-xl font-bold text-gray-900">Klaar om in te dienen?</h3>
                                    </div>
                                    <p class="text-gray-700 mb-4">Controleer of alle gegevens correct zijn. Na het indienen ontvangt u een bevestiging met een uniek meldingsnummer.</p>
                                    <ul class="text-sm text-gray-600 space-y-2">
                                        <li class="flex items-center gap-2">
                                            <span class="text-green-600">✓</span>
                                            <span>U ontvangt een e-mail bevestiging</span>
                                        </li>
                                        <li class="flex items-center gap-2">
                                            <span class="text-green-600">✓</span>
                                            <span>Uw melding wordt zo snel mogelijk in behandeling genomen</span>
                                        </li>
                                        <li class="flex items-center gap-2">
                                            <span class="text-green-600">✓</span>
                                            <span>U kunt de status volgen met uw meldingsnummer</span>
                                        </li>
                                    </ul>
                                </div>
                                <button type="submit"
                                        class="btn btn-primary btn-lg w-full md:w-auto flex items-center justify-center gap-3 text-lg">
                                    <span class="text-2xl">📤</span>
                                    <span>Melding Indienen</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script>
    // Location Picker Alpine.js Component
    function locationPicker() {
        return {
            latitude: '',
            longitude: '',
            gettingLocation: false,
            locationError: '',
            locationSuccess: false,
            miniMap: null,

            init() {
                // Initialize mini map when coordinates are available
                this.$watch('latitude', () => this.updateMiniMap());
                this.$watch('longitude', () => this.updateMiniMap());
            },

            getCurrentLocation() {
                if (!navigator.geolocation) {
                    this.locationError = 'Geolocation wordt niet ondersteund door uw browser.';
                    return;
                }

                this.gettingLocation = true;
                this.locationError = '';
                this.locationSuccess = false;

                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        this.latitude = position.coords.latitude.toFixed(6);
                        this.longitude = position.coords.longitude.toFixed(6);
                        this.gettingLocation = false;
                        this.locationSuccess = true;

                        // Hide success message after 3 seconds
                        setTimeout(() => {
                            this.locationSuccess = false;
                        }, 3000);
                    },
                    (error) => {
                        this.gettingLocation = false;
                        switch(error.code) {
                            case error.PERMISSION_DENIED:
                                this.locationError = 'Toestemming voor locatie geweigerd.';
                                break;
                            case error.POSITION_UNAVAILABLE:
                                this.locationError = 'Locatie informatie is niet beschikbaar.';
                                break;
                            case error.TIMEOUT:
                                this.locationError = 'Verzoek om locatie is verlopen.';
                                break;
                            default:
                                this.locationError = 'Er is een onbekende fout opgetreden.';
                                break;
                        }
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 5000,
                        maximumAge: 0
                    }
                );
            },

            updateMiniMap() {
                if (this.latitude && this.longitude) {
                    setTimeout(() => {
                        const mapElement = document.getElementById('miniMap');
                        if (mapElement && window.L) {
                            if (this.miniMap) {
                                this.miniMap.remove();
                            }

                            this.miniMap = L.map('miniMap').setView([this.latitude, this.longitude], 15);

                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '© OpenStreetMap contributors'
                            }).addTo(this.miniMap);

                            L.marker([this.latitude, this.longitude]).addTo(this.miniMap)
                                .bindPopup('Locatie van uw klacht')
                                .openPopup();
                        }
                    }, 100);
                }
            }
        }
    }

    // File Uploader Alpine.js Component
    function fileUploader() {
        return {
            files: [],
            isDragging: false,
            uploading: false,
            uploadProgress: 0,
            errors: [],
            maxFiles: 5,
            maxSize: 10 * 1024 * 1024, // 10MB

            init() {
                // Handle drag events
                window.addEventListener('dragover', (e) => e.preventDefault());
                window.addEventListener('drop', (e) => e.preventDefault());
            },

            handleDrop(event) {
                this.isDragging = false;
                const files = Array.from(event.dataTransfer.files);
                this.handleFiles(files);
            },

            handleFiles(fileList) {
                const files = Array.from(fileList);
                this.errors = [];

                // Check file count
                if (this.files.length + files.length > this.maxFiles) {
                    this.errors.push(`Maximaal ${this.maxFiles} bestanden toegestaan.`);
                    return;
                }

                files.forEach(file => {
                    // Check file size
                    if (file.size > this.maxSize) {
                        this.errors.push(`${file.name} is te groot (max 10MB).`);
                        return;
                    }

                    // Check file type
                    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
                    if (!allowedTypes.includes(file.type)) {
                        this.errors.push(`${file.name} heeft een niet-ondersteund bestandstype.`);
                        return;
                    }

                    // Create preview for images
                    const fileObj = {
                        file: file,
                        name: file.name,
                        size: file.size,
                        type: file.type,
                        preview: null
                    };

                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            fileObj.preview = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    }

                    this.files.push(fileObj);
                });

                // Update the actual file input
                this.updateFileInput();
            },

            removeFile(index) {
                this.files.splice(index, 1);
                this.updateFileInput();
            },

            updateFileInput() {
                const fileInput = this.$refs.fileInput;
                const dt = new DataTransfer();

                this.files.forEach(fileObj => {
                    dt.items.add(fileObj.file);
                });

                fileInput.files = dt.files;
            },

            formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }
        }
    }
</script>

</x-guest-layout>
