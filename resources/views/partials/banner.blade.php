<head>
    <link href="{{ asset('./css/components/navbar.css') }}" rel="stylesheet">
    <script src="{{ asset('./js/components/modal.js') }}"></script>
</head>

<div class="navbar-container">
    <!-- Logo -->
    <a href="#" class="navbar-logo">
        <svg width="25px" height="25px" viewBox="0 -3 150 150" fill="none" xmlns="http://www.w3.org/2000/svg"
            style="margin-right: 5px;">
            <path
                d="M136.331 130.878C136.331 130.878 81.7562 118.926 79.9312 118.356L79.8125 118.332L79.8187 118.32C68.4312 114.702 59.7688 105.498 57.15 94.116L55.9125 99.24C54.3188 107.622 46.7 114 37.5 114C27.1438 114 18.75 105.942 18.75 96C18.75 87.888 24.3813 81.108 32.0688 78.858L59.75 72.618C59.925 72.27 60.0625 71.904 60.25 71.568C58.9563 71.838 57.625 72 56.25 72C48.1 72 38.15 66.078 37.6 54.924C28.1 46.344 19.925 45 12.5 45V36C20.45 36 29.75 37.314 40.05 45.09C41.3375 42.936 43.05 41.064 45.125 39.588C37.5312 31.056 37.5 18.894 37.5 12.24L46.875 12V12.24C46.875 21.198 47.5937 30.96 55.0062 36.12C55.425 36.096 55.825 36 56.25 36C66.6062 36 75 44.058 75 54C75 55.32 74.8313 56.598 74.55 57.84C74.9063 57.66 75.2875 57.528 75.6437 57.36L82.15 30.762C84.5 23.394 91.5563 18 100 18C110.356 18 118.75 26.058 118.75 36C118.75 44.382 112.756 51.36 104.681 53.364L98.0062 54.858C110.594 57.522 120.606 66.738 123.85 78.654L136.331 130.878Z"
                fill="#FBC02D" />
            <path opacity="0.5"
                d="M112.5 125.64L130.869 108.006L133.344 118.356L123.288 128.01C119.988 127.29 116.3 126.474 112.5 125.64ZM97.8688 122.406L127.506 93.954L125.031 83.604L87.1125 120.006C90.1125 120.684 93.8 121.506 97.8688 122.406ZM73.6938 115.614L120.394 70.782C118.819 68.124 116.906 65.7 114.656 63.564L66.15 110.13C68.375 112.284 70.9313 114.102 73.6938 115.614ZM55.0063 36.12C47.5938 30.96 46.875 21.204 46.875 12.24V12L37.5 12.24C37.5 18.9 37.5313 31.056 45.125 39.588C47.925 37.59 51.3125 36.36 55.0063 36.12ZM37.5 54C37.5 50.742 38.475 47.724 40.05 45.09C29.75 37.314 20.45 36 12.5 36V45C19.925 45 28.1 46.344 37.6 54.924C37.5875 54.606 37.5 54.33 37.5 54Z"
                fill="#000000" />
            <path opacity="0.4"
                d="M55.9125 99.228C54.3188 107.622 46.7 114 37.5 114C27.1438 114 18.75 105.942 18.75 96C18.75 87.888 24.3813 81.108 32.0688 78.858L62.5 72L55.9125 99.228ZM118.75 36C118.75 26.058 110.356 18 100 18C91.5563 18 84.5 23.394 82.15 30.762L75 60L104.681 53.364C112.756 51.36 118.75 44.382 118.75 36Z"
                fill="white" />
        </svg>
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
            <li class="nav-item">
                <a href="#testimonios" style="color: rgb(212, 159, 0); font-weight: bold;"
                    class="nav-link">Testimonios</a>
            </li>
            <li class="nav-item">
                <a href="#contacto" style="color: rgb(212, 159, 0); font-weight: bold;" class="nav-link">Contacto</a>
            </li>
        </ul>
    </nav>

    <!-- Botones de acción -->
    <div class="navbar-actions">
        <!-- Botón de login -->
        <button class="action-button action-button-login" onclick="openModal('login-modal')">
            Iniciar sesión
        </button>

        <!-- Botón de registro -->
        <button class="action-button action-button-register" onclick="openModal('register-modal')">
            Registrarse
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
        <button class="mobile-action-button mobile-action-login" onclick="openModal('login-modal')">
            Iniciar sesión
        </button>
        <button class="mobile-action-button mobile-action-register" onclick="openModal('register-modal')">
            Registrarse
        </button>
    </div>
</div>

<script>

</script>
</header>