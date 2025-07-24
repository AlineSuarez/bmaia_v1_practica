@extends('layouts.app')

@section('content')

    <head>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    </head>

    <div class="container">
        <header class="settings-header">
            <h1>Configuración de la Cuenta</h1>
            <p class="settings-description">Gestiona todos los aspectos de tu cuenta de BeeMaiA para optimizar tu
                experiencia apícola.</p>
        </header>

        <!-- Navegación de Pestañas -->
        <nav class="settings-navigation">
            <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="user-data-tab" data-bs-toggle="tab" href="#user-data" role="tab"
                        aria-controls="user-data" aria-selected="true">Datos del Usuario/a</a>
                </li>
                <!--
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="billing-tab" data-bs-toggle="tab" href="#billing" role="tab"
                        aria-controls="billing" aria-selected="false">Datos de Facturación</a>
                </li>
-->
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="security-tab" data-bs-toggle="tab" href="#security" role="tab"
                        aria-controls="security" aria-selected="false">Seguridad</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="plans-tab" data-bs-toggle="tab" href="#plans" role="tab" aria-controls="plans"
                        aria-selected="false">Planes</a>
                </li>
<!--<li class="nav-item" role="presentation">
                    <a class="nav-link" id="permissions-tab" data-bs-toggle="tab" href="#permissions" role="tab"
                        aria-controls="permissions" aria-selected="false">Permisos</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="preferences-tab" data-bs-toggle="tab" href="#preferences" role="tab"
                        aria-controls="preferences" aria-selected="false">Preferencias</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="utilities-tab" data-bs-toggle="tab" href="#utilities" role="tab"
                        aria-controls="utilities" aria-selected="false">Utilidades</a>
                </li>-->
            </ul>
        </nav>

        <!-- Contenido de Pestañas -->
        <div class="tab-content settings-content" id="settingsTabsContent">
            <!-- SECCIÓN: DATOS DEL USUARIO -->
            <section class="tab-pane fade show active" id="user-data" role="tabpanel" aria-labelledby="user-data-tab">
                <div class="card settings-card mb-4">
                    <div class="card-header">
                        <h3>Datos del Usuario/a (o representante legal)</h3>
                        <p class="text-muted">Debes ingresar tus datos personales detalladamente y sin errores. Esta
                            información será utilizada para completar los registros y el cuaderno del campo, documentos
                            esenciales para la trazabilidad de tu producción apícola.</p>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('user.updateSettings') }}" method="POST" enctype="multipart/form-data"
                            class="user-data-form">
                            @csrf

                            @if (session('success_settings'))
                                <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                                    {{ session('success_settings') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Cerrar"></button>
                                </div>
                            @endif

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="rut">RUT del usuario o Representante Legal</label>
                                        <input type="text" class="form-control" id="rut" name="rut"
                                            placeholder="Ej: 12.345.678-9" required
                                            pattern="\d{1,2}\.\d{3}\.\d{3}-[0-9Kk]{1}"
                                            title="Ingrese un RUT válido (Ej: 12.345.678-9)" value='{{ $user->rut}}'>
                                        <small class="form-text text-muted">Formato: XX.XXX.XXX-X</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="razon_social">Razón Social</label>
                                        <input type="text" class="form-control" id="razon_social" name="razon_social"
                                            placeholder="Ingrese su razón social (opcional)"
                                            value='{{ old("razon_social", $user->razon_social ?? "") }}'>
                                        <small class="form-text text-muted">Solo para personas jurídicas</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Nombres</label>
                                        <input type="text" class="form-control" id="name" name="name"
                                            placeholder="Ingrese sus nombres" required value='{{ $user->name}}'>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="last_name">Apellidos</label>
                                        <input type="text" class="form-control" id="last_name" name="last_name"
                                            placeholder="Ingrese sus apellidos" value='{{ $user->last_name}}'>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone">Teléfono</label>
                                        <div class="input-group">
                                            <span class="input-group-text">+56</span>
                                            <input type="tel" class="form-control" id="phone" name="phone"
                                                placeholder="912345678" required pattern="\d{9}"
                                                title="Ingrese un número válido de 9 dígitos (Ej: 912345678)"
                                                value='{{ $user->telefono}}'>
                                        </div>
                                        <small class="form-text text-muted">Número de contacto principal</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Correo Electrónico</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            placeholder="Ingrese su correo electrónico" required value='{{ $user->email}}'>
                                        <small class="form-text text-muted">Recibirás notificaciones en este correo</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="region">Región</label>
                                        <select id="region" class="form-select" name="id_region">
                                            <option value="">Seleccione una región</option>
                                            @foreach($regiones as $region)
                                                <option value="{{ $region->id }}" {{ $user->id_region == $region->id ? 'selected' : '' }}>
                                                    {{ $region->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="comuna">Comuna</label>
                                        <select id="comuna" class="form-select" name="id_comuna" {{ $user->id_comuna ? '' : 'disabled' }}>
                                            <option value="">Seleccione una comuna</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="address">Dirección</label>
                                        <input type="text" class="form-control" id="address" name="address"
                                            placeholder="Ingrese su dirección particular" value='{{ $user->direccion}}'>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nregistro">Número de Registro del Apicultor/a en el SAG</label>
                                        <div class="input-group">
                                            <span class="input-group-text">#</span>
                                            <input type="text" class="form-control" title="Ingrese el número de registro"
                                                id="nregistro" name="nregistro" value='{{ $user->numero_registro}}'>
                                        </div>
                                        <small class="form-text text-muted">Registro oficial como apicultor</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="profile_picture">Foto de Perfil</label>

                                        <!-- Previsualización de la imagen -->
                                        <div class="profile-picture-preview mb-3">
                                            <img id="imagePreview"
                                                src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : asset('img/avatar/avatar_02.svg') }}"
                                                alt="Vista previa"
                                                style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 3px solid #dee2e6; display: block; margin: 0 auto;">
                                        </div>

                                        <input type="file" class="form-control" id="profile_picture" name="profile_picture"
                                            accept="image/*">
                                        <small class="form-text text-muted">Formatos aceptados: JPG, PNG (máx. 2MB)</small>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions mt-4">
                                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                <button type="reset" class="btn btn-outline-secondary ms-2">Restablecer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>

            <!-- SECCIÓN: DATOS DE FACTURACIÓN -->
            <section class="tab-pane fade" id="billing" role="tabpanel" aria-labelledby="billing-tab">
                <div class="card settings-card mb-4">
                    <div class="card-header">
                        <h3>Datos de Facturación</h3>
                        <p class="text-muted">Ingresa los datos necesarios para la emisión de tu factura de servicio, la
                            cual será reportada al Servicio de Impuestos Internos (SII). Bee Fractal SpA informa al SII
                            todas sus ventas brutas.</p>
                    </div>
                    <div class="card-body">
                        <form id="billing-form" method="POST" action="{{ route('datos-facturacion.store') }}">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="razon_social">Razón Social</label>
                                        <input type="text" class="form-control" id="razon_social" name="razon_social"
                                            value="{{ old('razon_social', $datosFacturacion->razon_social ?? '') }}">
                                        <small class="form-text text-muted">Nombre legal de la empresa o persona natural</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="rut">RUT</label>
                                        <input type="text" class="form-control" id="rut" name="rut"
                                            pattern="\d{1,2}\.\d{3}\.\d{3}-[0-9Kk]{1}" title="Ej: 12.345.678-9"
                                            value="{{ old('rut', $datosFacturacion->rut ?? '') }}">
                                        <small class="form-text text-muted">RUT de la empresa o persona natural</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="giro">Giro o Actividad</label>
                                        <input type="text" class="form-control" id="giro" name="giro"
                                            value="{{ old('giro', $datosFacturacion->giro ?? '') }}">
                                        <small class="form-text text-muted">Actividad económica registrada en el SII</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="direccion_comercial">Dirección Comercial</label>
                                        <input type="text" class="form-control" id="direccion_comercial" name="direccion_comercial"
                                            value="{{ old('direccion_comercial', $datosFacturacion->direccion_comercial ?? '') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="billing_region">Región</label>
                                        <select id="billing_region" class="form-select" name="billing_region">
                                            <option value="">Seleccione una región</option>
                                            @foreach($regiones as $region)
                                                <option value="{{ $region->id }}" {{ (old('billing_region', $datosFacturacion->region_id ?? '') == $region->id) ? 'selected' : '' }}>
                                                    {{ $region->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="billing_comuna">Comuna</label>
                                        <select id="billing_comuna" class="form-select" name="billing_comuna" {{ old('billing_comuna', $datosFacturacion->comuna_id ?? '') ? '' : 'disabled' }}>
                                            <option value="">Seleccione una comuna</option>
                                            <!-- Las opciones de las comunas se agregarán aquí dinámicamente -->
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="ciudad">Ciudad</label>
                                        <input type="text" class="form-control" id="ciudad" name="ciudad"
                                            value="{{ old('ciudad', $datosFacturacion->ciudad ?? '') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="telefono">Teléfono</label>
                                        <div class="input-group">
                                            <span class="input-group-text">+56</span>
                                            <input type="tel" class="form-control" id="telefono" name="telefono"
                                                value="{{ old('telefono', $datosFacturacion->telefono ?? '') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="correo">Correo electrónico</label>
                                        <input type="email" class="form-control" id="correo" name="correo"
                                            value="{{ old('correo', $datosFacturacion->correo ?? '') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="dte-authorization mt-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="autorizacion_envio_dte" name="autorizacion_envio_dte"
                                        {{ old('autorizacion_envio_dte', $datosFacturacion->autorizacion_envio_dte ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="autorizacion_envio_dte">
                                        <strong>Autorizo el envío del Documento Tributario Electrónico por Email</strong>
                                    </label>
                                </div>
                                <div class="mt-2 ps-4">
                                    <div class="form-group">
                                        <label for="correo_envio_dte">Enviar documentos al correo:</label>
                                        <input type="email" class="form-control" id="correo_envio_dte" name="correo_envio_dte"
                                            value="{{ old('correo_envio_dte', $datosFacturacion->correo_envio_dte ?? '') }}">
                                        <small class="form-text text-muted">
                                            <a href="#" data-bs-toggle="modal" data-bs-target="#dteInfoModal">
                                                Más información sobre DTE
                                            </a>
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions mt-4">
                                <button type="submit" class="btn btn-primary">Guardar Datos de Facturación</button>
                                <button type="reset" class="btn btn-outline-secondary ms-2">Restablecer</button>
                            </div>
                        </form>

                    </div>
                </div>

                <div class="card settings-card mt-4">
                    <div class="card-header">
                        <h3>Historial de Facturas</h3>
                        <p class="text-muted">Consulta el registro de tus facturas emitidas y su estado de pago.</p>
                    </div>
                    <div class="card-body">
                        <div class="invoice-filters mb-3">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="invoice-year">Año</label>
                                        <select class="form-select" id="invoice-year">
                                            <option value="all">Todos</option>
                                            <option value="2025">2025</option>
                                            <option value="2024">2024</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="invoice-status">Estado</label>
                                        <select class="form-select" id="invoice-status">
                                            <option value="all">Todos</option>
                                            <option value="paid">Pagada</option>
                                            <option value="pending">Pendiente</option>
                                            <option value="cancelled">Anulada</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <button type="button" class="btn btn-outline-primary w-100">Filtrar</button>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped invoice-table">
                                <thead>
                                    <tr>
                                        <th>Número</th>
                                        <th>Fecha</th>
                                        <th>Monto</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Aquí se mostrarían las facturas del usuario -->
                                    <tr>
                                        <td colspan="5" class="text-center">No hay facturas disponibles</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            <!-- SECCIÓN: SEGURIDAD -->
            <section class="tab-pane fade" id="security" role="tabpanel" aria-labelledby="security-tab">
                <div class="card settings-card mb-4">
                    <div class="card-header">
                        <h3>Seguridad de la Cuenta</h3>
                        <p class="text-muted">Gestiona la seguridad de tu cuenta con una contraseña robusta. Te
                            proporcionamos una clave encriptada con altos estándares de seguridad que puedes modificar en
                            cualquier momento.</p>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('user.update.password') }}" method="POST" class="security-form">
                            @csrf
                            @method('PATCH')

                            @if (session('success_password'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success_password') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Cerrar"></button>
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <p class="mb-0"><strong>Por favor, verifica la siguiente información:</strong></p>
                                    <ul class="mb-0">
                                        @if ($errors->has('current_password'))
                                            <li>La contraseña actual no es correcta</li>
                                        @endif

                                        @if ($errors->has('new_password'))
                                            <li>La nueva contraseña debe tener al menos 8 caracteres y contener letras y números
                                            </li>
                                        @endif

                                        @if ($errors->has('new_password_confirmation'))
                                            <li>Las contraseñas no coinciden</li>
                                        @endif

                                        @if (!$errors->has('current_password') && !$errors->has('new_password') && !$errors->has('new_password_confirmation') && $errors->any())
                                            <li>Ha ocurrido un error al actualizar tu contraseña. Por favor, inténtalo nuevamente.
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            @endif

                            <div class="password-strength-info mb-4">
                                <h4>Recomendaciones para una contraseña segura:</h4>
                                <ul>
                                    <li>Utiliza al menos 8 caracteres</li>
                                    <li>Combina letras mayúsculas y minúsculas</li>
                                    <li>Incluye números y símbolos</li>
                                    <li>Evita información personal fácil de adivinar</li>
                                    <li>No uses la misma contraseña en múltiples sitios</li>
                                </ul>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="current_password">Contraseña Actual</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="current_password"
                                                name="current_password" required>
                                            <button class="btn btn-outline-secondary toggle-password" type="button"
                                                data-target="current_password">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="new_password">Nueva Contraseña</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="new_password"
                                                name="new_password" required>
                                            <button class="btn btn-outline-secondary toggle-password" type="button"
                                                data-target="new_password">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="new_password_confirmation">Confirmar Nueva Contraseña</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="new_password_confirmation"
                                                name="new_password_confirmation" required>
                                            <button class="btn btn-outline-secondary toggle-password" type="button"
                                                data-target="new_password_confirmation">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="password-meter mt-3">
                                <label>Fortaleza de la contraseña:</label>
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0"
                                        aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <small class="password-strength-text">Ingresa una nueva contraseña</small>
                            </div>

                            <div class="form-actions mt-4">
                                <button type="submit" class="btn btn-warning">Actualizar Contraseña</button>
                                <button type="reset" class="btn btn-outline-secondary ms-2">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>

            <!-- SECCIÓN: PLANES -->
            <section class="tab-pane fade" id="plans" role="tabpanel" aria-labelledby="plans-tab">
                <div class="card settings-card mb-4">
                    <div class="card-header">
                        <h3>Planes de Suscripción</h3>
                        <p class="text-muted">Bee Fractal te ofrece un modelo de negocio B2B de Software como Servicio
                            (SaaS). Para que disfrutes al máximo de sus beneficios, te ofrecemos diferentes planes de
                            suscripción anual, cada uno con 12 meses de acceso garantizado.</p>
                    </div>
                    <div class="card-body">
                        <div class="plan-status mb-4">
                            <h4>Tu Plan Actual</h4>
                            <div class="current-plan-info">
                                <div class="plan-badge {{ $user->plan == 'drone' ? 'plan-free' : 'plan-premium' }}">
                                    <span>{{ ucfirst($user->plan ?? 'drone') }}</span>
                                </div>
                                <div class="plan-details">
                                    <p><strong>Fecha de inicio:</strong> {{ $user->plan_start_date ?? 'N/A' }}</p>
                                    <p><strong>Fecha de vencimiento:</strong> {{ $user->plan_end_date ?? 'N/A' }}</p>
                                    <div class="plan-progress">
                                        <div class="progress">
                                            <div class="progress-bar" role="progressbar"
                                                style="width: {{ $user->plan_progress ?? 0 }}%;"
                                                aria-valuenow="{{ $user->plan_progress ?? 0 }}" aria-valuemin="0"
                                                aria-valuemax="100">
                                                {{ $user->plan_progress ?? 0 }}%
                                            </div>
                                        </div>
                                        <small>{{ $user->plan_days_left ?? 0 }} días restantes</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('payment.initiate') }}" method="GET" class="plans-form">
                            <div class="table-responsive mb-4">
                                <table class="table table-bordered plans-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Plan</th>
                                            <th>Características</th>
                                            <th>Precio ($)</th>
                                            <th>Seleccionar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="{{ $user->plan == 'drone' ? 'current-plan' : '' }}">
                                            <td><strong>Drone</strong></td>
                                            <td>
                                                <ul class="plan-features">
                                                    <li>Vive la experiencia durante 16 días de prueba</li>
                                                    <li>Acceso limitado a funcionalidades básicas</li>
                                                    <li>Ideal para conocer la plataforma</li>
                                                </ul>
                                            </td>
                                            <td>Prueba gratuita</td>
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="plan" id="planDrone"
                                                        value="drone" disabled {{ $user->plan == 'drone' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="planDrone">Seleccionar</label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="{{ $user->plan == 'afc' ? 'current-plan' : '' }}">
                                            <td><strong>WorkerBee AFC</strong></td>
                                            <td>
                                                <ul class="plan-features">
                                                    <li>1 Usuario Administrador</li>
                                                    <li>Apiarios ilimitados hasta 299 colmenas</li>
                                                    <li>Acceso a todas las funcionalidades básicas</li>
                                                    <li>Soporte técnico por correo electrónico</li>
                                                </ul>
                                            </td>
                                            <td>
                                                <div class="price-info">
                                                    <div class="main-price">$69.900 + IVA</div>
                                                    <div class="price-details">
                                                        <small>*Costo Mensual: $5.825 + IVA</small>
                                                        <small>*Costo Anual por Colmena: $234</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="plan" id="planAFC"
                                                        value="anual" {{ $user->plan == 'afc' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="planAFC">Seleccionar</label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="{{ $user->plan == 'me' ? 'current-plan' : '' }}">
                                            <td><strong>WorkerBee ME</strong></td>
                                            <td>
                                                <ul class="plan-features">
                                                    <li>1 Usuario Administrador</li>
                                                    <li>1 Colaborador</li>
                                                    <li>Apiarios ilimitados hasta 799 colmenas</li>
                                                    <li>Acceso a todas las funcionalidades</li>
                                                    <li>Soporte técnico prioritario</li>
                                                </ul>
                                            </td>
                                            <td>
                                                <div class="price-info">
                                                    <div class="main-price">$87.900 + IVA</div>
                                                    <div class="price-details">
                                                        <small>*Costo Mensual: $7.325 + IVA</small>
                                                        <small>*Costo Anual por Colmena: $110</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="plan" id="planME"
                                                        value="me" {{ $user->plan == 'me' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="planME">Seleccionar</label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="{{ $user->plan == 'ge' ? 'current-plan' : '' }}">
                                            <td><strong>WorkerBee GE</strong></td>
                                            <td>
                                                <ul class="plan-features">
                                                    <li>1 Usuario Administrador</li>
                                                    <li>3 Colaboradores</li>
                                                    <li>Apiarios ilimitados y sin límite de colmenas</li>
                                                    <li>Acceso a todas las funcionalidades premium</li>
                                                    <li>Soporte técnico prioritario 24/7</li>
                                                    <li>Capacitación personalizada</li>
                                                </ul>
                                            </td>
                                            <td>
                                                <div class="price-info">
                                                    <div class="main-price">$150.900 + IVA</div>
                                                    <div class="price-details">
                                                        <small>*Costo Mensual: $12.575 + IVA</small>
                                                        <small>*Costo Anual por Colmena: $86</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="plan" id="planGE"
                                                        value="ge" {{ $user->plan == 'ge' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="planGE">Seleccionar</label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Queen</strong></td>
                                            <td>
                                                <ul class="plan-features">
                                                    <li>Plan empresarial personalizado</li>
                                                    <li>Usuarios ilimitados</li>
                                                    <li>Funcionalidades exclusivas</li>
                                                    <li>Integración con sistemas propios</li>
                                                    <li>Soporte dedicado</li>
                                                </ul>
                                            </td>
                                            <td>Consultar</td>
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="plan" id="planQueen"
                                                        value="queen" disabled>
                                                    <label class="form-check-label" for="planQueen">Próximamente</label>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="plan-notes">
                                <p class="text-muted"><i class="bi bi-info-circle"></i> Recuerda ingresar tus datos de
                                    facturación antes de suscribirte.</p>
                                <p class="text-muted"><i class="bi bi-calendar-check"></i> 15 días antes del vencimiento de
                                    tu plan, te enviaremos un correo electrónico para invitarte a renovar tu suscripción.
                                </p>
                                <p class="text-muted"><i class="bi bi-shield-check"></i> Todos los pagos son procesados de
                                    forma segura a través de WebPay Plus.</p>
                            </div>

                            <div class="form-actions mt-4">
                                <button type="submit" class="btn btn-success">Suscribirse Ahora</button>
                                <a href="#" class="btn btn-outline-primary ms-2" data-bs-toggle="modal"
                                    data-bs-target="#planComparisonModal">Comparar Planes</a>
                            </div>
                        </form>
                    </div>
                </div>
            </section>

            <!-- SECCIÓN: PERMISOS -->
            @include('user.partials.permissions')
            <!-- SECCIÓN: PREFERENCIAS -->
            @include('user.partials.preferences')
            <!-- SECCIÓN: UTILIDADES -->
            @include('user.partials.utilities')

        </div>
    </div>

    <!-- Modales para las utilidades -->
    <div class="modal fade" id="alertModal" tabindex="-1" aria-labelledby="alertModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="alertModalLabel">Configurar Alertas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form id="alertFormModal">
                        <div class="mb-3">
                            <label for="alertTitle" class="form-label">Título de la Alerta</label>
                            <input type="text" class="form-control" id="alertTitle" placeholder="Ej: Alerta de inspección">
                        </div>
                        <div class="mb-3">
                            <label for="alertDescription" class="form-label">Descripción</label>
                            <textarea class="form-control" id="alertDescription" rows="3"
                                placeholder="Describe la alerta"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="alertType" class="form-label">Tipo de Alerta</label>
                            <select class="form-select" id="alertType">
                                <option value="inspection">Inspección</option>
                                <option value="feeding">Alimentación</option>
                                <option value="harvest">Cosecha</option>
                                <option value="treatment">Tratamiento</option>
                                <option value="other">Otro</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="alertDate" class="form-label">Fecha</label>
                            <input type="date" class="form-control" id="alertDate">
                        </div>
                        <div class="mb-3">
                            <label for="alertPriority" class="form-label">Prioridad</label>
                            <select class="form-select" id="alertPriority">
                                <option value="low">Baja</option>
                                <option value="medium">Media</option>
                                <option value="high">Alta</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary">Guardar Alerta</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="reminderModal" tabindex="-1" aria-labelledby="reminderModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reminderModalLabel">Configurar Recordatorios</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form id="reminderFormModal">
                        <div class="mb-3">
                            <label for="reminderTitle" class="form-label">Título del Recordatorio</label>
                            <input type="text" class="form-control" id="reminderTitle"
                                placeholder="Ej: Inspeccionar apiario">
                        </div>
                        <div class="mb-3">
                            <label for="reminderDate" class="form-label">Fecha</label>
                            <input type="date" class="form-control" id="reminderDate">
                        </div>
                        <div class="mb-3">
                            <label for="reminderTime" class="form-label">Hora</label>
                            <input type="time" class="form-control" id="reminderTime">
                        </div>
                        <div class="mb-3">
                            <label for="reminderRepeat" class="form-label">Repetir</label>
                            <select class="form-select" id="reminderRepeat">
                                <option value="none">No repetir</option>
                                <option value="daily">Diariamente</option>
                                <option value="weekly">Semanalmente</option>
                                <option value="monthly">Mensualmente</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="reminderNotes" class="form-label">Notas</label>
                            <textarea class="form-control" id="reminderNotes" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary">Guardar Recordatorio</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="datesModal" tabindex="-1" aria-labelledby="datesModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="datesModalLabel">Gestionar Fechas Importantes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form id="datesForm">
                        <div class="mb-3">
                            <label for="dateTitle" class="form-label">Título</label>
                            <input type="text" class="form-control" id="dateTitle" placeholder="Ej: Cumpleaños de Juan">
                        </div>
                        <div class="mb-3">
                            <label for="dateType" class="form-label">Tipo</label>
                            <select class="form-select" id="dateType">
                                <option value="birthday">Cumpleaños</option>
                                <option value="anniversary">Aniversario</option>
                                <option value="flowering">Inicio de Floración</option>
                                <option value="event">Evento Apícola</option>
                                <option value="other">Otro</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="dateValue" class="form-label">Fecha</label>
                            <input type="date" class="form-control" id="dateValue">
                        </div>
                        <div class="mb-3">
                            <label for="dateRecurring" class="form-label">¿Se repite anualmente?</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="dateRecurring">
                                <label class="form-check-label" for="dateRecurring">Sí, repetir cada año</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="dateNotes" class="form-label">Notas</label>
                            <textarea class="form-control" id="dateNotes" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary">Guardar Fecha</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="contactsModal" tabindex="-1" aria-labelledby="contactsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="contactsModalLabel">Gestionar Contactos de Emergencia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form id="contactsForm">
                        <div class="mb-3">
                            <label for="contactName" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="contactName" placeholder="Nombre completo">
                        </div>
                        <div class="mb-3">
                            <label for="contactRelation" class="form-label">Relación</label>
                            <select class="form-select" id="contactRelation">
                                <option value="family">Familiar</option>
                                <option value="friend">Amigo</option>
                                <option value="colleague">Colega Apicultor</option>
                                <option value="vet">Veterinario</option>
                                <option value="emergency">Servicio de Emergencia</option>
                                <option value="supplier">Proveedor</option>
                                <option value="other">Otro</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="contactPhone" class="form-label">Teléfono</label>
                            <div class="input-group">
                                <span class="input-group-text">+56</span>
                                <input type="tel" class="form-control" id="contactPhone" placeholder="912345678"
                                    pattern="\d{9}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="contactEmail" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="contactEmail" placeholder="correo@ejemplo.com">
                        </div>
                        <div class="mb-3">
                            <label for="contactAddress" class="form-label">Dirección</label>
                            <input type="text" class="form-control" id="contactAddress" placeholder="Dirección completa">
                        </div>
                        <div class="mb-3">
                            <label for="contactNotes" class="form-label">Notas</label>
                            <textarea class="form-control" id="contactNotes" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary">Guardar Contacto</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Información DTE -->
    <div class="modal fade" id="dteInfoModal" tabindex="-1" aria-labelledby="dteInfoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="dteInfoModalLabel">Información sobre DTE</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <h6>¿Qué es un Documento Tributario Electrónico (DTE)?</h6>
                    <p>Los Documentos Tributarios Electrónicos (DTE) son versiones digitales de los documentos tributarios
                        tradicionales como facturas, boletas, notas de crédito, etc. Estos documentos tienen la misma
                        validez legal que sus equivalentes en papel.</p>

                    <h6>Beneficios de recibir DTE por email:</h6>
                    <ul>
                        <li>Acceso inmediato a tus documentos tributarios</li>
                        <li>Reducción del uso de papel, contribuyendo al medio ambiente</li>
                        <li>Facilidad para archivar y buscar documentos históricos</li>
                        <li>Mayor seguridad en la recepción de documentos</li>
                    </ul>

                    <h6>Consideraciones importantes:</h6>
                    <p>Al autorizar el envío de DTE por email, recibirás todos tus documentos tributarios en la dirección de
                        correo electrónico que hayas proporcionado. Es importante mantener esta información actualizada.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Entendido</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Comparación de Planes -->
    <div class="modal fade" id="planComparisonModal" tabindex="-1" aria-labelledby="planComparisonModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="planComparisonModalLabel">Comparación Detallada de Planes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered plan-comparison-table">
                            <thead>
                                <tr>
                                    <th>Características</th>
                                    <th>Drone</th>
                                    <th>WorkerBee AFC</th>
                                    <th>WorkerBee ME</th>
                                    <th>WorkerBee GE</th>
                                    <th>Queen</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Filas de comparación de características -->
                                <!-- Contenido detallado de comparación de planes -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const regiones = @json($regiones);
            const regionSelect = document.getElementById('region');
            const comunaSelect = document.getElementById('comuna');
            const userComunaId = {{ $user->id_comuna ?? 'null' }};

            const billingRegionSelect = document.getElementById('billing_region');
            const billingComunaSelect = document.getElementById('billing_comuna');
            const userBillingComunaId = {{ $user->billing_comuna ?? 'null' }};

            function loadComunas(regionId, comunaSelect, selectedComunaId = null) {
                comunaSelect.innerHTML = '<option value="">Seleccione una comuna</option>';
                comunaSelect.disabled = true;

                if (regionId) {
                    const region = regiones.find(r => r.id == regionId);

                    if (region) {
                        region.comunas.forEach(comuna => {
                            const option = document.createElement('option');
                            option.value = comuna.id;
                            option.textContent = comuna.nombre;
                            if (comuna.id == selectedComunaId) {
                                option.selected = true;
                            }
                            comunaSelect.appendChild(option);
                        });
                        comunaSelect.disabled = false;
                    }
                }
            }

            // Cargar comunas al cambiar la región (datos personales)
            regionSelect.addEventListener('change', function () {
                loadComunas(this.value, comunaSelect);
            });

            // Cargar comunas al cambiar la región (datos de facturación)
            billingRegionSelect.addEventListener('change', function () {
                loadComunas(this.value, billingComunaSelect);
            });

            // Si hay una región seleccionada por defecto, cargar sus comunas (datos personales)
            if (regionSelect.value) {
                loadComunas(regionSelect.value, comunaSelect, userComunaId);
            }

            // Si hay una región de facturación seleccionada por defecto, cargar sus comunas
            if (billingRegionSelect.value) {
                loadComunas(billingRegionSelect.value, billingComunaSelect, userBillingComunaId);
            }

            // Previsualización de imagen de perfil
            const profilePictureInput = document.getElementById('profile_picture');
            const imagePreview = document.getElementById('imagePreview');

            if (profilePictureInput) {
                profilePictureInput.addEventListener('change', function (event) {
                    const file = event.target.files[0];

                    if (file) {
                        // Validar que sea una imagen
                        if (file.type.startsWith('image/')) {
                            const reader = new FileReader();

                            reader.onload = function (e) {
                                imagePreview.src = e.target.result;
                            };

                            reader.readAsDataURL(file);
                        } else {
                            alert('Por favor selecciona un archivo de imagen válido');
                            profilePictureInput.value = '';
                        }
                    }
                });
            }

            document.querySelectorAll('.toggle-password').forEach(button => {
                button.addEventListener('click', function () {
                    const targetId = this.getAttribute('data-target');
                    const passwordInput = document.getElementById(targetId);

                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        this.innerHTML = '<i class="bi bi-eye-slash"></i>';
                    } else {
                        passwordInput.type = 'password';
                        this.innerHTML = '<i class="bi bi-eye"></i>';
                    }
                });
            });

            // Medidor de fortaleza de contraseña
            const passwordInput = document.getElementById('new_password');
            const passwordMeter = document.querySelector('.progress-bar');
            const passwordText = document.querySelector('.password-strength-text');

            if (passwordInput) {
                passwordInput.addEventListener('input', function () {
                    const password = this.value;
                    let strength = 0;
                    let message = '';

                    if (password.length >= 8) strength += 25;
                    if (password.match(/[a-z]/)) strength += 25;
                    if (password.match(/[A-Z]/)) strength += 25;
                    if (password.match(/[0-9]/)) strength += 25;

                    passwordMeter.style.width = strength + '%';

                    if (strength < 25) {
                        passwordMeter.className = 'progress-bar bg-danger';
                        message = 'Muy débil';
                    } else if (strength < 50) {
                        passwordMeter.className = 'progress-bar bg-warning';
                        message = 'Débil';
                    } else if (strength < 75) {
                        passwordMeter.className = 'progress-bar bg-info';
                        message = 'Buena';
                    } else {
                        passwordMeter.className = 'progress-bar bg-success';
                        message = 'Fuerte';
                    }

                    passwordText.textContent = message;
                });
            }

            // Inicializar tooltips de Bootstrap
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            document.querySelector('.user-data-form').addEventListener('submit', function () {
                comunaSelect.disabled = false;
            });
        });
    </script>
@endsection

@section('optional-scripts')
    <link href="{{ asset('./css/components/home-user/settings.css') }}" rel="stylesheet">
@endsection