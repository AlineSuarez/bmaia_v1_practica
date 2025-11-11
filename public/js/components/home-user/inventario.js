document.addEventListener('DOMContentLoaded', () => {

    function title(palabra) {
        return palabra
            .trim()
            .replace(/\s+/g, ' ')
            .split(' ')
            .map(word =>
                word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()
            )
            .join(' ');
    }
    
    function limpiarBotonesVacios() {
        document.querySelectorAll('.btn-observacion').forEach(btn => {
            const text = btn.textContent.trim();
            if (text.includes('{{') || text.includes('Str::limit') || text === '...' || !text) {
                btn.textContent = '';
            }
        });
    }
    
    limpiarBotonesVacios();

    function actualizarBotonesObservacion() {
        document.querySelectorAll('.btn-observacion').forEach(btn => {
            const contenido = btn.textContent.trim();
            if (contenido === '' || contenido === null || contenido === '...') {
                btn.textContent = '';
            }
        });
    }

    //Modal de filtros
    const btnModalFiltros = document.getElementById('modalFiltrar')
    const divModalFiltro = document.getElementById('inventario-filtros')

    // Cerrar Modal de Filtros
    window.closeFilterModal = function() {
        if (divModalFiltro) {
            divModalFiltro.style.display = 'none';
        }
    }

    // Abrir Modal de Filtros
    document.addEventListener('click', function(e) {
        if (e.target.id === 'modalFiltrar') {
            divModalFiltro.style.display = 'block';
        }
    });

    // Cerrar Modal de filtros al apretar ESC
    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape" && divModalFiltro.style.display === 'block') {
            divModalFiltro.style.display = 'none';
        }
    });

    // Cerrar Modal al hacer click fuera de la modal
    document.addEventListener("click", (e) => {
        if (
            divModalFiltro.style.display === 'block' &&
            !divModalFiltro.contains(e.target) &&
            e.target !== btnModalFiltros
        ) {
            divModalFiltro.style.display = 'none';
        }
    });

    const btnClearFilters = document.getElementById('btn-clear-filters');
    const searchInput = document.getElementById('searchInput');
    const categorySelect = document.getElementById('categorySelect');
    const subcategoryCheckboxes = document.querySelectorAll('#filter-subcategories input[name="subcategory_id[]"]');

    searchInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            fetchFilteredResults();
        }
    });

    searchInput.addEventListener('input', function () {
        if (this.value.trim() === '') {
            fetchFilteredResults();
        }
    });

    function getSubcategoryCheckboxes() {
        const filterContainer = document.querySelector('#inventario-filtros');
        return filterContainer
            ? filterContainer.querySelectorAll('input[name="subcategory_id[]"]')
            : [];
    }

    function actualizarBotonQuitarFiltros() {
        const hasSearch = searchInput.value.trim() !== '';
        const hasCategory = categorySelect.value !== '';
        const hasSubcategory = Array.from(getSubcategoryCheckboxes()).some(cb => cb.checked);

        if (hasSearch || hasCategory || hasSubcategory) {
            btnClearFilters.style.display = 'inline-block';
            btnClearFilters.classList.add('btn-cancel-observacion');
        } else {
            btnClearFilters.style.display = 'none';
            btnClearFilters.classList.remove('btn-cancel-observacion');
        }
    }

    if (btnClearFilters) {
        btnClearFilters.addEventListener('click', () => {
            searchInput.value = '';
            categorySelect.value = '';
            getSubcategoryCheckboxes().forEach(cb => cb.checked = false);
            fetchFilteredResults();
            actualizarBotonQuitarFiltros();
        });
    }

    function fetchFilteredResults(page = 1) {
        const data = {
            q: searchInput.value,
            category_id: categorySelect.value,
            subcategory_id: Array.from(getSubcategoryCheckboxes())
                .filter(cb => cb.checked)
                .map(cb => cb.value)
        };

        fetch(`/inventario/search?page=${page}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        })
        .then(res => res.text())
        .then(html => {
            document.querySelector('#inventario-listado').innerHTML = html;
            bindFilterEvents();
            bindPaginationLinks();
            bindEditButtons()
            actualizarBotonesObservacion();
            actualizarBotonQuitarFiltros();

            if (window._reinicializarSeleccion) window._reinicializarSeleccion();

            const btnEditar = document.getElementById('btn-editar');
            const btnCancelar = document.getElementById('btn-cancelar');
            const btnGuardar = document.getElementById('btn-guardar-cambios');

            if (window._modoEdicionActivo) {
                document.querySelectorAll('.edit-mode').forEach(el => el.hidden = false);
                document.querySelectorAll('.view-mode').forEach(el => el.hidden = true);
                document.querySelectorAll('th#actions, td#btn-archivar').forEach(el => el.style.display = 'none');
                document.querySelectorAll('.checkbox-seleccionar-producto').forEach(cb => cb.style.display = 'none');
                btnEditar.textContent = 'Editar (Activo)';
                btnCancelar.style.display = 'inline-flex';
                btnGuardar.style.display = 'none';
                if (btnEditar) {
                    btnEditar.innerHTML = '<i class="fa fa-pen"></i> Editar (Activo)';
                    btnEditar.style.pointerEvents = 'none';
                    btnEditar.style.opacity = '0.6';
                }
                if (btnCancelar) btnCancelar.style.display = 'inline-flex';
                if (btnGuardar) btnGuardar.style.display = window._inventarioCambios?.length > 0 ? 'inline-flex' : 'none';
            
            } else {
                document.querySelectorAll('.edit-mode').forEach(el => el.hidden = true);
                document.querySelectorAll('.view-mode').forEach(el => el.hidden = false);
                
                if (window._productosSeleccionados && window._productosSeleccionados.size > 0) {
                    document.querySelectorAll('th#actions, td#btn-archivar').forEach(el => el.style.display = 'none');
                    document.querySelectorAll('.checkbox-seleccionar-producto').forEach(cb => cb.style.display = '');
                } else {
                    document.querySelectorAll('th#actions, td#btn-archivar').forEach(el => el.style.display = '');
                    document.querySelectorAll('.checkbox-seleccionar-producto').forEach(cb => cb.style.display = '');
                }
                
                if (btnEditar) {
                    btnEditar.innerHTML = 'Editar';
                    btnEditar.style.pointerEvents = 'auto';
                    btnEditar.style.opacity = '1';
                }
                if (btnCancelar) btnCancelar.style.display = 'none';
                if (btnGuardar) btnGuardar.style.display = 'none';
            }

            if (window._inventarioCambios?.length) {
                window._inventarioCambios.forEach(change => {
                    const tr = document.querySelector(`input.product-id[value="${change.id}"]`)?.closest('tr');
                    if (!tr) return;
                    tr.classList.add('producto-editando');

                    for (const [field, value] of Object.entries(change)) {
                        if (field === 'id') continue;
                        const input = tr.querySelector(`[name="${field}"]`);
                        if (!input) continue;

                        if (Array.isArray(value)) {
                            value.forEach(v => {
                                const cb = tr.querySelector(`input[name="subcategories[]"][value="${v}"]`);
                                if (cb) cb.checked = true;
                            });
                        } else {
                            input.value = value;
                        }
                    }
                });
            }

            if (window._inventarioCambios?.length > 0) {
                btnGuardar.style.display = 'inline-flex';
            }

        })
        .catch(err => console.error('Error al buscar:', err));
    }

    function bindEditButtons() {
        const btnEditar = document.getElementById('btn-editar');
        const btnGuardar = document.getElementById('btn-guardar-cambios');
        const btnCancelar = document.getElementById('btn-cancelar');
        const editables = document.querySelectorAll('.edit-mode');
        const visibles = document.querySelectorAll('.view-mode');

        if (!btnEditar || !btnGuardar || !btnCancelar) return;

        let modoEdicion = window._modoEdicionActivo || false;

        if (modoEdicion) document.querySelectorAll('th#actions, td#btn-archivar').forEach(el => el.style.display = 'none');

        const valoresOriginales = new Map();

        function guardarOriginales() {
            valoresOriginales.clear();
            document.querySelectorAll('.inventory-table tbody tr').forEach(tr => {
                const productId = tr.querySelector('input.product-id').value;
                valoresOriginales.set(productId, {});

                tr.querySelectorAll('.edit-mode:not([name="subcategories[]"])').forEach(input => {
                    if (input.tagName === 'DIV' || input.tagName === 'BUTTON') return;
                    input.querySelectorAll && input.querySelectorAll('input,select,textarea').forEach(inner => {
                        valoresOriginales.get(productId)[inner.name || inner.dataset.field || inner.id] = inner.value;
                    });
                    if (['INPUT','SELECT','TEXTAREA'].includes(input.tagName)) {
                        valoresOriginales.get(productId)[input.name || input.id] = input.value;
                    }
                });

                const subcategoriesChecked = Array.from(tr.querySelectorAll('input[name="subcategories[]"]:checked'))
                    .map(cb => cb.value);
                valoresOriginales.get(productId)['subcategories[]'] = subcategoriesChecked;

                const observacionTa = tr.querySelector('.observacion-input');
                valoresOriginales.get(productId)['observacion'] = observacionTa ? observacionTa.value : '';
            });
        }

        guardarOriginales();

        btnGuardar.style.display = 'none';
        btnCancelar.style.display = modoEdicion ? 'inline-flex' : 'none';

        btnEditar.onclick = () => {
            modoEdicion = !modoEdicion;
            window._modoEdicionActivo = modoEdicion;

            btnEditar.textContent = modoEdicion ? 'Editar (Activo)' : 'Editar';
            btnCancelar.style.display = modoEdicion ? 'inline-flex' : 'none';
            btnGuardar.style.display = 'none';

            editables.forEach(el => el.hidden = !modoEdicion);
            visibles.forEach(el => el.hidden = modoEdicion);
            
            const btnArchivarMultiple = document.getElementById('btn-eliminar-seleccionados');
            if (btnArchivarMultiple) btnArchivarMultiple.style.display = modoEdicion ? 'none' : '';

            document.querySelectorAll('th#actions, td#btn-archivar').forEach(el => {
                el.style.display = modoEdicion ? 'none' : '';
            });
            
            document.querySelectorAll('.checkbox-seleccionar-producto').forEach(cb => {
                cb.style.display = modoEdicion ? 'none' : '';
            });

            if (modoEdicion) {
                document.querySelectorAll('.inventory-table tbody tr').forEach(tr => {
                    tr.classList.remove('producto-seleccionado');
                });
                
                guardarOriginales();
                
                document.querySelectorAll('.btn-observacion').forEach(btn => {
                    const text = btn.textContent.trim();
                    if (text.includes('{{') || text === '...' || text === '') {
                        btn.textContent = '';
                    }
                });
            }
            
            if (modoEdicion) {
                btnEditar.innerHTML = '<i class="fa fa-pen"></i> Editar (Activo)';
                btnEditar.style.pointerEvents = 'none';
                btnEditar.style.opacity = '0.6';
            }
        };

        btnCancelar.onclick = () => {
            modoEdicion = false;
            window._modoEdicionActivo = false;

            btnEditar.textContent = 'Editar';
            btnCancelar.style.display = 'none';
            btnGuardar.style.display = 'none';
            
            btnEditar.style.pointerEvents = 'auto';
            btnEditar.style.opacity = '1';
            btnEditar.innerHTML = 'Editar';

            document.querySelectorAll('th#actions, td#btn-archivar').forEach(el => {
                el.style.display = '';
            });

            const btnArchivarMultiple = document.getElementById('btn-eliminar-seleccionados');
            if (btnArchivarMultiple) btnArchivarMultiple.style.display = '';
            
            document.querySelectorAll('.checkbox-seleccionar-producto').forEach(cb => {
                cb.style.display = '';
            });

            document.querySelectorAll('.inventory-table tbody tr').forEach(tr => {
                const productId = tr.querySelector('input.product-id').value;
                const originales = valoresOriginales.get(productId);
                if (!originales) return;

                Object.entries(originales).forEach(([name, value]) => {
                    if (name === 'subcategories[]') {
                        const checkboxes = tr.querySelectorAll('input[name="subcategories[]"]');
                        checkboxes.forEach(cb => {
                            cb.checked = value.includes(cb.value);
                        });
                    } else if (name === 'observacion') {
                        const modalTa = tr.querySelector('.observacion-input');
                        if (modalTa) modalTa.value = value;

                        const btnInline = tr.querySelector(`#btn-openModalEditObservacionInline-${productId}`);
                        if (btnInline) {
                            btnInline.textContent = value ? (value.length > 15 ? value.substring(0,15) + '...' : value) : '';
                        }
                    } else {
                        const input = tr.querySelector(`[name="${name}"]`) || tr.querySelector(`#${name}`);
                        if (!input) return;
                        input.value = value;
                    }
                });

                tr.querySelectorAll('.edit-mode').forEach(el => el.hidden = true);
                tr.querySelectorAll('.view-mode').forEach(el => el.hidden = false);
            });

            window._inventarioCambios.length = 0;
            fetchFilteredResults();
            btnGuardar.style.display = 'none';
            guardarOriginales();
        };

        if (!btnGuardar) return;

        btnGuardar.onclick = async () => {
            if (!window._inventarioCambios?.length) {
                alert('No hay cambios para guardar.');
                return;
            }

            const errores = [];
            const productosConError = [];

            window._inventarioCambios.forEach((cambio, index) => {
                const tr = document.querySelector(`input.product-id[value="${cambio.id}"]`)?.closest('tr');
                const nombreProductoOriginal = tr?.querySelector('.view-mode')?.textContent.trim() || `Producto ${cambio.id}`;
                let erroresProducto = [];

                if (cambio.nombreProducto !== undefined) {
                    const nombre = cambio.nombreProducto.trim();
                    if (nombre === '') {
                        erroresProducto.push('El nombre no puede estar vacío');
                    } else if (/^\d+$/.test(nombre)) {
                        erroresProducto.push('El nombre no puede ser solo números');
                    } else if (/^[^a-zA-Z0-9]+$/.test(nombre)) {
                        erroresProducto.push('El nombre no puede contener solo signos');
                    }
                }

                if (cambio.cantidad !== undefined) {
                    const cantidad = parseFloat(cambio.cantidad);
                    if (isNaN(cantidad)) {
                        erroresProducto.push('La cantidad debe ser un número válido');
                    } else if (cantidad < 0) {
                        erroresProducto.push('La cantidad no puede ser negativa');
                    }
                }

                if (cambio.precio !== undefined) {
                    const precio = parseFloat(cambio.precio);
                    if (isNaN(precio)) {
                        erroresProducto.push('El precio debe ser un número válido');
                    } else if (precio < 0) {
                        erroresProducto.push('El precio no puede ser negativo');
                    }
                }

                if (cambio['subcategories[]'] !== undefined) {
                    const subcategorias = cambio['subcategories[]'];
                    if (!Array.isArray(subcategorias) || subcategorias.length === 0) {
                        erroresProducto.push('Debe seleccionar al menos una subcategoría');
                    }
                }

                if (erroresProducto.length > 0) {
                    productosConError.push({
                        nombre: nombreProductoOriginal,
                        errores: erroresProducto
                    });
                }
            });

            if (productosConError.length > 0) {
                let mensajeError = 'Se encontraron los siguientes errores:\n\n';
                productosConError.forEach(prod => {
                    mensajeError += `- ${prod.nombre}:\n`;
                    prod.errores.forEach(error => {
                        mensajeError += `   • ${error}\n`;
                    });
                    mensajeError += '\n';
                });
                alert(mensajeError);
                return;
            }

            if (!confirm('¿Guardar los cambios realizados en el inventario?')) return;

            const routeElement = document.getElementById('update-route');
            const updateUrl = routeElement.dataset.updateMultiple;

            try {
                const productosParaBackend = window._inventarioCambios.map(prod => {
                    const copy = { ...prod };
                    if (copy['subcategories[]']) {
                        copy['subcategories'] = copy['subcategories[]'];
                        delete copy['subcategories[]'];
                    }
                    return copy;
                });
                const res = await fetch(updateUrl, {
                    method: "PATCH",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ productos: productosParaBackend })
                });

                if (!res.ok) throw new Error('Error al actualizar');

                alert('Cambios guardados correctamente.');
                window._inventarioCambios.length = 0;
                location.reload();
            } catch (err) {
                console.error(err);
                alert('Ocurrió un error al guardar los cambios.');
            }
        };

        document.addEventListener('input', (e) => {
            if (!modoEdicion) return;
            if (e.target.closest('.edit-mode') || e.target.classList.contains('observacion-input')) {
                const tr = e.target.closest('tr');
                if (tr) tr.classList.add('producto-editando');
                btnGuardar.style.display = 'inline-flex';
            }
        });

    }

    function bindFilterEvents() {
        getSubcategoryCheckboxes().forEach(cb => {
            cb.addEventListener('change', fetchFilteredResults);
        });
    }

    function bindPaginationLinks() {
        document.querySelectorAll('#inventario-listado .pagination a').forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                const url = new URL(this.href);
                const page = url.searchParams.get('page') || 1;
                fetchFilteredResults(page);
            });
        });
    }

    bindFilterEvents();
    bindPaginationLinks();
    bindEditButtons();
    actualizarBotonQuitarFiltros();

    searchInput.addEventListener('change', fetchFilteredResults);
    categorySelect.addEventListener('change', fetchFilteredResults);
    subcategoryCheckboxes.forEach(cb => cb.addEventListener('change', fetchFilteredResults));

    
    if (btnClearFilters) {
        btnClearFilters.addEventListener('click', () => {
            searchInput.value = '';
            categorySelect.value = '';
            getSubcategoryCheckboxes().forEach(cb => cb.checked = false);
            fetchFilteredResults();
        });
    }

    // Modales de observación 
    // Mostrar/Ocultar Modal
    document.addEventListener('click', function(event) {
        if (event.target.id.startsWith('btn-openModalEditObservacion-')) {
            const id = event.target.id.split('-').pop();
            document.getElementById(`modalEditObservacion-${id}`).style.display = 'block';
        }
        if (event.target.id.startsWith('CancelEditarObservacion-')) {
            const id = event.target.id.split('-').pop();
            document.getElementById(`modalEditObservacion-${id}`).style.display = 'none';
        }
    });

    document.addEventListener('click', function(ev) {
        const btn = ev.target.closest('[id^="btn-openModalEditObservacionInline-"]');
        if (btn) {
            const id = btn.id.split('-').pop();
            const modal = document.getElementById(`modalEditObservacionInline-${id}`);
            if (!modal) return;
            const textarea = modal.querySelector('.observacion-input');
            const tr = btn.closest('tr');
            const inlineTa = tr?.querySelector('.observacion-input');
            if (inlineTa && inlineTa !== textarea) {
                textarea.value = inlineTa.value;
            }
            modal.style.display = 'block';
        }
    });

    document.addEventListener('click', function(ev) {
        if (ev.target.closest('.btn-close-modal') || ev.target.closest('.btn-cancel-observacion')) {
            const modal = ev.target.closest('.modal-observacion');
            if (modal) modal.style.display = 'none';
            return;
        }

        if (ev.target.classList.contains('btn-ok-observacion')) {
            const modal = ev.target.closest('.modal-observacion');
            if (!modal) return;
            const textarea = modal.querySelector('.observacion-input');
            const productId = textarea?.dataset.productId;
            if (!productId) { modal.style.display = 'none'; return; }

            const newValue = textarea.value.trim();

            const btnInline = document.getElementById(`btn-openModalEditObservacionInline-${productId}`);
            if (btnInline) btnInline.textContent = newValue.length > 15 ? newValue.substring(0,15) + '...' : newValue;

            if (typeof upsertChange === 'function') {
                upsertChange(productId, 'observacion', newValue);
            } else {
                window._inventarioCambios = window._inventarioCambios || [];
                const existing = window._inventarioCambios.find(x => x.id === productId);
                if (existing) existing.observacion = newValue;
                else window._inventarioCambios.push({ id: productId, observacion: newValue });
            }

            const btnGuardar = document.getElementById('btn-guardar-cambios');
            if (btnGuardar) btnGuardar.style.display = 'inline-flex';

            modal.style.display = 'none';
            return;
        }
    });

    // Cerrar Modal con ESC
    window.addEventListener('keydown', function(ev) {
        if (ev.key === 'Escape') {
            document.querySelectorAll('[id^="modalEditObservacionInline-"]').forEach(m => m.style.display = 'none');
        }
    });

    // Cerrar Modal con click fuera de modal
    document.addEventListener('click', function(ev) {
        document.querySelectorAll('[id^="modalEditObservacionInline-"]').forEach(modal => {
            if (modal.style.display === 'block' &&
                !modal.contains(ev.target) &&
                !ev.target.id.startsWith('btn-openModalEditObservacionInline-')) {
                modal.style.display = 'none';
            }
        });
    });

    document.querySelectorAll('[id^="modalEditObservacion-"] form').forEach(form => {
        form.addEventListener('submit', function(event) {
            event.preventDefault();

            const textarea = this.querySelector('textarea[name="observacion"]');
            const valor = textarea.value.trim();
            let errores = [];

            if (valor === '') errores.push('Debe ingresar una observación antes de enviar.');
            // if (/^\d+$/.test(valor)) errores.push('La observación no puede ser solo números.');
            if (/^[^a-zA-Z0-9]+$/.test(valor)) errores.push('La observación no puede contener solo signos.');

            if (errores.length === 0) {
                form.submit();
            } else {
                alert('Corrige los siguientes errores antes de enviar:\n\n' + errores.join('\n'));
            }
        });
    });

    window.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            document.querySelectorAll('[id^="modalEditObservacion-"]').forEach(modal => {
                modal.style.display = 'none';
            });
        }
    });

    document.addEventListener("click", (e) => {
        document.querySelectorAll('[id^="modalEditObservacion-"]').forEach(modal => {
            if (
                modal.style.display === 'block' &&
                !modal.contains(e.target) &&
                !e.target.id.startsWith('btn-openModalEditObservacion-')
            ) {
                modal.style.display = 'none';
            }
        });
    });

    // Modal de creación 
    const btnCreateProduct = document.getElementById('btn-openModalCreateProduct');
    const divModalCreateProduct = document.getElementById('modalCreateProduct');
    const btnCancelProductCreation = document.getElementById('cancelProductCreation');
    const formCrearProducto = document.querySelector('#modalCreateProduct form');

    btnCreateProduct?.addEventListener('click', function() {
        divModalCreateProduct.style.display = 'block';
    });

    btnCancelProductCreation?.addEventListener('click', function() {
        divModalCreateProduct.style.display = 'none';
    });

    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape" && divModalCreateProduct.style.display === 'block') {
            divModalCreateProduct.style.display = 'none';
        }
    });

    document.addEventListener("click", (e) => {
        if (
            divModalCreateProduct.style.display === 'block' &&
            !divModalCreateProduct.contains(e.target) &&
            e.target !== btnCreateProduct
        ) {
            divModalCreateProduct.style.display = 'none';
        }
    });

    formCrearProducto?.addEventListener('submit', function(event) {
        event.preventDefault();

        const nombreProductoInput = document.getElementById('create_nombre_producto');
        const cantidad = document.getElementById('create_cantidad_producto').value.trim();
        const precio = document.getElementById('create_precio_producto').value.trim();
        const categoria = document.getElementById('create_category_id').value;
        const subcategorias = document.querySelectorAll('input[name="subcategory_id[]"]:checked');

        let nombre = nombreProductoInput.value.trim();
        let errores = [];

        if (nombre === '') {
            errores.push('El nombre del producto es obligatorio.');
        } else {
            if (/^\d+$/.test(nombre)) errores.push('El nombre del producto no puede ser solo números.');
            if (/^[^a-zA-Z0-9]+$/.test(nombre)) errores.push('El nombre del producto no puede contener solo signos.');
            if (errores.length === 0) {
                nombre = title(nombre);
                nombreProductoInput.value = nombre;
            }
        }

        if (cantidad === '' || isNaN(cantidad)) {
            errores.push('Debe ingresar una cantidad válida.');
        } else if (parseFloat(cantidad) < 0) {
            errores.push('La cantidad debe ser un número positivo.');
        }

        if (precio === '' || isNaN(precio)) {
            errores.push('Debe ingresar un precio válido.');
        } else if (parseFloat(precio) < 0) {
            errores.push('El precio debe ser un número positivo.');
        }

        if (categoria === '') {
            errores.push('Debe seleccionar una categoría.');
        }

        if (subcategorias.length === 0) {
            errores.push('Debe seleccionar al menos una subcategoría.');
        }

        if (errores.length > 0) {
            alert('Corrige los siguientes errores:\n\n' + errores.join('\n'));
        } else {
            const formData = new FormData(formCrearProducto);

            fetch(formCrearProducto.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(async res => {
                if (!res.ok) throw new Error('Error al crear el producto');
                return res.json();
            })
            .then(async data => {
                if (data.status === 'duplicate') {
                    const confirmar = confirm(data.message);
                    if (confirmar) {
                        formData.append('confirmar', 'true');
                        
                        const res2 = await fetch(formCrearProducto.action, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: formData
                        });

                        if (!res2.ok) throw new Error('Error al crear el producto');
                        
                        const data2 = await res2.json();
                        
                        if (data2.status === 'success') {
                            alert('Producto creado correctamente.');
                            location.reload();
                        } else {
                            alert('No se pudo crear el producto.');
                        }
                    } else {
                        alert('El producto no fue creado.');
                    }
                } else if (data.status === 'success') {
                    alert('Producto creado correctamente.');
                    location.reload();
                } else {
                    alert('No se pudo crear el producto.');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Ocurrió un error al crear el producto.');
            });
        }
    });

    // Vista Preliminar de inventario (solo inventario)
    const routeElement = document.getElementById('inventario-preview-route');
    
    window.showInventarioPreview = function() {
        const modal = new bootstrap.Modal(document.getElementById("modalInventarioPreview"));
        const iframe = document.getElementById('iframeInventarioPreview');
        const cacheBuster = '?t=' + new Date().getTime();
        
        if (routeElement) {
            iframe.src = routeElement.dataset.inventarioPreview + cacheBuster;
        }
        
        modal.show();
    };

    //Obtener la unidad nueva
    document.addEventListener('change', function (e) {
        if (e.target && e.target.id.startsWith('unidad-')) {
            const id = e.target.id.split('-').pop();
            const hiddenInput = document.getElementById(`unidad-hidden-${id}`);
            if (hiddenInput) hiddenInput.value = e.target.value;
        }
    });

    document.addEventListener('change', (e) => {
        const select = e.target.closest('select[name="unidad"]');
        if (!select) return;

        // si el usuario elige Otros
        if (select.value === 'otros') {
            // Crear modal si no existe
            let modal = document.getElementById('modalNuevaUnidad');
            if (!modal) {
                modal = document.createElement('div');
                modal.id = 'modalNuevaUnidad';
                modal.className = 'modal-nueva-unidad';
                modal.style.cssText = `
                    display:none; position:fixed; top:0; left:0; right:0; bottom:0;
                    background:rgba(0,0,0,0.4); z-index:9999; justify-content:center; align-items:center;
                `;
                modal.innerHTML = `
                    <div style="background:#fff; padding:20px; border-radius:8px; width:300px; text-align:center;">
                        <h3>Agregar nueva unidad</h3>
                        <input type="text" id="nueva_unidad_input" placeholder="Ej: Paquete"
                            style="width:100%; padding:5px; margin-top:10px;">
                        <div style="margin-top:15px;">
                            <button id="guardar_nueva_unidad" class="btn-inventory" style="margin-right:10px;">Guardar</button>
                            <button id="cancelar_nueva_unidad" class="btn-filtro">Cancelar</button>
                        </div>
                    </div>
                `;
                document.body.appendChild(modal);
            }

            // Mostrar modal
            modal.style.display = 'flex';
            const input = document.getElementById('nueva_unidad_input');
            input.value = '';
            input.focus();

            // Cancelar
            const btnCancelar = document.getElementById('cancelar_nueva_unidad');
            btnCancelar.onclick = () => {
                modal.style.display = 'none';
                select.value = 'otros';
            };

            // Guardar
            const btnGuardar = document.getElementById('guardar_nueva_unidad');
            btnGuardar.onclick = () => {
                let nuevaUnidad = input.value.trim();
                if (!nuevaUnidad) {
                    alert('Debes ingresar un nombre de unidad.');
                    return;
                }

                // Normalizar con title()
                nuevaUnidad = title(nuevaUnidad);

                // Evitar duplicados
                const existe = Array.from(select.options).some(
                    opt => opt.text.toLowerCase() === nuevaUnidad.toLowerCase()
                );
                if (existe) {
                    alert('Esa unidad ya existe.');
                    modal.style.display = 'none';
                    select.value = '';
                    return;
                }

                // Crear nueva opcion y agregarla antes de "Otros"
                const opcion = document.createElement('option');
                opcion.value = nuevaUnidad;
                opcion.textContent = nuevaUnidad;
                const otros = select.querySelector('option[value="otros"]');
                select.insertBefore(opcion, otros);

                // Seleccionar automaticamente
                select.value = nuevaUnidad;

                alert(`Unidad "${nuevaUnidad}" agregada correctamente.`);
                modal.style.display = 'none';
            };

            // Cerrar con ESC
            document.addEventListener('keydown', (ev) => {
                if (ev.key === 'Escape' && modal.style.display === 'flex') {
                    modal.style.display = 'none';
                    select.value = '';
                }
            });
        }
    });

});

//edicion multiple
(() => {
    const cambios = [];
    const container = document.getElementById('inventario-listado') || document;

    function getProductIdFromElement(el) {
        const tr = el.closest('tr');
        if (!tr) return null;

        const hidden = tr.querySelector('input.product-id');
        if (hidden) return hidden.value;
        
        const m = el.id && el.id.match(/-(\d+)$/);
        return m ? m[1] : null;
    }

    function upsertChange(productId, field, value) {
        let entry = cambios.find(e => e.id === productId);
        if (!entry) {
            entry = { id: productId };
            cambios.push(entry);
        }
        entry[field] = value;
    }

    function handler(e) {
        const target = e.target;
        if (!target.closest('.inventory-table')) return;

        const tr = target.closest('tr');
        const productId = getProductIdFromElement(target);
        if (!productId) return;

        let fieldName = target.name || target.dataset.field || null;
        if (!fieldName && target.id) {
            const m = target.id.match(/^([a-zA-Z_-]+)-\d+$/);
            if (m) fieldName = m[1];
        }
        if (!fieldName) return;

        let value;

        if (target.name === 'subcategories[]') {
            const checkboxes = tr.querySelectorAll('input[name="subcategories[]"]:checked');
            value = Array.from(checkboxes).map(cb => cb.value);
        } else if (target.type === 'checkbox') {
            value = target.checked ? target.value : '';
        } else {
            value = target.value;
        }

        upsertChange(productId, fieldName, value);
    }

    container.addEventListener('change', handler);

    window._inventarioCambios = cambios;
})();

// seleccion multiple para archivar
(() => {
    window._productosSeleccionados = window._productosSeleccionados || new Set();
    const seleccionados = window._productosSeleccionados;

    function actualizarBotonEliminar() {
        let btn = document.getElementById('btn-eliminar-seleccionados');
        
        if (seleccionados.size > 0) {
            if (!btn) {
                btn = document.createElement('button');
                btn.id = 'btn-eliminar-seleccionados';
                btn.className = 'btn-inventory btn-eliminar-multiple';
                btn.style.cssText = 'background: #ef4444; margin-left: 10px;';
                btn.innerHTML = `<i class="fa fa-trash"></i> Archivar ${seleccionados.size} producto${seleccionados.size > 1 ? 's' : ''}`;
                
                const editContainer = document.querySelector('.buttons-edit-container');
                if (editContainer) {
                    const wrapper = document.createElement('div');
                    wrapper.style.cssText = `
                        position:absolute;
                        display: flex;
                        justify-content: flex-end;
                        width: 100%;
                        margin-top: 40px;
                        left:-23px;
                    `;
                    wrapper.appendChild(btn);
                    editContainer.parentNode.insertBefore(wrapper, editContainer.nextSibling);
                }
                
                btn.addEventListener('click', eliminarSeleccionados);
            } else {
                btn.innerHTML = `<i class="fa fa-trash"></i> Archivar ${seleccionados.size} producto${seleccionados.size > 1 ? 's' : ''}`;
            }
        } else {
            if (btn) btn.remove();
        }
        
        document.querySelectorAll('td#btn-archivar, td#actions').forEach(td => {
            td.style.display = seleccionados.size > 0 ? 'none' : '';
        });
    }

    function eliminarSeleccionados() {
        if (!confirm(`¿Está seguro de archivar ${seleccionados.size} producto${seleccionados.size > 1 ? 's' : ''}?`)) {
            return;
        }

        const ids = Array.from(seleccionados);
        const token = document.querySelector('meta[name="csrf-token"]').content;
        
        fetch('/inventario/archivar/0', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
            },
            body: JSON.stringify({ ids })
        })
        .then(res => {
            if (!res.ok) throw new Error('Error al archivar');
            return res.text();
        })
        .then(() => {
            alert(`${ids.length} producto${ids.length > 1 ? 's archivados' : ' archivado'} correctamente. \nTus Productos quedaran archivados por 30 dias antes de ser eliminados permanentemente`);
            seleccionados.clear();
            location.reload();
        })
        .catch(err => {
            console.error(err);
            alert('Ocurrió un error al archivar los productos.');
        });
    }

    function manejarCheckbox(e) {
        const checkbox = e.target;
        if (!checkbox.classList.contains('checkbox-seleccionar-producto')) return;
        
        const productId = checkbox.dataset.productId;
        const tr = checkbox.closest('tr');
        
        if (checkbox.checked) {
            seleccionados.add(productId);
            tr?.classList.add('producto-seleccionado');
        } else {
            seleccionados.delete(productId);
            tr?.classList.remove('producto-seleccionado');
        }
        
        actualizarBotonEliminar();
    }

    function inicializarCheckboxes() {
        document.removeEventListener('change', manejarCheckbox);
        document.addEventListener('change', manejarCheckbox);

        const enEdicion = window._modoEdicionActivo === true;

        document.querySelectorAll('.checkbox-seleccionar-producto').forEach(cb => {
            const productId = cb.dataset.productId;
            const tr = cb.closest('tr'); 

            if (!enEdicion) {
                cb.checked = seleccionados.has(productId);
                if (cb.checked) {
                    tr?.classList.add('producto-seleccionado');
                } else {
                    tr?.classList.remove('producto-seleccionado');
                }
            } else {
                cb.checked = false;
                tr?.classList.remove('producto-seleccionado');
            }
        });

        if (!enEdicion) {
            actualizarBotonEliminar();
        } else {
            const btn = document.getElementById('btn-eliminar-seleccionados');
            if (btn) btn.remove();
        }
    }

    inicializarCheckboxes();

    const fetchOriginal = window.fetchFilteredResults;
    if (typeof fetchOriginal === 'function') {
        const observer = new MutationObserver(() => {
            inicializarCheckboxes();
        });
        
        const listado = document.getElementById('inventario-listado');
        if (listado) {
            observer.observe(listado, { childList: true, subtree: true });
        }
    }

    window._reinicializarSeleccion = inicializarCheckboxes;
})();

// Mantener ocultos botones individuales si hay productos seleccionados
const seleccionadosGlobal = window._productosSeleccionados || new Set();
window._productosSeleccionados = seleccionadosGlobal;

function actualizarVisibilidadBotones() {
    const haySeleccion = seleccionadosGlobal.size > 0;
    const enEdicion = window._modoEdicionActivo === true;

    document.querySelectorAll('th#actions, td#actions, td#btn-archivar').forEach(el => {
        el.style.display = (haySeleccion || enEdicion) ? 'none' : '';
    });
}

const listado = document.getElementById('inventario-listado');
if (listado) {
    const observer = new MutationObserver(() => {
        actualizarVisibilidadBotones();
    });
    observer.observe(listado, { childList: true, subtree: true });
}

document.addEventListener('change', e => {
    if (!e.target.classList.contains('checkbox-seleccionar-producto')) return;

    const id = e.target.dataset.productId;
    if (e.target.checked) seleccionadosGlobal.add(id);
    else seleccionadosGlobal.delete(id);

    actualizarVisibilidadBotones();
});

actualizarVisibilidadBotones();