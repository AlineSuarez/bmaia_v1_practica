// Variables globales para paginación
let currentPage = 1;
const itemsPerPage = 5;
let totalItems = 0;
let totalPages = 0;
let allRows = [];
let pendingAction = null; // Para almacenar la acción pendiente

// Sistema de notificaciones
function showNotification(message, type = "success", duration = 4000) {
    const notificationId = "notification-" + Date.now();
    const iconMap = {
        success: "fas fa-check-circle",
        error: "fas fa-exclamation-circle",
        warning: "fas fa-exclamation-triangle",
        info: "fas fa-info-circle",
    };

    const notification = $(`
                 <div id="${notificationId}" class="notification notification-${type}">
                     <div class="notification-content">
                         <i class="${iconMap[type]} notification-icon"></i>
                         <span class="notification-message">${message}</span>
                         <button class="notification-close" onclick="closeNotification('${notificationId}')">
                             <i class="fas fa-times"></i>
                         </button>
                     </div>
                     <div class="notification-progress">
                         <div class="notification-progress-bar"></div>
                     </div>
                 </div>
             `);

    $("#notificationContainer").append(notification);

    // Animar entrada
    setTimeout(() => {
        $(`#${notificationId}`).addClass("notification-show");
    }, 100);

    // Iniciar barra de progreso
    setTimeout(() => {
        $(`#${notificationId} .notification-progress-bar`).css(
            "animation",
            `progress ${duration}ms linear forwards`
        );
    }, 200);

    // Auto-cerrar
    setTimeout(() => {
        closeNotification(notificationId);
    }, duration);
}

function closeNotification(notificationId) {
    const notification = $(`#${notificationId}`);
    notification.removeClass("notification-show").addClass("notification-hide");
    setTimeout(() => {
        notification.remove();
    }, 300);
}

function cargarConsejo(
    apiarioId,
    row,
    nombreApiario = null,
    showNotifications = false
) {
    var consejoTd = row.find(".consejo-td");
    consejoTd.find(".spinner-border").removeClass("d-none");
    consejoTd.find(".consejo-text").text("");

    $.ajax({
        url: "/apiarios/" + apiarioId + "/obtener-consejo",
        type: "GET",
        success: function (data) {
            consejoTd.find(".spinner-border").addClass("d-none");
            if (data.success) {
                consejoTd.find(".consejo-text").text(data.consejo);
                // Solo mostrar notificación si showNotifications es true (recarga manual)
                if (showNotifications) {
                    const apiarioName =
                        nombreApiario || row.find("td:first strong").text();
                    showNotification(
                        `Consejo regenerado exitosamente para "${apiarioName}"`,
                        "success", // Color naranja/amber
                        3000
                    );
                }
            } else {
                consejoTd
                    .find(".consejo-text")
                    .html(
                        '<div class="alert alert-success p-2 mb-0"><span>' +
                            data.message +
                            '</span> <a href="' +
                            data.registrar_pcc_url +
                            '" class="btn btn-sm btn-outline-secondary ms-2">Registrar PCC</a></div>'
                    );
                // Solo mostrar notificación si showNotifications es true
                if (showNotifications) {
                    showNotification(
                        `${data.message}`,
                        "success", // Color naranja/amber
                        4000
                    );
                }
            }
        },
        error: function (xhr, status, error) {
            consejoTd.find(".spinner-border").addClass("d-none");
            consejoTd
                .find(".consejo-text")
                .html(
                    '<div class="alert alert-danger p-2 mb-0">Error al obtener consejo</div>'
                );
            // Solo mostrar notificación si showNotifications es true
            if (showNotifications) {
                const apiarioName =
                    nombreApiario || row.find("td:first strong").text();
                showNotification(
                    `Error al regenerar consejo para "${apiarioName}"`,
                    "warning", // Color naranja/amber incluso para errores
                    4000
                );
            }
        },
    });
}

function showConfirmModal(message, action) {
    $("#confirmMessage").text(message);
    pendingAction = action;
    $("#confirmModal").modal("show");
}

function regenerarConsejosGlobal() {
    let completedRequests = 0;
    let successCount = 0;
    let errorCount = 0;
    const totalRequests = $(".apiario-row").length;

    // Regenerar todos los consejos de todos los apiarios (todas las páginas)
    $(".apiario-row").each(function () {
        var apiarioId = $(this).data("apiario");
        if (apiarioId) {
            const row = $(this);
            const nombreApiario = row.find("td:first strong").text();
            var consejoTd = row.find(".consejo-td");
            consejoTd.find(".spinner-border").removeClass("d-none");
            consejoTd.find(".consejo-text").text("");

            $.ajax({
                url: "/apiarios/" + apiarioId + "/obtener-consejo",
                type: "GET",
                success: function (data) {
                    consejoTd.find(".spinner-border").addClass("d-none");
                    if (data.success) {
                        consejoTd.find(".consejo-text").text(data.consejo);
                        successCount++;
                    } else {
                        consejoTd
                            .find(".consejo-text")
                            .html(
                                '<div class="alert alert-warning p-2 mb-0"><span>' +
                                    data.message +
                                    '</span> <a href="' +
                                    data.registrar_pcc_url +
                                    '" class="btn btn-sm btn-outline-secondary ms-2">Registrar PCC</a></div>'
                            );
                        errorCount++;
                    }
                },
                error: function () {
                    consejoTd.find(".spinner-border").addClass("d-none");
                    consejoTd
                        .find(".consejo-text")
                        .html(
                            '<div class="alert alert-danger p-2 mb-0">Error al obtener consejo</div>'
                        );
                    errorCount++;
                },
                complete: function () {
                    completedRequests++;
                    if (completedRequests === totalRequests) {
                        // Mostrar notificación resumen SOLO para regeneración global manual
                        if (errorCount === 0) {
                            showNotification(
                                `Todos los consejos se regeneraron exitosamente (${successCount} apiarios)`,
                                "success",
                                5000
                            );
                        } else if (successCount > 0) {
                            showNotification(
                                `Regeneración completada: ${successCount} exitosos, ${errorCount} con errores`,
                                "success",
                                5000
                            );
                        } else {
                            showNotification(
                                `Regeneración completada con errores (${errorCount} errores)`,
                                "warning",
                                5000
                            );
                        }
                    }
                },
            });
        }
    });
}

function regenerarConsejoIndividual(apiarioId, row, nombreApiario) {
    // Pasar true para mostrar notificaciones en recarga manual individual
    cargarConsejo(apiarioId, row, nombreApiario, true);
}

function initializePagination() {
    allRows = $(".apiario-row").toArray();
    totalItems = allRows.length;
    totalPages = Math.ceil(totalItems / itemsPerPage);

    if (totalItems > itemsPerPage) {
        $("#paginationContainer").show();
        showPage(1);
        renderPaginationControls();
    } else {
        $("#paginationContainer").hide();
        $(".apiario-row").show();
        // Cargar consejos para todas las filas si no hay paginación
        // NO mostrar notificaciones en carga inicial (false por defecto)
        $(".apiario-row").each(function () {
            var apiarioId = $(this).data("apiario");
            if (apiarioId) {
                cargarConsejo(apiarioId, $(this));
            }
        });
    }
}

function showPage(page) {
    currentPage = page;

    // Añadir clase de transición
    $(".apiario-row").addClass("page-transition");

    setTimeout(() => {
        // Ocultar todas las filas
        $(".apiario-row").hide();

        // Calcular índices
        const startIndex = (page - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;

        // Mostrar filas de la página actual
        const rowsToShow = allRows.slice(startIndex, endIndex);
        $(rowsToShow).show().removeClass("page-transition");

        // Actualizar información de paginación
        updatePaginationInfo();

        // Cargar consejos para las filas visibles
        // NO mostrar notificaciones en cambio de página (false por defecto)
        $(rowsToShow).each(function () {
            const apiarioId = $(this).data("apiario");
            if (apiarioId) {
                cargarConsejo(apiarioId, $(this));
            }
        });
    }, 150);
}

function updatePaginationInfo() {
    const startItem = (currentPage - 1) * itemsPerPage + 1;
    const endItem = Math.min(currentPage * itemsPerPage, totalItems);
    const infoText = `Mostrando ${startItem}-${endItem} de ${totalItems} apiario${
        totalItems !== 1 ? "s" : ""
    }`;
    $("#paginationInfo").text(infoText);
}

function renderPaginationControls() {
    const nav = $("#paginationNav");
    nav.empty();

    // Botón anterior
    const prevDisabled = currentPage === 1 ? "disabled" : "";
    nav.append(`
                 <li class="page-item ${prevDisabled}">
                     <a class="page-link prev-btn" href="#" data-page="${
                         currentPage - 1
                     }" aria-label="Página anterior">
                         <i class="fas fa-chevron-left"></i>
                     </a>
                 </li>
             `);

    // Lógica para mostrar números de página
    let startPage = Math.max(1, currentPage - 2);
    let endPage = Math.min(totalPages, currentPage + 2);

    // Ajustar si estamos cerca del inicio o final
    if (currentPage <= 3) {
        endPage = Math.min(5, totalPages);
    }
    if (currentPage > totalPages - 3) {
        startPage = Math.max(1, totalPages - 4);
    }

    // Primera página si no está visible
    if (startPage > 1) {
        nav.append(`
                     <li class="page-item">
                         <a class="page-link" href="#" data-page="1">1</a>
                     </li>
                 `);
        if (startPage > 2) {
            nav.append(`
                         <li class="page-item disabled">
                             <span class="page-link">...</span>
                         </li>
                     `);
        }
    }

    // Páginas del rango actual
    for (let i = startPage; i <= endPage; i++) {
        const activeClass = i === currentPage ? "active" : "";
        nav.append(`
                     <li class="page-item ${activeClass}">
                         <a class="page-link" href="#" data-page="${i}">${i}</a>
                     </li>
                 `);
    }

    // Última página si no está visible
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            nav.append(`
                         <li class="page-item disabled">
                             <span class="page-link">...</span>
                         </li>
                     `);
        }
        nav.append(`
                     <li class="page-item">
                         <a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a>
                     </li>
                 `);
    }

    // Botón siguiente
    const nextDisabled = currentPage === totalPages ? "disabled" : "";
    nav.append(`
                 <li class="page-item ${nextDisabled}">
                     <a class="page-link next-btn" href="#" data-page="${
                         currentPage + 1
                     }" aria-label="Página siguiente">
                         <i class="fas fa-chevron-right"></i>
                     </a>
                 </li>
             `);
}

$(document).ready(function () {
    // Inicializar paginación
    initializePagination();

    // Event listeners para paginación
    $(document).on("click", ".page-link", function (e) {
        e.preventDefault();
        const page = parseInt($(this).data("page"));

        if (
            !isNaN(page) &&
            page >= 1 &&
            page <= totalPages &&
            page !== currentPage
        ) {
            showPage(page);
            renderPaginationControls();
        }
    });

    // Botón Regenerar Consejo individual con confirmación
    $(document).on("click", ".btn-consejo", function () {
        var apiarioId = $(this).data("id");
        var row = $(this).closest("tr");
        var nombreApiario = row.find("td:first strong").text();

        showConfirmModal(
            `¿Estás seguro de que deseas regenerar el consejo para "${nombreApiario}"?`,
            function () {
                regenerarConsejoIndividual(apiarioId, row, nombreApiario);
            }
        );
    });

    // Botón Regenerar Consejos global con confirmación - TODOS LOS APIARIOS
    $("#regenerarConsejos").click(function () {
        const mensaje = `¿Estás seguro de que deseas regenerar todos los consejos de todos los apiarios (${totalItems} apiario${
            totalItems !== 1 ? "s" : ""
        })?`;

        showConfirmModal(mensaje, regenerarConsejosGlobal);
    });

    // Confirmar acción en el modal
    $("#confirmRegenerate").click(function () {
        if (pendingAction && typeof pendingAction === "function") {
            pendingAction();
            pendingAction = null;
        }
        $("#confirmModal").modal("hide");
    });

    // Limpiar acción pendiente al cerrar el modal
    $("#confirmModal").on("hidden.bs.modal", function () {
        pendingAction = null;
    });

    // Mejorar la experiencia del botón en móviles
    function handleButtonResponsiveness() {
        const $btn = $("#regenerarConsejos");
        const $icon = $btn.find(".fas.fa-sync");

        // Detectar si está en proceso de regeneración
        let isRegenerating = false;

        // Función para manejar el estado de carga
        function setLoadingState(loading) {
            isRegenerating = loading;
            if (loading) {
                $btn.prop("disabled", true);
                $icon.addClass("fa-spin");

                // Cambiar texto según el tamaño de pantalla
                if (window.innerWidth <= 380) {
                    // Solo ícono en pantallas muy pequeñas
                } else if (window.innerWidth <= 576) {
                    $btn.find(".btn-text-full").text("Regenerando...");
                } else if (window.innerWidth <= 768) {
                    $btn.find(".btn-text-short").text("Regenerando...");
                } else {
                    $btn.find(".btn-text-full").text("Regenerando...");
                }
            } else {
                $btn.prop("disabled", false);
                $icon.removeClass("fa-spin");

                // Restaurar texto original
                $btn.find(".btn-text-full").text("Regenerar Todos");
                $btn.find(".btn-text-short").text("Regenerar");
            }
        }

        // Sobrescribir la función regenerarConsejosGlobal para incluir estados de carga
        const originalRegenerarConsejosGlobal = window.regenerarConsejosGlobal;
        window.regenerarConsejosGlobal = function () {
            setLoadingState(true);

            let completedRequests = 0;
            let successCount = 0;
            let errorCount = 0;
            const totalRequests = $(".apiario-row").length;

            $(".apiario-row").each(function () {
                var apiarioId = $(this).data("apiario");
                if (apiarioId) {
                    const row = $(this);
                    var consejoTd = row.find(".consejo-td");
                    consejoTd.find(".spinner-border").removeClass("d-none");
                    consejoTd.find(".consejo-text").text("");

                    $.ajax({
                        url: "/apiarios/" + apiarioId + "/obtener-consejo",
                        type: "GET",
                        success: function (data) {
                            consejoTd
                                .find(".spinner-border")
                                .addClass("d-none");
                            if (data.success) {
                                consejoTd
                                    .find(".consejo-text")
                                    .text(data.consejo);
                                successCount++;
                            } else {
                                consejoTd
                                    .find(".consejo-text")
                                    .html(
                                        '<div class="alert alert-warning p-2 mb-0"><span>' +
                                            data.message +
                                            '</span> <a href="' +
                                            data.registrar_pcc_url +
                                            '" class="btn btn-sm btn-outline-secondary ms-2">Registrar PCC</a></div>'
                                    );
                                errorCount++;
                            }
                        },
                        error: function () {
                            consejoTd
                                .find(".spinner-border")
                                .addClass("d-none");
                            consejoTd
                                .find(".consejo-text")
                                .html(
                                    '<div class="alert alert-danger p-2 mb-0">Error al obtener consejo</div>'
                                );
                            errorCount++;
                        },
                        complete: function () {
                            completedRequests++;
                            if (completedRequests === totalRequests) {
                                setLoadingState(false);

                                // Mostrar notificación resumen en color naranja
                                if (errorCount === 0) {
                                    showNotification(
                                        `Todos los consejos se regeneraron exitosamente (${successCount} apiarios)`,
                                        "success",
                                        5000
                                    );
                                } else if (successCount > 0) {
                                    showNotification(
                                        `Regeneración completada: ${successCount} exitosos, ${errorCount} con errores`,
                                        "success",
                                        5000
                                    );
                                } else {
                                    showNotification(
                                        `⚠️ Regeneración completada con errores (${errorCount} errores)`,
                                        "warning",
                                        5000
                                    );
                                }
                            }
                        },
                    });
                }
            });
        };
    }

    // Inicializar mejoras de responsividad
    handleButtonResponsiveness();
});
