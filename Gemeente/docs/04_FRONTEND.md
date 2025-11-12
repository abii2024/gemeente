# 🎨 Frontend - Modern UI/UX Implementatie

**Onderwerp:** Frontend Technologieën, CSS Styling & JavaScript  
**Datum:** 6 oktober 2025

---

## 📋 Inhoudsopgave

1. [Frontend Stack](#frontend-stack)
2. [Blade Templates](#blade-templates)
3. [Modern CSS Features](#modern-css-features)
4. [JavaScript & Animations](#javascript--animations)
5. [Responsive Design](#responsive-design)
6. [Interactive Map Implementation](#interactive-map-implementation)

---

## 🛠️ Frontend Stack

```
┌────────────────────────────────────────┐
│         Frontend Technologieën          │
├────────────────────────────────────────┤
│ • Blade Templates (Laravel)            │
│ • Tailwind CSS 3.x                     │
│ • Alpine.js (Lightweight JS)           │
│ • Custom CSS (Glassmorphism)           │
│ • Vite (Asset Bundling)                │
│ • Leaflet.js (Maps)                    │
│ • OpenStreetMap (Map Tiles)            │
└────────────────────────────────────────┘
```

### Build Process

```
resources/
├── css/
│   ├── app.css                 # Tailwind imports
│   └── gemeente-modern.css     # Custom modern styles
└── js/
    ├── app.js                  # Main JS entry
    └── moderne-animations.js   # Animation utilities

         │ Vite Build Process
         ↓

public/
├── build/
    ├── assets/
        ├── app-[hash].css      # Compiled & minified CSS
        └── app-[hash].js       # Compiled & minified JS
```

**Build Commands:**
```bash
# Development (hot reload)
npm run dev

# Production build
npm run build

# Watch mode
npm run watch
```

---

## 📄 Blade Templates

### Layout Structuur

```
resources/views/
├── layouts/
│   ├── app.blade.php          # Main public layout
│   └── admin.blade.php        # Admin dashboard layout
│
├── components/                 # Reusable components
│   ├── header.blade.php
│   ├── footer.blade.php
│   ├── navbar.blade.php
│   └── alert.blade.php
│
├── auth/
│   ├── login.blade.php
│   └── register.blade.php
│
├── complaints/
│   ├── index.blade.php        # List view
│   ├── show.blade.php         # Detail view
│   └── create.blade.php       # Create form
│
└── admin/
    ├── dashboard.blade.php
    ├── complaints/
    │   ├── index.blade.php
    │   ├── edit.blade.php
    │   └── show.blade.php
    └── partials/
        ├── header.blade.php
        ├── sidebar.blade.php
        ├── map-scripts.blade.php
        └── styles.blade.php
```

---

### Main Layout (`layouts/app.blade.php`)

```blade
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Gemeente Klachtensysteem')</title>
    
    {{-- Vite Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- Custom Modern CSS --}}
    <link rel="stylesheet" href="{{ asset('css/gemeente-modern.css') }}">
    
    {{-- Additional styles --}}
    @stack('styles')
</head>
<body class="bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen">
    
    {{-- Navigation --}}
    @include('components.navbar')
    
    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    
    {{-- Main Content --}}
    <main class="container mx-auto px-4 py-8">
        @yield('content')
    </main>
    
    {{-- Footer --}}
    @include('components.footer')
    
    {{-- Additional scripts --}}
    @stack('scripts')
</body>
</html>
```

#### **Blade Directives Uitleg:**

**1. Template Inheritance:**
```blade
{{-- Parent Template --}}
@yield('content')           <!-- Placeholder voor content -->
@yield('title', 'Default')  <!-- Met default waarde -->

{{-- Child Template --}}
@extends('layouts.app')     <!-- Extend parent -->

@section('content')         <!-- Vul placeholder -->
    <h1>Hello World</h1>
@endsection
```

**2. Components & Includes:**
```blade
{{-- Include partial --}}
@include('components.navbar')

{{-- Include met data --}}
@include('components.alert', ['type' => 'success', 'message' => 'Done!'])

{{-- Component --}}
<x-alert type="success" message="Done!" />
```

**3. Stacks (voor scripts/styles):**
```blade
{{-- In layout --}}
@stack('styles')

{{-- In view --}}
@push('styles')
    <link rel="stylesheet" href="custom.css">
@endpush
```

**4. Conditionals:**
```blade
@if($user->isAdmin())
    <p>Admin content</p>
@elseif($user->isModerator())
    <p>Moderator content</p>
@else
    <p>Regular user</p>
@endif

@unless($user->isGuest())
    <p>Logged in</p>
@endunless

@auth
    <p>Authenticated user</p>
@endauth

@guest
    <p>Guest user</p>
@endguest
```

**5. Loops:**
```blade
@foreach($complaints as $complaint)
    <div class="complaint-card">
        <h3>{{ $complaint->title }}</h3>
    </div>
@endforeach

@forelse($items as $item)
    <li>{{ $item }}</li>
@empty
    <p>No items found</p>
@endforelse
```

**6. Output Escaping:**
```blade
{{-- Escaped (veilig, voorkomt XSS) --}}
{{ $complaint->title }}

{{-- Raw (niet escaped, voor HTML) --}}
{!! $complaint->description !!}

{{-- JSON --}}
@json($data)  <!-- Escaped JSON voor JavaScript -->
```

---

### Complaint Create Form (`complaints/create.blade.php`)

```blade
@extends('layouts.app')

@section('title', 'Klacht Indienen')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="glass-card p-8">
        <h1 class="text-3xl font-bold mb-8 gradient-text">
            📝 Klacht Indienen
        </h1>
        
        <form action="{{ route('complaints.store') }}" 
              method="POST" 
              enctype="multipart/form-data"
              class="space-y-6"
              x-data="complaintForm()">
            
            @csrf
            
            {{-- Title Field --}}
            <div class="floating-label-group">
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    value="{{ old('title') }}"
                    class="floating-input @error('title') border-red-500 @enderror"
                    placeholder=" "
                    required
                >
                <label for="title" class="floating-label">
                    Titel van klacht *
                </label>
                @error('title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            {{-- Description Field --}}
            <div class="floating-label-group">
                <textarea 
                    id="description" 
                    name="description" 
                    rows="5"
                    class="floating-input @error('description') border-red-500 @enderror"
                    placeholder=" "
                    required
                >{{ old('description') }}</textarea>
                <label for="description" class="floating-label">
                    Beschrijving *
                </label>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            {{-- Category Select --}}
            <div class="floating-label-group">
                <select 
                    id="category" 
                    name="category"
                    class="floating-input @error('category') border-red-500 @enderror"
                    required
                >
                    <option value="">Kies een categorie</option>
                    <option value="openbare_ruimte" {{ old('category') == 'openbare_ruimte' ? 'selected' : '' }}>
                        🏛️ Openbare Ruimte
                    </option>
                    <option value="verkeer" {{ old('category') == 'verkeer' ? 'selected' : '' }}>
                        🚗 Verkeer
                    </option>
                    <option value="overlast" {{ old('category') == 'overlast' ? 'selected' : '' }}>
                        📢 Overlast
                    </option>
                    <option value="afval" {{ old('category') == 'afval' ? 'selected' : '' }}>
                        🗑️ Afval
                    </option>
                    <option value="verlichting" {{ old('category') == 'verlichting' ? 'selected' : '' }}>
                        💡 Verlichting
                    </option>
                    <option value="groen" {{ old('category') == 'groen' ? 'selected' : '' }}>
                        🌳 Groen
                    </option>
                    <option value="anders" {{ old('category') == 'anders' ? 'selected' : '' }}>
                        📌 Anders
                    </option>
                </select>
                <label for="category" class="floating-label">
                    Categorie *
                </label>
                @error('category')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            {{-- Address Field with GPS --}}
            <div class="space-y-4">
                <div class="floating-label-group">
                    <input 
                        type="text" 
                        id="address" 
                        name="address"
                        value="{{ old('address') }}"
                        class="floating-input"
                        placeholder=" "
                        required
                        x-on:blur="geocodeAddress($event.target.value)"
                    >
                    <label for="address" class="floating-label">
                        Adres *
                    </label>
                </div>
                
                {{-- Hidden GPS fields --}}
                <input type="hidden" name="latitude" x-model="latitude">
                <input type="hidden" name="longitude" x-model="longitude">
                
                {{-- GPS Status --}}
                <div x-show="latitude && longitude" class="text-sm text-green-600">
                    ✅ GPS coördinaten gevonden: <span x-text="latitude"></span>, <span x-text="longitude"></span>
                </div>
            </div>
            
            {{-- Photo Upload with Drag & Drop --}}
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">
                    Foto's (max 5)
                </label>
                
                <div 
                    class="drag-drop-area"
                    x-on:drop.prevent="handleDrop($event)"
                    x-on:dragover.prevent="dragOver = true"
                    x-on:dragleave.prevent="dragOver = false"
                    x-bind:class="{ 'drag-over': dragOver }"
                >
                    <input 
                        type="file" 
                        name="photos[]" 
                        id="photos"
                        multiple
                        accept="image/*"
                        class="hidden"
                        x-on:change="handleFileSelect($event)"
                    >
                    
                    <label for="photos" class="cursor-pointer block">
                        <div class="text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-600">
                                Sleep foto's hier of <span class="text-blue-600">klik om te uploaden</span>
                            </p>
                        </div>
                    </label>
                </div>
                
                {{-- Preview uploaded images --}}
                <div x-show="files.length > 0" class="grid grid-cols-5 gap-2 mt-4">
                    <template x-for="(file, index) in files" :key="index">
                        <div class="relative">
                            <img :src="file.preview" class="w-full h-24 object-cover rounded">
                            <button 
                                type="button"
                                x-on:click="removeFile(index)"
                                class="absolute top-0 right-0 bg-red-500 text-white rounded-full w-6 h-6"
                            >
                                ×
                            </button>
                        </div>
                    </template>
                </div>
            </div>
            
            {{-- Contact Information --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="floating-label-group">
                    <input 
                        type="text" 
                        id="contact_name" 
                        name="contact_name"
                        value="{{ old('contact_name', auth()->user()->name ?? '') }}"
                        class="floating-input"
                        placeholder=" "
                        required
                    >
                    <label for="contact_name" class="floating-label">
                        Uw naam *
                    </label>
                </div>
                
                <div class="floating-label-group">
                    <input 
                        type="email" 
                        id="contact_email" 
                        name="contact_email"
                        value="{{ old('contact_email', auth()->user()->email ?? '') }}"
                        class="floating-input"
                        placeholder=" "
                        required
                    >
                    <label for="contact_email" class="floating-label">
                        E-mail *
                    </label>
                </div>
                
                <div class="floating-label-group">
                    <input 
                        type="tel" 
                        id="contact_phone" 
                        name="contact_phone"
                        value="{{ old('contact_phone') }}"
                        class="floating-input"
                        placeholder=" "
                    >
                    <label for="contact_phone" class="floating-label">
                        Telefoonnummer
                    </label>
                </div>
            </div>
            
            {{-- Submit Button --}}
            <div class="flex justify-end space-x-4">
                <a href="{{ route('home') }}" class="btn-secondary">
                    Annuleren
                </a>
                <button type="submit" class="btn-primary">
                    Klacht Indienen
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function complaintForm() {
    return {
        latitude: null,
        longitude: null,
        files: [],
        dragOver: false,
        
        // Geocode address naar GPS coordinaten
        async geocodeAddress(address) {
            if (!address) return;
            
            try {
                const response = await fetch(
                    `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}`
                );
                const data = await response.json();
                
                if (data && data[0]) {
                    this.latitude = parseFloat(data[0].lat);
                    this.longitude = parseFloat(data[0].lon);
                }
            } catch (error) {
                console.error('Geocoding error:', error);
            }
        },
        
        // Handle file drop
        handleDrop(event) {
            this.dragOver = false;
            const files = Array.from(event.dataTransfer.files);
            this.addFiles(files);
        },
        
        // Handle file select
        handleFileSelect(event) {
            const files = Array.from(event.target.files);
            this.addFiles(files);
        },
        
        // Add files with preview
        addFiles(newFiles) {
            newFiles.forEach(file => {
                if (this.files.length >= 5) {
                    alert('Maximum 5 foto\'s toegestaan');
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.files.push({
                        file: file,
                        preview: e.target.result
                    });
                };
                reader.readAsDataURL(file);
            });
        },
        
        // Remove file
        removeFile(index) {
            this.files.splice(index, 1);
        }
    };
}
</script>
@endpush
@endsection
```

---

## 🎨 Modern CSS Features

### **File: `public/css/gemeente-modern.css`**

```css
/* ============================================
   MODERNE CSS FEATURES
   ============================================ */

/* ============================================
   CSS CUSTOM PROPERTIES (VARIABLES)
   ============================================ */
:root {
  /* Kleuren */
  --color-primary: #3b82f6;       /* Blue-500 */
  --color-primary-dark: #1e40af;  /* Blue-800 */
  --color-secondary: #8b5cf6;     /* Purple-500 */
  --color-success: #10b981;       /* Green-500 */
  --color-danger: #ef4444;        /* Red-500 */
  --color-warning: #f59e0b;       /* Yellow-500 */
  
  /* Gradients */
  --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  --gradient-success: linear-gradient(135deg, #0093e9 0%, #80d0c7 100%);
  --gradient-sunset: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
  
  /* Glassmorphism */
  --glass-bg: rgba(255, 255, 255, 0.7);
  --glass-border: rgba(255, 255, 255, 0.18);
  --glass-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
  
  /* Spacing */
  --spacing-xs: 0.25rem;
  --spacing-sm: 0.5rem;
  --spacing-md: 1rem;
  --spacing-lg: 1.5rem;
  --spacing-xl: 2rem;
  
  /* Border Radius */
  --radius-sm: 0.375rem;
  --radius-md: 0.5rem;
  --radius-lg: 1rem;
  --radius-xl: 1.5rem;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
  --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
  
  /* Transitions */
  --transition-fast: 150ms ease-in-out;
  --transition-normal: 300ms ease-in-out;
  --transition-slow: 500ms ease-in-out;
}

/* ============================================
   GLASSMORPHISM CARDS
   ============================================ */
.glass-card {
  background: var(--glass-bg);
  backdrop-filter: blur(10px) saturate(180%);
  -webkit-backdrop-filter: blur(10px) saturate(180%);
  border: 1px solid var(--glass-border);
  border-radius: var(--radius-lg);
  box-shadow: var(--glass-shadow);
  transition: all var(--transition-normal);
}

.glass-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.25);
}

/* Gradient variants */
.glass-card.gradient-border {
  position: relative;
  background: white;
}

.glass-card.gradient-border::before {
  content: '';
  position: absolute;
  inset: 0;
  border-radius: var(--radius-lg);
  padding: 2px;
  background: var(--gradient-primary);
  -webkit-mask: 
    linear-gradient(#fff 0 0) content-box, 
    linear-gradient(#fff 0 0);
  mask: 
    linear-gradient(#fff 0 0) content-box, 
    linear-gradient(#fff 0 0);
  -webkit-mask-composite: xor;
  mask-composite: exclude;
}

/* ============================================
   GRADIENT TEXT
   ============================================ */
.gradient-text {
  background: var(--gradient-primary);
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
  font-weight: 700;
}

.gradient-text-success {
  background: var(--gradient-success);
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
}

.gradient-text-sunset {
  background: var(--gradient-sunset);
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
}

/* ============================================
   ANIMATED GRADIENT BACKGROUNDS
   ============================================ */
.animated-gradient {
  background: linear-gradient(
    -45deg,
    #ee7752,
    #e73c7e,
    #23a6d5,
    #23d5ab
  );
  background-size: 400% 400%;
  animation: gradientShift 15s ease infinite;
}

@keyframes gradientShift {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}

/* ============================================
   FLOATING LABEL FORMS
   ============================================ */
.floating-label-group {
  position: relative;
  margin-bottom: 1.5rem;
}

.floating-input {
  width: 100%;
  padding: 1rem 0.75rem 0.5rem;
  font-size: 1rem;
  border: 2px solid #e5e7eb;
  border-radius: var(--radius-md);
  outline: none;
  transition: all var(--transition-normal);
  background: white;
}

.floating-input:focus {
  border-color: var(--color-primary);
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.floating-label {
  position: absolute;
  left: 0.75rem;
  top: 50%;
  transform: translateY(-50%);
  font-size: 1rem;
  color: #6b7280;
  pointer-events: none;
  transition: all var(--transition-fast);
  background: white;
  padding: 0 0.25rem;
}

/* Label moves up when input is focused or has value */
.floating-input:focus + .floating-label,
.floating-input:not(:placeholder-shown) + .floating-label {
  top: 0;
  font-size: 0.75rem;
  color: var(--color-primary);
}

/* ============================================
   MODERN BUTTONS
   ============================================ */
.btn-primary {
  position: relative;
  padding: 0.75rem 2rem;
  font-weight: 600;
  color: white;
  background: var(--gradient-primary);
  border: none;
  border-radius: var(--radius-md);
  cursor: pointer;
  overflow: hidden;
  transition: all var(--transition-normal);
  box-shadow: var(--shadow-md);
}

.btn-primary::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(
    90deg,
    transparent,
    rgba(255, 255, 255, 0.3),
    transparent
  );
  transition: left 0.5s;
}

.btn-primary:hover::before {
  left: 100%;
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-lg);
}

.btn-primary:active {
  transform: translateY(0);
  box-shadow: var(--shadow-sm);
}

/* Secondary button */
.btn-secondary {
  padding: 0.75rem 2rem;
  font-weight: 600;
  color: var(--color-primary);
  background: white;
  border: 2px solid var(--color-primary);
  border-radius: var(--radius-md);
  cursor: pointer;
  transition: all var(--transition-normal);
}

.btn-secondary:hover {
  background: var(--color-primary);
  color: white;
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
}

/* ============================================
   DRAG & DROP AREA
   ============================================ */
.drag-drop-area {
  border: 2px dashed #d1d5db;
  border-radius: var(--radius-lg);
  padding: 2rem;
  text-align: center;
  transition: all var(--transition-normal);
  background: #f9fafb;
}

.drag-drop-area:hover {
  border-color: var(--color-primary);
  background: #eff6ff;
}

.drag-drop-area.drag-over {
  border-color: var(--color-primary);
  background: #dbeafe;
  transform: scale(1.02);
}

/* ============================================
   SMOOTH ANIMATIONS
   ============================================ */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes slideIn {
  from {
    transform: translateX(-100%);
  }
  to {
    transform: translateX(0);
  }
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

.fade-in {
  animation: fadeIn 0.6s ease-out;
}

.slide-in {
  animation: slideIn 0.4s ease-out;
}

.pulse-animation {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* ============================================
   STATUS BADGES
   ============================================ */
.status-badge {
  display: inline-flex;
  align-items: center;
  padding: 0.25rem 0.75rem;
  font-size: 0.875rem;
  font-weight: 600;
  border-radius: 9999px;
  text-transform: capitalize;
}

.status-badge.status-open {
  background: #dbeafe;
  color: #1e40af;
}

.status-badge.status-in-behandeling {
  background: #fef3c7;
  color: #92400e;
}

.status-badge.status-afgerond {
  background: #d1fae5;
  color: #065f46;
}

.status-badge.status-gesloten {
  background: #e5e7eb;
  color: #374151;
}

/* Priority badges */
.priority-badge {
  display: inline-flex;
  align-items: center;
  padding: 0.25rem 0.75rem;
  font-size: 0.875rem;
  font-weight: 600;
  border-radius: 9999px;
}

.priority-badge.priority-laag {
  background: #d1fae5;
  color: #065f46;
}

.priority-badge.priority-normaal {
  background: #dbeafe;
  color: #1e40af;
}

.priority-badge.priority-hoog {
  background: #fed7aa;
  color: #92400e;
}

.priority-badge.priority-urgent {
  background: #fecaca;
  color: #991b1b;
}

/* ============================================
   TOOLTIP
   ============================================ */
[data-tooltip] {
  position: relative;
}

[data-tooltip]::after {
  content: attr(data-tooltip);
  position: absolute;
  bottom: 100%;
  left: 50%;
  transform: translateX(-50%) translateY(-8px);
  padding: 0.5rem 0.75rem;
  background: rgba(0, 0, 0, 0.9);
  color: white;
  font-size: 0.875rem;
  border-radius: var(--radius-sm);
  white-space: nowrap;
  opacity: 0;
  pointer-events: none;
  transition: opacity var(--transition-fast);
}

[data-tooltip]:hover::after {
  opacity: 1;
}

/* ============================================
   LOADING SPINNER
   ============================================ */
.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid rgba(59, 130, 246, 0.1);
  border-top-color: var(--color-primary);
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* ============================================
   RESPONSIVE UTILITIES
   ============================================ */
@media (max-width: 768px) {
  .glass-card {
    padding: 1rem;
  }
  
  .floating-input {
    font-size: 16px; /* Prevent zoom on iOS */
  }
  
  .btn-primary,
  .btn-secondary {
    width: 100%;
    padding: 1rem;
  }
}
```

---

## ⚡ JavaScript & Animations

### **File: `public/js/moderne-animations.js`**

```javascript
/**
 * MODERNE ANIMATIONS & INTERACTIONS
 * ===================================
 */

// ===================================
// INTERSECTION OBSERVER (Fade in on scroll)
// ===================================
document.addEventListener('DOMContentLoaded', () => {
  const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
  };

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('fade-in');
        observer.unobserve(entry.target);
      }
    });
  }, observerOptions);

  // Observe all elements with 'animate-on-scroll' class
  document.querySelectorAll('.animate-on-scroll').forEach(el => {
    observer.observe(el);
  });
});

// ===================================
// SMOOTH SCROLL
// ===================================
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function (e) {
    e.preventDefault();
    const target = document.querySelector(this.getAttribute('href'));
    
    if (target) {
      target.scrollIntoView({
        behavior: 'smooth',
        block: 'start'
      });
    }
  });
});

// ===================================
// RIPPLE EFFECT
// ===================================
function createRipple(event) {
  const button = event.currentTarget;
  const circle = document.createElement('span');
  const diameter = Math.max(button.clientWidth, button.clientHeight);
  const radius = diameter / 2;

  circle.style.width = circle.style.height = `${diameter}px`;
  circle.style.left = `${event.clientX - button.offsetLeft - radius}px`;
  circle.style.top = `${event.clientY - button.offsetTop - radius}px`;
  circle.classList.add('ripple');

  const ripple = button.getElementsByClassName('ripple')[0];
  if (ripple) {
    ripple.remove();
  }

  button.appendChild(circle);
}

// Add ripple to all buttons
document.querySelectorAll('.btn-primary, .btn-secondary').forEach(button => {
  button.addEventListener('click', createRipple);
});

// ===================================
// PARALLAX EFFECT
// ===================================
window.addEventListener('scroll', () => {
  const scrolled = window.pageYOffset;
  const parallaxElements = document.querySelectorAll('[data-parallax]');
  
  parallaxElements.forEach(element => {
    const speed = element.dataset.parallax || 0.5;
    element.style.transform = `translateY(${scrolled * speed}px)`;
  });
});

// ===================================
// TOAST NOTIFICATIONS
// ===================================
function showToast(message, type = 'success') {
  const toast = document.createElement('div');
  toast.className = `toast toast-${type}`;
  toast.textContent = message;
  
  toast.style.cssText = `
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    padding: 1rem 1.5rem;
    background: ${type === 'success' ? '#10b981' : '#ef4444'};
    color: white;
    border-radius: 0.5rem;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    animation: slideIn 0.3s ease-out;
    z-index: 9999;
  `;
  
  document.body.appendChild(toast);
  
  setTimeout(() => {
    toast.style.animation = 'fadeOut 0.3s ease-out';
    setTimeout(() => toast.remove(), 300);
  }, 3000);
}

// ===================================
// AUTO-DISMISS ALERTS
// ===================================
document.querySelectorAll('.alert').forEach(alert => {
  setTimeout(() => {
    alert.style.opacity = '0';
    setTimeout(() => alert.remove(), 300);
  }, 5000);
});

// ===================================
// FORM VALIDATION FEEDBACK
// ===================================
document.querySelectorAll('input[required], textarea[required]').forEach(input => {
  input.addEventListener('blur', function() {
    if (!this.value) {
      this.classList.add('border-red-500');
    } else {
      this.classList.remove('border-red-500');
      this.classList.add('border-green-500');
    }
  });
  
  input.addEventListener('input', function() {
    if (this.value) {
      this.classList.remove('border-red-500');
    }
  });
});

// ===================================
// COPY TO CLIPBOARD
// ===================================
function copyToClipboard(text) {
  navigator.clipboard.writeText(text).then(() => {
    showToast('Gekopieerd naar klembord!', 'success');
  }).catch(err => {
    console.error('Copy failed:', err);
    showToast('Kopiëren mislukt', 'error');
  });
}

// ===================================
// KEYBOARD SHORTCUTS
// ===================================
document.addEventListener('keydown', (e) => {
  // Ctrl/Cmd + K = Open search
  if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
    e.preventDefault();
    const searchInput = document.querySelector('input[type="search"]');
    if (searchInput) searchInput.focus();
  }
  
  // Escape = Close modals
  if (e.key === 'Escape') {
    document.querySelectorAll('.modal').forEach(modal => {
      modal.classList.remove('show');
    });
  }
});

// ===================================
// LAZY LOAD IMAGES
// ===================================
const lazyImages = document.querySelectorAll('img[data-src]');
const imageObserver = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      const img = entry.target;
      img.src = img.dataset.src;
      img.removeAttribute('data-src');
      imageObserver.unobserve(img);
    }
  });
});

lazyImages.forEach(img => imageObserver.observe(img));
```

---

## 🗺️ Interactive Map Implementation

### **Map Demo HTML** (`public/map-demo.html`)

```html
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Klachten Kaart - Gemeente</title>
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        #map {
            height: 100vh;
            width: 100%;
        }
        
        /* Control Panel */
        .control-panel {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            min-width: 300px;
        }
        
        .control-panel h3 {
            margin-bottom: 15px;
            color: #1e293b;
        }
        
        .filter-group {
            margin-bottom: 15px;
        }
        
        .filter-group label {
            display: block;
            margin-bottom: 5px;
            font-size: 14px;
            color: #64748b;
            font-weight: 500;
        }
        
        .filter-group select {
            width: 100%;
            padding: 10px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.2s;
        }
        
        .filter-group select:focus {
            outline: none;
            border-color: #667eea;
        }
        
        /* Stats */
        .stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-top: 15px;
        }
        
        .stat-item {
            background: #f1f5f9;
            padding: 10px;
            border-radius: 8px;
            text-align: center;
        }
        
        .stat-item .label {
            font-size: 12px;
            color: #64748b;
        }
        
        .stat-item .value {
            font-size: 24px;
            font-weight: 700;
            color: #1e293b;
        }
        
        /* Custom Marker Styles */
        .marker-cluster {
            background: rgba(102, 126, 234, 0.6);
            border-radius: 50%;
            color: white;
            font-weight: 700;
            text-align: center;
            line-height: 40px;
        }
        
        /* Popup Styles */
        .leaflet-popup-content-wrapper {
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        
        .popup-content h4 {
            margin: 0 0 10px 0;
            color: #1e293b;
        }
        
        .popup-content .meta {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 8px;
        }
        
        .popup-content .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: capitalize;
        }
        
        .status-open { background: #dbeafe; color: #1e40af; }
        .status-in_behandeling { background: #fef3c7; color: #92400e; }
        .status-afgerond { background: #d1fae5; color: #065f46; }
        .status-gesloten { background: #e5e7eb; color: #374151; }
    </style>
</head>
<body>
    <div id="map"></div>
    
    <!-- Control Panel -->
    <div class="control-panel">
        <h3>🗺️ Klachten Kaart</h3>
        
        <div class="filter-group">
            <label for="status-filter">Status Filter:</label>
            <select id="status-filter">
                <option value="">Alle statussen</option>
                <option value="open">Open</option>
                <option value="in_behandeling">In Behandeling</option>
                <option value="afgerond">Afgerond</option>
                <option value="gesloten">Gesloten</option>
            </select>
        </div>
        
        <div class="stats">
            <div class="stat-item">
                <div class="label">Totaal</div>
                <div class="value" id="stat-total">-</div>
            </div>
            <div class="stat-item">
                <div class="label">Open</div>
                <div class="value" id="stat-open">-</div>
            </div>
            <div class="stat-item">
                <div class="label">In Behandeling</div>
                <div class="value" id="stat-progress">-</div>
            </div>
            <div class="stat-item">
                <div class="label">Afgerond</div>
                <div class="value" id="stat-done">-</div>
            </div>
        </div>
        
        <button onclick="refreshData()" style="
            width: 100%;
            margin-top: 15px;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
        ">
            🔄 Ververs Data
        </button>
    </div>
    
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <script>
        // Initialize map (centered on Amsterdam)
        const map = L.map('map').setView([52.3676, 4.9041], 12);
        
        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);
        
        let markers = [];
        let complaints = [];
        
        // Fetch and display complaints
        async function loadComplaints(statusFilter = '') {
            try {
                const url = statusFilter 
                    ? `http://gemeente.test/api/complaints/map?status=${statusFilter}`
                    : `http://gemeente.test/api/complaints/map`;
                
                const response = await fetch(url);
                const data = await response.json();
                
                complaints = data.data || [];
                
                // Clear existing markers
                markers.forEach(marker => map.removeLayer(marker));
                markers = [];
                
                // Add markers for each complaint
                complaints.forEach(complaint => {
                    const icon = getMarkerIcon(complaint.status, complaint.priority);
                    
                    const marker = L.marker([complaint.latitude, complaint.longitude], { icon })
                        .addTo(map)
                        .bindPopup(createPopupContent(complaint));
                    
                    markers.push(marker);
                });
                
                // Update stats
                updateStats();
                
                // Fit bounds to show all markers
                if (markers.length > 0) {
                    const group = new L.featureGroup(markers);
                    map.fitBounds(group.getBounds().pad(0.1));
                }
            } catch (error) {
                console.error('Error loading complaints:', error);
            }
        }
        
        // Get custom marker icon based on status
        function getMarkerIcon(status, priority) {
            const colors = {
                open: '#3b82f6',
                in_behandeling: '#f59e0b',
                afgerond: '#10b981',
                gesloten: '#6b7280'
            };
            
            const sizes = {
                laag: 25,
                normaal: 30,
                hoog: 35,
                urgent: 40
            };
            
            const color = colors[status] || '#6b7280';
            const size = sizes[priority] || 30;
            
            return L.divIcon({
                className: 'custom-marker',
                html: `<div style="
                    background: ${color};
                    width: ${size}px;
                    height: ${size}px;
                    border-radius: 50%;
                    border: 3px solid white;
                    box-shadow: 0 4px 10px rgba(0,0,0,0.3);
                "></div>`,
                iconSize: [size, size],
                iconAnchor: [size/2, size/2]
            });
        }
        
        // Create popup content
        function createPopupContent(complaint) {
            return `
                <div class="popup-content">
                    <h4>${complaint.title}</h4>
                    <div class="meta">
                        📍 ${complaint.address}<br>
                        📅 ${complaint.created_at}
                    </div>
                    <div style="margin: 10px 0;">
                        <span class="status-badge status-${complaint.status}">
                            ${complaint.status.replace('_', ' ')}
                        </span>
                    </div>
                    <div style="font-size: 13px; color: #64748b;">
                        Prioriteit: <strong>${complaint.priority}</strong><br>
                        Categorie: <strong>${complaint.category}</strong>
                    </div>
                </div>
            `;
        }
        
        // Update statistics
        function updateStats() {
            const stats = {
                total: complaints.length,
                open: complaints.filter(c => c.status === 'open').length,
                in_behandeling: complaints.filter(c => c.status === 'in_behandeling').length,
                afgerond: complaints.filter(c => c.status === 'afgerond').length
            };
            
            document.getElementById('stat-total').textContent = stats.total;
            document.getElementById('stat-open').textContent = stats.open;
            document.getElementById('stat-progress').textContent = stats.in_behandeling;
            document.getElementById('stat-done').textContent = stats.afgerond;
        }
        
        // Refresh data
        function refreshData() {
            const status = document.getElementById('status-filter').value;
            loadComplaints(status);
        }
        
        // Filter change handler
        document.getElementById('status-filter').addEventListener('change', refreshData);
        
        // Initial load
        loadComplaints();
        
        // Auto-refresh every 30 seconds
        setInterval(refreshData, 30000);
    </script>
</body>
</html>
```

**Map Features:**
- ✅ OpenStreetMap integration
- ✅ Custom markers (kleur per status, grootte per prioriteit)
- ✅ Interactive popups met klacht details
- ✅ Live filtering op status
- ✅ Real-time statistieken
- ✅ Auto-refresh elke 30 seconden
- ✅ Responsive design
- ✅ Glassmorphism UI

---

**Volgende document:** [05_API_DOCUMENTATION.md](05_API_DOCUMENTATION.md) - Complete API reference met voorbeelden
