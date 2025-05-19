document.addEventListener("DOMContentLoaded", () => {
    const navbar = document.querySelector(".navbar");

    if (navbar) {
        window.addEventListener("scroll", () => {
            if (window.scrollY > 50) {
                navbar.classList.add("scrolled");
            } else {
                navbar.classList.remove("scrolled");
            }
        });
    }

    // Add floating particles dynamically
    const particles = document.querySelector(".floating-particles");
    if (particles) {
        for (let i = 0; i < 6; i++) {
            const particle = document.createElement("span");
            particle.classList.add("particle");
            particle.style.width = `${Math.random() * 10 + 5}px`;
            particle.style.height = particle.style.width;
            particle.style.left = `${Math.random() * 100}%`;
            particle.style.top = `${Math.random() * 100}%`;
            particle.style.animationDuration = `${Math.random() * 20 + 10}s`;
            particle.style.animationDelay = `${Math.random() * 5}s`;
            particles.appendChild(particle);
        }
    }

    // Funcionalidad para el botón "back to top"
    const backToTopBtn = document.getElementById("back-to-top");
    if (backToTopBtn) {
        backToTopBtn.addEventListener("click", function (e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: "smooth" });
        });
    }
});
