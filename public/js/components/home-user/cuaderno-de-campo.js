let activeDropdown = null;
let activeMenu = null;

// Variable global para mantener el estado de la vista
let currentViewType = "card"; // Vista por defecto

// Función para detectar si es dispositivo con pantalla pequeña (incluyendo 768px)
function isSmallScreen() {
    return window.innerWidth <= 768;
}

// Función mejorada para cambiar vista
function toggleView(viewType) {
    // Prevenir cambio de vista en pantallas pequeñas
    if (isSmallScreen()) {
        return;
    }

    // Actualizar la variable global
    currentViewType = viewType;

    // Guardar la vista preferida en localStorage
    localStorage.setItem("preferredView", viewType);

    // Aplicar la vista a TODAS las pestañas
    document.querySelectorAll(".apiaries-container").forEach((container) => {
        container.className = `apiaries-container ${viewType}-view`;
    });

    // Actualizar botones
    const viewButtons = document.querySelectorAll(".view-btn");
    viewButtons.forEach((btn) => {
        btn.classList.toggle(
            "active",
            btn.getAttribute("data-view") === viewType
        );
    });
}

// Función para manejar cambios responsive
function handleResponsiveChanges() {
    const viewControls = document.querySelector(".view-controls");

    if (isSmallScreen()) {
        // Ocultar controles de vista
        if (viewControls) {
            viewControls.style.display = "none";
        }

        // Forzar vista de tarjetas
        currentViewType = "card";
        document
            .querySelectorAll(".apiaries-container")
            .forEach((container) => {
                container.className = `apiaries-container card-view`;
            });

        // Actualizar botones (aunque estén ocultos)
        document.querySelectorAll(".view-btn").forEach((btn) => {
            btn.classList.toggle(
                "active",
                btn.getAttribute("data-view") === "card"
            );
        });
    } else {
        // Mostrar controles de vista
        if (viewControls) {
            viewControls.style.display = "flex";
        }

        // Restaurar vista preferida
        const savedView = localStorage.getItem("preferredView") || "card";
        currentViewType = savedView;

        document
            .querySelectorAll(".apiaries-container")
            .forEach((container) => {
                container.className = `apiaries-container ${savedView}-view`;
            });

        document.querySelectorAll(".view-btn").forEach((btn) => {
            btn.classList.toggle(
                "active",
                btn.getAttribute("data-view") === savedView
            );
        });
    }
}

// Función para inicializar todas las vistas con el formato guardado
function initializeAllViews() {
    if (isSmallScreen()) {
        // En pantallas pequeñas, siempre vista de tarjetas
        currentViewType = "card";
        document
            .querySelectorAll(".apiaries-container")
            .forEach((container) => {
                container.className = `apiaries-container card-view`;
            });

        // Ocultar controles de vista
        const viewControls = document.querySelector(".view-controls");
        if (viewControls) {
            viewControls.style.display = "none";
        }

        // Actualizar botones
        document.querySelectorAll(".view-btn").forEach((btn) => {
            btn.classList.toggle(
                "active",
                btn.getAttribute("data-view") === "card"
            );
        });
    } else {
        // En pantallas grandes, usar preferencia guardada
        const savedView = localStorage.getItem("preferredView") || "card";
        currentViewType = savedView;

        // Aplicar la vista a todos los contenedores
        document
            .querySelectorAll(".apiaries-container")
            .forEach((container) => {
                container.className = `apiaries-container ${savedView}-view`;
            });

        // Mostrar controles de vista
        const viewControls = document.querySelector(".view-controls");
        if (viewControls) {
            viewControls.style.display = "flex";
        }

        // Actualizar botones de vista
        document.querySelectorAll(".view-btn").forEach((btn) => {
            btn.classList.toggle(
                "active",
                btn.getAttribute("data-view") === savedView
            );
        });
    }
}

function filterApiaries() {
    const searchInput = document.getElementById("apiarySearch");
    const clearButton = document.querySelector(".clear-search");
    const filter = searchInput.value.toLowerCase().trim();
    const resultsCount = document.getElementById("resultsCount");

    // Obtener solo las tarjetas de la pestaña activa
    const activeTabContent = document.querySelector(".tab-content.active");
    if (!activeTabContent) return;

    const apiaryCards = activeTabContent.querySelectorAll(".apiary-card");

    clearButton.style.display = filter ? "flex" : "none";

    let count = 0;

    apiaryCards.forEach((card) => {
        const apiaryName = card.getAttribute("data-name");
        if (apiaryName && apiaryName.toLowerCase().includes(filter)) {
            card.style.display = "";
            count++;

            // Reiniciar la animación para que aparezca gradualmente
            card.style.animation = "none";
            card.offsetHeight; // Forzar reflow
            card.style.animation = "cardFadeIn 0.4s forwards";
        } else {
            card.style.display = "none";
        }
    });

    // Actualizar contador de resultados
    if (filter) {
        resultsCount.textContent = `${count} ${
            count === 1 ? "apiario encontrado" : "apiarios encontrados"
        }`;
    } else {
        resultsCount.textContent = "";
    }
}

// Limpiar búsqueda
function clearSearch() {
    const searchInput = document.getElementById("apiarySearch");
    searchInput.value = "";
    filterApiaries();
    searchInput.focus();
}

// Manejar dropdowns de descargas
function toggleDropdown(id, event) {
    // Prevenir cualquier comportamiento predeterminado
    event.preventDefault();

    const dropdown = document.getElementById(`downloadPanel${id}`);
    const container = dropdown.closest(".action-dropdown");

    // Cerrar dropdown activo si existe
    if (activeDropdown && activeDropdown !== container) {
        activeDropdown.classList.remove("active");
    }

    // Alternar estado del dropdown actual
    container.classList.toggle("active");

    // Actualizar referencia al dropdown activo
    activeDropdown = container.classList.contains("active") ? container : null;

    // Importante: detener propagación del evento
    event.stopPropagation();
}

// Manejar menús de tarjeta
function toggleCardMenu(element, event) {
    const menuContainer = element.closest(".card-menu");

    // Cerrar menú activo si existe
    if (activeMenu && activeMenu !== menuContainer) {
        activeMenu.classList.remove("active");
    }

    // Alternar estado del menú actual
    menuContainer.classList.toggle("active");

    // Actualizar referencia al menú activo
    activeMenu = menuContainer.classList.contains("active")
        ? menuContainer
        : null;

    // Importante: detener propagación del evento
    event.stopPropagation();
}

// Cerrar dropdowns al hacer clic en cualquier parte del documento
document.addEventListener("click", function (e) {
    // Cerrar el dropdown de descargas si está activo
    if (activeDropdown) {
        activeDropdown.classList.remove("active");
        activeDropdown = null;
    }

    // Cerrar el menú de tarjeta si está activo
    if (activeMenu) {
        activeMenu.classList.remove("active");
        activeMenu = null;
    }
});

// Guardar la página actual por pestaña en localStorage
function setCurrentPage(tabName, page) {
    localStorage.setItem(`apiaryPage_${tabName}`, page);
}

// Obtener la página guardada por pestaña
function getCurrentPage(tabName) {
    return parseInt(localStorage.getItem(`apiaryPage_${tabName}`)) || 1;
}

// Función para cambiar de pestaña (mejorada)
function switchTab(tabName) {
    // Desactivar todas las pestañas y contenidos
    document.querySelectorAll(".tab-btn").forEach((btn) => {
        btn.classList.remove("active");
    });

    document.querySelectorAll(".tab-content").forEach((content) => {
        content.classList.remove("active");
    });

    // Activar la pestaña seleccionada
    const activeTabBtn = document.querySelector(`[data-tab="${tabName}"]`);
    const activeTabContent = document.getElementById(`tab-${tabName}`);

    if (activeTabBtn && activeTabContent) {
        activeTabBtn.classList.add("active");
        activeTabContent.classList.add("active");

        // Aplicar la vista actual al nuevo contenedor
        const container = activeTabContent.querySelector(".apiaries-container");
        if (container) {
            container.className = `apiaries-container ${currentViewType}-view`;
        }

        // Guardar la pestaña activa en localStorage
        localStorage.setItem("activeTab", tabName);

        // Actualizar el filtro de búsqueda para la nueva pestaña
        filterApiaries();

        // --- AGREGADO: volver a inicializar la paginación según la pestaña ---
        if (tabName === "fijos") {
            paginateApiaries(
                "apiariesFijosContainer",
                "paginationFijos",
                6,
                getCurrentPage("fijos"),
                "fijos"
            );
        } else if (tabName === "base") {
            paginateApiaries(
                "apiariesBaseContainer",
                "paginationBase",
                6,
                getCurrentPage("base"),
                "base"
            );
        } else if (tabName === "temporales") {
            paginateApiaries(
                "apiariesTemporalesContainer",
                "paginationTemporales",
                6,
                getCurrentPage("temporales"),
                "temporales"
            );
        }
    }
}

// Función para paginar apiarios
function paginateApiaries(
    containerId,
    paginationId,
    cardsPerPage = 6,
    initialPage = 1,
    tabName = ""
) {
    const container = document.getElementById(containerId);
    const pagination = document.getElementById(paginationId);
    if (!container || !pagination) return;

    const cards = Array.from(container.getElementsByClassName("apiary-card"));
    let currentPage = initialPage;
    const totalPages = Math.ceil(cards.length / cardsPerPage);

    function showPage(page, scrollTo = false) {
        currentPage = page;
        cards.forEach((card, idx) => {
            if (idx >= (page - 1) * cardsPerPage && idx < page * cardsPerPage) {
                card.style.display = "";
                card.classList.remove("page-animating", "page-animated");
                void card.offsetWidth; // Forzar reflow
                card.classList.add("page-animating", "page-animated");
            } else {
                card.style.display = "none";
                card.classList.remove("page-animating", "page-animated");
            }
        });
        renderPagination();
        if (tabName) setCurrentPage(tabName, currentPage);

        // Solo hacer scroll si se indica explícitamente
        if (container && scrollTo) {
            container.scrollIntoView({ behavior: "smooth", block: "start" });
        }
    }

    function renderPagination() {
        if (totalPages <= 1) {
            pagination.innerHTML = "";
            return;
        }
        let html = "";
        if (currentPage > 1) {
            html += `<button class="page-btn" data-page="${
                currentPage - 1
            }">&laquo;</button>`;
        }
        for (let i = 1; i <= totalPages; i++) {
            html += `<button class="page-btn${
                i === currentPage ? " active" : ""
            }" data-page="${i}">${i}</button>`;
        }
        if (currentPage < totalPages) {
            html += `<button class="page-btn" data-page="${
                currentPage + 1
            }">&raquo;</button>`;
        }
        pagination.innerHTML = html;

        pagination.querySelectorAll(".page-btn").forEach((btn) => {
            btn.onclick = () =>
                showPage(Number(btn.getAttribute("data-page")), true); // Solo aquí scroll
        });
    }

    showPage(currentPage, false); // No hacer scroll al inicializar
}

// Configuración al cargar el documento
document.addEventListener("DOMContentLoaded", function () {
    // Evitar que los clics dentro de los menús los cierren
    document
        .querySelectorAll(".card-menu-dropdown, .dropdown-panel")
        .forEach((menu) => {
            menu.addEventListener("click", function (e) {
                e.stopPropagation();
            });
        });

    // Configurar botones de vista (modificado para prevenir en pantallas pequeñas)
    document.querySelectorAll(".view-btn").forEach((btn) => {
        btn.addEventListener("click", function () {
            if (!isSmallScreen()) {
                toggleView(this.getAttribute("data-view"));
            }
        });
    });

    // Configurar botones de menú
    document.querySelectorAll(".menu-trigger").forEach((btn) => {
        btn.addEventListener("click", function (e) {
            toggleCardMenu(this, e);
        });
    });

    // Actualizar los event listeners para los botones de descarga
    document.querySelectorAll('[id^="downloadBtn"]').forEach((btn) => {
        const id = btn.id.replace("downloadBtn", "");
        btn.addEventListener("click", function (e) {
            toggleDropdown(id, e);
        });
    });

    // Agregar manejo específico para los enlaces de descarga
    document.querySelectorAll(".dropdown-option").forEach((option) => {
        option.addEventListener("click", function (e) {
            e.stopPropagation();
        });
    });

    // Configurar pestañas
    document.querySelectorAll(".tab-btn").forEach((btn) => {
        btn.addEventListener("click", function () {
            const tabName = this.getAttribute("data-tab");
            switchTab(tabName);
        });
    });

    // Inicializar todas las vistas con el formato adecuado
    initializeAllViews();

    // Restaurar la pestaña activa desde localStorage
    const savedTab = localStorage.getItem("activeTab");
    if (savedTab && document.querySelector(`[data-tab="${savedTab}"]`)) {
        switchTab(savedTab);
    } else {
        // Si no hay pestaña guardada, activar la primera disponible
        const firstTab = document.querySelector(".tab-btn");
        if (firstTab) {
            const firstTabName = firstTab.getAttribute("data-tab");
            switchTab(firstTabName);
        }
    }

    // Event listener para cambios de tamaño de ventana
    window.addEventListener("resize", function () {
        handleResponsiveChanges();
    });

    // Efecto parallax para el encabezado
    const headerElement = document.querySelector(".dashboard-header");
    const headerContent = document.querySelector(".header-content");

    window.addEventListener("scroll", function () {
        const scrollPosition = window.scrollY;
        if (scrollPosition < 300) {
            headerElement.style.backgroundPosition = `0 ${
                scrollPosition * 0.15
            }px`;
            headerContent.style.transform = `translateY(${
                scrollPosition * 0.1
            }px)`;
        }
    });

    // Efecto de revelación para títulos y elementos al hacer scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: "0px 0px -50px 0px",
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add("revealed");
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Agregar clase para observar elementos
    document
        .querySelectorAll(".apiary-card, .dashboard-header, .control-panel")
        .forEach((el) => {
            el.classList.add("reveal-element");
            observer.observe(el);
        });

    // Efecto de hover 3D para tarjetas
    document.querySelectorAll(".apiary-card").forEach((card) => {
        card.addEventListener("mousemove", function (e) {
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            const xPercent = x / rect.width;
            const yPercent = y / rect.height;

            const rotateX = (0.5 - yPercent) * 8;
            const rotateY = (xPercent - 0.5) * 8;

            this.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.02, 1.02, 1.02)`;
        });

        card.addEventListener("mouseleave", function () {
            this.style.transform = "";
        });
    });

    // Inicializar paginación al cargar
    paginateApiaries(
        "apiariesFijosContainer",
        "paginationFijos",
        6,
        getCurrentPage("fijos"),
        "fijos"
    );
    paginateApiaries(
        "apiariesBaseContainer",
        "paginationBase",
        6,
        getCurrentPage("base"),
        "base"
    );
    paginateApiaries(
        "apiariesTemporalesContainer",
        "paginationTemporales",
        6,
        getCurrentPage("temporales"),
        "temporales"
    );
});
