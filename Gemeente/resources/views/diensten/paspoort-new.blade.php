<x-diensten-layout
    title="Paspoort Aanvragen"
    subtitle="Vraag eenvoudig uw nieuwe paspoort aan"
    theme="blue"
    dienst="Paspoort Aanvraag">

    <x-slot:infoItems>
        <p>• <strong>Kosten:</strong> €71,55 (18 jaar en ouder), €56,05 (onder 18 jaar)</p>
        <p>• <strong>Geldigheid:</strong> 10 jaar (18+), 5 jaar (onder 18)</p>
        <p>• <strong>Levertijd:</strong> 3-5 werkdagen</p>
        <p>• <strong>Meenemen:</strong> Geldig identiteitsbewijs en pasfoto</p>
    </x-slot:infoItems>

    <!-- Persoonlijke gegevens -->
    <div class="form-section">
        <h2 class="form-section-title">Persoonlijke Gegevens</h2>
        <div class="form-grid form-grid-2">
            <div>
                <label class="form-label">Voornaam <span class="required">*</span></label>
                <input type="text" name="voornaam" required class="form-input focus-blue" placeholder="Vul uw voornaam in">
            </div>
            <div>
                <label class="form-label">Achternaam <span class="required">*</span></label>
                <input type="text" name="achternaam" required class="form-input focus-blue" placeholder="Vul uw achternaam in">
            </div>
            <div>
                <label class="form-label">Geboortedatum <span class="required">*</span></label>
                <input type="date" name="geboortedatum" required class="form-input focus-blue">
            </div>
            <div>
                <label class="form-label">BSN <span class="required">*</span></label>
                <input type="text" name="bsn" required pattern="[0-9]{9}" maxlength="9" class="form-input focus-blue" placeholder="123456789">
                <p class="help-text">9 cijfers</p>
            </div>
        </div>
    </div>
</x-diensten-layout>
