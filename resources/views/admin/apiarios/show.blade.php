@extends('layouts.admin')

@section('title', 'Detalle del Apiario')

@section('content')
<div style="padding: 20px;">
    <!-- Botón de retorno -->
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.apiarios.index') }}"
           style="display: inline-flex; align-items: center; gap: 8px;
                  padding: 10px 20px;
                  background: linear-gradient(135deg, #64748b 0%, #475569 100%);
                  color: white;
                  text-decoration: none;
                  border-radius: 10px;
                  font-weight: 600;
                  transition: all 0.3s ease;
                  box-shadow: 0 2px 4px rgba(100, 116, 139, 0.3);"
           onmouseover="this.style.transform='translateX(-5px)'; this.style.boxShadow='0 4px 8px rgba(100, 116, 139, 0.4)';"
           onmouseout="this.style.transform='translateX(0)'; this.style.boxShadow='0 2px 4px rgba(100, 116, 139, 0.3)';">
            <i class="fas fa-arrow-left"></i> Volver a la lista
        </a>
    </div>

    <!-- Header del Apiario -->
    <div style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(255, 255, 255, 0.95) 100%);
                backdrop-filter: blur(10px);
                padding: 30px;
                margin-bottom: 30px;
                border-radius: 15px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                border: 1px solid rgba(148, 163, 184, 0.1);">

        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 20px;">
            <div style="flex: 1;">
                <h2 style="margin: 0 0 10px 0; color: #0f172a; font-weight: 700; font-size: 2rem;">
                    <i class="fas fa-warehouse" style="color: #2563eb; margin-right: 12px;"></i>
                    {{ $apiario->nombre_apiario }}
                </h2>
                <p style="margin: 0; color: #64748b; font-size: 1rem;">
                    <i class="far fa-calendar"></i> Registrado el {{ $apiario->created_at->format('d/m/Y H:i') }}
                </p>
            </div>
            <div>
                @if($apiario->activo)
                    <span style="padding: 10px 20px;
                                 background: linear-gradient(135deg, #10b981 0%, #059669 100%);
                                 color: white;
                                 border-radius: 25px;
                                 font-size: 0.9rem;
                                 font-weight: 600;
                                 display: inline-block;">
                        <i class="fas fa-check-circle"></i> Activo
                    </span>
                @else
                    <span style="padding: 10px 20px;
                                 background: linear-gradient(135deg, #64748b 0%, #475569 100%);
                                 color: white;
                                 border-radius: 25px;
                                 font-size: 0.9rem;
                                 font-weight: 600;
                                 display: inline-block;">
                        <i class="fas fa-times-circle"></i> Inactivo
                    </span>
                @endif
            </div>
        </div>

        <!-- Información básica en cards -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <div style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
                        padding: 20px;
                        border-radius: 12px;
                        text-align: center;">
                <div style="font-size: 2rem; color: #2563eb; margin-bottom: 8px;">
                    <i class="fas fa-cube"></i>
                </div>
                <div style="font-size: 2rem; font-weight: 700; color: #1e40af; margin-bottom: 5px;">
                    {{ $apiario->colmenas_count }}
                </div>
                <div style="color: #1e40af; font-weight: 600; font-size: 0.9rem;">
                    Colmenas
                </div>
            </div>

            <div style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
                        padding: 20px;
                        border-radius: 12px;
                        text-align: center;">
                <div style="font-size: 2rem; color: #d97706; margin-bottom: 8px;">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div style="font-size: 2rem; font-weight: 700; color: #92400e; margin-bottom: 5px;">
                    {{ $apiario->visitas_count }}
                </div>
                <div style="color: #92400e; font-weight: 600; font-size: 0.9rem;">
                    Visitas
                </div>
            </div>

            <div style="background: linear-gradient(135deg, #e9d5ff 0%, #d8b4fe 100%);
                        padding: 20px;
                        border-radius: 12px;
                        text-align: center;">
                <div style="font-size: 2rem; color: #7c3aed; margin-bottom: 8px;">
                    <i class="fas fa-tag"></i>
                </div>
                <div style="font-size: 1.2rem; font-weight: 700; color: #5b21b6; margin-bottom: 5px;">
                    {{ ucfirst($apiario->tipo_apiario) }}
                </div>
                <div style="color: #5b21b6; font-weight: 600; font-size: 0.9rem;">
                    Tipo
                </div>
            </div>

            @if($apiario->es_temporal)
            <div style="background: linear-gradient(135deg, #fecaca 0%, #fca5a5 100%);
                        padding: 20px;
                        border-radius: 12px;
                        text-align: center;">
                <div style="font-size: 2rem; color: #dc2626; margin-bottom: 8px;">
                    <i class="fas fa-clock"></i>
                </div>
                <div style="font-size: 1.2rem; font-weight: 700; color: #991b1b; margin-bottom: 5px;">
                    Temporal
                </div>
                <div style="color: #991b1b; font-weight: 600; font-size: 0.9rem;">
                    Estado
                </div>
            </div>
            @endif
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px;">
        <!-- Información del Propietario -->
        <div style="background: white;
                    border-radius: 15px;
                    padding: 25px;
                    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);">
            <h4 style="margin: 0 0 20px 0; color: #0f172a; font-weight: 700; font-size: 1.25rem; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">
                <i class="fas fa-user" style="color: #2563eb; margin-right: 8px;"></i>
                Información del Propietario
            </h4>

            <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px;">
                <div style="width: 60px; height: 60px; border-radius: 50%;
                            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
                            display: flex; align-items: center; justify-content: center;
                            color: white; font-weight: 700; font-size: 1.5rem;">
                    {{ substr($apiario->user->name, 0, 1) }}
                </div>
                <div>
                    <div style="font-weight: 700; color: #0f172a; font-size: 1.1rem; margin-bottom: 3px;">
                        {{ $apiario->user->name }}
                    </div>
                    <div style="color: #64748b; font-size: 0.9rem;">
                        <i class="fas fa-envelope"></i> {{ $apiario->user->email }}
                    </div>
                </div>
            </div>

            <div style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
                        padding: 15px;
                        border-radius: 10px;">
                <div style="display: grid; gap: 10px;">
                    <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #cbd5e1;">
                        <span style="color: #64748b; font-size: 0.9rem;">
                            <i class="fas fa-warehouse"></i> Apiarios totales:
                        </span>
                        <span style="font-weight: 700; color: #0f172a;">
                            {{ $apiario->user->apiarios->count() }}
                        </span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 8px 0;">
                        <span style="color: #64748b; font-size: 0.9rem;">
                            <i class="far fa-calendar"></i> Miembro desde:
                        </span>
                        <span style="font-weight: 700; color: #0f172a;">
                            {{ $apiario->user->created_at->format('d/m/Y') }}
                        </span>
                    </div>
                </div>
            </div>

            <a href="{{ route('admin.users.show', $apiario->user->id) }}"
               style="display: block; margin-top: 15px; text-align: center;
                      padding: 12px 20px;
                      background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
                      color: white;
                      text-decoration: none;
                      border-radius: 10px;
                      font-weight: 600;
                      transition: all 0.3s ease;
                      box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);"
               onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(59, 130, 246, 0.4)';"
               onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(59, 130, 246, 0.3)';">
                <i class="fas fa-user-circle"></i> Ver perfil completo
            </a>
        </div>

        <!-- Detalles del Apiario -->
        <div style="background: white;
                    border-radius: 15px;
                    padding: 25px;
                    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);">
            <h4 style="margin: 0 0 20px 0; color: #0f172a; font-weight: 700; font-size: 1.25rem; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">
                <i class="fas fa-info-circle" style="color: #2563eb; margin-right: 8px;"></i>
                Detalles del Apiario
            </h4>

            <div style="display: grid; gap: 15px;">
                <div style="padding: 12px 15px;
                            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
                            border-radius: 10px;
                            border-left: 4px solid #3b82f6;">
                    <div style="color: #64748b; font-size: 0.85rem; margin-bottom: 3px;">
                        <i class="fas fa-map-marker-alt"></i> Ubicación
                    </div>
                    <div style="font-weight: 600; color: #0f172a;">
                        @if($apiario->localizacion)
                            {{ $apiario->localizacion }}
                        @elseif($apiario->comuna)
                            {{ $apiario->comuna->nombre }}, {{ $apiario->comuna->region->nombre }}
                        @else
                            No especificada
                        @endif
                    </div>
                </div>

                @if($apiario->latitud && $apiario->longitud)
                <div style="padding: 12px 15px;
                            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
                            border-radius: 10px;
                            border-left: 4px solid #10b981;">
                    <div style="color: #64748b; font-size: 0.85rem; margin-bottom: 3px;">
                        <i class="fas fa-map-pin"></i> Coordenadas
                    </div>
                    <div style="font-weight: 600; color: #0f172a;">
                        Lat: {{ $apiario->latitud }}, Lng: {{ $apiario->longitud }}
                    </div>
                </div>
                @endif

                @if($apiario->temporada_principal)
                <div style="padding: 12px 15px;
                            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
                            border-radius: 10px;
                            border-left: 4px solid #f59e0b;">
                    <div style="color: #64748b; font-size: 0.85rem; margin-bottom: 3px;">
                        <i class="fas fa-sun"></i> Temporada Principal
                    </div>
                    <div style="font-weight: 600; color: #0f172a;">
                        {{ ucfirst($apiario->temporada_principal) }}
                    </div>
                </div>
                @endif

                @if($apiario->fecha_inicio_temporal && $apiario->fecha_fin_temporal)
                <div style="padding: 12px 15px;
                            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
                            border-radius: 10px;
                            border-left: 4px solid #f59e0b;">
                    <div style="color: #92400e; font-size: 0.85rem; margin-bottom: 3px;">
                        <i class="fas fa-calendar-alt"></i> Período Temporal
                    </div>
                    <div style="font-weight: 600; color: #78350f;">
                        Del {{ \Carbon\Carbon::parse($apiario->fecha_inicio_temporal)->format('d/m/Y') }}
                        al {{ \Carbon\Carbon::parse($apiario->fecha_fin_temporal)->format('d/m/Y') }}
                    </div>
                </div>
                @endif

                @if($apiario->descripcion)
                <div style="padding: 12px 15px;
                            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
                            border-radius: 10px;
                            border-left: 4px solid #8b5cf6;">
                    <div style="color: #64748b; font-size: 0.85rem; margin-bottom: 5px;">
                        <i class="fas fa-align-left"></i> Descripción
                    </div>
                    <div style="font-weight: 500; color: #475569; line-height: 1.5;">
                        {{ $apiario->descripcion }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Últimas Visitas -->
    @if($apiario->visitas->count() > 0)
    <div style="background: white;
                border-radius: 15px;
                padding: 25px;
                margin-top: 25px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);">
        <h4 style="margin: 0 0 20px 0; color: #0f172a; font-weight: 700; font-size: 1.25rem; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">
            <i class="fas fa-calendar-check" style="color: #2563eb; margin-right: 8px;"></i>
            Últimas Visitas (Últimas 10)
        </h4>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
                               border-bottom: 2px solid #cbd5e1;">
                        <th style="padding: 12px 15px; text-align: left; font-weight: 700; color: #0f172a; font-size: 0.85rem;">
                            Fecha
                        </th>
                        <th style="padding: 12px 15px; text-align: left; font-weight: 700; color: #0f172a; font-size: 0.85rem;">
                            Tipo de Visita
                        </th>
                        <th style="padding: 12px 15px; text-align: left; font-weight: 700; color: #0f172a; font-size: 0.85rem;">
                            Observaciones
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($apiario->visitas as $visita)
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 12px 15px; color: #475569;">
                            <i class="far fa-calendar"></i> {{ \Carbon\Carbon::parse($visita->fecha_visita)->format('d/m/Y') }}
                        </td>
                        <td style="padding: 12px 15px;">
                            <span style="padding: 4px 10px;
                                         background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
                                         color: #1e40af;
                                         border-radius: 15px;
                                         font-size: 0.8rem;
                                         font-weight: 600;">
                                {{ $visita->tipo_visita }}
                            </span>
                        </td>
                        <td style="padding: 12px 15px; color: #475569; font-size: 0.9rem;">
                            {{ Str::limit($visita->observaciones ?? 'Sin observaciones', 80) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
