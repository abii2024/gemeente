<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track je Melding - Gemeente Portal</title>
    <link rel="stylesheet" href="{{ asset('css/gemeente-modern.css') }}">
    <style>
        body {
            background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .search-card {
            background: white;
            border-radius: 20px;
            padding: 3rem 2.5rem;
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.25);
            max-width: 520px;
            width: 100%;
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
        .form-group {
            margin-bottom: 2rem;
            position: relative;
        }
        .form-label {
            display: block;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            letter-spacing: 0.025em;
        }
        .form-input {
            width: 100%;
            padding: 1rem 1.25rem;
            padding-top: 0.75rem;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-family: system-ui, -apple-system, sans-serif;
            background: #fafafa;
            color: #1f2937;
        }
        .form-input::placeholder {
            color: #9ca3af;
            opacity: 0.7;
        }
        .form-input:hover {
            border-color: #cbd5e1;
            background: white;
        }
        .form-input:focus {
            outline: none;
            border-color: #0ea5e9;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            transform: translateY(-1px);
            background: white;
        }
        .form-input:focus::placeholder {
            opacity: 0.5;
        }
        .help-text {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.8125rem;
            color: #6b7280;
            margin-top: 0.5rem;
            line-height: 1.4;
        }
        .btn-submit {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.05rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 16px rgba(102, 126, 234, 0.3);
            margin-top: 0.5rem;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(102, 126, 234, 0.4);
        }
        .btn-submit:active {
            transform: translateY(0);
        }
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
    </style>
</head>
<body>
    <div class="search-card">
        <div style="text-align: center; margin-bottom: 2.5rem;">
            <div style="width: 64px; height: 64px; background: linear-gradient(135deg, #0ea5e9, #06b6d4); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; box-shadow: 0 8px 16px rgba(102, 126, 234, 0.3);">
                <span style="font-size: 2rem;">🔍</span>
            </div>
            <h1 style="font-size: 2rem; font-weight: 700; color: #1f2937; margin-bottom: 0.5rem;">
                Track je Melding
            </h1>
            <p style="color: #6b7280; font-size: 1rem;">Volg de status van je ingediende melding</p>
        </div>

        @if(session('error'))
            <div class="alert alert-error">
                <strong>⚠️ Let op:</strong> {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('complaint.track') }}" method="GET">
            <div class="form-group">
                <label for="id" class="form-label">MELDING NUMMER</label>
                <input type="number"
                       id="id"
                       name="id"
                       class="form-input"
                       placeholder=""
                       required
                       min="1"
                       value="{{ old('id', request('id')) }}"
                       autocomplete="off">
            </div>

            <div class="form-group">
                <label for="email" class="form-label">E-MAILADRES</label>
                <input type="email"
                       id="email"
                       name="email"
                       class="form-input"
                       placeholder=""
                       required
                       value="{{ old('email', request('email')) }}"
                       autocomplete="email">
            </div>

            <button type="submit" class="btn-submit">
                🔍 Zoek Melding
            </button>
        </form>

        <div style="margin-top: 2.5rem; padding-top: 2rem; border-top: 2px solid #f3f4f6;">
            <div style="text-align: center; margin-bottom: 1.5rem;">
                <p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 1rem;">
                    Heb je nog geen melding ingediend?
                </p>
                <a href="{{ route('complaint.create') }}"
                   style="display: inline-block; padding: 0.875rem 1.75rem; background: linear-gradient(135deg, #0ea5e9, #06b6d4); color: white; border-radius: 8px; text-decoration: none; font-weight: 600; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3); transition: transform 0.2s;"
                   onmouseover="this.style.transform='translateY(-2px)'"
                   onmouseout="this.style.transform='translateY(0)'">
                    ➕ Dien een Melding in
                </a>
            </div>

            <div style="text-align: center;">
                <a href="{{ route('home') }}"
                   style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; color: #6b7280; text-decoration: none; font-size: 0.875rem; transition: color 0.2s;"
                   onmouseover="this.style.color='#1f2937'"
                   onmouseout="this.style.color='#6b7280'">
                    🏠 Terug naar Home
                </a>
            </div>
        </div>
    </div>
</body>
</html>
