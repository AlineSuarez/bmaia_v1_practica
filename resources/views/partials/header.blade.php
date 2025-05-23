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
                        <img src="{{ asset('img/logo.png') }}" alt="Logo" class="apiario-navbar-logo">
                    </a>
                </div>

                <div class="apiario-navbar-collapse" id="apiarioNavbarContent">
                    <ul class="apiario-navbar-nav">
                    </ul>
                </div>

                <div class="apiario-navbar-user">
                    <div class="apiario-user-dropdown">
                        <button class="apiario-user-button" id="apiarioUserDropdownBtn" aria-expanded="false">
                            <img src="/img/avatar/avatar_03.svg" alt="Usuario" class="apiario-user-avatar">
                            <span class="apiario-user-name">Mi Cuenta</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>

                        <div class="apiario-dropdown-menu" id="apiarioUserDropdownMenu">
                            <a class="apiario-dropdown-item" href="{{route('user.settings')}}">
                                <i class="fas fa-cog apiario-icon-gray"></i>Configuración de cuenta
                            </a>
                            <a class="apiario-dropdown-item" href="#">
                                <i class="fas fa-question-circle apiario-icon-gray"></i>Ayuda
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
@else
    <script type="text/javascript">
        window.location = "{{ route('welcome') }}";
    </script>
@endif