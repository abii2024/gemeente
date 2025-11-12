@php
use Illuminate\Support\Facades\Storage;
@endphp
<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Klacht bewerken #{{ $complaint->id }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.complaints.show', $complaint) }}" 
                   class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                    Terug
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Edit Form -->
                <div class="lg:col-span-2 space-y-6">
                    <form method="POST" action="{{ route('admin.complaints.update', $complaint) }}">
                        @csrf
                        @method('PATCH')

                        <!-- Basic Information -->
                        <div class="bg-white shadow-sm sm:rounded-lg mb-6">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">📋 Basis Informatie</h3>

                                <!-- Title -->
                                <div class="mb-4">
                                    <label for="title" class="block text-sm font-medium text-gray-700">Titel</label>
                                    <input type="text" id="title" name="title" 
                                           value="{{ old('title', $complaint->title) }}" required
                                           class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('title')
                                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Description -->
                                <div class="mb-4">
                                    <label for="description" class="block text-sm font-medium text-gray-700">Beschrijving</label>
                                    <textarea id="description" name="description" rows="4" required
                                              class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $complaint->description) }}</textarea>
                                    @error('description')
                                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Category & Priority -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="category" class="block text-sm font-medium text-gray-700">Categorie</label>
                                        <select id="category" name="category" required
                                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <optgroup label="🛣️ Wegen & Verkeer">
                                                <option value="wegen_onderhoud" {{ old('category', $complaint->category) === 'wegen_onderhoud' ? 'selected' : '' }}>Wegen Onderhoud</option>
                                                <option value="stoepen" {{ old('category', $complaint->category) === 'stoepen' ? 'selected' : '' }}>Stoepen</option>
                                                <option value="fietspaden" {{ old('category', $complaint->category) === 'fietspaden' ? 'selected' : '' }}>Fietspaden</option>
                                                <option value="verkeersborden" {{ old('category', $complaint->category) === 'verkeersborden' ? 'selected' : '' }}>Verkeersborden</option>
                                                <option value="wegmarkeringen" {{ old('category', $complaint->category) === 'wegmarkeringen' ? 'selected' : '' }}>Wegmarkeringen</option>
                                                <option value="parkeren" {{ old('category', $complaint->category) === 'parkeren' ? 'selected' : '' }}>Parkeren</option>
                                            </optgroup>
                                            <optgroup label="💡 Verlichting">
                                                <option value="straatverlichting" {{ old('category', $complaint->category) === 'straatverlichting' ? 'selected' : '' }}>Straatverlichting</option>
                                                <option value="parkverlichting" {{ old('category', $complaint->category) === 'parkverlichting' ? 'selected' : '' }}>Parkverlichting</option>
                                                <option value="tunnel_verlichting" {{ old('category', $complaint->category) === 'tunnel_verlichting' ? 'selected' : '' }}>Tunnel Verlichting</option>
                                            </optgroup>
                                            <optgroup label="🗑️ Afval & Reiniging">
                                                <option value="afval_ophaling" {{ old('category', $complaint->category) === 'afval_ophaling' ? 'selected' : '' }}>Afval Ophaling</option>
                                                <option value="afval_container" {{ old('category', $complaint->category) === 'afval_container' ? 'selected' : '' }}>Afval Container</option>
                                                <option value="zwerfvuil" {{ old('category', $complaint->category) === 'zwerfvuil' ? 'selected' : '' }}>Zwerfvuil</option>
                                                <option value="graffiti" {{ old('category', $complaint->category) === 'graffiti' ? 'selected' : '' }}>Graffiti</option>
                                                <option value="hondenpoep" {{ old('category', $complaint->category) === 'hondenpoep' ? 'selected' : '' }}>Hondenpoep</option>
                                                <option value="straat_reiniging" {{ old('category', $complaint->category) === 'straat_reiniging' ? 'selected' : '' }}>Straat Reiniging</option>
                                            </optgroup>
                                            <optgroup label="🌳 Groen">
                                                <option value="bomen" {{ old('category', $complaint->category) === 'bomen' ? 'selected' : '' }}>Bomen</option>
                                                <option value="onkruid" {{ old('category', $complaint->category) === 'onkruid' ? 'selected' : '' }}>Onkruid</option>
                                                <option value="parken" {{ old('category', $complaint->category) === 'parken' ? 'selected' : '' }}>Parken</option>
                                                <option value="speeltuinen" {{ old('category', $complaint->category) === 'speeltuinen' ? 'selected' : '' }}>Speeltuinen</option>
                                                <option value="groenstroken" {{ old('category', $complaint->category) === 'groenstroken' ? 'selected' : '' }}>Groenstroken</option>
                                            </optgroup>
                                            <optgroup label="💧 Water & Riolering">
                                                <option value="riool_verstopt" {{ old('category', $complaint->category) === 'riool_verstopt' ? 'selected' : '' }}>Riool Verstopt</option>
                                                <option value="wateroverlast" {{ old('category', $complaint->category) === 'wateroverlast' ? 'selected' : '' }}>Wateroverlast</option>
                                                <option value="put_deksel" {{ old('category', $complaint->category) === 'put_deksel' ? 'selected' : '' }}>Put Deksel</option>
                                                <option value="lekkage" {{ old('category', $complaint->category) === 'lekkage' ? 'selected' : '' }}>Lekkage</option>
                                            </optgroup>
                                            <optgroup label="🪑 Openbare Ruimte">
                                                <option value="straatmeubilair" {{ old('category', $complaint->category) === 'straatmeubilair' ? 'selected' : '' }}>Straatmeubilair</option>
                                                <option value="fietsenrekken" {{ old('category', $complaint->category) === 'fietsenrekken' ? 'selected' : '' }}>Fietsenrekken</option>
                                                <option value="bushokje" {{ old('category', $complaint->category) === 'bushokje' ? 'selected' : '' }}>Bushokje</option>
                                                <option value="speelplaats" {{ old('category', $complaint->category) === 'speelplaats' ? 'selected' : '' }}>Speelplaats</option>
                                                <option value="bankjes" {{ old('category', $complaint->category) === 'bankjes' ? 'selected' : '' }}>Bankjes</option>
                                            </optgroup>
                                            <optgroup label="⚠️ Overlast">
                                                <option value="geluidsoverlast" {{ old('category', $complaint->category) === 'geluidsoverlast' ? 'selected' : '' }}>Geluidsoverlast</option>
                                                <option value="stankoverlast" {{ old('category', $complaint->category) === 'stankoverlast' ? 'selected' : '' }}>Stankoverlast</option>
                                                <option value="wildgroei" {{ old('category', $complaint->category) === 'wildgroei' ? 'selected' : '' }}>Wildgroei</option>
                                                <option value="drugsoverlast" {{ old('category', $complaint->category) === 'drugsoverlast' ? 'selected' : '' }}>Drugsoverlast</option>
                                                <option value="dierenplaag" {{ old('category', $complaint->category) === 'dierenplaag' ? 'selected' : '' }}>Dierenplaag</option>
                                                <option value="illegaal_afval" {{ old('category', $complaint->category) === 'illegaal_afval' ? 'selected' : '' }}>Illegaal Afval</option>
                                            </optgroup>
                                            <option value="overig" {{ old('category', $complaint->category) === 'overig' ? 'selected' : '' }}>Overig</option>
                                        </select>
                                        @error('category')
                                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="priority" class="block text-sm font-medium text-gray-700">Urgentie</label>
                                        <select id="priority" name="priority" 
                                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="low" {{ old('priority', $complaint->priority) === 'low' ? 'selected' : '' }}>🟢 Laag</option>
                                            <option value="medium" {{ old('priority', $complaint->priority) === 'medium' ? 'selected' : '' }}>🟡 Normaal</option>
                                            <option value="high" {{ old('priority', $complaint->priority) === 'high' ? 'selected' : '' }}>🟠 Hoog</option>
                                            <option value="urgent" {{ old('priority', $complaint->priority) === 'urgent' ? 'selected' : '' }}>🔴 Urgent</option>
                                        </select>
                                        @error('priority')
                                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Status & Assignment -->
                        <div class="bg-white shadow-sm sm:rounded-lg mb-6">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">⚙️ Status & Toewijzing</h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                        <select id="status" name="status" 
                                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="open" {{ old('status', $complaint->status) === 'open' ? 'selected' : '' }}>📋 Open</option>
                                            <option value="in_progress" {{ old('status', $complaint->status) === 'in_progress' ? 'selected' : '' }}>⚙️ In Behandeling</option>
                                            <option value="resolved" {{ old('status', $complaint->status) === 'resolved' ? 'selected' : '' }}>✅ Opgelost</option>
                                            <option value="closed" {{ old('status', $complaint->status) === 'closed' ? 'selected' : '' }}>🔒 Gesloten</option>
                                        </select>
                                        @error('status')
                                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="assigned_to" class="block text-sm font-medium text-gray-700">Toegewezen aan</label>
                                        <select id="assigned_to" name="assigned_to" 
                                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">Niet toegewezen</option>
                                            @foreach(\App\Models\User::where('role', 'admin')->get() as $admin)
                                                <option value="{{ $admin->id }}" {{ old('assigned_to', $complaint->assigned_to) == $admin->id ? 'selected' : '' }}>
                                                    {{ $admin->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('assigned_to')
                                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="bg-white shadow-sm sm:rounded-lg mb-6">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">📍 Locatie</h3>

                                <div class="mb-4">
                                    <label for="location" class="block text-sm font-medium text-gray-700">Locatiebeschrijving</label>
                                    <input type="text" id="location" name="location" 
                                           value="{{ old('location', $complaint->location) }}"
                                           class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                           placeholder="bijv. Hoofdstraat 123, Amsterdam">
                                    @error('location')
                                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="lat" class="block text-sm font-medium text-gray-700">Breedtegraad (Latitude)</label>
                                        <input type="number" id="lat" name="lat" step="0.0000001"
                                               value="{{ old('lat', $complaint->lat) }}"
                                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                               placeholder="52.3676">
                                        @error('lat')
                                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="lng" class="block text-sm font-medium text-gray-700">Lengtegraad (Longitude)</label>
                                        <input type="number" id="lng" name="lng" step="0.0000001"
                                               value="{{ old('lng', $complaint->lng) }}"
                                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                               placeholder="4.9041">
                                        @error('lng')
                                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Reporter Info -->
                        <div class="bg-white shadow-sm sm:rounded-lg mb-6">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">👤 Melder Informatie</h3>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label for="reporter_name" class="block text-sm font-medium text-gray-700">Naam</label>
                                        <input type="text" id="reporter_name" name="reporter_name" required
                                               value="{{ old('reporter_name', $complaint->reporter_name) }}"
                                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        @error('reporter_name')
                                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="reporter_email" class="block text-sm font-medium text-gray-700">Email</label>
                                        <input type="email" id="reporter_email" name="reporter_email" required
                                               value="{{ old('reporter_email', $complaint->reporter_email) }}"
                                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        @error('reporter_email')
                                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="reporter_phone" class="block text-sm font-medium text-gray-700">Telefoon</label>
                                        <input type="text" id="reporter_phone" name="reporter_phone"
                                               value="{{ old('reporter_phone', $complaint->reporter_phone) }}"
                                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        @error('reporter_phone')
                                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Internal Notes -->
                        <div class="bg-white shadow-sm sm:rounded-lg mb-6">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">📝 Interne Notities</h3>

                                <div>
                                    <label for="internal_notes" class="block text-sm font-medium text-gray-700">
                                        Notities (alleen zichtbaar voor admins)
                                    </label>
                                    <textarea id="internal_notes" name="internal_notes" rows="4"
                                              class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                              placeholder="Voeg interne notities toe...">{{ old('internal_notes', $complaint->internal_notes) }}</textarea>
                                    @error('internal_notes')
                                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <button type="submit" 
                                    class="px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-semibold">
                                💾 Wijzigingen Opslaan
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Quick Info -->
                    <div class="bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">ℹ️ Quick Info</h3>
                            
                            <div class="space-y-3 text-sm">
                                <div>
                                    <span class="text-gray-500">ID:</span>
                                    <span class="font-semibold ml-2">#{{ $complaint->id }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Aangemaakt:</span>
                                    <span class="font-semibold ml-2">{{ $complaint->created_at->format('d-m-Y H:i') }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Laatste update:</span>
                                    <span class="font-semibold ml-2">{{ $complaint->updated_at->format('d-m-Y H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Attachments -->
                    @if($complaint->attachments && $complaint->attachments->count() > 0)
                    <div class="bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                📷 Bijlagen ({{ $complaint->attachments->count() }})
                            </h3>
                            
                            <div class="grid grid-cols-2 gap-2">
                                @foreach($complaint->attachments as $attachment)
                                    <a href="{{ Storage::url($attachment->path) }}" target="_blank" class="block">
                                        <img src="{{ Storage::url($attachment->path) }}" 
                                             alt="Bijlage" 
                                             class="w-full h-24 object-cover rounded-lg hover:opacity-75 transition">
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Actions -->
                    <div class="bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">⚡ Acties</h3>
                            
                            <div class="space-y-2">
                                <a href="{{ route('complaint.track', ['id' => $complaint->id, 'email' => $complaint->reporter_email]) }}" 
                                   target="_blank"
                                   class="block w-full px-4 py-2 bg-green-600 text-white text-center rounded-md hover:bg-green-700">
                                    👁️ Bekijk als Melder
                                </a>
                                
                                <form method="POST" action="{{ route('admin.complaints.destroy', $complaint) }}" 
                                      onsubmit="return confirm('Weet je zeker dat je deze klacht wilt verwijderen?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                        🗑️ Verwijderen
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
