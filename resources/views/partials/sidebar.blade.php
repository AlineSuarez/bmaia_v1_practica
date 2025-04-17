<head>
    <link href="{{ asset('./css/components/home-user/sidebar.css') }}" rel="stylesheet">
    <script src="{{ asset('./js/components/sidebar.js') }}"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<div class="app-container">
    <button id="floatingSidebarToggle" class="floating-sidebar-toggle">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Sidebar -->
    <div id="sidebar" class="sidebar">
        <div class="sidebar-header">
            <div class="logo-container">
                <h3>Menú</h3>
            </div>
            <button id="sidebarToggleInside" class="sidebar-toggle-inside">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="sidebar-content">
            <ul class="sidebar-menu">
                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                        <div class="icon-container">
                            <i class="fas fa-home"></i>
                        </div>
                        <span class="menu-text">Inicio</span>
                        <span class="active-indicator"></span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->routeIs('apiarios') ? 'active' : '' }}"
                        href="{{ route('apiarios') }}">
                        <div class="icon-container">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <span class="menu-text">Apiarios</span>
                        <span class="active-indicator"></span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->routeIs('visitas') ? 'active' : '' }}"
                        href="{{ route('visitas') }}">
                        <div class="icon-container">
                            <i class="fas fa-book-open"></i>
                        </div>
                        <span class="menu-text">Cuaderno de campo</span>
                        <span class="active-indicator"></span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->routeIs('tareas') ? 'active' : '' }}"
                        href="{{ route('tareas') }}">
                        <div class="icon-container">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <span class="menu-text">Tareas</span>
                        <span class="active-indicator"></span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->routeIs('zonificacion') ? 'active' : '' }}"
                        href="{{ route('zonificacion') }}">
                        <div class="icon-container">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <span class="menu-text">Zonificación</span>
                        <span class="active-indicator"></span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->routeIs('sistemaexperto') ? 'active' : '' }}"
                        href="{{ route('sistemaexperto') }}">
                        <div class="icon-container">
                            <i class="fas fa-brain"></i>
                        </div>
                        <span class="menu-text">Sistema Experto</span>
                        <span class="active-indicator"></span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                        href="{{ route('dashboard') }}">
                        <div class="icon-container">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <span class="menu-text">Dashboard</span>
                        <span class="active-indicator"></span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Contenido principal -->
    <div id="main-content" class="main-content">
        <div class="content-area">
            @yield('content')
        </div>
    </div>

    <div class="sidebar-overlay"></div>
</div>