let activeDropdown = null;
let activeMenu = null;

// Variable global para mantener el estado de la vista
let currentViewType = "card"; // Vista por defecto

function filterApiaries() {
    const searchInput = document.getElementById("apiarySearch");
    const clearButton = document.querySelector(".clear-search");
    const filter = searchInput.value.toLowerCase().trim();
    const apiaryCards = document.querySelectorAll(".apiary-card");
    const noResults = document.getElementById("noResults");
    const resultsCount = document.getElementById("resultsCount");
    const container = document.getElementById("apiariesContainer");

    clearButton.style.display = filter ? "flex" : "none";

    let count = 0;

    apiaryCards.forEach((card) => {
        const apiaryName = card.getAttribute("data-name");
        if (apiaryName.includes(filter)) {
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

    // Mostrar mensaje si no hay resultados
    if (count === 0) {
        noResults.style.display = "block";
        container.style.display = "none";
    } else {
        noResults.style.display = "none";
        container.style.display = "";
    }
}

// Limpiar búsqueda
function clearSearch() {
    const searchInput = document.getElementById("apiarySearch");
    searchInput.value = "";
    filterApiaries();
    searchInput.focus();
}

// Alternar entre vistas de tarjeta y lista
function toggleView(viewType) {
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
    }
}

// Función modificada para filtrar apiarios considerando la pestaña activa
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

// Función para inicializar todas las vistas con el formato guardado
function initializeAllViews() {
    // Recuperar la vista preferida desde localStorage
    const savedView = localStorage.getItem("preferredView") || "card";
    currentViewType = savedView;

    // Aplicar la vista a todos los contenedores
    document.querySelectorAll(".apiaries-container").forEach((container) => {
        container.className = `apiaries-container ${savedView}-view`;
    });

    // Actualizar botones de vista
    document.querySelectorAll(".view-btn").forEach((btn) => {
        btn.classList.toggle(
            "active",
            btn.getAttribute("data-view") === savedView
        );
    });
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

    // Configurar botones de vista (modificado)
    document.querySelectorAll(".view-btn").forEach((btn) => {
        btn.addEventListener("click", function () {
            toggleView(this.getAttribute("data-view"));
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

    // Inicializar todas las vistas con el formato guardado
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
});
