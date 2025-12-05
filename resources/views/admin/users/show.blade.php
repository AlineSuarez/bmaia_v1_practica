@extends('layouts.admin')

@section('title', 'Perfil de Usuario')

@section('content')
<div style="padding: 20px;">
    <!-- Botón de regreso -->
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.users.index') }}"
           style="background: linear-gradient(135deg, #64748b 0%, #475569 100%);
                  color: white;
                  padding: 10px 20px;
                  border-radius: 10px;
                  text-decoration: none;
                  font-weight: 600;
                  display: inline-flex;
                  align-items: center;
                  gap: 8px;
                  box-shadow: 0 2px 4px rgba(100, 116, 139, 0.3);
                  transition: all 0.2s;"
           onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(100, 116, 139, 0.4)'"
           onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(100, 116, 139, 0.3)'">
            <i class="fas fa-arrow-left"></i>
            Volver a la lista
        </a>
    </div>

    <!-- Header del perfil -->
    <div style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(255, 255, 255, 0.95) 100%);
                backdrop-filter: blur(10px);
                padding: 20px;
                margin-bottom: 30px;
                border-radius: 15px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                border: 1px solid rgba(148, 163, 184, 0.1);">
        <div class="user-header-container">
            <div class="user-avatar">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div class="user-info">
                <h2 class="user-name">
                    {{ $user->name }}
                </h2>
                <p class="user-email">
                    <i class="fas fa-envelope"></i>
                    <span>{{ $user->email }}</span>
                </p>
                <div class="user-badges">
                    @if(isset($user->role))
                    <span class="badge-role">
                        {{ $user->role }}
                    </span>
                    @endif
                    @php
                        $planLabels = [
                            'afc' => 'AFC',
                            'me' => 'ME',
                            'ge' => 'GE',
                            'drone' => 'Drone',
                        ];
                        $hasPlan = $user->plan && trim($user->plan) !== '';
                        $planLabel = $hasPlan ? ($planLabels[$user->plan] ?? strtoupper($user->plan)) : null;

                        // Verificar si el plan está vencido
                        $planVencido = false;
                        if ($hasPlan && $user->fecha_vencimiento) {
                            $ahora = \Carbon\Carbon::now();
                            $vencimiento = \Carbon\Carbon::parse($user->fecha_vencimiento);
                            $planVencido = $ahora->isAfter($vencimiento);
                        }
                    @endphp
                    @if($hasPlan)
                        <span class="badge-plan-info {{ $planVencido ? 'plan-vencido' : '' }}">
                            <i class="fas {{ $planVencido ? 'fa-exclamation-triangle' : 'fa-crown' }}"></i>
                            <span>Plan {{ $planLabel }}{{ $planVencido ? ' Vencido' : '' }}</span>
                        </span>
                        @if($planVencido)
                            <button type="button"
                                    class="btn-reasignar-plan"
                                    data-user-id="{{ $user->id }}"
                                    data-user-name="{{ $user->name }}"
                                    data-user-email="{{ $user->email }}"
                                    style="background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
                                           color: white;
                                           padding: 6px 14px;
                                           border-radius: 8px;
                                           border: none;
                                           font-weight: 600;
                                           font-size: 0.75rem;
                                           display: inline-flex;
                                           align-items: center;
                                           gap: 6px;
                                           cursor: pointer;
                                           box-shadow: 0 2px 4px rgba(37, 99, 235, 0.3);
                                           transition: all 0.2s;
                                           text-transform: uppercase;
                                           letter-spacing: 0.05em;
                                           white-space: nowrap;"
                                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(37, 99, 235, 0.4)'"
                                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(37, 99, 235, 0.3)'">
                                <i class="fas fa-redo"></i>
                                <span>Reasignar Plan</span>
                            </button>
                        @endif
                    @else
                        <button type="button"
                                class="btn-liberar-plan"
                                data-user-id="{{ $user->id }}"
                                data-user-name="{{ $user->name }}"
                                data-user-email="{{ $user->email }}">
                            <i class="fas fa-crown"></i>
                            <span>Liberar Plan</span>
                        </button>
                    @endif
                </div>
            </div>
            <div class="user-date">
                <div class="date-label">Miembro desde</div>
                <div class="date-value">
                    {{ $user->created_at->format('d/m/Y') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <!-- Total de Apiarios -->
        <div style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
                    color: white;
                    padding: 25px;
                    border-radius: 15px;
                    box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.3);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                <div>
                    <p style="margin: 0 0 5px 0; font-size: 0.875rem; opacity: 0.9;">Total Apiarios</p>
                    <h3 style="margin: 0; font-size: 2.25rem; font-weight: 700;">{{ $user->apiarios_count }}</h3>
                </div>
                <div style="width: 50px;
                            height: 50px;
                            background: rgba(255, 255, 255, 0.2);
                            border-radius: 12px;
                            display: flex;
                            align-items: center;
                            justify-content: center;">
                    <i class="fas fa-warehouse" style="font-size: 1.5rem;"></i>
                </div>
            </div>
        </div>

        <!-- Total de Colmenas -->
        <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);
                    color: white;
                    padding: 25px;
                    border-radius: 15px;
                    box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                <div>
                    <p style="margin: 0 0 5px 0; font-size: 0.875rem; opacity: 0.9;">Total Colmenas</p>
                    <h3 style="margin: 0; font-size: 2.25rem; font-weight: 700;">{{ $user->colmenas_count ?? 0 }}</h3>
                </div>
                <div style="width: 50px;
                            height: 50px;
                            background: rgba(255, 255, 255, 0.2);
                            border-radius: 12px;
                            display: flex;
                            align-items: center;
                            justify-content: center;">
                    <i class="fas fa-boxes" style="font-size: 1.5rem;"></i>
                </div>
            </div>
        </div>

        <!-- Última Actividad -->
        <div style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
                    color: white;
                    padding: 25px;
                    border-radius: 15px;
                    box-shadow: 0 4px 6px -1px rgba(245, 158, 11, 0.3);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                <div>
                    <p style="margin: 0 0 5px 0; font-size: 0.875rem; opacity: 0.9;">Última Actualización</p>
                    <h3 style="margin: 0; font-size: 1.125rem; font-weight: 700;">{{ $user->updated_at->diffForHumans() }}</h3>
                </div>
                <div style="width: 50px;
                            height: 50px;
                            background: rgba(255, 255, 255, 0.2);
                            border-radius: 12px;
                            display: flex;
                            align-items: center;
                            justify-content: center;">
                    <i class="fas fa-clock" style="font-size: 1.5rem;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Apiarios -->
    <div style="background: white;
                border-radius: 15px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                overflow: hidden;">
        <div style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
                    padding: 20px 25px;
                    border-bottom: 2px solid #cbd5e1;">
            <h3 style="margin: 0; color: #0f172a; font-weight: 700; font-size: 1.25rem;">
                <i class="fas fa-warehouse" style="color: #2563eb; margin-right: 10px;"></i>
                Apiarios del Usuario
            </h3>
        </div>

        @if($user->apiarios->count() > 0)
        <div style="overflow-x: auto;">
            <table class="table table-hover" style="margin: 0;">
                <thead style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                    <tr>
                        <th style="padding: 15px 20px; font-weight: 700; color: #0f172a; font-size: 0.875rem;">Nombre</th>
                        <th style="padding: 15px 20px; font-weight: 700; color: #0f172a; font-size: 0.875rem;">Tipo</th>
                        <th style="padding: 15px 20px; font-weight: 700; color: #0f172a; font-size: 0.875rem;">Ubicación</th>
                        <th style="padding: 15px 20px; font-weight: 700; color: #0f172a; font-size: 0.875rem;">Colmenas</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($user->apiarios as $apiario)
                    <tr style="border-bottom: 1px solid #f1f5f9; transition: background-color 0.2s;"
                        onmouseover="this.style.backgroundColor='#f8fafc'"
                        onmouseout="this.style.backgroundColor='white'">
                        <td style="padding: 15px 20px;">
                            <div style="font-weight: 600; color: #0f172a;">{{ $apiario->nombre }}</div>
                        </td>
                        <td style="padding: 15px 20px;">
                            <span style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
                                        color: white;
                                        padding: 4px 10px;
                                        border-radius: 6px;
                                        font-weight: 600;
                                        font-size: 0.75rem;
                                        text-transform: capitalize;">
                                {{ $apiario->tipo_apiario ?? 'N/A' }}
                            </span>
                        </td>
                        <td style="padding: 15px 20px; color: #64748b; font-size: 0.875rem;">
                            <i class="fas fa-map-marker-alt" style="color: #94a3b8; margin-right: 6px;"></i>
                            {{ $apiario->comuna->nombre ?? 'N/A' }}, {{ $apiario->comuna->region->nombre ?? 'N/A' }}
                        </td>
                        <td style="padding: 15px 20px;">
                            <span style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
                                        color: white;
                                        padding: 4px 10px;
                                        border-radius: 6px;
                                        font-weight: 600;
                                        font-size: 0.75rem;">
                                {{ $apiario->colmenas->count() }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div style="padding: 40px; text-align: center; color: #64748b;">
            <i class="fas fa-warehouse" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 15px; display: block;"></i>
            <p style="margin: 0; font-size: 1.125rem; font-weight: 600;">Este usuario no tiene apiarios registrados</p>
        </div>
        @endif
    </div>

    <!-- Modal Liberar Plan -->
    <div class="modal fade" id="liberarPlanModal" tabindex="-1" aria-labelledby="liberarPlanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px; border: none; overflow: hidden; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);">
                <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); padding: 25px; color: white;">
                    <h5 class="modal-title" id="liberarPlanModalLabel" style="font-weight: 700; font-size: 1.5rem; margin: 0; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-crown"></i>
                        Liberar Plan
                    </h5>
                    <p style="margin: 8px 0 0 0; font-size: 0.9rem; opacity: 0.95;">Asignar plan a usuario</p>
                </div>
                <div class="modal-body" style="padding: 30px;">
                    <div style="margin-bottom: 25px;">
                        <label style="display: block; margin-bottom: 10px; font-weight: 600; color: #0f172a; font-size: 0.95rem;">
                            <i class="fas fa-layer-group" style="color: #10b981; margin-right: 8px;"></i>
                            Tipo de Plan
                        </label>
                        <select id="planType" class="form-control" style="padding: 12px 15px; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 1rem; transition: all 0.2s; outline: none;">
                            <option value="">Seleccione un plan</option>
                            <option value="afc">AFC</option>
                            <option value="me">ME</option>
                            <option value="ge">GE</option>
                            <option value="drone">Drone</option>
                        </select>
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 10px; font-weight: 600; color: #0f172a; font-size: 0.95rem;">
                            <i class="fas fa-clock" style="color: #10b981; margin-right: 8px;"></i>
                            Duración
                        </label>
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px;">
                            <!-- Años -->
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-size: 0.85rem; color: #64748b; font-weight: 500;">Años</label>
                                <input type="number" id="planYears" min="0" value="0" class="form-control duration-input" style="padding: 10px 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem;">
                            </div>
                            <!-- Meses -->
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-size: 0.85rem; color: #64748b; font-weight: 500;">Meses</label>
                                <input type="number" id="planMonths" min="0" value="1" class="form-control duration-input" style="padding: 10px 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem;">
                            </div>
                            <!-- Semanas -->
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-size: 0.85rem; color: #64748b; font-weight: 500;">Semanas</label>
                                <input type="number" id="planWeeks" min="0" value="0" class="form-control duration-input" style="padding: 10px 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem;">
                            </div>
                            <!-- Días -->
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-size: 0.85rem; color: #64748b; font-weight: 500;">Días</label>
                                <input type="number" id="planDays" min="0" value="0" class="form-control duration-input" style="padding: 10px 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="padding: 20px 30px; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="background: linear-gradient(135deg, #64748b 0%, #475569 100%); color: white; padding: 12px 24px; border: none; border-radius: 10px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 2px 4px rgba(100, 116, 139, 0.3);">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </button>
                    <button type="button" id="btnConfirmarPlan" class="btn btn-success" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 12px 24px; border: none; border-radius: 10px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 2px 4px rgba(16, 185, 129, 0.3);">
                        <i class="fas fa-check"></i>
                        Confirmar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Liberar Plan Modal
        const liberarPlanButton = document.querySelector('.btn-liberar-plan');
        const liberarPlanModal = new bootstrap.Modal(document.getElementById('liberarPlanModal'));
        let currentUserId = null;
        let currentUserName = null;
        let currentUserEmail = null;

        if (liberarPlanButton) {
            liberarPlanButton.addEventListener('click', function() {
                currentUserId = this.dataset.userId;
                currentUserName = this.dataset.userName;
                currentUserEmail = this.dataset.userEmail;

                // Reset form
                document.getElementById('planType').value = '';
                document.getElementById('planYears').value = '0';
                document.getElementById('planMonths').value = '1';
                document.getElementById('planWeeks').value = '0';
                document.getElementById('planDays').value = '0';

                liberarPlanModal.show();
            });
        }

        // Reasignar Plan Modal (same as liberar plan)
        const reasignarPlanButton = document.querySelector('.btn-reasignar-plan');

        if (reasignarPlanButton) {
            reasignarPlanButton.addEventListener('click', function() {
                currentUserId = this.dataset.userId;
                currentUserName = this.dataset.userName;
                currentUserEmail = this.dataset.userEmail;

                // Reset form
                document.getElementById('planType').value = '';
                document.getElementById('planYears').value = '0';
                document.getElementById('planMonths').value = '1';
                document.getElementById('planWeeks').value = '0';
                document.getElementById('planDays').value = '0';

                liberarPlanModal.show();
            });
        }

        // Confirmar Plan
        document.getElementById('btnConfirmarPlan').addEventListener('click', function() {
            const planType = document.getElementById('planType').value;
            const years = parseInt(document.getElementById('planYears').value) || 0;
            const months = parseInt(document.getElementById('planMonths').value) || 0;
            const weeks = parseInt(document.getElementById('planWeeks').value) || 0;
            const days = parseInt(document.getElementById('planDays').value) || 0;

            if (!planType) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Por favor selecciona un tipo de plan',
                    confirmButtonColor: '#ef4444',
                    customClass: {
                        popup: 'swal-custom-popup'
                    }
                });
                return;
            }

            // Validar que al menos una unidad de tiempo sea mayor a 0
            if (years === 0 && months === 0 && weeks === 0 && days === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Por favor ingresa al menos una duración (días, semanas, meses o años)',
                    confirmButtonColor: '#ef4444',
                    customClass: {
                        popup: 'swal-custom-popup'
                    }
                });
                return;
            }

            const planLabels = {
                'afc': 'AFC',
                'me': 'ME',
                'ge': 'GE',
                'drone': 'Drone'
            };

            const planLabel = planLabels[planType];

            // Construir texto de duración
            const durationParts = [];
            if (years > 0) durationParts.push(`${years} ${years === 1 ? 'año' : 'años'}`);
            if (months > 0) durationParts.push(`${months} ${months === 1 ? 'mes' : 'meses'}`);
            if (weeks > 0) durationParts.push(`${weeks} ${weeks === 1 ? 'semana' : 'semanas'}`);
            if (days > 0) durationParts.push(`${days} ${days === 1 ? 'día' : 'días'}`);
            const durationText = durationParts.join(', ');

            // Cerrar modal
            liberarPlanModal.hide();

            // Mostrar confirmación
            Swal.fire({
                title: '¿Confirmar asignación de plan?',
                html: `
                    <div style="text-align: left; padding: 20px;">
                        <p style="margin: 0 0 15px 0; color: #64748b; font-size: 0.95rem;">
                            Estás a punto de asignar el siguiente plan:
                        </p>
                        <div style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
                                    padding: 20px;
                                    border-radius: 10px;
                                    border-left: 4px solid #10b981;">
                            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 15px;">
                                <div style="width: 45px;
                                            height: 45px;
                                            border-radius: 50%;
                                            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
                                            display: flex;
                                            align-items: center;
                                            justify-content: center;
                                            color: white;
                                            font-weight: 700;
                                            font-size: 1.1rem;">
                                    ${currentUserName.charAt(0).toUpperCase()}
                                </div>
                                <div>
                                    <div style="font-weight: 600; color: #0f172a; font-size: 1rem;">${currentUserName}</div>
                                    <div style="color: #64748b; font-size: 0.875rem;">${currentUserEmail}</div>
                                </div>
                            </div>
                            <div style="border-top: 1px solid #cbd5e1; padding-top: 15px; margin-top: 15px;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                    <span style="color: #64748b; font-weight: 500;">
                                        <i class="fas fa-crown" style="color: #10b981; margin-right: 8px;"></i>Plan:
                                    </span>
                                    <span style="font-weight: 700; color: #0f172a; font-size: 1.1rem;">${planLabel}</span>
                                </div>
                                <div style="display: flex; justify-content: space-between;">
                                    <span style="color: #64748b; font-weight: 500;">
                                        <i class="fas fa-clock" style="color: #10b981; margin-right: 8px;"></i>Duración:
                                    </span>
                                    <span style="font-weight: 700; color: #0f172a; font-size: 1.1rem; text-align: right;">${durationText}</span>
                                </div>
                            </div>
                        </div>
                        <p style="margin: 15px 0 0 0; color: #10b981; font-weight: 600; font-size: 0.9rem;">
                            <i class="fas fa-info-circle" style="margin-right: 6px;"></i>
                            El plan se activará inmediatamente
                        </p>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="fas fa-check"></i> Sí, asignar plan',
                cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
                reverseButtons: true,
                customClass: {
                    popup: 'swal-custom-popup',
                    title: 'swal-custom-title',
                    confirmButton: 'swal-custom-confirm',
                    cancelButton: 'swal-custom-cancel'
                },
                width: '600px',
                padding: '2rem',
                backdrop: 'rgba(15, 23, 42, 0.7)',
                showClass: {
                    popup: 'animate__animated animate__fadeInDown animate__faster'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp animate__faster'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Mostrar loading
                    Swal.fire({
                        title: 'Asignando plan...',
                        html: 'Por favor espera un momento',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Enviar petición
                    fetch(`/admin/users/${currentUserId}/assign-plan`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            plan: planType,
                            years: years,
                            months: months,
                            weeks: weeks,
                            days: days
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        Swal.close();
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Plan asignado!',
                                html: `
                                    <div style="padding: 20px; text-align: center;">
                                        <p style="color: #10b981; font-size: 1.1rem; font-weight: 600; margin-bottom: 15px;">
                                            El plan ha sido asignado exitosamente
                                        </p>
                                        <div style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
                                                    padding: 15px;
                                                    border-radius: 10px;
                                                    display: inline-block;">
                                            <div style="color: #64748b; font-size: 0.9rem; margin-bottom: 5px;">Plan asignado:</div>
                                            <div style="font-weight: 700; color: #0f172a; font-size: 1.2rem;">${planLabel}</div>
                                            <div style="color: #64748b; font-size: 0.85rem; margin-top: 5px;">Vencimiento: ${data.fecha_vencimiento}</div>
                                        </div>
                                    </div>
                                `,
                                confirmButtonColor: '#10b981',
                                customClass: {
                                    popup: 'swal-custom-popup'
                                }
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message || 'No se pudo asignar el plan',
                                confirmButtonColor: '#ef4444',
                                customClass: {
                                    popup: 'swal-custom-popup'
                                }
                            });
                        }
                    })
                    .catch(error => {
                        Swal.close();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un error al asignar el plan',
                            confirmButtonColor: '#ef4444',
                            customClass: {
                                popup: 'swal-custom-popup'
                            }
                        });
                    });
                }
            });
        });
    });
</script>

<style>
    .swal-custom-popup {
        border-radius: 15px !important;
        font-family: 'Outfit', sans-serif !important;
    }

    .swal-custom-title {
        color: #0f172a !important;
        font-weight: 700 !important;
        font-size: 1.5rem !important;
    }

    .swal-custom-confirm {
        border-radius: 8px !important;
        padding: 12px 24px !important;
        font-weight: 600 !important;
        font-size: 0.95rem !important;
        box-shadow: 0 4px 6px rgba(16, 185, 129, 0.3) !important;
        transition: all 0.2s !important;
    }

    .swal-custom-confirm:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 10px rgba(16, 185, 129, 0.4) !important;
    }

    .swal-custom-cancel {
        border-radius: 8px !important;
        padding: 12px 24px !important;
        font-weight: 600 !important;
        font-size: 0.95rem !important;
        box-shadow: 0 4px 6px rgba(100, 116, 139, 0.3) !important;
        transition: all 0.2s !important;
    }

    .swal-custom-cancel:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 10px rgba(100, 116, 139, 0.4) !important;
    }

    /* Estilos responsive para el header del usuario */
    .user-header-container {
        display: flex;
        align-items: center;
        gap: 25px;
        flex-wrap: wrap;
    }

    .user-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 2rem;
        box-shadow: 0 4px 6px rgba(37, 99, 235, 0.4);
        flex-shrink: 0;
    }

    .user-info {
        flex: 1;
        min-width: 200px;
    }

    .user-name {
        margin: 0 0 8px 0;
        color: #0f172a;
        font-weight: 700;
        font-size: 1.875rem;
        word-break: break-word;
    }

    .user-email {
        margin: 0 0 8px 0;
        color: #64748b;
        font-size: 1rem;
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .user-email span {
        word-break: break-all;
    }

    .user-badges {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .badge-role {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        padding: 4px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.75rem;
        display: inline-block;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .btn-liberar-plan {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        padding: 6px 14px;
        border-radius: 8px;
        border: none;
        font-weight: 600;
        font-size: 0.75rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        box-shadow: 0 2px 4px rgba(16, 185, 129, 0.3);
        transition: all 0.2s;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        white-space: nowrap;
    }

    .btn-liberar-plan:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(16, 185, 129, 0.4);
    }

    .badge-plan-info {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        color: white;
        padding: 6px 14px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.75rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 2px 4px rgba(139, 92, 246, 0.3);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        white-space: nowrap;
    }

    .badge-plan-info.plan-vencido {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
        box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3) !important;
    }

    .user-date {
        text-align: center;
        flex-shrink: 0;
    }

    .date-label {
        font-size: 0.875rem;
        color: #64748b;
        margin-bottom: 5px;
    }

    .date-value {
        font-size: 1.125rem;
        font-weight: 700;
        color: #0f172a;
    }

    /* Responsive para pantallas pequeñas */
    @media (max-width: 768px) {
        .user-header-container {
            flex-direction: column;
            align-items: flex-start;
            gap: 20px;
        }

        .user-avatar {
            width: 60px;
            height: 60px;
            font-size: 1.5rem;
        }

        .user-name {
            font-size: 1.5rem;
        }

        .user-email {
            font-size: 0.875rem;
        }

        .user-badges {
            width: 100%;
        }

        .btn-liberar-plan span {
            display: none;
        }

        .btn-liberar-plan {
            padding: 8px 12px;
        }

        .badge-plan-info span {
            display: none;
        }

        .badge-plan-info {
            padding: 8px 12px;
        }

        .user-date {
            width: 100%;
            text-align: left;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
        }
    }

    @media (max-width: 480px) {
        .user-name {
            font-size: 1.25rem;
        }

        .badge-role,
        .btn-liberar-plan,
        .badge-plan-info {
            font-size: 0.7rem;
            padding: 4px 10px;
        }
    }
</style>
@endpush
