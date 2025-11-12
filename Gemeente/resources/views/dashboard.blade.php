<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - Gemeente Portal</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/gemeente-home.css') }}">
    <script src="{{ asset('js/chatbot.js') }}" defer></script>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="nav">
                <a class="logo" href="/">
                    <div class="logo-icon">G</div>
                    <div class="logo-text">
                        <span class="gemeente">Gemeente</span>
                        Portal
                    </div>
                </a>

                <nav class="header-nav">
                    <a class="nav-item" href="/">Home</a>
                    <a class="nav-item" href="{{ route('complaint.create') }}">Melding Doen</a>
                    @auth
                        @if(auth()->user()->hasRole('admin'))
                            <a class="nav-item" href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
                        @endif
                    @endauth
                </nav>

                <div class="header-actions">
                    <span class="text-gray-700 mr-4">{{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="btn btn-secondary">Uitloggen</button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <main>
        <section class="hero" style="min-height: 40vh; padding: 4rem 0;">
            <div class="container">
                <div class="hero-content" style="text-align: center;">
                    <h1 class="hero-title" style="font-size: 2.5rem; margin-bottom: 1rem;">
                        Welkom, {{ Auth::user()->name }}! 👋
                    </h1>
                    <p class="hero-subtitle">Je bent succesvol ingelogd in het Gemeente Portal</p>
                </div>
            </div>
        </section>

        <section class="services">
            <div class="container">
                <h2 class="section-title">Snelle Acties</h2>
                <p class="section-subtitle">Wat wilt u vandaag doen?</p>

                <div class="services-grid">
                    <div class="service-card">
                        <div class="service-icon" aria-hidden="true">📋</div>
                        <h3>Nieuwe Melding</h3>
                        <p>Dien een nieuwe klacht of melding in over problemen in uw buurt.</p>
                        <a class="service-link" href="{{ route('complaint.create') }}">Melding maken →</a>
                    </div>

                    <div class="service-card" style="border: 2px solid #0ea5e9; background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);">
                        <div class="service-icon" aria-hidden="true">📊</div>
                        <h3>Mijn Meldingen</h3>
                        <p>Bekijk de status van al uw ingediende meldingen en klachten.</p>
                        <a class="service-link" href="{{ route('complaint.search') }}" style="color: #06b6d4; border-color: #0ea5e9;">Bekijk meldingen →</a>
                    </div>

                    <div class="service-card">
                        <div class="service-icon" aria-hidden="true">👤</div>
                        <h3>Mijn Profiel</h3>
                        <p>Beheer uw persoonlijke gegevens en accountinstellingen.</p>
                        <a class="service-link" href="{{ route('profile.edit') }}">Profiel bewerken →</a>
                    </div>

                    @if(auth()->user()->hasRole('admin'))
                    <div class="service-card" style="background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%); color: white;">
                        <div class="service-icon" aria-hidden="true">🔐</div>
                        <h3 style="color: white;">Admin Dashboard</h3>
                        <p style="color: rgba(255, 255, 255, 0.9);">Toegang tot het beheerdersdashboard met kaart en alle functionaliteiten.</p>
                        <a class="service-link" href="{{ route('admin.dashboard') }}" style="color: white; border-color: white;">Naar Admin Dashboard →</a>
                    </div>
                    @endif
                </div>
            </div>
        </section>

        <section class="contact" id="contact" style="background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%); padding: 4rem 0; color: white;">
            <div class="container">
                <div class="contact-content" style="text-align: center;">
                    <h2 class="section-title" style="color: white; margin-bottom: 1rem;">Hulp Nodig?</h2>
                    <p class="section-subtitle" style="color: rgba(255, 255, 255, 0.9); margin-bottom: 2rem;">
                        Gebruik onze chatbot rechtsonder voor directe hulp.
                    </p>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer" style="background: #1a202c; color: white; padding: 3rem 0; text-align: center;">
        <div class="container">
            <div class="footer-content">
                <div class="logo" style="justify-content: center; margin-bottom: 1rem;">
                    <div class="logo-icon">G</div>
                    <div class="logo-text">
                        <span class="gemeente">Gemeente</span>
                        Portal
                    </div>
                </div>
                <p style="color: rgba(255, 255, 255, 0.7); margin-bottom: 1rem;">
                    © {{ date('Y') }} Gemeente Portal. Alle rechten voorbehouden.
                </p>
                <div style="display: flex; gap: 2rem; justify-content: center; flex-wrap: wrap;">
                    <a href="#" style="color: rgba(255, 255, 255, 0.7); hover:color: white;">Privacy</a>
                    <a href="#" style="color: rgba(255, 255, 255, 0.7); hover:color: white;">Cookies</a>
                    <a href="#" style="color: rgba(255, 255, 255, 0.7); hover:color: white;">Toegankelijkheid</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Chatbot wordt automatisch geladen via chatbot.js -->
</body>
</html>
