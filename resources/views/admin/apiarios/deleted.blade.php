@extends('layouts.admin')

@section('title', 'Apiarios Eliminados - Papelera')

@section('content')
<div style="padding: 20px;">
    <!-- Header -->
    <div style="background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(220, 38, 38, 0.05) 100%);
                backdrop-filter: blur(10px);
                padding: 25px 30px;
                margin-bottom: 30px;
                border-radius: 15px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                border: 2px solid rgba(239, 68, 68, 0.2);">

        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 style="margin: 0; color: #0f172a; font-weight: 700; font-size: 1.75rem;">
                    <i class="fas fa-trash-restore" style="color: #ef4444; margin-right: 10px;"></i>
                    Apiarios Eliminados
                </h2>
                <p style="margin: 5px 0 0 0; color: #64748b; font-size: 0.95rem;">
                    Papelera de reciclaje - Los apiarios se eliminan permanentemente después de 16 días
                </p>
            </div>
            <div style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
                        color: white;
                        padding: 12px 20px;
                        border-radius: 10px;
                        text-align: center;">
                <div style="font-size: 2rem; font-weight: 700; line-height: 1;">{{ count($deletedApiarios) }}</div>
                <div style="font-size: 0.75rem; text-transform: uppercase; opacity: 0.9; margin-top: 3px;">Eliminados</div>
            </div>
        </div>

        <!-- Alerta Informativa -->
        <div style="background: rgba(239, 68, 68, 0.1);
                    border: 1px solid rgba(239, 68, 68, 0.3);
                    border-radius: 10px;
                    padding: 15px 20px;
                    margin-top: 20px;
                    display: flex;
                    align-items: center;
                    gap: 15px;">
            <i class="fas fa-info-circle" style="color: #ef4444; font-size: 1.5rem;"></i>
            <div>
                <strong style="color: #ef4444;">Atención:</strong>
                <span style="color: #64748b;">
                    Los apiarios permanecen en la papelera por 16 días. Después de este período, se eliminan automáticamente del sistema junto con todas sus colmenas.
                </span>
            </div>
        </div>
    </div>

    <!-- Botón Volver -->
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.apiarios.index') }}" style="text-decoration: none;">
            <button style="background: #64748b;
                          color: white;
                          border: none;
                          padding: 10px 20px;
                          border-radius: 8px;
                          font-weight: 600;
                          cursor: pointer;
                          display: inline-flex;
                          align-items: center;
                          gap: 8px;">
                <i class="fas fa-arrow-left"></i>
                Volver a Apiarios
            </button>
        </a>
    </div>

    @if(session('success'))
        <div style="background: #10b981;
                    color: white;
                    padding: 15px 20px;
                    border-radius: 10px;
                    margin-bottom: 20px;
                    display: flex;
                    align-items: center;
                    gap: 12px;">
            <i class="fas fa-check-circle" style="font-size: 1.25rem;"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div style="background: #ef4444;
                    color: white;
                    padding: 15px 20px;
                    border-radius: 10px;
                    margin-bottom: 20px;
                    display: flex;
                    align-items: center;
                    gap: 12px;">
            <i class="fas fa-exclamation-circle" style="font-size: 1.25rem;"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if(count($deletedApiarios) > 0)
        <!-- Lista de Apiarios Eliminados -->
        <div style="display: grid; gap: 20px;">
            @foreach($deletedApiarios as $apiario)
            <div style="background: white;
                        border-radius: 15px;
                        padding: 25px;
                        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                        border-left: 5px solid {{ $apiario['remaining_days'] <= 3 ? '#ef4444' : ($apiario['remaining_days'] <= 7 ? '#f59e0b' : '#64748b') }};">

                <div style="display: grid; grid-template-columns: 1fr auto; gap: 30px; align-items: start;">
                    <!-- Información del Apiario -->
                    <div>
                        <h3 style="margin: 0 0 10px 0; color: #0f172a; font-weight: 700; font-size: 1.25rem;">
                            <i class="fas fa-hive" style="color: #64748b; margin-right: 8px;"></i>
                            {{ $apiario['nombre'] }}
                        </h3>

                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 15px;">
                            <div>
                                <div style="color: #64748b; font-size: 0.75rem; font-weight: 600; margin-bottom: 5px;">UBICACIÓN</div>
                                <div style="color: #0f172a; font-weight: 600;">{{ $apiario['ubicacion'] ?? 'No especificada' }}</div>
                            </div>

                            <div>
                                <div style="color: #64748b; font-size: 0.75rem; font-weight: 600; margin-bottom: 5px;">COLMENAS</div>
                                <div style="color: #0f172a; font-weight: 600;">
                                    <i class="fas fa-clone" style="color: #3b82f6;"></i>
                                    {{ $apiario['colmenas_count'] }} colmenas
                                </div>
                            </div>

                            <div>
                                <div style="color: #64748b; font-size: 0.75rem; font-weight: 600; margin-bottom: 5px;">ELIMINADO</div>
                                <div style="color: #0f172a;">
                                    {{ \Carbon\Carbon::parse($apiario['deleted_at'])->format('d/m/Y H:i') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contador Regresivo y Acciones -->
                    <div style="text-align: center;">
                        <!-- Contador Regresivo -->
                        <div style="background: linear-gradient(135deg, {{ $apiario['remaining_days'] <= 3 ? '#fee2e2' : ($apiario['remaining_days'] <= 7 ? '#fef3c7' : '#f1f5f9') }} 0%, {{ $apiario['remaining_days'] <= 3 ? '#fecaca' : ($apiario['remaining_days'] <= 7 ? '#fde68a' : '#e2e8f0') }} 100%);
                                    padding: 20px;
                                    border-radius: 12px;
                                    margin-bottom: 20px;
                                    min-width: 250px;">
                            <div style="color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; margin-bottom: 10px;">
                                Tiempo Restante
                            </div>

                            <!-- Contador -->
                            <div class="countdown" data-expires="{{ $apiario['expires_at'] }}">
                                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px;">
                                    <div>
                                        <div class="countdown-value" data-unit="days" style="font-size: 2rem; font-weight: 700; color: {{ $apiario['remaining_days'] <= 3 ? '#ef4444' : ($apiario['remaining_days'] <= 7 ? '#f59e0b' : '#0f172a') }};">
                                            {{ $apiario['remaining_days'] }}
                                        </div>
                                        <div style="font-size: 0.7rem; color: #64748b; font-weight: 600;">DÍAS</div>
                                    </div>
                                    <div>
                                        <div class="countdown-value" data-unit="hours" style="font-size: 2rem; font-weight: 700; color: {{ $apiario['remaining_days'] <= 3 ? '#ef4444' : ($apiario['remaining_days'] <= 7 ? '#f59e0b' : '#0f172a') }};">
                                            {{ $apiario['remaining_hours'] }}
                                        </div>
                                        <div style="font-size: 0.7rem; color: #64748b; font-weight: 600;">HORAS</div>
                                    </div>
                                    <div>
                                        <div class="countdown-value" data-unit="minutes" style="font-size: 2rem; font-weight: 700; color: {{ $apiario['remaining_days'] <= 3 ? '#ef4444' : ($apiario['remaining_days'] <= 7 ? '#f59e0b' : '#0f172a') }};">
                                            {{ $apiario['remaining_minutes'] }}
                                        </div>
                                        <div style="font-size: 0.7rem; color: #64748b; font-weight: 600;">MIN</div>
                                    </div>
                                    <div>
                                        <div class="countdown-value" data-unit="seconds" style="font-size: 2rem; font-weight: 700; color: {{ $apiario['remaining_days'] <= 3 ? '#ef4444' : ($apiario['remaining_days'] <= 7 ? '#f59e0b' : '#0f172a') }};">
                                            {{ $apiario['remaining_seconds'] }}
                                        </div>
                                        <div style="font-size: 0.7rem; color: #64748b; font-weight: 600;">SEG</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div style="display: flex; flex-direction: column; gap: 10px;">
                            <form action="{{ route('admin.apiarios.restore', $apiario['id']) }}" method="POST" style="margin: 0;">
                                @csrf
                                <button type="submit" style="width: 100%;
                                                              background: linear-gradient(135deg, #10b981 0%, #059669 100%);
                                                              color: white;
                                                              border: none;
                                                              padding: 12px 20px;
                                                              border-radius: 8px;
                                                              font-weight: 600;
                                                              cursor: pointer;
                                                              display: inline-flex;
                                                              align-items: center;
                                                              justify-content: center;
                                                              gap: 8px;">
                                    <i class="fas fa-undo"></i>
                                    Restaurar
                                </button>
                            </form>

                            <form action="{{ route('admin.apiarios.permanent-delete', $apiario['id']) }}" method="POST" style="margin: 0;"
                                  onsubmit="return confirm('¿Estás seguro de eliminar permanentemente este apiario? Esta acción no se puede deshacer.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="width: 100%;
                                                              background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
                                                              color: white;
                                                              border: none;
                                                              padding: 12px 20px;
                                                              border-radius: 8px;
                                                              font-weight: 600;
                                                              cursor: pointer;
                                                              display: inline-flex;
                                                              align-items: center;
                                                              justify-content: center;
                                                              gap: 8px;">
                                    <i class="fas fa-trash"></i>
                                    Eliminar Permanentemente
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <!-- Estado Vacío -->
        <div style="background: white;
                    border-radius: 15px;
                    padding: 60px 20px;
                    text-align: center;
                    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
            <i class="fas fa-trash" style="font-size: 4rem; color: #cbd5e1; margin-bottom: 20px;"></i>
            <h3 style="margin: 0 0 10px 0; color: #0f172a; font-weight: 700;">No hay apiarios eliminados</h3>
            <p style="margin: 0; color: #64748b;">La papelera está vacía</p>
        </div>
    @endif
</div>

<script>
// Actualizar contadores cada segundo
function updateCountdowns() {
    document.querySelectorAll('.countdown').forEach(countdown => {
        const expiresAt = new Date(countdown.dataset.expires);
        const now = new Date();
        const diff = expiresAt - now;

        if (diff <= 0) {
            location.reload();
            return;
        }

        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
        const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((diff % (1000 * 60)) / 1000);

        countdown.querySelector('[data-unit="days"]').textContent = days;
        countdown.querySelector('[data-unit="hours"]').textContent = hours;
        countdown.querySelector('[data-unit="minutes"]').textContent = minutes;
        countdown.querySelector('[data-unit="seconds"]').textContent = seconds;
    });
}

// Actualizar cada segundo
setInterval(updateCountdowns, 1000);
updateCountdowns();
</script>
@endsection
