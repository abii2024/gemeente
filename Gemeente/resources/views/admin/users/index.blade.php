<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gebruikersbeheer - Gemeente Admin</title>
    <link rel="stylesheet" href="{{ asset('css/gemeente-modern.css') }}">
    <script src="{{ asset('js/chatbot.js') }}" defer></script>
</head>
<body style="background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%); min-height: 100vh; padding: 2rem 1rem;">

    <div style="max-width: 1200px; margin: 0 auto;">
        <!-- Header -->
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem;">
            <a href="{{ route('admin.dashboard') }}" style="color: white; text-decoration: none; display: flex; align-items: center; gap: 0.5rem; font-size: 1rem;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                <span>Terug naar Dashboard</span>
            </a>
        </div>

        <!-- Title & Add Button -->
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem;">
            <div>
                <h1 style="font-size: 2.5rem; font-weight: 800; color: white; margin: 0 0 0.5rem 0;">
                    👥 Gebruikersbeheer
                </h1>
                <p style="color: rgba(255, 255, 255, 0.9); font-size: 1.125rem; margin: 0;">
                    Beheer gebruikers en hun toegangsrechten
                </p>
            </div>

            <a
                href="{{ route('admin.users.create') }}"
                style="padding: 0.875rem 1.75rem; background: white; color: #0ea5e9; border-radius: 12px; font-weight: 600; text-decoration: none; display: flex; align-items: center; gap: 0.5rem; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); transition: transform 0.2s;"
                onmouseover="this.style.transform='translateY(-2px)'"
                onmouseout="this.style.transform='translateY(0)'"
            >
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 5v14M5 12h14"/>
                </svg>
                Nieuwe Gebruiker
            </a>
        </div>

        <!-- Success Message -->
        @if (session('success'))
            <div style="background: #d1fae5; border: 2px solid #0ea5e9; border-radius: 12px; padding: 1rem 1.5rem; margin-bottom: 2rem; display: flex; align-items: center; gap: 0.75rem;">
                <span style="font-size: 1.5rem;">✅</span>
                <span style="color: #065f46; font-weight: 600;">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div style="background: #fee2e2; border: 2px solid #ef4444; border-radius: 12px; padding: 1rem 1.5rem; margin-bottom: 2rem; display: flex; align-items: center; gap: 0.75rem;">
                <span style="font-size: 1.5rem;">⚠️</span>
                <span style="color: #991b1b; font-weight: 600;">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Users Table -->
        <div style="background: white; border-radius: 16px; padding: 0; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1); overflow: hidden;">

            @if($users->isEmpty())
                <div style="padding: 4rem; text-align: center;">
                    <div style="font-size: 4rem; margin-bottom: 1rem;">👥</div>
                    <h3 style="font-size: 1.5rem; font-weight: 700; color: #374151; margin-bottom: 0.5rem;">
                        Geen gebruikers gevonden
                    </h3>
                    <p style="color: #6b7280; margin-bottom: 2rem;">
                        Begin met het toevoegen van een nieuwe gebruiker
                    </p>
                    <a
                        href="{{ route('admin.users.create') }}"
                        style="padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #0ea5e9, #06b6d4); color: white; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-block;"
                    >
                        Eerste Gebruiker Toevoegen
                    </a>
                </div>
            @else
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: linear-gradient(135deg, #0ea5e9, #06b6d4); color: white;">
                            <th style="padding: 1rem 1.5rem; text-align: left; font-weight: 600;">Gebruiker</th>
                            <th style="padding: 1rem 1.5rem; text-align: left; font-weight: 600;">E-mail</th>
                            <th style="padding: 1rem 1.5rem; text-align: left; font-weight: 600;">Rol</th>
                            <th style="padding: 1rem 1.5rem; text-align: left; font-weight: 600;">Aangemaakt</th>
                            <th style="padding: 1rem 1.5rem; text-align: right; font-weight: 600;">Acties</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                <td style="padding: 1rem 1.5rem;">
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #0ea5e9, #06b6d4); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600;">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div style="font-weight: 600; color: #374151;">{{ $user->name }}</div>
                                            @if($user->id === auth()->id())
                                                <span style="font-size: 0.75rem; color: #0ea5e9; font-weight: 600;">👤 Jij</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 1rem 1.5rem; color: #6b7280;">
                                    {{ $user->email }}
                                </td>
                                <td style="padding: 1rem 1.5rem;">
                                    @if($user->hasRole('admin'))
                                        <span style="padding: 0.25rem 0.75rem; background: #dbeafe; color: #1e40af; border-radius: 6px; font-size: 0.875rem; font-weight: 600;">
                                            🔐 Admin
                                        </span>
                                    @else
                                        <span style="padding: 0.25rem 0.75rem; background: #f3f4f6; color: #374151; border-radius: 6px; font-size: 0.875rem; font-weight: 600;">
                                            👤 Gebruiker
                                        </span>
                                    @endif
                                </td>
                                <td style="padding: 1rem 1.5rem; color: #6b7280; font-size: 0.875rem;">
                                    {{ $user->created_at->format('d-m-Y') }}
                                </td>
                                <td style="padding: 1rem 1.5rem; text-align: right;">
                                    <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                        <a
                                            href="{{ route('admin.users.edit', $user) }}"
                                            style="padding: 0.5rem 1rem; background: #dbeafe; color: #1e40af; border-radius: 6px; text-decoration: none; font-size: 0.875rem; font-weight: 600; transition: background 0.2s;"
                                            onmouseover="this.style.background='#bfdbfe'"
                                            onmouseout="this.style.background='#dbeafe'"
                                        >
                                            ✏️ Bewerken
                                        </a>

                                        @if($user->id !== auth()->id())
                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" style="display: inline;" onsubmit="return confirm('Weet je zeker dat je deze gebruiker wilt verwijderen?')">
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    type="submit"
                                                    style="padding: 0.5rem 1rem; background: #fee2e2; color: #991b1b; border: none; border-radius: 6px; font-size: 0.875rem; font-weight: 600; cursor: pointer; transition: background 0.2s;"
                                                    onmouseover="this.style.background='#fecaca'"
                                                    onmouseout="this.style.background='#fee2e2'"
                                                >
                                                    🗑️ Verwijderen
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Pagination -->
                @if($users->hasPages())
                    <div style="padding: 1.5rem; background: #f9fafb; border-top: 1px solid #e5e7eb;">
                        {{ $users->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>

</body>
</html>
