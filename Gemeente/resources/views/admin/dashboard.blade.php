<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard - Gemeente Portal</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/gemeente-modern.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="{{ asset('js/chatbot.js') }}" defer></script>
    <script src="{{ asset('js/moderne-animations.js') }}" defer></script>
    @include('admin.partials.styles')
</head>
<body class="bg-gradient">
    @include('admin.partials.header')

    <main style="min-height: 100vh; padding: 2rem 0;">
        <div class="hero-background">
            <div class="float-circle"></div>
            <div class="float-circle" style="animation-delay: 2s;"></div>
            <div class="float-circle" style="animation-delay: 4s;"></div>
        </div>

        <section style="padding: 3rem 0 2rem; position: relative;">
            <div class="container">
                <div style="text-align: center; max-width: 800px; margin: 0 auto;">
                    <div style="display: inline-flex; align-items: center; gap: 1rem; margin-bottom: 1rem; padding: 0.75rem 1.5rem; background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(10px); border-radius: 50px; border: 1px solid rgba(255, 255, 255, 0.2);">
                        <span style="font-size: 2rem;">🏛️</span>
                        <span style="color: white; font-weight: 600; font-size: 1rem;">Admin Dashboard</span>
                    </div>
                    <h1 style="font-size: clamp(2rem, 5vw, 3rem); font-weight: 800; margin-bottom: 1rem; color: white; text-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);">
                        Beheer & Overzicht
                    </h1>
                    <p style="font-size: clamp(1rem, 2.5vw, 1.25rem); color: rgba(255, 255, 255, 0.9); line-height: 1.6;">
                        Interactieve kaart, realtime statistieken en geavanceerde filters
                    </p>
                </div>
            </div>
        </section>

        <div class="container">
            <!-- Stats Cards with Glassmorphism -->
            <div class="stagger-children" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                <div style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(37, 99, 235, 0.05) 100%); backdrop-filter: blur(20px); padding: 2rem; border-radius: 20px; border: 1px solid rgba(59, 130, 246, 0.2); box-shadow: var(--shadow-lg); transition: all var(--transition-base); position: relative; overflow: hidden;"
                     onmouseover="this.style.transform='translateY(-8px)'; this.style.boxShadow='0 25px 50px -12px rgba(59, 130, 246, 0.25)'"
                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='var(--shadow-lg)'">
                    <div style="position: absolute; top: -20px; right: -20px; width: 100px; height: 100px; background: radial-gradient(circle, rgba(59, 130, 246, 0.15) 0%, transparent 70%); border-radius: 50%;"></div>
                    <div style="font-size: 2.5rem; font-weight: 800; color: var(--primary-600); margin-bottom: 0.5rem; position: relative;">{{ $stats['total_complaints'] }}</div>
                    <div style="font-size: 1rem; font-weight: 600; color: var(--neutral-700); text-transform: uppercase; letter-spacing: 0.05em; position: relative;">Totaal Klachten</div>
                </div>

                <div style="background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(220, 38, 38, 0.05) 100%); backdrop-filter: blur(20px); padding: 2rem; border-radius: 20px; border: 1px solid rgba(239, 68, 68, 0.2); box-shadow: var(--shadow-lg); transition: all var(--transition-base); position: relative; overflow: hidden;"
                     onmouseover="this.style.transform='translateY(-8px)'; this.style.boxShadow='0 25px 50px -12px rgba(239, 68, 68, 0.25)'"
                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='var(--shadow-lg)'">
                    <div style="position: absolute; top: -20px; right: -20px; width: 100px; height: 100px; background: radial-gradient(circle, rgba(239, 68, 68, 0.15) 0%, transparent 70%); border-radius: 50%;"></div>
                    <div style="font-size: 2.5rem; font-weight: 800; color: #EF4444; margin-bottom: 0.5rem; position: relative;">{{ $stats['open_complaints'] }}</div>
                    <div style="font-size: 1rem; font-weight: 600; color: var(--neutral-700); text-transform: uppercase; letter-spacing: 0.05em; position: relative;">Open</div>
                </div>

                <div style="background: linear-gradient(135deg, rgba(245, 158, 11, 0.1) 0%, rgba(217, 119, 6, 0.05) 100%); backdrop-filter: blur(20px); padding: 2rem; border-radius: 20px; border: 1px solid rgba(245, 158, 11, 0.2); box-shadow: var(--shadow-lg); transition: all var(--transition-base); position: relative; overflow: hidden;"
                     onmouseover="this.style.transform='translateY(-8px)'; this.style.boxShadow='0 25px 50px -12px rgba(245, 158, 11, 0.25)'"
                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='var(--shadow-lg)'">
                    <div style="position: absolute; top: -20px; right: -20px; width: 100px; height: 100px; background: radial-gradient(circle, rgba(245, 158, 11, 0.15) 0%, transparent 70%); border-radius: 50%;"></div>
                    <div style="font-size: 2.5rem; font-weight: 800; color: #F59E0B; margin-bottom: 0.5rem; position: relative;">{{ $stats['in_behandeling_complaints'] }}</div>
                    <div style="font-size: 1rem; font-weight: 600; color: var(--neutral-700); text-transform: uppercase; letter-spacing: 0.05em; position: relative;">In Behandeling</div>
                </div>

                <div style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(5, 150, 105, 0.05) 100%); backdrop-filter: blur(20px); padding: 2rem; border-radius: 20px; border: 1px solid rgba(16, 185, 129, 0.2); box-shadow: var(--shadow-lg); transition: all var(--transition-base); position: relative; overflow: hidden;"
                     onmouseover="this.style.transform='translateY(-8px)'; this.style.boxShadow='0 25px 50px -12px rgba(16, 185, 129, 0.25)'"
                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='var(--shadow-lg)'">
                    <div style="position: absolute; top: -20px; right: -20px; width: 100px; height: 100px; background: radial-gradient(circle, rgba(16, 185, 129, 0.15) 0%, transparent 70%); border-radius: 50%;"></div>
                    <div style="font-size: 2.5rem; font-weight: 800; color: #0ea5e9; margin-bottom: 0.5rem; position: relative;">{{ $stats['opgelost_complaints'] }}</div>
                    <div style="font-size: 1rem; font-weight: 600; color: var(--neutral-700); text-transform: uppercase; letter-spacing: 0.05em; position: relative;">Opgelost</div>
                </div>
            </div>

            <!-- Filter Card with Modern Design -->
            <div style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(20px) saturate(180%); padding: 2rem; border-radius: 24px; border: 1px solid rgba(255, 255, 255, 0.5); box-shadow: var(--shadow-xl); margin-bottom: 2rem;">
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem;">
                    <span style="font-size: 1.5rem;">🔍</span>
                    <h3 style="font-size: 1.5rem; font-weight: 700; color: var(--neutral-900); margin: 0;">Zoeken & Filteren</h3>
                </div>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem;">
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--neutral-700); font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em;">Zoek op ID</label>
                        <div style="display: flex; gap: 0.5rem;">
                            <input type="number" id="search-id" placeholder="Klacht ID"
                                   style="flex: 1; padding: 0.875rem 1rem; border: 2px solid var(--neutral-200); border-radius: 12px; font-size: 1rem; transition: all var(--transition-fast); background: white;"
                                   onfocus="this.style.borderColor='var(--primary-500)'; this.style.boxShadow='0 0 0 4px rgba(0, 102, 204, 0.1)'"
                                   onblur="this.style.borderColor='var(--neutral-200)'; this.style.boxShadow='none'">
                            <button onclick="searchById()"
                                    style="padding: 0.875rem 1.5rem; background: var(--primary-600); color: white; border: none; border-radius: 12px; font-weight: 600; cursor: pointer; transition: all var(--transition-fast); white-space: nowrap;"
                                    onmouseover="this.style.background='var(--primary-700)'; this.style.transform='translateY(-2px)'; this.style.boxShadow='var(--shadow-lg)'"
                                    onmouseout="this.style.background='var(--primary-600)'; this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                Zoek
                            </button>
                        </div>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--neutral-700); font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em;">Status</label>
                        <select id="filter-status" onchange="applyFilters()"
                                style="width: 100%; padding: 0.875rem 1rem; border: 2px solid var(--neutral-200); border-radius: 12px; font-size: 1rem; background: white; cursor: pointer; transition: all var(--transition-fast);"
                                onfocus="this.style.borderColor='var(--primary-500)'; this.style.boxShadow='0 0 0 4px rgba(0, 102, 204, 0.1)'"
                                onblur="this.style.borderColor='var(--neutral-200)'; this.style.boxShadow='none'">
                            <option value="">Alle Statussen</option>
                            <option value="open">Open</option>
                            <option value="in_progress">In Behandeling</option>
                            <option value="resolved">Opgelost</option>
                            <option value="closed">Gesloten</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--neutral-700); font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em;">Prioriteit</label>
                        <select id="filter-priority" onchange="applyFilters()"
                                style="width: 100%; padding: 0.875rem 1rem; border: 2px solid var(--neutral-200); border-radius: 12px; font-size: 1rem; background: white; cursor: pointer; transition: all var(--transition-fast);"
                                onfocus="this.style.borderColor='var(--primary-500)'; this.style.boxShadow='0 0 0 4px rgba(0, 102, 204, 0.1)'"
                                onblur="this.style.borderColor='var(--neutral-200)'; this.style.boxShadow='none'">
                            <option value="">Alle Prioriteiten</option>
                            <option value="low">Laag</option>
                            <option value="medium">Middel</option>
                            <option value="high">Hoog</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                    <div style="display: flex; align-items: flex-end;">
                        <button onclick="resetFilters()"
                                style="width: 100%; padding: 0.875rem 1.5rem; background: white; color: var(--neutral-700); border: 2px solid var(--neutral-300); border-radius: 12px; font-weight: 600; cursor: pointer; transition: all var(--transition-fast);"
                                onmouseover="this.style.background='var(--neutral-50)'; this.style.borderColor='var(--neutral-400)'"
                                onmouseout="this.style.background='white'; this.style.borderColor='var(--neutral-300)'">
                            Reset Filters
                        </button>
                    </div>
                </div>
            </div>

            <!-- Map Card with Modern Design -->
            <div style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(20px) saturate(180%); padding: 2rem; border-radius: 24px; border: 1px solid rgba(255, 255, 255, 0.5); box-shadow: var(--shadow-xl); margin-bottom: 2rem;">
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem;">
                    <span style="font-size: 1.5rem;">🗺️</span>
                    <h3 style="font-size: 1.5rem; font-weight: 700; color: var(--neutral-900); margin: 0;">Interactieve Kaart - Alle Klachten</h3>
                </div>
                <div id="map" style="height: 600px; width: 100%; border-radius: 16px; overflow: hidden; box-shadow: var(--shadow-lg);"></div>
                <div style="margin-top: 1.5rem; padding: 1.5rem; background: linear-gradient(135deg, rgba(0, 102, 204, 0.05) 0%, rgba(102, 179, 255, 0.05) 100%); border-radius: 16px; border: 1px solid rgba(0, 102, 204, 0.1);">
                    <p style="font-weight: 700; margin-bottom: 1rem; color: var(--neutral-900); font-size: 1rem; text-transform: uppercase; letter-spacing: 0.05em;">Legenda:</p>
                    <div style="display: flex; flex-wrap: wrap; gap: 1.5rem;">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <svg width="24" height="30" viewBox="0 0 24 36" style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));">
                                <path d="M12 0C7.03 0 3 4.03 3 9c0 6.75 9 18 9 18s9-11.25 9-18c0-4.97-4.03-9-9-9z" fill="#EF4444" stroke="#fff" stroke-width="1.5"/>
                            </svg>
                            <span style="font-weight: 600; color: var(--neutral-700);">Open</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <svg width="24" height="30" viewBox="0 0 24 36" style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));">
                                <path d="M12 0C7.03 0 3 4.03 3 9c0 6.75 9 18 9 18s9-11.25 9-18c0-4.97-4.03-9-9-9z" fill="#F59E0B" stroke="#fff" stroke-width="1.5"/>
                            </svg>
                            <span style="font-weight: 600; color: var(--neutral-700);">In Behandeling</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <svg width="24" height="30" viewBox="0 0 24 36" style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));">
                                <path d="M12 0C7.03 0 3 4.03 3 9c0 6.75 9 18 9 18s9-11.25 9-18c0-4.97-4.03-9-9-9z" fill="#10B981" stroke="#fff" stroke-width="1.5"/>
                            </svg>
                            <span style="font-weight: 600; color: var(--neutral-700);">Opgelost</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <svg width="24" height="30" viewBox="0 0 24 36" style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));">
                                <path d="M12 0C7.03 0 3 4.03 3 9c0 6.75 9 18 9 18s9-11.25 9-18c0-4.97-4.03-9-9-9z" fill="#6B7280" stroke="#fff" stroke-width="1.5"/>
                            </svg>
                            <span style="font-weight: 600; color: var(--neutral-700);">Gesloten</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Complaints Table with Modern Design -->
            <div style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(20px) saturate(180%); padding: 2rem; border-radius: 24px; border: 1px solid rgba(255, 255, 255, 0.5); box-shadow: var(--shadow-xl);">
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem;">
                    <span style="font-size: 1.5rem;">📋</span>
                    <h3 style="font-size: 1.5rem; font-weight: 700; color: var(--neutral-900); margin: 0;">5 Meest Recente Klachten</h3>
                </div>
                @if($recent_complaints->count() > 0)
                    <div style="overflow-x: auto; border-radius: 12px;">
                        <table style="width: 100%; border-collapse: separate; border-spacing: 0;">
                            <thead>
                                <tr style="background: linear-gradient(135deg, var(--primary-50) 0%, var(--primary-100) 100%);">
                                    <th style="padding: 1rem 1.25rem; text-align: left; font-weight: 700; color: var(--primary-900); font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 2px solid var(--primary-200); white-space: nowrap;">ID</th>
                                    <th style="padding: 1rem 1.25rem; text-align: left; font-weight: 700; color: var(--primary-900); font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 2px solid var(--primary-200);">Titel</th>
                                    <th style="padding: 1rem 1.25rem; text-align: left; font-weight: 700; color: var(--primary-900); font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 2px solid var(--primary-200); white-space: nowrap;">Categorie</th>
                                    <th style="padding: 1rem 1.25rem; text-align: left; font-weight: 700; color: var(--primary-900); font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 2px solid var(--primary-200); white-space: nowrap;">Prioriteit</th>
                                    <th style="padding: 1rem 1.25rem; text-align: left; font-weight: 700; color: var(--primary-900); font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 2px solid var(--primary-200); white-space: nowrap;">Status</th>
                                    <th style="padding: 1rem 1.25rem; text-align: left; font-weight: 700; color: var(--primary-900); font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 2px solid var(--primary-200);">Locatie</th>
                                    <th style="padding: 1rem 1.25rem; text-align: left; font-weight: 700; color: var(--primary-900); font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 2px solid var(--primary-200); white-space: nowrap;">Datum</th>
                                    <th style="padding: 1rem 1.25rem; text-align: left; font-weight: 700; color: var(--primary-900); font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 2px solid var(--primary-200); white-space: nowrap;">Acties</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recent_complaints as $complaint)
                                    <tr style="transition: all var(--transition-fast); background: white;"
                                        onmouseover="this.style.background='rgba(0, 102, 204, 0.03)'; this.style.transform='scale(1.01)'"
                                        onmouseout="this.style.background='white'; this.style.transform='scale(1)'">
                                        <td style="padding: 1.25rem; font-weight: 700; color: var(--primary-600); border-bottom: 1px solid var(--neutral-100); white-space: nowrap;">#{{ $complaint->id }}</td>
                                        <td style="padding: 1.25rem; color: var(--neutral-900); font-weight: 500; border-bottom: 1px solid var(--neutral-100);">{{ Str::limit($complaint->title, 40) }}</td>
                                        <td style="padding: 1.25rem; color: var(--neutral-700); border-bottom: 1px solid var(--neutral-100); white-space: nowrap;">{{ $complaint->category }}</td>
                                        <td style="padding: 1.25rem; border-bottom: 1px solid var(--neutral-100); white-space: nowrap;">
                                            @php
                                                $priorityColors = [
                                                    'urgent' => 'background: linear-gradient(135deg, #FEE2E2 0%, #FECACA 100%); color: #991B1B; border: 1px solid #FCA5A5;',
                                                    'high' => 'background: linear-gradient(135deg, #FED7AA 0%, #FDBA74 100%); color: #9A3412; border: 1px solid #FB923C;',
                                                    'medium' => 'background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%); color: #92400E; border: 1px solid #FCD34D;',
                                                    'low' => 'background: linear-gradient(135deg, #D1FAE5 0%, #A7F3D0 100%); color: #065F46; border: 1px solid #6EE7B7;'
                                                ];
                                                $priorityStyle = $priorityColors[$complaint->priority] ?? $priorityColors['low'];
                                            @endphp
                                            <span style="{{ $priorityStyle }} padding: 0.5rem 1rem; border-radius: 8px; font-weight: 600; font-size: 0.875rem; display: inline-block;">
                                                {{ ucfirst($complaint->priority) }}
                                            </span>
                                        </td>
                                        <td style="padding: 1.25rem; border-bottom: 1px solid var(--neutral-100); white-space: nowrap;">
                                            @php
                                                $statusColors = [
                                                    'open' => 'background: linear-gradient(135deg, #DBEAFE 0%, #BFDBFE 100%); color: #1E40AF; border: 1px solid #93C5FD;',
                                                    'in_progress' => 'background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%); color: #92400E; border: 1px solid #FCD34D;',
                                                    'resolved' => 'background: linear-gradient(135deg, #D1FAE5 0%, #A7F3D0 100%); color: #065F46; border: 1px solid #6EE7B7;',
                                                    'closed' => 'background: linear-gradient(135deg, #F3F4F6 0%, #E5E7EB 100%); color: #374151; border: 1px solid #D1D5DB;'
                                                ];
                                                $statusStyle = $statusColors[$complaint->status] ?? $statusColors['open'];
                                            @endphp
                                            <span style="{{ $statusStyle }} padding: 0.5rem 1rem; border-radius: 8px; font-weight: 600; font-size: 0.875rem; display: inline-block;">
                                                {{ ucfirst(str_replace('_', ' ', $complaint->status)) }}
                                            </span>
                                        </td>
                                        <td style="padding: 1.25rem; color: var(--neutral-700); border-bottom: 1px solid var(--neutral-100);">{{ Str::limit($complaint->location ?? 'Geen locatie', 30) }}</td>
                                        <td style="padding: 1.25rem; color: var(--neutral-600); font-size: 0.875rem; border-bottom: 1px solid var(--neutral-100); white-space: nowrap;">{{ $complaint->created_at->format('d-m-Y H:i') }}</td>
                                        <td style="padding: 1.25rem; border-bottom: 1px solid var(--neutral-100); white-space: nowrap;">
                                            <a href="{{ route('admin.complaints.show', $complaint) }}"
                                               style="color: var(--primary-600); font-weight: 600; text-decoration: none; transition: color var(--transition-fast);"
                                               onmouseover="this.style.color='var(--primary-700)'"
                                               onmouseout="this.style.color='var(--primary-600)'">
                                                Bekijk
                                            </a>
                                            @if($complaint->lat && $complaint->lng)
                                                <button onclick="zoomToComplaint({{ $complaint->lat }}, {{ $complaint->lng }}, {{ $complaint->id }})"
                                                        style="margin-left: 0.75rem; color: #0ea5e9; background: none; border: none; cursor: pointer; font-weight: 600; transition: color var(--transition-fast);"
                                                        onmouseover="this.style.color='#06b6d4'"
                                                        onmouseout="this.style.color='#0ea5e9'">
                                                    📍 Kaart
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div style="text-align: center; padding: 3rem;">
                        <div style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;">📋</div>
                        <p style="color: var(--neutral-600); font-size: 1.125rem; font-weight: 500;">Nog geen klachten ingediend.</p>
                    </div>
                @endif
            </div>
        </div>
    </main>

    @include('admin.partials.footer')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    @include('admin.partials.map-scripts')
</body>
</html>
