// public/js/hoja_ruta/capacidad.js

(function () {
    console.log('capacidad.js cargado');

    document.addEventListener('DOMContentLoaded', function () {
        const root = document.getElementById('capacity-root');
        if (!root) return;

        const parseJSONAttr = (attr, fallback) => {
            const raw = root.getAttribute(attr);
            if (!raw) return fallback;
            try {
                return JSON.parse(raw);
            } catch (e) {
                console.warn('No se pudo parsear', attr, e);
                return fallback;
            }
        };

        const apiariosFijos      = parseJSONAttr('data-apiarios-fijos', []);
        const apiariosBase       = parseJSONAttr('data-apiarios-base', []);
        const apiariosTemporales = parseJSONAttr('data-apiarios-temporales', []);
        const apiariosArchivados = parseJSONAttr('data-apiarios-archivados', []);
        const apiKey             = root.getAttribute('data-api-key') || '';

        if (typeof window.initZonificacion === 'function') {
            window.initZonificacion(
                apiariosFijos,
                apiariosBase,
                apiariosTemporales,
                apiariosArchivados,
                apiKey
            );
        } else {
            console.warn('window.initZonificacion no está definido');
        }
    });
})();
