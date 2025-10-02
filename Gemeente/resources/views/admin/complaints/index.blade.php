<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Klachtenbeheer
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 md:items-end">
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700">Zoekterm of ID</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Bijv. 123 of Damrak"
                                   class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" id="status" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Alle</option>
                                <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                                <option value="in_behandeling" {{ request('status') === 'in_behandeling' ? 'selected' : '' }}>In behandeling</option>
                                <option value="opgelost" {{ request('status') === 'opgelost' ? 'selected' : '' }}>Opgelost</option>
                            </select>
                        </div>
                        <div class="md:col-span-2 flex space-x-3">
                            <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Zoeken
                            </button>
                            <a href="{{ route('admin.complaints.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                                Reset
                            </a>
                            <a href="{{ route('admin.complaints.map') }}" class="inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                Kaartweergave
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if ($complaints->count())
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Titel</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categorie</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Urgentie</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aangemaakt</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acties</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($complaints as $complaint)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#{{ $complaint->id }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900">{{ \Illuminate\Support\Str::limit($complaint->title, 60) }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-500">{{ ucfirst(str_replace('_', ' ', $complaint->category)) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php($statusColors = ['open' => 'bg-red-100 text-red-700', 'in_behandeling' => 'bg-yellow-100 text-yellow-700', 'opgelost' => 'bg-green-100 text-green-700'])
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$complaint->status] ?? 'bg-gray-100 text-gray-700' }}">
                                                    {{ ucfirst(str_replace('_', ' ', $complaint->status)) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500">
                                                @php($priorityLabels = ['low' => 'Laag', 'medium' => 'Normaal', 'high' => 'Hoog', 'urgent' => 'Urgent'])
                                                {{ $priorityLabels[$complaint->priority] ?? 'Onbekend' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $complaint->created_at->format('d-m-Y H:i') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                                <a href="{{ route('admin.complaints.show', $complaint) }}" class="text-blue-600 hover:text-blue-900">Bekijken</a>
                                                <a href="{{ route('admin.complaints.edit', $complaint) }}" class="text-yellow-600 hover:text-yellow-700">Bewerken</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6">
                            {{ $complaints->withQueryString()->links() }}
                        </div>
                    @else
                        <p class="text-gray-500">Geen klachten gevonden.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
