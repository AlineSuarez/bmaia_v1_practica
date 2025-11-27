(function () {
    console.log('explorador.js cargado');

    // Función auxiliar para obtener siempre los elementos actualizados
    function getElems() {
        return {
            mapBox: document.getElementById('mapContainer'),
            tooltip: document.getElementById('mapTooltip'),
            regionName: document.getElementById('regionName'),
            regionCode: document.getElementById('regionCode'),
        };
    }

    function resetInfo() {
        const { tooltip, regionName, regionCode } = getElems();

        if (!regionName || !regionCode || !tooltip) return;

        regionName.textContent = 'Pasa el cursor sobre una región del mapa';
        regionCode.textContent = 'ID: —';
        tooltip.style.display = 'none';
    }

    // Hacemos la función global para usarla en onload del <object>
    window.initChileMap = function (obj) {
        console.log('initChileMap llamado');

        const { mapBox, tooltip, regionName, regionCode } = getElems();

        const svgDoc = obj.contentDocument || obj.getSVGDocument();
        if (!svgDoc) {
            console.warn('No se pudo acceder al contenido de chile.svg');
            return;
        }

        const svgRoot = svgDoc.documentElement;

        // ====== ZOOM DENTRO DEL SVG ======
        svgRoot.removeAttribute('width');
        svgRoot.removeAttribute('height');
        svgRoot.setAttribute('viewBox', '430 0 200 708');
        svgRoot.setAttribute('preserveAspectRatio', 'xMidYMid meet');

        // ====== ESTILOS DE REGIONES ======
        const styleEl = svgDoc.createElementNS('http://www.w3.org/2000/svg', 'style');
        styleEl.textContent = `
            path[title]{
                fill:#1f2f48;
                stroke:#9fb3c7;
                stroke-width:0.7;
                cursor:pointer;
                transition: fill 0.15s ease, stroke 0.15s ease, stroke-width 0.15s ease;
            }
            path[title].is-hovered{
                fill:#1ee2a4;
                stroke:#ffffff;
                stroke-width:1.2;
            }
        `;
        svgRoot.appendChild(styleEl);

        const regions = svgDoc.querySelectorAll('path[title]');

        regions.forEach(function (regionPath) {
            regionPath.addEventListener('mouseenter', function (event) {
                const title = regionPath.getAttribute('title') || 'Zona sin nombre';
                const id = regionPath.id || '';

                if (regionName) regionName.textContent = title;
                if (regionCode) regionCode.textContent = id ? ('ID: ' + id) : 'ID: —';

                regions.forEach(r => r.classList.remove('is-hovered'));
                regionPath.classList.add('is-hovered');

                if (tooltip) {
                    tooltip.style.display = 'block';
                    tooltip.textContent = title;
                }
            });

            regionPath.addEventListener('mousemove', function (event) {
                if (!tooltip || !mapBox) return;

                const rect = mapBox.getBoundingClientRect();
                const x = event.clientX - rect.left;
                const y = event.clientY - rect.top;

                tooltip.style.left = x + 'px';
                tooltip.style.top = (y - 10) + 'px';
            });

            regionPath.addEventListener('mouseleave', function () {
                if (tooltip) tooltip.style.display = 'none';
                regionPath.classList.remove('is-hovered');
            });

            regionPath.addEventListener('click', function () {
                const title = regionPath.getAttribute('title') || 'Zona sin nombre';
                const id = regionPath.id || '';

                if (regionName) regionName.textContent = title;
                if (regionCode) regionCode.textContent = id ? ('ID: ' + id) : 'ID: —';
            });
        });
    };

    // Esperamos a que el DOM esté listo para resetear el texto inicial
    document.addEventListener('DOMContentLoaded', resetInfo);
})();


