<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Klachten Kaart') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Map Controls -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex flex-wrap items-center gap-4">
                        <div class="flex items-center space-x-2">
                            <label class="text-sm font-medium text-gray-700">Status Filter:</label>
                            <select id="statusFilter" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="all">Alle</option>
                                <option value="open">Open</option>
                                <option value="in_behandeling">In Behandeling</option>
                                <option value="opgelost">Opgelost</option>
                            </select>
                        </div>
                        
                        <div class="flex items-center space-x-2">
                            <button id="refreshMap" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Vernieuwen
                            </button>
                        </div>

                        <div class="flex items-center space-x-4 ml-auto">
                            <div class="flex items-center space-x-2">
                                <span class="w-4 h-4 bg-red-500 rounded-full"></span>
                                <span class="text-sm text-gray-600">Open ({{ $complaints->where('status', 'open')->count() }})</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="w-4 h-4 bg-yellow-500 rounded-full"></span>
                                <span class="text-sm text-gray-600">In Behandeling ({{ $complaints->where('status', 'in_behandeling')->count() }})</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="w-4 h-4 bg-green-500 rounded-full"></span>
                                <span class="text-sm text-gray-600">Opgelost ({{ $complaints->where('status', 'opgelost')->count() }})</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Map Container -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div id="map" class="w-full h-96 lg:h-[600px] rounded-lg shadow-inner"></div>
                </div>
            </div>

            <!-- Complaints List Sidebar -->
            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg" x-data="{ showList: false }">
                <div class="p-6">
                    <button @click="showList = !showList" class="flex items-center justify-between w-full text-left">
                        <h3 class="text-lg font-medium text-gray-900">Klachten Lijst ({{ $complaints->count() }})</h3>
                        <svg class="w-5 h-5 transform transition-transform" :class="{ 'rotate-180': showList }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    
                    <div x-show="showList" x-transition class="mt-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($complaints as $complaint)
                                <div class="border rounded-lg p-4 hover:shadow-md transition-shadow cursor-pointer complaint-item" 
                                     data-lat="{{ $complaint->lat }}" 
                                     data-lng="{{ $complaint->lng }}"
                                     data-id="{{ $complaint->id }}">
                                    <div class="flex items-start justify-between mb-2">
                                        <h4 class="font-medium text-gray-900 text-sm">{{ $complaint->title }}</h4>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($complaint->status === 'open') bg-red-100 text-red-800
                                            @elseif($complaint->status === 'in_behandeling') bg-yellow-100 text-yellow-800
                                            @else bg-green-100 text-green-800 @endif">
                                            {{ ucfirst(str_replace('_', ' ', $complaint->status)) }}
                                        </span>
                                    </div>
                                    <p class="text-gray-600 text-sm mb-2">{{ Str::limit($complaint->description, 80) }}</p>
                                    <p class="text-gray-500 text-xs">{{ $complaint->created_at->format('d-m-Y H:i') }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize map
            const map = L.map('map').setView([52.3676, 4.9041], 10); // Amsterdam center

            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // Custom marker icons
            const createIcon = (color) => {
                return L.divIcon({
                    className: 'custom-div-icon',
                    html: `<div style="background-color: ${color}; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>`,
                    iconSize: [20, 20],
                    iconAnchor: [10, 10]
                });
            };

            const icons = {
                open: createIcon('#ef4444'),
                in_behandeling: createIcon('#eab308'),
                opgelost: createIcon('#22c55e')
            };

            // Complaints data
            const complaintsData = @json($complaints);
            let markers = [];

            // Function to add markers to map
            function addMarkers(complaints) {
                // Clear existing markers
                markers.forEach(marker => map.removeLayer(marker));
                markers = [];

                complaints.forEach(complaint => {
                    if (complaint.lat && complaint.lng) {
                        const marker = L.marker([complaint.lat, complaint.lng], {
                            icon: icons[complaint.status] || icons.open
                        });

                        const popupContent = `
                            <div class="p-2 min-w-[250px]">
                                <h3 class="font-semibold text-gray-900 mb-2">${complaint.title}</h3>
                                <p class="text-gray-600 text-sm mb-2">${complaint.description.substring(0, 100)}${complaint.description.length > 100 ? '...' : ''}</p>
                                <div class="flex items-center justify-between text-xs text-gray-500 mb-2">
                                    <span>Status: <span class="font-medium">${complaint.status.replace('_', ' ')}</span></span>
                                    <span>${new Date(complaint.created_at).toLocaleDateString('nl-NL')}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500">Door: ${complaint.reporter_name}</span>
                                    <a href="/admin/complaints/${complaint.id}" class="bg-blue-500 text-white px-2 py-1 rounded text-xs hover:bg-blue-600">
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

                // Fit map to show all markers
                if (markers.length > 0) {
                    const group = new L.featureGroup(markers);
                    map.fitBounds(group.getBounds().pad(0.1));
                }
            }

            // Initial load
            addMarkers(complaintsData);

            // Status filter functionality
            document.getElementById('statusFilter').addEventListener('change', function() {
                const selectedStatus = this.value;
                let filteredComplaints;

                if (selectedStatus === 'all') {
                    filteredComplaints = complaintsData;
                } else {
                    filteredComplaints = complaintsData.filter(complaint => complaint.status === selectedStatus);
                }

                addMarkers(filteredComplaints);
            });

            // Refresh map button
            document.getElementById('refreshMap').addEventListener('click', function() {
                location.reload();
            });

            // Complaint list item click to center map
            document.querySelectorAll('.complaint-item').forEach(item => {
                item.addEventListener('click', function() {
                    const lat = parseFloat(this.dataset.lat);
                    const lng = parseFloat(this.dataset.lng);
                    const id = this.dataset.id;

                    if (lat && lng) {
                        map.setView([lat, lng], 16);
                        
                        // Find and open the popup for this complaint
                        markers.forEach(marker => {
                            const markerPos = marker.getLatLng();
                            if (Math.abs(markerPos.lat - lat) < 0.0001 && Math.abs(markerPos.lng - lng) < 0.0001) {
                                marker.openPopup();
                            }
                        });
                    }
                });
            });
        });
    </script>
    @endpush
</x-admin-layout>