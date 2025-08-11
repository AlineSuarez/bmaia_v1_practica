@if(auth()->check())
    <div class="apiario-navbar-wrapper">
        <link href="{{ asset('./css/components/home-user/navbar-apiario.css') }}" rel="stylesheet">
        <script src="{{ asset('./js/components/home-user/home-funcionalidad.js') }}" defer></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">

        <nav class="apiario-navbar">
            <div class="apiario-navbar-container">
                <div class="apiario-navbar-brand">
                    <a href="{{ route('home') }}">
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
                            @if(Auth::user()->profile_picture)
                                <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" alt="Usuario"
                                    class="apiario-user-avatar">
                            @else
                                <img src="{{ asset('img/avatar/avatar_03.svg') }}" alt="Usuario" class="apiario-user-avatar">
                            @endif
                            <span class="apiario-user-name">Mi Cuenta</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>

                        <div class="apiario-dropdown-menu" id="apiarioUserDropdownMenu">
                            <a class="apiario-dropdown-item" href="{{ url('/') }}">
                                <i class="fas fa-home apiario-icon-gray"></i>Página principal
                            </a>
                            <div class="apiario-dropdown-divider"></div>
                            <a class="apiario-dropdown-item" href="{{route('user.settings')}}">
                                <i class="fas fa-cog apiario-icon-gray"></i>Configuración de cuenta
                            </a>
                            <div class="apiario-dropdown-divider"></div>
                            <a class="apiario-dropdown-item apiario-logout-item" href="{{route('logout')}}"
                                onclick="event.preventDefault(); document.getElementById('apiario-logout-form').submit();">
                                <i class="fas fa-sign-out-alt apiario-icon-gray"></i>Cerrar sesión
                            </a>
                            <form id="apiario-logout-form" action="{{ route('logout') }}" method="POST"
                                style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </div>
@endif