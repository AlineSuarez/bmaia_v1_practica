@extends('layouts.admin')

@section('title', 'Configuración de la Cuenta - Admin')

@section('content')
<link href="{{ asset('css/components/user-settings/settings.css') }}" rel="stylesheet">

<div class="container">
    <header class="settings-header">
        <h1>Configuración de la Cuenta de Administrador</h1>
    </header>

    <!-- Navegación de Pestañas -->
    <nav class="settings-navigation">
        <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="admin-data-tab" data-bs-toggle="tab" href="#admin-data" role="tab"
                    aria-controls="admin-data" aria-selected="true">Datos del Administrador</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="security-tab" data-bs-toggle="tab" href="#security" role="tab"
                    aria-controls="security" aria-selected="false">Seguridad</a>
            </li>
        </ul>
    </nav>

    <!-- Contenido de Pestañas -->
    <div class="tab-content settings-content" id="settingsTabsContent">
        <!-- SECCIÓN: DATOS DEL ADMINISTRADOR -->
        <section class="tab-pane fade show active" id="admin-data" role="tabpanel" aria-labelledby="admin-data-tab">
            <div class="card settings-card mb-4">
                <div class="card-header">
                    <h3>Información Personal</h3>
                    <p class="text-muted">Actualiza tu información de administrador del sistema.</p>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                        </div>
                    @endif

                    <!-- Actualizar Nombre -->
                    <form action="{{ route('admin.profile.updateName') }}" method="POST" class="mb-4">
                        @csrf
                        <div class="form-group">
                            <label for="name">Nombre Completo</label>
                            <input type="text" class="form-control" id="name" name="name"
                                   value="{{ $admin->name }}" required>
                        </div>
                        <button type="submit" class="btn btn-primary mt-2">
                            <i class="fas fa-save"></i> Actualizar Nombre
                        </button>
                    </form>

                    <hr>

                    <!-- Actualizar Email -->
                    <form action="{{ route('admin.profile.updateEmail') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="email">Correo Electrónico</label>
                            <input type="email" class="form-control" id="email" name="email"
                                   value="{{ $admin->email }}" required>
                            @error('email')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary mt-2">
                            <i class="fas fa-save"></i> Actualizar Email
                        </button>
                    </form>
                </div>
            </div>
        </section>

        <!-- SECCIÓN: SEGURIDAD -->
        <section class="tab-pane fade" id="security" role="tabpanel" aria-labelledby="security-tab">
            <div class="card settings-card">
                <div class="card-header">
                    <h3>Cambiar Contraseña</h3>
                    <p class="text-muted">Asegúrate de usar una contraseña segura para proteger tu cuenta de administrador.</p>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.profile.updatePassword') }}" method="POST">
                        @csrf

                        <div class="form-group mb-3">
                            <label for="current_password">Contraseña Actual</label>
                            <input type="password" class="form-control" id="current_password"
                                   name="current_password" required>
                            @error('current_password')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="new_password">Nueva Contraseña</label>
                            <input type="password" class="form-control" id="new_password"
                                   name="new_password" required>
                            <small class="form-text text-muted">Mínimo 8 caracteres</small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="new_password_confirmation">Confirmar Nueva Contraseña</label>
                            <input type="password" class="form-control" id="new_password_confirmation"
                                   name="new_password_confirmation" required>
                        </div>

                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-lock"></i> Cambiar Contraseña
                        </button>
                    </form>
                </div>
            </div>
        </section>
    </div>
</div>

<style>
    .container {
        padding: 20px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .settings-header {
        margin-bottom: 30px;
    }

    .settings-header h1 {
        color: #0f172a;
        font-weight: 700;
        font-size: 2rem;
        margin: 0;
    }

    .settings-navigation {
        margin-bottom: 30px;
    }

    .nav-tabs {
        border-bottom: 2px solid #e2e8f0;
    }

    .nav-tabs .nav-link {
        color: #64748b;
        font-weight: 600;
        padding: 12px 20px;
        border: none;
        border-bottom: 3px solid transparent;
        transition: all 0.3s;
    }

    .nav-tabs .nav-link:hover {
        color: #2563eb;
        border-color: #2563eb;
    }

    .nav-tabs .nav-link.active {
        color: #2563eb;
        border-color: #2563eb;
        background: transparent;
    }

    .settings-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }

    .settings-card .card-header {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        border-bottom: 2px solid #cbd5e1;
        padding: 25px 30px;
    }

    .settings-card .card-header h3 {
        margin: 0 0 8px 0;
        color: #0f172a;
        font-weight: 700;
        font-size: 1.5rem;
    }

    .settings-card .card-header p {
        margin: 0;
        font-size: 0.95rem;
    }

    .settings-card .card-body {
        padding: 30px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: #0f172a;
        font-weight: 600;
        font-size: 0.95rem;
    }

    .form-control {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 1rem;
        transition: all 0.2s;
    }

    .form-control:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        outline: none;
    }

    .btn {
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.95rem;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-primary {
        background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
        color: white;
        box-shadow: 0 2px 4px rgba(37, 99, 235, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(37, 99, 235, 0.4);
    }

    .btn-danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
        box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3);
    }

    .btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(239, 68, 68, 0.4);
    }

    hr {
        border: none;
        border-top: 2px solid #e2e8f0;
        margin: 30px 0;
    }

    .alert {
        border-radius: 10px;
        padding: 15px 20px;
        border: none;
    }

    .alert-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }

    .text-danger {
        color: #ef4444;
        font-size: 0.875rem;
        margin-top: 5px;
    }
</style>
@endsection
