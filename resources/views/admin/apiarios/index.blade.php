@extends('layouts.admin')

@section('title', 'Gestión de Apiarios')

@section('content')
<div style="padding: 20px;">
    <!-- Header Unificado con Buscador y Filtros -->
    <div style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(255, 255, 255, 0.95) 100%);
                backdrop-filter: blur(10px);
                padding: 25px 30px;
                margin-bottom: 30px;
                border-radius: 15px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                border: 1px solid rgba(148, 163, 184, 0.1);">

        <!-- Título y Total -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <div>
                <h2 style="margin: 0; color: #0f172a; font-weight: 700; font-size: 1.75rem;">
                    <i class="fas fa-warehouse" style="color: #2563eb; margin-right: 10px;"></i>
                    Gestión de Apiarios
                </h2>
                <p style="margin: 5px 0 0 0; color: #64748b; font-size: 0.95rem;">
                    Administra todos los apiarios del sistema
                </p>
            </div>
            <div style="display: flex; gap: 12px; align-items: center;">
                <!-- Botón Temporales -->
                <a href="{{ route('admin.apiarios.index', ['tipo2' => 'temporal'] + request()->except('tipo2', 'page')) }}"
                   class="apiario-stat-btn temporales"
                   style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
                          color: white;
                          padding: 12px 20px;
                          border-radius: 10px;
                          font-weight: 600;
                          box-shadow: 0 4px 6px -1px rgba(139, 92, 246, 0.3);
                          display: inline-flex;
                          align-items: center;
                          gap: 8px;
                          text-decoration: none;
                          {{ request('tipo2') == 'temporal' ? 'border: 2px solid white;' : '' }}">
                    <i class="fas fa-clock"></i>
                    <span>Temporales: {{ $totalTemporales }}</span>
                    @if(request('tipo2') == 'temporal')
                        <i class="fas fa-check-circle" style="margin-left: 5px;"></i>
                    @endif
                </a>

                <!-- Botón Base -->
                <a href="{{ route('admin.apiarios.index', ['tipo2' => 'base'] + request()->except('tipo2', 'page')) }}"
                   class="apiario-stat-btn base"
                   style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
                          color: white;
                          padding: 12px 20px;
                          border-radius: 10px;
                          font-weight: 600;
                          box-shadow: 0 4px 6px -1px rgba(6, 182, 212, 0.3);
                          display: inline-flex;
                          align-items: center;
                          gap: 8px;
                          text-decoration: none;
                          {{ request('tipo2') == 'base' ? 'border: 2px solid white;' : '' }}">
                    <i class="fas fa-home"></i>
                    <span>Base: {{ $totalBase }}</span>
                    @if(request('tipo2') == 'base')
                        <i class="fas fa-check-circle" style="margin-left: 5px;"></i>
                    @endif
                </a>

                <!-- Botón Archivados -->
                <a href="{{ route('admin.apiarios.index', ['tipo2' => 'archivados'] + request()->except('tipo2', 'page')) }}"
                   class="apiario-stat-btn archivados"
                   style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
                          color: white;
                          padding: 12px 20px;
                          border-radius: 10px;
                          font-weight: 600;
                          text-decoration: none;
                          box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.3);
                          display: inline-flex;
                          align-items: center;
                          gap: 8px;
                          {{ request('tipo2') == 'archivados' ? 'border: 2px solid white;' : '' }}">
                    <i class="fas fa-archive"></i>
                    <span>Archivados: {{ $totalArchivados }}</span>
                    @if(request('tipo2') == 'archivados')
                        <i class="fas fa-check-circle" style="margin-left: 5px;"></i>
                    @endif
                </a>
            </div>
        </div>

        <!-- Alertas -->
        @if(session('success'))
        <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);
                    color: white;
                    padding: 15px 20px;
                    margin-bottom: 20px;
                    border-radius: 12px;
                    box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3);
                    display: flex;
                    align-items: center;
                    gap: 12px;">
            <i class="fas fa-check-circle" style="font-size: 1.25rem;"></i>
            <span style="font-weight: 500;">{{ session('success') }}</span>
        </div>
        @endif

        @if(session('error'))
        <div style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
                    color: white;
                    padding: 15px 20px;
                    margin-bottom: 20px;
                    border-radius: 12px;
                    box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.3);
                    display: flex;
                    align-items: center;
                    gap: 12px;">
            <i class="fas fa-exclamation-circle" style="font-size: 1.25rem;"></i>
            <span style="font-weight: 500;">{{ session('error') }}</span>
        </div>
        @endif

        <!-- Buscador y Filtros -->
        <form method="GET" action="{{ route('admin.apiarios.index') }}" style="margin: 0;">
            <div style="display: flex; gap: 12px; align-items: center; margin-bottom: 15px;">
                <div style="flex: 1; position: relative;">
                    <i class="fas fa-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 1rem;"></i>
                    <input type="text"
                           id="searchInput"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Buscar por nombre, ubicación o propietario..."
                           style="width: 100%;
                                  padding: 12px 15px 12px 45px;
                                  border: 2px solid #e2e8f0;
                                  border-radius: 10px;
                                  font-size: 0.95rem;
                                  transition: all 0.3s ease;
                                  outline: none;"
                           onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)';"
                           onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none';">
                </div>

                <!-- Filtro por Tipo -->
                <select name="tipo"
                        style="padding: 12px 15px;
                               border: 2px solid #e2e8f0;
                               border-radius: 10px;
                               font-size: 0.95rem;
                               background: white;
                               color: #475569;
                               cursor: pointer;
                               transition: all 0.3s ease;
                               outline: none;"
                        onchange="this.form.submit()"
                        onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)';"
                        onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none';">
                    <option value="">Todos los tipos</option>
                    <option value="fijo" {{ request('tipo') == 'fijo' ? 'selected' : '' }}>Fijos</option>
                    <option value="trashumante" {{ request('tipo') == 'trashumante' ? 'selected' : '' }}>Trashumantes</option>
                </select>

                <button type="submit"
                        style="padding: 12px 24px;
                               background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
                               color: white;
                               border: none;
                               border-radius: 10px;
                               font-weight: 600;
                               cursor: pointer;
                               transition: all 0.3s ease;
                               box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);"
                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 12px -1px rgba(59, 130, 246, 0.4)';"
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px -1px rgba(59, 130, 246, 0.3)';">
                    <i class="fas fa-search"></i> Buscar
                </button>

                @if(request('search') || request('tipo') || request('estado') || request('tipo2'))
                <a href="{{ route('admin.apiarios.index') }}"
                   style="padding: 12px 24px;
                          background: linear-gradient(135deg, #64748b 0%, #475569 100%);
                          color: white;
                          border: none;
                          border-radius: 10px;
                          font-weight: 600;
                          text-decoration: none;
                          display: inline-flex;
                          align-items: center;
                          gap: 8px;
                          transition: all 0.3s ease;
                          box-shadow: 0 4px 6px -1px rgba(100, 116, 139, 0.3);"
                   onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 12px -1px rgba(100, 116, 139, 0.4)';"
                   onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px -1px rgba(100, 116, 139, 0.3)';">
                    <i class="fas fa-times"></i> Limpiar
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabla de Apiarios -->
    @if($apiarios->count() > 0)
    <div style="background: white;
                border-radius: 15px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                overflow: hidden;">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
                               border-bottom: 2px solid #cbd5e1;">
                        <th style="padding: 18px 20px; text-align: left; font-weight: 700; color: #0f172a; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">
                            <i class="fas fa-warehouse" style="color: #2563eb; margin-right: 8px;"></i>Nombre
                        </th>
                        <th style="padding: 18px 20px; text-align: left; font-weight: 700; color: #0f172a; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">
                            <i class="fas fa-user" style="color: #2563eb; margin-right: 8px;"></i>Propietario
                        </th>
                        <th style="padding: 18px 20px; text-align: left; font-weight: 700; color: #0f172a; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">
                            <i class="fas fa-map-marker-alt" style="color: #2563eb; margin-right: 8px;"></i>Ubicación
                        </th>
                        <th style="padding: 18px 20px; text-align: center; font-weight: 700; color: #0f172a; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">
                            <i class="fas fa-tag" style="color: #2563eb; margin-right: 8px;"></i>Tipo
                        </th>
                        <th style="padding: 18px 20px; text-align: center; font-weight: 700; color: #0f172a; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">
                            <i class="fas fa-tags" style="color: #2563eb; margin-right: 8px;"></i>Tipo 2
                        </th>
                        <th style="padding: 18px 20px; text-align: center; font-weight: 700; color: #0f172a; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">
                            <i class="fas fa-cube" style="color: #2563eb; margin-right: 8px;"></i>Colmenas
                        </th>
                        <th style="padding: 18px 20px; text-align: center; font-weight: 700; color: #0f172a; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">
                            <i class="fas fa-cog" style="color: #2563eb; margin-right: 8px;"></i>Acciones
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($apiarios as $apiario)
                    <tr style="border-bottom: 1px solid #e2e8f0; transition: all 0.3s ease;"
                        onmouseover="this.style.backgroundColor='#f8fafc';"
                        onmouseout="this.style.backgroundColor='white';">
                        <td style="padding: 18px 20px;">
                            <div style="font-weight: 600; color: #0f172a; margin-bottom: 4px;">
                                {{ $apiario->nombre }}
                            </div>
                            <div style="font-size: 0.85rem; color: #64748b;">
                                <i class="far fa-calendar"></i> {{ $apiario->created_at->format('d/m/Y') }}
                            </div>
                        </td>
                        <td style="padding: 18px 20px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 35px; height: 35px; border-radius: 50%;
                                            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
                                            display: flex; align-items: center; justify-content: center;
                                            color: white; font-weight: 700; font-size: 0.9rem;">
                                    {{ substr($apiario->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <div style="font-weight: 500; color: #0f172a;">{{ $apiario->user->name }}</div>
                                    <div style="font-size: 0.8rem; color: #64748b;">{{ $apiario->user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 18px 20px;">
                            <div style="color: #475569; font-size: 0.9rem;">
                                <i class="fas fa-map-marker-alt" style="color: #ef4444; margin-right: 5px;"></i>
                                @if($apiario->localizacion)
                                    {{ $apiario->localizacion }}
                                @elseif($apiario->comuna)
                                    {{ $apiario->comuna->nombre }}, {{ $apiario->comuna->region->nombre }}
                                @else
                                    No especificada
                                @endif
                            </div>
                        </td>
                        <td style="padding: 18px 20px; text-align: center;">
                            @if($apiario->tipo_apiario === 'fijo')
                                <span style="padding: 6px 12px;
                                             background: linear-gradient(135deg, #10b981 0%, #059669 100%);
                                             color: white;
                                             border-radius: 20px;
                                             font-size: 0.8rem;
                                             font-weight: 600;
                                             display: inline-block;">
                                    <i class="fas fa-map-pin"></i> Fijo
                                </span>
                            @else
                                <span style="padding: 6px 12px;
                                             background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
                                             color: white;
                                             border-radius: 20px;
                                             font-size: 0.8rem;
                                             font-weight: 600;
                                             display: inline-block;">
                                    <i class="fas fa-truck-moving"></i> Trashumante
                                </span>
                            @endif
                        </td>
                        <td style="padding: 18px 20px; text-align: center;">
                            @if($apiario->es_temporal)
                                <span style="padding: 6px 12px;
                                             background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
                                             color: white;
                                             border-radius: 20px;
                                             font-size: 0.8rem;
                                             font-weight: 600;
                                             display: inline-block;">
                                    <i class="fas fa-clock"></i> Temporal
                                </span>
                            @else
                                <span style="padding: 6px 12px;
                                             background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
                                             color: white;
                                             border-radius: 20px;
                                             font-size: 0.8rem;
                                             font-weight: 600;
                                             display: inline-block;">
                                    <i class="fas fa-home"></i> Base
                                </span>
                            @endif
                        </td>
                        <td style="padding: 18px 20px; text-align: center;">
                            <div style="display: inline-flex; align-items: center; gap: 6px;
                                        padding: 8px 16px;
                                        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
                                        border-radius: 10px;">
                                <i class="fas fa-cube" style="color: #2563eb;"></i>
                                <span style="font-weight: 700; color: #1e40af; font-size: 1.1rem;">
                                    {{ $apiario->colmenas_count }}
                                </span>
                            </div>
                        </td>
                        <td style="padding: 18px 20px; text-align: center;">
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <a href="{{ route('admin.apiarios.show', $apiario->id) }}"
                                   style="padding: 8px 12px;
                                          background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
                                          color: white;
                                          border-radius: 8px;
                                          text-decoration: none;
                                          font-size: 0.85rem;
                                          font-weight: 600;
                                          transition: all 0.3s ease;
                                          box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);"
                                   onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(59, 130, 246, 0.4)';"
                                   onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(59, 130, 246, 0.3)';">
                                    <i class="fas fa-eye"></i> Ver
                                </a>
                                <button onclick="confirmarEliminacion('{{ $apiario->id }}', '{{ $apiario->nombre_apiario }}', '{{ $apiario->user->name }}')"
                                        style="padding: 8px 12px;
                                               background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
                                               color: white;
                                               border: none;
                                               border-radius: 8px;
                                               font-size: 0.85rem;
                                               font-weight: 600;
                                               cursor: pointer;
                                               transition: all 0.3s ease;
                                               box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3);"
                                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(239, 68, 68, 0.4)';"
                                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(239, 68, 68, 0.3)';">
                                    <i class="fas fa-trash"></i> Eliminar
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginación -->
    @if($apiarios->hasPages())
    <div style="margin-top: 25px; display: flex; justify-content: center;">
        {{ $apiarios->links() }}
    </div>
    @endif

    @else
    <!-- Mensaje cuando no hay resultados -->
    <div style="background: white;
                border-radius: 15px;
                padding: 60px 40px;
                text-align: center;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);">
        <div style="font-size: 4rem; color: #cbd5e1; margin-bottom: 20px;">
            <i class="fas fa-warehouse"></i>
        </div>
        <h3 style="color: #475569; font-weight: 600; margin-bottom: 10px; font-size: 1.5rem;">
            No se encontraron apiarios
        </h3>
        <p style="color: #94a3b8; font-size: 1rem;">
            @if(request('search') || request('tipo') || request('estado'))
                No hay apiarios que coincidan con los criterios de búsqueda.
                <br>
                <a href="{{ route('admin.apiarios.index') }}"
                   style="color: #3b82f6; text-decoration: none; font-weight: 600; margin-top: 15px; display: inline-block;">
                    <i class="fas fa-arrow-left"></i> Ver todos los apiarios
                </a>
            @else
                Aún no hay apiarios registrados en el sistema.
            @endif
        </p>
    </div>
    @endif
</div>

<!-- Formularios ocultos para eliminación -->
@foreach($apiarios as $apiario)
<form id="delete-form-{{ $apiario->id }}" action="{{ route('admin.apiarios.destroy', $apiario->id) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endforeach

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function confirmarEliminacion(apiarioId, apiarioNombre, propietario) {
    Swal.fire({
        title: '¿Eliminar apiario?',
        html: `
            <div style="text-align: left; padding: 20px;">
                <p style="margin-bottom: 15px;">Estás a punto de eliminar el siguiente apiario:</p>
                <div style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
                            padding: 20px;
                            border-radius: 12px;
                            border-left: 4px solid #ef4444;
                            margin-bottom: 15px;">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                        <div style="width: 45px; height: 45px; border-radius: 10px;
                                    background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
                                    display: flex; align-items: center; justify-content: center;
                                    color: white; font-size: 1.2rem;">
                            <i class="fas fa-warehouse"></i>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 700; font-size: 1.1rem; color: #0f172a; margin-bottom: 3px;">
                                ${apiarioNombre}
                            </div>
                            <div style="color: #64748b; font-size: 0.9rem;">
                                <i class="fas fa-user"></i> Propietario: ${propietario}
                            </div>
                        </div>
                    </div>
                </div>
                <div style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
                            padding: 15px;
                            border-radius: 10px;
                            border-left: 4px solid #f59e0b;">
                    <div style="display: flex; gap: 10px; align-items: start;">
                        <i class="fas fa-exclamation-triangle" style="color: #d97706; font-size: 1.2rem; margin-top: 2px;"></i>
                        <div style="color: #92400e; font-size: 0.9rem;">
                            <strong>Advertencia:</strong> Esta acción no se puede deshacer. Si el apiario tiene colmenas asociadas, no podrá ser eliminado.
                        </div>
                    </div>
                </div>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<i class="fas fa-trash-alt"></i> Sí, eliminar',
        cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
        width: '600px',
        padding: '20px',
        backdrop: 'rgba(15, 23, 42, 0.7)',
        customClass: {
            popup: 'swal-custom-popup',
            confirmButton: 'swal-custom-confirm',
            cancelButton: 'swal-custom-cancel'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + apiarioId).submit();
        }
    });
}
</script>

<style>
    .swal-custom-popup {
        border-radius: 20px !important;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
    }

    .swal-custom-confirm, .swal-custom-cancel {
        padding: 12px 30px !important;
        border-radius: 10px !important;
        font-weight: 600 !important;
        font-size: 1rem !important;
        transition: all 0.3s ease !important;
    }

    .swal-custom-confirm:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px -4px rgba(239, 68, 68, 0.5) !important;
    }

    .swal-custom-cancel:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px -4px rgba(100, 116, 139, 0.5) !important;
    }

    /* Efectos hover para los botones de conteo */
    .apiario-stat-btn {
        transition: all 0.3s ease;
    }

    .apiario-stat-btn:hover {
        transform: translateY(-3px);
    }

    .apiario-stat-btn.temporales:hover {
        box-shadow: 0 8px 20px -4px rgba(139, 92, 246, 0.5) !important;
    }

    .apiario-stat-btn.base:hover {
        box-shadow: 0 8px 20px -4px rgba(6, 182, 212, 0.5) !important;
    }

    .apiario-stat-btn.archivados:hover {
        box-shadow: 0 8px 20px -4px rgba(239, 68, 68, 0.5) !important;
    }
</style>

<script>
    // Búsqueda reactiva con debounce
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const form = searchInput.closest('form');
        let debounceTimer;

        searchInput.addEventListener('input', function() {
            // Limpiar el temporizador anterior
            clearTimeout(debounceTimer);

            // Establecer un nuevo temporizador para enviar el formulario después de 500ms
            debounceTimer = setTimeout(function() {
                form.submit();
            }, 500);
        });
    });
</script>

@endsection
