<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Database Tabel: {{ ucfirst(str_replace('_', ' ', $table)) }}
            </h2>
            <a href="{{ route('admin.database.index') }}" 
               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Terug naar Overzicht
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($data->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        @foreach($columns as $column)
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                {{ $column }}
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($data as $row)
                                        <tr>
                                            @foreach($columns as $column)
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    @php
                                                        $value = $row->$column;
                                                        if (is_string($value) && strlen($value) > 50) {
                                                            echo Str::limit($value, 50);
                                                        } elseif (is_null($value)) {
                                                            echo '<span class="text-gray-400">NULL</span>';
                                                        } else {
                                                            echo $value;
                                                        }
                                                    @endphp
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6">
                            {{ $data->links() }}
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">Geen data gevonden in deze tabel.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
