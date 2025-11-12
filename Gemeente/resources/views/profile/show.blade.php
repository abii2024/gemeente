<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mijn Profiel - Gemeente Portal</title>
    <link rel="stylesheet" href="{{ asset('css/gemeente-home.css') }}">
    <style>
        body {
            background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%);
            min-height: 100vh;
            padding: 2rem 1rem;
        }
        .profile-container {
            max-width: 800px;
            margin: 0 auto;
        }
        .profile-card {
            background: white;
            border-radius: 20px;
            padding: 3rem 2.5rem;
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.25);
            animation: fadeInUp 0.5s ease-out;
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .profile-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        .profile-avatar {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #0ea5e9, #06b6d4);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
            font-size: 3rem;
            color: white;
        }
        .profile-name {
            font-size: 2rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }
        .profile-role {
            display: inline-block;
            padding: 0.5rem 1.5rem;
            background: linear-gradient(135deg, #0ea5e9, #06b6d4);
            color: white;
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }
        .info-section {
            margin-bottom: 2.5rem;
        }
        .info-section h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #f3f4f6;
        }
        .info-row {
            display: flex;
            padding: 1rem 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            flex: 0 0 140px;
            font-weight: 600;
            color: #6b7280;
            font-size: 0.875rem;
        }
        .info-value {
            flex: 1;
            color: #1f2937;
            font-size: 0.95rem;
        }
        .actions {
            display: flex;
            gap: 1rem;
            margin-top: 2.5rem;
            padding-top: 2rem;
            border-top: 2px solid #f3f4f6;
        }
        .btn {
            flex: 1;
            padding: 1rem;
            border-radius: 10px;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            cursor: pointer;
            font-size: 0.95rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, #0ea5e9, #06b6d4);
            color: white;
            box-shadow: 0 4px 16px rgba(102, 126, 234, 0.3);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(102, 126, 234, 0.4);
        }
        .btn-secondary {
            background: #f3f4f6;
            color: #374151;
        }
        .btn-secondary:hover {
            background: #e5e7eb;
        }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: white;
            text-decoration: none;
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
            transition: opacity 0.2s;
        }
        .back-link:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <a href="{{ route('dashboard') }}" class="back-link">
            ← Terug naar Dashboard
        </a>

        <div class="profile-card">
            <div class="profile-header">
                <div class="profile-avatar">
                    👤
                </div>
                <h1 class="profile-name">{{ Auth::user()->name }}</h1>
                @if(Auth::user()->hasRole('admin'))
                    <span class="profile-role">🔐 Administrator</span>
                @else
                    <span class="profile-role" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8);">👤 Gebruiker</span>
                @endif
            </div>

            <div class="info-section">
                <h3>📋 Persoonlijke Informatie</h3>
                <div class="info-row">
                    <div class="info-label">Naam:</div>
                    <div class="info-value">{{ Auth::user()->name }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">E-mailadres:</div>
                    <div class="info-value">{{ Auth::user()->email }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Account type:</div>
                    <div class="info-value">
                        @if(Auth::user()->hasRole('admin'))
                            Administrator
                        @else
                            Standaard gebruiker
                        @endif
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Geregistreerd op:</div>
                    <div class="info-value">{{ Auth::user()->created_at->format('d-m-Y H:i') }}</div>
                </div>
            </div>

            <div class="info-section">
                <h3>📊 Account Statistieken</h3>
                <div class="info-row">
                    <div class="info-label">Laatste login:</div>
                    <div class="info-value">{{ Auth::user()->updated_at->format('d-m-Y H:i') }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Account status:</div>
                    <div class="info-value">
                        <span style="color: #0ea5e9; font-weight: 600;">✓ Actief</span>
                    </div>
                </div>
            </div>

            <div class="actions">
                <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                    ✏️ Profiel Bewerken
                </a>
                <form method="POST" action="{{ route('logout') }}" style="flex: 1;">
                    @csrf
                    <button type="submit" class="btn btn-secondary" style="width: 100%;">
                        🚪 Uitloggen
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
