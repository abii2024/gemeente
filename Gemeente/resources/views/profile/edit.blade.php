<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profiel Bewerken - Gemeente Portal</title>
    <link rel="stylesheet" href="{{ asset('css/gemeente-modern.css') }}">
    <script src="{{ asset('js/chatbot.js') }}" defer></script>
</head>
<body style="background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%); min-height: 100vh; padding: 2rem 1rem;">

    <div style="max-width: 900px; margin: 0 auto;">
        <!-- Header -->
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem;">
            <a href="{{ route('profile.show') }}" style="color: white; text-decoration: none; display: flex; align-items: center; gap: 0.5rem; font-size: 1rem; transition: opacity 0.2s;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                <span>Terug naar Profiel</span>
            </a>

            <a href="{{ route('dashboard') }}" style="color: white; text-decoration: none; font-size: 1rem;">Dashboard</a>
        </div>

        <!-- Title -->
        <div style="text-align: center; margin-bottom: 3rem;">
            <h1 style="font-size: 2.5rem; font-weight: 800; color: white; margin-bottom: 0.5rem;">
                Profiel Bewerken
            </h1>
            <p style="color: rgba(255, 255, 255, 0.9); font-size: 1.125rem;">
                Beheer uw persoonlijke gegevens en instellingen
            </p>
        </div>

        <!-- Profile Information -->
        <div style="background: white; border-radius: 16px; padding: 2rem; margin-bottom: 1.5rem; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);">
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 2px solid #e5e7eb;">
                <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #0ea5e9, #06b6d4); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    👤
                </div>
                <div>
                    <h2 style="font-size: 1.5rem; font-weight: 700; color: #1f2937; margin: 0;">Persoonlijke Informatie</h2>
                    <p style="color: #6b7280; font-size: 0.875rem; margin: 0;">Update uw naam en e-mailadres</p>
                </div>
            </div>

            @include('profile.partials.update-profile-information-form')
        </div>

        <!-- Password Update -->
        <div style="background: white; border-radius: 16px; padding: 2rem; margin-bottom: 1.5rem; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);">
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 2px solid #e5e7eb;">
                <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #f59e0b, #d97706); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    🔒
                </div>
                <div>
                    <h2 style="font-size: 1.5rem; font-weight: 700; color: #1f2937; margin: 0;">Wachtwoord Wijzigen</h2>
                    <p style="color: #6b7280; font-size: 0.875rem; margin: 0;">Zorg voor een sterk en veilig wachtwoord</p>
                </div>
            </div>

            @include('profile.partials.update-password-form')
        </div>

        <!-- Delete Account -->
        <div style="background: white; border-radius: 16px; padding: 2rem; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);">
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 2px solid #fee2e2;">
                <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #ef4444, #dc2626); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    ⚠️
                </div>
                <div>
                    <h2 style="font-size: 1.5rem; font-weight: 700; color: #1f2937; margin: 0;">Account Verwijderen</h2>
                    <p style="color: #6b7280; font-size: 0.875rem; margin: 0;">Verwijder permanent uw account en alle gegevens</p>
                </div>
            </div>

            @include('profile.partials.delete-user-form')
        </div>
    </div>

</body>
</html>
