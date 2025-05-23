@if(auth()->check())

    <head>
        <link href="{{ asset('./css/components/home-user/navbar-apiario.css') }}" rel="stylesheet">
        <script src="{{ asset('./js/components/home-user/home-funcionalidad.js') }}" defer></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    </head>

    <nav class="standard-navbar">
        <div class="navbar-container">
            <div class="navbar-brand">
                <a href="{{ route('home') }}">
                    <img src="{{ asset('img/logo.png') }}" alt="Logo" class="navbar-logo">
                </a>
            </div>

            <div class="navbar-collapse" id="navbarContent">
                <ul class="navbar-nav">
                </ul>
            </div>

            <div class="navbar-user">
                <div class="user-dropdown">
                    <button class="user-button" id="userDropdownBtn" aria-expanded="false">
                   
                    <img src="{{ auth()->user()->profile_picture
                            ? Storage::url(auth()->user()->profile_picture)
                            : asset('img/avatar/avatar_03.svg') }}"
                    alt="{{ auth()->user()->name }}"
                    class="user-avatar">
                        <span class="user-name">Mi Cuenta</span>
                        <i class="fas fa-chevron-down"></i>
                    </button>

                    <div class="dropdown-menu" id="userDropdownMenu">
                        <a class="dropdown-item" href="{{route('user.settings')}}">
                            <i class="fas fa-cog icon-gray"></i>Configuración de cuenta
                        </a>
                        <a class="dropdown-item" href="#">
                            <i class="fas fa-question-circle icon-gray"></i>Ayuda
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item logout-item" href="{{route('logout')}}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt icon-gray"></i>Cerrar sesión
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>

@else
    <script type="text/javascript">
        window.location = "{{ route('welcome') }}";
    </script>
@endif