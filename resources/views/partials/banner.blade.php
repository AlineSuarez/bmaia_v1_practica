<head>
    <link href="{{ asset('./css/components/navbar.css') }}" rel="stylesheet">
    <script src="{{ asset('./js/components/modal.js') }}"></script>
</head>

<div class="navbar-container">
    <!-- Logo -->
    <a href="#" class="navbar-logo">
        <img src="{{ asset('img/abeja.png') }}" width="38px" height="32px" style="margin-right:10px;" alt="bee">
        <span class="logo-text">B-MaiA</span>
    </a>

    <!-- Navegación principal -->
    <nav class="navbar-nav">
        <ul class="nav-list">
            <li class="nav-item">
                <a href="#inicio" style="color: rgb(212, 159, 0); font-weight: bold;" class="nav-link active">Inicio</a>
            </li>
            <li class="nav-item">
                <a href="#mision" style="color: rgb(212, 159, 0); font-weight: bold;" class="nav-link">Nuestra
                    Misión</a>
            </li>
            <li class="nav-item">
                <a href="#herramientas" style="color: rgb(212, 159, 0); font-weight: bold;"
                    class="nav-link">Herramientas</a>
            </li>
            <li class="nav-item">
                <a href="#como-funciona" style="color: rgb(212, 159, 0); font-weight: bold;" class="nav-link">Cómo
                    funciona</a>
            </li>
            <li class="nav-item">
                <a href="#logros" style="color: rgb(212, 159, 0); font-weight: bold;"
                    class="nav-link">Logros</a>
            </li>
            <li class="nav-item">
                <a href="#contacto" style="color: rgb(212, 159, 0); font-weight: bold;" class="nav-link">Contacto</a>
            </li>
        </ul>
    </nav>

    <!-- Botones de acción -->
    <div class="navbar-actions">
        @if(Auth::check())
            @php
                $defaultView = Auth::user()->preference->default_view ?? 'dashboard';
                $map = [
                    'dashboard' => 'dashboard',
                    'apiaries' => 'apiarios',
                    'calendar' => 'tareas.calendario',
                    'reports' => 'dashboard',
                    'home' => 'home',
                    'cuaderno' => 'visitas.index',
                    'tareas' => 'tareas',
                    'zonificacion' => 'zonificacion',
                    'sistemaexperto' => 'sistemaexperto',
                ];
                $routeName = $map[$defaultView] ?? 'dashboard';
            @endphp
            <button class="action-button action-button-enter"
                style="background-color:#ecc100; color:rgb(255, 255, 255); justify-content: center; height: 50px; margin-top: 7px;"
                onclick="window.location.href='{{ route($routeName) }}'">
                Ir a mi cuenta
            </button>
            <form id="logout-form-banner" action="{{ route('logout') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="action-button" title="Cerrar sesión"
                    style="background: none; border: none; color: #d9534f; vertical-align: middle; margin-top: 11px; height: 40px; width: 40px; padding: 0;">
                    <i class="fas fa-sign-out-alt" style="font-size: 1em;"></i>
                </button>
            </form>
        @else
            <button class="action-button action-button-enter"
                style="background-color:#ecc100; color:rgb(255, 255, 255); justify-content: center;"
                onclick="openModal('login-modal')">
                Ingresar
            </button>
        @endif
    </div>

    <!-- Botón de menú móvil -->
    <button class="navbar-toggle" id="navbar-toggle" aria-label="Menú">
        <div class="toggle-icon">
            <span class="toggle-bar"></span>
            <span class="toggle-bar"></span>
            <span class="toggle-bar"></span>
        </div>
    </button>
</div>

<!-- Menú móvil -->
<div class="mobile-menu" id="mobile-menu">
    <ul class="mobile-nav-list">
        <li class="mobile-nav-item">
            <a href="#inicio" class="mobile-nav-link active" style="font-weight: bold;">Inicio</a>
        </li>
        <li class="mobile-nav-item">
            <a href="#mision" class="mobile-nav-link" style="font-weight: bold;">Nuestra Misión</a>
        </li>
        <li class="mobile-nav-item">
            <a href="#herramientas" class="mobile-nav-link" style="font-weight: bold;">Herramientas</a>
        </li>
        <li class="mobile-nav-item">
            <a href="#como-funciona" class="mobile-nav-link" style="font-weight: bold;">Cómo funciona</a>
        </li>
        <li class="mobile-nav-item">
            <a href="#logros" class="mobile-nav-link" style="font-weight: bold;">Logros</a>
        </li>
        <li class="mobile-nav-item">
            <a href="#contacto" class="mobile-nav-link" style="font-weight: bold;">Contacto</a>
        </li>
    </ul>

    <div class="mobile-actions">
        @if(Auth::check())
            @php
                $defaultView = Auth::user()->preference->default_view ?? 'dashboard';
                $map = [
                    'dashboard' => 'dashboard',
                    'apiaries' => 'apiarios',
                    'calendar' => 'tareas.calendario',
                    'reports' => 'dashboard',
                    'home' => 'home',
                    'cuaderno' => 'visitas.index',
                    'tareas' => 'tareas',
                    'zonificacion' => 'zonificacion',
                    'sistemaexperto' => 'sistemaexperto',
                ];
                $routeName = $map[$defaultView] ?? 'dashboard';
            @endphp
            <button class="mobile-action-button mobile-action-enter"
                onclick="window.location.href='{{ route($routeName) }}'">
                Ir a mi cuenta
            </button>
            <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="mobile-action-button" title="Cerrar sesión"
                    style="background: none; border: none; color: #d9534f; vertical-align: middle; margin-left: 10px; height: 40px; width: 40px; padding: 0;">
                    <i class="fas fa-sign-out-alt" style="font-size: 1.2em;"></i>
                </button>
            </form>
        @else
            <button class="mobile-action-button mobile-action-enter" onclick="openModal('login-modal')">
                Ingresar
            </button>
        @endif
    </div>
</div>
</header>