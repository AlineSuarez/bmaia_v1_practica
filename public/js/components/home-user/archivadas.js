(() => {
    const seleccionados = new Set();

    function actualizarBotonesAccion() {
        let wrapper = document.getElementById('wrapper-acciones-multiples');
        
        if (seleccionados.size > 0) {
            if (!wrapper) {
                wrapper = document.createElement('div');
                wrapper.id = 'wrapper-acciones-multiples';
                wrapper.style.cssText = `
                    position: absolute;
                    display: flex;
                    justify-content: flex-end;
                    width: 100%;
                    left: -23px;
                    bottom: 80px;
                    gap: 15px;
                `;

                const editContainer = document.querySelector('.buttons-edit-container');
                if (editContainer) {
                    editContainer.parentNode.insertBefore(wrapper, editContainer.nextSibling);
                }
            }

            wrapper.innerHTML = `
                <button id="btn-restaurar-seleccionados" class="btn-inventory" style="background:#10b981;">
                    <i class="fa fa-undo"></i> Restaurar ${seleccionados.size} producto${seleccionados.size > 1 ? 's' : ''}
                </button>
                <button id="btn-eliminar-seleccionados" class="btn-inventory" style="background:#ef4444;">
                    <i class="fa fa-trash"></i> Eliminar ${seleccionados.size} producto${seleccionados.size > 1 ? 's' : ''}
                </button>
            `;

            // Ocultar acciones individuales
            document.querySelectorAll('td#btn-archivar, td#actions, th#actions').forEach(td => {
                td.style.display = 'none';
            });

            document.getElementById('btn-restaurar-seleccionados').addEventListener('click', restaurarSeleccionados);
            document.getElementById('btn-eliminar-seleccionados').addEventListener('click', eliminarSeleccionados);

        } else {
            if (wrapper) wrapper.remove();
            // Mostrar nuevamente acciones individuales
            document.querySelectorAll('td#btn-archivar, td#actions, th#actions').forEach(td => {
                td.style.display = '';
            });
        }
    }

    // Restaurar seleccionados
    function restaurarSeleccionados() {
        if (!confirm(`¿Restaurar ${seleccionados.size} producto${seleccionados.size > 1 ? 's' : ''}?`)) return;

        const ids = Array.from(seleccionados);
        const token = document.querySelector('meta[name="csrf-token"]').content;

        fetch('/inventario/restaurar/0', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
            },
            body: JSON.stringify({ ids })
        })
        .then(res => {
            if (!res.ok) throw new Error('Error al restaurar');
            return res.text();
        })
        .then(() => {
            alert(`${ids.length} producto${ids.length > 1 ? 's restaurados' : ' restaurado'} correctamente.`);
            seleccionados.clear();
            location.reload();
        })
        .catch(err => {
            console.error(err);
            alert('Ocurrió un error al restaurar los productos.');
        });
    }

    //Eliminar Seleccionados
    function eliminarSeleccionados() {
        if (!confirm(`¿Eliminar PERMANENTEMENTE ${seleccionados.size} producto${seleccionados.size > 1 ? 's' : ''}?`)) return;

        const ids = Array.from(seleccionados);
        const token = document.querySelector('meta[name="csrf-token"]').content;

        fetch('/inventario/destroy/0', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
            },
            body: JSON.stringify({
                ids,
                _method: 'DELETE'
            })
        })
        .then(res => {
            if (!res.ok) throw new Error('Error al eliminar');
            return res.text();
        })
        .then(() => {
            alert(`${ids.length} producto${ids.length > 1 ? 's eliminados' : ' eliminado'} correctamente.`);
            seleccionados.clear();
            location.reload();
        })
        .catch(err => {
            console.error(err);
            alert('Ocurrió un error al eliminar los productos.');
        });
    }

    // Manejo de checkbox
    function manejarCheckbox(e) {
        const cb = e.target;
        if (!cb.classList.contains('checkbox-seleccionar-producto')) return;
        const id = cb.dataset.productId;

        if (cb.checked) seleccionados.add(id);
        else seleccionados.delete(id);

        actualizarBotonesAccion();
    }

    function inicializarCheckboxes() {
        document.removeEventListener('change', manejarCheckbox);
        document.addEventListener('change', manejarCheckbox);

        document.querySelectorAll('.checkbox-seleccionar-producto').forEach(cb => {
            const id = cb.dataset.productId;
            cb.checked = seleccionados.has(id);
        });

        actualizarBotonesAccion();
    }

    inicializarCheckboxes();

    const fetchOriginal = window.fetchFilteredResults;
    if (typeof fetchOriginal === 'function') {
        const observer = new MutationObserver(() => inicializarCheckboxes());
        const listado = document.getElementById('inventario-listado');
        if (listado) observer.observe(listado, { childList: true, subtree: true });
    }

    window._reinicializarSeleccion = inicializarCheckboxes;

    document.querySelectorAll('.btn-observacion').forEach(btn => {
        const id = btn.id.replace('btn-openModalEditObservacion-', '');
        const modal = document.getElementById(`modalEditObservacion-${id}`);
        const cerrar = document.getElementById(`CancelEditarObservacion-${id}`);

        if (!modal) return;

        btn.addEventListener('click', () => {
            modal.style.display = 'block';
        });

        if (cerrar) {
            cerrar.addEventListener('click', () => {
                modal.style.display = 'none';
            });
        }

        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape" && modal.style.display === 'block') {
                modal.style.display = 'none';
            }
        });

        document.addEventListener("click", (e) => {
            if (
                modal.style.display === 'block' &&
                !modal.contains(e.target) &&
                !btn.contains(e.target)
            ) {
                modal.style.display = 'none';
            }
        });

    });

})();

function fetchArchivadas(page = 1) {
    const url = `/inventario/archivadas?page=${page}`;

    fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.text())
    .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');

        const nuevoContenedorPorId = doc.querySelector('#inventario-listado');
        const nuevoContenedorPorClase = doc.querySelector('.inventory-table-container');

        const destino = document.querySelector('#inventario-listado') || document.querySelector('.inventory-table-container');

        if (!destino) {
            console.error('No se encontró el contenedor destino (#inventario-listado ni .inventory-table-container) en la página actual.');
            return;
        }

        if (nuevoContenedorPorId) {
            destino.innerHTML = nuevoContenedorPorId.innerHTML;
        } else if (nuevoContenedorPorClase) {
            destino.innerHTML = nuevoContenedorPorClase.innerHTML;
        } else {
            console.warn('La respuesta AJAX no contiene #inventario-listado ni .inventory-table-container. No se reemplazó el contenido.');
            return;
        }

        if (typeof window._reinicializarSeleccion === 'function') {
            window._reinicializarSeleccion();
        } else if (typeof window._reinicializarSeleccionArchivadas === 'function') {
            window._reinicializarSeleccionArchivadas();
        }

        if (typeof configurarLinksPaginacion === 'function') {
            configurarLinksPaginacion();
        } else {
            document.querySelectorAll('.pagination a').forEach(link => {
                link.addEventListener('click', e => {
                    e.preventDefault();
                    const urlObj = new URL(e.target.closest('a').href);
                    const page = urlObj.searchParams.get('page') || 1;
                    fetchArchivadas(page);
                });
            });
        }
    })
    .catch(err => {
        console.error('Error al cargar archivadas:', err);
    });
}

function configurarLinksPaginacion() {
    document.querySelectorAll('.pagination a').forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();
            const url = new URL(e.target.closest('a').href);
            const page = url.searchParams.get('page') || 1;
            fetchArchivadas(page);
        });
    });
}

configurarLinksPaginacion();
