<head>
    <link href="{{ asset('./css/components/home-user/sidebar.css') }}" rel="stylesheet">
    <script src="{{ asset('./js/components/sidebar.js') }}"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<div class="apicola-sidebar-container">
    <button id="floatingSidebarToggle" class="apicola-floating-toggle" aria-label="Abrir menú">
        <div class="apicola-floating-icon">
            <i class="fas fa-bars"></i>
        </div>
        <span class="apicola-floating-pulse"></span>
    </button>

    <!-- Sidebar -->
    <aside id="sidebar" class="apicola-sidebar">
        <div class="apicola-sidebar-header">
            <div class="apicola-logo-container">
                <div class="apicola-logo-icon">
                    @if(Auth::check() && Auth::user()->profile_picture)
                        <img src="{{ asset('storage/' . (Auth::user()->profile_picture ?? 'default-profile.png')) }}"
                            alt="Foto de perfil" style="width: 40px; height: 40px; border-radius: 8px; object-fit: cover;">
                    @else
                        <i class="fas fa-user"></i>
                    @endif
                </div>
                @if(Auth::check())
                    <h3 class="apicola-logo-text">
                        {{ Auth::user()->name }}
                    </h3>
                @endif
            </div>
            <button id="sidebarToggleInside" class="apicola-toggle-inside" aria-label="Cerrar menú">
                <i class="fas fa-times"></i>
                <span class="apicola-toggle-ripple"></span>
            </button>
        </div>

        <div class="apicola-sidebar-content">
            <nav class="apicola-sidebar-nav">
                <div class="apicola-nav-section">
                    <ul class="apicola-sidebar-menu">
                        <li class="apicola-sidebar-item">
                            <a class="apicola-sidebar-link {{ request()->routeIs('home') ? 'active' : '' }}"
                                href="{{ route('home') }}">
                                <div class="apicola-icon-container">
                                    <i class="fas fa-home"></i>
                                    <span class="apicola-icon-background"></span>
                                </div>
                                <span class="apicola-menu-text">Inicio</span>
                                <span class="apicola-active-indicator"></span>
                            </a>
                        </li>
                        <li class="apicola-sidebar-item">
                            <a class="apicola-sidebar-link {{ request()->routeIs('apiarios') ? 'active' : '' }}"
                                href="{{ route('apiarios') }}">
                                <div class="apicola-icon-container">
                                    <i class="fas fa-boxes"></i>
                                    <span class="apicola-icon-background"></span>
                                </div>
                                <span class="apicola-menu-text">Apiarios</span>
                                <span class="apicola-active-indicator"></span>
                            </a>
                        </li>
                        <li class="apicola-sidebar-item">
                            <a class="apicola-sidebar-link {{ request()->routeIs('visitas') ? 'active' : '' }}"
                                href="{{ route('visitas') }}">
                                <div class="apicola-icon-container">
                                    <i class="fas fa-book-open"></i>
                                    <span class="apicola-icon-background"></span>
                                </div>
                                <span class="apicola-menu-text">Cuaderno de campo</span>
                                <span class="apicola-active-indicator"></span>
                            </a>
                        </li>
                        <li class="apicola-sidebar-item">
                            <a class="apicola-sidebar-link {{ request()->routeIs('tareas') ? 'active' : '' }}"
                                href="{{ route('tareas') }}">
                                <div class="apicola-icon-container">
                                    <i class="fas fa-tasks"></i>
                                    <span class="apicola-icon-background"></span>
                                </div>
                                <span class="apicola-menu-text">Tareas</span>
                                <span class="apicola-active-indicator"></span>
                            </a>
                        </li>
                        <li class="apicola-sidebar-item">
                            <a class="apicola-sidebar-link {{ request()->routeIs('zonificacion') ? 'active' : '' }}"
                                href="{{ route('zonificacion') }}">
                                <div class="apicola-icon-container">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span class="apicola-icon-background"></span>
                                </div>
                                <span class="apicola-menu-text">Zonificación</span>
                                <span class="apicola-active-indicator"></span>
                            </a>
                        </li>
                        <li class="apicola-sidebar-item">
                            <a class="apicola-sidebar-link {{ request()->routeIs('sistemaexperto.index') ? 'active' : '' }}"
                                href="{{ route('sistemaexperto.index') }}">
                                <div class="apicola-icon-container">
                                    <i class="fas fa-brain"></i>
                                    <span class="apicola-icon-background"></span>
                                </div>
                                <span class="apicola-menu-text">Sistema Experto</span>
                                <span class="apicola-active-indicator"></span>
                                <span class="apicola-menu-badge premium">IA</span>
                            </a>
                        </li>
                        <li class="apicola-sidebar-item">
                            <a class="apicola-sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                                href="{{ route('dashboard') }}">
                                <div class="apicola-icon-container">
                                    <i class="fas fa-chart-line"></i>
                                    <span class="apicola-icon-background"></span>
                                </div>
                                <span class="apicola-menu-text">Dashboard</span>
                                <span class="apicola-active-indicator"></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </aside>

    <!-- Contenido principal -->
    <main id="main-content" class="apicola-main-content">
        <div class="apicola-content-area">
            @yield('content')
        </div>
    </main>

    <div class="apicola-sidebar-overlay"></div>
</div>