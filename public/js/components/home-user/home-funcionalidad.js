document.addEventListener("DOMContentLoaded", function () {
    const userDropdownBtn = document.getElementById("userDropdownBtn");
    const userDropdownMenu = document.getElementById("userDropdownMenu");
    const navbar = document.querySelector(".standard-navbar");
    const userAvatar = document.querySelector(".user-avatar");

    let lastScrollTop = 0;
    let isDropdownOpen = false;

    function wrapDropdownContent() {
        if (!userDropdownMenu) return;

        if (!userDropdownMenu.querySelector(".dropdown-menu-container")) {
            const container = document.createElement("div");
            container.className = "dropdown-menu-container";
            while (userDropdownMenu.firstChild) {
                container.appendChild(userDropdownMenu.firstChild);
            }

            userDropdownMenu.appendChild(container);
        }
    }

    function wrapAvatar() {
        if (!userDropdownBtn) return;

        const avatar = userDropdownBtn.querySelector(".user-avatar");
        if (
            avatar &&
            !avatar.parentElement.classList.contains("user-avatar-container")
        ) {
            const container = document.createElement("div");
            container.className = "user-avatar-container";

            avatar.parentNode.insertBefore(container, avatar);
            container.appendChild(avatar);
        }
    }

    function toggleDropdown(e) {
        if (!userDropdownBtn || !userDropdownMenu) return;

        e.preventDefault();
        e.stopPropagation();

        isDropdownOpen = !isDropdownOpen;
        userDropdownBtn.setAttribute("aria-expanded", isDropdownOpen);

        if (isDropdownOpen) {
            userDropdownMenu.classList.add("show");
            addRippleEffect(e);

            createHoneycombParticles(15, userDropdownBtn);

            const items = userDropdownMenu.querySelectorAll(".dropdown-item");
            items.forEach((item, index) => {
                item.style.opacity = "0";
                item.style.transform = "translateX(-10px)";
                setTimeout(() => {
                    item.style.transition =
                        "all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) " +
                        index * 0.08 +
                        "s";
                    item.style.opacity = "1";
                    item.style.transform = "translateX(0)";
                }, 50);
            });
        } else {
            createHoneycombParticles(10, userDropdownBtn);

            const items = userDropdownMenu.querySelectorAll(".dropdown-item");
            items.forEach((item) => {
                item.style.opacity = "0";
                item.style.transform = "translateX(10px)";
            });

            setTimeout(() => {
                userDropdownMenu.classList.remove("show");
                items.forEach((item) => {
                    item.style = "";
                });
            }, 200);
        }
    }

    function addRippleEffect(e) {
        if (!userDropdownBtn) return;

        const oldRipples = userDropdownBtn.querySelectorAll(".ripple-effect");
        oldRipples.forEach((ripple) => ripple.remove());

        const ripple = document.createElement("span");
        ripple.classList.add("ripple-effect");
        userDropdownBtn.appendChild(ripple);

        const rect = userDropdownBtn.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height) * 2;

        ripple.style.width = ripple.style.height = `${size}px`;
        ripple.style.left = `${e.clientX - rect.left - size / 2}px`;
        ripple.style.top = `${e.clientY - rect.top - size / 2}px`;

        setTimeout(() => {
            ripple.remove();
        }, 600);
    }

    function handleScroll() {
        if (!navbar) return;

        const scrollTop =
            window.pageYOffset || document.documentElement.scrollTop;

        if (scrollTop > lastScrollTop && scrollTop > 80) {
            navbar.style.transform = "translateY(-100%)";
        } else {
            navbar.style.transform = "translateY(0)";
        }

        if (scrollTop > 10) {
            navbar.classList.add("scrolled");
        } else {
            navbar.classList.remove("scrolled");
        }

        lastScrollTop = scrollTop;
    }

    function handleOutsideClick(e) {
        if (!userDropdownBtn || !userDropdownMenu) return;

        if (
            isDropdownOpen &&
            !userDropdownBtn.contains(e.target) &&
            !userDropdownMenu.contains(e.target)
        ) {
            isDropdownOpen = false;
            userDropdownBtn.setAttribute("aria-expanded", "false");

            const items = userDropdownMenu.querySelectorAll(".dropdown-item");
            items.forEach((item) => {
                item.style.opacity = "0";
                item.style.transform = "translateX(10px)";
            });

            setTimeout(() => {
                userDropdownMenu.classList.remove("show");
                items.forEach((item) => {
                    item.style = "";
                });
            }, 200);
        }
    }

    // Manejar tecla ESC
    function handleEscKey(e) {
        if (e.key === "Escape" && isDropdownOpen) {
            isDropdownOpen = false;
            userDropdownBtn.setAttribute("aria-expanded", "false");

            const items = userDropdownMenu.querySelectorAll(".dropdown-item");
            items.forEach((item) => {
                item.style.opacity = "0";
                item.style.transform = "translateX(10px)";
            });

            setTimeout(() => {
                userDropdownMenu.classList.remove("show");
                items.forEach((item) => {
                    item.style = "";
                });
            }, 200);
        }
    }

    function addMenuItemEffects() {
        const dropdownItems = document.querySelectorAll(".dropdown-item");

        dropdownItems.forEach((item) => {
            item.addEventListener("click", function (e) {
                const icon = this.querySelector("i");
                if (icon) {
                    icon.style.transform =
                        "translateY(-3px) rotate(5deg) scale(1.1)";
                    setTimeout(() => {
                        icon.style.transform =
                            "translateY(0) rotate(0) scale(1)";
                    }, 300);
                }

                createHoneycombParticles(8, this);
            });
        });
    }

    function createHoneycombParticles(count = 5, sourceElement = null) {
        const source = sourceElement || userAvatar;
        if (!source) return;

        // Obtener la posición del elemento fuente
        const rect = source.getBoundingClientRect();
        const centerX = rect.left + rect.width / 2;
        const centerY = rect.top + rect.height / 2;

        const honeyColors = ["#f59e0b", "#fbbf24", "#fcd34d", "#d97706"];

        for (let i = 0; i < count; i++) {
            const particle = document.createElement("div");
            particle.className = "honeycomb-particle";
            document.body.appendChild(particle);

            const angle = Math.random() * Math.PI * 2;
            const distance = 10 + Math.random() * 15;
            const startX = centerX + Math.cos(angle) * distance;
            const startY = centerY + Math.sin(angle) * distance;

            const endDistance = 30 + Math.random() * 50;
            const endX = Math.cos(angle) * endDistance;
            const endY = Math.sin(angle) * endDistance;

            const rotation = Math.random() * 360;

            const size = 4 + Math.random() * 6;

            const color =
                honeyColors[Math.floor(Math.random() * honeyColors.length)];

            const duration = 800 + Math.random() * 1200;

            // Aplicar estilos a la partícula
            particle.style.width = `${size}px`;
            particle.style.height = `${size}px`;
            particle.style.backgroundColor = color;
            particle.style.left = `${startX}px`;
            particle.style.top = `${startY}px`;
            particle.style.opacity = "0.8";
            particle.style.setProperty("--end-x", `${endX}px`);
            particle.style.setProperty("--end-y", `${endY}px`);
            particle.style.setProperty("--rotation", `${rotation}deg`);
            particle.style.setProperty("--duration", `${duration}ms`);

            setTimeout(() => {
                particle.remove();
            }, duration);
        }
    }

    function init() {
        wrapDropdownContent();
        wrapAvatar();

        if (userDropdownBtn) {
            userDropdownBtn.addEventListener("click", toggleDropdown);
            userDropdownBtn.setAttribute("aria-expanded", "false");
            userDropdownBtn.addEventListener("click", function (e) {});
        }

        document.addEventListener("click", handleOutsideClick);
        document.addEventListener("keydown", handleEscKey);
        window.addEventListener("scroll", handleScroll);

        addMenuItemEffects();

        handleScroll();
    }
    init();

    const styleElement = document.createElement("style");
    styleElement.textContent = `
        @keyframes ripple {
            to {
                transform: scale(2);
                opacity: 0;
            }
        }
        
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
            100% { transform: translateY(0px); }
        }
        
        @keyframes pulse {
            0% { 
                box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.4), 0 0 0 0 rgba(251, 191, 36, 0.4);
                transform: scale(1);
            }
            50% { 
                box-shadow: 0 0 10px 3px rgba(245, 158, 11, 0.2), 0 0 15px 5px rgba(251, 191, 36, 0.1);
                transform: scale(1.05);
            }
            100% { 
                box-shadow: 0 0 0 0 rgba(245, 158, 11, 0), 0 0 0 0 rgba(251, 191, 36, 0);
                transform: scale(1);
            }
        }
        
        .navbar-logo {
            animation: float 6s ease-in-out infinite;
        }
        
        .user-avatar-container {
            animation: pulse 3s infinite;
            border-radius: 50% !important; /* Forzar forma circular */
        }
    `;
    document.head.appendChild(styleElement);
});
