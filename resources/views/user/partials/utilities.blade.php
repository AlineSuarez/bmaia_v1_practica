
<section class="tab-pane fade" id="utilities" role="tabpanel" aria-labelledby="utilities-tab">
    <div class="card settings-card mb-4">
        <div class="card-header">
            <h3>Utilidades</h3>
            <p class="text-muted">Herramientas adicionales para mejorar tu experiencia con BeeMaiA y optimizar
                tu gestión apícola.</p>
        </div>
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

<script>
    document.addEventListener('DOMContentLoaded', function(){
        /**
         * Función genérica para enviar un form como JSON y manejar la respuesta.
         * @param {string} formId    – id del <form>
         * @param {string} url       – endpoint al que hacer POST
         * @param {string} modalId   – id del modal para cerrarlo tras el éxito
         * @param {Function} onSuccess – callback con el objeto creado
         */
        async function submitModalForm(formId, url, modalId, onSuccess) {
            const form = document.getElementById(formId);
            const data = Object.fromEntries(new FormData(form));
            // Convertir checkbox a booleanos
            for (let [key, val] of Object.entries(data)) {
            if (val === 'on') data[key] = true;
            }
            try {
            const resp = await fetch(url, {
                method: 'POST',
                headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(data)
            });
            if (!resp.ok) throw new Error('Error HTTP ' + resp.status);
            const json = await resp.json();
            // callback para insertar en la UI
            onSuccess(json);
            // cerrar modal
            const modalEl = document.getElementById(modalId);
            bootstrap.Modal.getInstance(modalEl).hide();
            // limpiar form
            form.reset();
            } catch (err) {
            console.error(err);
            alert('No se pudo guardar. Revisa la consola.');
            }
        }
        
        // 1) Alertas
        document.getElementById('saveAlertBtn').addEventListener('click', ()=>{
            submitModalForm(
            'alertForm',
            '/alerts',
            'alertModal',
            alert => {
                // ejemplo: agregar fila a tu tabla de alertas
                const tbody = document.querySelector('.alert-list tbody');
                const tr = document.createElement('tr');
                tr.innerHTML = `
                <td>${alert.title}</td>
                <td>${alert.type}</td>
                <td>${alert.date}</td>
                <td>${alert.priority}</td>
                <td>
                    <button class="btn btn-sm btn-danger btn-delete-alert" data-id="${alert.id}">Eliminar</button>
                </td>`;
                tbody.prepend(tr);
            }
            );
        });
        
        // 2) Recordatorios
        document.getElementById('saveReminderBtn').addEventListener('click', ()=>{
            submitModalForm(
            'reminderForm',
            '/reminders',
            'reminderModal',
            reminder => {
                const tbody = document.querySelector('.reminder-list tbody');
                const tr = document.createElement('tr');
                tr.innerHTML = `
                <td>${reminder.title}</td>
                <td>${reminder.date} ${reminder.time || ''}</td>
                <td>${reminder.repeat}</td>
                <td>${reminder.notes || ''}</td>
                <td>
                    <button class="btn btn-sm btn-danger btn-delete-reminder" data-id="${reminder.id}">Eliminar</button>
                </td>`;
                tbody.prepend(tr);
            }
            );
        });
        
        // 3) Fechas Importantes
        document.getElementById('saveDateBtn').addEventListener('click', ()=>{
            submitModalForm(
            'datesForm',
            '/important-dates',
            'datesModal',
            date => {
                const tbody = document.querySelector('.dates-list tbody');
                const tr = document.createElement('tr');
                tr.innerHTML = `
                <td>${date.title}</td>
                <td>${date.type}</td>
                <td>${date.value}</td>
                <td>${date.recurring ? 'Sí' : 'No'}</td>
                <td>${date.notes || ''}</td>
                <td>
                    <button class="btn btn-sm btn-danger btn-delete-date" data-id="${date.id}">Eliminar</button>
                </td>`;
                tbody.prepend(tr);
            }
            );
        });
        
        // 4) Contactos de Emergencia
        document.getElementById('saveContactBtn').addEventListener('click', ()=>{
            submitModalForm(
            'contactsForm',
            '/emergency-contacts',
            'contactsModal',
            contact => {
                const tbody = document.querySelector('.contacts-list tbody');
                const tr = document.createElement('tr');
                tr.innerHTML = `
                <td>${contact.name}</td>
                <td>${contact.relation}</td>
                <td>+56 ${contact.phone}</td>
                <td>${contact.email || ''}</td>
                <td>${contact.address || ''}</td>
                <td>
                    <button class="btn btn-sm btn-danger btn-delete-contact" data-id="${contact.id}">Eliminar</button>
                </td>`;
                tbody.prepend(tr);
            }
            );
        });
        
        // Opcional: manejo genérico de delete
        ['alert','reminder','date','contact'].forEach(type=>{
            document.addEventListener('click', async e=>{
            if (!e.target.classList.contains(`btn-delete-${type}`)) return;
            const id = e.target.dataset.id;
            if (!confirm('¿Eliminar este ítem?')) return;
            try {
                await fetch(`/${type === 'date' ? 'important-dates' : (type==='contact'?'emergency-contacts': type+'s')}/${id}`, {
                method:'DELETE',
                headers:{ 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                e.target.closest('tr').remove();
            } catch(err){
                console.error(err);
                alert('No se pudo eliminar.');
            }
            });
        });
    
    });
    </script>
    