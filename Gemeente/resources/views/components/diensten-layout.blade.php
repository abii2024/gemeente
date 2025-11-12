@props(['title', 'subtitle', 'infoItems', 'theme' => 'blue', 'dienst'])

@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/diensten-forms.css') }}">

<div class="diensten-container">
    <!-- Header -->
    <div class="diensten-header">
        <a href="{{ route('home') }}" class="back-link">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Terug naar home
        </a>
        <h1 class="diensten-title">{{ $title }}</h1>
        <p class="diensten-subtitle">{{ $subtitle }}</p>
    </div>

    <!-- Info Alert -->
    <div class="info-alert info-alert-{{ $theme }}">
        <div class="info-alert-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div class="info-alert-content">
            <h3>Belangrijke informatie</h3>
            <div class="info-alert-list">
                {!! $infoItems !!}
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="diensten-form-card">
        <form action="{{ route('diensten.afspraak.store') }}" method="POST">
            @csrf
            <input type="hidden" name="dienst" value="{{ $dienst }}">

            {{ $slot }}

            <!-- Afspraak details -->
            <div class="form-section">
                <h2 class="form-section-title">Afspraak Plannen</h2>
                <div class="form-grid form-grid-2">
                    <div>
                        <label class="form-label">Gewenste Datum <span class="required">*</span></label>
                        <input type="date" name="datum" required min="{{ date('Y-m-d', strtotime('+1 day')) }}" class="form-input focus-{{ $theme }}">
                    </div>
                    <div>
                        <label class="form-label">Gewenste Tijd <span class="required">*</span></label>
                        <select name="tijd" required class="form-select focus-{{ $theme }}">
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
            </div>

            <!-- Opmerking -->
            <div class="form-section">
                <label class="form-label">Opmerking (optioneel)</label>
                <textarea name="opmerking" rows="4" class="form-textarea focus-{{ $theme }}" placeholder="Eventuele opmerkingen of vragen..."></textarea>
            </div>

            <!-- Buttons -->
            <div class="form-actions">
                <a href="{{ route('home') }}" class="btn btn-secondary">Annuleren</a>
                <button type="submit" class="btn btn-{{ $theme }}">Afspraak Bevestigen</button>
            </div>
        </form>
    </div>
</div>
@endsection
