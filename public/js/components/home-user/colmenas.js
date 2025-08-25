document.addEventListener("DOMContentLoaded", function () {
    const cards = document.querySelectorAll(".colmena-card");
    let activeTooltip = null;
    let isHoveringCards = false;
    let qrCache = new Map();
    let currentCard = null;

    function preloadQR(dataTooltip) {
        return new Promise((resolve, reject) => {
            // Verificar cache primero
            if (qrCache.has(dataTooltip)) {
                resolve(qrCache.get(dataTooltip));
                return;
            }

            const tempDiv = document.createElement("div");
            tempDiv.innerHTML = dataTooltip;
            const img = tempDiv.querySelector("img");

            if (!img) {
                const fallbackHtml = `<div style="width: 100px; height: 100px; background: #f3f4f6; border-radius: 0.375rem; display: flex; align-items: center; justify-content: center; color: #6b7280; font-size: 0.75rem; text-align: center;">QR no<br>disponible</div>`;
                qrCache.set(dataTooltip, fallbackHtml);
                resolve(fallbackHtml);
                return;
            }

            const newImg = new Image();
            let resolved = false;

            newImg.onload = () => {
                if (!resolved) {
                    resolved = true;
                    qrCache.set(dataTooltip, dataTooltip);
                    resolve(dataTooltip);
                }
            };

            newImg.onerror = () => {
                if (!resolved) {
                    resolved = true;
                    const fallbackHtml = `<div style="width: 100px; height: 100px; background: #fecaca; border-radius: 0.375rem; display: flex; align-items: center; justify-content: center; color: #ef4444; font-size: 0.75rem; text-align: center;">QR no<br>disponible</div>`;
                    qrCache.set(dataTooltip, fallbackHtml);
                    resolve(fallbackHtml);
                }
            };

            setTimeout(() => {
                if (!resolved) {
                    resolved = true;
                    qrCache.set(dataTooltip, dataTooltip);
                    resolve(dataTooltip);
                }
            }, 1500);

            newImg.src = img.src;
        });
    }

    // Función simplificada para obtener QR
    function getQRContent(dataTooltip) {
        if (qrCache.has(dataTooltip)) {
            return Promise.resolve(qrCache.get(dataTooltip));
        }

        // Si no está en cache, usar directamente el contenido
        qrCache.set(dataTooltip, dataTooltip);
        return Promise.resolve(dataTooltip);
    }

    // Precargar todos los QRs al inicio
    function preloadAllQRs() {
        cards.forEach((card) => {
            const dataTooltip = card.getAttribute("data-tooltip");
            if (dataTooltip && !qrCache.has(dataTooltip)) {
                setTimeout(() => {
                    preloadQR(dataTooltip).catch(() => {});
                }, Math.random() * 2000);
            }
        });
    }

    function clearTooltip() {
        if (activeTooltip && activeTooltip.parentNode) {
            activeTooltip.style.opacity = "0";
            setTimeout(() => {
                if (activeTooltip && activeTooltip.parentNode) {
                    activeTooltip.remove();
                }
                activeTooltip = null;
                currentCard = null;
            }, 200);
        }
    }

    function positionTooltip(tooltip, rect) {
        const viewportWidth = window.innerWidth;
        const viewportHeight = window.innerHeight;
        const scrollTop = window.pageYOffset;
        const scrollLeft = window.pageXOffset;

        const tooltipWidth = 130;
        const tooltipHeight = 130;
        const margin = 15;

        let top, left;
        let position = "top";

        left = rect.left + rect.width / 2 - tooltipWidth / 2;
        top = rect.top - tooltipHeight - margin;

        if (top < scrollTop + 20) {
            top = rect.bottom + margin;
            position = "bottom";

            if (top + tooltipHeight > scrollTop + viewportHeight - 20) {
                top = rect.top + rect.height / 2 - tooltipHeight / 2;

                if (rect.right + tooltipWidth + margin < viewportWidth - 20) {
                    left = rect.right + margin;
                    position = "right";
                } else {
                    left = rect.left - tooltipWidth - margin;
                    position = "left";
                }
            }
        }

        if (position === "top" || position === "bottom") {
            if (left < 20) {
                left = 20;
            } else if (left + tooltipWidth > viewportWidth - 20) {
                left = viewportWidth - tooltipWidth - 20;
            }
        }

        if (position === "left" || position === "right") {
            if (top < scrollTop + 20) {
                top = scrollTop + 20;
            } else if (top + tooltipHeight > scrollTop + viewportHeight - 20) {
                top = scrollTop + viewportHeight - tooltipHeight - 20;
            }
        }

        tooltip.style.top = `${top + scrollTop}px`;
        tooltip.style.left = `${left + scrollLeft}px`;
        tooltip.className = `qr-tooltip ${position} show`;
    }

    // Función principal para mostrar tooltip
    function showTooltip(card) {
        const rect = card.getBoundingClientRect();
        const dataTooltip = card.getAttribute("data-tooltip");

        if (currentCard === card && activeTooltip) {
            // Misma tarjeta, solo reposicionar
            positionTooltip(activeTooltip, rect);
            return;
        }

        // Obtener contenido QR rápidamente
        getQRContent(dataTooltip)
            .then((qrContent) => {
                // Verificar que aún estamos en la misma tarjeta
                if (currentCard !== card) return;

                if (activeTooltip) {
                    // Actualizar tooltip existente
                    activeTooltip.innerHTML = qrContent;
                    positionTooltip(activeTooltip, rect);
                } else {
                    // Crear nuevo tooltip
                    const qrTooltip = document.createElement("div");
                    qrTooltip.classList.add("qr-tooltip");
                    qrTooltip.innerHTML = qrContent;
                    document.body.appendChild(qrTooltip);

                    // Posicionar y mostrar
                    qrTooltip.style.display = "block";
                    positionTooltip(qrTooltip, rect);
                    activeTooltip = qrTooltip;
                }
            })
            .catch((error) => {
                console.warn("Error al cargar QR:", error);
                if (activeTooltip && currentCard === card) {
                    activeTooltip.innerHTML = `<div style="width: 100px; height: 100px; background: #fecaca; border-radius: 0.375rem; display: flex; align-items: center; justify-content: center; color: #ef4444; font-size: 0.75rem; text-align: center;">Error QR</div>`;
                    positionTooltip(activeTooltip, rect);
                }
            });

        currentCard = card;
    }

    // Event listeners para las tarjetas
    cards.forEach((card) => {
        card.addEventListener("mouseenter", function () {
            isHoveringCards = true;
            showTooltip(this);
        });

        card.addEventListener("mouseleave", function () {
            isHoveringCards = false;

            // Dar un pequeño tiempo antes de ocultar
            setTimeout(() => {
                if (!isHoveringCards) {
                    clearTooltip();
                }
            }, 50);
        });
    });

    // Detectar cuando el mouse sale del área de colmenas
    const grids = document.querySelectorAll(".colmenas-grid");
    grids.forEach((grid) => {
        grid.addEventListener("mouseleave", function () {
            isHoveringCards = false;
            setTimeout(() => {
                if (!isHoveringCards) {
                    clearTooltip();
                }
            }, 100);
        });
    });

    // Limpiar tooltip en eventos globales
    window.addEventListener("scroll", () => {
        if (!isHoveringCards) clearTooltip();
    });

    window.addEventListener("resize", () => {
        if (!isHoveringCards) clearTooltip();
    });

    document.addEventListener("click", clearTooltip);

    // Inicializar precarga en background
    setTimeout(preloadAllQRs, 1000);
});
