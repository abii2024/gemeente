<header class="header">
    <div class="container">
        <div class="nav">
            <a class="logo" href="/">
                <div class="logo-icon">G</div>
                <div class="logo-text">
                    <span class="gemeente">Gemeente</span>
                    Portal
                </div>
            </a>

            <nav class="header-nav">
                <a class="nav-item" href="/">Home</a>
                <a class="nav-item" href="{{ route('admin.complaints.index') }}">Alle Klachten</a>
                <a class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Dashboard</a>
                <a class="nav-item" href="{{ route('admin.database.index') }}">Database</a>

                <div class="header-actions">
                    <span style="color: var(--neutral-700); font-weight: 500;">{{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-secondary">Uitloggen</button>
                    </form>
                </div>
            </nav>

            <button class="hamburger" aria-label="Toggle menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </div>
</header>

<script src="{{ asset('js/moderne-animations.js') }}" defer></script>
