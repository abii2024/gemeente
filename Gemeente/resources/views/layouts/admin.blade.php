<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} | Admin</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <nav class="bg-white border-b border-gray-200">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center space-x-4 py-3 text-sm font-medium text-gray-600">
                        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'text-blue-600' : 'hover:text-blue-500' }}">Dashboard</a>
                        <a href="{{ route('admin.complaints.index') }}" class="{{ request()->routeIs('admin.complaints.index') ? 'text-blue-600' : 'hover:text-blue-500' }}">Klachten</a>
                        <a href="{{ route('admin.complaints.map') }}" class="{{ request()->routeIs('admin.complaints.map') ? 'text-blue-600' : 'hover:text-blue-500' }}">Kaart</a>
                        <a href="{{ route('admin.database.index') }}" class="{{ request()->routeIs('admin.database.*') ? 'text-blue-600' : 'hover:text-blue-500' }}">Database</a>
                    </div>
                </div>
            </nav>

            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
