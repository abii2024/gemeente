<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Klacht bewerken #{{ $complaint->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.complaints.update', $complaint) }}" class="space-y-6">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">Titel</label>
                            <input type="text" id="title" name="title" value="{{ old('title', $complaint->title) }}" required
                                   class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('title')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Beschrijving</label>
                            <textarea id="description" name="description" rows="4" required class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $complaint->description) }}</textarea>
                            @error('description')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="category" class="block text-sm font-medium text-gray-700">Categorie</label>
                                <select id="category" name="category" required class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @foreach (['wegen' => 'Wegen & Verkeer', 'openbare_verlichting' => 'Openbare Verlichting', 'afval' => 'Afval & Reiniging', 'groen' => 'Groen & Parken', 'overlast' => 'Overlast', 'openbare_ruimte' => 'Openbare Ruimte', 'water' => 'Water & Riolering', 'overig' => 'Overig'] as $value => $label)
                                        <option value="{{ $value }}" {{ old('category', $complaint->category) === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('category')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="priority" class="block text-sm font-medium text-gray-700">Urgentie</label>
                                <select id="priority" name="priority" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @foreach (['low' => 'Laag', 'medium' => 'Normaal', 'high' => 'Hoog', 'urgent' => 'Urgent'] as $value => $label)
                                        <option value="{{ $value }}" {{ old('priority', $complaint->priority) === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('priority')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-700">Locatiebeschrijving</label>
                            <input type="text" id="location" name="location" value="{{ old('location', $complaint->location) }}" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('location')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="lat" class="block text-sm font-medium text-gray-700">Latitude</label>
                                <input type="number" step="any" id="lat" name="lat" value="{{ old('lat', $complaint->lat) }}" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('lat')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="lng" class="block text-sm font-medium text-gray-700">Longitude</label>
                                <input type="number" step="any" id="lng" name="lng" value="{{ old('lng', $complaint->lng) }}" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('lng')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="reporter_name" class="block text-sm font-medium text-gray-700">Naam melder</label>
                                <input type="text" id="reporter_name" name="reporter_name" value="{{ old('reporter_name', $complaint->reporter_name) }}" required class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('reporter_name')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="reporter_email" class="block text-sm font-medium text-gray-700">E-mailadres</label>
                                <input type="email" id="reporter_email" name="reporter_email" value="{{ old('reporter_email', $complaint->reporter_email) }}" required class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('reporter_email')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="reporter_phone" class="block text-sm font-medium text-gray-700">Telefoonnummer</label>
                            <input type="text" id="reporter_phone" name="reporter_phone" value="{{ old('reporter_phone', $complaint->reporter_phone) }}" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('reporter_phone')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="internal_notes" class="block text-sm font-medium text-gray-700">Interne notities</label>
                            <textarea id="internal_notes" name="internal_notes" rows="3" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('internal_notes', $complaint->internal_notes) }}</textarea>
                            @error('internal_notes')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('admin.complaints.show', $complaint) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">Annuleren</a>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Opslaan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
