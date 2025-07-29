document.addEventListener("DOMContentLoaded", function () {
    // ============================================================
    // SISTEMA DE PAGINACIÓN PERSONALIZADO
    // ============================================================

    class TablePagination {
        constructor(tableId, paginationId, infoId, itemsPerPage = 4) {
            this.table = document.getElementById(tableId);
            this.pagination = document.getElementById(paginationId);
            this.info = document.getElementById(infoId);
            this.itemsPerPage = itemsPerPage;
            this.currentPage = 1;
            this.totalItems = 0;
            this.totalPages = 0;
            this.rows = [];

            if (this.table && this.pagination && this.info) {
                this.init();
            }
        }

        init() {
            this.rows = Array.from(
                this.table.querySelectorAll("tbody tr")
            ).filter(
                (row) =>
                    !row.id ||
                    (!row.id.includes("empty") && row.style.display !== "none")
            );
            this.totalItems = this.rows.length;
            this.totalPages = Math.ceil(this.totalItems / this.itemsPerPage);

            if (this.totalItems > 0) {
                this.showPage(1);
                this.createPagination();
                this.updateInfo();
            } else {
                this.pagination.style.display = "none";
                this.info.style.display = "none";
            }
        }

        showPage(page) {
            this.currentPage = page;
            const start = (page - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;

            this.rows.forEach((row, index) => {
                if (index >= start && index < end) {
                    row.style.display = "";
                    // Animación de entrada
                    row.style.opacity = "0";
                    row.style.transform = "translateY(10px)";
                    setTimeout(() => {
                        row.style.transition =
                            "opacity 0.3s ease, transform 0.3s ease";
                        row.style.opacity = "1";
                        row.style.transform = "translateY(0)";
                    }, (index - start) * 50);
                } else {
                    row.style.display = "none";
                }
            });

            this.updatePagination();
            this.updateInfo();
        }

        createPagination() {
            if (this.totalPages <= 1) {
                this.pagination.style.display = "none";
                return;
            }

            this.pagination.innerHTML = "";
            this.pagination.style.display = "flex";

            // Botón Anterior
            const prevLi = document.createElement("li");
            prevLi.className = `page-item ${
                this.currentPage === 1 ? "disabled" : ""
            }`;
            prevLi.innerHTML = `
                <a class="page-link" href="#" data-page="${
                    this.currentPage - 1
                }">
                    <i class="fas fa-chevron-left"></i>
                </a>
            `;
            this.pagination.appendChild(prevLi);

            // Botones de páginas
            for (let i = 1; i <= this.totalPages; i++) {
                if (this.shouldShowPage(i)) {
                    const li = document.createElement("li");
                    li.className = `page-item ${
                        i === this.currentPage ? "active" : ""
                    }`;
                    li.innerHTML = `<a class="page-link" href="#" data-page="${i}">${i}</a>`;
                    this.pagination.appendChild(li);
                } else if (this.shouldShowEllipsis(i)) {
                    const li = document.createElement("li");
                    li.className = "page-item disabled";
                    li.innerHTML = '<span class="page-link">...</span>';
                    this.pagination.appendChild(li);
                }
            }

            // Botón Siguiente
            const nextLi = document.createElement("li");
            nextLi.className = `page-item ${
                this.currentPage === this.totalPages ? "disabled" : ""
            }`;
            nextLi.innerHTML = `
                <a class="page-link" href="#" data-page="${
                    this.currentPage + 1
                }">
                    <i class="fas fa-chevron-right"></i>
                </a>
            `;
            this.pagination.appendChild(nextLi);

            // Event listeners
            this.pagination.addEventListener("click", (e) => {
                e.preventDefault();
                const link = e.target.closest(".page-link");
                if (
                    link &&
                    !link.closest(".page-item").classList.contains("disabled")
                ) {
                    const page = parseInt(link.dataset.page);
                    if (
                        page &&
                        page !== this.currentPage &&
                        page >= 1 &&
                        page <= this.totalPages
                    ) {
                        this.showPage(page);
                    }
                }
            });
        }

        shouldShowPage(page) {
            if (this.totalPages <= 7) return true;
            if (page === 1 || page === this.totalPages) return true;
            if (Math.abs(page - this.currentPage) <= 2) return true;
            return false;
        }

        shouldShowEllipsis(page) {
            if (this.totalPages <= 7) return false;
            return (
                (page === 2 && this.currentPage > 4) ||
                (page === this.totalPages - 1 &&
                    this.currentPage < this.totalPages - 3)
            );
        }

        updatePagination() {
            if (!this.pagination) return;

            const items = this.pagination.querySelectorAll(".page-item");
            items.forEach((item) => {
                const link = item.querySelector(".page-link");
                if (link && link.dataset.page) {
                    const page = parseInt(link.dataset.page);
                    if (page === this.currentPage) {
                        item.classList.add("active");
                    } else {
                        item.classList.remove("active");
                    }
                }
            });

            // Actualizar estados de prev/next
            const firstItem = this.pagination.querySelector(
                ".page-item:first-child"
            );
            const lastItem = this.pagination.querySelector(
                ".page-item:last-child"
            );

            if (firstItem) {
                firstItem.classList.toggle("disabled", this.currentPage === 1);
                const firstLink = firstItem.querySelector(".page-link");
                if (firstLink) {
                    firstLink.dataset.page = this.currentPage - 1;
                }
            }
            if (lastItem) {
                lastItem.classList.toggle(
                    "disabled",
                    this.currentPage === this.totalPages
                );
                const lastLink = lastItem.querySelector(".page-link");
                if (lastLink) {
                    lastLink.dataset.page = this.currentPage + 1;
                }
            }
        }

        updateInfo() {
            if (!this.info) return;

            const start = Math.min(
                (this.currentPage - 1) * this.itemsPerPage + 1,
                this.totalItems
            );
            const end = Math.min(
                this.currentPage * this.itemsPerPage,
                this.totalItems
            );
            this.info.textContent = `Mostrando ${start}-${end} de ${this.totalItems} apiarios`;
        }

        refresh() {
            this.init();
        }

        // Método para obtener elementos visibles (para checkboxes)
        getVisibleRows() {
            return this.rows.filter((row, index) => {
                const start = (this.currentPage - 1) * this.itemsPerPage;
                const end = start + this.itemsPerPage;
                return index >= start && index < end;
            });
        }

        // Método para obtener TODOS los checkboxes (visibles y no visibles)
        getAllRows() {
            return this.rows;
        }
    }

    // Inicializar paginación para todas las tablas
    const fijosPagination = new TablePagination(
        "apiariosTable",
        "fijos-pagination",
        "fijos-pagination-info"
    );
    const basePagination = new TablePagination(
        "apiariosTableTrashumante",
        "base-pagination",
        "base-pagination-info"
    );
    const temporalesPagination = new TablePagination(
        "apiariosTemporalesTable",
        "temporales-pagination",
        "temporales-pagination-info"
    );

    // Paginación para modal de archivados
    let archivedPagination = null;

    // Inicializar paginación de archivados cuando se abra el modal
    const archivedModal = document.getElementById("archivedModal");
    if (archivedModal) {
        archivedModal.addEventListener("shown.bs.modal", function () {
            if (!archivedPagination) {
                archivedPagination = new TablePagination(
                    "apiariosArchivadosTable",
                    "archived-pagination",
                    "archived-pagination-info"
                );
            } else {
                archivedPagination.refresh();
            }
        });
    }

    // ============================================================
    // FUNCIONES AUXILIARES PARA GESTIÓN DE MODALES
    // ============================================================

    function cleanupModalBackdrops() {
        const backdrops = document.querySelectorAll(".modal-backdrop");
        const activeModals = document.querySelectorAll(".modal.show");

        if (activeModals.length === 0) {
            backdrops.forEach((backdrop) => backdrop.remove());
            document.body.classList.remove("modal-open");
            document.body.style.overflow = "";
            document.body.style.paddingRight = "";
        } else if (backdrops.length > 1) {
            for (let i = 1; i < backdrops.length; i++) {
                backdrops[i].remove();
            }
        }
    }

    function setupModalEvents() {
        const modals = document.querySelectorAll(".modal");

        modals.forEach((modal) => {
            modal.addEventListener("shown.bs.modal", function () {
                const backdrops = document.querySelectorAll(".modal-backdrop");
                if (backdrops.length > 1) {
                    for (let i = 1; i < backdrops.length; i++) {
                        backdrops[i].remove();
                    }
                }
            });

            modal.addEventListener("hidden.bs.modal", function () {
                setTimeout(() => {
                    const activeModals =
                        document.querySelectorAll(".modal.show");
                    if (activeModals.length === 0) {
                        cleanupModalBackdrops();
                    }
                }, 150);
            });
        });
    }

    setupModalEvents();
    cleanupModalBackdrops();

    // ============================================================
    // HANDLERS DE CHECKBOXES CON PAGINACIÓN CORREGIDOS
    // ============================================================
    function setupTrashumanteBaseHandlers(
        selectAllId,
        checkboxSelector,
        trasladarBtnId,
        pagination
    ) {
        const selectAll = document.getElementById(selectAllId);
        const trasladarBtn = document.getElementById(trasladarBtnId);

        function updateTrasladarButton() {
            // CORRECCIÓN: Usar Set para valores únicos como las otras funciones
            const allCheckboxes = document.querySelectorAll(
                ".select-checkbox-trashumante"
            );
            const selectedValues = new Set();

            allCheckboxes.forEach((chk) => {
                if (chk.checked) {
                    selectedValues.add(chk.value);
                }
            });

            const selectedCount = selectedValues.size;

            if (trasladarBtn) {
                trasladarBtn.disabled = selectedCount === 0;
                if (selectedCount > 0) {
                    trasladarBtn.innerHTML = `<i class="fas fa-arrow-right"></i> Trasladar Colmenas (${selectedCount})`;
                } else {
                    trasladarBtn.innerHTML = `<i class="fas fa-arrow-right"></i> Trasladar Colmenas`;
                }
            }
        }

        if (selectAll) {
            selectAll.addEventListener("change", function () {
                const visibleRows = pagination
                    ? pagination.getVisibleRows()
                    : Array.from(
                          document.querySelectorAll(
                              "#apiariosTableTrashumante tbody tr"
                          )
                      ).filter((row) => row.style.display !== "none");

                const visibleCheckboxes = visibleRows
                    .map((row) => row.querySelector(checkboxSelector))
                    .filter((chk) => chk);
                visibleCheckboxes.forEach(
                    (chk) => (chk.checked = selectAll.checked)
                );
                updateTrasladarButton();
            });
        }

        // Usar delegación de eventos para manejar checkboxes dinámicamente
        document.addEventListener("change", function (e) {
            if (
                e.target.matches(checkboxSelector) &&
                e.target.closest("#apiariosTableTrashumante")
            ) {
                const visibleRows = pagination
                    ? pagination.getVisibleRows()
                    : Array.from(
                          document.querySelectorAll(
                              "#apiariosTableTrashumante tbody tr"
                          )
                      ).filter((row) => row.style.display !== "none");

                const visibleCheckboxes = visibleRows
                    .map((row) => row.querySelector(checkboxSelector))
                    .filter((chk) => chk);
                const total = visibleCheckboxes.length;
                const totalChecked = visibleCheckboxes.filter(
                    (chk) => chk.checked
                ).length;

                if (selectAll) {
                    selectAll.checked = total === totalChecked && total > 0;
                }
                updateTrasladarButton();
            }
        });

        // NUEVO: Actualizar conteo cuando cambie de página
        if (pagination && pagination.pagination) {
            pagination.pagination.addEventListener("click", function () {
                setTimeout(updateTrasladarButton, 100);
            });
        }

        updateTrasladarButton();
    }

    function setupTemporalesHandlers(
        selectAllId,
        checkboxSelector,
        buttonId,
        pagination
    ) {
        const selectAll = document.getElementById(selectAllId);
        const retornarBtn = document.getElementById(buttonId);

        function updateRetornarButtonState() {
            // CORRECCIÓN: Usar Set para valores únicos como las otras funciones
            const allCheckboxes = document.querySelectorAll(
                ".select-checkbox-temporales"
            );
            const selectedValues = new Set();

            allCheckboxes.forEach((chk) => {
                if (chk.checked) {
                    selectedValues.add(chk.value);
                }
            });

            const checkedCount = selectedValues.size;

            if (!retornarBtn) return;

            retornarBtn.disabled = checkedCount === 0;
            if (checkedCount > 0) {
                retornarBtn.innerHTML = `<i class="fas fa-arrow-left"></i> Retornar Colmenas (${checkedCount})`;
            } else {
                retornarBtn.innerHTML = `<i class="fas fa-arrow-left"></i> Retornar Colmenas`;
            }
        }

        if (selectAll) {
            selectAll.addEventListener("change", function () {
                const visibleRows = pagination
                    ? pagination.getVisibleRows()
                    : Array.from(
                          document.querySelectorAll(
                              "#apiariosTemporalesTable tbody tr"
                          )
                      ).filter((row) => row.style.display !== "none");

                const visibleCheckboxes = visibleRows
                    .map((row) => row.querySelector(checkboxSelector))
                    .filter((chk) => chk);
                visibleCheckboxes.forEach(
                    (chk) => (chk.checked = selectAll.checked)
                );
                updateRetornarButtonState();
            });
        }

        // Usar delegación de eventos para manejar checkboxes dinámicamente
        document.addEventListener("change", function (e) {
            if (
                e.target.matches(checkboxSelector) &&
                e.target.closest("#apiariosTemporalesTable")
            ) {
                const visibleRows = pagination
                    ? pagination.getVisibleRows()
                    : Array.from(
                          document.querySelectorAll(
                              "#apiariosTemporalesTable tbody tr"
                          )
                      ).filter((row) => row.style.display !== "none");

                const visibleCheckboxes = visibleRows
                    .map((row) => row.querySelector(checkboxSelector))
                    .filter((chk) => chk);
                const total = visibleCheckboxes.length;
                const totalChecked = visibleCheckboxes.filter(
                    (chk) => chk.checked
                ).length;

                if (selectAll) {
                    selectAll.checked = total === totalChecked && total > 0;
                }
                updateRetornarButtonState();
            }
        });

        // NUEVO: Actualizar conteo cuando cambie de página
        if (pagination && pagination.pagination) {
            pagination.pagination.addEventListener("click", function () {
                setTimeout(updateRetornarButtonState, 100);
            });
        }

        updateRetornarButtonState();
    }

    // Configurar handlers para todas las tablas con sus respectivas paginaciones
    setupTrashumanteBaseHandlers(
        "selectAllTrashumante",
        ".select-checkbox-trashumante",
        "trasladarColmenasButton",
        basePagination
    );
    setupTemporalesHandlers(
        "selectAllTemporales",
        ".select-checkbox-temporales",
        "retornarColmenasButton",
        temporalesPagination
    );

    // ============================================================
    // RESTO DE FUNCIONALIDAD (SIN CAMBIOS)
    // ============================================================

    // Handler para botón trasladar
    const trasladarBtn = document.getElementById("trasladarColmenasButton");
    if (trasladarBtn) {
        trasladarBtn.addEventListener("click", function () {
            const seleccionados = Array.from(
                document.querySelectorAll(
                    ".select-checkbox-trashumante:checked"
                )
            ).map((chk) => chk.value);

            if (seleccionados.length === 0) return;

            const url = new URL(
                window.apiariosCreateTemporalUrl,
                window.location.origin
            );
            url.searchParams.set("tipo", "traslado");
            url.searchParams.set("apiarios", seleccionados.join(","));
            window.location.href = url.toString();
        });
    }

    // Handler para retornar colmenas con modal de confirmación
    const retornarBtnTemp = document.getElementById("retornarColmenasButton");
    const returnConfirmationModal = document.getElementById(
        "returnConfirmationModal"
    );
    const returnSelectedList = document.getElementById("returnSelectedList");
    const confirmReturnButton = document.getElementById("confirmReturnButton");

    if (retornarBtnTemp && returnConfirmationModal) {
        retornarBtnTemp.addEventListener("click", function () {
            const allCheckboxes = document.querySelectorAll(
                ".select-checkbox-temporales:checked"
            );

            // CORRECCIÓN: Obtener valores únicos para evitar duplicados en el modal
            const selectedValues = new Set();
            allCheckboxes.forEach((chk) => selectedValues.add(chk.value));
            const seleccionados = Array.from(selectedValues);

            if (seleccionados.length === 0) return;

            // Limpiar lista previa
            if (returnSelectedList) {
                returnSelectedList.innerHTML = "";

                seleccionados.forEach((id) => {
                    // Buscar SOLO UN checkbox con este value para obtener los datos
                    const chk = document.querySelector(
                        `.select-checkbox-temporales[value="${id}"]`
                    );
                    if (!chk) return;

                    const fila = chk.closest("tr");
                    const apicultorNombre =
                        fila
                            .querySelector("td:nth-child(2)")
                            ?.textContent?.trim() || "-";

                    const li = document.createElement("li");
                    li.className = "mb-2";
                    li.innerHTML = `<i class="fas fa-warehouse"></i> ${apicultorNombre}`;
                    returnSelectedList.appendChild(li);
                });
            }

            // Abrir modal con gestión segura
            const bsModal = new bootstrap.Modal(returnConfirmationModal);
            bsModal.show();
        });
    }

    if (confirmReturnButton) {
        confirmReturnButton.addEventListener("click", function () {
            const allCheckboxes = document.querySelectorAll(
                ".select-checkbox-temporales:checked"
            );

            // CORRECCIÓN: Obtener valores únicos
            const selectedValues = new Set();
            allCheckboxes.forEach((chk) => selectedValues.add(chk.value));
            const seleccionados = Array.from(selectedValues);

            if (seleccionados.length === 0) {
                const bsModal = bootstrap.Modal.getInstance(
                    returnConfirmationModal
                );
                if (bsModal) bsModal.hide();
                return;
            }

            // Deshabilitar botón para evitar clics múltiples
            confirmReturnButton.disabled = true;
            confirmReturnButton.innerHTML =
                '<i class="fas fa-spinner fa-spin"></i> Procesando...';

            const form = document.createElement("form");
            form.method = "POST";
            form.action = window.apiariosArchivarMultiplesUrl;
            form.style.display = "none";

            const csrfInput = document.createElement("input");
            csrfInput.type = "hidden";
            csrfInput.name = "_token";
            csrfInput.value = window.csrfToken;
            form.appendChild(csrfInput);

            seleccionados.forEach((id) => {
                const input = document.createElement("input");
                input.type = "hidden";
                input.name = "ids[]";
                input.value = id;
                form.appendChild(input);
            });

            document.body.appendChild(form);

            // Cerrar modal antes de enviar
            const bsModal = bootstrap.Modal.getInstance(
                returnConfirmationModal
            );
            if (bsModal) {
                bsModal.hide();
            }

            setTimeout(() => {
                cleanupModalBackdrops();
                form.submit();
            }, 300);
        });
    }

    // ============================================================
    // MODAL CREAR APIARIO TEMPORAL
    // ============================================================
    const createTemporalButton = document.getElementById(
        "createTemporalButton"
    );
    const createTemporalModal = document.getElementById("createTemporalModal");
    const selectedApiariosList = document.getElementById(
        "selectedApiariosList"
    );
    const createTrasladoButton = document.getElementById(
        "createTrasladoButton"
    );
    const createRetornoButton = document.getElementById("createRetornoButton");

    if (createTemporalButton && createTemporalModal) {
        createTemporalButton.addEventListener("click", function () {
            const selectedCheckboxes = document.querySelectorAll(
                ".select-checkbox-trashumante:checked"
            );
            if (selectedCheckboxes.length === 0) return;

            if (selectedApiariosList) {
                selectedApiariosList.innerHTML = "";

                selectedCheckboxes.forEach((chk) => {
                    const row = chk.closest("tr");
                    const apiarioName =
                        row.querySelector(".apiario-id")?.textContent || "-";
                    const numColmenas =
                        row.querySelector(".counter")?.textContent || "0";

                    const listItem = document.createElement("li");
                    listItem.className = "mb-2";
                    listItem.innerHTML = `
                        <div class="d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-warehouse"></i> ${apiarioName}</span>
                            <span class="badge bg-primary">${numColmenas} colmenas</span>
                        </div>
                    `;
                    selectedApiariosList.appendChild(listItem);
                });
            }

            const bsModal = new bootstrap.Modal(createTemporalModal);
            bsModal.show();
        });
    }

    if (createTrasladoButton) {
        createTrasladoButton.addEventListener("click", function () {
            const seleccionados = Array.from(
                document.querySelectorAll(
                    ".select-checkbox-trashumante:checked"
                )
            ).map((chk) => chk.value);

            if (seleccionados.length === 0) return;

            const url = new URL(
                window.apiariosCreateTemporalUrl,
                window.location.origin
            );
            url.searchParams.set("tipo", "traslado");
            url.searchParams.set("apiarios", seleccionados.join(","));
            window.location.href = url.toString();
        });
    }

    if (createRetornoButton) {
        createRetornoButton.addEventListener("click", function () {
            const seleccionados = Array.from(
                document.querySelectorAll(
                    ".select-checkbox-trashumante:checked"
                )
            ).map((chk) => chk.value);

            if (seleccionados.length === 0) return;

            const url = new URL(
                window.apiariosCreateTemporalUrl,
                window.location.origin
            );
            url.searchParams.set("tipo", "retorno");
            url.searchParams.set("apiarios", seleccionados.join(","));
            window.location.href = url.toString();
        });
    }

    // ============================================================
    // MANEJO ESPECÍFICO DE MODALES DE ELIMINACIÓN INDIVIDUAL
    // ============================================================
    function setupIndividualDeleteModals() {
        document.querySelectorAll('[id^="deleteModal"]').forEach((modal) => {
            const form = modal.querySelector("form");
            const submitButton = modal.querySelector(".modal-btn-danger");

            if (submitButton && form) {
                submitButton.addEventListener("click", function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    // Deshabilitar el botón temporalmente
                    submitButton.disabled = true;
                    submitButton.innerHTML =
                        '<i class="fas fa-spinner fa-spin"></i> Eliminando...';

                    // Cerrar el modal primero
                    const modalInstance = bootstrap.Modal.getInstance(modal);
                    if (modalInstance) {
                        modalInstance.hide();
                    }

                    // Limpiar backdrops y enviar formulario después de un delay
                    setTimeout(() => {
                        cleanupModalBackdrops();
                        form.submit();
                    }, 300);
                });
            }
        });

        // Manejar modales de temporales específicamente
        document
            .querySelectorAll('[id^="deleteModalTemporal"]')
            .forEach((modal) => {
                const form = modal.querySelector("form");
                const submitButton = modal.querySelector(".modal-btn-danger");

                if (submitButton && form) {
                    submitButton.addEventListener("click", function (e) {
                        e.preventDefault();
                        e.stopPropagation();

                        // Deshabilitar el botón temporalmente
                        submitButton.disabled = true;
                        submitButton.innerHTML =
                            '<i class="fas fa-spinner fa-spin"></i> Eliminando...';

                        // Cerrar el modal primero
                        const modalInstance =
                            bootstrap.Modal.getInstance(modal);
                        if (modalInstance) {
                            modalInstance.hide();
                        }

                        // Limpiar backdrops y enviar formulario después de un delay
                        setTimeout(() => {
                            cleanupModalBackdrops();
                            form.submit();
                        }, 300);
                    });
                }
            });
    }

    // Configurar modales de eliminación individual
    setupIndividualDeleteModals();

    // ============================================================
    // TOOLTIPS DINÁMICOS
    // ============================================================
    function setupDynamicTooltips() {
        const tooltipElements = document.querySelectorAll("[data-tooltip]");

        tooltipElements.forEach((element) => {
            element.addEventListener("mouseenter", function (e) {
                const rect = e.target.getBoundingClientRect();
                const viewportWidth = window.innerWidth;
                const viewportHeight = window.innerHeight;

                // Verificar si hay headers de tabla sticky arriba
                const tableHeaders = document.querySelectorAll(
                    ".apiarios-table thead"
                );
                let headerBottom = 0;

                tableHeaders.forEach((header) => {
                    const headerRect = header.getBoundingClientRect();
                    if (headerRect.bottom > headerBottom) {
                        headerBottom = headerRect.bottom;
                    }
                });

                // Calcular espacio disponible arriba y abajo
                const spaceAbove = rect.top - headerBottom;
                const spaceBelow = viewportHeight - rect.bottom;

                // Determinar la mejor posición
                let position = "top"; // Por defecto arriba

                // Si no hay suficiente espacio arriba (menos de 60px), usar abajo
                if (spaceAbove < 60 && spaceBelow > 60) {
                    position = "bottom";
                }

                // Si está muy cerca de los bordes laterales, usar left/right
                if (rect.left < 100 && rect.right < viewportWidth - 100) {
                    position = "right";
                } else if (
                    rect.right > viewportWidth - 100 &&
                    rect.left > 100
                ) {
                    position = "left";
                }

                // Aplicar la posición calculada
                e.target.setAttribute("data-tooltip-position", position);

                // Si el tooltip se queda debajo del header, forzar posición bottom
                if (position === "top" && rect.top - 45 < headerBottom) {
                    e.target.setAttribute("data-tooltip-position", "bottom");
                }
            });
        });
    }

    // Inicializar tooltips
    setupDynamicTooltips();

    // Reinicializar tooltips cuando cambie el tamaño de ventana
    let resizeTimeout;
    window.addEventListener("resize", function () {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            setupDynamicTooltips();
        }, 250);
    });

    // Reinicializar tooltips cuando se cambie de pestaña
    document.querySelectorAll('[data-bs-toggle="tab"]').forEach((tab) => {
        tab.addEventListener("shown.bs.tab", function () {
            setTimeout(() => {
                setupDynamicTooltips();
            }, 100);
        });
    });

    // ============================================================
    // LIMPIEZA GLOBAL Y EVENTOS ADICIONALES
    // ============================================================

    // Interceptar clics en backdrops para cerrar modales correctamente
    document.addEventListener("click", function (e) {
        if (e.target.classList.contains("modal-backdrop")) {
            const activeModal = document.querySelector(".modal.show");
            if (activeModal) {
                const modalInstance = bootstrap.Modal.getInstance(activeModal);
                if (modalInstance) {
                    modalInstance.hide();
                }
            }
            setTimeout(cleanupModalBackdrops, 200);
        }
    });

    // Limpiar backdrops cuando se cambia de pestaña
    document.querySelectorAll('[data-bs-toggle="tab"]').forEach((tab) => {
        tab.addEventListener("shown.bs.tab", function () {
            cleanupModalBackdrops();
        });
    });

    // Observer para detectar cambios en el DOM y limpiar backdrops órfanos
    const observer = new MutationObserver(function (mutations) {
        mutations.forEach(function (mutation) {
            if (mutation.type === "childList") {
                mutation.addedNodes.forEach(function (node) {
                    if (
                        node.nodeType === 1 &&
                        node.classList &&
                        node.classList.contains("modal-backdrop")
                    ) {
                        const existingBackdrops =
                            document.querySelectorAll(".modal-backdrop");
                        if (existingBackdrops.length > 1) {
                            node.remove();
                        }
                    }
                });
            }
        });
    });

    observer.observe(document.body, {
        childList: true,
        subtree: false,
    });

    // Limpieza final al cargar
    setTimeout(cleanupModalBackdrops, 500);

    // ============================================================
    // SISTEMA DE VISTA DE TARJETAS INDEPENDIENTE
    // ============================================================

    class IndependentViewToggle {
        constructor() {
            this.currentView = "table"; // 'table' o 'cards'
            this.init();
        }

        init() {
            this.setupToggleEvents();
            this.createViewContainers();
            this.populateCards();
            this.setupCheckboxSynchronization();
        }

        createViewContainers() {
            // Crear contenedores de tarjetas para cada sección
            this.createCardContainer("fijos");
            this.createCardContainer("base");
            this.createCardContainer("temporales");
        }

        createCardContainer(section) {
            let sectionElement;

            if (section === "fijos") {
                sectionElement = document.getElementById("fijos");
            } else if (section === "base") {
                sectionElement = document.getElementById("trashumantes");
                if (sectionElement) {
                    sectionElement = sectionElement.querySelector(
                        ".subsection:first-child"
                    );
                }
            } else if (section === "temporales") {
                sectionElement = document.getElementById("trashumantes");
                if (sectionElement) {
                    sectionElement = sectionElement.querySelector(
                        ".subsection:last-child"
                    );
                }
            }

            if (!sectionElement) return;

            // Verificar si ya existe el contenedor
            if (sectionElement.querySelector(`#${section}-cards-view`)) return;

            const cardsContainer = document.createElement("div");
            cardsContainer.id = `${section}-cards-view`;
            cardsContainer.className = "cards-view-container";
            cardsContainer.innerHTML = `<div class="cards-grid" id="${section}-cards-grid"></div>`;

            // Insertar después del data-container existente
            const dataContainer =
                sectionElement.querySelector(".data-container");
            if (dataContainer) {
                dataContainer.parentNode.insertBefore(
                    cardsContainer,
                    dataContainer.nextSibling
                );
            } else {
                sectionElement.appendChild(cardsContainer);
            }
        }

        setupToggleEvents() {
            // Crear switches en cada toolbar-right
            document
                .querySelectorAll(".toolbar-right")
                .forEach((toolbar, index) => {
                    if (toolbar.querySelector(".view-toggle-container")) return; // Ya existe

                    const toggleContainer = document.createElement("div");
                    toggleContainer.className = "view-toggle-container";

                    // Determinar la sección basada en el contexto
                    let sectionName = "fijos";
                    const parentSection = toolbar.closest(
                        "#fijos, #trashumantes"
                    );
                    if (parentSection) {
                        if (parentSection.id === "trashumantes") {
                            const subsection = toolbar.closest(".subsection");
                            const isFirst =
                                subsection ===
                                parentSection.querySelector(
                                    ".subsection:first-child"
                                );
                            sectionName = isFirst ? "base" : "temporales";
                        }
                    }

                    toggleContainer.innerHTML = `
                    <span class="view-toggle-label">Vista:</span>
                    <div class="view-switch" data-section="${sectionName}">
                        <div class="view-switch-slider"></div>
                        <div class="view-switch-option left">
                            <i class="fas fa-table"></i>
                        </div>
                        <div class="view-switch-option right">
                            <i class="fas fa-th"></i>
                        </div>
                    </div>
                `;

                    // Insertar al principio del toolbar-right
                    toolbar.insertBefore(toggleContainer, toolbar.firstChild);

                    // Agregar event listener
                    const viewSwitch =
                        toggleContainer.querySelector(".view-switch");
                    viewSwitch.addEventListener("click", (e) => {
                        e.preventDefault();
                        this.toggleView(viewSwitch.dataset.section);
                    });
                });
        }

        toggleView(section) {
            const viewSwitch = document.querySelector(
                `.view-switch[data-section="${section}"]`
            );
            if (!viewSwitch) return;

            const isCardsActive = viewSwitch.classList.contains("cards-active");

            if (isCardsActive) {
                // Cambiar a tabla
                viewSwitch.classList.remove("cards-active");
                this.showTableView(section);
            } else {
                // Cambiar a tarjetas
                viewSwitch.classList.add("cards-active");
                this.showCardsView(section);
            }
        }

        showTableView(section) {
            const tableContainer = this.getTableContainer(section);
            const cardsContainer = document.getElementById(
                `${section}-cards-view`
            );
            const paginationContainer = this.getPaginationContainer(section);

            if (tableContainer) tableContainer.style.display = "block";
            if (cardsContainer) cardsContainer.classList.remove("active");
            if (paginationContainer) paginationContainer.style.display = "flex";
        }

        showCardsView(section) {
            const tableContainer = this.getTableContainer(section);
            const cardsContainer = document.getElementById(
                `${section}-cards-view`
            );
            const paginationContainer = this.getPaginationContainer(section);

            if (tableContainer) tableContainer.style.display = "none";
            if (cardsContainer) {
                cardsContainer.classList.add("active");
                // Repoblar las tarjetas cada vez que se muestra
                this.populateCardsForSection(section);
            }
            if (paginationContainer) paginationContainer.style.display = "none";
        }

        getTableContainer(section) {
            const tableId =
                section === "fijos"
                    ? "apiariosTable"
                    : section === "base"
                    ? "apiariosTableTrashumante"
                    : "apiariosTemporalesTable";
            const table = document.getElementById(tableId);
            return table ? table.closest(".data-container") : null;
        }

        getPaginationContainer(section) {
            const paginationId =
                section === "fijos"
                    ? "fijos-pagination"
                    : section === "base"
                    ? "base-pagination"
                    : "temporales-pagination";
            const pagination = document.getElementById(paginationId);
            return pagination
                ? pagination.closest(".pagination-container")
                : null;
        }

        populateCards() {
            this.populateCardsForSection("fijos");
            this.populateCardsForSection("base");
            this.populateCardsForSection("temporales");
        }

        populateCardsForSection(section) {
            const cardsGrid = document.getElementById(`${section}-cards-grid`);
            if (!cardsGrid) return;

            const tableId =
                section === "fijos"
                    ? "apiariosTable"
                    : section === "base"
                    ? "apiariosTableTrashumante"
                    : "apiariosTemporalesTable";

            const table = document.getElementById(tableId);
            if (!table) return;

            const rows = table.querySelectorAll("tbody tr");
            cardsGrid.innerHTML = "";

            rows.forEach((row, index) => {
                if (
                    row.id &&
                    (row.id.includes("empty") || row.style.display === "none")
                )
                    return;

                const card = this.createCardFromRow(row, section);
                if (card) {
                    cardsGrid.appendChild(card);
                }
            });
        }

        createCardFromRow(row, section) {
            const cells = row.querySelectorAll("td");
            if (cells.length === 0) return null;

            const card = document.createElement("div");
            card.className = `apiario-card-modern ${
                section === "base"
                    ? "base-card"
                    : section === "temporales"
                    ? "temporal-card"
                    : ""
            }`;

            if (section === "fijos") {
                card.innerHTML = this.createFijosCard(cells);
            } else if (section === "base") {
                card.innerHTML = this.createBaseCard(cells);
            } else if (section === "temporales") {
                card.innerHTML = this.createTemporalesCard(cells);
            }

            return card;
        }

        createFijosCard(cells) {
            const nombre = cells[0]?.textContent?.trim() || "-";
            const temporada = cells[1]?.textContent?.trim() || "-";
            const registroSag = cells[2]?.textContent?.trim() || "-";
            const colmenas = cells[3]?.textContent?.trim() || "0";
            const tipo = cells[4]?.textContent?.trim() || "-";
            const manejo = cells[5]?.textContent?.trim() || "-";
            const objetivo = cells[6]?.textContent?.trim() || "-";
            const ubicacion = cells[7]?.textContent?.trim() || "-";
            const coordenadas = cells[8]?.textContent?.trim() || "-";

            const imgElement = cells[9]?.querySelector("img");
            const actionLinks = cells[10]?.querySelectorAll("a, button");

            return `
                <div class="card-header-modern">
                    <h3 class="card-title-modern">
                        <i class="fas fa-warehouse"></i>
                        ${nombre}
                    </h3>
                    <div class="card-subtitle-modern">
                        <i class="fas fa-calendar"></i>
                        Temporada ${temporada}
                    </div>
                </div>
                
                <div class="card-body-modern">
                    <div class="card-info-grid-modern">
                        <div class="card-info-item-modern">
                            <span class="card-info-label-modern">
                                <i class="fas fa-id-card"></i>
                                Registro SAG
                            </span>
                            <span class="card-info-value-modern">${registroSag}</span>
                        </div>
                        <div class="card-info-item-modern">
                            <span class="card-info-label-modern">
                                <i class="fas fa-cubes"></i>
                                Colmenas
                            </span>
                            <span class="card-info-value-modern">${colmenas}</span>
                        </div>
                    </div>

                    <div class="card-badges-modern">
                        <div class="card-badge-item-modern">
                            <span class="card-badge-label-modern">Tipo:</span>
                            <span class="card-badge-modern secondary">${tipo}</span>
                        </div>
                        <div class="card-badge-item-modern">
                            <span class="card-badge-label-modern">Manejo:</span>
                            <span class="card-badge-modern info">${manejo}</span>
                        </div>
                        <div class="card-badge-item-modern">
                            <span class="card-badge-label-modern">Objetivo:</span>
                            <span class="card-badge-modern warning">${objetivo}</span>
                        </div>
                    </div>

                    <div class="card-location-modern">
                        <i class="fas fa-map-marker-alt"></i>
                        <span class="card-location-text-modern">${ubicacion}</span>
                        <span class="card-coordinates-modern">${coordenadas}</span>
                    </div>

                    ${
                        imgElement
                            ? `<img src="${imgElement.src}" alt="${
                                  imgElement.alt
                              }" class="card-image-modern" 
                              onclick="document.querySelector('[data-bs-target=\\'#imageModal${this.extractIdFromActions(
                                  actionLinks
                              )}\\']').click()">`
                            : `<div class="card-no-image-modern">
                            <i class="fas fa-image"></i>
                            <span>Sin imagen</span>
                        </div>`
                    }

                    <div class="card-actions-modern">
                        <div class="card-actions-left-modern">
                            <span class="card-badge-modern primary">Fijo</span>
                        </div>
                        <div class="card-actions-right-modern">
                            ${this.createActionButtons(actionLinks)}
                        </div>
                    </div>
                </div>
            `;
        }

        createBaseCard(cells) {
            const checkbox = cells[0]?.querySelector('input[type="checkbox"]');
            const nombre = cells[1]?.textContent?.trim() || "-";
            const temporada = cells[2]?.textContent?.trim() || "-";
            const registroSag = cells[3]?.textContent?.trim() || "-";
            const colmenas = cells[4]?.textContent?.trim() || "0";
            const tipo = cells[5]?.textContent?.trim() || "-";
            const manejo = cells[6]?.textContent?.trim() || "-";
            const objetivo = cells[7]?.textContent?.trim() || "-";
            const ubicacion = cells[8]?.textContent?.trim() || "-";
            const coordenadas = cells[9]?.textContent?.trim() || "-";

            const imgElement = cells[10]?.querySelector("img");
            const actionLinks = cells[11]?.querySelectorAll("a");

            return `
                <div class="card-header-modern">
                    <h3 class="card-title-modern">
                        <i class="fas fa-home"></i>
                        ${nombre}
                    </h3>
                    <div class="card-subtitle-modern">
                        <i class="fas fa-calendar"></i>
                        Temporada ${temporada}
                    </div>
                    <div class="card-checkbox-modern">
                        <label class="checkbox-wrapper">
                            <input type="checkbox" class="select-checkbox-trashumante card-checkbox-input" 
                                   value="${checkbox?.value || ""}" 
                                   ${checkbox?.checked ? "checked" : ""}
                                   data-row-id="${checkbox?.value || ""}">
                            <span class="checkmark"></span>
                        </label>
                    </div>
                </div>
                
                <div class="card-body-modern">
                    <div class="card-info-grid-modern">
                        <div class="card-info-item-modern">
                            <span class="card-info-label-modern">
                                <i class="fas fa-id-card"></i>
                                Registro SAG
                            </span>
                            <span class="card-info-value-modern">${registroSag}</span>
                        </div>
                        <div class="card-info-item-modern">
                            <span class="card-info-label-modern">
                                <i class="fas fa-cubes"></i>
                                Colmenas
                            </span>
                            <span class="card-info-value-modern">${colmenas}</span>
                        </div>
                    </div>

                    <div class="card-badges-modern">
                        <div class="card-badge-item-modern">
                            <span class="card-badge-label-modern">Tipo:</span>
                            <span class="card-badge-modern secondary">${tipo}</span>
                        </div>
                        <div class="card-badge-item-modern">
                            <span class="card-badge-label-modern">Manejo:</span>
                            <span class="card-badge-modern info">${manejo}</span>
                        </div>
                        <div class="card-badge-item-modern">
                            <span class="card-badge-label-modern">Objetivo:</span>
                            <span class="card-badge-modern warning">${objetivo}</span>
                        </div>
                    </div>

                    <div class="card-location-modern">
                        <i class="fas fa-map-marker-alt"></i>
                        <span class="card-location-text-modern">${ubicacion}</span>
                        <span class="card-coordinates-modern">${coordenadas}</span>
                    </div>

                    ${
                        imgElement
                            ? `<img src="${imgElement.src}" alt="${
                                  imgElement.alt
                              }" class="card-image-modern" 
                              onclick="document.querySelector('[data-bs-target=\\'#imageModal${this.extractIdFromActions(
                                  actionLinks
                              )}\\']').click()">`
                            : `<div class="card-no-image-modern">
                            <i class="fas fa-image"></i>
                            <span>Sin imagen</span>
                        </div>`
                    }

                    <div class="card-actions-modern">
                        <div class="card-actions-left-modern">
                            <span class="card-badge-modern info">Base</span>
                        </div>
                        <div class="card-actions-right-modern">
                            ${this.createActionButtons(actionLinks)}
                        </div>
                    </div>
                </div>
            `;
        }

        createTemporalesCard(cells) {
            const checkbox = cells[0]?.querySelector('input[type="checkbox"]');
            const nombre = cells[1]?.textContent?.trim() || "-";
            const colmenas = cells[2]?.textContent?.trim() || "0";
            const regionOrigen = cells[3]?.textContent?.trim() || "-";
            const comunaOrigen = cells[4]?.textContent?.trim() || "-";
            const regionDestino = cells[5]?.textContent?.trim() || "-";
            const comunaDestino = cells[6]?.textContent?.trim() || "-";
            const fechaMovimiento = cells[7]?.textContent?.trim() || "-";
            const motivo = cells[8]?.textContent?.trim() || "-";
            const cultivo = cells[9]?.textContent?.trim() || "-";
            const actionLinks = cells[10]?.querySelectorAll("a");

            return `
                <div class="card-header-modern">
                    <h3 class="card-title-modern">
                        <i class="fas fa-route"></i>
                        ${nombre}
                    </h3>
                    <div class="card-subtitle-modern">
                        <i class="fas fa-calendar"></i>
                        Movimiento ${fechaMovimiento}
                    </div>
                    <div class="card-checkbox-modern">
                        <label class="checkbox-wrapper">
                            <input type="checkbox" class="select-checkbox-temporales card-checkbox-input" 
                                   value="${checkbox?.value || ""}" 
                                   ${checkbox?.checked ? "checked" : ""}
                                   data-row-id="${checkbox?.value || ""}">
                            <span class="checkmark"></span>
                        </label>
                    </div>
                </div>
                
                <div class="card-body-modern">
                    <div class="temporal-route-modern">
                        <i class="fas fa-route"></i>
                        <span class="temporal-route-text-modern">
                            ${comunaOrigen}, ${regionOrigen} → ${comunaDestino}, ${regionDestino}
                        </span>
                    </div>

                    <div class="card-info-grid-modern">
                        <div class="card-info-item-modern">
                            <span class="card-info-label-modern">
                                <i class="fas fa-cubes"></i>
                                Colmenas
                            </span>
                            <span class="card-info-value-modern">${colmenas}</span>
                        </div>
                        <div class="card-info-item-modern">
                            <span class="card-info-label-modern">
                                <i class="fas fa-seedling"></i>
                                Cultivo
                            </span>
                            <span class="card-info-value-modern">${
                                cultivo !== "—" ? cultivo : "-"
                            }</span>
                        </div>
                    </div>

                    <div class="card-badges-modern">
                        <div class="card-badge-item-modern">
                            <span class="card-badge-label-modern">Motivo:</span>
                            <span class="card-badge-modern info">${motivo}</span>
                        </div>
                        <div class="card-badge-item-modern">
                            <span class="card-badge-label-modern">Estado:</span>
                            <span class="card-badge-modern warning">Temporal</span>
                        </div>
                    </div>

                    <div class="card-actions-modern">
                        <div class="card-actions-left-modern">
                            <span class="card-badge-modern warning">En trashumancia</span>
                        </div>
                        <div class="card-actions-right-modern">
                            ${this.createActionButtons(actionLinks)}
                        </div>
                    </div>
                </div>
            `;
        }

        createActionButtons(actionElements) {
            if (!actionElements) return "";

            let buttons = "";
            actionElements.forEach((action) => {
                const href = action.getAttribute("href");
                const title = action.getAttribute("title");
                const icon =
                    action.querySelector("i")?.className || "fas fa-cog";

                let buttonClass = "card-action-btn-modern";
                if (title?.toLowerCase().includes("editar"))
                    buttonClass += " edit";
                else if (
                    title?.toLowerCase().includes("ver") ||
                    title?.toLowerCase().includes("colmenas")
                )
                    buttonClass += " view";
                else if (
                    title?.toLowerCase().includes("descargar") ||
                    title?.toLowerCase().includes("pdf")
                )
                    buttonClass += " download";
                else if (title?.toLowerCase().includes("eliminar"))
                    buttonClass += " delete";

                // --- MODIFICACIÓN: botón de opciones de movimiento ---
                if (
                    icon.includes("fa-cogs") ||
                    title?.toLowerCase().includes("opciones")
                ) {
                    const dataId = action.getAttribute("data-id") || "";
                    buttonClass += " action-icon cogs";
                    buttons += `<a href="#" class="${buttonClass}" title="${title}" data-bs-toggle="modal" data-bs-target="#modalOpcionesApiario" data-id="${dataId}"><i class="${icon}"></i></a>`;
                    return;
                }

                if (href) {
                    buttons += `<a href="${href}" class="${buttonClass}" title="${title}"><i class="${icon}"></i></a>`;
                } else if (action.tagName === "BUTTON") {
                    const dataTarget = action.getAttribute("data-bs-target");
                    buttons += `<button class="${buttonClass}" title="${title}" ${
                        dataTarget
                            ? `data-bs-toggle="modal" data-bs-target="${dataTarget}"`
                            : ""
                    }><i class="${icon}"></i></button>`;
                }
            });

            return buttons;
        }

        extractIdFromActions(actionElements) {
            if (!actionElements) return "";

            for (let action of actionElements) {
                const href = action.getAttribute("href");
                if (href && href.includes("/")) {
                    const parts = href.split("/");
                    const id = parts[parts.length - 1];
                    if (!isNaN(id)) return id;
                }
            }
            return "";
        }

        // NUEVO: Configurar sincronización bidireccional de checkboxes
        setupCheckboxSynchronization() {
            // Event listener para sincronizar checkboxes entre tabla y tarjetas
            document.addEventListener("change", (e) => {
                if (e.target.matches(".select-checkbox-trashumante")) {
                    this.syncCheckboxesBidirectional("base", e.target);
                    this.updateButtonCounts("base");
                } else if (e.target.matches(".select-checkbox-temporales")) {
                    this.syncCheckboxesBidirectional("temporales", e.target);
                    this.updateButtonCounts("temporales");
                }
            });

            // Select All para tablas
            document.addEventListener("change", (e) => {
                if (e.target.id === "selectAllTrashumante") {
                    this.syncSelectAll("base", e.target.checked);
                } else if (e.target.id === "selectAllTemporales") {
                    this.syncSelectAll("temporales", e.target.checked);
                }
            });
        }

        syncCheckboxesBidirectional(section, changedCheckbox) {
            const checkboxClass =
                section === "base"
                    ? ".select-checkbox-trashumante"
                    : ".select-checkbox-temporales";
            const allCheckboxes = document.querySelectorAll(checkboxClass);
            const targetValue = changedCheckbox.value;
            const isChecked = changedCheckbox.checked;

            // Sincronizar todos los checkboxes con el mismo valor
            allCheckboxes.forEach((checkbox) => {
                if (
                    checkbox.value === targetValue &&
                    checkbox !== changedCheckbox
                ) {
                    checkbox.checked = isChecked;
                }
            });
        }

        syncSelectAll(section, isChecked) {
            const checkboxClass =
                section === "base"
                    ? ".select-checkbox-trashumante"
                    : ".select-checkbox-temporales";
            const allCheckboxes = document.querySelectorAll(checkboxClass);

            allCheckboxes.forEach((checkbox) => {
                checkbox.checked = isChecked;
            });

            this.updateButtonCounts(section);
        }

        updateButtonCounts(section) {
            if (section === "base") {
                updateTrasladarButtonForBothViews();
            } else if (section === "temporales") {
                updateRetornarButtonForBothViews();
            }
        }
    }

    // Inicializar el sistema independiente de vista de tarjetas
    const independentViewToggle = new IndependentViewToggle();

    // Funciones de actualización de botones para ambas vistas (CORREGIDAS)
    function updateTrasladarButtonForBothViews() {
        const allCheckboxes = document.querySelectorAll(
            ".select-checkbox-trashumante"
        );

        // CORRECCIÓN: Obtener valores únicos seleccionados
        const selectedValues = new Set();
        allCheckboxes.forEach((chk) => {
            if (chk.checked) {
                selectedValues.add(chk.value);
            }
        });

        const selectedCount = selectedValues.size;

        const trasladarBtn = document.getElementById("trasladarColmenasButton");
        if (trasladarBtn) {
            trasladarBtn.disabled = selectedCount === 0;
            if (selectedCount > 0) {
                trasladarBtn.innerHTML = `<i class="fas fa-arrow-right"></i> Trasladar Colmenas (${selectedCount})`;
            } else {
                trasladarBtn.innerHTML = `<i class="fas fa-arrow-right"></i> Trasladar`;
            }
        }
    }

    function updateRetornarButtonForBothViews() {
        const allCheckboxes = document.querySelectorAll(
            ".select-checkbox-temporales"
        );

        // CORRECCIÓN: Obtener valores únicos seleccionados
        const selectedValues = new Set();
        allCheckboxes.forEach((chk) => {
            if (chk.checked) {
                selectedValues.add(chk.value);
            }
        });

        const selectedCount = selectedValues.size;

        const retornarBtn = document.getElementById("retornarColmenasButton");
        if (retornarBtn) {
            retornarBtn.disabled = selectedCount === 0;
            if (selectedCount > 0) {
                retornarBtn.innerHTML = `<i class="fas fa-arrow-left"></i> Retornar Colmenas (${selectedCount})`;
            } else {
                retornarBtn.innerHTML = `<i class="fas fa-arrow-left"></i> Retornar`;
            }
        }
    }

    // Actualizar tarjetas cuando cambie la paginación
    document.addEventListener("click", function (e) {
        if (e.target.closest(".pagination-controls")) {
            setTimeout(() => {
                independentViewToggle.populateCards();
            }, 100);
        }
    });

    window.openDetalleMovimientoModal = function (apiarioId) {
        const modal = document.getElementById("detalleMovimientoModal");
        if (modal) {
            modal.classList.remove("d-none");
            const contentDiv = document.getElementById(
                "detalleMovimientoContent"
            );
            if (contentDiv) {
                contentDiv.innerHTML = `
                <div class="loading-spinner text-center">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                    <p>Cargando detalles del movimiento...</p>
                </div>
            `;
                fetch(`/apiarios-temporales/${apiarioId}/detalle-movimiento`)
                    .then((response) => response.text())
                    .then((html) => {
                        contentDiv.innerHTML = html;
                    })
                    .catch((error) => {
                        contentDiv.innerHTML = `<div class="alert alert-danger">Error al cargar detalle.</div>`;
                    });
            }
        }
    };

    window.closeDetalleMovimiento = function () {
        const modal = document.getElementById("detalleMovimientoModal");
        if (modal) {
            modal.classList.add("d-none");
        }
    };
});

// ============================================================
// GUARDADO Y RESTAURACIÓN DE SCROLL Y PESTAÑA ACTIVA POR MÓDULO
// ============================================================

(function () {
    // Devuelve un ID único para la pestaña/módulo actual
    function getScrollKey(tabId) {
        const module = window.location.pathname || "apiarios";
        return `scrollPos_${module}_${tabId}`;
    }

    // Guarda el scroll actual
    function saveScrollPosition(tabId) {
        const key = getScrollKey(tabId);
        localStorage.setItem(key, window.scrollY || window.pageYOffset);
    }

    // Restaura el scroll guardado
    function restoreScrollPosition(tabId) {
        const key = getScrollKey(tabId);
        const pos = parseInt(localStorage.getItem(key), 10);
        if (!isNaN(pos)) {
            setTimeout(() => window.scrollTo(0, pos), 10);
        }
    }

    // Guarda la pestaña activa
    function saveActiveTab(tabId) {
        const module = window.location.pathname || "apiarios";
        localStorage.setItem(`activeTab_${module}`, tabId);
    }

    // Restaura la pestaña activa
    function restoreActiveTab() {
        const module = window.location.pathname || "apiarios";
        const savedTabId = localStorage.getItem(`activeTab_${module}`);
        if (savedTabId) {
            const tabBtn = document.querySelector(
                `[data-bs-target="#${savedTabId}"]`
            );
            if (tabBtn) {
                // Si ya está activa, solo restaurar scroll
                if (!tabBtn.classList.contains("active")) {
                    tabBtn.click();
                } else {
                    restoreScrollPosition(savedTabId);
                }
            }
        }
    }

    // Guardar scroll y pestaña al cambiar de pestaña
    document.querySelectorAll('[data-bs-toggle="tab"]').forEach((tabBtn) => {
        tabBtn.addEventListener("show.bs.tab", function (e) {
            const currentTab = document.querySelector(
                ".tab-pane.active, .tab-content.active"
            );
            if (currentTab) {
                saveScrollPosition(currentTab.id);
            }
        });
        tabBtn.addEventListener("shown.bs.tab", function (e) {
            const targetTabId = tabBtn
                .getAttribute("data-bs-target")
                ?.replace("#", "");
            if (targetTabId) {
                saveActiveTab(targetTabId);
                restoreScrollPosition(targetTabId);
            }
        });
    });

    // Guardar scroll antes de salir del módulo/página
    window.addEventListener("beforeunload", function () {
        const currentTab = document.querySelector(
            ".tab-pane.active, .tab-content.active"
        );
        if (currentTab) {
            saveScrollPosition(currentTab.id);
        }
    });

    // Restaurar pestaña activa y scroll al cargar la página
    document.addEventListener("DOMContentLoaded", function () {
        restoreActiveTab();
        // Si no hay pestaña guardada, restaurar scroll de la actual
        setTimeout(() => {
            const currentTab = document.querySelector(
                ".tab-pane.active, .tab-content.active"
            );
            if (currentTab) {
                restoreScrollPosition(currentTab.id);
            }
        }, 20);
    });
})();

// MODAL OPCIONES APIARIO: ENLACES DINÁMICOS
document.addEventListener("DOMContentLoaded", () => {
    const baseDescargarDoc = "/generate-document/__ID__";

    // Cuando se abre el modal de opciones
    const modal = document.getElementById("modalOpcionesApiario");
    if (modal) {
        modal.addEventListener("show.bs.modal", function (event) {
            const button = event.relatedTarget;
            const apiarioId = button?.getAttribute("data-id");
            if (!apiarioId) return;

            // Enlazar dinámicamente los botones
            const btnVerDetalle = document.getElementById("btnVerDetalleMovimiento");
            if (btnVerDetalle) {
                btnVerDetalle.onclick = function () {
                    openDetalleMovimientoModal(apiarioId);
                };
            }
            const btnExportar = document.getElementById("btnExportarHistorial");
            if (btnExportar) {
                btnExportar.href = baseDescargarDoc.replace("__ID__", apiarioId);
            }
        });
    }
});
