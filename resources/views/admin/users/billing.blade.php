@extends('layouts.admin')

@section('title', 'Facturación - ' . $user->name)

@section('content')
<div style="padding: 20px;">
    <!-- Header -->
    <div style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(255, 255, 255, 0.95) 100%);
                backdrop-filter: blur(10px);
                padding: 25px 30px;
                margin-bottom: 30px;
                border-radius: 15px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                border: 1px solid rgba(148, 163, 184, 0.1);">

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <div>
                <a href="{{ route('admin.users.index') }}"
                   style="color: #64748b; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; margin-bottom: 10px; font-weight: 600; transition: color 0.2s;"
                   onmouseover="this.style.color='#2563eb'"
                   onmouseout="this.style.color='#64748b'">
                    <i class="fas fa-arrow-left"></i> Volver a usuarios
                </a>
                <h2 style="margin: 0; color: #0f172a; font-weight: 700; font-size: 1.75rem;">
                    <i class="fas fa-file-invoice-dollar" style="color: #2563eb; margin-right: 10px;"></i>
                    Facturación y Pagos
                </h2>
                <p style="margin: 5px 0 0 0; color: #64748b; font-size: 0.95rem;">
                    Usuario: <strong>{{ $user->name }}</strong> ({{ $user->email }})
                </p>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 10px; border-left: 4px solid #10b981;">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 10px; border-left: 4px solid #ef4444;">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        <!-- Columna izquierda: Estado del plan -->
        <div class="col-md-6 mb-4">
            <!-- Estado del Plan -->
            <div style="background: white;
                        border-radius: 15px;
                        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                        padding: 25px;
                        margin-bottom: 20px;">
                <h4 style="margin: 0 0 20px 0; color: #0f172a; font-weight: 700; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-crown" style="color: #f59e0b;"></i>
                    Estado del Plan
                </h4>

                @php
                    $planColors = [
                        'afc' => ['bg' => '#8b5cf6', 'name' => 'AFC'],
                        'me' => ['bg' => '#f59e0b', 'name' => 'ME'],
                        'ge' => ['bg' => '#10b981', 'name' => 'GE'],
                        'drone' => ['bg' => '#64748b', 'name' => 'Drone'],
                    ];
                    $currentPlan = $planColors[$user->plan ?? 'drone'] ?? ['bg' => '#94a3b8', 'name' => 'Sin Plan'];

                    // Calcular días restantes
                    $fechaVencimiento = $user->fecha_vencimiento ? \Carbon\Carbon::parse($user->fecha_vencimiento) : null;

                    // Si no tiene fecha de vencimiento, el plan está pendiente de activación
                    if ($fechaVencimiento === null) {
                        $planActivo = null; // Pendiente de activación
                        $diasRestantes = null;
                    } else {
                        $diasRestantes = now()->diffInDays($fechaVencimiento, false);
                        $planActivo = $diasRestantes >= 0;
                    }
                @endphp

                @if($planActivo === null)
                    <!-- Usuario sin plan activado -->
                    <div style="text-align: center; padding: 60px 20px;">
                        <i class="fas fa-ban" style="font-size: 4rem; color: #cbd5e1; margin-bottom: 20px; display: block;"></i>
                        <div style="font-size: 1.5rem; font-weight: 700; color: #64748b; margin-bottom: 10px;">
                            Sin Plan
                        </div>
                        <div style="font-size: 0.875rem; color: #94a3b8;">
                            Este usuario no ha activado ningún plan aún
                        </div>
                    </div>
                @else
                    <!-- Usuario con plan activado -->
                    <div style="background: linear-gradient(135deg, {{ $currentPlan['bg'] }} 0%, {{ $currentPlan['bg'] }}dd 100%);
                                color: white;
                                padding: 20px;
                                border-radius: 12px;
                                margin-bottom: 20px;">
                        <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 5px;">Plan Actual</div>
                        <div style="font-size: 2rem; font-weight: 700; margin-bottom: 10px;">{{ $currentPlan['name'] }}</div>
                        <div style="font-size: 0.875rem; opacity: 0.9;">
                            Límite de colmenas: <strong>{{ $user->colmenaLimit() ?? 'Sin límite' }}</strong>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div style="background: #f8fafc; padding: 15px; border-radius: 10px;">
                            <div style="color: #64748b; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; margin-bottom: 5px;">
                                Estado
                            </div>
                            <div style="font-size: 1.25rem; font-weight: 700; color: {{ $planActivo ? '#10b981' : '#ef4444' }}; display: flex; align-items: center; gap: 8px;">
                                @if($planActivo)
                                    <i class="fas fa-check-circle" style="color: #10b981;"></i>
                                @else
                                    <i class="fas fa-times-circle" style="color: #ef4444;"></i>
                                @endif
                                {{ $planActivo ? 'Activo' : 'Vencido' }}
                            </div>
                        </div>

                        <div style="background: #f8fafc; padding: 15px; border-radius: 10px;">
                            <div style="color: #64748b; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; margin-bottom: 5px;">
                                Vencimiento
                            </div>
                            <div style="font-size: 1rem; font-weight: 700; color: #0f172a;">
                                {{ $fechaVencimiento->format('d/m/Y') }}
                            </div>
                        </div>

                    @if($diasRestantes !== null)
                    <div style="background: #f8fafc; padding: 15px; border-radius: 10px; grid-column: 1 / -1;">
                        <div style="color: #64748b; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; margin-bottom: 10px;">
                            Tiempo Restante
                        </div>
                        @php
                            $now = now();
                            $vencido = $fechaVencimiento->isPast();

                            if (!$vencido) {
                                // Plan activo - calcular tiempo restante
                                $totalSegundos = $now->diffInSeconds($fechaVencimiento);
                                $dias = floor($totalSegundos / 86400);
                                $horas = floor(($totalSegundos % 86400) / 3600);
                                $minutos = floor(($totalSegundos % 3600) / 60);
                                $segundos = $totalSegundos % 60;
                            } else {
                                // Plan vencido - calcular tiempo desde vencimiento
                                $totalSegundos = $fechaVencimiento->diffInSeconds($now);
                                $dias = floor($totalSegundos / 86400);
                                $horas = floor(($totalSegundos % 86400) / 3600);
                                $minutos = floor(($totalSegundos % 3600) / 60);
                                $segundos = $totalSegundos % 60;
                            }
                        @endphp

                        @if(!$vencido)
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(70px, 1fr)); gap: 10px; margin-bottom: 10px;">
                                <div style="text-align: center; background: white; padding: 10px 5px; border-radius: 8px; border: 2px solid {{ $dias > 7 ? '#10b981' : '#f59e0b' }};">
                                    <div style="font-size: clamp(1.2rem, 4vw, 1.5rem); font-weight: 700; color: {{ $dias > 7 ? '#10b981' : '#f59e0b' }};">
                                        {{ $dias }}
                                    </div>
                                    <div style="font-size: clamp(0.6rem, 2vw, 0.7rem); color: #64748b; font-weight: 600; text-transform: uppercase;">
                                        Días
                                    </div>
                                </div>
                                <div style="text-align: center; background: white; padding: 10px 5px; border-radius: 8px; border: 2px solid {{ $dias > 7 ? '#10b981' : '#f59e0b' }};">
                                    <div style="font-size: clamp(1.2rem, 4vw, 1.5rem); font-weight: 700; color: {{ $dias > 7 ? '#10b981' : '#f59e0b' }};">
                                        {{ $horas }}
                                    </div>
                                    <div style="font-size: clamp(0.6rem, 2vw, 0.7rem); color: #64748b; font-weight: 600; text-transform: uppercase;">
                                        Horas
                                    </div>
                                </div>
                                <div style="text-align: center; background: white; padding: 10px 5px; border-radius: 8px; border: 2px solid {{ $dias > 7 ? '#10b981' : '#f59e0b' }};">
                                    <div style="font-size: clamp(1.2rem, 4vw, 1.5rem); font-weight: 700; color: {{ $dias > 7 ? '#10b981' : '#f59e0b' }};">
                                        {{ $minutos }}
                                    </div>
                                    <div style="font-size: clamp(0.6rem, 2vw, 0.7rem); color: #64748b; font-weight: 600; text-transform: uppercase;">
                                        Minutos
                                    </div>
                                </div>
                                <div style="text-align: center; background: white; padding: 10px 5px; border-radius: 8px; border: 2px solid {{ $dias > 7 ? '#10b981' : '#f59e0b' }};">
                                    <div style="font-size: clamp(1.2rem, 4vw, 1.5rem); font-weight: 700; color: {{ $dias > 7 ? '#10b981' : '#f59e0b' }};">
                                        {{ $segundos }}
                                    </div>
                                    <div style="font-size: clamp(0.6rem, 2vw, 0.7rem); color: #64748b; font-weight: 600; text-transform: uppercase;">
                                        Segundos
                                    </div>
                                </div>
                            </div>
                            <div style="background: #e2e8f0; height: 8px; border-radius: 4px; overflow: hidden;">
                                <div style="background: {{ $dias > 7 ? '#10b981' : '#f59e0b' }}; height: 100%; width: {{ min(100, ($dias / 30) * 100) }}%; transition: width 0.3s;"></div>
                            </div>
                        @else
                            <div style="text-align: center; padding: 15px; background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); border-radius: 8px; border: 2px solid #ef4444;">
                                <div style="font-size: 1.25rem; font-weight: 700; color: #ef4444; margin-bottom: 5px;">
                                    <i class="fas fa-exclamation-triangle"></i> Plan Vencido
                                </div>
                                <div style="font-size: 0.875rem; color: #991b1b;">
                                    Hace {{ $dias }} días, {{ $horas }} horas, {{ $minutos }} minutos
                                </div>
                            </div>
                        @endif
                    </div>
                    @endif
                    </div>
                @endif
            </div>

            <!-- Datos de Facturación / Boleta -->
            <div style="background: white;
                        border-radius: 15px;
                        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                        padding: 25px;">
                @if($user->datosFacturacion)
                    <h4 style="margin: 0 0 20px 0; color: #0f172a; font-weight: 700; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-building" style="color: #3b82f6;"></i>
                        Datos de Facturación
                    </h4>
                    <div style="display: grid; gap: 15px;">
                        <div>
                            <div style="color: #64748b; font-size: 0.75rem; font-weight: 600; margin-bottom: 5px;">Razón Social</div>
                            <div style="color: #0f172a; font-weight: 600;">{{ $user->datosFacturacion->razon_social }}</div>
                        </div>
                        <div>
                            <div style="color: #64748b; font-size: 0.75rem; font-weight: 600; margin-bottom: 5px;">RUT</div>
                            <div style="color: #0f172a; font-weight: 600;">{{ $user->datosFacturacion->rut }}</div>
                        </div>
                        <div>
                            <div style="color: #64748b; font-size: 0.75rem; font-weight: 600; margin-bottom: 5px;">Giro</div>
                            <div style="color: #0f172a;">{{ $user->datosFacturacion->giro }}</div>
                        </div>
                        <div>
                            <div style="color: #64748b; font-size: 0.75rem; font-weight: 600; margin-bottom: 5px;">Dirección</div>
                            <div style="color: #0f172a;">
                                {{ $user->datosFacturacion->direccion_comercial }}<br>
                                {{ $user->datosFacturacion->comuna->nombre ?? 'Sin comuna' }}, {{ $user->datosFacturacion->region->nombre ?? 'Sin región' }}
                            </div>
                        </div>
                        <div>
                            <div style="color: #64748b; font-size: 0.75rem; font-weight: 600; margin-bottom: 5px;">Correo Facturación</div>
                            <div style="color: #0f172a;">{{ $user->datosFacturacion->correo }}</div>
                        </div>
                    </div>
                @else
                    <h4 style="margin: 0 0 20px 0; color: #0f172a; font-weight: 700; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-receipt" style="color: #f59e0b;"></i>
                        Datos de Boleta
                    </h4>
                    <div style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
                                padding: 15px;
                                border-radius: 10px;
                                border-left: 4px solid #f59e0b;
                                margin-bottom: 20px;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <i class="fas fa-user" style="font-size: 1.5rem; color: #92400e;"></i>
                            <div>
                                <strong style="display: block; color: #92400e; margin-bottom: 3px;">Persona Natural</strong>
                                <span style="font-size: 0.9rem; color: #78350f;">Este usuario no tiene datos de facturación. Se emitirán boletas.</span>
                            </div>
                        </div>
                    </div>
                    <div style="display: grid; gap: 15px;">
                        <div>
                            <div style="color: #64748b; font-size: 0.75rem; font-weight: 600; margin-bottom: 5px;">Nombre Completo</div>
                            <div style="color: #0f172a; font-weight: 600;">{{ $user->name }} {{ $user->last_name }}</div>
                        </div>
                        <div>
                            <div style="color: #64748b; font-size: 0.75rem; font-weight: 600; margin-bottom: 5px;">RUT</div>
                            <div style="color: #0f172a; font-weight: 600;">{{ $user->rut ?? 'No registrado' }}</div>
                        </div>
                        <div>
                            <div style="color: #64748b; font-size: 0.75rem; font-weight: 600; margin-bottom: 5px;">Correo Electrónico</div>
                            <div style="color: #0f172a;">{{ $user->email }}</div>
                        </div>
                        <div>
                            <div style="color: #64748b; font-size: 0.75rem; font-weight: 600; margin-bottom: 5px;">Teléfono</div>
                            <div style="color: #0f172a;">+56 {{ $user->telefono ?? 'No registrado' }}</div>
                        </div>
                        <div>
                            <div style="color: #64748b; font-size: 0.75rem; font-weight: 600; margin-bottom: 5px;">Dirección</div>
                            <div style="color: #0f172a;">
                                {{ $user->direccion ?? 'No registrada' }}<br>
                                @if($user->comuna)
                                    {{ $user->comuna->nombre }}, {{ $user->region->nombre ?? '' }}
                                @else
                                    Sin ubicación registrada
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Columna derecha: Historial de facturas -->
        <div class="col-md-6 mb-4">
            <div style="background: white;
                        border-radius: 15px;
                        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                        padding: 25px;">
                <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 20px;">
                    <h4 style="margin: 0; color: #0f172a; font-weight: 700; display: flex; align-items: center; gap: 10px; flex: 1 1 auto; min-width: 0; overflow-wrap: break-word;">
                        <i class="fas fa-file-invoice" style="color: #10b981; flex-shrink: 0;"></i>
                        <span style="line-height: 1.3;">Historial de Facturas/Boletas</span>
                    </h4>
                    <button type="button"
                            class="btn btn-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#emitirFacturaModal"
                            style="background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
                                   border: none;
                                   padding: 8px 16px;
                                   border-radius: 8px;
                                   font-weight: 600;
                                   font-size: 0.875rem;
                                   display: inline-flex;
                                   align-items: center;
                                   gap: 8px;
                                   box-shadow: 0 2px 4px rgba(37, 99, 235, 0.3);
                                   cursor: pointer;
                                   transition: all 0.2s;
                                   white-space: nowrap;
                                   flex-shrink: 0;"
                            onmouseover="this.style.transform='scale(1.05)'; this.style.boxShadow='0 4px 8px rgba(37, 99, 235, 0.4)'"
                            onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 2px 4px rgba(37, 99, 235, 0.3)'">
                        @php
                            $tieneFacturacion = $user->datosFacturacion &&
                                              $user->datosFacturacion->razon_social &&
                                              $user->datosFacturacion->rut;
                            $tipoDocumento = $tieneFacturacion ? 'Factura' : 'Boleta';
                            $tieneDocumentos = ($user->facturas && $user->facturas->count() > 0) || ($user->boletas && $user->boletas->count() > 0);
                            $textoBoton = $tieneDocumentos
                                ? "Nueva $tipoDocumento"
                                : "Emitir $tipoDocumento";
                        @endphp
                        <i class="fas fa-plus"></i>
                        <span class="d-none d-sm-inline">{{ $textoBoton }}</span>
                        <span class="d-inline d-sm-none">+</span>
                    </button>
                </div>

                @if($user->documentos && $user->documentos->count() > 0)
                    <div style="display: grid; gap: 12px;">
                        @foreach($user->documentos as $documento)
                        @php
                            // Mapeo de estados con colores
                            $estadoColors = [
                                'emitida' => '#10b981',   // Verde
                                'pagada' => '#10b981',     // Verde
                                'pendiente' => '#f59e0b',  // Amarillo
                                'anulada' => '#ef4444',    // Rojo
                                'ajustada' => '#3b82f6'    // Azul
                            ];
                            $estadoColor = $estadoColors[$documento->estado] ?? '#64748b';
                        @endphp
                        <div style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
                                    padding: 15px;
                                    border-radius: 10px;
                                    border-left: 4px solid {{ $estadoColor }};">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 10px;">
                                <div>
                                    <div style="font-weight: 700; color: #0f172a; margin-bottom: 5px; display: flex; align-items: center; gap: 8px;">
                                        <span style="background: {{ $documento->tipo_documento === 'Factura' ? '#3b82f6' : '#f59e0b' }};
                                                    color: white;
                                                    padding: 2px 8px;
                                                    border-radius: 4px;
                                                    font-size: 0.7rem;
                                                    font-weight: 700;">
                                            {{ $documento->tipo_documento }}
                                        </span>
                                        #{{ $documento->numero ?? $documento->id }}
                                    </div>
                                    <div style="font-size: 0.875rem; color: #64748b;">
                                        {{ \Carbon\Carbon::parse($documento->fecha_emision)->format('d/m/Y') }}
                                    </div>
                                </div>
                                <span style="background: {{ $estadoColor }};
                                            color: white;
                                            padding: 4px 12px;
                                            border-radius: 6px;
                                            font-size: 0.75rem;
                                            font-weight: 600;
                                            text-transform: uppercase;">
                                    {{ ucfirst($documento->estado) }}
                                </span>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div style="color: #0f172a; font-weight: 600; font-size: 1.125rem;">
                                    ${{ number_format($documento->monto_total ?? 0, 0, ',', '.') }}
                                </div>
                                @if($documento->archivo_pdf ?? null)
                                <a href="{{ asset('storage/' . $documento->archivo_pdf) }}"
                                   target="_blank"
                                   style="color: #2563eb; text-decoration: none; font-size: 0.875rem; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;"
                                   onmouseover="this.style.color='#1e40af'"
                                   onmouseout="this.style.color='#2563eb'">
                                    <i class="fas fa-download"></i> Descargar PDF
                                </a>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align: center; padding: 60px 20px; color: #64748b;">
                        <i class="fas fa-file-invoice" style="font-size: 4rem; color: #cbd5e1; margin-bottom: 20px; display: block;"></i>
                        <p style="margin: 0; font-size: 1.125rem; font-weight: 600;">No hay facturas registradas</p>
                        <p style="margin: 10px 0 0 0; font-size: 0.875rem;">El historial de pagos aparecerá aquí</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Emitir Factura -->
<div class="modal fade" id="emitirFacturaModal" tabindex="-1" aria-labelledby="emitirFacturaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 15px; border: none;">
            <div class="modal-header" style="background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); color: white; border-radius: 15px 15px 0 0;">
                <h5 class="modal-title" id="emitirFacturaModalLabel" style="font-weight: 700;">
                    <i class="fas fa-file-invoice"></i> Emitir {{ $tipoDocumento }} para {{ $user->name }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.facturas.store', $user->id) }}" method="POST">
                @csrf
                <input type="hidden" name="tipo_documento" value="{{ $tieneFacturacion ? 'factura' : 'boleta' }}">

                <div class="modal-body" style="padding: 25px;">
                    <!-- Alerta informativa sobre el tipo de documento -->
                    <div class="alert {{ $tieneFacturacion ? 'alert-info' : 'alert-warning' }} mb-4" style="border-radius: 10px;">
                        <div style="display: flex; align-items: start; gap: 12px;">
                            <i class="fas {{ $tieneFacturacion ? 'fa-building' : 'fa-user' }}" style="font-size: 1.5rem; margin-top: 2px;"></i>
                            <div>
                                <strong style="display: block; margin-bottom: 5px;">
                                    Tipo de documento: {{ $tipoDocumento }}
                                </strong>
                                @if($tieneFacturacion)
                                    <p style="margin: 0; font-size: 0.9rem;">
                                        Este usuario tiene datos de facturación registrados como <strong>{{ $user->datosFacturacion->razon_social }}</strong>.
                                        Se emitirá una factura electrónica.
                                    </p>
                                @else
                                    <p style="margin: 0; font-size: 0.9rem;">
                                        Este usuario no tiene datos de facturación registrados. Se emitirá una boleta como persona natural.
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Plan -->
                        <div class="col-md-6 mb-3">
                            <label for="plan" class="form-label" style="font-weight: 600; color: #0f172a;">Plan</label>
                            <select class="form-select" id="plan" name="plan" required>
                                <option value="">Seleccionar plan...</option>
                                <option value="afc" {{ ($user->plan ?? '') == 'afc' ? 'selected' : '' }}>AFC - Apicultor Familiar Campesino</option>
                                <option value="me" {{ ($user->plan ?? '') == 'me' ? 'selected' : '' }}>ME - Mediana Empresa</option>
                                <option value="ge" {{ ($user->plan ?? '') == 'ge' ? 'selected' : '' }}>GE - Gran Empresa</option>
                                <option value="drone" {{ ($user->plan ?? '') == 'drone' ? 'selected' : '' }}>Drone - Plan Gratuito</option>
                            </select>
                        </div>

                        <!-- Monto Total -->
                        <div class="col-md-6 mb-3">
                            <label for="monto_total_input" class="form-label" style="font-weight: 600; color: #0f172a;">Monto Total (con IVA)</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="monto_total_input" name="monto_total_input" value="69900" required min="0">
                            </div>
                            <input type="hidden" id="monto_neto" name="monto_neto" value="58739">
                            <small class="text-muted">Se carga automáticamente según el plan seleccionado</small>
                        </div>

                        <!-- Duración del Plan (meses) -->
                        <div class="col-md-6 mb-3">
                            <label for="duracion_meses" class="form-label" style="font-weight: 600; color: #0f172a;">Duración del Plan (meses)</label>
                            <input type="number" class="form-control" id="duracion_meses" name="duracion_meses" value="1" required min="1" max="12">
                            <small class="text-muted">Duración mensual del plan (1 mes = 30 días)</small>
                        </div>

                        <!-- Fecha de Emisión -->
                        <div class="col-md-6 mb-3">
                            <label for="fecha_emision" class="form-label" style="font-weight: 600; color: #0f172a;">Fecha de Emisión</label>
                            <input type="date" class="form-control" id="fecha_emision" name="fecha_emision" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <!-- Estado -->
                        <div class="col-md-6 mb-3">
                            <label for="estado" class="form-label" style="font-weight: 600; color: #0f172a;">Estado</label>
                            <select class="form-select" id="estado" name="estado" required>
                                <option value="emitida" selected>Emitida</option>
                                <option value="pagada">Pagada</option>
                                <option value="pendiente">Pendiente</option>
                            </select>
                        </div>

                        <!-- Actualizar Plan del Usuario -->
                        <div class="col-md-6 mb-3">
                            <label for="actualizar_plan" class="form-label" style="font-weight: 600; color: #0f172a;">
                                <input type="checkbox" id="actualizar_plan" name="actualizar_plan" value="1" checked>
                                Actualizar plan del usuario
                            </label>
                            <small class="text-muted d-block">Marca para activar el plan en el usuario</small>
                        </div>
                    </div>

                    <!-- Resumen de Montos -->
                    <div style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
                                padding: 20px;
                                border-radius: 10px;
                                margin-top: 15px;">
                        <h6 style="font-weight: 700; color: #0f172a; margin-bottom: 15px;">
                            <i class="fas fa-calculator"></i> Resumen de Montos
                        </h6>
                        <div style="display: grid; gap: 10px;">
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: #64748b;">Monto Neto:</span>
                                <span id="resumen_neto" style="font-weight: 600; color: #0f172a;">$58,739</span>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: #64748b;">IVA (19%):</span>
                                <span id="resumen_iva" style="font-weight: 600; color: #0f172a;">$11,161</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding-top: 10px; border-top: 2px solid #cbd5e1;">
                                <span style="color: #0f172a; font-weight: 700;">Total:</span>
                                <span id="resumen_total" style="font-weight: 700; color: #2563eb; font-size: 1.25rem;">$69,900</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 2px solid #e2e8f0; padding: 20px;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); border: none;">
                        <i class="fas fa-check"></i> Emitir Factura
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const planSelect = document.getElementById('plan');
    const montoTotalInput = document.getElementById('monto_total_input');
    const montoNetoHidden = document.getElementById('monto_neto');
    const resumenNeto = document.getElementById('resumen_neto');
    const resumenIva = document.getElementById('resumen_iva');
    const resumenTotal = document.getElementById('resumen_total');

    // Precios totales por plan (CON IVA incluido)
    const preciosPlanes = {
        'afc': 69900,   // Plan AFC: $69,900
        'me': 87900,    // Plan ME: $87,900
        'ge': 150900,   // Plan GE: $150,900
        'drone': 0      // Plan Drone: Gratuito
    };

    const duracionMesesInput = document.getElementById('duracion_meses');

    function calcularMontos() {
        const meses = parseInt(duracionMesesInput.value) || 1;
        const plan = planSelect.value;
        const precioBase = preciosPlanes[plan] || 0;

        // Multiplicar el precio base por la cantidad de meses
        const total = precioBase * meses;
        const neto = Math.round(total / 1.19);
        const iva = total - neto;

        // Actualizar campo visible
        montoTotalInput.value = total;

        // Actualizar campo hidden con el neto
        montoNetoHidden.value = neto;

        // Actualizar resumen visual
        resumenNeto.textContent = '$' + neto.toLocaleString('es-CL');
        resumenIva.textContent = '$' + iva.toLocaleString('es-CL');
        resumenTotal.textContent = '$' + total.toLocaleString('es-CL');
    }

    // Actualizar monto cuando cambia el plan
    planSelect.addEventListener('change', function() {
        calcularMontos();
    });

    // Actualizar monto cuando cambia la duración en meses
    duracionMesesInput.addEventListener('input', function() {
        calcularMontos();
    });

    // Si el usuario edita manualmente el monto total
    montoTotalInput.addEventListener('input', function() {
        const total = parseFloat(this.value) || 0;
        const neto = Math.round(total / 1.19);
        const iva = total - neto;

        montoNetoHidden.value = neto;
        resumenNeto.textContent = '$' + neto.toLocaleString('es-CL');
        resumenIva.textContent = '$' + iva.toLocaleString('es-CL');
        resumenTotal.textContent = '$' + total.toLocaleString('es-CL');
    });

    // Calcular inicial si hay un plan preseleccionado
    if (planSelect.value && preciosPlanes[planSelect.value]) {
        montoTotalInput.value = preciosPlanes[planSelect.value];
    }
    calcularMontos();
});
</script>

@endsection
