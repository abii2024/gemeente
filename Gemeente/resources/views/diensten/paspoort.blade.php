<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paspoort Aanvragen - Gemeente Portal</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/diensten-modern.css') }}">
</head>
<body class="blue">
    <div class="diensten-page">
        <div class="page-header">
            <a href="{{ route('home') }}" class="back-btn">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Terug naar home
            </a>
            <h1 class="page-title">Paspoort Aanvragen</h1>
            <p class="page-subtitle">Vraag eenvoudig uw nieuwe paspoort aan</p>
        </div>

        <div class="info-box blue">
            <h3>📋 Belangrijke informatie</h3>
            <div class="info-list">
                <p>• <strong>Kosten:</strong> €71,55 (18 jaar en ouder), €56,05 (onder 18 jaar)</p>
                <p>• <strong>Geldigheid:</strong> 10 jaar (18+), 5 jaar (onder 18)</p>
                <p>• <strong>Levertijd:</strong> 3-5 werkdagen</p>
                <p>• <strong>Meenemen:</strong> Geldig identiteitsbewijs en pasfoto</p>
            </div>
        </div>

        <div class="form-card">
            <form action="{{ route('diensten.afspraak.store') }}" method="POST">
                @csrf
                <input type="hidden" name="dienst" value="Paspoort Aanvraag">

                <h2 class="section-title">Persoonlijke Gegevens</h2>
                <div class="form-row">
                    <div class="form-group">
                        <label>Voornaam <span class="required">*</span></label>
                        <input type="text" name="voornaam" required class="form-control" placeholder="Vul uw voornaam in">
                    </div>
                    <div class="form-group">
                        <label>Achternaam <span class="required">*</span></label>
                        <input type="text" name="achternaam" required class="form-control" placeholder="Vul uw achternaam in">
                    </div>
                    <div class="form-group">
                        <label>Geboortedatum <span class="required">*</span></label>
                        <input type="date" name="geboortedatum" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label>BSN <span class="required">*</span></label>
                        <input type="text" name="bsn" required pattern="[0-9]{9}" maxlength="9" class="form-control" placeholder="123456789">
                        <p class="help-text">9 cijfers</p>
                    </div>
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
                            <option value="09:30">09:30</option>
                            <option value="10:00">10:00</option>
                            <option value="10:30">10:30</option>
                            <option value="11:00">11:00</option>
                            <option value="11:30">11:30</option>
                            <option value="13:00">13:00</option>
                            <option value="13:30">13:30</option>
                            <option value="14:00">14:00</option>
                            <option value="14:30">14:30</option>
                            <option value="15:00">15:00</option>
                            <option value="15:30">15:30</option>
                            <option value="16:00">16:00</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Opmerking (optioneel)</label>
                    <textarea name="opmerking" rows="4" class="form-control" placeholder="Eventuele opmerkingen of vragen..."></textarea>
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
