<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Gemeente Portal</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('css/gemeente-modern.css') }}">
    <script src="{{ asset('js/chatbot.js') }}" defer></script>
    <script src="{{ asset('js/moderne-animations.js') }}" defer></script>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="nav">
                <a class="logo" href="#hero">
                    <div class="logo-icon">G</div>
                    <div class="logo-text">
                        <span class="gemeente">Gemeente</span>
                        <span>Portal</span>
                    </div>
                </a>

                <nav class="header-nav">
                    <a class="nav-item active" href="#hero">Home</a>
                    <a class="nav-item" href="{{ route('complaint.create') }}">Melding Doen</a>
                    <a class="nav-item" href="#services">Diensten</a>
                    <a class="nav-item" href="#contact">Contact</a>

                    @if (Route::has('login'))
                        <div class="header-actions" style="display: flex; gap: 0.75rem;">
                            @auth
                                <a class="btn btn-ghost" href="{{ url('/dashboard') }}">Dashboard</a>
                            @else
                                <a class="btn btn-ghost" href="{{ route('login') }}">Inloggen</a>
                                @if (Route::has('register'))
                                    <a class="btn btn-primary" href="{{ route('register') }}">Registreren</a>
                                @endif
                            @endauth
                        </div>
                    @endif
                </nav>

                <button class="hamburger" aria-label="Toggle menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </header>

    <main>
        <section class="hero fade-in" id="hero">
            <div class="container">
                <div class="hero-content">
                    <h1>Uw Gemeente, Digitaal & Toegankelijk</h1>
                    <p>Meld problemen, vraag documenten aan, regel uw verhuizing — allemaal binnen één modern portaal. Snel, veilig en 24/7 beschikbaar.</p>

                    <div style="display: flex; gap: 1rem; justify-content: center; margin-top: 2rem; flex-wrap: wrap;">
                        <a class="btn btn-primary" href="{{ route('complaint.create') }}">
                            📋 Melding Doen
                        </a>
                        <a class="btn btn-secondary" href="#services">
                            Alle Diensten →
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <section style="padding: 4rem 0;" id="services">
            <div class="container">
                <div class="fade-in" style="text-align: center; max-width: 700px; margin: 0 auto 3rem;">
                    <h2 style="font-size: 2.5rem; font-weight: 800; margin-bottom: 1rem; color: var(--neutral-900);">
                        Populaire Diensten
                    </h2>
                    <p style="font-size: 1.125rem; color: var(--neutral-600);">
                        De meest gebruikte gemeentelijke diensten, snel en eenvoudig toegankelijk.
                    </p>
                </div>

                <div class="services stagger-children">
                    <div class="service-card">
                        <div class="service-icon">📋</div>
                        <h3>Melding Doen</h3>
                        <p>Meld problemen zoals kapotte straatverlichting, zwerfafval of overlast in uw buurt.</p>
                        <a class="btn btn-ghost" href="{{ route('complaint.create') }}" style="margin-top: 1rem;">Melding maken →</a>
                    </div>

                    <div class="service-card">
                        <div class="service-icon">🆔</div>
                        <h3>Paspoort & ID</h3>
                        <p>Vraag paspoorten, Nederlandse identiteitskaarten en uittreksels eenvoudig online aan.</p>
                        <a class="btn btn-ghost" href="{{ route('diensten.paspoort') }}" style="margin-top: 1rem;">Paspoort aanvragen →</a>
                    </div>

                    <div class="service-card">
                        <div class="service-icon">🚗</div>
                        <h3>Rijbewijs</h3>
                        <p>Vraag uw rijbewijs aan of verleng deze eenvoudig online via ons digitale loket.</p>
                        <a class="btn btn-ghost" href="{{ route('diensten.rijbewijs') }}" style="margin-top: 1rem;">Rijbewijs aanvragen →</a>
                    </div>

                    <div class="service-card">
                        <div class="service-icon">�</div>
                        <h3>Vergunningen</h3>
                        <p>Vraag vergunningen aan voor bouw, evenementen, horeca en meer.</p>
                        <a class="btn btn-ghost" href="{{ route('diensten.vergunning') }}" style="margin-top: 1rem;">Vergunning aanvragen →</a>
                    </div>

                    <div class="service-card">
                        <div class="service-icon">🅿️</div>
                        <h3>Parkeren</h3>
                        <p>Regel bewoners- en bezoekersvergunningen en beheer uw parkeervoorzieningen.</p>
                        <a class="btn btn-ghost" href="{{ route('diensten.parkeren') }}" style="margin-top: 1rem;">Parkeren regelen →</a>
                    </div>

                    <div class="service-card">
                        <div class="service-icon">�</div>
                        <h3>Subsidies</h3>
                        <p>Vraag financiële ondersteuning aan voor uw projecten of vereniging.</p>
                        <a class="btn btn-ghost" href="{{ route('diensten.subsidie') }}" style="margin-top: 1rem;">Subsidie aanvragen →</a>
                    </div>
                </div>
            </div>
        </section>

        <section class="slide-in-left" style="padding: 4rem 0; background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%);" id="contact">
            <div class="container">
                <div style="text-align: center; max-width: 700px; margin: 0 auto;">
                    <h2 style="font-size: 2.5rem; font-weight: 800; margin-bottom: 1rem; color: white;">
                        Hulp Nodig?
                    </h2>
                    <p style="font-size: 1.125rem; color: rgba(255, 255, 255, 0.9);">
                        Gebruik onze chatbot rechtsonder voor directe hulp.
                    </p>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="container">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 3rem; padding: 2rem 0;">
                <div>
                    <h4 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1rem; color: white;">Gemeente Portal</h4>
                    <p style="color: var(--neutral-400); line-height: 1.7;">Uw digitale toegang tot gemeentelijke diensten, beschikbaar waar en wanneer u wilt.</p>
                </div>

                <div>
                    <h4 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1rem; color: white;">Diensten</h4>
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <a href="{{ route('complaint.create') }}" style="color: var(--neutral-400); text-decoration: none; transition: color 0.2s;">Melding Doen</a>
                        <a href="#services" style="color: var(--neutral-400); text-decoration: none; transition: color 0.2s;">Alle Diensten</a>
                        <a href="#contact" style="color: var(--neutral-400); text-decoration: none; transition: color 0.2s;">Contact</a>
                    </div>
                </div>

                <div>
                    <h4 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1rem; color: white;">Volg ons</h4>
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <a href="#" style="color: var(--neutral-400); text-decoration: none; transition: color 0.2s;">LinkedIn</a>
                        <a href="#" style="color: var(--neutral-400); text-decoration: none; transition: color 0.2s;">Facebook</a>
                        <a href="#" style="color: var(--neutral-400); text-decoration: none; transition: color 0.2s;">Twitter</a>
                    </div>
                </div>
            </div>

            <div style="text-align: center; padding-top: 2rem; margin-top: 2rem; border-top: 1px solid rgba(255, 255, 255, 0.1);">
                <p style="color: var(--neutral-400); font-size: var(--text-sm);">© {{ now()->year }} Gemeente Portal. Alle rechten voorbehouden.</p>
            </div>
        </div>
    </footer>
</body>
</html>
