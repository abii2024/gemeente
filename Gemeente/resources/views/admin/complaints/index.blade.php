<x-admin-layout>
    <x-slot name="header">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <span style="font-size: 2rem;">📋</span>
            <h2 style="font-size: clamp(1.5rem, 3vw, 2rem); font-weight: 800; color: var(--neutral-900); margin: 0;">
                Klachtenbeheer
            </h2>
        </div>
    </x-slot>

    <div style="padding: 3rem 0;">
        <div class="container" style="max-width: 1400px;">
            <!-- Filter Card -->
            <div style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(20px) saturate(180%); padding: 2rem; border-radius: 24px; border: 1px solid rgba(255, 255, 255, 0.5); box-shadow: var(--shadow-xl); margin-bottom: 2rem;">
                <form method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; align-items: end;">
                    <div>
                        <label for="search" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--neutral-700); font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em;">Zoekterm of ID</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Bijv. 123 of Damrak"
                               style="width: 100%; padding: 0.875rem 1rem; border: 2px solid var(--neutral-200); border-radius: 12px; font-size: 1rem; transition: all var(--transition-fast); background: white;"
                               onfocus="this.style.borderColor='var(--primary-500)'; this.style.boxShadow='0 0 0 4px rgba(0, 102, 204, 0.1)'"
                               onblur="this.style.borderColor='var(--neutral-200)'; this.style.boxShadow='none'">
                    </div>
                    <div>
                        <label for="status" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--neutral-700); font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em;">Status</label>
                        <select name="status" id="status"
                                style="width: 100%; padding: 0.875rem 1rem; border: 2px solid var(--neutral-200); border-radius: 12px; font-size: 1rem; background: white; cursor: pointer; transition: all var(--transition-fast);"
                                onfocus="this.style.borderColor='var(--primary-500)'; this.style.boxShadow='0 0 0 4px rgba(0, 102, 204, 0.1)'"
                                onblur="this.style.borderColor='var(--neutral-200)'; this.style.boxShadow='none'">
                            <option value="">Alle</option>
                            <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                            <option value="in_behandeling" {{ request('status') === 'in_behandeling' ? 'selected' : '' }}>In behandeling</option>
                            <option value="opgelost" {{ request('status') === 'opgelost' ? 'selected' : '' }}>Opgelost</option>
                        </select>
                    </div>
                    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                        <button type="submit"
                                style="flex: 1; min-width: 120px; padding: 0.875rem 1.5rem; background: var(--primary-600); color: white; border: none; border-radius: 12px; font-weight: 600; cursor: pointer; transition: all var(--transition-fast); white-space: nowrap;"
                                onmouseover="this.style.background='var(--primary-700)'; this.style.transform='translateY(-2px)'; this.style.boxShadow='var(--shadow-lg)'"
                                onmouseout="this.style.background='var(--primary-600)'; this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                            🔍 Zoeken
                        </button>
                        <a href="{{ route('admin.complaints.index') }}"
                           style="flex: 1; min-width: 100px; padding: 0.875rem 1.5rem; background: white; color: var(--neutral-700); border: 2px solid var(--neutral-300); border-radius: 12px; font-weight: 600; text-align: center; text-decoration: none; transition: all var(--transition-fast); white-space: nowrap;"
                           onmouseover="this.style.background='var(--neutral-50)'; this.style.borderColor='var(--neutral-400)'"
                           onmouseout="this.style.background='white'; this.style.borderColor='var(--neutral-300)'">
                            Reset
                        </a>
                        <a href="{{ route('admin.complaints.map') }}"
                           style="flex: 1; min-width: 140px; padding: 0.875rem 1.5rem; background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%); color: white; border: none; border-radius: 12px; font-weight: 600; text-align: center; text-decoration: none; transition: all var(--transition-fast); white-space: nowrap;"
                           onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='var(--shadow-lg)'"
                           onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                            🗺️ Kaartweergave
                        </a>
                    </div>
                </form>
            </div>

            <!-- Table Card -->
            <div style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(20px) saturate(180%); padding: 2rem; border-radius: 24px; border: 1px solid rgba(255, 255, 255, 0.5); box-shadow: var(--shadow-xl);">
                @if ($complaints->count())
                    <div style="overflow-x: auto; border-radius: 12px;">
                        <table style="width: 100%; border-collapse: separate; border-spacing: 0;">
                            <thead>
                                <tr style="background: linear-gradient(135deg, var(--primary-50) 0%, var(--primary-100) 100%);">
                                    <th style="padding: 1rem 1.25rem; text-align: left; font-weight: 700; color: var(--primary-900); font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 2px solid var(--primary-200); white-space: nowrap;">ID</th>
                                    <th style="padding: 1rem 1.25rem; text-align: left; font-weight: 700; color: var(--primary-900); font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 2px solid var(--primary-200);">Titel</th>
                                    <th style="padding: 1rem 1.25rem; text-align: left; font-weight: 700; color: var(--primary-900); font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 2px solid var(--primary-200); white-space: nowrap;">Categorie</th>
                                    <th style="padding: 1rem 1.25rem; text-align: left; font-weight: 700; color: var(--primary-900); font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 2px solid var(--primary-200); white-space: nowrap;">Status</th>
                                    <th style="padding: 1rem 1.25rem; text-align: left; font-weight: 700; color: var(--primary-900); font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 2px solid var(--primary-200); white-space: nowrap;">Urgentie</th>
                                    <th style="padding: 1rem 1.25rem; text-align: left; font-weight: 700; color: var(--primary-900); font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 2px solid var(--primary-200); white-space: nowrap;">Aangemaakt</th>
                                    <th style="padding: 1rem 1.25rem; text-align: right; font-weight: 700; color: var(--primary-900); font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 2px solid var(--primary-200); white-space: nowrap;">Acties</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($complaints as $complaint)
                                    <tr style="transition: all var(--transition-fast); background: white;"
                                        onmouseover="this.style.background='rgba(0, 102, 204, 0.03)'; this.style.transform='scale(1.01)'"
                                        onmouseout="this.style.background='white'; this.style.transform='scale(1)'">
                                        <td style="padding: 1.25rem; font-weight: 700; color: var(--primary-600); border-bottom: 1px solid var(--neutral-100); white-space: nowrap;">#{{ $complaint->id }}</td>
                                        <td style="padding: 1.25rem; color: var(--neutral-900); font-weight: 500; border-bottom: 1px solid var(--neutral-100);">{{ \Illuminate\Support\Str::limit($complaint->title, 60) }}</td>
                                        <td style="padding: 1.25rem; color: var(--neutral-700); border-bottom: 1px solid var(--neutral-100); white-space: nowrap;">{{ ucfirst(str_replace('_', ' ', $complaint->category)) }}</td>
                                        <td style="padding: 1.25rem; border-bottom: 1px solid var(--neutral-100); white-space: nowrap;">
                                            @php
                                                $statusColors = [
                                                    'open' => 'background: linear-gradient(135deg, #DBEAFE 0%, #BFDBFE 100%); color: #1E40AF; border: 1px solid #93C5FD;',
                                                    'in_behandeling' => 'background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%); color: #92400E; border: 1px solid #FCD34D;',
                                                    'opgelost' => 'background: linear-gradient(135deg, #D1FAE5 0%, #A7F3D0 100%); color: #065F46; border: 1px solid #6EE7B7;'
                                                ];
                                                $statusStyle = $statusColors[$complaint->status] ?? 'background: linear-gradient(135deg, #F3F4F6 0%, #E5E7EB 100%); color: #374151; border: 1px solid #D1D5DB;';
                                            @endphp
                                            <span style="{{ $statusStyle }} padding: 0.5rem 1rem; border-radius: 8px; font-weight: 600; font-size: 0.875rem; display: inline-block;">
                                                {{ ucfirst(str_replace('_', ' ', $complaint->status)) }}
                                            </span>
                                        </td>
                                        <td style="padding: 1.25rem; color: var(--neutral-700); font-weight: 500; border-bottom: 1px solid var(--neutral-100); white-space: nowrap;">
                                            @php
                                                $priorityLabels = ['low' => 'Laag', 'medium' => 'Normaal', 'high' => 'Hoog', 'urgent' => 'Urgent'];
                                                $priorityColors = [
                                                    'urgent' => 'color: #991B1B;',
                                                    'high' => 'color: #9A3412;',
                                                    'medium' => 'color: #92400E;',
                                                    'low' => 'color: #065F46;'
                                                ];
                                            @endphp
                                            <span style="{{ $priorityColors[$complaint->priority] ?? 'color: #374151;' }}">
                                                {{ $priorityLabels[$complaint->priority] ?? 'Onbekend' }}
                                            </span>
                                        </td>
                                        <td style="padding: 1.25rem; color: var(--neutral-600); font-size: 0.875rem; border-bottom: 1px solid var(--neutral-100); white-space: nowrap;">{{ $complaint->created_at->format('d-m-Y H:i') }}</td>
                                        <td style="padding: 1.25rem; border-bottom: 1px solid var(--neutral-100); text-align: right; white-space: nowrap;">
                                            <a href="{{ route('admin.complaints.show', $complaint) }}"
                                               style="color: var(--primary-600); font-weight: 600; text-decoration: none; margin-right: 0.75rem; transition: color var(--transition-fast);"
                                               onmouseover="this.style.color='var(--primary-700)'"
                                               onmouseout="this.style.color='var(--primary-600)'">Bekijken</a>
                                            <a href="{{ route('admin.complaints.edit', $complaint) }}"
                                               style="color: #F59E0B; font-weight: 600; text-decoration: none; transition: color var(--transition-fast);"
                                               onmouseover="this.style.color='#D97706'"
                                               onmouseout="this.style.color='#F59E0B'">Bewerken</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div style="margin-top: 2rem;">
                        {{ $complaints->withQueryString()->links() }}
                    </div>
                @else
                    <div style="text-align: center; padding: 3rem;">
                        <div style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;">📋</div>
                        <p style="color: var(--neutral-600); font-size: 1.125rem; font-weight: 500;">Geen klachten gevonden.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>
