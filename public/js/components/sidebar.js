document.addEventListener("DOMContentLoaded", () => {
    // Elementos DOM
    const sidebarToggleInside = document.getElementById("sidebarToggleInside");
    const floatingSidebarToggle = document.getElementById(
        "floatingSidebarToggle"
    );
    const sidebar = document.getElementById("sidebar");
    const sidebarOverlay = document.querySelector(".apicola-sidebar-overlay");
    const sidebarLinks = document.querySelectorAll(".apicola-sidebar-link");
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
        link.addEventListener("click", () => {
            if (window.innerWidth <= 992) {
                preventHorizontalScroll();
                document.body.classList.remove("sidebar-active");
                document.body.classList.add("sidebar-collapsed");
                setTimeout(restoreNormalScroll, 300);
            }
        });
    });

    // CAMBIO AQUÍ: Configurar estado inicial según el tamaño de pantalla
    if (window.innerWidth <= 992) {
        // En móvil, cerrar sidebar por defecto
        document.body.classList.add("sidebar-collapsed");
        document.body.classList.remove("sidebar-active");
        // Guardar el estado inicial como colapsado para móvil
        localStorage.setItem("sidebarState", "collapsed");
    } else {
        // En desktop, abrir sidebar por defecto
        document.body.classList.remove("sidebar-collapsed");
        // Guardar el estado inicial como expandido para desktop
        localStorage.setItem("sidebarState", "expanded");
    }

    // Manejar el cambio de tamaño de ventana
    window.addEventListener("resize", () => {
        // Prevenir scroll horizontal durante la transición
        preventHorizontalScroll();

        if (window.innerWidth > 992) {
            document.body.classList.remove("sidebar-active");
            sidebarOverlay.style.visibility = "hidden";
        } else {
            // En pantallas pequeñas, asegurar que el sidebar esté cerrado
            document.body.classList.add("sidebar-collapsed");
            document.body.classList.remove("sidebar-active");
        }

        // Actualizar visibilidad del botón flotante
        updateFloatingButtonVisibility();

        // Restaurar el overflow después de un breve retraso
        setTimeout(restoreNormalScroll, 300);
    });

    // Actualizar la visibilidad del botón flotante al cargar
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
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.attributeName === "class") {
                updateFloatingButtonVisibility();
            }
        });
    });

    observer.observe(document.body, { attributes: true });

    // Añadir efecto de ondas al hacer clic en los elementos del menú
    sidebarLinks.forEach((link) => {
        link.addEventListener("click", function (e) {
            const ripple = document.createElement("span");
            ripple.classList.add("apicola-ripple-effect");

            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            ripple.style.left = x + "px";
            ripple.style.top = y + "px";

            this.appendChild(ripple);

            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });

    // Añadir estilos para la animación de ondas
    if (!document.getElementById("ripple-style")) {
        const style = document.createElement("style");
        style.id = "ripple-style";
        style.textContent = `
              @keyframes ripple {
                  to {
                      transform: translate(-50%, -50%) scale(3);
                      opacity: 0;
                  }
              }
          `;
        document.head.appendChild(style);
    }
});

// --- Mostrar/ocultar botón flotante según dirección de scroll SOLO cuando el sidebar está oculto ---
let lastScrollY = window.scrollY;
let ticking = false;

function handleFloatingButtonOnScroll() {
    const btn = document.getElementById("floatingSidebarToggle");
    // Solo activar si el sidebar está oculto (collapsed o en móvil)
    const sidebarHidden =
        document.body.classList.contains("sidebar-collapsed") ||
        window.innerWidth <= 992;

    if (!btn || !sidebarHidden) {
        // Si el sidebar está visible, mostrar el botón y salir
        if (btn) {
            btn.style.transform = "translateY(0)";
            btn.style.opacity = "1";
            btn.style.pointerEvents = "auto";
        }
        ticking = false;
        lastScrollY = window.scrollY;
        return;
    }

    const currentScrollY = window.scrollY;

    if (currentScrollY > lastScrollY && currentScrollY > 40) {
        // Scroll hacia abajo: ocultar botón
        btn.style.transform = "translateY(100px)";
        btn.style.opacity = "0";
        btn.style.pointerEvents = "none";
    } else {
        // Scroll hacia arriba: mostrar botón
        btn.style.transform = "translateY(0)";
        btn.style.opacity = "1";
        btn.style.pointerEvents = "auto";
    }

    lastScrollY = currentScrollY;
    ticking = false;
}

window.addEventListener("scroll", function () {
    if (!ticking) {
        window.requestAnimationFrame(handleFloatingButtonOnScroll);
        ticking = true;
    }
});

// Asegúrate de que el botón tenga transición suave
const btn = document.getElementById("floatingSidebarToggle");
if (btn) {
    btn.style.transition =
        "transform 0.35s cubic-bezier(0.16,1,0.3,1), opacity 0.35s cubic-bezier(0.16,1,0.3,1)";
}
