<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Database Overzicht') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">Database Tabellen</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($tables as $tableName => $count)
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-900 capitalize">{{ str_replace('_', ' ', $tableName) }}</h4>
                                        <p class="text-2xl font-bold text-blue-600">{{ number_format($count) }}</p>
                                        <p class="text-sm text-gray-500">records</p>
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.database.table', $tableName) }}" 
                                           class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            Bekijk
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-8 p-4 bg-yellow-50 rounded-lg">
                        <h4 class="text-sm font-medium text-yellow-800 mb-2">Database Connectie Info</h4>
                        <div class="text-sm text-yellow-700">
                            <p><strong>Host:</strong> {{ config('database.connections.mysql.host') }}</p>
                            <p><strong>Database:</strong> {{ config('database.connections.mysql.database') }}</p>
                            <p><strong>Port:</strong> {{ config('database.connections.mysql.port') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>