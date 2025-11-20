<x-guest-layout>
    <div class="min-h-screen bg-gradient-to-br from-amber-950 via-amber-900 to-slate-950 relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -top-32 -left-24 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 right-10 w-[34rem] h-[34rem] bg-amber-300/10 rounded-full blur-[110px]"></div>
            <div class="absolute inset-x-0 top-1/3 h-px bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
        </div>

        <div class="relative z-10 w-full px-4 sm:px-6 lg:px-12 py-12 lg:py-16 space-y-12">
            <header class="space-y-6">
                <p class="text-xs uppercase tracking-[0.25em] text-amber-200 font-semibold">Gemeente · Klachtendienst</p>
                <div class="space-y-3">
                    <h1 class="text-4xl md:text-5xl font-black text-white leading-tight">Dien een klacht in als een professioneel bedrijf dat u verwacht</h1>
                    <p class="text-lg text-slate-200 max-w-3xl">Elke melding wordt veilig vastgelegd, voorzien van statusupdates en – indien u toestemming geeft – automatisch verrijkt met GPS-coördinaten voor snelle opvolging.</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <span class="px-4 py-2 rounded-full border border-white/15 text-amber-100 bg-white/5 text-sm font-semibold">AVG-proof verwerking</span>
                    <span class="px-4 py-2 rounded-full border border-white/15 text-amber-100 bg-white/5 text-sm font-semibold">Transparante updates</span>
                </div>
            </header>

            <div class="bg-white/98 backdrop-blur-xl rounded-3xl border border-white/30 shadow-2xl shadow-amber-900/30 overflow-hidden w-full">
                <div class="p-8 md:p-12">
                    <form x-data="complaintForm()" x-init="init()" @submit.prevent="handleSubmit($event)" action="{{ route('complaint.store') }}" method="POST" enctype="multipart/form-data" class="space-y-10">
                        @csrf

                        <div class="bg-slate-50 border border-slate-200 rounded-2xl p-7 md:p-8">
                            <div class="flex items-start gap-4 mb-6">
                                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 font-black flex items-center justify-center">A</div>
                                <div>
                                    <h3 class="text-xl font-black text-slate-900">Klantgegevens</h3>
                                    <p class="text-sm text-slate-600">Contactgegevens die we gebruiken voor bevestigingen en terugkoppeling.</p>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="reporter_name" class="block text-sm font-semibold text-slate-800 mb-2">Naam *</label>
                                        <input type="text" id="reporter_name" name="reporter_name" value="{{ old('reporter_name') }}" required class="w-full px-4 py-3 text-base border border-slate-200 rounded-xl focus:ring-4 focus:ring-amber-200 focus:border-amber-500 transition-all bg-white shadow-sm" placeholder="Uw volledige naam">
                                        @error('reporter_name')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="reporter_email" class="block text-sm font-semibold text-slate-800 mb-2">E-mail *</label>
                                        <input type="email" id="reporter_email" name="reporter_email" value="{{ old('reporter_email') }}" required class="w-full px-4 py-3 text-base border border-slate-200 rounded-xl focus:ring-4 focus:ring-amber-200 focus:border-amber-500 transition-all bg-white shadow-sm" placeholder="naam@voorbeeld.nl">
                                        @error('reporter_email')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="reporter_phone" class="block text-sm font-semibold text-slate-800 mb-2">Telefoon</label>
                                        <input type="tel" id="reporter_phone" name="reporter_phone" value="{{ old('reporter_phone') }}" class="w-full px-4 py-3 text-base border border-slate-200 rounded-xl focus:ring-4 focus:ring-amber-200 focus:border-amber-500 transition-all bg-white shadow-sm" placeholder="06-12345678">
                                    </div>
                                    <div>
                                        <label for="location" class="block text-sm font-semibold text-slate-800 mb-2">Adres</label>
                                        <input type="text" id="location" name="location" value="{{ old('location') }}" class="w-full px-4 py-3 text-base border border-slate-200 rounded-xl focus:ring-4 focus:ring-amber-200 focus:border-amber-500 transition-all bg-white shadow-sm" placeholder="Straat, huisnummer, plaats">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white border border-slate-200 rounded-2xl p-7 md:p-8 shadow-sm">
                            <div class="flex items-start gap-4 mb-6">
                                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 font-black flex items-center justify-center">B</div>
                                <div>
                                    <h3 class="text-xl font-black text-slate-900">Details van de klacht</h3>
                                    <p class="text-sm text-slate-600">Beschrijf de situatie zo concreet mogelijk.</p>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="title" class="block text-sm font-semibold text-slate-800 mb-2">Titel *</label>
                                        <input type="text" id="title" name="title" value="{{ old('title') }}" required maxlength="100" class="w-full px-4 py-3 text-base border border-slate-200 rounded-xl focus:ring-4 focus:ring-amber-200 focus:border-amber-500 transition-all bg-white shadow-sm" placeholder="Bijvoorbeeld: Kapotte straatlantaarn">
                                        @error('title')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="category" class="block text-sm font-semibold text-slate-800 mb-2">Categorie *</label>
                                        <select id="category" name="category" required class="w-full px-4 py-3 text-base border border-slate-200 rounded-xl focus:ring-4 focus:ring-amber-200 focus:border-amber-500 transition-all bg-white shadow-sm">
                                            <option value="">Selecteer een categorie...</option>
                                            <optgroup label="Wegen & Verkeer">
                                                <option value="wegen_onderhoud">Wegen - Gaten of barsten</option>
                                                <option value="stoepen">Stoepen - Losse tegels</option>
                                                <option value="fietspaden">Fietspaden - Slecht onderhoud</option>
                                                <option value="verkeersborden">Verkeersborden - Beschadigd</option>
                                                <option value="parkeren">Parkeren - Illegaal geparkeerd</option>
                                            </optgroup>
                                            <optgroup label="Openbare Verlichting">
                                                <option value="straatverlichting">Straatverlichting - Kapot</option>
                                                <option value="parkverlichting">Parkverlichting - Defect</option>
                                            </optgroup>
                                            <optgroup label="Afval & Reiniging">
                                                <option value="afval_ophaling">Afval - Niet opgehaald</option>
                                                <option value="afval_container">Container - Vol of beschadigd</option>
                                                <option value="zwerfvuil">Zwerfvuil - Rommel</option>
                                                <option value="graffiti">Graffiti - Vandalisme</option>
                                            </optgroup>
                                            <optgroup label="Groen & Natuur">
                                                <option value="bomen">Bomen - Gevaarlijk of ziek</option>
                                                <option value="parken">Parken - Onderhoud nodig</option>
                                                <option value="speeltuinen">Speeltuinen - Kapot</option>
                                            </optgroup>
                                            <optgroup label="Water & Riolering">
                                                <option value="riool_verstopt">Riool - Verstopt</option>
                                                <option value="wateroverlast">Wateroverlast</option>
                                            </optgroup>
                                            <optgroup label="Overlast">
                                                <option value="geluidsoverlast">Geluidsoverlast</option>
                                                <option value="stankoverlast">Stankoverlast</option>
                                            </optgroup>
                                            <optgroup label="Anders">
                                                <option value="overig">Overig</option>
                                            </optgroup>
                                        </select>
                                        @error('category')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="priority" class="block text-sm font-semibold text-slate-800 mb-2">Urgentie</label>
                                        <select id="priority" name="priority" class="w-full px-4 py-3 text-base border border-slate-200 rounded-xl focus:ring-4 focus:ring-amber-200 focus:border-amber-500 transition-all bg-white shadow-sm">
                                            <option value="low">Laag</option>
                                            <option value="medium" selected>Normaal</option>
                                            <option value="high">Hoog</option>
                                            <option value="urgent">Urgent</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label for="description" class="block text-sm font-semibold text-slate-800 mb-2">Beschrijving *</label>
                                    <textarea id="description" name="description" rows="7" required class="w-full px-4 py-3 text-base border border-slate-200 rounded-xl focus:ring-4 focus:ring-amber-200 focus:border-amber-500 transition-all bg-white shadow-sm" placeholder="Beschrijf het probleem zo concreet mogelijk...">{{ old('description') }}</textarea>
                                    <p class="text-xs text-slate-500 mt-2">Hoe meer details u deelt (tijdstip, frequentie, context), hoe sneller we de juiste actie kunnen nemen.</p>
                                    @error('description')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="bg-slate-50 border border-slate-200 rounded-2xl p-7 md:p-8">
                            <div class="flex items-start gap-4 mb-6">
                                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 font-black flex items-center justify-center">C</div>
                                <div>
                                    <h3 class="text-xl font-black text-slate-900">Locatie (GPS-optie)</h3>
                                    <p class="text-sm text-slate-600">Met toestemming vullen we automatisch de GPS-coördinaten in. Anders kunt u het adres of coördinaten handmatig invullen.</p>
                                </div>
                            </div>

                            <div class="space-y-5">
                                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-amber-600 text-xl">📡</div>
                                        <div>
                                            <p class="text-sm font-semibold text-slate-800">Automatische GPS-opvraag</p>
                                            <p class="text-sm text-slate-600">We vragen alleen toestemming bij indienen of wanneer u er expliciet om vraagt.</p>
                                            <label class="mt-2 inline-flex items-center gap-2 text-sm text-slate-700">
                                                <input type="checkbox" x-model="autoPrompt" class="text-amber-600 border-slate-300 rounded focus:ring-amber-500">
                                                <span>Vraag automatisch GPS bij indienen</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="flex flex-wrap gap-3">
                                        <button type="button" @click="requestLocation('manual')" :disabled="locationFetching" class="inline-flex items-center gap-2 px-4 py-3 rounded-xl bg-amber-600 text-white font-semibold shadow-md hover:bg-amber-700 transition disabled:opacity-50">
                                            <svg x-show="!locationFetching" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-2-9-7-7-9 9 9 2z"/></svg>
                                            <svg x-show="locationFetching" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4l3-3-3-3v4A8 8 0 004 12z"/></svg>
                                            <span x-text="locationFetching ? 'Ophalen...' : 'Gebruik mijn locatie'"></span>
                                        </button>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 lg:grid-cols-[1.1fr,0.9fr] gap-6 items-start">
                                    <div class="relative h-72 md:h-80 rounded-2xl overflow-hidden border border-slate-200 shadow-inner bg-slate-200">
                                        <div id="complaint-map" class="absolute inset-0"></div>
                                        <div class="absolute top-3 left-3 bg-white/90 text-slate-800 rounded-xl px-3 py-1.5 text-xs font-semibold shadow">Kaart: GPS & handmatige input</div>
                                        <div class="absolute bottom-3 left-3 bg-white/85 text-slate-700 rounded-xl px-3 py-1 text-[11px] shadow">Klik op de kaart om coördinaten te vullen</div>
                                    </div>
                                    <div class="space-y-4">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label for="lat" class="block text-sm font-semibold text-slate-800 mb-2">Latitude</label>
                                                <input type="number" id="lat" name="lat" step="any" x-model="lat" class="w-full px-4 py-3 text-base border border-slate-200 rounded-xl focus:ring-4 focus:ring-amber-200 focus:border-amber-500 transition-all bg-white shadow-sm" placeholder="52.367600">
                                            </div>
                                            <div>
                                                <label for="lng" class="block text-sm font-semibold text-slate-800 mb-2">Longitude</label>
                                                <input type="number" id="lng" name="lng" step="any" x-model="lng" class="w-full px-4 py-3 text-base border border-slate-200 rounded-xl focus:ring-4 focus:ring-amber-200 focus:border-amber-500 transition-all bg-white shadow-sm" placeholder="4.904100">
                                            </div>
                                        </div>
                                        <div x-show="locationStatus" class="border border-emerald-200 bg-emerald-50 text-emerald-800 rounded-xl px-4 py-3 text-sm" x-text="locationStatus"></div>
                                        <div x-show="locationError" class="border border-red-200 bg-red-50 text-red-700 rounded-xl px-4 py-3 text-sm" x-text="locationError"></div>
                                        <p class="text-xs text-slate-600">De kaart synchroniseert realtime met ingevulde coördinaten of uw GPS-keuze. Klik op de kaart om een nieuwe pin te plaatsen.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div x-data="fileUploader()" class="bg-white border border-slate-200 rounded-2xl p-7 md:p-8 shadow-sm">
                            <div class="flex items-start gap-4 mb-6">
                                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 font-black flex items-center justify-center">D</div>
                                <div>
                                    <h3 class="text-xl font-black text-slate-900">Bijlagen</h3>
                                    <p class="text-sm text-slate-600">Optioneel, maar foto’s versnellen de beoordeling.</p>
                                </div>
                            </div>

                            <div class="space-y-5">
                                <div @click="$refs.fileInput.click()" class="border-2 border-dashed border-slate-300 rounded-2xl p-8 text-center cursor-pointer hover:border-amber-500 hover:bg-slate-50 transition-all bg-white shadow-sm">
                                    <svg class="mx-auto h-12 w-12 text-slate-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2"/>
                                    </svg>
                                    <p class="text-base text-slate-700 mt-4 font-medium">Klik om foto’s te selecteren of sleep ze hierheen</p>
                                    <p class="text-xs text-slate-500 mt-1">PNG, JPG tot 10MB · Maximaal 5 bestanden</p>
                                </div>

                                <input type="file" x-ref="fileInput" @change="handleFiles($event.target.files)" name="attachments[]" multiple accept="image/*" class="hidden">

                                <div x-show="files.length > 0" class="space-y-3">
                                    <h4 class="text-sm font-semibold text-slate-800">Geselecteerde bestanden</h4>
                                    <template x-for="(file, index) in files" :key="index">
                                        <div class="flex items-center justify-between bg-slate-50 p-4 rounded-xl border border-slate-200">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center text-amber-700 font-bold">IMG</div>
                                                <p class="text-sm font-medium text-slate-800" x-text="file.name"></p>
                                            </div>
                                            <button type="button" @click="removeFile(index)" class="text-red-600 hover:text-red-800 p-2 rounded-lg hover:bg-red-50 transition">Verwijder</button>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-7 md:p-8 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                            <div class="space-y-2">
                                <p class="text-sm font-semibold text-emerald-800">Laatste controle</p>
                                <h3 class="text-2xl font-black text-emerald-900">Klaar om te verzenden?</h3>
                                <p class="text-sm text-emerald-800/80">U ontvangt direct een bevestiging per e-mail en kunt uw melding volgen met het dossiernummer.</p>
                            </div>
                            <button type="submit" class="inline-flex items-center gap-3 px-8 py-4 rounded-2xl bg-amber-600 text-white font-black text-lg shadow-lg shadow-amber-500/40 hover:bg-amber-700 transition">
                                <span class="text-2xl">📤</span>
                                <span>Melding indienen</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script>
function complaintForm() {
    return {
        lat: @js(old('lat', '')),
        lng: @js(old('lng', '')),
        autoPrompt: true,
        locationStatus: '',
        locationError: '',
        locationFetching: false,
        map: null,
        marker: null,
        mapReady: false,
        defaultCenter: { lat: 52.370216, lng: 4.895168 },

        init() {
            if (navigator.permissions && navigator.geolocation && !this.lat && !this.lng) {
                navigator.permissions.query({ name: 'geolocation' }).then((status) => {
                    if (status.state === 'granted') {
                        this.requestLocation('auto');
                    }
                }).catch(() => {});
            }

            this.$nextTick(() => {
                this.initMap();
                this.$watch('lat', () => this.syncMap());
                this.$watch('lng', () => this.syncMap());
            });
        },

        async handleSubmit(event) {
            const form = event.target;

            if (this.autoPrompt && !this.lat && !this.lng) {
                await this.requestLocation('auto');
            }

            form.submit();
        },

        async requestLocation(trigger = 'manual') {
            if (!navigator.geolocation) {
                this.locationError = 'Locatievoorziening niet beschikbaar.';
                return false;
            }

            this.locationFetching = true;
            this.locationError = '';
            this.locationStatus = trigger === 'auto' ? 'GPS-toestemming gevraagd...' : 'Locatie ophalen...';

            return new Promise((resolve) => {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        this.lat = position.coords.latitude.toFixed(6);
                        this.lng = position.coords.longitude.toFixed(6);
                        this.locationFetching = false;
                        this.locationStatus = 'Locatie automatisch ingevuld.';
                        setTimeout(() => this.locationStatus = '', 2500);
                        resolve(true);
                    },
                    (error) => {
                        this.locationFetching = false;
                        this.locationError = this.translateLocationError(error);
                        this.locationStatus = '';
                        resolve(false);
                    },
                    { enableHighAccuracy: true, timeout: 12000, maximumAge: 0 }
                );
            });
        },

        initMap() {
            const container = document.getElementById('complaint-map');
            if (!container || !window.L) return;

            const [lat, lng] = this.getSafeCoords();
            this.map = L.map(container, { zoomControl: true }).setView([lat, lng], this.lat && this.lng ? 16 : 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap-bijdragers'
            }).addTo(this.map);

            this.marker = L.marker([lat, lng]).addTo(this.map);
            this.map.on('click', (e) => {
                this.lat = e.latlng.lat.toFixed(6);
                this.lng = e.latlng.lng.toFixed(6);
                this.updateMarker(e.latlng.lat, e.latlng.lng, true);
            });

            this.mapReady = true;
            this.updateMarker(lat, lng, false);
        },

        syncMap() {
            if (!this.mapReady) return;
            const latNum = parseFloat(this.lat);
            const lngNum = parseFloat(this.lng);
            if (isNaN(latNum) || isNaN(lngNum)) return;
            this.updateMarker(latNum, lngNum, true);
        },

        updateMarker(lat, lng, pan = false) {
            if (!this.mapReady || !this.marker) return;
            this.marker.setLatLng([lat, lng]);
            if (pan) {
                this.map.setView([lat, lng], this.map.getZoom() < 14 ? 15 : this.map.getZoom(), { animate: true });
            }
        },

        getSafeCoords() {
            const latNum = parseFloat(this.lat);
            const lngNum = parseFloat(this.lng);
            if (!isNaN(latNum) && !isNaN(lngNum)) {
                return [latNum, lngNum];
            }
            return [this.defaultCenter.lat, this.defaultCenter.lng];
        },

        translateLocationError(error) {
            if (!error) return 'Locatie ophalen mislukt.';
            switch (error.code) {
                case error.PERMISSION_DENIED:
                    return 'Toestemming geweigerd. U kunt handmatig coördinaten of adres invullen.';
                case error.POSITION_UNAVAILABLE:
                    return 'GPS-signaal niet beschikbaar. Probeer buiten of controleer locatie-instellingen.';
                case error.TIMEOUT:
                    return 'Locatie ophalen duurde te lang. Probeer opnieuw.';
                default:
                    return 'Locatie ophalen mislukt. Vul handmatig of probeer opnieuw.';
            }
        }
    };
}

function fileUploader() {
    return {
        files: [],
        maxFiles: 5,

        handleFiles(fileList) {
            const files = Array.from(fileList);
            if (this.files.length + files.length > this.maxFiles) return;

            files.forEach(file => {
                this.files.push({ file: file, name: file.name });
            });
            this.updateFileInput();
        },

        removeFile(index) {
            this.files.splice(index, 1);
            this.updateFileInput();
        },

        updateFileInput() {
            const fileInput = this.$refs.fileInput;
            const dt = new DataTransfer();
            this.files.forEach(fileObj => dt.items.add(fileObj.file));
            fileInput.files = dt.files;
        }
    };
}
</script>

</x-guest-layout>
