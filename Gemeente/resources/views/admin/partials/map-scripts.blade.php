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

                // Add new markers
                data.forEach(complaint => {
                    if (complaint.lat && complaint.lng) {
                        const color = getMarkerColor(complaint.status);
                        const marker = L.circleMarker([complaint.lat, complaint.lng], {
                            radius: 10,
                            fillColor: color,
                            color: '#ffffff',
                            weight: 2,
                            opacity: 1,
                            fillOpacity: 0.8
                        }).addTo(map);

                        const popupContent = `
                            <div style="min-width: 250px;">
                                <h3 style="font-weight: 600; margin-bottom: 8px; color: #1F2937;">
                                    Klacht #${complaint.id}
                                </h3>
                                <p style="margin-bottom: 8px;">
                                    <strong>Titel:</strong> ${complaint.title}
                                </p>
                                <p style="margin-bottom: 8px;">
                                    <strong>Beschrijving:</strong> ${complaint.description.substring(0, 100)}...
                                </p>
                                <p style="margin-bottom: 8px;">
                                    <strong>Status:</strong> <span style="color: ${color}; font-weight: 600;">${translateStatus(complaint.status)}</span>
                                </p>
                                <p style="margin-bottom: 8px;">
                                    <strong>Prioriteit:</strong> ${translatePriority(complaint.priority)}
                                </p>
                                <p style="margin-bottom: 8px;">
                                    <strong>Categorie:</strong> ${complaint.category}
                                </p>
                                <p style="margin-bottom: 8px;">
                                    <strong>Locatie:</strong> ${complaint.location || 'Geen locatie'}
                                </p>
                                <p style="margin-bottom: 12px; color: #6B7280; font-size: 0.875rem;">
                                    ${new Date(complaint.created_at).toLocaleDateString('nl-NL')}
                                </p>
                                <a href="/admin/complaints/${complaint.id}"
                                   style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                                          color: white; padding: 0.5rem 1rem; border-radius: 8px;
                                          text-decoration: none; font-weight: 500;">
                                    Bekijk Details
                                </a>
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
            'open': '#3B82F6',          // Blue
            'in_progress': '#EAB308',   // Yellow
            'resolved': '#10B981',      // Green
            'closed': '#6B7280'         // Gray
        };
        return colors[status] || '#3B82F6';
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
                } else {
                    window.location.href = `/admin/complaints/${data.id}`;
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
