<x-admin-layout>
    <x-slot name="header">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <span style="font-size: 2rem;">🔍</span>
                <h2 style="font-size: clamp(1.5rem, 3vw, 2rem); font-weight: 800; color: var(--neutral-900); margin: 0;">
                    Klacht #{{ $complaint->id }}
                </h2>
            </div>
            <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                <a href="{{ route('admin.complaints.edit', $complaint) }}"
                   style="padding: 0.875rem 1.5rem; background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%); color: white; border: none; border-radius: 12px; font-weight: 600; text-decoration: none; transition: all var(--transition-fast); display: inline-flex; align-items: center; gap: 0.5rem;"
                   onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='var(--shadow-lg)'"
                   onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                    ✏️ Bewerken
                </a>
                <form action="{{ route('admin.complaints.destroy', $complaint) }}" method="POST" onsubmit="return confirm('Weet u zeker dat u deze klacht wilt verwijderen?');" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            style="padding: 0.875rem 1.5rem; background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%); color: white; border: none; border-radius: 12px; font-weight: 600; cursor: pointer; transition: all var(--transition-fast); display: inline-flex; align-items: center; gap: 0.5rem;"
                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='var(--shadow-lg)'"
                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                        🗑️ Verwijderen
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div style="padding: 3rem 0;">
        <div class="container" style="max-width: 1400px;">
            <!-- Complaint Summary Card -->
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="lg:col-span-2 space-y-2">
                            <h3 class="text-2xl font-semibold text-gray-900">{{ $complaint->title }}</h3>
                            <p class="text-gray-700 leading-relaxed">{{ $complaint->description }}</p>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 uppercase">Status</h4>
                                @php($statusColors = ['open' => 'bg-red-100 text-red-700', 'in_behandeling' => 'bg-yellow-100 text-yellow-700', 'opgelost' => 'bg-green-100 text-green-700'])
                                <span class="mt-1 inline-flex px-3 py-1 rounded-full text-xs font-semibold {{ $statusColors[$complaint->status] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ ucfirst(str_replace('_', ' ', $complaint->status)) }}
                                </span>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 uppercase">Urgentie</h4>
                                @php($priorityLabels = ['low' => 'Laag', 'medium' => 'Normaal', 'high' => 'Hoog', 'urgent' => 'Urgent'])
                                <p class="mt-1 text-gray-800">{{ $priorityLabels[$complaint->priority] ?? 'Onbekend' }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 uppercase">Categorie</h4>
                                <p class="mt-1 text-gray-800">{{ ucfirst(str_replace('_', ' ', $complaint->category)) }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 uppercase">Ingediend</h4>
                                <p class="mt-1 text-gray-800">{{ $complaint->created_at->format('d-m-Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 uppercase">Melder</h4>
                            <p class="mt-1 text-gray-800">{{ $complaint->reporter_name }}</p>
                            <p class="text-sm text-gray-600">{{ $complaint->reporter_email }}</p>
                            @if ($complaint->reporter_phone)
                                <p class="text-sm text-gray-600">{{ $complaint->reporter_phone }}</p>
                            @endif
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 uppercase">Locatie</h4>
                            @if ($complaint->location)
                                <p class="mt-1 text-gray-800">{{ $complaint->location }}</p>
                            @else
                                <p class="mt-1 text-gray-500">Geen adres opgegeven.</p>
                            @endif
                            @if ($complaint->lat && $complaint->lng)
                                <p class="text-sm text-gray-600">Coördinaten: {{ $complaint->lat }}, {{ $complaint->lng }}</p>
                                <a href="https://www.openstreetmap.org/?mlat={{ $complaint->lat }}&mlon={{ $complaint->lng }}#map=18/{{ $complaint->lat }}/{{ $complaint->lng }}" target="_blank" class="inline-flex items-center mt-2 text-sm text-blue-600 hover:text-blue-800">
                                    Open in kaart
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status update -->
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Status bijwerken</h3>
                    <form action="{{ route('admin.complaints.update-status', $complaint) }}" method="POST" class="flex flex-wrap gap-4 items-center">
                        @csrf
                        @method('PATCH')
                        <div>
                            <label for="status" class="sr-only">Status</label>
                            <select name="status" id="status" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="open" {{ $complaint->status === 'open' ? 'selected' : '' }}>Open</option>
                                <option value="in_behandeling" {{ $complaint->status === 'in_behandeling' ? 'selected' : '' }}>In behandeling</option>
                                <option value="opgelost" {{ $complaint->status === 'opgelost' ? 'selected' : '' }}>Opgelost</option>
                            </select>
                        </div>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Status opslaan
                        </button>
                    </form>
                </div>
            </div>

            <!-- Attachments -->
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Bijlagen</h3>
                    @if ($complaint->attachments->count())
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @foreach ($complaint->attachments as $attachment)
                                <div class="border border-gray-200 rounded-lg p-3 flex flex-col">
                                    <div class="text-sm text-gray-500 mb-2">{{ $attachment->mime }}</div>
                                    @if ($attachment->isImage())
                                        <img src="{{ asset('storage/' . $attachment->path) }}" alt="Bijlage" class="rounded-md object-cover h-40 mb-3">
                                    @else
                                        <div class="flex-1 flex items-center justify-center bg-gray-50 rounded-md mb-3 text-gray-500 text-sm">
                                            Document
                                        </div>
                                    @endif
                                    <div class="mt-auto flex items-center justify-between text-sm text-gray-600">
                                        <span>{{ $attachment->human_readable_size }}</span>
                                        <a href="{{ asset('storage/' . $attachment->path) }}" target="_blank" class="text-blue-600 hover:text-blue-800">Bekijk</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">Geen bijlagen toegevoegd.</p>
                    @endif
                </div>
            </div>

            <!-- Notes -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 space-y-4">
                        <h3 class="text-lg font-medium text-gray-900">Notities</h3>
                        <form action="{{ route('admin.complaints.notes.store', $complaint) }}" method="POST" class="space-y-3">
                            @csrf
                            <div>
                                <label for="note_body" class="sr-only">Notitie</label>
                                <textarea name="body" id="note_body" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Voeg een interne notitie toe"></textarea>
                            </div>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Opslaan
                            </button>
                        </form>

                        <div class="space-y-4">
                            @forelse ($complaint->notes as $note)
                                <div class="border border-gray-200 rounded-md p-4">
                                    <div class="flex items-center justify-between mb-2 text-sm text-gray-500">
                                        <span>{{ $note->user?->name ?? 'Onbekende gebruiker' }}</span>
                                        <span>{{ $note->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-gray-800 text-sm">{{ $note->body }}</p>
                                    <form action="{{ route('admin.complaints.notes.destroy', $note) }}" method="POST" class="mt-3">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-red-600 hover:text-red-800" onclick="return confirm('Notitie verwijderen?');">
                                            Verwijderen
                                        </button>
                                    </form>
                                </div>
                            @empty
                                <p class="text-gray-500 text-sm">Nog geen notities toegevoegd.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Status history -->
                <div class="bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Statusgeschiedenis</h3>
                        @if ($complaint->statusHistories->count())
                            <ul class="space-y-3">
                                @foreach ($complaint->statusHistories as $history)
                                    <li class="border border-gray-200 rounded-md p-3">
                                        <div class="text-sm text-gray-600 flex items-center justify-between">
                                            <span>{{ $history->created_at->format('d-m-Y H:i') }}</span>
                                            <span>{{ $history->user?->name ?? 'Systeem' }}</span>
                                        </div>
                                        <p class="mt-1 text-sm text-gray-800">
                                            Status gewijzigd van <strong>{{ ucfirst(str_replace('_', ' ', $history->from)) }}</strong>
                                            naar <strong>{{ ucfirst(str_replace('_', ' ', $history->to)) }}</strong>
                                        </p>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-gray-500">Nog geen statuswijzigingen geregistreerd.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
