<head>
    <link href="{{ asset('./css/components/navbar.css') }}" rel="stylesheet">
    <script src="{{ asset('./js/components/modal.js') }}"></script>
</head>

<div class="navbar-container">
    <!-- Logo -->
    <a href="#" class="navbar-logo">
        <img src="{{ asset('img/abeja.png') }}" width="38px" height="32px" style="margin-right:10px;" alt="bee">
        <span class="logo-text">MaiA</span>
    </a>

    <!-- Navegación principal -->
    <nav class="navbar-nav">
        <ul class="nav-list">
            <li class="nav-item">
                <a href="#inicio" style="color: rgb(212, 159, 0); font-weight: bold;" class="nav-link active">Inicio</a>
            </li>
            <li class="nav-item">
                <a href="#maia" style="color: rgb(212, 159, 0); font-weight: bold;" class="nav-link">Sobre MaiA</a>
            </li>
            <li class="nav-item">
                <a href="#herramientas" style="color: rgb(212, 159, 0); font-weight: bold;"
                    class="nav-link">Herramientas</a>
            </li>
            <li class="nav-item">
                <a href="#descarga" style="color: rgb(212, 159, 0); font-weight: bold;" class="nav-link">Descarga</a>
            </li>
            <li class="nav-item">
                <a href="#como-funciona" style="color: rgb(212, 159, 0); font-weight: bold;" class="nav-link">Cómo
                    funciona</a>
            </li>
            <!-- <li class="nav-item">
                <a href="#testimonios" style="color: rgb(212, 159, 0); font-weight: bold;"
                    class="nav-link">Testimonios</a>
            </li> -->
            <li class="nav-item">
                <a href="#contacto" style="color: rgb(212, 159, 0); font-weight: bold;" class="nav-link">Contacto</a>
            </li>
        </ul>
    </nav>

    <!-- Botones de acción -->
    <div class="navbar-actions">
        <!-- Botón de Ingresar -->
        <button class="action-button action-button-enter" style="background-color:#ecc100; color:rgb(255, 255, 255); justify-content: center;"
            onclick="openModal('login-modal')">
            Ingresar
        </button>
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
            <a href="#inicio" class="mobile-nav-link active">Inicio</a>
        </li>
        <li class="mobile-nav-item">
            <a href="#maia" class="mobile-nav-link">Sobre MaiA</a>
        </li>
        <li class="mobile-nav-item">
            <a href="#herramientas" class="mobile-nav-link">Herramientas</a>
        </li>
        <li class="mobile-nav-item">
            <a href="#descarga" class="mobile-nav-link">Descarga</a>
        </li>
        <li class="mobile-nav-item">
            <a href="#como-funciona" class="mobile-nav-link">Cómo funciona</a>
        </li>
        <li class="mobile-nav-item">
            <a href="#testimonios" class="mobile-nav-link">Testimonios</a>
        </li>
        <li class="mobile-nav-item">
            <a href="#contacto" class="mobile-nav-link">Contacto</a>
        </li>
    </ul>

    <div class="mobile-actions">
        <button class="mobile-action-button mobile-action-enter" onclick="openModal('login-modal')">
            Ingresar
        </button>
    </div>
</div>
</header>