@extends('layouts.app')

@section('title', 'B-MaiA - Configuración de la Cuenta')

@section('content')
@push('styles')
<style>
  .btn-icon{
    width: 36px; height: 36px; padding: 0;
    display:inline-flex; align-items:center; justify-content:center;
    border-radius: 50%;
  }
  .invoice-table .btn-icon i{ font-size:1rem; line-height:1; }
</style>
@endpush

    <head>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    </head>

    <div class="container">
        <header class="settings-header">
            <h1>Configuración de la Cuenta</h1>
            <!--<p class="settings-description">Gestiona todos los aspectos de tu cuenta de BeeMaiA para optimizar tu
                experiencia apícola.</p> -->
        </header>

        <!-- Navegación de Pestañas -->
        <nav class="settings-navigation">
            <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="user-data-tab" data-bs-toggle="tab" href="#user-data" role="tab"
                        aria-controls="user-data" aria-selected="true">Datos del Usuario/a</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="billing-tab" data-bs-toggle="tab" href="#billing" role="tab"
                        aria-controls="billing" aria-selected="false">Datos de Facturación</a>
                </li>
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
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="profile_picture" style="text-align: center;">Foto de Perfil</label>
                                        <div class="profile-picture-preview mb-3">
                                            <img id="imagePreview"
                                                src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : asset('img/avatar/avatar_02.svg') }}"
                                                alt="Vista previa"
                                                style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 3px solid #dee2e6; display: block; margin: 0 auto;">
                                        </div>

                                        <input type="file" class="form-control input-file-sm" id="profile_picture" name="profile_picture" accept="image/*"
                                            style="max-width:450px; margin:0 auto; display:block;">
                                        <small class="form-text text-muted">Formatos aceptados: JPG, PNG (máx. 2MB)</small>
                                    </div>
                                </div>
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
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                    </ul>
                </div>
            @endif

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
                                <input type="hidden" name="autorizacion_envio_dte" value="0">
                                <div class="form-check">
                                    <input type="checkbox" id="autorizacion_envio_dte" name="autorizacion_envio_dte" value="1"
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
                                <button type="submit" class="btn btn-primary">Guardar Datos</button>
                                <button type="reset" class="btn btn-outline-secondary ms-2">Restablecer</button>
                            </div>
                        </form>

                    </div>
                </div>

                <div class="card settings-card mt-4">
                    <div class="card-header">
                        <h3 class="text-center fw-bold">Historial de Facturas</h3>
                        <p class="text-muted text-center">Consulta el registro de tus facturas emitidas y su estado de pago.</p>
                    </div>
                    <div class="card-body">
                        <div class="invoice-filters mb-3">
                            <form method="GET" action="{{ route('user.settings') }}">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-4">
                                        {{-- Elimina el label para que coincida con el diseño --}}
                                        <select class="form-select" id="invoice-year" name="year">
                                            <option value="all" {{ $selectedYear==='all' ? 'selected' : '' }}>Año: Todos</option>
                                            @foreach($years as $y)
                                                <option value="{{ $y }}" {{ (string)$selectedYear===(string)$y ? 'selected' : '' }}>Año: {{ $y }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        {{-- Elimina el label para que coincida con el diseño --}}
                                        <select class="form-select" id="invoice-status" name="estado">
                                            @php $estados = ['all'=>'Todos','emitida'=>'Emitida','pendiente'=>'Pendiente','anulada'=>'Anulada','ajustada'=>'Ajustada']; @endphp
                                            @foreach($estados as $k=>$label)
                                                <option value="{{ $k }}" {{ $selectedEstado===$k ? 'selected' : '' }}>Estado: {{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-outline-primary w-100">Filtrar</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <hr>

                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle invoice-table">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-start">Folio / Nº</th>
                                        <th class="text-start">Emisión</th>
                                        <th class="text-start">Vence factura</th>
                                        <th class="text-start">Vence plan</th>
                                        <th class="text-start">Plan</th>
                                        <th class="text-end">Neto</th>
                                        <th class="text-end">IVA</th>
                                        <th class="text-end">Total</th>
                                        <th class="text-start">Estado</th>
                                        <th class="text-start">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($facturas as $f)
                                        @php
                                            // snapshot seguro (soporta array ya casteado o string JSON antiguo)
                                            $snap = $f->datos_facturacion_snapshot;
                                            if (is_string($snap)) {
                                                $snap = json_decode($snap, true) ?? [];
                                            }

                                            // helpers visuales
                                            $estadoMap = ['emitida'=>'success','pendiente'=>'warning','anulada'=>'secondary','ajustada'=>'info'];
                                            $badge = $estadoMap[$f->estado] ?? 'secondary';
                                            $offcanvasId = 'detalleFactura'.$f->id;

                                            // urls si existen (accessors del modelo resuelven S3/local)
                                            $pdfUrl = $f->pdf_url;   // puede ser null
                                            $xmlUrl = $f->xml_url;   // puede ser null
                                        @endphp

                                        @php
                                            // 365 si no tienes config/plans.php
                                            $duration = config('plans.duration_days', 365);

                                            // Si existe relación con Payment:
                                            $vencePlan = null;
                                            if ($f->payment) {
                                                // Si más adelante agregas columna expires_at en payments, se usa
                                                $vencePlan = $f->payment->expires_at
                                                    ?? optional($f->payment->created_at)->copy()->addDays($duration);
                                            }

                                            // Fallback: si no hay payment asociado, estimar desde la emisión de la factura
                                            if (!$vencePlan) {
                                                $vencePlan = optional($f->fecha_emision)->copy()->addDays($duration);
                                            }
                                        @endphp
                                        <tr>
                                            <td class="font-monospace">{{ $f->numero_mostrar }}</td>
                                            <td>{{ $f->fecha_emision?->format('d/m/Y') ?? '—' }}</td>
                                            <td>{{ $f->fecha_vencimiento?->format('d/m/Y') ?? '—' }}</td>
                                            <td>{{ $vencePlan ? $vencePlan->format('d/m/Y') : '—' }}</td>
                                            <td>{{ strtoupper($f->plan ?? '—') }}</td>
                                            <td class="text-end">${{ number_format((int)$f->monto_neto, 0, ',', '.') }}</td>
                                            <td class="text-end">${{ number_format((int)$f->monto_iva, 0, ',', '.') }}</td>
                                            <td class="text-end fw-semibold">${{ number_format((int)$f->monto_total, 0, ',', '.') }}</td>
                                            <td><span class="badge bg-{{ $badge }}">{{ ucfirst($f->estado) }}</span></td>

                                            <td class="text-nowrap">
                                                {{-- Ver detalle (offcanvas) --}}
                                                <button title="Ver factura"
                                                        class="btn btn-icon btn-outline-dark me-1"
                                                        data-bs-toggle="offcanvas"
                                                        data-bs-target="#{{ $offcanvasId }}">
                                                    <i class="bi bi-eye"></i>
                                                </button>

                                                @php
                                                    // Rutas dedicadas (recomendado)
                                                    $descargarUrl = route('facturas.descargar', $f);
                                                @endphp

                                                @if($descargarUrl)
                                                    <a href="{{ $descargarUrl }}"
                                                    class="btn btn-icon btn-outline-success"
                                                    data-bs-toggle="tooltip" title="Descargar PDF">
                                                    <i class="bi bi-download"></i>
                                                    </a>
                                                @else
                                                    <span class="badge bg-secondary align-middle">Sin PDF</span>
                                                @endif
                                            </td>


                                        </tr>

                                        {{-- Offcanvas de detalle (fuera de la fila para mantener el DOM limpio) --}}
                                        <div class="offcanvas offcanvas-end" tabindex="-1" id="{{ $offcanvasId }}">
                                            <div class="offcanvas-header">
                                                <h5 class="offcanvas-title">Factura {{ $f->numero_mostrar }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                                            </div>
                                            <div class="offcanvas-body small">
                                                <p class="mb-1"><strong>Estado SII:</strong> {{ ucfirst($f->estado) }}</p>
                                                @if($f->sii_track_id)
                                                    <p class="mb-1"><strong>Track ID:</strong> <span class="font-monospace">{{ $f->sii_track_id }}</span></p>
                                                @endif
                                                <hr>
                                                <p class="mb-1"><strong>Receptor:</strong> {{ $snap['razon_social'] ?? '—' }}</p>
                                                <p class="mb-1"><strong>RUT:</strong> {{ $snap['rut'] ?? '—' }}</p>
                                                <p class="mb-1"><strong>Giro:</strong> {{ $snap['giro'] ?? '—' }}</p>
                                                <p class="mb-1"><strong>Dirección:</strong> {{ $snap['direccion_comercial'] ?? '—' }}</p>
                                                <p class="mb-1"><strong>Ciudad:</strong> {{ $snap['ciudad'] ?? '—' }}</p>
                                                <p class="mb-1"><strong>Región:</strong> {{ $snap['region'] ?? ($snap['region_nombre'] ?? '—') }}</p>
                                                <p class="mb-1"><strong>Teléfono:</strong> {{ $snap['telefono'] ?? '—' }}</p>
                                                <p class="mb-1"><strong>Correo:</strong> {{ $snap['correo'] ?? ($snap['correo_envio_dte'] ?? '—') }}</p>
                                                <hr>
                                                <p class="mb-1"><strong>Moneda:</strong> {{ $f->moneda ?? 'CLP' }}</p>
                                                <p class="mb-1"><strong>Plan:</strong> {{ strtoupper($f->plan ?? '—') }}</p>
                                                <p class="mb-1"><strong>Emisión:</strong> {{ $f->fecha_emision?->format('d/m/Y H:i') ?? '—' }}</p>
                                                <p class="mb-1"><strong>Vencimiento:</strong> {{ $f->fecha_vencimiento?->format('d/m/Y') ?? '—' }}</p>
                                                <hr>
                                                <p class="mb-1"><strong>Neto:</strong> ${{ number_format((int)$f->monto_neto, 0, ',', '.') }}</p>
                                                <p class="mb-1"><strong>IVA ({{ $f->porcentaje_iva ?? 19 }}%):</strong> ${{ number_format((int)$f->monto_iva, 0, ',', '.') }}</p>
                                                <p class="mb-1"><strong>Total:</strong> ${{ number_format((int)$f->monto_total, 0, ',', '.') }}</p>
                                            </div>
                                        </div>
                                    @empty
                                        <tr><td colspan="9" class="text-center">No hay facturas disponibles</td></tr>
                                    @endforelse
                                    </tbody>


                            </table>
                        </div>

                        @if($facturas->hasPages())
                        <div class="mt-3">
                            {{ $facturas->onEachSide(1)->links() }}
                        </div>
                        @endif
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
                                    <p><strong>Fecha de inicio:</strong> {{ $plan_start_date ?? 'N/A' }}</p>
                                    <p><strong>Fecha de vencimiento:</strong> {{ $plan_end_date ?? 'N/A' }}</p>
                                    <div class="plan-progress">
                                        <div class="progress">
                                            <div class="progress-bar" role="progressbar"
                                                style="width: {{ $plan_progress ?? 0 }}%;"
                                                aria-valuenow="{{ $plan_progress ?? 0 }}" aria-valuemin="0"
                                                aria-valuemax="100">
                                                {{ $plan_progress ?? 0 }}%
                                            </div>
                                        </div>
                                        <small>
                                            {{ $plan_days_left }} días
                                            @if($plan_hours_left > 0)
                                                y {{ $plan_hours_left }} horas
                                            @endif
                                            restantes
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @php
                            $isAugust = now()->month == 8;
                            $afcPrice = $isAugust ? intval(round(69900 * 0.7)) : 69900;
                            $mePrice = $isAugust ? intval(round(87900 * 0.7)) : 87900;
                            $gePrice = $isAugust ? intval(round(150900 * 0.7)) : 150900;
                        @endphp

                        @php
                            $datosFacturacionCompletos = $user->datosFacturacion &&
                                $user->datosFacturacion->razon_social &&
                                $user->datosFacturacion->rut &&
                                $user->datosFacturacion->correo_envio_dte;
                        @endphp

                        @if (!$datosFacturacionCompletos)
                            <div class="alert alert-warning d-flex align-items-center" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <div>
                                    Debes completar tus <strong>datos de facturación</strong> antes de seleccionar un plan.
<!--<a href="{{ route('user.settings') }}#billing" class="btn btn-sm btn-outline-dark ms-2">
                                        Completar ahora
                                    </a> -->
                                </div>
                            </div>
                        @endif


                        <form action="{{ route('payment.initiate') }}" method="POST" class="plans-form">
                            @csrf

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

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
                                                    <li>Acceso a todas las funcionalidades básicas</li>
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
                                                    <li>Soporte técnico estándar</li>
                                                </ul>
                                            </td>
                                            <td>
                                                <div class="price-info">
                                                    <div class="main-price">
                                                        @if($isAugust)
                                                            <span style="text-decoration:line-through; color:#888;">$69.900</span>
                                                            <span style="color:#FF8C00; font-weight:bold;">${{ number_format($afcPrice, 0, ',', '.') }} + IVA</span>
                                                            <span class="badge bg-success ms-2">-30% Agosto</span>
                                                        @else
                                                            $69.900 + IVA
                                                        @endif
                                                    </div>
                                                    <div class="price-details">
                                                        <small>*Costo Mensual: $5.825 + IVA</small>
                                                        <small>*Costo Anual por Colmena: $234</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="plan" id="planAFC"
                                                        value="afc" {{ $user->plan == 'afc' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="planAFC">Seleccionar</label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="{{ $user->plan == 'me' ? 'current-plan' : '' }}">
                                            <td><strong>WorkerBee ME</strong></td>
                                            <td>
                                                <ul class="plan-features">
                                                    <li>1 Usuario Administrador</li>
                                                    <li>Apiarios ilimitados hasta 799 colmenas</li>
                                                    <li>Acceso a todas las funcionalidades básicas</li>
                                                    <li>Soporte técnico prioritario</li>
                                                </ul>
                                            </td>
                                            <td>
                                                <div class="price-info">
                                                    <div class="main-price">
                                                        @if($isAugust)
                                                            <span style="text-decoration:line-through; color:#888;">$87.900</span>
                                                            <span style="color:#FF8C00; font-weight:bold;">${{ number_format($mePrice, 0, ',', '.') }} + IVA</span>
                                                            <span class="badge bg-success ms-2">-30% Agosto</span>
                                                        @else
                                                            $87.900 + IVA
                                                        @endif
                                                    </div>
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
                                                    <li>Apiarios ilimitados y sin límite de colmenas</li>
                                                    <li>Acceso a todas las funcionalidades básicas</li>
                                                    <li>Soporte técnico prioritario 24/7</li>
                                                    <li>Capacitación personalizada</li>
                                                </ul>
                                            </td>
                                            <td>
                                                <div class="price-info">
                                                    <div class="main-price">
                                                        @if($isAugust)
                                                            <span style="text-decoration:line-through; color:#888;">$150.900</span>
                                                            <span style="color:#FF8C00; font-weight:bold;">${{ number_format($gePrice, 0, ',', '.') }} + IVA</span>
                                                            <span class="badge bg-success ms-2">-30% Agosto</span>
                                                        @else
                                                            $150.900 + IVA
                                                        @endif
                                                    </div>
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
                                <!--<a href="#" class="btn btn-outline-primary ms-2" data-bs-toggle="modal"
                                    data-bs-target="#planComparisonModal">Comparar Planes</a> -->
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
            const userBillingComunaId = "{{ old('billing_comuna', $datosFacturacion->comuna_id ?? '') }}";

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

        function handleProfileImageCompression() {
                const input = document.getElementById('profile_picture');
                const preview = document.getElementById('imagePreview');

                if (!input || !preview) return;

                input.addEventListener('change', function (event) {
                    const file = event.target.files[0];
                    if (!file) return;

                    const acceptedTypes = [
                        'image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/bmp', 'image/svg+xml'
                    ];
                    if (!acceptedTypes.includes(file.type)) {
                        alert('Por favor selecciona una imagen en formato JPG, PNG, WEBP, GIF, BMP o SVG.');
                        input.value = '';
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const img = new Image();
                        img.onload = function () {
                            const canvas = document.createElement('canvas');
                            const maxWidth = 600;
                            const maxHeight = 600;
                            let width = img.width;
                            let height = img.height;

                            if (width > maxWidth || height > maxHeight) {
                                if (width > height) {
                                    height = Math.round(height * (maxWidth / width));
                                    width = maxWidth;
                                } else {
                                    width = Math.round(width * (maxHeight / height));
                                    height = maxHeight;
                                }
                            }

                            canvas.width = width;
                            canvas.height = height;
                            const ctx = canvas.getContext('2d');
                            ctx.drawImage(img, 0, 0, width, height);

                            // Calidad 0.85 para mejor calidad visual
                            canvas.toBlob(function (blob) {
                                preview.src = URL.createObjectURL(blob);

                                let sizeDiv = document.getElementById('imageSize');
                                if (!sizeDiv) {
                                    sizeDiv = document.createElement('div');
                                    sizeDiv.id = 'imageSize';
                                    sizeDiv.style.textAlign = 'center';
                                    sizeDiv.style.color = '#888';
                                    sizeDiv.style.fontSize = '0.95em';
                                    sizeDiv.style.marginTop = '0.5em';
                                    input.parentNode.insertBefore(sizeDiv, input.nextSibling);
                                }
                                const sizeKB = (blob.size / 1024).toFixed(1);

                                const compressedFile = new File([blob], file.name.replace(/\.[^/.]+$/, ".jpg"), { type: "image/jpeg" });
                                const dataTransfer = new DataTransfer();
                                dataTransfer.items.add(compressedFile);
                                input.files = dataTransfer.files;
                            }, 'image/jpeg', 0.85);
                        };
                        img.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                });
            }

            document.addEventListener('DOMContentLoaded', handleProfileImageCompression);
    </script>
@endsection

@section('optional-scripts')
    <link href="{{ asset('./css/components/home-user/settings.css') }}" rel="stylesheet">
@endsection