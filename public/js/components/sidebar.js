document.addEventListener("DOMContentLoaded", function () {
    // Elementos DOM
    const sidebarToggleInside = document.getElementById("sidebarToggleInside");
    const floatingSidebarToggle = document.getElementById(
        "floatingSidebarToggle"
    );
    const sidebar = document.getElementById("sidebar");
    const sidebarOverlay = document.querySelector(".sidebar-overlay");
    const sidebarLinks = document.querySelectorAll(".sidebar-link");
    const htmlElement = document.documentElement;
    const bodyElement = document.body;

    // Función para prevenir scroll horizontal
    function preventHorizontalScroll() {
        htmlElement.style.overflowX = "hidden";
        bodyElement.style.overflowX = "hidden";
        htmlElement.style.width = "100%";
        bodyElement.style.width = "100%";
    }

    // Función para restaurar scroll normal
    function restoreNormalScroll() {
        htmlElement.style.overflowX = "";
        bodyElement.style.overflowX = "";
        htmlElement.style.width = "";
        bodyElement.style.width = "";
    }

    // Función para alternar el sidebar
    function toggleSidebar() {
        preventHorizontalScroll();

        document.body.classList.toggle("sidebar-collapsed");

        // Solo en móvil añadimos la clase sidebar-active
        if (window.innerWidth <= 992) {
            document.body.classList.toggle("sidebar-active");
        }

        // Guardar estado del sidebar en localStorage
        const isCollapsed =
            document.body.classList.contains("sidebar-collapsed");
        localStorage.setItem(
            "sidebarState",
            isCollapsed ? "collapsed" : "expanded"
        );

        // Restaurar scroll después de la transición
        setTimeout(restoreNormalScroll, 300);
    }

    // Función para abrir el sidebar en móvil o cuando está colapsado
    function openSidebar() {
        preventHorizontalScroll();

        document.body.classList.remove("sidebar-collapsed");
        document.body.classList.add("sidebar-active");

        // Guardar estado del sidebar en localStorage
        localStorage.setItem("sidebarState", "expanded");

        // Restaurar scroll después de la transición
        setTimeout(restoreNormalScroll, 300);
    }

    // Función para cerrar el sidebar en móvil
    function closeSidebar() {
        preventHorizontalScroll();

        document.body.classList.add("sidebar-collapsed");
        document.body.classList.remove("sidebar-active");

        // Guardar estado del sidebar en localStorage
        localStorage.setItem("sidebarState", "collapsed");

        // Restaurar scroll después de la transición
        setTimeout(restoreNormalScroll, 300);
    }

    // Event Listeners
    sidebarToggleInside.addEventListener("click", closeSidebar);
    floatingSidebarToggle.addEventListener("click", openSidebar);
    sidebarOverlay.addEventListener("click", closeSidebar);

    // Responsive: cerrar sidebar al hacer clic en un enlace en pantallas pequeñas
    sidebarLinks.forEach((link) => {
        link.addEventListener("click", function () {
            if (window.innerWidth <= 992) {
                preventHorizontalScroll();
                document.body.classList.remove("sidebar-active");
                document.body.classList.add("sidebar-collapsed");
                setTimeout(restoreNormalScroll, 300);
            }
        });
    });

    // Asegurar que el sidebar esté abierto por defecto
    document.body.classList.remove("sidebar-collapsed");
    if (window.innerWidth <= 992) {
        document.body.classList.add("sidebar-active");
    }

    // Guardar el estado inicial como expandido
    localStorage.setItem("sidebarState", "expanded");

    // Manejar el cambio de tamaño de ventana
    window.addEventListener("resize", function () {
        // Prevenir scroll horizontal durante la transición
        preventHorizontalScroll();

        if (window.innerWidth > 992) {
            document.body.classList.remove("sidebar-active");
            sidebarOverlay.style.visibility = "hidden";
        } else {
            // En pantallas pequeñas, asegurar que el botón flotante sea visible
            document.body.classList.add("sidebar-collapsed");
            document.body.classList.remove("sidebar-active");
            updateFloatingButtonVisibility();
        }

        // Restaurar el overflow después de un breve retraso
        setTimeout(restoreNormalScroll, 300);
    });

    // Comprobar el estado inicial para mostrar u ocultar el botón flotante
    updateFloatingButtonVisibility();

    // Función para actualizar la visibilidad del botón flotante
    function updateFloatingButtonVisibility() {
        if (
            document.body.classList.contains("sidebar-collapsed") ||
            window.innerWidth <= 992
        ) {
            floatingSidebarToggle.style.opacity = "1";
            floatingSidebarToggle.style.visibility = "visible";
        } else {
            floatingSidebarToggle.style.opacity = "0";
            floatingSidebarToggle.style.visibility = "hidden";
        }
    }

    // Observar cambios en las clases del body para actualizar el botón flotante
    const observer = new MutationObserver(function (mutations) {
        mutations.forEach(function (mutation) {
            if (mutation.attributeName === "class") {
                updateFloatingButtonVisibility();
            }
        });
    });

    observer.observe(document.body, { attributes: true });
});
