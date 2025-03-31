document.addEventListener("DOMContentLoaded", () => {
    // Variables
    const navbarContainer = document.querySelector(".navbar-container");
    const navbarToggle = document.getElementById("navbar-toggle");
    const mobileMenu = document.getElementById("mobile-menu");
    const navLinks = document.querySelectorAll(".nav-link");
    const mobileNavLinks = document.querySelectorAll(".mobile-nav-link");
    const mobileNavItems = document.querySelectorAll(".mobile-nav-item");
    const logoText = document.querySelector(".logo-text");
    const navItems = document.querySelectorAll(".nav-item");

    // Configurar el atributo data-text para el efecto del logo
    if (logoText) {
        logoText.setAttribute("data-text", logoText.textContent);
    }

    // Función para manejar el scroll
    function handleScroll() {
        if (window.scrollY > 10) {
            navbarContainer.classList.add("scrolled");
        } else {
            navbarContainer.classList.remove("scrolled");
        }
    }

    // Función para alternar el menú móvil
    function toggleMobileMenu() {
        navbarToggle.classList.toggle("active");
        mobileMenu.classList.toggle("active");

        // Bloquear scroll cuando el menú está abierto
        if (mobileMenu.classList.contains("active")) {
            document.body.style.overflow = "hidden";

            // Reiniciar animaciones de elementos del menú
            mobileNavItems.forEach((item, index) => {
                item.style.transitionDelay = `${0.07 * (index + 1)}s`;
            });
        } else {
            document.body.style.overflow = "";

            // Eliminar delays al cerrar
            setTimeout(() => {
                mobileNavItems.forEach((item) => {
                    item.style.transitionDelay = "0s";
                });
            }, 500);
        }
    }

    // Función para cerrar el menú móvil
    function closeMobileMenu() {
        if (mobileMenu && mobileMenu.classList.contains("active")) {
            navbarToggle.classList.remove("active");
            mobileMenu.classList.remove("active");
            document.body.style.overflow = "";

            // Eliminar delays
            setTimeout(() => {
                mobileNavItems.forEach((item) => {
                    item.style.transitionDelay = "0s";
                });
            }, 500);
        }
    }

    // Función para manejar cambios de tamaño de ventana
    function handleResize() {
        // Cerrar el menú móvil si la pantalla es mayor a 599px
        if (window.innerWidth > 599) {
            closeMobileMenu();
        }
    }

    // Función para marcar el enlace activo en ambos menús
    function setActiveLink(clickedLink) {
        const href = clickedLink.getAttribute("href");

        // Remover activo de todos los enlaces
        navLinks.forEach((link) => link.classList.remove("active"));
        mobileNavLinks.forEach((link) => link.classList.remove("active"));

        // Agregar activo al enlace clickeado y su correspondiente
        clickedLink.classList.add("active");
        const correspondingLink = [...navLinks, ...mobileNavLinks].find(
            (link) => link !== clickedLink && link.getAttribute("href") === href
        );
        if (correspondingLink) correspondingLink.classList.add("active");
    }

    // Función para actualizar enlaces según el hash actual
    function updateActiveLinksFromHash() {
        const hash = window.location.hash || "#";
        const targetLink = [...navLinks, ...mobileNavLinks].find(
            (link) => link.getAttribute("href") === hash
        );

        if (targetLink) setActiveLink(targetLink);
    }

    // Función para encontrar el elemento de destino, manejando diferentes IDs posibles
    function findTargetElement(targetId) {
        // Primero intentamos con el ID exacto
        let targetElement = document.getElementById(targetId);

        // Si no lo encontramos, probamos con variaciones comunes
        if (!targetElement) {
            // Verificar si existe un elemento con ID "maia-ecosystem" cuando buscamos "maia"
            if (targetId === "maia") {
                targetElement = document.getElementById("maia-ecosystem");
            }
            // Verificar si existe un elemento con ID "maia" cuando buscamos "maia-ecosystem"
            else if (targetId === "maia-ecosystem") {
                const maiaElement = document.getElementById("maia");
                if (maiaElement) {
                    targetElement = maiaElement;
                }
            }
        }

        return targetElement;
    }

    // Event listeners
    window.addEventListener("scroll", handleScroll);
    window.addEventListener("resize", handleResize);

    // Menú móvil
    if (navbarToggle) {
        navbarToggle.addEventListener("click", toggleMobileMenu);
    }

    // Event listeners para enlaces de escritorio
    navLinks.forEach((link) => {
        link.addEventListener("click", function (e) {
            if (this.getAttribute("href").startsWith("#")) {
                const targetId = this.getAttribute("href").substring(1);
                const targetElement = findTargetElement(targetId);

                if (targetElement) {
                    e.preventDefault();
                    setActiveLink(this);

                    // Scroll suave
                    window.scrollTo({
                        top: targetElement.offsetTop - 80,
                        behavior: "smooth",
                    });

                    // Actualizar URL
                    history.pushState(null, null, `#${targetId}`);
                }
            }
        });
    });

    // Event listeners para enlaces móviles
    mobileNavLinks.forEach((link) => {
        link.addEventListener("click", function (e) {
            if (this.getAttribute("href").startsWith("#")) {
                const targetId = this.getAttribute("href").substring(1);
                const targetElement = findTargetElement(targetId);

                if (targetElement) {
                    e.preventDefault();
                    setActiveLink(this);

                    toggleMobileMenu(); // Cerrar menú móvil

                    setTimeout(() => {
                        window.scrollTo({
                            top: targetElement.offsetTop - 80,
                            behavior: "smooth",
                        });
                        history.pushState(null, null, `#${targetId}`);
                    }, 300);
                }
            } else {
                toggleMobileMenu();
            }
        });
    });

    // Manejar el botón CTA
    const ctaButtons = document.querySelectorAll(".cta-button");
    ctaButtons.forEach((button) => {
        button.addEventListener("click", function (e) {
            if (this.getAttribute("href").startsWith("#")) {
                const targetId = this.getAttribute("href").substring(1);
                const targetElement = findTargetElement(targetId);

                if (targetElement) {
                    e.preventDefault();

                    // Scroll suave
                    window.scrollTo({
                        top: targetElement.offsetTop - 80,
                        behavior: "smooth",
                    });

                    // Actualizar URL
                    history.pushState(null, null, `#${targetId}`);
                }
            }
        });
    });

    // Actualizar enlaces al cargar y al cambiar hash
    updateActiveLinksFromHash();
    window.addEventListener("hashchange", updateActiveLinksFromHash);

    // Inicializar estado de scroll
    handleScroll();

    // Efecto hover del logo
    const logo = document.querySelector(".navbar-logo");
    if (logo) {
        logo.addEventListener("mouseenter", function () {
            const logoIcon = this.querySelector(".logo-icon");
            if (logoIcon) {
                logoIcon.style.transform = "rotate(10deg) scale(1.1)";
            }
        });

        logo.addEventListener("mouseleave", function () {
            const logoIcon = this.querySelector(".logo-icon");
            if (logoIcon) {
                logoIcon.style.transform = "";
            }
        });
    }
});

// Función para abrir modales
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = "block";
        modal.style.opacity = "0";

        setTimeout(() => {
            modal.style.opacity = "1";
            modal.style.transition = "opacity 0.3s ease";
        }, 10);
    }
}
