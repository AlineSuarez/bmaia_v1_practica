<section class="tab-pane fade" id="preferences" role="tabpanel" aria-labelledby="preferences-tab">
    <div class="card settings-card mb-4">
        <div class="card-header">
            <h3>Preferencias de Usuario</h3>
            <p class="text-muted">Personaliza tu experiencia con BeeMaiA según tus preferencias de uso e
                interacción.</p>
        </div>
        <div class="card-body">
            <form id="preferences-form" class="preferences-form">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-4">
                            <label for="language"><strong>Idioma y País</strong></label>
                            <select class="form-select" id="language" name="language">
                                <option value="es_CL" {{ ($user->language ?? '') == 'es_CL' ? 'selected' : '' }}>
                                    Español (Chile)</option>
                                <option value="en_US" {{ ($user->language ?? '') == 'en_US' ? 'selected' : '' }}>
                                    English (United States)</option>
                                <option value="pt_BR" {{ ($user->language ?? '') == 'pt_BR' ? 'selected' : '' }}>
                                    Português (Brasil)</option>
                            </select>
                            <small class="form-text text-muted">Establece tu idioma preferido para la interfaz
                                de la aplicación y comunicación de BeeMaiA.</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-4">
                            <label for="date_format"><strong>Formato de Fecha</strong></label>
                            <select class="form-select" id="date_format" name="date_format">
                                <option value="dd/mm/yyyy" {{ ($user->date_format ?? '') == 'dd/mm/yyyy' ? 'selected' : '' }}>DD/MM/AAAA</option>
                                <option value="mm/dd/yyyy" {{ ($user->date_format ?? '') == 'mm/dd/yyyy' ? 'selected' : '' }}>MM/DD/AAAA</option>
                                <option value="yyyy-mm-dd" {{ ($user->date_format ?? '') == 'yyyy-mm-dd' ? 'selected' : '' }}>AAAA-MM-DD</option>
                            </select>
                            <small class="form-text text-muted">Define cómo se mostrarán las fechas en la
                                aplicación.</small>
                        </div>
                    </div>
                </div>

                <div class="form-group mb-4">
                    <label><strong>Aspecto</strong></label>
                    <div class="theme-options">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="theme-option">
                                    <input class="form-check-input" type="radio" name="theme" id="themeLight"
                                        value="light" {{ ($user->theme ?? '') == 'light' ? 'checked' : '' }}>
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
                                    <input class="form-check-input" type="radio" name="theme" id="themeDark"
                                        value="dark" {{ ($user->theme ?? '') == 'dark' ? 'checked' : '' }}>
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
                                    <input class="form-check-input" type="radio" name="theme" id="themeAuto"
                                        value="auto" {{ ($user->theme ?? '') == 'auto' ? 'checked' : '' }}>
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
                    <small class="form-text text-muted">Selecciona el tema visual de la aplicación según tus
                        preferencias de visualización.</small>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-4">
                            <label for="voice_preference"><strong>Preferencia de Voz BeeMaiA</strong></label>
                            <select class="form-select" id="voice_preference" name="voice_preference">
                                <option value="female_1" {{ ($user->voice_preference ?? '') == 'female_1' ? 'selected' : '' }}>Voz Femenina 1</option>
                                <option value="female_2" {{ ($user->voice_preference ?? '') == 'female_2' ? 'selected' : '' }}>Voz Femenina 2</option>
                                <option value="male_1" {{ ($user->voice_preference ?? '') == 'male_1' ? 'selected' : '' }}>Voz Masculina 1</option>
                                <option value="male_2" {{ ($user->voice_preference ?? '') == 'male_2' ? 'selected' : '' }}>Voz Masculina 2</option>
                            </select>
                            <small class="form-text text-muted">Elige entre diferentes voces predefinidas para
                                la interacción de BeeMaiA.</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-4">
                            <label for="default_view"><strong>Vista Predeterminada</strong></label>
                            <select class="form-select" id="default_view" name="default_view">
                                <option value="dashboard" {{ ($user->default_view ?? '') == 'dashboard' ? 'selected' : '' }}>Panel de Control</option>
                                <option value="apiaries" {{ ($user->default_view ?? '') == 'apiaries' ? 'selected' : '' }}>Apiarios</option>
                                <option value="calendar" {{ ($user->default_view ?? '') == 'calendar' ? 'selected' : '' }}>Calendario</option>
                                <option value="reports" {{ ($user->default_view ?? '') == 'reports' ? 'selected' : '' }}>Informes</option>
                            </select>
                            <small class="form-text text-muted">Selecciona la pantalla que se mostrará al
                                iniciar la aplicación.</small>
                        </div>
                    </div>
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" id="voice_match" name="voice_match" {{ ($user->voice_match ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label" for="voice_match">
                        <strong>BeeMaiA Siempre Activa (Voice Match)</strong>
                    </label>
                    <div class="text-muted ms-4">
                        Integra la funcionalidad de "Voice Match" de Google para que BeeMaiA pueda activarse con
                        un comando de voz específico (ej. "Ok MaiA").
                        <div class="voice-match-info mt-2">
                            <p><i class="bi bi-mic-fill text-primary"></i> Di "Ok MaiA" para activar el
                                asistente sin tocar la pantalla</p>
                            <p><i class="bi bi-shield-check text-primary"></i> Tu voz se procesa localmente para
                                mayor privacidad</p>
                        </div>
                    </div>
                </div>

                <div class="form-group mb-4">
                    <label><strong>Notificaciones de Calendario</strong></label>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="calendar_email"
                                    name="calendar_email" {{ ($user->calendar_email ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="calendar_email">Recibir recordatorios por
                                    email</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="calendar_push"
                                    name="calendar_push" {{ ($user->calendar_push ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="calendar_push">Recibir notificaciones
                                    push</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mt-2">
                        <label for="reminder_time">Tiempo de anticipación para recordatorios</label>
                        <select class="form-select" id="reminder_time" name="reminder_time">
                            <option value="15" {{ ($user->reminder_time ?? '') == '15' ? 'selected' : '' }}>15
                                minutos antes</option>
                            <option value="30" {{ ($user->reminder_time ?? '') == '30' ? 'selected' : '' }}>30
                                minutos antes</option>
                            <option value="60" {{ ($user->reminder_time ?? '') == '60' ? 'selected' : '' }}>1 hora
                                antes</option>
                            <option value="120" {{ ($user->reminder_time ?? '') == '120' ? 'selected' : '' }}>2
                                horas antes</option>
                            <option value="1440" {{ ($user->reminder_time ?? '') == '1440' ? 'selected' : '' }}>1
                                día antes</option>
                        </select>
                    </div>
                </div>

                <div class="form-actions mt-4">
                    <button type="button" class="btn btn-primary" id="savePreferencesBtn">Guardar
                        Preferencias</button>
                    <button type="reset" class="btn btn-outline-secondary ms-2">Restablecer</button>
                </div>
            </form>
        </div>
    </div>
</section>


<script>
    document.addEventListener('DOMContentLoaded',function(){
        const form = document.getElementById('preferences-form');
        form.addEventListener('submit',()=>{
            form.querySelector('button[type="submit"]').disabled = true;
        });
    });
    </script>