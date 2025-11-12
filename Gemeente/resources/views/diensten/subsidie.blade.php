<!DOCTYPE html>@extends('layouts.app')

<html lang="nl">

<head>@section('content')

    <meta charset="UTF-8"><div class="container mx-auto px-4 py-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">    <div class="max-w-4xl mx-auto">

    <title>Subsidie Aanvragen - Gemeente Portal</title>        <!-- Header -->

    <link rel="stylesheet" href="{{ asset('css/diensten-modern.css') }}">        <div class="mb-8">

</head>            <a href="{{ route('home') }}" class="text-orange-600 hover:text-orange-800 flex items-center mb-4 transition-colors">

<body class="orange">                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">

    <div class="dienst-container">                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>

        <!-- Back Button -->                </svg>

        <a href="{{ route('home') }}" class="back-button">                Terug naar home

            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">            </a>

                <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>            <h1 class="text-4xl font-bold text-gray-900 mb-2">Subsidie Aanvragen</h1>

            </svg>            <p class="text-lg text-gray-600">Vraag financiële ondersteuning aan voor uw project</p>

            Terug naar home        </div>

        </a>

        <!-- Info Card -->

        <!-- Hero Section -->        <div class="bg-orange-50 border-l-4 border-orange-500 p-6 mb-8 rounded-r-lg shadow-sm">

        <div class="dienst-hero">            <div class="flex">

            <div class="hero-icon">                <div class="flex-shrink-0">

                <svg width="48" height="48" viewBox="0 0 24 24" fill="none">                    <svg class="h-6 w-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                    <path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2"/>                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>

                </svg>                    </svg>

            </div>                </div>

            <h1 class="hero-title">Subsidie Aanvragen</h1>                <div class="ml-3">

            <p class="hero-subtitle">Vraag financiële ondersteuning aan voor uw project</p>                    <h3 class="text-lg font-semibold text-orange-900 mb-2">Belangrijke informatie</h3>

        </div>                    <div class="text-sm text-orange-800 space-y-1">

                        <p>• <strong>Energiesubsidie:</strong> Tot €2.500 voor duurzame energie</p>

        <!-- Info Card -->                        <p>• <strong>Verenigingssubsidie:</strong> Tot €5.000 voor lokale verenigingen</p>

        <div class="info-card">                        <p>• <strong>Evenementensubsidie:</strong> Tot €1.000 voor culturele evenementen</p>

            <div class="info-icon">                        <p>• <strong>Behandeltijd:</strong> 4-8 weken</p>

                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">                    </div>

                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>                </div>

                    <path d="M12 16v-4M12 8h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>            </div>

                </svg>        </div>

            </div>

            <div class="info-content">        <!-- Form -->

                <h3 class="info-title">Belangrijke informatie</h3>        <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-100">

                <ul class="info-list">            <form action="{{ route('diensten.afspraak.store') }}" method="POST" class="space-y-8">

                    <li><strong>Energiesubsidie:</strong> Tot €2.500 voor duurzame energie</li>                @csrf

                    <li><strong>Verenigingssubsidie:</strong> Tot €5.000 voor lokale verenigingen</li>                <input type="hidden" name="dienst" value="Subsidie Aanvraag">

                    <li><strong>Evenementensubsidie:</strong> Tot €1.000 voor culturele evenementen</li>

                    <li><strong>Behandeltijd:</strong> 4-8 weken</li>                <!-- Aanvrager gegevens -->

                </ul>                <div>

            </div>                    <h2 class="text-2xl font-bold text-gray-900 mb-6 pb-3 border-b border-gray-200">Aanvrager Gegevens</h2>

        </div>                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div>

        <!-- Form -->                            <label class="block text-sm font-semibold text-gray-700 mb-2">

        <form action="{{ route('diensten.afspraak.store') }}" method="POST" class="dienst-form">                                Type Aanvrager <span class="text-red-500">*</span>

            @csrf                            </label>

            <input type="hidden" name="dienst" value="Subsidie Aanvraag">                            <select name="type_aanvrager" required

                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">

            <!-- Aanvrager gegevens -->                                <option value="">Selecteer type</option>

            <div class="form-section">                                <option value="particulier">Particulier</option>

                <h2 class="section-title">Aanvrager Gegevens</h2>                                <option value="vereniging">Vereniging/Stichting</option>

                <div class="form-group full-width">                                <option value="bedrijf">Bedrijf</option>

                    <label class="form-label">                            </select>

                        Type Aanvrager <span class="required">*</span>                        </div>

                    </label>                        <div>

                    <select name="type_aanvrager" required class="form-input">                            <label class="block text-sm font-semibold text-gray-700 mb-2">

                        <option value="">Selecteer type</option>                                Naam (organisatie of persoon) <span class="text-red-500">*</span>

                        <option value="particulier">Particulier</option>                            </label>

                        <option value="vereniging">Vereniging</option>                            <input type="text" name="naam" required

                        <option value="stichting">Stichting</option>                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all"

                        <option value="bedrijf">Bedrijf</option>                                placeholder="Naam">

                    </select>                        </div>

                </div>                        <div>

                <div class="form-grid">                            <label class="block text-sm font-semibold text-gray-700 mb-2">

                    <div class="form-group">                                Email <span class="text-red-500">*</span>

                        <label class="form-label">                            </label>

                            Naam <span class="required">*</span>                            <input type="email" name="email" required

                        </label>                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all"

                        <input type="text" name="naam" required class="form-input" placeholder="Voor- en achternaam of organisatienaam">                                placeholder="uw.email@voorbeeld.nl">

                    </div>                        </div>

                    <div class="form-group">                        <div>

                        <label class="form-label">                            <label class="block text-sm font-semibold text-gray-700 mb-2">

                            KvK Nummer (indien van toepassing)                                Telefoon <span class="text-red-500">*</span>

                        </label>                            </label>

                        <input type="text" name="kvk" class="form-input" placeholder="12345678">                            <input type="tel" name="telefoon" required

                    </div>                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all"

                    <div class="form-group full-width">                                placeholder="06-12345678">

                        <label class="form-label">                        </div>

                            Adres <span class="required">*</span>                        <div class="md:col-span-2">

                        </label>                            <label class="block text-sm font-semibold text-gray-700 mb-2">

                        <input type="text" name="adres" required class="form-input" placeholder="Straatnaam 123, 1234 AB Plaats">                                Adres <span class="text-red-500">*</span>

                    </div>                            </label>

                    <div class="form-group">                            <input type="text" name="adres" required

                        <label class="form-label">                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all"

                            Email <span class="required">*</span>                                placeholder="Straatnaam 123, 1234 AB Plaats">

                        </label>                        </div>

                        <input type="email" name="email" required class="form-input" placeholder="uw.email@voorbeeld.nl">                    </div>

                    </div>                </div>

                    <div class="form-group">

                        <label class="form-label">                <!-- Subsidie details -->

                            Telefoon <span class="required">*</span>                <div>

                        </label>                    <h2 class="text-2xl font-bold text-gray-900 mb-6 pb-3 border-b border-gray-200">Subsidie Gegevens</h2>

                        <input type="tel" name="telefoon" required class="form-input" placeholder="06-12345678">                    <div class="space-y-6">

                    </div>                        <div>

                </div>                            <label class="block text-sm font-semibold text-gray-700 mb-2">

            </div>                                Type Subsidie <span class="text-red-500">*</span>

                            </label>

            <!-- Subsidie details -->                            <select name="type_subsidie" required

            <div class="form-section">                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">

                <h2 class="section-title">Subsidie Details</h2>                                <option value="">Selecteer type subsidie</option>

                <div class="form-group full-width">                                <option value="energie">Energiebesparingssubsidie</option>

                    <label class="form-label">                                <option value="zonnepanelen">Zonnepanelensubsidie</option>

                        Type Subsidie <span class="required">*</span>                                <option value="vereniging">Verenigingssubsidie</option>

                    </label>                                <option value="evenement">Evenementensubsidie</option>

                    <select name="type_subsidie" required class="form-input">                                <option value="sport">Sportsubsidie</option>

                        <option value="">Selecteer subsidie type</option>                                <option value="cultuur">Cultuursubsidie</option>

                        <option value="energie">Energiesubsidie (tot €2.500)</option>                                <option value="welzijn">Welzijnssubsidie</option>

                        <option value="vereniging">Verenigingssubsidie (tot €5.000)</option>                                <option value="overig">Overig</option>

                        <option value="evenement">Evenementensubsidie (tot €1.000)</option>                            </select>

                        <option value="sport">Sportsubsidie (tot €3.000)</option>                        </div>

                        <option value="cultuur">Cultuursubsidie (tot €4.000)</option>                        <div>

                        <option value="overig">Overige subsidie</option>                            <label class="block text-sm font-semibold text-gray-700 mb-2">

                    </select>                                Titel Project <span class="text-red-500">*</span>

                </div>                            </label>

                <div class="form-group full-width">                            <input type="text" name="project_titel" required

                    <label class="form-label">                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all"

                        Project Titel <span class="required">*</span>                                placeholder="Korte titel van uw project">

                    </label>                        </div>

                    <input type="text" name="project_titel" required class="form-input" placeholder="Korte titel van uw project">                        <div>

                </div>                            <label class="block text-sm font-semibold text-gray-700 mb-2">

                <div class="form-group full-width">                                Projectomschrijving <span class="text-red-500">*</span>

                    <label class="form-label">                            </label>

                        Project Beschrijving <span class="required">*</span>                            <textarea name="project_omschrijving" rows="6" required

                    </label>                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all"

                    <textarea name="project_beschrijving" required rows="5" class="form-input"                                 placeholder="Beschrijf uw project in detail: doel, uitvoering, planning en verwachte resultaten..."></textarea>

                        placeholder="Beschrijf uw project, wat u wilt bereiken en waarom u subsidie nodig heeft..."></textarea>                        </div>

                    <p class="form-help">Minimaal 100 karakters</p>                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                </div>                            <div>

                <div class="form-grid">                                <label class="block text-sm font-semibold text-gray-700 mb-2">

                    <div class="form-group">                                    Totale Projectkosten <span class="text-red-500">*</span>

                        <label class="form-label">                                </label>

                            Gevraagd Bedrag <span class="required">*</span>                                <div class="relative">

                        </label>                                    <span class="absolute left-4 top-3 text-gray-500">€</span>

                        <input type="number" name="bedrag" required min="100" max="10000" step="50" class="form-input" placeholder="2500">                                    <input type="number" name="totale_kosten" required min="0" step="0.01"

                        <p class="form-help">In euro's</p>                                        class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all"

                    </div>                                        placeholder="0,00">

                    <div class="form-group">                                </div>

                        <label class="form-label">                            </div>

                            Startdatum Project <span class="required">*</span>                            <div>

                        </label>                                <label class="block text-sm font-semibold text-gray-700 mb-2">

                        <input type="date" name="startdatum" required class="form-input">                                    Gevraagd Subsidiebedrag <span class="text-red-500">*</span>

                    </div>                                </label>

                </div>                                <div class="relative">

            </div>                                    <span class="absolute left-4 top-3 text-gray-500">€</span>

                                    <input type="number" name="subsidiebedrag" required min="0" step="0.01"

            <!-- Budget & Planning -->                                        class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all"

            <div class="form-section">                                        placeholder="0,00">

                <h2 class="section-title">Budget & Planning</h2>                                </div>

                <div class="form-group full-width">                            </div>

                    <label class="form-label">                        </div>

                        Totale Projectkosten <span class="required">*</span>                    </div>

                    </label>                </div>

                    <input type="number" name="totale_kosten" required min="0" step="50" class="form-input" placeholder="5000">

                    <p class="form-help">In euro's</p>                <!-- Afspraak details -->

                </div>                <div>

                <div class="form-group full-width">                    <h2 class="text-2xl font-bold text-gray-900 mb-6 pb-3 border-b border-gray-200">Afspraak Plannen (Optioneel)</h2>

                    <label class="form-label">                    <p class="text-sm text-gray-600 mb-4">Plan een afspraak voor een toelichting op uw aanvraag</p>

                        Eigen Bijdrage <span class="required">*</span>                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    </label>                        <div>

                    <input type="number" name="eigen_bijdrage" required min="0" step="50" class="form-input" placeholder="1500">                            <label class="block text-sm font-semibold text-gray-700 mb-2">

                    <p class="form-help">Uw eigen financiële bijdrage in euro's</p>                                Gewenste Datum

                </div>                            </label>

                <div class="form-group full-width">                            <input type="date" name="datum" min="{{ date('Y-m-d', strtotime('+1 day')) }}"

                    <label class="form-label">                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">

                        Overige Financiering                        </div>

                    </label>                        <div>

                    <textarea name="overige_financiering" rows="3" class="form-input"                             <label class="block text-sm font-semibold text-gray-700 mb-2">

                        placeholder="Beschrijf eventuele andere financieringsbronnen..."></textarea>                                Gewenste Tijd

                </div>                            </label>

            </div>                            <select name="tijd"

                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">

            <!-- Afspraak details -->                                <option value="">Geen afspraak nodig</option>

            <div class="form-section">                                <option value="09:00">09:00</option>

                <h2 class="section-title">Afspraak voor Toelichting (optioneel)</h2>                                <option value="09:30">09:30</option>

                <div class="form-grid">                                <option value="10:00">10:00</option>

                    <div class="form-group">                                <option value="10:30">10:30</option>

                        <label class="form-label">                                <option value="11:00">11:00</option>

                            Gewenste Datum                                <option value="11:30">11:30</option>

                        </label>                                <option value="13:00">13:00</option>

                        <input type="date" name="datum" min="{{ date('Y-m-d', strtotime('+1 day')) }}" class="form-input">                                <option value="13:30">13:30</option>

                    </div>                                <option value="14:00">14:00</option>

                    <div class="form-group">                                <option value="14:30">14:30</option>

                        <label class="form-label">                                <option value="15:00">15:00</option>

                            Gewenste Tijd                                <option value="15:30">15:30</option>

                        </label>                                <option value="16:00">16:00</option>

                        <select name="tijd" class="form-input">                            </select>

                            <option value="">Geen afspraak nodig</option>                        </div>

                            <option value="09:00">09:00</option>                    </div>

                            <option value="09:30">09:30</option>                </div>

                            <option value="10:00">10:00</option>

                            <option value="10:30">10:30</option>                <!-- Opmerking -->

                            <option value="11:00">11:00</option>                <div>

                            <option value="11:30">11:30</option>                    <label class="block text-sm font-semibold text-gray-700 mb-2">

                            <option value="13:00">13:00</option>                        Aanvullende Informatie (optioneel)

                            <option value="13:30">13:30</option>                    </label>

                            <option value="14:00">14:00</option>                    <textarea name="opmerking" rows="4"

                            <option value="14:30">14:30</option>                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all"

                            <option value="15:00">15:00</option>                        placeholder="Eventuele aanvullende informatie of vragen..."></textarea>

                            <option value="15:30">15:30</option>                </div>

                            <option value="16:00">16:00</option>

                        </select>                <!-- Buttons -->

                    </div>                <div class="flex flex-col sm:flex-row justify-end gap-4 pt-6 border-t border-gray-200">

                </div>                    <a href="{{ route('home') }}"

            </div>                        class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 transition-colors text-center">

                        Annuleren

            <!-- Opmerking -->                    </a>

            <div class="form-section">                    <button type="submit"

                <div class="form-group full-width">                        class="px-6 py-3 bg-orange-600 text-white rounded-lg font-semibold hover:bg-orange-700 transition-colors shadow-md hover:shadow-lg">

                    <label class="form-label">                        Aanvraag Indienen

                        Opmerking (optioneel)                    </button>

                    </label>                </div>

                    <textarea name="opmerking" rows="4" class="form-input" placeholder="Eventuele opmerkingen of aanvullende informatie..."></textarea>            </form>

                </div>        </div>

            </div>    </div>

</div>

            <!-- Buttons -->@endsection

            <div class="form-actions">
                <a href="{{ route('home') }}" class="btn-secondary">
                    Annuleren
                </a>
                <button type="submit" class="btn-primary">
                    Subsidie Aanvragen
                </button>
            </div>
        </form>
    </div>
</body>
</html>
