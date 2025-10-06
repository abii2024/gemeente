<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Klachten Kaart - Gemeente Portal</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/gemeente-modern.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="{{ asset('js/chatbot.js') }}" defer></script>
    @include('admin.partials.styles')
</head>
<body>
    @include('admin.partials.header')

    <main style="background: #F3F4F6; min-height: 100vh; padding: 2rem 0;">
        <section style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 3rem 0; margin-bottom: 2rem;">
            <div class="container">
                <div style="text-align: center; color: white;">
                    <h1 style="font-size: 2.5rem; font-weight: 700; margin-bottom: 0.5rem;">🗺️ Klachten Kaart</h1>
                    <p style="font-size: 1.125rem; opacity: 0.9;">Alle klachten op de kaart met status filtering</p>
                </div>
            </div>
        </section>

        <div class="container">
            <!-- Map Controls -->
            <div class="filter-card">
                <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem;">
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <div>
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #374151;">Status Filter:</label>
                            <select id="statusFilter" style="padding: 0.5rem; border: 1px solid #D1D5DB; border-radius: 8px;">
                                <option value="all">Alle</option>
                                <option value="open">Open</option>
                                <option value="in_progress">In Behandeling</option>
                                <option value="resolved">Opgelost</option>
                                <option value="closed">Gesloten</option>
                            </select>
                        </div>
                        <div style="padding-top: 1.75rem;">
                            <button id="refreshMap" class="btn-primary">
                                🔄 Vernieuwen
                            </button>
                        </div>
                    </div>

                    <div style="display: flex; gap: 1.5rem; flex-wrap: wrap;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="width: 16px; height: 16px; background: #EF4444; border-radius: 50%; display: inline-block;"></span>
                            <span style="font-size: 0.875rem;">Open ({{ $complaints->where('status', 'open')->count() }})</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="width: 16px; height: 16px; background: #EAB308; border-radius: 50%; display: inline-block;"></span>
                            <span style="font-size: 0.875rem;">In Behandeling ({{ $complaints->where('status', 'in_progress')->count() }})</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="width: 16px; height: 16px; background: #10B981; border-radius: 50%; display: inline-block;"></span>
                            <span style="font-size: 0.875rem;">Opgelost ({{ $complaints->where('status', 'resolved')->count() }})</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="width: 16px; height: 16px; background: #6B7280; border-radius: 50%; display: inline-block;"></span>
                            <span style="font-size: 0.875rem;">Gesloten ({{ $complaints->where('status', 'closed')->count() }})</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Map Container -->
            <div class="map-card">
                <h3 class="section-title">📍 Interactieve Kaart</h3>
                <div id="map" style="height: 700px; width: 100%; border-radius: 12px; overflow: hidden;"></div>
            </div>

            <!-- Complaints List -->
            <div class="table-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h3 class="section-title" style="margin-bottom: 0;">📋 Alle Klachten ({{ $complaints->count() }})</h3>
                    <button onclick="toggleList()" class="btn-secondary" id="toggleListBtn">
                        Toon Lijst
                    </button>
                </div>

                <div id="complaintsList" style="display: none; margin-top: 1.5rem;">
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1rem;">
                        @foreach($complaints as $complaint)
                            <div class="stat-card complaint-item"
                                 data-lat="{{ $complaint->lat }}"
                                 data-lng="{{ $complaint->lng }}"
                                 data-id="{{ $complaint->id }}"
                                 style="cursor: pointer; transition: all 0.2s;">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.75rem;">
                                    <h4 style="font-weight: 600; color: #1F2937; font-size: 0.95rem;">{{ $complaint->title }}</h4>
                                    @php
                                        $statusColors = [
                                            'open' => 'background: #FEE2E2; color: #991B1B;',
                                            'in_progress' => 'background: #FEF3C7; color: #92400E;',
                                            'resolved' => 'background: #D1FAE5; color: #065F46;',
                                            'closed' => 'background: #F3F4F6; color: #374151;'
                                        ];
                                        $statusStyle = $statusColors[$complaint->status] ?? $statusColors['open'];
                                    @endphp
                                    <span class="badge" style="{{ $statusStyle }}">
                                        {{ ucfirst(str_replace('_', ' ', $complaint->status)) }}
                                    </span>
                                </div>
                                <p style="color: #6B7280; font-size: 0.875rem; margin-bottom: 0.5rem;">
                                    {{ Str::limit($complaint->description, 80) }}
                                </p>
                                <p style="color: #9CA3AF; font-size: 0.75rem;">
                                    📅 {{ $complaint->created_at->format('d-m-Y H:i') }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </main>

    @include('admin.partials.footer')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        let map;
        let markers = [];
        const complaintsData = @json($complaints);

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize map
            map = L.map('map').setView([52.3676, 4.9041], 10);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(map);

            // Create custom icon function
            const createIcon = (color) => {
                return L.divIcon({
                    className: 'custom-div-icon',
                    html: `<div style="background-color: ${color}; width: 24px; height: 24px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 6px rgba(0,0,0,0.3);"></div>`,
                    iconSize: [24, 24],
                    iconAnchor: [12, 12]
                });
            };

            const icons = {
                open: createIcon('#EF4444'),
                in_progress: createIcon('#EAB308'),
                resolved: createIcon('#10B981'),
                closed: createIcon('#6B7280')
            };

            // Function to add markers
            function addMarkers(complaints) {
                markers.forEach(marker => map.removeLayer(marker));
                markers = [];

                complaints.forEach(complaint => {
                    if (complaint.lat && complaint.lng) {
                        const marker = L.marker([complaint.lat, complaint.lng], {
                            icon: icons[complaint.status] || icons.open
                        });

                        const statusTranslations = {
                            'open': 'Open',
                            'in_progress': 'In Behandeling',
                            'resolved': 'Opgelost',
                            'closed': 'Gesloten'
                        };

                        const popupContent = `
                            <div style="min-width: 250px;">
                                <h3 style="font-weight: 600; margin-bottom: 8px; color: #1F2937;">${complaint.title}</h3>
                                <p style="color: #6B7280; font-size: 0.875rem; margin-bottom: 8px;">${complaint.description.substring(0, 100)}${complaint.description.length > 100 ? '...' : ''}</p>
                                <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.75rem;">
                                    <span style="color: #9CA3AF;">Status: <strong>${statusTranslations[complaint.status] || complaint.status}</strong></span>
                                    <span style="color: #9CA3AF;">${new Date(complaint.created_at).toLocaleDateString('nl-NL')}</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <span style="color: #9CA3AF; font-size: 0.75rem;">👤 ${complaint.reporter_name}</span>
                                    <a href="/admin/complaints/${complaint.id}"
                                       style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                                              color: white; padding: 0.4rem 0.8rem; border-radius: 6px;
                                              text-decoration: none; font-size: 0.75rem; font-weight: 500;">
                                        Bekijken
                                    </a>
                                </div>
                            </div>
                        `;

                        marker.bindPopup(popupContent);
                        marker.addTo(map);
                        markers.push(marker);
                    }
                });

                if (markers.length > 0) {
                    const group = new L.featureGroup(markers);
                    map.fitBounds(group.getBounds().pad(0.1));
                }
            }

            // Initial load
            addMarkers(complaintsData);

            // Status filter
            document.getElementById('statusFilter').addEventListener('change', function() {
                const selectedStatus = this.value;
                let filtered;

                if (selectedStatus === 'all') {
                    filtered = complaintsData;
                } else {
                    filtered = complaintsData.filter(c => c.status === selectedStatus);
                }

                addMarkers(filtered);
            });

            // Refresh button
            document.getElementById('refreshMap').addEventListener('click', function() {
                location.reload();
            });

            // Complaint item click
            document.querySelectorAll('.complaint-item').forEach(item => {
                item.addEventListener('click', function() {
                    const lat = parseFloat(this.dataset.lat);
                    const lng = parseFloat(this.dataset.lng);

                    if (lat && lng) {
                        map.setView([lat, lng], 16);

                        markers.forEach(marker => {
                            const pos = marker.getLatLng();
                            if (Math.abs(pos.lat - lat) < 0.0001 && Math.abs(pos.lng - lng) < 0.0001) {
                                marker.openPopup();
                            }
                        });

                        // Scroll to map
                        document.getElementById('map').scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                });
            });
        });

        // Toggle complaints list
        function toggleList() {
            const list = document.getElementById('complaintsList');
            const btn = document.getElementById('toggleListBtn');

            if (list.style.display === 'none') {
                list.style.display = 'block';
                btn.textContent = 'Verberg Lijst';
            } else {
                list.style.display = 'none';
                btn.textContent = 'Toon Lijst';
            }
        }
    </script>
</body>
</html>
