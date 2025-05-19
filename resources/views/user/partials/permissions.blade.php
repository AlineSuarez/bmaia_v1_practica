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
                <div id="permission-status-message" class="alert alert-info mt-3" style="display: none;"></div>
                </div>
            </form>
            <div class="alert alert-warning mt-3">
                Nota: Algunos permisos como cámara, micrófono y ubicación solo funcionan en sitios seguros (HTTPS o localhost).
            </div>
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
    const messageContainer = document.getElementById('permission-status-message');
    const fields   = ['notifications','camera_access','microphone','location','bluetooth'];

    // Función genérica para verificar y solicitar un permiso del navegador
    async function requestBrowserPermission(permissionType) {
        let status = 'denied';
        switch (permissionType) {
            case 'notifications':
                status = await Notification.requestPermission();
                return status === 'granted';
            case 'camera_access':
                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    console.warn('API de MediaDevices no soportada.');
                    return false;
                }
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({ video: true });
                    stream.getTracks().forEach(track => track.stop());
                    return true;
                } catch (error) {
                    console.error(`Error al solicitar cámara:`, error);
                    return false;
                }

            case 'microphone':
                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    console.warn('API de MediaDevices no soportada.');
                    return false;
                }
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                    stream.getTracks().forEach(track => track.stop());
                    return true;
                } catch (error) {
                    console.error(`Error al solicitar micrófono:`, error);
                    return false;
                }

            case 'location':
                if (!navigator.geolocation) {
                    console.warn('API de Geolocalización no soportada.');
                    return false;
                }
                return new Promise((resolve) => {
                    navigator.geolocation.getCurrentPosition(
                        () => resolve(true),
                        () => resolve(false)
                    );
                });

            case 'bluetooth':
                if (!navigator.bluetooth) {
                    console.warn('API de Bluetooth no soportada.');
                    return false;
                }
                try {
                    const device = await navigator.bluetooth.requestDevice({
                        acceptAllDevices: true,
                        optionalServices: []
                    });
                    console.log('Dispositivo Bluetooth seleccionado:', device);
                    return !!device;
                } catch (error) {
                    console.error('Error al solicitar dispositivo Bluetooth:', error);
                    return false;
                }

            default:
                return false;
        }
    }


    // Función para manejar el cambio en un checkbox de permiso
    async function handlePermissionChange(field, isChecked) {
        let permissionActuallyGranted = isChecked;
        if (isChecked) {
            const granted = await requestBrowserPermission(field);
            permissionActuallyGranted = granted;
        }
        form.querySelector(`#allow_${field}`).checked = permissionActuallyGranted;
    }

    // 1) Cargar estado inicial
    async function loadPermissions() {
        try {
            const { data } = await axios.get(URL_GET);
            for (const field of fields) {
                const checkbox = form.querySelector(`#allow_${field}`);
                if (checkbox) {
                    checkbox.checked = !!data[field]; // Establecer según la preferencia guardada
                }
            }
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
            messageContainer.style.display = 'block';
            messageContainer.innerHTML = 'Permisos actualizados correctamente.';
        } catch (err) {
            console.error('Error al guardar permisos:', err.response || err);
            messageContainer.style.display = 'block';
            messageContainer.innerHTML = 'No se pudo guardar. Revisa la consola.';
        } finally {
            saveBtn.disabled = false;
        }
    }

    // 3) Restablecer a OFF
    async function resetPermissions() {
        resetBtn.disabled = true;
        try {
            const { data } = await axios.post(URL_RESET);
            messageContainer.style.display = 'block';
            messageContainer.innerHTML = 'Permisos restablecidos a OFF.';
            await loadPermissions();
        } catch (err) {
            console.error('Error al restablecer permisos:', err.response || err);
            messageContainer.style.display = 'block';
            messageContainer.innerHTML = 'No se pudo restablecer. Revisa la consola.';
        } finally {
            resetBtn.disabled = false;
        }
    }

    // Asociar eventos a los cambios de los checkboxes
    fields.forEach(field => {
        const checkbox = form.querySelector(`#allow_${field}`);
        if (checkbox) {
            checkbox.addEventListener('change', function() {
                handlePermissionChange(field, this.checked);
            });
        }
    });

    // Asociar eventos
    saveBtn .addEventListener('click', savePermissions);
    resetBtn.addEventListener('click', resetPermissions);

    // Arrancamos
    loadPermissions();
});
</script>
@endpush