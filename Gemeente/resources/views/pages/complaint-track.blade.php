@php
use Illuminate\Support\Facades\Storage;
@endphp
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track je Klacht - Gemeente Portal</title>
    <link rel="stylesheet" href="{{ asset('css/gemeente-modern.css') }}">
    <style>
        body {
            background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%);
            min-height: 100vh;
            padding: 2rem 1rem;
        }
        .track-container {
            max-width: 800px;
            margin: 0 auto;
        }
        .track-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        .status-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.875rem;
        }
        .status-open { background: #dbeafe; color: #1e40af; }
        .status-in_progress { background: #fef3c7; color: #92400e; }
        .status-resolved { background: #d1fae5; color: #065f46; }
        .status-closed { background: #e5e7eb; color: #374151; }

        .timeline {
            position: relative;
            padding-left: 2rem;
            margin-top: 2rem;
        }
        .timeline::before {
            content: '';
            position: absolute;
            left: 8px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e5e7eb;
        }
        .timeline-item {
            position: relative;
            margin-bottom: 2rem;
        }
        .timeline-dot {
            position: absolute;
            left: -1.95rem;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #3b82f6;
            border: 3px solid white;
            box-shadow: 0 0 0 2px #3b82f6;
        }
        .timeline-content {
            background: #f9fafb;
            padding: 1rem;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="track-container">
        <div class="track-card">
            <!-- Header -->
            <div class="text-center mb-6">
                <h1 style="font-size: 2rem; font-weight: 700; color: #1f2937; margin-bottom: 0.5rem;">
                    Klacht Tracking
                </h1>
                <p style="color: #6b7280;">Volg de status van je melding #{{ $complaint->id }}</p>
            </div>

            <!-- Status -->
            <div style="text-align: center; margin-bottom: 2rem;">
                <span class="status-badge status-{{ $complaint->status }}">
                    @if($complaint->status === 'open')
                        📋 Open
                    @elseif($complaint->status === 'in_progress')
                        ⚙️ In Behandeling
                    @elseif($complaint->status === 'resolved')
                        ✅ Opgelost
                    @elseif($complaint->status === 'closed')
                        🔒 Gesloten
                    @endif
                </span>
            </div>

            <!-- Complaint Details -->
            <div style="background: #f9fafb; padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem;">
                <h2 style="font-size: 1.25rem; font-weight: 600; color: #1f2937; margin-bottom: 1rem;">
                    {{ $complaint->title }}
                </h2>
                <p style="color: #4b5563; margin-bottom: 1rem;">
                    {{ $complaint->description }}
                </p>

                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-top: 1rem;">
                    <div>
                        <p style="font-size: 0.875rem; color: #6b7280;">Categorie</p>
                        <p style="font-weight: 600; color: #1f2937;">{{ ucfirst(str_replace('_', ' ', $complaint->category)) }}</p>
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; color: #6b7280;">Prioriteit</p>
                        <p style="font-weight: 600; color: #1f2937;">
                            @if($complaint->priority === 'low') 🟢 Laag
                            @elseif($complaint->priority === 'medium') 🟡 Normaal
                            @elseif($complaint->priority === 'high') 🟠 Hoog
                            @elseif($complaint->priority === 'urgent') 🔴 Urgent
                            @endif
                        </p>
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; color: #6b7280;">Ingediend op</p>
                        <p style="font-weight: 600; color: #1f2937;">{{ $complaint->created_at->format('d-m-Y H:i') }}</p>
                    </div>
                    @if($complaint->location)
                    <div>
                        <p style="font-size: 0.875rem; color: #6b7280;">Locatie</p>
                        <p style="font-weight: 600; color: #1f2937;">{{ $complaint->location }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Photos -->
            @if($complaint->attachments && $complaint->attachments->count() > 0)
            <div style="margin-bottom: 2rem;">
                <h3 style="font-size: 1.125rem; font-weight: 600; color: #1f2937; margin-bottom: 1rem;">
                    📷 Bijgevoegde Foto's ({{ $complaint->attachments->count() }})
                </h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 0.5rem;">
                    @foreach($complaint->attachments as $attachment)
                        <a href="{{ Storage::url($attachment->path) }}" target="_blank">
                            <img src="{{ Storage::url($attachment->path) }}" alt="Foto"
                                 style="width: 100%; height: 120px; object-fit: cover; border-radius: 8px;">
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Timeline -->
            <div>
                <h3 style="font-size: 1.125rem; font-weight: 600; color: #1f2937; margin-bottom: 1rem;">
                    📅 Tijdlijn
                </h3>
                <div class="timeline">
                    <!-- Created -->
                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <div class="timeline-content">
                            <p style="font-weight: 600; color: #1f2937; margin-bottom: 0.25rem;">Klacht Ingediend</p>
                            <p style="font-size: 0.875rem; color: #6b7280;">{{ $complaint->created_at->format('d-m-Y H:i') }}</p>
                            <p style="font-size: 0.875rem; color: #4b5563; margin-top: 0.5rem;">
                                Uw melding is ontvangen en wacht op behandeling.
                            </p>
                        </div>
                    </div>

                    <!-- In Progress -->
                    @if(in_array($complaint->status, ['in_progress', 'resolved', 'closed']))
                    <div class="timeline-item">
                        <div class="timeline-dot" style="background: #f59e0b; box-shadow: 0 0 0 2px #f59e0b;"></div>
                        <div class="timeline-content">
                            <p style="font-weight: 600; color: #1f2937; margin-bottom: 0.25rem;">In Behandeling</p>
                            <p style="font-size: 0.875rem; color: #6b7280;">{{ $complaint->updated_at->format('d-m-Y H:i') }}</p>
                            <p style="font-size: 0.875rem; color: #4b5563; margin-top: 0.5rem;">
                                We zijn bezig met het oplossen van uw melding.
                            </p>
                        </div>
                    </div>
                    @endif

                    <!-- Resolved -->
                    @if(in_array($complaint->status, ['resolved', 'closed']))
                    <div class="timeline-item">
                        <div class="timeline-dot" style="background: #0ea5e9; box-shadow: 0 0 0 2px #0ea5e9;"></div>
                        <div class="timeline-content">
                            <p style="font-weight: 600; color: #1f2937; margin-bottom: 0.25rem;">Opgelost</p>
                            <p style="font-size: 0.875rem; color: #6b7280;">
                                @if($complaint->resolved_at)
                                    {{ $complaint->resolved_at->format('d-m-Y H:i') }}
                                @else
                                    {{ $complaint->updated_at->format('d-m-Y H:i') }}
                                @endif
                            </p>
                            <p style="font-size: 0.875rem; color: #4b5563; margin-top: 0.5rem;">
                                Uw melding is afgehandeld.
                            </p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #e5e7eb; text-align: center;">
                <p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 1rem;">
                    Vragen over deze melding? Neem contact op via {{ $complaint->reporter_email }}
                </p>
                <a href="{{ route('home') }}"
                   style="display: inline-block; padding: 0.75rem 1.5rem; background: #3b82f6; color: white; border-radius: 8px; text-decoration: none; font-weight: 600;">
                    🏠 Terug naar Home
                </a>
            </div>
        </div>
    </div>
</body>
</html>
