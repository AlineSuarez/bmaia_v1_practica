@extends('layouts.guest')

@section('title', 'B-Maia - Email de recuperación')

@section('content')

<head>
    <link rel="stylesheet" href="{{ asset('css/components/auth/email.css') }}">
</head>

<div class="email-container">
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
            <h1 class="tagline">Recuperación Segura</h1>
            <p class="subtitle">Te enviaremos un enlace de recuperación a tu email para restablecer tu contraseña de forma segura</p>
            
            <a href="{{ url('/') }}" class="home-button">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                </svg>
                Volver al inicio
            </a>
            
            <div class="features">
                <div class="feature-item">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M20,8L12,13L4,8V6L12,11L20,6M20,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V6C22,4.89 21.1,4 20,4Z"/>
                        </svg>
                    </div>
                    <span>Enlace enviado por email</span>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1M10,17L6,13L7.41,11.59L10,14.17L16.59,7.58L18,9L10,17Z"/>
                        </svg>
                    </div>
                    <span>Proceso completamente seguro</span>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12,20A8,8 0 0,1 4,12A8,8 0 0,1 12,4A8,8 0 0,1 20,12A8,8 0 0,1 12,20M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M12.5,7V12.25L17,14.92L16.25,16.15L11,13V7H12.5Z"/>
                        </svg>
                    </div>
                    <span>Enlace válido por 60 minutos</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección derecha con formulario -->
    <div class="right-section">
        <div class="email-form-wrapper">
            <div class="form-header">
                <h2 class="form-title">Recuperar Contraseña</h2>
                <p class="form-subtitle">Ingresa tu email para recibir el enlace de recuperación</p>
            </div>

            @if (session('status'))
                <div class="success-alert">
                    <svg class="success-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2M11,16.5L18,9.5L16.59,8.09L11,13.67L7.41,10.09L6,11.5L11,16.5Z"/>
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="form-group">
                    <label for="email" class="form-label">Dirección de Email</label>
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

                <button type="submit" class="btn-primary">
                    Enviar Enlace de Recuperación
                </button>

                <div class="form-footer">
                    <div class="login-link">
                        ¿Recordaste tu contraseña? <a href="{{ route('login') }}">Inicia sesión aquí</a>
                    </div>
                    <div class="login-link">
                        ¿No tienes cuenta? <a href="{{ route('register') }}">Regístrate aquí</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection