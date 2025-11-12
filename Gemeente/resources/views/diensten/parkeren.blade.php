<!DOCTYPE html><!DOCTYPE html>

<html lang="nl"><html lang="nl">

<head><head>

    <meta charset="UTF-8">    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Parkeervergunning Aanvragen - Gemeente Portal</title>    <title>Parkeervergunning Aanvragen - Gemeente Portal</title>

    <link rel="stylesheet" href="{{ asset('css/diensten-modern.css') }}">    <link rel="stylesheet" href="{{ asset('css/diensten-modern.css') }}">

</head></head>

<body class="indigo"><body class="indigo">

    <div class="dienst-container">    <div class="dienst-container">

        <!-- Back Button -->        <!-- Back Button -->

        <a href="{{ route('home') }}" class="back-button">        <a href="{{ route('home') }}" class="back-button">

            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">

                <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>                <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>

            </svg>            </svg>

            Terug naar home            Terug naar home

        </a>        </a>



        <!-- Hero Section -->        <!-- Hero Section -->

        <div class="dienst-hero">        <div class="dienst-hero">

            <div class="hero-icon">            <div class="hero-icon">

                <svg width="48" height="48" viewBox="0 0 24 24" fill="none">                <svg width="48" height="48" viewBox="0 0 24 24" fill="none">

                    <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z" stroke="currentColor" stroke-width="2"/>                    <path d="M3 6h18M5 6v14a2 2 0 002 2h10a2 2 0 002-2V6M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2" stroke="currentColor" stroke-width="2"/>

                </svg>                </svg>

            </div>            </div>

            <h1 class="hero-title">Parkeervergunning Aanvragen</h1>            <h1 class="hero-title">Parkeervergunning Aanvragen</h1>

            <p class="hero-subtitle">Vraag uw parkeervergunning eenvoudig online aan</p>            <p class="hero-subtitle">Vraag uw parkeervergunning eenvoudig online aan</p>

        </div>        </div>



        <!-- Info Card -->        <!-- Info Card -->

        <div class="info-card">        <div class="info-card">

            <div class="info-icon">            <div class="info-icon">

                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">

                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>

                    <path d="M12 16v-4M12 8h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>                    <path d="M12 16v-4M12 8h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>

                </svg>                </svg>

            </div>            </div>

            <div class="info-content">            <div class="info-content">

                <h3 class="info-title">Belangrijke informatie</h3>                <h3 class="info-title">Belangrijke informatie</h3>

                <ul class="info-list">                <ul class="info-list">

                    <li><strong>Bewonersvergunning:</strong> €120,00 per jaar</li>                    <li><strong>Bewonersvergunning:</strong> €120,00 per jaar</li>

                    <li><strong>Tweede auto:</strong> €240,00 per jaar</li>                    <li><strong>Tweede auto:</strong> €240,00 per jaar</li>

                    <li><strong>Bezoekersvergunning:</strong> €50,00 per jaar (max 150 uur)</li>                    <li><strong>Bezoekersvergunning:</strong> €50,00 per jaar (max 150 uur)</li>

                    <li><strong>Geldigheid:</strong> 1 jaar, automatische verlenging mogelijk</li>                    <li><strong>Geldigheid:</strong> 1 jaar, automatische verlenging mogelijk</li>

                </ul>                </ul>

            </div>            </div>

        </div>        </div>



        <!-- Form -->        <!-- Form -->

        <form action="{{ route('diensten.afspraak.store') }}" method="POST" class="dienst-form">        <form action="{{ route('diensten.afspraak.store') }}" method="POST" class="dienst-form">

            @csrf            @csrf

            <input type="hidden" name="dienst" value="Parkeervergunning Aanvraag">            <input type="hidden" name="dienst" value="Parkeervergunning Aanvraag">



            <!-- Persoonlijke gegevens -->            <!-- Persoonlijke gegevens -->

            <div class="form-section">            <div class="form-section">

                <h2 class="section-title">Persoonlijke Gegevens</h2>                <h2 class="section-title">Persoonlijke Gegevens</h2>

                <div class="form-grid">                <div class="form-grid">

                    <div class="form-group">                    <div class="form-group">

                        <label class="form-label">                        <label class="form-label">

                            Voornaam <span class="required">*</span>                            Voornaam <span class="required">*</span>

                        </label>                        </label>

                        <input type="text" name="voornaam" required class="form-input" placeholder="Vul uw voornaam in">                        <input type="text" name="voornaam" required class="form-input" placeholder="Vul uw voornaam in">

                    </div>                    </div>

                    <div class="form-group">                    <div class="form-group">

                        <label class="form-label">                        <label class="form-label">

                            Achternaam <span class="required">*</span>                            Achternaam <span class="required">*</span>

                        </label>                        </label>

                        <input type="text" name="achternaam" required class="form-input" placeholder="Vul uw achternaam in">                        <input type="text" name="achternaam" required class="form-input" placeholder="Vul uw achternaam in">

                    </div>                    </div>

                    <div class="form-group full-width">                    <div class="form-group full-width">

                        <label class="form-label">                        <label class="form-label">

                            Woonadres <span class="required">*</span>                            Woonadres <span class="required">*</span>

                        </label>                        </label>

                        <input type="text" name="adres" required class="form-input" placeholder="Straatnaam 123, 1234 AB Plaats">                        <input type="text" name="adres" required class="form-input" placeholder="Straatnaam 123, 1234 AB Plaats">

                    </div>                    </div>

                    <div class="form-group">                    <div class="form-group">

                        <label class="form-label">                        <label class="form-label">

                            Email <span class="required">*</span>                            Email <span class="required">*</span>

                        </label>                        </label>

                        <input type="email" name="email" required class="form-input" placeholder="uw.email@voorbeeld.nl">                        <input type="email" name="email" required class="form-input" placeholder="uw.email@voorbeeld.nl">

                    </div>                    </div>

                    <div class="form-group">                    <div class="form-group">

                        <label class="form-label">                        <label class="form-label">

                            Telefoon <span class="required">*</span>                            Telefoon <span class="required">*</span>

                        </label>                        </label>

                        <input type="tel" name="telefoon" required class="form-input" placeholder="06-12345678">                        <input type="tel" name="telefoon" required class="form-input" placeholder="06-12345678">

                    </div>                    </div>

                </div>                </div>

            </div>            </div>



            <!-- Voertuig informatie -->                <!-- Voertuig details -->

            <div class="form-section">                <div>

                <h2 class="section-title">Voertuig Informatie</h2>                    <h2 class="text-2xl font-bold text-gray-900 mb-6 pb-3 border-b border-gray-200">Voertuig Gegevens</h2>

                <div class="form-group full-width">                    <div class="space-y-6">

                    <label class="form-label">                        <div>

                        Type Vergunning <span class="required">*</span>                            <label class="block text-sm font-semibold text-gray-700 mb-2">

                    </label>                                Type Vergunning <span class="text-red-500">*</span>

                    <select name="type_vergunning" required class="form-input">                            </label>

                        <option value="">Selecteer type vergunning</option>                            <select name="type_vergunning" required

                        <option value="bewoners_eerste">Bewonersvergunning (eerste auto) - €120,00/jaar</option>                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">

                        <option value="bewoners_tweede">Bewonersvergunning (tweede auto) - €240,00/jaar</option>                                <option value="">Selecteer type vergunning</option>

                        <option value="bezoekers">Bezoekersvergunning - €50,00/jaar</option>                                <option value="bewoners_eerste">Bewonersvergunning (eerste auto)</option>

                        <option value="bedrijf">Bedrijfsvergunning</option>                                <option value="bewoners_tweede">Bewonersvergunning (tweede auto)</option>

                    </select>                                <option value="bezoekers">Bezoekersvergunning</option>

                </div>                                <option value="bedrijf">Bedrijfsvergunning</option>

                <div class="form-grid">                            </select>

                    <div class="form-group">                        </div>

                        <label class="form-label">                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            Kenteken <span class="required">*</span>                            <div>

                        </label>                                <label class="block text-sm font-semibold text-gray-700 mb-2">

                        <input type="text" name="kenteken" required pattern="[A-Z0-9\-]{6,8}" maxlength="8"                                    Kenteken <span class="text-red-500">*</span>

                            class="form-input uppercase" placeholder="XX-XX-XX" style="text-transform: uppercase;">                                </label>

                        <p class="form-help">Nederlandse kentekenformaat</p>                                <input type="text" name="kenteken" required pattern="[A-Z0-9\-]{6,8}" maxlength="8"

                    </div>                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all uppercase"

                    <div class="form-group">                                    placeholder="XX-XX-XX">

                        <label class="form-label">                                <p class="text-xs text-gray-500 mt-1">Nederlandse kentekenformaat</p>

                            Merk & Model <span class="required">*</span>                            </div>

                        </label>                            <div>

                        <input type="text" name="voertuig" required class="form-input" placeholder="BMW 3-serie">                                <label class="block text-sm font-semibold text-gray-700 mb-2">

                    </div>                                    Merk & Model <span class="text-red-500">*</span>

                </div>                                </label>

            </div>                                <input type="text" name="voertuig" required

                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"

            <!-- Afspraak details -->                                    placeholder="BMW 3-serie">

            <div class="form-section">                            </div>

                <h2 class="section-title">Afspraak Plannen</h2>                        </div>

                <div class="form-grid">                    </div>

                    <div class="form-group">                </div>

                        <label class="form-label">

                            Gewenste Datum <span class="required">*</span>                <!-- Afspraak details -->

                        </label>                <div>

                        <input type="date" name="datum" required min="{{ date('Y-m-d', strtotime('+1 day')) }}" class="form-input">                    <h2 class="text-2xl font-bold text-gray-900 mb-6 pb-3 border-b border-gray-200">Afspraak Plannen</h2>

                    </div>                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div class="form-group">                        <div>

                        <label class="form-label">                            <label class="block text-sm font-semibold text-gray-700 mb-2">

                            Gewenste Tijd <span class="required">*</span>                                Gewenste Datum <span class="text-red-500">*</span>

                        </label>                            </label>

                        <select name="tijd" required class="form-input">                            <input type="date" name="datum" required min="{{ date('Y-m-d', strtotime('+1 day')) }}"

                            <option value="">Selecteer een tijd</option>                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">

                            <option value="09:00">09:00</option>                        </div>

                            <option value="09:30">09:30</option>                        <div>

                            <option value="10:00">10:00</option>                            <label class="block text-sm font-semibold text-gray-700 mb-2">

                            <option value="10:30">10:30</option>                                Gewenste Tijd <span class="text-red-500">*</span>

                            <option value="11:00">11:00</option>                            </label>

                            <option value="11:30">11:30</option>                            <select name="tijd" required

                            <option value="13:00">13:00</option>                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">

                            <option value="13:30">13:30</option>                                <option value="">Selecteer een tijd</option>

                            <option value="14:00">14:00</option>                                <option value="09:00">09:00</option>

                            <option value="14:30">14:30</option>                                <option value="09:30">09:30</option>

                            <option value="15:00">15:00</option>                                <option value="10:00">10:00</option>

                            <option value="15:30">15:30</option>                                <option value="10:30">10:30</option>

                            <option value="16:00">16:00</option>                                <option value="11:00">11:00</option>

                        </select>                                <option value="11:30">11:30</option>

                    </div>                                <option value="13:00">13:00</option>

                </div>                                <option value="13:30">13:30</option>

            </div>                                <option value="14:00">14:00</option>

                                <option value="14:30">14:30</option>

            <!-- Opmerking -->                                <option value="15:00">15:00</option>

            <div class="form-section">                                <option value="15:30">15:30</option>

                <div class="form-group full-width">                                <option value="16:00">16:00</option>

                    <label class="form-label">                            </select>

                        Opmerking (optioneel)                        </div>

                    </label>                    </div>

                    <textarea name="opmerking" rows="4" class="form-input" placeholder="Eventuele opmerkingen of vragen..."></textarea>                </div>

                </div>

            </div>                <!-- Opmerking -->

                <div>

            <!-- Buttons -->                    <label class="block text-sm font-semibold text-gray-700 mb-2">

            <div class="form-actions">                        Opmerking (optioneel)

                <a href="{{ route('home') }}" class="btn-secondary">                    </label>

                    Annuleren                    <textarea name="opmerking" rows="4"

                </a>                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"

                <button type="submit" class="btn-primary">                        placeholder="Eventuele opmerkingen of vragen..."></textarea>

                    Afspraak Bevestigen                </div>

                </button>

            </div>                <!-- Buttons -->

        </form>                <div class="flex flex-col sm:flex-row justify-end gap-4 pt-6 border-t border-gray-200">

    </div>                    <a href="{{ route('home') }}"

</body>                        class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 transition-colors text-center">

</html>                        Annuleren

                    </a>
                    <button type="submit"
                        class="px-6 py-3 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition-colors shadow-md hover:shadow-lg">
                        Afspraak Bevestigen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
