<x-guest-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h1 class="text-3xl font-bold text-gray-900 mb-6">Klacht Indienen</h1>
                    
                    <form action="{{ route('complaint.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        
                        <!-- Title -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                Titel van de klacht *
                            </label>
                            <input type="text" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title') }}"
                                   required 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Kort beschrijving van het probleem">
                            @error('title')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                                Categorie *
                            </label>
                            <select id="category" 
                                    name="category" 
                                    required 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Selecteer een categorie</option>
                                <option value="wegen" {{ old('category') == 'wegen' ? 'selected' : '' }}>Wegen & Verkeer</option>
                                <option value="openbare_verlichting" {{ old('category') == 'openbare_verlichting' ? 'selected' : '' }}>Openbare Verlichting</option>
                                <option value="afval" {{ old('category') == 'afval' ? 'selected' : '' }}>Afval & Reiniging</option>
                                <option value="groen" {{ old('category') == 'groen' ? 'selected' : '' }}>Groen & Parken</option>
                                <option value="overlast" {{ old('category') == 'overlast' ? 'selected' : '' }}>Overlast</option>
                                <option value="openbare_ruimte" {{ old('category') == 'openbare_ruimte' ? 'selected' : '' }}>Openbare Ruimte</option>
                                <option value="water" {{ old('category') == 'water' ? 'selected' : '' }}>Water & Riolering</option>
                                <option value="overig" {{ old('category') == 'overig' ? 'selected' : '' }}>Overig</option>
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
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="06-12345678">
                            @error('reporter_phone')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                Beschrijving *
                            </label>
                            <textarea id="description" 
                                      name="description" 
                                      rows="5" 
                                      required
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                      placeholder="Uitgebreide beschrijving van het probleem">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Location Section -->
                        <div x-data="locationPicker()" class="space-y-4">
                            <div class="flex items-center justify-between">
                                <label class="block text-sm font-medium text-gray-700">
                                    Locatie (optioneel)
                                </label>
                                <button type="button" 
                                        @click="getCurrentLocation()" 
                                        :disabled="gettingLocation"
                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <svg x-show="!gettingLocation" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <svg x-show="gettingLocation" class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
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
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
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
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                           placeholder="4.9041">
                                </div>
                            </div>

                            <!-- Location Address -->
                            <div>
                                <label for="location" class="block text-sm font-medium text-gray-700 mb-1">
                                    Adres of beschrijving van de locatie
                                </label>
                                <input type="text" 
                                       id="location" 
                                       name="location" 
                                       value="{{ old('location') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="Bijv. Damrak 1, Amsterdam of tegenover het station">
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
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('reporter_email')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- File Attachments -->
                        <div x-data="fileUploader()" class="space-y-4">
                            <label class="block text-sm font-medium text-gray-700">
                                Foto's (optioneel)
                            </label>
                            
                            <!-- File Drop Zone -->
                            <div @click="$refs.fileInput.click()" 
                                 @dragover.prevent 
                                 @drop.prevent="handleDrop($event)"
                                 class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:border-blue-400 transition-colors"
                                 :class="{ 'border-blue-400 bg-blue-50': isDragging }">
                                
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
                        <div class="flex justify-end">
                            <button type="submit" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                Klacht Indienen
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>

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
