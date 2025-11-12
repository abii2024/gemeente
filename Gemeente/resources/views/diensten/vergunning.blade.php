<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vergunning Aanvragen - Gemeente Portal</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/diensten-modern.css') }}">
</head>
<body class="purple">
    <div class="diensten-page">
        <div class="page-header">
            <a href="{{ route('home') }}" class="back-btn">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Terug naar home
            </a>
            <h1 class="page-title">Vergunning Aanvragen</h1>
            <p class="page-subtitle">Vraag een vergunning aan voor bouw, evenement of bedrijf</p>
        </div>

        <div class="info-box purple">
            <h3>📋 Belangrijke informatie</h3>
            <div class="info-list">
                <p>• <strong>Bouwvergunning:</strong> €150 - €500 (afhankelijk van project)</p>
                <p>• <strong>Evenementenvergunning:</strong> €50 - €200</p>
                <p>• <strong>Horecavergunning:</strong> €200 - €400</p>
                <p>• <strong>Behandeltijd:</strong> 2-8 weken (afhankelijk van type)</p>
            </div>
        </div>

        <div class="form-card">
            <form action="{{ route('diensten.afspraak.store') }}" method="POST">
                @csrf
                <input type="hidden" name="dienst" value="Vergunning Aanvraag">

                <h2 class="section-title">Persoonlijke Gegevens</h2>
                <div class="form-row">
                    <div class="form-group">
                        <label>Voornaam <span class="required">*</span></label>
                        <input type="text" name="voornaam" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Achternaam <span class="required">*</span></label>
                        <input type="text" name="achternaam" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Email <span class="required">*</span></label>
                        <input type="email" name="email" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Telefoon <span class="required">*</span></label>
                        <input type="tel" name="telefoon" required class="form-control">
                    </div>
                </div>

                <h2 class="section-title">Vergunning Gegevens</h2>
                <div class="form-row">
                    <div class="form-group">
                        <label>Type Vergunning <span class="required">*</span></label>
                        <select name="type_vergunning" required class="form-control">
                            <option value="">Selecteer type vergunning</option>
                            <option value="bouwvergunning">Bouwvergunning</option>
                            <option value="evenementenvergunning">Evenementenvergunning</option>
                            <option value="horecavergunning">Horecavergunning</option>
                            <option value="terrassen">Terrassenvergunning</option>
                            <option value="kapvergunning">Kapvergunning (bomen)</option>
                            <option value="sloopvergunning">Sloopvergunning</option>
                            <option value="overig">Overig</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Adres/Locatie <span class="required">*</span></label>
                        <input type="text" name="locatie" required class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label>Beschrijving Project <span class="required">*</span></label>
                    <textarea name="beschrijving" rows="4" required class="form-control"></textarea>
                </div>

                <h2 class="section-title">Afspraak Plannen</h2>
                <div class="form-row">
                    <div class="form-group">
                        <label>Gewenste Datum <span class="required">*</span></label>
                        <input type="date" name="datum" required min="{{ date('Y-m-d', strtotime('+1 day')) }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Gewenste Tijd <span class="required">*</span></label>
                        <select name="tijd" required class="form-control">
                            <option value="">Selecteer een tijd</option>
                            <option value="09:00">09:00</option>
                            <option value="10:00">10:00</option>
                            <option value="11:00">11:00</option>
                            <option value="13:00">13:00</option>
                            <option value="14:00">14:00</option>
                            <option value="15:00">15:00</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Opmerking (optioneel)</label>
                    <textarea name="opmerking" rows="4" class="form-control"></textarea>
                </div>

                <div class="btn-group">
                    <a href="{{ route('home') }}" class="btn btn-secondary">Annuleren</a>
                    <button type="submit" class="btn btn-primary">Afspraak Bevestigen</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
