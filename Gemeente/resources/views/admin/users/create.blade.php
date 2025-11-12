<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Nieuwe Gebruiker Toevoegen - Gemeente Admin</title>
    <link rel="stylesheet" href="{{ asset('css/gemeente-modern.css') }}">
    <script src="{{ asset('js/chatbot.js') }}" defer></script>
</head>
<body style="background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%); min-height: 100vh; padding: 2rem 1rem;">

    <div style="max-width: 700px; margin: 0 auto;">
        <!-- Header -->
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem;">
            <a href="{{ route('admin.users.index') }}" style="color: white; text-decoration: none; display: flex; align-items: center; gap: 0.5rem; font-size: 1rem; transition: opacity 0.2s;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                <span>Terug naar Gebruikers</span>
            </a>

            <a href="{{ route('admin.dashboard') }}" style="color: white; text-decoration: none; font-size: 1rem;">Dashboard</a>
        </div>

        <!-- Title -->
        <div style="text-align: center; margin-bottom: 3rem;">
            <div style="width: 80px; height: 80px; background: rgba(255, 255, 255, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 2.5rem;">
                👥
            </div>
            <h1 style="font-size: 2.5rem; font-weight: 800; color: white; margin-bottom: 0.5rem;">
                Nieuwe Gebruiker
            </h1>
            <p style="color: rgba(255, 255, 255, 0.9); font-size: 1.125rem;">
                Voeg een nieuwe admin of gebruiker toe aan het systeem
            </p>
        </div>

        <!-- Form Card -->
        <div style="background: white; border-radius: 16px; padding: 2.5rem; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);">

            @if ($errors->any())
                <div style="background: #fee2e2; border: 1px solid #fecaca; border-radius: 12px; padding: 1rem; margin-bottom: 1.5rem;">
                    <div style="display: flex; gap: 0.75rem; align-items: start;">
                        <span style="font-size: 1.25rem;">⚠️</span>
                        <div>
                            <h3 style="color: #991b1b; font-weight: 600; margin: 0 0 0.5rem 0;">Er zijn fouten opgetreden:</h3>
                            <ul style="color: #dc2626; margin: 0; padding-left: 1.25rem;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf

                <!-- Name -->
                <div style="margin-bottom: 1.5rem;">
                    <label for="name" style="display: block; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">
                        Naam <span style="color: #ef4444;">*</span>
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem; transition: border-color 0.2s;"
                        onfocus="this.style.borderColor='#0ea5e9'"
                        onblur="this.style.borderColor='#e5e7eb'"
                    >
                </div>

                <!-- Email -->
                <div style="margin-bottom: 1.5rem;">
                    <label for="email" style="display: block; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">
                        E-mailadres <span style="color: #ef4444;">*</span>
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem; transition: border-color 0.2s;"
                        onfocus="this.style.borderColor='#0ea5e9'"
                        onblur="this.style.borderColor='#e5e7eb'"
                    >
                </div>

                <!-- Role -->
                <div style="margin-bottom: 1.5rem;">
                    <label for="role" style="display: block; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">
                        Rol <span style="color: #ef4444;">*</span>
                    </label>
                    <select
                        id="role"
                        name="role"
                        required
                        style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem; transition: border-color 0.2s; background: white;"
                        onfocus="this.style.borderColor='#0ea5e9'"
                        onblur="this.style.borderColor='#e5e7eb'"
                    >
                        <option value="">Selecteer een rol...</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>🔐 Administrator</option>
                        <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>👤 Gebruiker</option>
                    </select>
                    <p style="color: #6b7280; font-size: 0.875rem; margin-top: 0.5rem;">
                        Administrators hebben volledige toegang tot het systeem
                    </p>
                </div>

                <!-- Password -->
                <div style="margin-bottom: 1.5rem;">
                    <label for="password" style="display: block; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">
                        Wachtwoord <span style="color: #ef4444;">*</span>
                    </label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem; transition: border-color 0.2s;"
                        onfocus="this.style.borderColor='#0ea5e9'"
                        onblur="this.style.borderColor='#e5e7eb'"
                    >
                    <p style="color: #6b7280; font-size: 0.875rem; margin-top: 0.5rem;">
                        Minimaal 8 tekens
                    </p>
                </div>

                <!-- Password Confirmation -->
                <div style="margin-bottom: 2rem;">
                    <label for="password_confirmation" style="display: block; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">
                        Bevestig Wachtwoord <span style="color: #ef4444;">*</span>
                    </label>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        required
                        style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem; transition: border-color 0.2s;"
                        onfocus="this.style.borderColor='#0ea5e9'"
                        onblur="this.style.borderColor='#e5e7eb'"
                    >
                </div>

                <!-- Buttons -->
                <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                    <a
                        href="{{ route('admin.users.index') }}"
                        style="padding: 0.75rem 1.5rem; border: 2px solid #e5e7eb; border-radius: 8px; font-weight: 600; color: #374151; text-decoration: none; transition: all 0.2s;"
                        onmouseover="this.style.background='#f9fafb'"
                        onmouseout="this.style.background='white'"
                    >
                        Annuleren
                    </a>
                    <button
                        type="submit"
                        style="padding: 0.75rem 2rem; background: linear-gradient(135deg, #0ea5e9, #06b6d4); color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 1rem; cursor: pointer; transition: transform 0.2s; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);"
                        onmouseover="this.style.transform='translateY(-2px)'"
                        onmouseout="this.style.transform='translateY(0)'"
                    >
                        ✅ Gebruiker Aanmaken
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
