<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Gemeente Portal</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('css/gemeente-home.css') }}">
    <script src="{{ asset('js/chatbot.js') }}" defer></script>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="nav">
                <a class="logo" href="#hero">
                    <div class="logo-icon">G</div>
                    <div class="logo-text">
                        <span class="gemeente">Gemeente</span>
                        Portal
                    </div>
                </a>

                <nav class="header-nav">
                    <a class="nav-item" href="#hero">Home</a>
                    <a class="nav-item" href="{{ route('complaint.create') }}">Melding Doen</a>
                    <a class="nav-item" href="#services">Diensten</a>
                    <a class="nav-item" href="#contact">Contact</a>
                </nav>

                @if (Route::has('login'))
                    <div class="header-actions">
                        @auth
                            <a class="btn btn-secondary" href="{{ url('/dashboard') }}">Dashboard</a>
                        @else
                            <a class="btn btn-secondary" href="{{ route('login') }}">Inloggen</a>
                            @if (Route::has('register'))
                                <a class="btn btn-primary" href="{{ route('register') }}">Registreren</a>
                            @endif
                        @endauth
                    </div>
                @endif
            </div>
        </div>
    </header>

    <main>
        <section class="hero" id="hero">
            <div class="container">
                <div class="hero-content">
                    <p class="section-subtitle">Uw digitale toegang tot alle gemeentelijke diensten. Snel, eenvoudig en 24/7 beschikbaar.</p>
                    <h1 class="hero-title">Hoe kunnen wij u helpen?</h1>
                    <p class="hero-subtitle">Meld problemen in uw buurt, vraag documenten aan, regel uw verhuizing of bekijk populaire diensten — allemaal binnen één portaal.</p>

                    <div class="hero-buttons">
                        <a class="btn btn-primary" href="{{ route('complaint.create') }}">Melding Doen</a>
                        <a class="btn btn-secondary" href="#services">Alle Diensten</a>
                    </div>
                </div>
            </div>
        </section>

        <section class="services" id="services">
            <div class="container">
                <h2 class="section-title">Populaire Diensten</h2>
                <p class="section-subtitle">De meest gebruikte gemeentelijke diensten, snel en eenvoudig toegankelijk.</p>

                <div class="services-grid">
                    <div class="service-card">
                        <div class="service-icon" aria-hidden="true">📋</div>
                        <h3>Melding Doen</h3>
                        <p>Meld problemen zoals kapotte straatverlichting, zwerfafval of overlast in uw buurt.</p>
                        <a class="service-link" href="{{ route('complaint.create') }}">Melding maken</a>
                    </div>

                    <div class="service-card">
                        <div class="service-icon" aria-hidden="true">🆔</div>
                        <h3>Paspoort &amp; ID</h3>
                        <p>Vraag paspoorten, Nederlandse identiteitskaarten en uittreksels eenvoudig online aan.</p>
                        <a class="service-link" href="#contact">Documenten aanvragen</a>
                    </div>

                    <div class="service-card">
                        <div class="service-icon" aria-hidden="true">🏠</div>
                        <h3>Verhuizing</h3>
                        <p>Geef uw verhuizing online door en regel alle benodigde formaliteiten in één keer.</p>
                        <a class="service-link" href="#contact">Verhuizing doorgeven</a>
                    </div>

                    <div class="service-card">
                        <div class="service-icon" aria-hidden="true">💰</div>
                        <h3>Belastingen</h3>
                        <p>Bekijk uw gemeentelijke aanslagen en betaal veilig online via het portaal.</p>
                        <a class="service-link" href="#contact">Belastingen betalen</a>
                    </div>

                    <div class="service-card">
                        <div class="service-icon" aria-hidden="true">🚗</div>
                        <h3>Parkeren</h3>
                        <p>Regel bewoners- en bezoekersvergunningen en beheer uw parkeervoorzieningen.</p>
                        <a class="service-link" href="#contact">Parkeren regelen</a>
                    </div>

                    <div class="service-card">
                        <div class="service-icon" aria-hidden="true">💼</div>
                        <h3>Uitkeringen</h3>
                        <p>Vind informatie over bijstand, toeslagen en andere vormen van financiële ondersteuning.</p>
                        <a class="service-link" href="#contact">Uitkeringen aanvragen</a>
                    </div>
                </div>
            </div>
        </section>

        <section class="contact" id="contact">
            <div class="container">
                <h2 class="section-title">Contact en Openingstijden</h2>
                <p class="section-subtitle">Onze medewerkers staan klaar om u te helpen. Neem contact op of bezoek ons stadskantoor.</p>

                <div class="contact-grid">
                    <div class="contact-card">
                        <h3>Contact</h3>
                        <p><strong>Telefoon:</strong> 14 010</p>
                        <p><strong>Email:</strong> info@gemeente.nl</p>
                        <p><strong>Adres:</strong> Hoofdstraat 1<br>1234 AB Gemeente</p>
                    </div>

                    <div class="contact-card">
                        <h3>Openingstijden</h3>
                        <p>Maandag t/m vrijdag: 08:30 - 17:00 uur</p>
                        <p>Zaterdag: 09:00 - 13:00 uur</p>
                        <p>Zondag: Gesloten</p>
                        <p><strong>Online diensten:</strong> 24/7 beschikbaar</p>
                    </div>

                    <div class="contact-card">
                        <h3>Extra ondersteuning</h3>
                        <p>Onze digitale assistent helpt u stap voor stap met veelgestelde vragen.</p>
                        <p><strong>Chat:</strong> Klik op de chatbot rechtsonder</p>
                        <p><strong>Loket:</strong> Maak indien nodig een afspraak via het klantcontactcentrum.</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h4>Gemeente Portal</h4>
                    <p>Uw digitale toegang tot gemeentelijke diensten, beschikbaar waar en wanneer u wilt.</p>
                </div>

                <div class="footer-section">
                    <h4>Diensten</h4>
                    <a href="{{ route('complaint.create') }}">Melding Doen</a>
                    <a href="#services">Alle Diensten</a>
                    <a href="#contact">Contact</a>
                </div>

                <div class="footer-section">
                    <h4>Volg ons</h4>
                    <a href="#">LinkedIn</a>
                    <a href="#">Facebook</a>
                    <a href="#">Twitter</a>
                </div>
            </div>

            <div class="footer-bottom">
                <p>© {{ now()->year }} Gemeente Portal. Alle rechten voorbehouden.</p>
            </div>
        </div>
    </footer>
</body>
</html>
