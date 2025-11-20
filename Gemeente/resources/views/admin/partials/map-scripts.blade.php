<script>
    let map;
    let markers = [];

    // Initialize map
    document.addEventListener('DOMContentLoaded', function() {
        map = L.map('map').setView([52.3676, 4.9041], 12); // Amsterdam center

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        loadMapData();
    });

    // Load all complaints on map
    function loadMapData(filters = {}) {
        let url = '{{ route('admin.api.dashboard.map-data') }}';
        const params = new URLSearchParams(filters);
        if (params.toString()) {
            url += '?' + params.toString();
        }

        fetch(url)
            .then(response => response.json())
            .then(data => {
                // Clear existing markers
                markers.forEach(marker => map.removeLayer(marker));
                markers = [];

                // Add new markers with custom pin icons
                data.forEach(complaint => {
                    if (complaint.lat && complaint.lng) {
                        const color = getMarkerColor(complaint.status);

                        // Create custom pin icon
                        const pinIcon = L.divIcon({
                            className: 'custom-pin',
                            html: `
                                <div style="position: relative;">
                                    <svg width="40" height="50" viewBox="0 0 24 36" style="filter: drop-shadow(0 4px 6px rgba(0,0,0,0.3));">
                                        <path d="M12 0C7.03 0 3 4.03 3 9c0 6.75 9 18 9 18s9-11.25 9-18c0-4.97-4.03-9-9-9z"
                                              fill="${color}" stroke="#fff" stroke-width="1.5"/>
                                        <circle cx="12" cy="9" r="4" fill="#fff" opacity="0.9"/>
                                        <text x="12" y="12" text-anchor="middle" font-size="8" font-weight="bold" fill="${color}">${complaint.id}</text>
                                    </svg>
                                </div>
                            `,
                            iconSize: [40, 50],
                            iconAnchor: [20, 50],
                            popupAnchor: [0, -50]
                        });

                        const marker = L.marker([complaint.lat, complaint.lng], { icon: pinIcon }).addTo(map);

                        const popupContent = `
                            <div style="min-width: 280px; font-family: system-ui, -apple-system, sans-serif;">
                                <div style="background: linear-gradient(135deg, ${color} 0%, ${color}dd 100%); padding: 12px; margin: -10px -10px 12px -10px; border-radius: 8px 8px 0 0;">
                                    <h3 style="font-weight: 700; margin: 0; color: white; font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">
                                        <span style="font-size: 1.3rem;">📋</span>
                                        Klacht #${complaint.id}
                                    </h3>
                                </div>
                                <div style="padding: 4px 0;">
                                    <p style="margin-bottom: 10px; display: flex; align-items: start; gap: 8px;">
                                        <span style="font-size: 1.2rem; margin-top: 2px;">📝</span>
                                        <span><strong style="color: #374151;">Titel:</strong><br>${complaint.title}</span>
                                    </p>
                                    <p style="margin-bottom: 10px; display: flex; align-items: start; gap: 8px;">
                                        <span style="font-size: 1.2rem; margin-top: 2px;">📄</span>
                                        <span><strong style="color: #374151;">Beschrijving:</strong><br><span style="color: #6B7280;">${complaint.description.substring(0, 80)}...</span></span>
                                    </p>
                                    <p style="margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                                        <span style="font-size: 1.2rem;">🚦</span>
                                        <span><strong style="color: #374151;">Status:</strong> <span style="background: ${color}; color: white; padding: 4px 12px; border-radius: 6px; font-weight: 600; font-size: 0.85rem;">${translateStatus(complaint.status)}</span></span>
                                    </p>
                                    <p style="margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                                        <span style="font-size: 1.2rem;">⚡</span>
                                        <span><strong style="color: #374151;">Prioriteit:</strong> ${translatePriority(complaint.priority)}</span>
                                    </p>
                                    <p style="margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                                        <span style="font-size: 1.2rem;">📂</span>
                                        <span><strong style="color: #374151;">Categorie:</strong> ${complaint.category}</span>
                                    </p>
                                    <p style="margin-bottom: 10px; display: flex; align-items: start; gap: 8px;">
                                        <span style="font-size: 1.2rem; margin-top: 2px;">📍</span>
                                        <span><strong style="color: #374151;">Locatie:</strong><br><span style="color: #6B7280;">${complaint.location || 'Geen locatie opgegeven'}</span></span>
                                    </p>
                                    <p style="margin-bottom: 16px; display: flex; align-items: center; gap: 8px; color: #9CA3AF; font-size: 0.875rem;">
                                        <span>🕐</span>
                                        ${complaint.created_at}
                                    </p>
                                    <a href="/admin/complaints/${complaint.id}"
                                       style="display: block; text-align: center; background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%);
                                              color: white; padding: 12px 20px; border-radius: 10px;
                                              text-decoration: none; font-weight: 600; font-size: 0.95rem;
                                              box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3); transition: all 0.2s;"
                                       onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(14, 165, 233, 0.4)'"
                                       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(14, 165, 233, 0.3)'">
                                        👁️ Bekijk Volledige Details
                                    </a>
                                </div>
                            </div>
                        `;

                        marker.bindPopup(popupContent);
                        markers.push(marker);
                    }
                });

                // Fit bounds to show all markers
                if (markers.length > 0) {
                    const group = L.featureGroup(markers);
                    map.fitBounds(group.getBounds().pad(0.1));
                }
            })
            .catch(error => console.error('Error loading map data:', error));
    }

    // Get marker color based on status
    function getMarkerColor(status) {
        const colors = {
            'open': '#EF4444',          // Red - Open/Urgent
            'in_progress': '#F59E0B',   // Orange - In Progress
            'resolved': '#10B981',      // Green - Resolved
            'closed': '#6B7280'         // Gray - Closed
        };
        return colors[status] || '#EF4444';
    }

    // Translate status to Dutch
    function translateStatus(status) {
        const translations = {
            'open': 'Open',
            'in_progress': 'In Behandeling',
            'resolved': 'Opgelost',
            'closed': 'Gesloten'
        };
        return translations[status] || status;
    }

    // Translate priority to Dutch
    function translatePriority(priority) {
        const translations = {
            'low': 'Laag',
            'medium': 'Middel',
            'high': 'Hoog',
            'urgent': 'Urgent'
        };
        return translations[priority] || priority;
    }

    // Search by ID
    function searchById() {
        const id = document.getElementById('search-id').value;
        if (!id) {
            alert('Voer een klacht ID in');
            return;
        }

        fetch(`{{ route('admin.api.dashboard.search') }}?id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                } else if (data.success && data.data && data.data.id) {
                    // Navigate to complaint details page
                    window.location.href = `/admin/complaints/${data.data.id}`;
                } else {
                    alert('Klacht niet gevonden');
                }
            })
            .catch(error => {
                console.error('Error searching:', error);
                alert('Er is een fout opgetreden bij het zoeken');
            });
    }

    // Apply filters
    function applyFilters() {
        const status = document.getElementById('filter-status').value;
        const priority = document.getElementById('filter-priority').value;

        const filters = {};
        if (status) filters.status = status;
        if (priority) filters.priority = priority;

        loadMapData(filters);
    }

    // Reset filters
    function resetFilters() {
        document.getElementById('filter-status').value = '';
        document.getElementById('filter-priority').value = '';
        loadMapData();
    }

    // Zoom to specific complaint
    function zoomToComplaint(lat, lng, id) {
        map.setView([lat, lng], 16);

        // Find and open the marker popup
        markers.forEach(marker => {
            const markerLatLng = marker.getLatLng();
            if (Math.abs(markerLatLng.lat - lat) < 0.0001 && Math.abs(markerLatLng.lng - lng) < 0.0001) {
                marker.openPopup();
            }
        });

        // Scroll to map
        document.getElementById('map').scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
</script>
