
<section class="tab-pane fade" id="utilities" role="tabpanel" aria-labelledby="utilities-tab">
    <div class="card settings-card mb-4">
        <div class="card-header">
            <h3>Utilidades</h3>
            <p class="text-muted">Herramientas adicionales para mejorar tu experiencia con BeeMaiA y optimizar
                tu gestión apícola.</p>
        </div>
        <!-- Alertas -->
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card utility-card h-100">
                        <div class="card-header">
                            <h4><i class="bi bi-bell"></i> Crear Alertas</h4>
                        </div>
                        <div class="card-body">
                            <p>Define alertas personalizadas para eventos específicos relacionados con tus
                                colmenas:</p>
                            <ul class="utility-features">
                                <li>Alertas de temperatura</li>
                                <li>Recordatorios de inspección</li>
                                <li>Notificaciones de tratamientos</li>
                                <li>Alertas de cosecha</li>
                            </ul>
                            <button type="button" class="btn btn-outline-primary mt-3" data-bs-toggle="modal"
                                data-bs-target="#alertModal">
                                Configurar Alertas
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Recordatorios -->
                <div class="col-md-6 mb-4">
                    <div class="card utility-card h-100">
                        <div class="card-header">
                            <h4><i class="bi bi-calendar-check"></i> Crear Recordatorios</h4>
                        </div>
                        <div class="card-body">
                            <p>Establece recordatorios para tareas apícolas importantes:</p>
                            <ul class="utility-features">
                                <li>Alimentación de colmenas</li>
                                <li>Revisiones periódicas</li>
                                <li>Aplicación de tratamientos</li>
                                <li>Preparación para invierno/verano</li>
                            </ul>
                            <button type="button" class="btn btn-outline-primary mt-3" data-bs-toggle="modal"
                                data-bs-target="#reminderModal">
                                Configurar Recordatorios
                            </button>
                        </div>
                    </div>
                </div>
                <!-- fechas importantes -->
                <div class="col-md-6 mb-4">
                    <div class="card utility-card h-100">
                        <div class="card-header">
                            <h4><i class="bi bi-calendar-heart"></i> Fechas Importantes</h4>
                        </div>
                        <div class="card-body">
                            <p>Guarda fechas relevantes para tu agenda personal y apícola:</p>
                            <ul class="utility-features">
                                <li>Cumpleaños y aniversarios</li>
                                <li>Inicio de temporadas de floración</li>
                                <li>Eventos apícolas y ferias</li>
                                <li>Fechas de vencimiento de certificaciones</li>
                            </ul>
                            <button type="button" class="btn btn-outline-primary mt-3" data-bs-toggle="modal"
                                data-bs-target="#datesModal">
                                Gestionar Fechas
                            </button>
                        </div>
                    </div>
                </div>
                <!-- contactos de emergencia -->
                <div class="col-md-6 mb-4">
                    <div class="card utility-card h-100">
                        <div class="card-header">
                            <h4><i class="bi bi-person-lines-fill"></i> Contactos de Emergencia</h4>
                        </div>
                        <div class="card-body">
                            <p>Registra contactos importantes para situaciones imprevistas:</p>
                            <ul class="utility-features">
                                <li>Veterinarios especializados</li>
                                <li>Otros apicultores de confianza</li>
                                <li>Servicios de emergencia locales</li>
                                <li>Proveedores de equipos y suministros</li>
                            </ul>
                            <button type="button" class="btn btn-outline-primary mt-3" data-bs-toggle="modal"
                                data-bs-target="#contactsModal">
                                Gestionar Contactos
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
{{-- Alertas --}}
<div class="modal fade" id="alertModal" tabindex="-1" aria-labelledby="alertModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="alertForm" class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="alertModalLabel">Configurar Alerta</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            @csrf
            <div class="mb-3">
            <label for="alertTitleInput" class="form-label">Título de la Alerta</label>
            <input type="text" class="form-control" id="alertTitleInput" name="title" required>
            </div>
            <div class="mb-3">
            <label for="alertDescriptionTextarea" class="form-label">Descripción</label>
            <textarea class="form-control" id="alertDescriptionTextarea" name="description"></textarea>
            </div>
            <div class="mb-3">
            <label for="alertTypeSelect" class="form-label">Tipo de Alerta</label>
            <select class="form-select" id="alertTypeSelect" name="type" required>
                <option value="temperature">Temperatura</option>
                <option value="inspection">Inspección</option>
                <option value="treatment">Tratamiento</option>
                <option value="harvest">Cosecha</option>
            </select>
            </div>
            <div class="mb-3">
            <label for="alertDateInput" class="form-label">Fecha</label>
            <input type="date" class="form-control" id="alertDateInput" name="date" required>
            </div>
            <div class="mb-3">
            <label for="alertPrioritySelect" class="form-label">Prioridad</label>
            <select class="form-select" id="alertPrioritySelect" name="priority" required>
                <option value="low">Baja</option>
                <option value="medium">Media</option>
                <option value="high">Alta</option>
            </select>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            <button type="button" id="saveAlertBtn" class="btn btn-primary">Guardar Alerta</button>
        </div>
        </form>
    </div>
    </div>

    {{-- Recordatorios --}}
    <div class="modal fade" id="reminderModal" tabindex="-1" aria-labelledby="reminderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="reminderForm" class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="reminderModalLabel">Configurar Recordatorio</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            @csrf
            <div class="mb-3">
            <label for="reminderTitleInput" class="form-label">Título del Recordatorio</label>
            <input type="text" class="form-control" id="reminderTitleInput" name="title" required>
            </div>
            <div class="mb-3">
            <label for="reminderDateInput" class="form-label">Fecha</label>
            <input type="date" class="form-control" id="reminderDateInput" name="date" required>
            </div>
            <div class="mb-3">
            <label for="reminderTimeInput" class="form-label">Hora</label>
            <input type="time" class="form-control" id="reminderTimeInput" name="time">
            </div>
            <div class="mb-3">
            <label for="reminderRepeatSelect" class="form-label">Repetir</label>
            <select class="form-select" id="reminderRepeatSelect" name="repeat">
                <option value="none">No repetir</option>
                <option value="daily">Diariamente</option>
                <option value="weekly">Semanalmente</option>
                <option value="monthly">Mensualmente</option>
            </select>
            </div>
            <div class="mb-3">
            <label for="reminderNotesTextarea" class="form-label">Notas</label>
            <textarea class="form-control" id="reminderNotesTextarea" name="notes"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            <button type="button" id="saveReminderBtn" class="btn btn-primary">Guardar Recordatorio</button>
        </div>
        </form>
    </div>
    </div>

    {{-- Fechas Importantes --}}
    <div class="modal fade" id="datesModal" tabindex="-1" aria-labelledby="datesModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="dateForm" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="datesModalLabel">Gestionar Fecha Importante</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @csrf

                <div class="mb-3">
                <label for="dateTitleInput" class="form-label">Título</label>
                <input
                    type="text"
                    class="form-control"
                    id="dateTitleInput"
                    name="title"
                    required
                >
                </div>

                <div class="mb-3">
                <label for="dateTypeSelect" class="form-label">Tipo</label>
                <select
                    class="form-select"
                    id="dateTypeSelect"
                    name="type"
                    required
                >
                    <option value="birthday">Cumpleaños</option>
                    <option value="anniversary">Aniversario</option>
                    <option value="flowering">Floración</option>
                    <option value="event">Evento</option>
                    <option value="other">Otro</option>
                </select>
                </div>

                <div class="mb-3">
                <label for="dateValueInput" class="form-label">Fecha</label>
                <input
                    type="date"
                    class="form-control"
                    id="dateValueInput"
                    name="date"
                    required
                >
                </div>

                <div class="form-check mb-3">
                <input
                    class="form-check-input"
                    type="checkbox"
                    id="dateRecurringCheckbox"
                    name="recurs_annually"
                    value="1"
                >
                <label
                    class="form-check-label"
                    for="dateRecurringCheckbox"
                >
                    Repetir anualmente
                </label>
                </div>

                <div class="mb-3">
                <label for="dateNotesTextarea" class="form-label">Notas</label>
                <textarea
                    class="form-control"
                    id="dateNotesTextarea"
                    name="notes"
                ></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button
                type="button"
                class="btn btn-secondary"
                data-bs-dismiss="modal"
                >
                Cerrar
                </button>
                <button
                type="button"
                id="saveDateBtn"
                class="btn btn-primary"
                >
                Guardar Fecha
                </button>
            </div>
            </form>
        </div>
    </div>

    {{-- Contactos de Emergencia --}}
    <div class="modal fade" id="contactsModal" tabindex="-1" aria-labelledby="contactsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="contactForm" class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="contactsModalLabel">Gestionar Contacto</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            @csrf
            <div class="mb-3">
            <label for="contactNameInput" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="contactNameInput" name="name" required>
            </div>
            <div class="mb-3">
            <label for="contactRelationSelect" class="form-label">Relación</label>
            <select class="form-select" id="contactRelationSelect" name="relation" required>
                <option value="family">Familiar</option>
                <option value="friend">Amigo</option>
                <option value="vet">Veterinario</option>
                <option value="other">Otro</option>
            </select>
            </div>
            <div class="mb-3">
            <label for="contactPhoneInput" class="form-label">Teléfono</label>
            <input type="tel" class="form-control" id="contactPhoneInput" name="phone" required>
            </div>
            <div class="mb-3">
            <label for="contactEmailInput" class="form-label">Correo</label>
            <input type="email" class="form-control" id="contactEmailInput" name="email">
            </div>
            <div class="mb-3">
            <label for="contactAddressInput" class="form-label">Dirección</label>
            <input type="text" class="form-control" id="contactAddressInput" name="address">
            </div>
            <div class="mb-3">
            <label for="contactNotesTextarea" class="form-label">Notas</label>
            <textarea class="form-control" id="contactNotesTextarea" name="notes"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            <button type="button" id="saveContactBtn" class="btn btn-primary">Guardar Contacto</button>
        </div>
        </form>
    </div>
</div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

    function showSuccess(msg) {
        toastr.success(msg, '', { timeOut: 1000 });
    }

    async function handleSave(formId, url, modalId, successMsg) {
        const form = document.getElementById(formId);
        const data = Object.fromEntries(new FormData(form));
        for (let k in data) {
            if (data[k] === 'on') data[k] = true;
        }

        try {
        // 1) Enviar petición
        const res = await fetch(url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(data)
        });

        // 2) Manejar errores HTTP
        if (!res.ok) {
            let errBody;
            try {
                errBody = await res.json();
            } catch {
                errBody = { message: res.statusText };
            }
            console.error('>> Server error body:', errBody);

            // muestra el mensaje de la excepción que devolviste en PHP
            toastr.error(errBody.message || errBody.error || res.statusText);
            return;
            }


        // 3) Éxito: cerrar modal, reset y notificar
        await res.json(); // opcionalmente lo guardas en una variable
        showSuccess(successMsg);
        bootstrap.Modal.getInstance(
            document.getElementById(modalId)
        ).hide();
        form.reset();

        // 4) Aquí podrías recargar la lista de alertas, si la tienes en el DOM
        }
        catch (err) {
        console.error(err);
        toastr.error('Error al guardar');
        }
    }

    // Asignar listeners
    document.getElementById('saveAlertBtn')
        .addEventListener('click', () => {
        handleSave(
            'alertForm',
            '/user/settings/alerts',
            'alertModal',
            'Alerta guardada'
        );
        });

    document.getElementById('saveReminderBtn')
        .addEventListener('click', () => {
        handleSave(
            'reminderForm',
            '/user/settings/reminders',
            'reminderModal',
            'Recordatorio guardado'
        );
        });

    document.getElementById('saveDateBtn')
        .addEventListener('click', () => {
        handleSave(
            'dateForm',
            '/user/settings/dates',
            'datesModal',
            'Fecha guardada'
        );
        });

    document.getElementById('saveContactBtn')
        .addEventListener('click', () => {
        handleSave(
            'contactForm',
            '/user/settings/contacts',
            'contactsModal',
            'Contacto guardado'
        );
    });
});
</script>
@endpush
