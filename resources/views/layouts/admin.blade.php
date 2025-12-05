<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel Admin')</title>
    <meta name="csrf-token" content="{{ csrf_token()}}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
    <link href="{{ asset('./css/components/home-user/sidebar.css') }}" rel="stylesheet">
    <link href="{{ asset('./css/components/home-user/navbar-apiario.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('styles')

    <style>
        /* Fondo del body igual al del usuario */
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            font-family: "Outfit", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            min-height: 100vh;
        }
    </style>
</head>
<body>
    <!-- Header del Admin -->
    <div class="apiario-navbar-wrapper">
        <nav class="apiario-navbar">
            <div class="apiario-navbar-container">
                <div class="apiario-navbar-brand">
                    <a href="{{ route('admin.dashboard') }}">
                        <img src="{{ asset('img/logo-2.png') }}" alt="Logo" class="apiario-navbar-logo">
                    </a>
                </div>

                <div class="apiario-navbar-collapse" id="apiarioNavbarContent">
                    <ul class="apiario-navbar-nav">
                    </ul>
                </div>

                <div class="apiario-navbar-user">
                    <div class="apiario-user-dropdown">
                        <button class="apiario-user-button" id="apiarioUserDropdownBtn" aria-expanded="false">
                            <img src="{{ asset('img/avatar/avatar_03.svg') }}" alt="Admin" class="apiario-user-avatar">
                            <span class="apiario-user-name">Mi Cuenta</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>

                        <div class="apiario-dropdown-menu" id="apiarioUserDropdownMenu">
                            <a class="apiario-dropdown-item" href="{{ route('admin.profile') }}">
                                <i class="fas fa-cog apiario-icon-gray"></i>Configuración de cuenta
                            </a>
                            <div class="apiario-dropdown-divider"></div>
                            <a class="apiario-dropdown-item apiario-logout-item" href="#"
                                onclick="event.preventDefault(); confirmarCerrarSesion();">
                                <i class="fas fa-sign-out-alt apiario-icon-gray"></i>Cerrar sesión
                            </a>
                            <form id="admin-logout-form" action="{{ route('admin.logout') }}" method="POST"
                                style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </div>

    <div class="apicola-sidebar-container">
        <!-- Botón flotante para abrir sidebar -->
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
                        @if(Auth::guard('admin')->check())
                            <i class="fas fa-user-shield"></i>
                        @else
                            <i class="fas fa-user"></i>
                        @endif
                    </div>
                    @if(Auth::guard('admin')->check())
                        <h3 class="apicola-logo-text">
                            {{ Auth::guard('admin')->user()->name }}
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
                                <a class="apicola-sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                                    href="{{ route('admin.users.index') }}">
                                    <div class="apicola-icon-container">
                                        <i class="fas fa-users"></i>
                                        <span class="apicola-icon-background"></span>
                                    </div>
                                    <span class="apicola-menu-text">Usuarios</span>
                                    <span class="apicola-active-indicator"></span>
                                </a>
                            </li>
                            <li class="apicola-sidebar-item">
                                <a class="apicola-sidebar-link {{ request()->routeIs('admin.apiarios.index') || request()->routeIs('admin.apiarios.show') ? 'active' : '' }}"
                                    href="{{ route('admin.apiarios.index') }}">
                                    <div class="apicola-icon-container">
                                        <i class="fas fa-warehouse"></i>
                                        <span class="apicola-icon-background"></span>
                                    </div>
                                    <span class="apicola-menu-text">Apiarios</span>
                                    <span class="apicola-active-indicator"></span>
                                </a>
                            </li>
                            <li class="apicola-sidebar-item">
                                <a class="apicola-sidebar-link {{ request()->routeIs('admin.geo') ? 'active' : '' }}"
                                    href="{{ route('admin.geo') }}">
                                    <div class="apicola-icon-container">
                                        <i class="fas fa-map-marked-alt"></i>
                                        <span class="apicola-icon-background"></span>
                                    </div>
                                    <span class="apicola-menu-text">Georeferenciación</span>
                                    <span class="apicola-active-indicator"></span>
                                </a>
                            </li>
                            <li class="apicola-sidebar-item">
                                <a class="apicola-sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                                    href="{{ route('admin.dashboard') }}">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('./js/components/sidebar.js') }}"></script>

    <!-- Script para el dropdown del header -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const userDropdownBtn = document.getElementById('apiarioUserDropdownBtn');
            const userDropdownMenu = document.getElementById('apiarioUserDropdownMenu');

            if (userDropdownBtn && userDropdownMenu) {
                userDropdownBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const isExpanded = userDropdownBtn.getAttribute('aria-expanded') === 'true';

                    if (isExpanded) {
                        userDropdownMenu.classList.remove('show');
                        userDropdownBtn.setAttribute('aria-expanded', 'false');
                    } else {
                        userDropdownMenu.classList.add('show');
                        userDropdownBtn.setAttribute('aria-expanded', 'true');
                    }
                });

                // Cerrar el dropdown al hacer clic fuera
                document.addEventListener('click', function(e) {
                    if (!userDropdownBtn.contains(e.target) && !userDropdownMenu.contains(e.target)) {
                        userDropdownMenu.classList.remove('show');
                        userDropdownBtn.setAttribute('aria-expanded', 'false');
                    }
                });

                // Cerrar el dropdown al hacer clic en un item (excepto logout que tiene su propia alerta)
                const dropdownItems = userDropdownMenu.querySelectorAll('.apiario-dropdown-item:not(.apiario-logout-item)');
                dropdownItems.forEach(item => {
                    item.addEventListener('click', function() {
                        userDropdownMenu.classList.remove('show');
                        userDropdownBtn.setAttribute('aria-expanded', 'false');
                    });
                });
            }
        });

        // Función para confirmar cierre de sesión
        function confirmarCerrarSesion() {
            Swal.fire({
                title: '¿Cerrar sesión?',
                html: `
                    <div style="text-align: center; padding: 20px;">
                        <div style="margin-bottom: 20px;">
                            <i class="fas fa-sign-out-alt" style="font-size: 4rem; color: #64748b;"></i>
                        </div>
                        <p style="color: #475569; font-size: 1.05rem; margin: 0;">
                            ¿Estás seguro de que deseas cerrar tu sesión de administrador?
                        </p>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="fas fa-sign-out-alt"></i> Cerrar sesión',
                cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
                width: '500px',
                padding: '30px',
                backdrop: 'rgba(15, 23, 42, 0.7)',
                customClass: {
                    popup: 'swal-logout-popup',
                    confirmButton: 'swal-logout-confirm',
                    cancelButton: 'swal-logout-cancel'
                },
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Cerrar el dropdown antes de enviar el formulario
                    const userDropdownMenu = document.getElementById('apiarioUserDropdownMenu');
                    const userDropdownBtn = document.getElementById('apiarioUserDropdownBtn');
                    if (userDropdownMenu) {
                        userDropdownMenu.classList.remove('show');
                    }
                    if (userDropdownBtn) {
                        userDropdownBtn.setAttribute('aria-expanded', 'false');
                    }

                    // Enviar el formulario de logout
                    document.getElementById('admin-logout-form').submit();
                }
            });
        }
    </script>

    <style>
        .swal-logout-popup {
            border-radius: 20px !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
        }

        .swal-logout-confirm, .swal-logout-cancel {
            padding: 12px 30px !important;
            border-radius: 10px !important;
            font-weight: 600 !important;
            font-size: 1rem !important;
            transition: all 0.3s ease !important;
            border: none !important;
        }

        .swal-logout-confirm:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 16px -4px rgba(239, 68, 68, 0.5) !important;
        }

        .swal-logout-cancel:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 16px -4px rgba(100, 116, 139, 0.5) !important;
        }

        .swal2-icon.swal2-question {
            border-color: #3b82f6 !important;
            color: #3b82f6 !important;
        }
    </style>

    @stack('scripts')
</body>
</html>
