document.addEventListener("DOMContentLoaded", () => {
    // Variable para controlar si ya se hizo la carga inicial
    let cargaInicialCompleta = false;

    // Función para mostrar/ocultar el loader global - SIMPLIFICADA
    function mostrarGlobalLoader(show = true) {
        const loader = document.getElementById("globalLoader");
        if (!loader) return;

        if (show) {
            loader.classList.remove("hidden");
            loader.style.display = "flex";
        } else {
            loader.classList.add("hidden");
            setTimeout(() => {
                if (loader.classList.contains("hidden")) {
                    loader.style.display = "none";
                }
            }, 600);
        }
    }

    function mostrarContenidoSuave() {
        const wrapper = document.querySelector(".apiario-wrapper");
        if (wrapper) {
            wrapper.classList.add("visible");
        }
    }

    // Función para verificar si existen tareas en el DOM
    function verificarTareasExistentes() {
        const tareasLista = document.querySelectorAll(".task-card");
        const tareasKanban = document.querySelectorAll(".kanban-task");
        const tareasTimeline = document.querySelectorAll(".timeline-task");
        const eventosCalendario = window.calendarEvents || [];

        const totalTareas =
            tareasLista.length +
            tareasKanban.length +
            tareasTimeline.length +
            eventosCalendario.length;
        return totalTareas > 0;
    }

    // Función de carga inicial - MODIFICADA para mostrar siempre la estructura
    // ...existing code...
    function inicializarModuloTareas() {
        if (cargaInicialCompleta) return; // Evitar múltiples ejecuciones

        mostrarGlobalLoader(true);

        // Verifica si hay tareas
        if (!verificarTareasExistentes()) {
            setTimeout(() => {
                mostrarGlobalLoader(false);
                cargaInicialCompleta = true;
                mostrarContenidoSuave();
            }, 50);
        } else {
            setTimeout(() => {
                mostrarGlobalLoader(false);
                cargaInicialCompleta = true;
                mostrarContenidoSuave();
            }, 1000);
        }
    }

    // EventBus y otras configuraciones...
    window.eventBus = window.eventBus || new EventTarget();

    // Referencias DOM actualizadas para la nueva estructura
    const DOM = {
        selectAllCheckbox: document.getElementById("select-all-subtasks"),
        subtasksCheckboxes: document.querySelectorAll(".subtask-checkbox"),
        estadoModal: document.getElementById("estadoModal")
            ? new bootstrap.Modal(document.getElementById("estadoModal"))
            : null,
        addSubtareaBtn: document.getElementById("add-subtarea"),
        subtareasContainer: document.getElementById("subtareas-container"),
        toggleFormBtn: document.getElementById("toggle-form"),
        confirmarEstadoBtn: document.getElementById("confirmarEstado"),
        viewTogglers: document.querySelectorAll(".view-toggler"),
        views: document.querySelectorAll(".view"),
        form: document.querySelector("form"),
        calendar: document.getElementById("calendar"),
        verTareasBtn: document.getElementById("ver-tareas-btn"),
        agregarTareasBtn: document.getElementById("agregar-tareas-btn"),
        tareasListModal: document.getElementById("tareasListModal"),
        tareasListContainer: document.getElementById("tareas-list-container"),
        searchTareas: document.getElementById("search-tareas"),
        filterEstado: document.getElementById("filter-estado"),
        filterPrioridad: document.getElementById("filter-prioridad"),
        tareasCount: document.getElementById("tareas-count"),
        tareasCompleted: document.getElementById("tareas-completed"),
        emptyState: document.getElementById("empty-state"),

        // Modal de tareas predefinidas - ACTUALIZADO para nueva estructura
        tareasPredefinidasModal: document.getElementById(
            "tareasPredefinidasModal"
        ),
        selectAllGlobal: document.getElementById("select-all-global"),
        selectAllEtapaCheckboxes:
            document.querySelectorAll(".select-all-etapa"),
        subtasksCheckboxesPredefinidas: document.querySelectorAll(
            ".subtask-checkbox-predefinidas"
        ),
        tareasSeleccionadasCount: document.getElementById(
            "tareas-seleccionadas-count"
        ),
        btnAgregarSeleccionadas: document.getElementById(
            "btn-agregar-seleccionadas"
        ),
        formTareasPredefinidasModal: document.getElementById(
            "form-tareas-predefinidas"
        ),

        // Modal de crear tareas
        crearTareasModal: document.getElementById("crearTareasModal"),
        tareaGeneralIdModal: document.getElementById("tarea_general_id_modal"),
        btnNuevaEtapaModal: document.getElementById("btn-nueva-etapa-modal"),
        formNuevaEtapaModal: document.getElementById("form-nueva-etapa-modal"),
        nombreNuevaEtapaModal: document.getElementById(
            "nombre_nueva_etapa_modal"
        ),
        guardarNuevaEtapaModal: document.getElementById(
            "guardar-nueva-etapa-modal"
        ),
        cancelarNuevaEtapaModal: document.getElementById(
            "cancelar-nueva-etapa-modal"
        ),
        addSubtareaModal: document.getElementById("add-subtarea-modal"),
        subtareasContainerModal: document.getElementById(
            "subtareas-container-modal"
        ),
        tareasCreadasCount: document.getElementById("tareas-creadas-count"),
        btnGuardarTareas: document.getElementById("btn-guardar-tareas"),
        formCrearTareas: document.getElementById("form-crear-tareas"),
    };

    let subtareaIndex = 0;
    let currentButton;

    // Variables para modales
    let subtareaIndexModal = 0;
    let tareasSeleccionadas = 0;
    let tareasCreadasCount = 0;

    // Variables para formulario directo
    let formularioDirectoVisible = false;

    // Referencias DOM para formulario directo
    const formularioDirectoDOM = {
        btnAgregarTareaDirecta: document.getElementById(
            "btn-agregar-tarea-directa"
        ),
        formularioTareaDirecta: document.getElementById(
            "formulario-tarea-directa"
        ),
        btnCerrarFormulario: document.getElementById("btn-cerrar-formulario"),
        btnCancelarTareaDirecta: document.getElementById(
            "btn-cancelar-tarea-directa"
        ),
        formTareaDirecta: document.getElementById("form-tarea-directa"),
        btnGuardarTareaDirecta: document.getElementById(
            "btn-guardar-tarea-directa"
        ),

        // Campos del formulario
        nombreTareaDirecta: document.getElementById("nombre_tarea_directa"),
        etapaTareaDirecta: document.getElementById("etapa_tarea_directa"),
        prioridadTareaDirecta: document.getElementById(
            "prioridad_tarea_directa"
        ),
        estadoTareaDirecta: document.getElementById("estado_tarea_directa"),
        fechaInicioDirecta: document.getElementById("fecha_inicio_directa"),
        fechaFinDirecta: document.getElementById("fecha_fin_directa"),

        // Nueva etapa
        btnNuevaEtapaDirecta: document.getElementById(
            "btn-nueva-etapa-directa"
        ),
        formNuevaEtapaDirecta: document.getElementById(
            "form-nueva-etapa-directa"
        ),
        nombreNuevaEtapaDirecta: document.getElementById(
            "nombre_nueva_etapa_directa"
        ),
        guardarNuevaEtapaDirecta: document.getElementById(
            "guardar-nueva-etapa-directa"
        ),
        cancelarNuevaEtapaDirecta: document.getElementById(
            "cancelar-nueva-etapa-directa"
        ),
    };

    // Funciones de actualización SIN LOADER
    const actualizarKanban = () => {
        console.debug("Actualizando Kanban...");
        setTimeout(() => {
            initializeKanban();
        }, 300);
    };

    const actualizarTimeline = () => {
        console.debug("Actualizando Timeline...");
    };

    // Recargar subtareas SIN LOADER (solo para actualizaciones de datos)
    const recargarSubtareas = () => {
        console.debug("Recargando subtareas...");
        // Sin loader - solo actualiza datos
    };

    // API de EventBus
    const emitirEvento = (nombre, detalle = {}) => {
        try {
            const evento = new CustomEvent(nombre, { detail: detalle });
            window.eventBus.dispatchEvent(evento);
            console.debug(`Evento "${nombre}" emitido`, detalle);
        } catch (error) {
            console.error(`Error al emitir evento "${nombre}":`, error);
        }
    };

    const escucharEvento = (nombre, callback) => {
        if (typeof callback !== "function") {
            console.warn("El callback proporcionado no es una función válida");
            return;
        }
        window.eventBus.addEventListener(nombre, callback);
    };

    // Gestión mejorada de checkboxes
    const updateSelectAllCheckbox = () => {
        if (!DOM.selectAllCheckbox) return;
        const checkboxes = Array.from(DOM.subtasksCheckboxes);
        if (checkboxes.length === 0) return;

        const allChecked = checkboxes.every((checkbox) => checkbox.checked);
        const someChecked = checkboxes.some((checkbox) => checkbox.checked);

        DOM.selectAllCheckbox.checked = allChecked;
        DOM.selectAllCheckbox.indeterminate = !allChecked && someChecked;
    };

    // Inicializar listeners para checkboxes
    if (DOM.selectAllCheckbox) {
        DOM.selectAllCheckbox.addEventListener("change", () => {
            const isChecked = DOM.selectAllCheckbox.checked;
            DOM.subtasksCheckboxes.forEach((checkbox) => {
                checkbox.checked = isChecked;
            });
        });
    }

    DOM.subtasksCheckboxes.forEach((checkbox) => {
        checkbox.addEventListener("change", updateSelectAllCheckbox);
    });

    // Inicializar estado de checkboxes
    updateSelectAllCheckbox();

    // Confirmar cambio de estado con validación mejorada
    if (DOM.confirmarEstadoBtn) {
        DOM.confirmarEstadoBtn.addEventListener("click", () => {
            const subtareaId = document.getElementById("subtareaId").value;
            const nuevoEstado = document.getElementById("nuevoEstado").value;

            if (!subtareaId || !nuevoEstado) {
                console.error("ID de subtarea o nuevo estado no válidos");
                Swal.fire(
                    "Error",
                    "Datos incompletos para actualizar estado",
                    "error"
                );
                return;
            }

            const csrfToken = document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute("content");
            if (!csrfToken) {
                console.error("Token CSRF no encontrado");
                Swal.fire(
                    "Error",
                    "Error de seguridad. Refresca la página e intenta nuevamente.",
                    "error"
                );
                return;
            }

            fetch(`/tareas/${subtareaId}/update-status`, {
                method: "PATCH",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                body: JSON.stringify({ estado: nuevoEstado }),
            })
                .then((response) => {
                    if (!response.ok)
                        throw new Error(`Error HTTP: ${response.status}`);
                    return response.json();
                })
                .then((data) => {
                    if (data.success) {
                        currentButton.textContent = nuevoEstado;
                        currentButton.dataset.currentState = nuevoEstado;
                        Swal.fire({
                            title: "¡Actualizado!",
                            text: "El estado de la subtarea ha sido actualizado.",
                            icon: "success",
                            showConfirmButton: false,
                            timer: 1500,
                        });
                        // Emitir evento para actualizar vistas
                        emitirEvento("subtareaActualizada", {
                            id: subtareaId,
                            estado: nuevoEstado,
                        });
                    } else {
                        Swal.fire(
                            "Error",
                            data.message ||
                                "Hubo un problema al actualizar el estado.",
                            "error"
                        );
                    }
                    DOM.estadoModal?.hide();
                })
                .catch((error) => {
                    console.error("Error en la petición:", error);
                    Swal.fire(
                        "Error",
                        "Error al procesar la solicitud. Intenta de nuevo.",
                        "error"
                    );
                    DOM.estadoModal?.hide();
                });
        });
    }

    // Agregar subtareas dinámicamente con mejor manejo de errores
    if (DOM.addSubtareaBtn) {
        DOM.addSubtareaBtn.addEventListener("click", () => {
            const template = document.getElementById("subtarea-template");
            if (!template || !DOM.subtareasContainer) {
                console.error(
                    "No se encontró la plantilla o el contenedor de subtareas"
                );
                return;
            }

            const newSubtarea = template.cloneNode(true);
            newSubtarea.style.display = "block";
            newSubtarea.removeAttribute("id");

            // Asignar nombres e IDs únicos a los inputs
            const inputs = newSubtarea.querySelectorAll("input, select");
            inputs.forEach((input) => {
                const fieldName = input.getAttribute("data-field");
                if (fieldName) {
                    input.name = `subtareas[${subtareaIndex}][${fieldName}]`;
                    input.id = `subtarea_${subtareaIndex}_${fieldName}`;
                    input.required = true;
                }
            });

            DOM.subtareasContainer.appendChild(newSubtarea);
            subtareaIndex++;

            // Animar entrada de la nueva subtarea para mejor experiencia de usuario
            newSubtarea.style.opacity = "0";
            newSubtarea.style.transform = "translateY(20px)";
            newSubtarea.style.transition =
                "opacity 0.3s ease, transform 0.3s ease";

            // Forzar un reflow para asegurar que la transición funcione
            newSubtarea.offsetHeight;

            // Aplicar transición
            setTimeout(() => {
                newSubtarea.style.opacity = "1";
                newSubtarea.style.transform = "translateY(0)";
            }, 10);
        });
    }

    // Gestión de vistas SIN LOADER - NAVEGACIÓN FLUIDA
    const hideAllViews = () => {
        DOM.views.forEach((view) => {
            if (!view.classList.contains("active")) {
                view.style.display = "none";
            }
        });
    };

    // Inicializar vista por defecto
    DOM.views.forEach((view) => view.classList.remove("active"));
    const defaultView = document.querySelector(".view.list");
    if (defaultView) defaultView.classList.add("active");
    hideAllViews();

    // Cambio entre vistas SIN LOADER - NAVEGACIÓN INSTANTÁNEA
    DOM.viewTogglers.forEach((button) => {
        button.addEventListener("click", function () {
            const viewName = this.getAttribute("data-view");
            if (!viewName) return;

            // Guardar la pestaña activa en localStorage
            localStorage.setItem("tareas_pestana_activa", viewName);

            // Actualizar clases activas
            DOM.viewTogglers.forEach((btn) => btn.classList.remove("active"));
            this.classList.add("active");

            DOM.views.forEach((v) => v.classList.remove("active"));
            const targetView = document.querySelector(`.view.${viewName}`);
            if (!targetView) return;

            targetView.classList.add("active");
            hideAllViews();

            // Mostrar vista seleccionada con transición suave
            targetView.style.display = "";
            targetView.style.opacity = "0";
            targetView.style.transition = "opacity 0.3s ease";

            setTimeout(() => {
                targetView.style.opacity = "1";
            }, 10);

            // Inicializar vistas específicas
            if (viewName === "kanban") {
                setTimeout(() => initializeKanban(), 100);
            }
        });
    });

    // Restaurar la pestaña activa desde localStorage
    const pestanaGuardada = localStorage.getItem("tareas_pestana_activa");
    const viewToActivate = pestanaGuardada || "list"; // "list" es la vista por defecto

    // Activar la pestaña guardada
    const togglerToActivate = document.querySelector(
        `.view-toggler[data-view="${viewToActivate}"]`
    );
    if (togglerToActivate) {
        togglerToActivate.classList.add("active");
        togglerToActivate.click();
    } else {
        // Si no existe, activa la primera por defecto
        const defaultViewToggler = document.querySelector(
            '.view-toggler[data-view="list"]'
        );
        if (defaultViewToggler) {
            defaultViewToggler.classList.add("active");
            defaultViewToggler.click();
        }
    }

    // Implementación de funcionalidad de arrastrar y soltar para el Kanban
    function initializeKanban() {
        // Limpiar listeners previos
        document.querySelectorAll(".kanban-task").forEach((t) => {
            t.replaceWith(t.cloneNode(true));
        });

        const columnasKanban = document.querySelectorAll(".kanban-column");
        const tareasKanban = document.querySelectorAll(".kanban-task");

        // Hacer tareas arrastrables
        tareasKanban.forEach((tarea) => {
            tarea.setAttribute("draggable", "true");

            tarea.addEventListener("dragstart", function (e) {
                e.dataTransfer.setData(
                    "text/plain",
                    this.getAttribute("data-id")
                );
                e.dataTransfer.effectAllowed = "move";
                this.classList.add("task-dragging");
                setTimeout(() => {
                    this.style.opacity = "0.4";
                }, 0);
            });

            tarea.addEventListener("dragend", function () {
                this.classList.remove("task-dragging");
                this.style.opacity = "1";
            });
        });

        // Función para añadir listeners a un área de drop (columna o contenedor interno)
        function addDropListeners(area, columna) {
            area.addEventListener("dragover", (e) => {
                e.preventDefault();
                e.dataTransfer.dropEffect = "move";
                columna.classList.add("column-highlight");
            });

            area.addEventListener("dragleave", () => {
                columna.classList.remove("column-highlight");
            });

            area.addEventListener("drop", (e) => {
                e.preventDefault();
                columna.classList.remove("column-highlight");

                const tareaId = e.dataTransfer.getData("text/plain");
                const nuevoEstado = columna.getAttribute("data-estado");

                if (!tareaId || !nuevoEstado) return;

                const tareaElement = document.querySelector(
                    `.kanban-task[data-id="${tareaId}"]`
                );
                if (!tareaElement) return;

                if (tareaElement.getAttribute("data-estado") === nuevoEstado)
                    return;

                // Mover visualmente la tarea
                area.appendChild(tareaElement);

                // Actualizar en backend
                const csrfToken = document
                    .querySelector('meta[name="csrf-token"]')
                    ?.getAttribute("content");
                if (!csrfToken) {
                    mostrarNotificacion(
                        "Error de seguridad. Refresca la página.",
                        "error"
                    );
                    return;
                }

                tareaElement.classList.add("task-updating");

                fetch(`/tareas/${tareaId}/update-status`, {
                    method: "PATCH",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                    },
                    body: JSON.stringify({ estado: nuevoEstado }),
                })
                    .then((response) => {
                        if (!response.ok)
                            throw new Error(`Error HTTP: ${response.status}`);
                        return response.json();
                    })
                    .then((data) => {
                        if (data.success) {
                            tareaElement.setAttribute(
                                "data-estado",
                                nuevoEstado
                            );
                            const estadoBadge =
                                tareaElement.querySelector(".estado-badge");
                            if (estadoBadge) {
                                estadoBadge.textContent = nuevoEstado;
                                estadoBadge.dataset.currentState = nuevoEstado;
                                actualizarClaseEstado(estadoBadge, nuevoEstado);
                            }
                            mostrarNotificacion(
                                "Tarea actualizada correctamente",
                                "success"
                            );
                            emitirEvento("subtareaActualizada", {
                                id: tareaId,
                                estado: nuevoEstado,
                            });
                        } else {
                            mostrarNotificacion(
                                data.message ||
                                    "Hubo un problema al actualizar el estado.",
                                "error"
                            );
                            actualizarKanban();
                        }
                    })
                    .catch((error) => {
                        console.error("Error en la petición:", error);
                        mostrarNotificacion(
                            "Error al actualizar. Intenta de nuevo.",
                            "error"
                        );
                        actualizarKanban();
                    })
                    .finally(() => {
                        tareaElement.classList.remove("task-updating");
                    });
            });
        }

        // Añadir listeners a cada columna y a su contenedor interno (si existe)
        columnasKanban.forEach((columna) => {
            const container = columna.querySelector(".kanban-tasks-container");
            if (container) {
                addDropListeners(container, columna);
            }
            addDropListeners(columna, columna);
        });
    }

    // Función para actualizar clases según el estado
    function actualizarClaseEstado(elemento, estado) {
        // Eliminar todas las clases de estado existentes
        elemento.classList.remove(
            "badge-pending",
            "badge-progress",
            "badge-completed",
            "badge-cancelled"
        );

        // Añadir la clase correspondiente según el nuevo estado
        switch (estado.toLowerCase()) {
            case "pendiente":
                elemento.classList.add("badge-pending");
                break;
            case "en progreso":
            case "en proceso":
                elemento.classList.add("badge-progress");
                break;
            case "completada":
            case "completado":
                elemento.classList.add("badge-completed");
                break;
            case "cancelada":
            case "cancelado":
                elemento.classList.add("badge-cancelled");
                break;
        }
    }

    // Función para mostrar notificaciones toast
    function mostrarNotificacion(mensaje, tipo = "info") {
        if (typeof Swal !== "undefined") {
            const iconos = {
                success: "success",
                error: "error",
                warning: "warning",
                info: "info",
            };

            Swal.fire({
                title: mensaje,
                icon: iconos[tipo] || "info",
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: "top-end",
            });
        } else {
            // Fallback a toast nativo si SweetAlert2 no está disponible
            mostrarToastNativo(mensaje, tipo);
        }
    }

    function mostrarToastNativo(mensaje, tipo) {
        // Crear contenedor si no existe
        let toastContainer = document.querySelector(".toast-container");
        if (!toastContainer) {
            toastContainer = document.createElement("div");
            toastContainer.className =
                "toast-container position-fixed top-0 end-0 p-3";
            toastContainer.style.zIndex = "9999";
            document.body.appendChild(toastContainer);
        }

        // Crear toast
        const toast = document.createElement("div");
        toast.className = `alert alert-${
            tipo === "error" ? "danger" : tipo
        } alert-dismissible fade show`;
        toast.style.minWidth = "300px";
        toast.innerHTML = `
            ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;

        toastContainer.appendChild(toast);

        // Auto-remover después de 3 segundos
        setTimeout(() => {
            if (toast.parentNode) {
                toast.remove();
            }
        }, 3000);
    }

    // ===== FUNCIONES PARA FORMULARIO DIRECTO =====

    // Mostrar formulario directo
    function mostrarFormularioDirecto() {
        if (!formularioDirectoDOM.formularioTareaDirecta) return;

        formularioDirectoVisible = true;
        formularioDirectoDOM.formularioTareaDirecta.style.display = "flex";
        formularioDirectoDOM.formularioTareaDirecta.setAttribute(
            "aria-hidden",
            "false"
        );

        // Agregar clase show con delay para animación
        setTimeout(() => {
            formularioDirectoDOM.formularioTareaDirecta.classList.add("show");
        }, 10);

        // Focus en el primer campo
        if (formularioDirectoDOM.nombreTareaDirecta) {
            setTimeout(
                () => formularioDirectoDOM.nombreTareaDirecta.focus(),
                300
            );
        }

        // Prevenir scroll del body
        document.body.style.overflow = "hidden";
    }

    // Ocultar formulario directo
    function ocultarFormularioDirecto() {
        if (!formularioDirectoDOM.formularioTareaDirecta) return;

        formularioDirectoVisible = false;
        formularioDirectoDOM.formularioTareaDirecta.classList.remove("show");
        formularioDirectoDOM.formularioTareaDirecta.setAttribute(
            "aria-hidden",
            "true"
        );

        setTimeout(() => {
            formularioDirectoDOM.formularioTareaDirecta.style.display = "none";
            limpiarFormularioDirecto();
        }, 300);

        // Restaurar scroll del body
        document.body.style.overflow = "";
    }

    // Limpiar formulario directo
    function limpiarFormularioDirecto() {
        if (formularioDirectoDOM.formTareaDirecta) {
            formularioDirectoDOM.formTareaDirecta.reset();
        }

        // Ocultar formulario nueva etapa si está visible
        if (formularioDirectoDOM.formNuevaEtapaDirecta) {
            formularioDirectoDOM.formNuevaEtapaDirecta.style.display = "none";
            if (formularioDirectoDOM.nombreNuevaEtapaDirecta) {
                formularioDirectoDOM.nombreNuevaEtapaDirecta.value = "";
            }
        }

        // Limpiar errores de validación
        const campos = formularioDirectoDOM.formTareaDirecta?.querySelectorAll(
            ".input-miel, .select-miel"
        );
        campos?.forEach((campo) => {
            campo.classList.remove("error");
            const errorMsg = campo.parentNode.querySelector(".error-message");
            if (errorMsg) errorMsg.remove();
        });
    }

    // Validar formulario directo
    function validarFormularioDirecto() {
        let esValido = true;
        const errores = [];

        // Limpiar errores previos
        const campos = formularioDirectoDOM.formTareaDirecta.querySelectorAll(
            ".input-miel, .select-miel"
        );
        campos.forEach((campo) => {
            campo.classList.remove("error");
            const errorMsg = campo.parentNode.querySelector(".error-message");
            if (errorMsg) errorMsg.remove();
        });

        // Validar nombre
        if (!formularioDirectoDOM.nombreTareaDirecta.value.trim()) {
            mostrarErrorCampo(
                formularioDirectoDOM.nombreTareaDirecta,
                "El nombre de la tarea es obligatorio"
            );
            esValido = false;
        }

        // Validar etapa
        if (!formularioDirectoDOM.etapaTareaDirecta.value) {
            mostrarErrorCampo(
                formularioDirectoDOM.etapaTareaDirecta,
                "Debes seleccionar una etapa"
            );
            esValido = false;
        }

        // Validar fechas
        const fechaInicio = formularioDirectoDOM.fechaInicioDirecta.value;
        const fechaFin = formularioDirectoDOM.fechaFinDirecta.value;

        if (
            fechaInicio &&
            fechaFin &&
            new Date(fechaInicio) > new Date(fechaFin)
        ) {
            mostrarErrorCampo(
                formularioDirectoDOM.fechaFinDirecta,
                "La fecha fin debe ser posterior a la fecha inicio"
            );
            esValido = false;
        }

        return esValido;
    }

    // Mostrar error en campo específico
    function mostrarErrorCampo(campo, mensaje) {
        campo.classList.add("error");

        const errorDiv = document.createElement("div");
        errorDiv.className = "error-message";
        errorDiv.innerHTML = `<i class="fa fa-exclamation-circle"></i> ${mensaje}`;

        campo.parentNode.appendChild(errorDiv);
    }

    // Enviar formulario directo
    async function enviarFormularioDirecto(e) {
        e.preventDefault();

        if (!validarFormularioDirecto()) {
            mostrarNotificacion(
                "Por favor corrige los errores en el formulario",
                "warning"
            );
            return;
        }

        // Mostrar estado de carga
        const wrapper =
            formularioDirectoDOM.formularioTareaDirecta.querySelector(
                ".formulario-wrapper"
            );
        wrapper.classList.add("formulario-loading");
        formularioDirectoDOM.btnGuardarTareaDirecta.disabled = true;

        try {
            const formData = new FormData(
                formularioDirectoDOM.formTareaDirecta
            );
            const csrfToken = document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute("content");

            const response = await fetch(
                formularioDirectoDOM.formTareaDirecta.action,
                {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                        Accept: "application/json",
                    },
                    body: formData,
                }
            );

            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                mostrarNotificacion("Tarea creada correctamente", "success");
                ocultarFormularioDirecto();

                // Emitir evento para actualizar vistas
                emitirEvento("tareaCreada", { tarea: data.tarea });

                // Recargar página después de un breve delay para mostrar la notificación
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                throw new Error(data.message || "Error al crear la tarea");
            }
        } catch (error) {
            console.error("Error al enviar formulario:", error);
            mostrarNotificacion(
                "Error al crear la tarea. Intenta de nuevo.",
                "error"
            );
        } finally {
            // Quitar estado de carga
            wrapper.classList.remove("formulario-loading");
            formularioDirectoDOM.btnGuardarTareaDirecta.disabled = false;
        }
    }

    // Crear nueva etapa desde formulario directo
    async function crearNuevaEtapaDirecta() {
        const nombre =
            formularioDirectoDOM.nombreNuevaEtapaDirecta.value.trim();

        if (!nombre) {
            mostrarNotificacion(
                "Por favor ingresa un nombre para la etapa",
                "warning"
            );
            formularioDirectoDOM.nombreNuevaEtapaDirecta.focus();
            return;
        }

        try {
            const csrfToken = document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute("content");

            const response = await fetch("/tareas-generales", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                    Accept: "application/json",
                },
                body: JSON.stringify({ nombre }),
            });

            if (!response.ok) throw new Error("Error al crear etapa");

            const etapa = await response.json();

            // Agregar nueva opción al select
            const option = new Option(etapa.nombre, etapa.id);
            formularioDirectoDOM.etapaTareaDirecta.appendChild(option);
            formularioDirectoDOM.etapaTareaDirecta.value = etapa.id;

            // Ocultar formulario nueva etapa
            formularioDirectoDOM.formNuevaEtapaDirecta.style.display = "none";
            formularioDirectoDOM.nombreNuevaEtapaDirecta.value = "";

            mostrarNotificacion("Etapa creada correctamente", "success");
        } catch (error) {
            console.error("Error:", error);
            mostrarNotificacion("Error al crear la etapa", "error");
        }
    }

    // Event Listeners para formulario directo
    if (formularioDirectoDOM.btnAgregarTareaDirecta) {
        formularioDirectoDOM.btnAgregarTareaDirecta.addEventListener(
            "click",
            mostrarFormularioDirecto
        );
    }

    if (formularioDirectoDOM.btnCerrarFormulario) {
        formularioDirectoDOM.btnCerrarFormulario.addEventListener(
            "click",
            ocultarFormularioDirecto
        );
    }

    if (formularioDirectoDOM.btnCancelarTareaDirecta) {
        formularioDirectoDOM.btnCancelarTareaDirecta.addEventListener(
            "click",
            ocultarFormularioDirecto
        );
    }

    if (formularioDirectoDOM.formTareaDirecta) {
        formularioDirectoDOM.formTareaDirecta.addEventListener(
            "submit",
            enviarFormularioDirecto
        );
    }

    // Nueva etapa en formulario directo
    if (formularioDirectoDOM.btnNuevaEtapaDirecta) {
        formularioDirectoDOM.btnNuevaEtapaDirecta.addEventListener(
            "click",
            () => {
                formularioDirectoDOM.formNuevaEtapaDirecta.style.display =
                    "block";
                formularioDirectoDOM.nombreNuevaEtapaDirecta.focus();
            }
        );
    }

    if (formularioDirectoDOM.cancelarNuevaEtapaDirecta) {
        formularioDirectoDOM.cancelarNuevaEtapaDirecta.addEventListener(
            "click",
            () => {
                formularioDirectoDOM.formNuevaEtapaDirecta.style.display =
                    "none";
                formularioDirectoDOM.nombreNuevaEtapaDirecta.value = "";
            }
        );
    }

    if (formularioDirectoDOM.guardarNuevaEtapaDirecta) {
        formularioDirectoDOM.guardarNuevaEtapaDirecta.addEventListener(
            "click",
            crearNuevaEtapaDirecta
        );
    }

    // Cerrar formulario con ESC
    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape" && formularioDirectoVisible) {
            ocultarFormularioDirecto();
        }
    });

    // Cerrar formulario al hacer clic fuera
    if (formularioDirectoDOM.formularioTareaDirecta) {
        formularioDirectoDOM.formularioTareaDirecta.addEventListener(
            "click",
            (e) => {
                if (e.target === formularioDirectoDOM.formularioTareaDirecta) {
                    ocultarFormularioDirecto();
                }
            }
        );
    }

    // Toggle del formulario con animación mejorada
    if (DOM.toggleFormBtn) {
        DOM.toggleFormBtn.addEventListener("click", () => {
            const form = document.getElementById("new-task-form");
            if (!form) return;

            // Si está oculto, mostrarlo con animación
            if (form.style.display === "none" || form.style.display === "") {
                form.style.display = "block";
                form.style.maxHeight = "0px";
                form.style.overflow = "hidden";
                form.style.transition = "max-height 0.5s ease";

                // Forzar un reflow para asegurar que la transición funcione
                form.offsetHeight;

                // Obtener la altura natural del contenido
                const height = form.scrollHeight;
                form.style.maxHeight = height + "px";

                // Quitar restricciones después de la transición
                setTimeout(() => {
                    form.style.maxHeight = "";
                    form.style.overflow = "";
                }, 500);
            } else {
                // Si está visible, ocultarlo con animación
                form.style.maxHeight = form.scrollHeight + "px";
                form.style.overflow = "hidden";
                form.style.transition = "max-height 0.5s ease";

                // Forzar un reflow
                form.offsetHeight;

                // Colapsar
                form.style.maxHeight = "0px";

                // Ocultar después de la transición
                setTimeout(() => {
                    form.style.display = "none";
                    form.style.maxHeight = "";
                    form.style.overflow = "";
                }, 500);
            }
        });
    }

    // Sincronizar el editor Quill si existe
    let quill;
    if (DOM.form) {
        DOM.form.addEventListener("submit", (e) => {
            if (quill) {
                try {
                    const descriptionField =
                        document.querySelector("#description");
                    if (descriptionField) {
                        descriptionField.value = quill.root.innerHTML;
                    }
                } catch (error) {
                    console.error("Error al sincronizar editor Quill:", error);
                    // No prevenir envío del formulario para evitar bloqueo
                }
            }
        });
    }

    // Escuchar eventos SIN LOADER para actualizaciones
    escucharEvento("subtareaActualizada", (event) => {
        console.debug("Evento subtareaActualizada recibido:", event.detail);
        // Solo actualizar datos, sin loader
        recargarSubtareas();
        actualizarKanban();
        actualizarTimeline();
    });

    // INICIALIZACIÓN: Solo ejecutar la carga inicial UNA VEZ
    inicializarModuloTareas();

    // Llamar a la inicialización durante la carga inicial
    if (document.querySelector(".view.kanban.active")) {
        initializeKanban();
    }

    // Agregar estilos para mejorar la experiencia de arrastrar y soltar
    const agregarEstilosKanban = () => {
        // Verificar si ya existe el estilo
        if (document.getElementById("kanban-drag-styles")) return;

        const estilos = document.createElement("style");
        estilos.id = "kanban-drag-styles";
        estilos.textContent = `
            .kanban-task {
                cursor: grab;
                transition: transform 0.2s, opacity 0.2s, box-shadow 0.2s;
            }
            .task-dragging {
                cursor: grabbing;
                box-shadow: 0 10px 20px rgba(0,0,0,0.19), 0 6px 6px rgba(0,0,0,0.23) !important;
                z-index: 999;
            }
            .column-highlight {
                background-color: rgba(255, 250, 230, 0.7);
                border: 2px dashed var(--primary-color) !important;
            }
            .task-updating {
                position: relative;
                pointer-events: none;
            }
            .task-updating::after {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(255, 255, 255, 0.7);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 10;
            }
            .task-updating::before {
                content: '';
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                width: 24px;
                height: 24px;
                border: 3px solid rgba(237, 168, 20, 0.2);
                border-top-color: var(--primary-color);
                border-radius: 50%;
                animation: spin 1s linear infinite;
                z-index: 11;
            }
            .toast-container {
                z-index: 9999;
            }
            .toast {
                opacity: 0;
                animation: fadeInUp 0.3s ease forwards;
            }
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            @keyframes spin {
                to {
                    transform: translate(-50%, -50%) rotate(360deg);
                }
            }
        `;
        document.head.appendChild(estilos);
    };

    // Llamar a la función para añadir estilos
    agregarEstilosKanban();

    // --- Agregar nueva etapa dinámicamente ---
    const btnNuevaEtapa = document.getElementById("btn-nueva-etapa");
    const formNuevaEtapa = document.getElementById("form-nueva-etapa");
    const inputNombreEtapa = document.getElementById("nombre_nueva_etapa");
    const selectEtapas = document.getElementById("tarea_general_id");

    if (btnNuevaEtapa && formNuevaEtapa && inputNombreEtapa && selectEtapas) {
        btnNuevaEtapa.addEventListener("click", () => {
            formNuevaEtapa.style.display =
                formNuevaEtapa.style.display === "none" ? "block" : "none";
        });

        document
            .getElementById("guardar-nueva-etapa")
            .addEventListener("click", () => {
                const nombre = inputNombreEtapa.value.trim();
                if (!nombre) {
                    mostrarNotificacion(
                        "Debes ingresar un nombre para la etapa.",
                        "warning"
                    );
                    return;
                }

                const csrfToken = document
                    .querySelector('meta[name="csrf-token"]')
                    ?.getAttribute("content");

                fetch("/tareas-generales", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                    },
                    body: JSON.stringify({ nombre }),
                })
                    .then((res) => {
                        if (!res.ok) throw new Error("Error al guardar etapa");
                        return res.json();
                    })
                    .then((data) => {
                        // Agregar nueva opción al select y seleccionarla
                        const newOption = document.createElement("option");
                        newOption.value = data.id;
                        newOption.textContent = data.nombre;
                        newOption.selected = true;
                        selectEtapas.appendChild(newOption);

                        // Limpiar y ocultar formulario
                        inputNombreEtapa.value = "";
                        formNuevaEtapa.style.display = "none";

                        mostrarNotificacion(
                            "Etapa creada correctamente.",
                            "success"
                        );
                    })
                    .catch((err) => {
                        console.error(err);
                        mostrarNotificacion(
                            "Error al crear la etapa.",
                            "error"
                        );
                    });
            });
    }

    // Variables para el modal de tareas
    let tareasData = [];
    let filteredTareas = [];

    // Función para cargar tareas en el modal
    async function cargarTareasModal() {
        try {
            const response = await fetch("/api/tareas", {
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    Accept: "application/json",
                },
            });

            if (!response.ok) throw new Error("Error al cargar tareas");

            const data = await response.json();
            tareasData = data.tareas || [];
            filteredTareas = [...tareasData];
            mostrarTareasEnModal();
            actualizarEstadisticas();
        } catch (error) {
            console.error("Error al cargar tareas:", error);
            mostrarErrorEnModal();
        }
    }

    // Función para mostrar tareas en el modal
    function mostrarTareasEnModal() {
        if (!DOM.tareasListContainer) return;

        // Limpiar loading
        DOM.tareasListContainer.innerHTML = "";

        if (filteredTareas.length === 0) {
            DOM.emptyState.style.display = "block";
            return;
        }

        DOM.emptyState.style.display = "none";

        const tareasHTML = filteredTareas
            .map(
                (tarea) => `
            <div class="tarea-card-modal" data-id="${tarea.id}" tabindex="0">
                <div class="tarea-header">
                    <h4 class="tarea-titulo">${escapeHtml(tarea.nombre)}</h4>
                    <span class="tarea-prioridad prioridad-${
                        tarea.prioridad?.toLowerCase() || "media"
                    }">
                        ${tarea.prioridad || "Media"}
                    </span>
                </div>
                
                <div class="tarea-info">
                    <div class="tarea-etapa">
                        <i class="fa fa-folder me-1"></i>
                        ${escapeHtml(tarea.etapa || "Sin etapa")}
                    </div>
                    
                    <div class="tarea-fechas">
                        <div class="fecha-item">
                            <i class="fa fa-calendar-start fecha-icon"></i>
                            <span>Inicio: ${formatearFecha(
                                tarea.fecha_inicio
                            )}</span>
                        </div>
                        <div class="fecha-item">
                            <i class="fa fa-calendar-check fecha-icon"></i>
                            <span>Fin: ${formatearFecha(
                                tarea.fecha_limite
                            )}</span>
                        </div>
                    </div>
                </div>
                
                <div class="tarea-footer">
                    <span class="tarea-estado estado-${
                        tarea.estado?.toLowerCase().replace(" ", "-") ||
                        "pendiente"
                    }">
                        ${tarea.estado || "Pendiente"}
                    </span>
                    
                    <div class="tarea-acciones">
                        <button class="btn-accion" onclick="editarTarea(${
                            tarea.id
                        })" title="Editar tarea">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn-accion" onclick="eliminarTarea(${
                            tarea.id
                        })" title="Eliminar tarea">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `
            )
            .join("");

        DOM.tareasListContainer.innerHTML = tareasHTML;

        // Agregar animación de entrada
        setTimeout(() => {
            document
                .querySelectorAll(".tarea-card-modal")
                .forEach((card, index) => {
                    card.style.opacity = "0";
                    card.style.transform = "translateY(20px)";
                    card.style.transition =
                        "opacity 0.3s ease, transform 0.3s ease";

                    setTimeout(() => {
                        card.style.opacity = "1";
                        card.style.transform = "translateY(0)";
                    }, index * 100);
                });
        }, 50);
    }

    // Función para filtrar tareas
    function filtrarTareas() {
        const searchTerm = DOM.searchTareas?.value.toLowerCase() || "";
        const estadoFilter = DOM.filterEstado?.value || "";
        const prioridadFilter = DOM.filterPrioridad?.value || "";

        filteredTareas = tareasData.filter((tarea) => {
            const matchSearch =
                !searchTerm ||
                tarea.nombre?.toLowerCase().includes(searchTerm) ||
                tarea.etapa?.toLowerCase().includes(searchTerm);

            const matchEstado =
                !estadoFilter ||
                tarea.estado?.toLowerCase() === estadoFilter.toLowerCase();

            const matchPrioridad =
                !prioridadFilter ||
                tarea.prioridad?.toLowerCase() ===
                    prioridadFilter.toLowerCase();

            return matchSearch && matchEstado && matchPrioridad;
        });

        mostrarTareasEnModal();
        actualizarEstadisticas();
    }

    // Función para actualizar estadísticas
    function actualizarEstadisticas() {
        if (!DOM.tareasCount || !DOM.tareasCompleted) return;

        const total = filteredTareas.length;
        const completadas = filteredTareas.filter(
            (t) => t.estado?.toLowerCase() === "completada"
        ).length;

        DOM.tareasCount.textContent = `${total} tarea${total !== 1 ? "s" : ""}`;
        DOM.tareasCompleted.textContent = `${completadas} completada${
            completadas !== 1 ? "s" : ""
        }`;
    }

    // Utilidades
    function escapeHtml(text) {
        const div = document.createElement("div");
        div.textContent = text;
        return div.innerHTML;
    }

    function formatearFecha(fecha) {
        if (!fecha) return "No definida";
        try {
            return new Date(fecha).toLocaleDateString("es-ES", {
                day: "2-digit",
                month: "2-digit",
                year: "numeric",
            });
        } catch {
            return fecha;
        }
    }

    function mostrarErrorEnModal() {
        if (!DOM.tareasListContainer) return;

        DOM.tareasListContainer.innerHTML = `
            <div class="error-state">
                <div class="empty-icon">
                    <i class="fa fa-exclamation-triangle"></i>
                </div>
                <h3>Error al cargar tareas</h3>
                <p>No se pudieron cargar las tareas. Intenta recargar la página.</p>
                <button type="button" class="btn-miel" onclick="window.location.reload()">
                    <i class="fa fa-refresh"></i> Recargar
                </button>
            </div>
        `;
    }

    // Event Listeners para los nuevos botones
    if (DOM.agregarTareasBtn) {
        DOM.agregarTareasBtn.addEventListener("click", () => {
            const form = document.getElementById("new-task-form");
            if (!form) return;

            // Mostrar el formulario con animación (código existente)
            if (form.style.display === "none" || form.style.display === "") {
                form.style.display = "block";
                form.style.maxHeight = "0px";
                form.style.overflow = "hidden";
                form.style.transition = "max-height 0.5s ease";
                form.offsetHeight;
                const height = form.scrollHeight;
                form.style.maxHeight = height + "px";

                setTimeout(() => {
                    form.style.maxHeight = "";
                    form.style.overflow = "";
                }, 500);

                // Scroll suave hasta el formulario
                form.scrollIntoView({ behavior: "smooth", block: "start" });
            } else {
                // Ocultar formulario (código existente)
                form.style.maxHeight = form.scrollHeight + "px";
                form.style.overflow = "hidden";
                form.style.transition = "max-height 0.5s ease";
                form.offsetHeight;
                form.style.maxHeight = "0px";

                setTimeout(() => {
                    form.style.display = "none";
                    form.style.maxHeight = "";
                    form.style.overflow = "";
                }, 500);
            }
        });
    }

    // Event Listeners para filtros del modal
    if (DOM.searchTareas) {
        DOM.searchTareas.addEventListener(
            "input",
            debounce(filtrarTareas, 300)
        );
    }
    if (DOM.filterEstado) {
        DOM.filterEstado.addEventListener("change", filtrarTareas);
    }
    if (DOM.filterPrioridad) {
        DOM.filterPrioridad.addEventListener("change", filtrarTareas);
    }

    // Cargar tareas cuando se abre el modal
    if (DOM.tareasListModal) {
        DOM.tareasListModal.addEventListener("show.bs.modal", () => {
            cargarTareasModal();
        });
    }

    // Funciones globales para acciones de tareas
    window.editarTarea = (id) => {
        // Aquí puedes abrir otro modal o redireccionar
        Swal.fire({
            title: "Editar Tarea",
            text: "Funcionalidad de edición en desarrollo",
            icon: "info",
        });
    };

    window.eliminarTarea = (id) => {
        Swal.fire({
            title: "¿Estás seguro?",
            text: "Esta acción no se puede deshacer",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#dc2626",
            cancelButtonColor: "#6b7280",
            confirmButtonText: "Sí, eliminar",
            cancelButtonText: "Cancelar",
        }).then((result) => {
            if (result.isConfirmed) {
                eliminarTareaConfirmado(id);
            }
        });
    };

    async function eliminarTareaConfirmado(id) {
        try {
            const csrfToken = document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute("content");

            const response = await fetch(`/tareas/${id}`, {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    Accept: "application/json",
                },
            });

            if (!response.ok) throw new Error("Error al eliminar");

            const data = await response.json();
            if (data.success) {
                Swal.fire(
                    "Eliminada",
                    "La tarea ha sido eliminada correctamente",
                    "success"
                );
                cargarTareasModal(); // Recargar lista
                // Emitir evento para actualizar otras vistas
                emitirEvento("tareaEliminada", { id });
            } else {
                throw new Error(data.message || "Error desconocido");
            }
        } catch (error) {
            console.error("Error:", error);
            Swal.fire("Error", "No se pudo eliminar la tarea", "error");
        }
    }

    // Utilidad debounce
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // ===== FUNCIONES PARA MODAL DE TAREAS PREDEFINIDAS - ACTUALIZADAS =====

    // Función para actualizar contadores y estados de checkboxes por etapa
    function updateSelectAllEtapaCheckbox(etapaId) {
        const selectAllEtapa = document.getElementById(
            `select-all-etapa-${etapaId}`
        );
        const checkboxesEtapa = document.querySelectorAll(
            `.etapa-${etapaId}-checkbox`
        );

        if (!selectAllEtapa || checkboxesEtapa.length === 0) return;

        const checkedBoxes = document.querySelectorAll(
            `.etapa-${etapaId}-checkbox:checked`
        );

        // Actualizar estado del checkbox de la etapa
        if (checkedBoxes.length === 0) {
            selectAllEtapa.checked = false;
            selectAllEtapa.indeterminate = false;
        } else if (checkedBoxes.length === checkboxesEtapa.length) {
            selectAllEtapa.checked = true;
            selectAllEtapa.indeterminate = false;
        } else {
            selectAllEtapa.checked = false;
            selectAllEtapa.indeterminate = true;
        }
    }

    // Función para actualizar el selector global
    function updateSelectAllGlobalCheckbox() {
        if (!DOM.selectAllGlobal) return;

        const todosLosCheckboxes = document.querySelectorAll(
            ".subtask-checkbox-predefinidas"
        );
        const checkboxesMarcados = document.querySelectorAll(
            ".subtask-checkbox-predefinidas:checked"
        );

        tareasSeleccionadas = checkboxesMarcados.length;

        // Actualizar estado del checkbox global
        if (checkboxesMarcados.length === 0) {
            DOM.selectAllGlobal.checked = false;
            DOM.selectAllGlobal.indeterminate = false;
        } else if (checkboxesMarcados.length === todosLosCheckboxes.length) {
            DOM.selectAllGlobal.checked = true;
            DOM.selectAllGlobal.indeterminate = false;
        } else {
            DOM.selectAllGlobal.checked = false;
            DOM.selectAllGlobal.indeterminate = true;
        }

        // Actualizar contador y botón
        DOM.tareasSeleccionadasCount.textContent = `${tareasSeleccionadas} tareas seleccionadas`;
        DOM.btnAgregarSeleccionadas.disabled = tareasSeleccionadas === 0;
    }

    // Event listener para selectores "Todo" por etapa
    document.addEventListener("change", (e) => {
        if (e.target.classList.contains("select-all-etapa")) {
            const etapaId = e.target.getAttribute("data-etapa");
            const checkboxesEtapa = document.querySelectorAll(
                `.etapa-${etapaId}-checkbox`
            );

            checkboxesEtapa.forEach((checkbox) => {
                checkbox.checked = e.target.checked;
            });

            updateSelectAllGlobalCheckbox();
        }
    });

    // Event listener para selector global
    if (DOM.selectAllGlobal) {
        DOM.selectAllGlobal.addEventListener("change", function () {
            const todosLosCheckboxes = document.querySelectorAll(
                ".subtask-checkbox-predefinidas"
            );
            const todosLosSelectoresEtapa =
                document.querySelectorAll(".select-all-etapa");

            todosLosCheckboxes.forEach((checkbox) => {
                checkbox.checked = this.checked;
            });

            todosLosSelectoresEtapa.forEach((selector) => {
                selector.checked = this.checked;
                selector.indeterminate = false;
            });

            updateSelectAllGlobalCheckbox();
        });
    }

    // Event listener para checkboxes individuales
    document.addEventListener("change", (e) => {
        if (e.target.classList.contains("subtask-checkbox-predefinidas")) {
            // Obtener la etapa del checkbox
            const etapaId =
                e.target.className.match(/etapa-(\d+)-checkbox/)?.[1];

            if (etapaId) {
                updateSelectAllEtapaCheckbox(etapaId);
            }

            updateSelectAllGlobalCheckbox();
        }
    });

    // Inicializar modal de tareas predefinidas
    if (DOM.tareasPredefinidasModal) {
        DOM.tareasPredefinidasModal.addEventListener("show.bs.modal", () => {
            // Limpiar todas las selecciones
            const todosLosCheckboxes = document.querySelectorAll(
                ".subtask-checkbox-predefinidas"
            );
            const todosLosSelectoresEtapa =
                document.querySelectorAll(".select-all-etapa");

            todosLosCheckboxes.forEach(
                (checkbox) => (checkbox.checked = false)
            );
            todosLosSelectoresEtapa.forEach((selector) => {
                selector.checked = false;
                selector.indeterminate = false;
            });

            if (DOM.selectAllGlobal) {
                DOM.selectAllGlobal.checked = false;
                DOM.selectAllGlobal.indeterminate = false;
            }

            updateSelectAllGlobalCheckbox();
        });
    }

    // ===== FUNCIONES PARA MODAL DE CREAR TAREAS =====

    // Mostrar/ocultar formulario nueva etapa
    if (DOM.btnNuevaEtapaModal) {
        DOM.btnNuevaEtapaModal.addEventListener("click", () => {
            DOM.formNuevaEtapaModal.style.display = "block";
            DOM.nombreNuevaEtapaModal.focus();
        });
    }

    if (DOM.cancelarNuevaEtapaModal) {
        DOM.cancelarNuevaEtapaModal.addEventListener("click", () => {
            DOM.formNuevaEtapaModal.style.display = "none";
            DOM.nombreNuevaEtapaModal.value = "";
        });
    }

    // Guardar nueva etapa
    if (DOM.guardarNuevaEtapaModal) {
        DOM.guardarNuevaEtapaModal.addEventListener("click", async () => {
            const nombre = DOM.nombreNuevaEtapaModal.value.trim();
            if (!nombre) {
                mostrarNotificacion(
                    "Por favor ingresa un nombre para la etapa",
                    "warning"
                );
                return;
            }

            try {
                const response = await fetch("/tareas-generales", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector(
                            'meta[name="csrf-token"]'
                        ).content,
                        Accept: "application/json",
                    },
                    body: JSON.stringify({ nombre }),
                });

                if (!response.ok) throw new Error("Error al crear etapa");

                const etapa = await response.json();

                // Agregar nueva opción al select
                const option = new Option(etapa.nombre, etapa.id);
                DOM.tareaGeneralIdModal.appendChild(option);
                DOM.tareaGeneralIdModal.value = etapa.id;

                // Ocultar formulario
                DOM.formNuevaEtapaModal.style.display = "none";
                DOM.nombreNuevaEtapaModal.value = "";

                mostrarNotificacion("Etapa creada correctamente", "success");
            } catch (error) {
                console.error("Error:", error);
                mostrarNotificacion("Error al crear la etapa", "error");
            }
        });
    }

    // ===== FUNCIONES PARA AGREGAR SUBTAREAS EN MODAL - CORREGIDAS =====

    // Agregar nueva subtarea en modal - FUNCIONA CORRECTAMENTE
    if (DOM.addSubtareaModal) {
        DOM.addSubtareaModal.addEventListener("click", () => {
            console.log("Botón Agregar Tarea clickeado"); // Debug
            agregarSubtareaModal();
        });
    }

    function agregarSubtareaModal() {
        console.log("Ejecutando agregarSubtareaModal"); // Debug

        const template = document.getElementById("subtarea-template-modal");
        const container = DOM.subtareasContainerModal;

        if (!template || !container) {
            console.error("No se encontró la plantilla o el contenedor");
            return;
        }

        subtareaIndexModal++;
        const clone = template.cloneNode(true);
        clone.id = `subtarea-${subtareaIndexModal}`;
        clone.style.display = "block";

        // Habilitar los campos del clon
        const inputs = clone.querySelectorAll("[data-field]");
        inputs.forEach((input) => {
            const field = input.getAttribute("data-field");
            input.name = `subtareas[${subtareaIndexModal - 1}][${field}]`;
            input.disabled = false; // <-- Habilita el campo
        });

        // Ocultar estado vacío si existe
        const emptyState = container.querySelector(".empty-subtareas");
        if (emptyState) {
            emptyState.style.display = "none";
        }

        // Agregar al contenedor
        container.appendChild(clone);

        // Actualizar contador
        actualizarContadorTareasCreadas();

        // Hacer scroll al nuevo elemento
        clone.scrollIntoView({ behavior: "smooth", block: "nearest" });

        // Focus en el primer input
        const primerInput = clone.querySelector('input[data-field="nombre"]');
        if (primerInput) {
            setTimeout(() => primerInput.focus(), 100);
        }

        console.log("Subtarea agregada correctamente"); // Debug
    }

    // Eliminar subtarea en modal
    document.addEventListener("click", (e) => {
        if (e.target.closest(".remove-subtarea-modal")) {
            const subtareaCard = e.target.closest(".subtarea-template");
            if (subtareaCard) {
                subtareaCard.remove();
                actualizarNumerosSubtareas();
                actualizarContadorTareasCreadas();

                // Mostrar estado vacío si no hay tareas
                const subtareas = DOM.subtareasContainerModal.querySelectorAll(
                    '.subtarea-template[style*="block"]'
                );
                if (subtareas.length === 0) {
                    const emptyState =
                        DOM.subtareasContainerModal.querySelector(
                            ".empty-subtareas"
                        );
                    if (emptyState) {
                        emptyState.style.display = "flex";
                    }
                }
            }
        }
    });

    function actualizarNumerosSubtareas() {
        const subtareas = DOM.subtareasContainerModal.querySelectorAll(
            '.subtarea-template[style*="block"]'
        );
        subtareas.forEach((subtarea, index) => {
            const numeroTarea = subtarea.querySelector(".numero-tarea");
            if (numeroTarea) {
                numeroTarea.textContent = index + 1;
            }

            // Actualizar nombres de campos
            const inputs = subtarea.querySelectorAll("[data-field]");
            inputs.forEach((input) => {
                const field = input.getAttribute("data-field");
                input.name = `subtareas[${index}][${field}]`;
            });
        });
    }

    function actualizarContadorTareasCreadas() {
        const subtareas = DOM.subtareasContainerModal.querySelectorAll(
            '.subtarea-template[style*="block"]'
        );
        tareasCreadasCount = subtareas.length;
        DOM.btnGuardarTareas.disabled = tareasCreadasCount === 0;
    }

    // Inicializar modal de crear tareas - CORREGIDO PARA MANTENER EL BOTÓN
    if (DOM.crearTareasModal) {
        DOM.crearTareasModal.addEventListener("show.bs.modal", () => {
            console.log("Modal de crear tareas abierto"); // Debug

            // Solo limpiar las tareas dinámicas, NO todo el contenedor
            const tareasExistentes =
                DOM.subtareasContainerModal.querySelectorAll(
                    '.subtarea-template[style*="block"]'
                );
            tareasExistentes.forEach((tarea) => tarea.remove());

            // Mostrar estado vacío
            const emptyState =
                DOM.subtareasContainerModal.querySelector(".empty-subtareas");
            if (emptyState) {
                emptyState.style.display = "flex";
            }

            // Reset contadores
            subtareaIndexModal = 0;
            tareasCreadasCount = 0;
            actualizarContadorTareasCreadas();

            // Ocultar formulario nueva etapa
            if (DOM.formNuevaEtapaModal) {
                DOM.formNuevaEtapaModal.style.display = "none";
                DOM.nombreNuevaEtapaModal.value = "";
            }
        });
    }

    // Validación del formulario crear tareas
    if (DOM.formCrearTareas) {
        DOM.formCrearTareas.addEventListener("submit", (e) => {
            const etapaSeleccionada = DOM.tareaGeneralIdModal.value;
            const subtareas = DOM.subtareasContainerModal.querySelectorAll(
                '.subtarea-template[style*="block"]'
            );

            if (!etapaSeleccionada) {
                e.preventDefault();
                mostrarNotificacion(
                    "Por favor selecciona una etapa",
                    "warning"
                );
                return;
            }

            if (subtareas.length === 0) {
                e.preventDefault();
                mostrarNotificacion(
                    "Por favor agrega al menos una tarea",
                    "warning"
                );
                return;
            }

            // Validar que todas las tareas tengan nombre
            let hayErrores = false;
            subtareas.forEach((subtarea) => {
                const nombreInput = subtarea.querySelector(
                    'input[data-field="nombre"]'
                );
                if (!nombreInput.value.trim()) {
                    nombreInput.style.borderColor = "#dc2626";
                    hayErrores = true;
                } else {
                    nombreInput.style.borderColor = "";
                }
            });

            if (hayErrores) {
                e.preventDefault();
                mostrarNotificacion(
                    "Por favor completa el nombre de todas las tareas",
                    "warning"
                );
                return;
            }
        });
    }
});
