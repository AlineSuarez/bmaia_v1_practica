@extends('layouts.guest')

@section('title', 'B-Maia - Iniciar Sesión')

@section('content')

    <head>
        <link rel="stylesheet" href="{{ asset('css/components/auth/login.css') }}">
    </head>

    <div class="login-container">
        <!-- Sección izquierda -->
        <div class="left-section">
            <div class="animated-bg"></div>
            <div class="floating-shapes">
                <div class="shape"></div>
                <div class="shape"></div>
                <div class="shape"></div>
                <div class="shape"></div>
            </div>
            <div class="content-wrapper">
                <div class="logo">B-MaiA</div>
                <h1 class="tagline">Ecosistema de Apicultura Inteligente</h1>
                <p class="subtitle">Plataforma todo en uno, 100% digital, que integra herramientas avanzadas para gestionar
                    tus colmenas de manera más fácil
                    y rápida, adaptándose a las particularidades de la apicultura chilena.</p>
                <a href="{{ url('/') }}" class="home-button">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z" />
                    </svg>
                    Volver al inicio
                </a>

                <div class="features">
                    <div class="feature-item">
                        <div class="feature-icon">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 1H9V3H13.5L19 8.5V9H21ZM21 11H15C14.4 11 14 10.6 14 10S14.4 9 15 9H21C21.6 9 22 9.4 22 10S21.6 11 21 11ZM3 13V11H9V13H3ZM21 15H9V13H21V15ZM21 19V17H3V19H21Z" />
                            </svg>
                        </div>
                        <span>Monitoreo en tiempo real de colmenas</span>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M22 21H2V3H4V19H6V10H10V19H12V6H16V19H18V14H22V21Z" />
                            </svg>
                        </div>
                        <span>Análisis avanzado de productividad</span>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M17,4H20A2,2 0 0,1 22,6V20A2,2 0 0,1 20,22H4A2,2 0 0,1 2,20V6A2,2 0 0,1 4,4H7V2H9V4H15V2H17V4M4,8V20H20V8H4M6,10H8V12H6V10M10,10H12V12H10V10M14,10H16V12H14V10M18,10H20V12H18V10Z" />
                            </svg>
                        </div>
                        <span>Gestión desde cualquier lugar</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección derecha con formulario -->
        <div class="right-section">
            <div class="login-form-wrapper">
                <div class="form-header">
                    <h2 class="form-title">Iniciar Sesión</h2>
                    <p class="form-subtitle">Accede a tu ecosistema de gestión inteligente</p>
                </div>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <div class="input-wrapper">
                            <input id="email" type="email" class="form-input @error('email') is-invalid @enderror"
                                name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                                placeholder="tu@email.com">
                        </div>
                        @error('email')
                            <div class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Contraseña</label>
                        <div class="input-wrapper">
                            <input id="password" type="password"
                                class="form-input password-input @error('password') is-invalid @enderror" name="password"
                                required autocomplete="current-password" placeholder="••••••••">
                            <button type="button" class="password-toggle" onclick="togglePassword()">
                                <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <div class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </div>
                        @enderror
                    </div>

                    <div class="checkbox-wrapper">
                        <input class="checkbox-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="checkbox-label" for="remember">
                            Recordar sesión
                        </label>
                    </div>

                    <button type="submit" class="btn-primary">
                        Iniciar Sesión
                    </button>

                    <div class="divider">
                        <span class="divider-text">O continúa con</span>
                    </div>

                    <a href="{{ route('auth.google') }}" class="btn-google">
                        <svg class="google-icon" viewBox="0 0 24 24">
                            <path fill="#4285F4"
                                d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                            <path fill="#34A853"
                                d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                            <path fill="#FBBC05"
                                d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                            <path fill="#EA4335"
                                d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                        </svg>
                        Continuar con Google
                    </a>

                    <div class="form-footer">
                        @if (Route::has('password.request'))
                            <div class="forgot-password">
                                <a href="{{ route('password.request') }}">
                                    ¿Olvidaste tu contraseña?
                                </a>
                            </div>
                        @endif

                        <div class="register-link">
                            ¿No tienes cuenta? <a href="{{ route('register') }}">Regístrate aquí</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                // Ícono de ojo cerrado (más limpio)
                eyeIcon.innerHTML = `
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 11-4.243-4.243m4.242 4.242L9.88 9.88" />
                        `;
            } else {
                passwordInput.type = 'password';
                // Ícono de ojo abierto
                eyeIcon.innerHTML = `
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        `;
            }
        }
    </script>
@endsection