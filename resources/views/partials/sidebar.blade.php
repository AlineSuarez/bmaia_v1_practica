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
                    <i class="fas fa-home"></i>
                </div>
                <h3 class="apicola-logo-text">Bienvenido</h3>
            </div>
            <button id="sidebarToggleInside" class="apicola-toggle-inside" aria-label="Cerrar menú">
                <i class="fas fa-times"></i>
                <span class="apicola-toggle-ripple"></span>
            </button>
        </div>

        <div class="apicola-sidebar-content">
            <div class="apicola-user-profile">
                <div class="apicola-user-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="apicola-user-info">
                    <h4 class="apicola-user-name">Usuario Apícola</h4>
                    <p class="apicola-user-role">Administrador</p>
                </div>
                <div class="apicola-user-status online">
                    <span class="apicola-status-indicator"></span>
                </div>
            </div>

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
                            <a class="apicola-sidebar-link {{ request()->routeIs('sistemaexperto') ? 'active' : '' }}"
                                href="{{ route('sistemaexperto') }}">
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

        <div class="apicola-sidebar-footer">
            <div class="apicola-system-status">
                <div class="apicola-status-item">
                    <i class="fas fa-server"></i>
                    <span class="apicola-status-text">Sistema: <span class="apicola-status-value">Activo</span></span>
                </div>
                <div class="apicola-status-item">
                    <i class="fas fa-wifi"></i>
                    <span class="apicola-status-text">Red: <span class="apicola-status-value">Estable</span></span>
                </div>
            </div>
            <div class="apicola-footer-content">
                <div class="apicola-footer-icon">
                    <i class="fas fa-info-circle"></i>
                </div>
                <span class="apicola-footer-text">Sistema de Gestión Apícola</span>
            </div>
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