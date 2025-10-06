<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} | Admin</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="{{ asset('css/gemeente-modern.css') }}">
        <script src="{{ asset('js/chatbot.js') }}" defer></script>
        <script src="{{ asset('js/moderne-animations.js') }}" defer></script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gradient">
        <div class="min-h-screen">
            @include('admin.partials.header')

            @isset($header)
                <header style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(20px) saturate(180%); box-shadow: var(--shadow-md); border-bottom: 1px solid rgba(255, 255, 255, 0.3);">
                    <div class="container" style="padding: 2rem 0;">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main>
                {{ $slot }}
            </main>

            @include('admin.partials.footer')
        </div>
    </body>
</html>
