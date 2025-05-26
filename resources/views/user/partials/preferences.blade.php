<section class="tab-pane fade" id="preferences" role="tabpanel" aria-labelledby="preferences-tab">
    <div class="card settings-card mb-4">
        <div class="card-header">
        <h3>Preferencias de Usuario</h3>
        <p class="text-muted">
            Personaliza tu experiencia con BeeMaiA según tus preferencias de uso e interacción.
        </p>
        </div>
        <div class="card-body">
        {{-- Rutas AJAX --}}
        <input type="hidden" id="preferences-get-url"   value="{{ url('/user/settings/preferences') }}">
        <input type="hidden" id="preferences-post-url"  value="{{ url('/user/settings/preferences') }}">
        <input type="hidden" id="preferences-reset-url" value="{{ url('/user/settings/preferences/reset') }}">

        <form id="preferences-form" class="preferences-form">
            {{-- Idioma y País --}}
            <div class="row">
            <div class="col-md-6">
                <div class="form-group mb-4">
                <label for="language"><strong>Idioma y País</strong></label>
                <select class="form-select" id="language" name="language">
                    <option value="es_CL">Español (Chile)</option>
                    <option value="en_US">English (United States)</option>
                    <option value="pt_BR">Português (Brasil)</option>
                </select>
                <small class="form-text text-muted">
                    Establece tu idioma preferido para la interfaz de la aplicación y comunicación de BeeMaiA.
                </small>
                </div>
            </div>
            {{-- Formato de Fecha --}}
            <div class="col-md-6">
                <div class="form-group mb-4">
                <label for="date_format"><strong>Formato de Fecha</strong></label>
                <select class="form-select" id="date_format" name="date_format">
                    <option value="DD/MM/YYYY">DD/MM/AAAA</option>
                    <option value="MM/DD/YYYY">MM/DD/AAAA</option>
                    <option value="YYYY-MM-DD">AAAA-MM-DD</option>
                </select>
                <small class="form-text text-muted">
                    Define cómo se mostrarán las fechas en la aplicación.
                </small>
                </div>
            </div>
            </div>

            {{-- Aspecto (Tema) --}}
            <div class="form-group mb-4">
            <label><strong>Aspecto</strong></label>
            <div class="theme-options">
                <div class="row">
                <div class="col-md-4">
                    <div class="theme-option">
                    <input class="form-check-input" type="radio" name="theme" id="themeLight" value="light">
                    <label class="form-check-label" for="themeLight">
                        <div class="theme-preview light-theme">
                        <div class="theme-preview-header"></div>
                        <div class="theme-preview-body"></div>
                        </div>
                        <span>Claro</span>
                    </label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="theme-option">
                    <input class="form-check-input" type="radio" name="theme" id="themeDark" value="dark">
                    <label class="form-check-label" for="themeDark">
                        <div class="theme-preview dark-theme">
                        <div class="theme-preview-header"></div>
                        <div class="theme-preview-body"></div>
                        </div>
                        <span>Oscuro</span>
                    </label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="theme-option">
                    <input class="form-check-input" type="radio" name="theme" id="themeAuto" value="auto">
                    <label class="form-check-label" for="themeAuto">
                        <div class="theme-preview auto-theme">
                        <div class="theme-preview-header"></div>
                        <div class="theme-preview-body"></div>
                        </div>
                        <span>Automático</span>
                    </label>
                    </div>
                </div>
                </div>
            </div>
            <small class="form-text text-muted">
                Selecciona el tema visual de la aplicación según tus preferencias de visualización.
            </small>
            </div>

            {{-- Voz BeeMaiA y Vista Predeterminada --}}
            <div class="row">
            <div class="col-md-6">
                <div class="form-group mb-4">
                <label for="voice_preference"><strong>Preferencia de Voz BeeMaiA</strong></label>
                <select class="form-select" id="voice_preference" name="voice_preference">
                    <option value="female_1">Voz Femenina 1</option>
                    <option value="female_2">Voz Femenina 2</option>
                    <option value="male_1">Voz Masculina 1</option>
                    <option value="male_2">Voz Masculina 2</option>
                </select>
                <small class="form-text text-muted">
                    Elige entre diferentes voces predefinidas para la interacción de BeeMaiA.
                </small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-4">
                <label for="default_view"><strong>Vista Predeterminada</strong></label>
                <select class="form-select" id="default_view" name="default_view">
                    <option value="dashboard">Panel de Control</option>
                    <option value="apiaries">Apiarios</option>
                    <option value="calendar">Calendario</option>
                    <option value="reports">Informes</option>
                </select>
                <small class="form-text text-muted">
                    Selecciona la pantalla que se mostrará al iniciar la aplicación.
                </small>
                </div>
            </div>
            </div>

            {{-- Voice Match --}}
            <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" id="voice_match" name="voice_match">
            <label class="form-check-label" for="voice_match">
                <strong>BeeMaiA Siempre Activa (Voice Match)</strong>
            </label>
            <div class="text-muted ms-4">
                Integra la funcionalidad de "Voice Match" de Google para que BeeMaiA se active con "Ok MaiA".
                <div class="voice-match-info mt-2">
                <p><i class="bi bi-mic-fill text-primary"></i> Di "Ok MaiA" para activar sin tocar.</p>
                <p><i class="bi bi-shield-check text-primary"></i> Tu voz se procesa localmente.</p>
                </div>
            </div>
            </div>

            {{-- Notificaciones de Calendario --}}
            <div class="form-group mb-4">
            <label><strong>Notificaciones de Calendario</strong></label>
            <div class="row">
                <div class="col-md-6">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="calendar_email" name="calendar_email">
                    <label class="form-check-label" for="calendar_email">
                    Recibir recordatorios por email
                    </label>
                </div>
                </div>
                <div class="col-md-6">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="calendar_push" name="calendar_push">
                    <label class="form-check-label" for="calendar_push">
                    Recibir notificaciones push
                    </label>
                </div>
                </div>
            </div>
            <div class="form-group mt-2">
                <label for="reminder_time">Tiempo de anticipación para recordatorios</label>
                <select class="form-select" id="reminder_time" name="reminder_time">
                <option value="15">15 minutos antes</option>
                <option value="30">30 minutos antes</option>
                <option value="60">1 hora antes</option>
                <option value="120">2 horas antes</option>
                <option value="1440">1 día antes</option>
                </select>
            </div>
            </div>

            {{-- Botones --}}
            <div class="form-actions mt-4">
            <button type="button" class="btn btn-primary" id="savePreferencesBtn">
                Guardar Preferencias
            </button>
            <button type="reset" class="btn btn-outline-secondary ms-2" id="resetPreferencesBtn">
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
    // Toastr breve (1s)
    toastr.options = {
        timeOut: 1000,
        positionClass: 'toast-top-right',
        showMethod: 'fadeIn',
        hideMethod: 'fadeOut'
    };

    // Leer URLs
    const URL_GET   = document.getElementById('preferences-get-url').value;
    const URL_POST  = document.getElementById('preferences-post-url').value;
    const URL_RESET = document.getElementById('preferences-reset-url').value;

    // CSRF para Axios
    axios.defaults.headers.common['X-CSRF-TOKEN'] =
        document.querySelector('meta[name="csrf-token"]').content;

    const form     = document.getElementById('preferences-form');
    const saveBtn  = document.getElementById('savePreferencesBtn');
    const resetBtn = document.getElementById('resetPreferencesBtn');
    const fields   = [
        'language','date_format','theme','voice_preference',
        'default_view','voice_match','calendar_email',
        'calendar_push','reminder_time'
    ];

    // 1) Cargar preferencias
    async function loadPreferences() {
        try {
            const { data } = await axios.get(URL_GET);
            fields.forEach(f => {
                const elList = form.querySelectorAll(`[name="${f}"]`);
                if (!elList.length) return;

                if (elList[0].type === 'checkbox') {
                    elList[0].checked = !!data[f];
                } else if (elList[0].type === 'radio') {
                    elList.forEach(r => {
                        r.checked = (r.value === data[f]);
                    });
                } else {
                    elList[0].value = data[f];
                }
            });
        } catch (e) {
            console.error(e);
            toastr.error('Error cargando preferencias');
            }
    }

    // 2) Guardar cambios
    async function savePreferences() {
        const payload = {};
        fields.forEach(f => {
            const elList = form.querySelectorAll(`[name="${f}"]`);
            if (!elList.length) return;

            if (elList[0].type === 'checkbox') {
                payload[f] = elList[0].checked;
            } else if (elList[0].type === 'radio') {
                const checkedRadio = [...elList].find(r => r.checked);
                if (checkedRadio) payload[f] = checkedRadio.value;
            } else {
                payload[f] = elList[0].value;
            }
        });

        saveBtn.disabled = true;
        try {
            await axios.post(URL_POST, payload);
            toastr.success('Preferencias guardadas');
        } catch (e) {
            console.error(e);
            toastr.error('No se pudieron guardar');
        } finally {
            saveBtn.disabled = false;
        }
    }

    // 3) Restablecer
    async function resetPreferences() {
        if (!confirm('¿Restablecer todas las preferencias a los valores predeterminados?')) {
        return;
        }
        resetBtn.disabled = true;
        try {
        await axios.post(URL_RESET);
        toastr.success('Preferencias restablecidas');
        await loadPreferences();
        } catch (e) {
        console.error(e);
        toastr.error('No se pudieron restablecer');
        } finally {
        resetBtn.disabled = false;
        }
    }

    // Eventos
    saveBtn .addEventListener('click', savePreferences);
    resetBtn.addEventListener('click', resetPreferences);

    // Inicializar carga
    loadPreferences();
});
</script>
@endpush