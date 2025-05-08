<section class="tab-pane fade" id="permissions" role="tabpanel" aria-labelledby="permissions-tab">
    <div class="card settings-card mb-4">
        <div class="card-header">
        <h3>Permisos de la Aplicación</h3>
        <p class="text-muted">
            Configura los permisos que deseas otorgar a la aplicación para mejorar tu experiencia y funcionalidad.
        </p>
        </div>
        <div class="card-body">
        {{-- Inyectamos aquí las URLs de tus rutas web --}}
        <input type="hidden" id="permissions-get-url"   value="{{ url('/user/settings/permissions') }}">
        <input type="hidden" id="permissions-post-url"  value="{{ url('/user/settings/permissions') }}">
        <input type="hidden" id="permissions-reset-url" value="{{ url('/user/settings/permissions/reset') }}">

        <form id="permissions-form" class="permissions-form">
            @php
            $fields = [
                'notifications' => 'Permitir Notificaciones',
                'camera_access' => 'Permitir acceso a imágenes y cámara',
                'microphone'    => 'Activar Micrófono',
                'location'      => 'Activar Ubicación o GPS',
                'bluetooth'     => 'Conectar tus Dispositivos Bluetooth',
            ];
            $descriptions = [
                'notifications' => 'Personaliza qué tipo de notificaciones deseas recibir (alertas, recordatorios de tareas, avisos de BeeMaiA, etc.).',
                'camera_access' => 'Permite acceder a la galería y cámara para adjuntar fotos y videos relevantes a tus colmenas.',
                'microphone'    => 'Autoriza el uso del micrófono para interactuar con BeeMaiA mediante voz.',
                'location'      => 'Permite obtener la ubicación GPS de tu dispositivo para geolocalizar apiarios o datos climáticos.',
                'bluetooth'     => 'Mantiene la conexión Bluetooth con dispositivos como altavoces, audífonos o manos libres.',
            ];
            @endphp

            @foreach($fields as $field => $label)
            <div class="permission-item mb-4">
                <div class="form-check form-switch">
                <input class="form-check-input"
                        type="checkbox"
                        id="allow_{{ $field }}"
                        name="{{ $field }}">
                <label class="form-check-label" for="allow_{{ $field }}">
                    <strong>{{ $label }}</strong>
                </label>
                </div>
                <div class="permission-details">
                <p class="text-muted">{{ $descriptions[$field] }}</p>
                </div>
            </div>
            @endforeach

            <div class="form-actions mt-4">
            <button type="button" class="btn btn-primary" id="savePermissionsBtn">
                Guardar Permisos
            </button>
            <button type="button" class="btn btn-outline-secondary ms-2" id="resetPermissionsBtn">
                Restablecer
            </button>
            </div>
        </form>
        </div>
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Leemos las URLs inyectadas
    const URL_GET    = document.getElementById('permissions-get-url').value;
    const URL_POST   = document.getElementById('permissions-post-url').value;
    const URL_RESET  = document.getElementById('permissions-reset-url').value;

    // Asegúrate de que Axios esté cargado antes de esto
    axios.defaults.headers.common['X-CSRF-TOKEN'] =
        document.querySelector('meta[name="csrf-token"]').content;

    const form     = document.getElementById('permissions-form');
    const saveBtn  = document.getElementById('savePermissionsBtn');
    const resetBtn = document.getElementById('resetPermissionsBtn');
    const fields   = ['notifications','camera_access','microphone','location','bluetooth'];

    // 1) Cargar estado inicial
    async function loadPermissions() {
        try {
        const { data } = await axios.get(URL_GET);
        fields.forEach(f => {
            const cb = form.querySelector(`#allow_${f}`);
            if (cb) cb.checked = !!data[f];
        });
        } catch (err) {
        console.error('Error al GET permissions:', err);
        alert('No se pudo cargar los permisos. Revisa la consola.');
        }
    }

    // 2) Guardar cambios
    async function savePermissions() {
        const payload = {};
        fields.forEach(f => {
        payload[f] = form.querySelector(`#allow_${f}`).checked;
        });

        saveBtn.disabled = true;
        try {
        const { data } = await axios.post(URL_POST, payload);
        alert(data.message);
        } catch (err) {
        console.error('Error al POST permissions:', err.response || err);
        alert('No se pudo guardar. Revisa la consola.');
        } finally {
        saveBtn.disabled = false;
        }
    }

    // 3) Restablecer a OFF
    async function resetPermissions() {
        resetBtn.disabled = true;
        try {
        const { data } = await axios.post(URL_RESET);
        alert(data.message);
        await loadPermissions();
        } catch (err) {
        console.error('Error al RESET permissions:', err.response || err);
        alert('No se pudo restablecer. Revisa la consola.');
        } finally {
        resetBtn.disabled = false;
        }
    }

    // Asociar eventos
    saveBtn .addEventListener('click', savePermissions);
    resetBtn.addEventListener('click', resetPermissions);

    // Arrancamos
    loadPermissions();
});
</script>
@endpush