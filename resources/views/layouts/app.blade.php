<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'EXIM Track Dokumen PT. Detpak Indonesia') - Monitoring Dokumen</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>📑</text></svg>">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <!-- Background Gradients -->
    <div class="bg-glow bg-glow-1"></div>
    <div class="bg-glow bg-glow-2"></div>
    <div class="bg-glow bg-glow-3"></div>

    <div class="app-container">
        <!-- Sidebar Overlay (Mobile) -->
        <div class="sidebar-overlay" id="sidebar-overlay"></div>
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo-container" style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div class="logo-icon">
                            <i class="bi bi-file-earmark-arrow-up-fill"></i>
                        </div>
                        <div class="logo-info">
                            <h1 class="logo-text" style="font-size: 13px; font-weight: 800; line-height: 1.3; margin: 0;">EXIM Track Dokumen <br><span style="color: var(--color-accent); font-size: 10px;">PT. Detpak Indonesia</span></h1>
                        </div>
                    </div>
                    <button class="sidebar-toggle-btn" id="sidebar-toggle-btn" title="Minimize Sidebar">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                </div>
            </div>
            
            <nav class="sidebar-nav">
                <ul>
                    <li class="{{ Route::currentRouteName() == 'dashboard' ? 'active' : '' }}">
                        <a href="{{ route('dashboard') }}">
                            <i class="bi bi-grid-1x2-fill"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="{{ str_starts_with(Route::currentRouteName(), 'documents') ? 'active' : '' }}">
                        <a href="{{ route('documents.index') }}">
                            <i class="bi bi-file-earmark-text-fill"></i>
                            <span>Semua Dokumen</span>
                        </a>
                    </li>
                    <li>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="color: var(--color-danger); border-left-color: transparent;">
                            <i class="bi bi-box-arrow-left"></i>
                            <span>Keluar (Logout)</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="sidebar-footer">
                <div class="user-profile">
                    <div class="avatar">
                        <i class="bi bi-person-fill-gear text-accent" style="color: var(--color-accent);"></i>
                    </div>
                    <div class="user-info">
                        <p class="user-name">{{ Auth::user()->name ?? 'User EXIM' }}</p>
                        <p class="user-role">{{ Auth::check() ? ucfirst(Auth::user()->role) : 'Guest' }}</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="main-content">
            <!-- Mobile Top Bar Navigation -->
            <div class="mobile-top-bar">
                <button class="mobile-menu-toggle" id="mobile-menu-toggle" title="Buka Menu">
                    <i class="bi bi-list"></i>
                </button>
                <div class="logo-container" style="display: flex; align-items: center; gap: 8px;">
                    <div class="logo-icon" style="width: 32px; height: 32px; font-size: 16px;">
                        <i class="bi bi-file-earmark-arrow-up-fill"></i>
                    </div>
                    <h1 class="logo-text" style="font-size: 11px; font-weight: 800; line-height: 1.3; margin: 0; text-align: left;">EXIM Track Dokumen <br><span style="color: var(--color-accent); font-size: 8px;">PT. Detpak Indonesia</span></h1>
                </div>
                <div class="mobile-status-dot">
                    <span class="dot pulse green"></span>
                </div>
            </div>

            <header class="top-bar">
                <div class="top-bar-title">
                    <h2>@yield('header_title', 'Dashboard')</h2>
                    <p class="text-muted">@yield('header_subtitle', 'Statistik dan monitoring tanda tangan dokumen')</p>
                </div>
                <div class="top-bar-actions">
                    <div class="status-indicator">
                        <span class="dot pulse green"></span>
                        <span class="text-sm">Database Connected</span>
                    </div>
                </div>
            </header>

            <!-- Alert Notification -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible" id="success-alert">
                    <div class="alert-content">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button class="alert-close" onclick="document.getElementById('success-alert').remove()">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
            @endif

            <!-- Main Content Blade Yield -->
            <div class="content-body">
                @yield('content')
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.getElementById('sidebar-toggle-btn');
            const appContainer = document.querySelector('.app-container');

            // Load collapsed state from localStorage
            if (localStorage.getItem('sidebar-collapsed') === 'true') {
                appContainer.classList.add('sidebar-collapsed');
                toggleBtn.querySelector('i').className = 'bi bi-chevron-right';
            }

            toggleBtn.addEventListener('click', function () {
                appContainer.classList.toggle('sidebar-collapsed');
                const collapsed = appContainer.classList.contains('sidebar-collapsed');
                localStorage.setItem('sidebar-collapsed', collapsed);

                // Toggle icon class
                toggleBtn.querySelector('i').className = collapsed ? 'bi bi-chevron-right' : 'bi bi-chevron-left';
            });
            // Mobile Menu Toggle & Overlay
            const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
            const sidebarOverlay = document.getElementById('sidebar-overlay');

            if (mobileMenuToggle && sidebarOverlay) {
                mobileMenuToggle.addEventListener('click', function () {
                    appContainer.classList.add('mobile-sidebar-open');
                });

                sidebarOverlay.addEventListener('click', function () {
                    appContainer.classList.remove('mobile-sidebar-open');
                });
            }
        });
    </script>
</body>
</html>
