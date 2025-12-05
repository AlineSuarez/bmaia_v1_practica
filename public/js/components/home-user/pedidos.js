/* =============================================
   FUNCIONES BÁSICAS DE LA VISTA
   ============================================= */
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

function openModal(id) {
    document.getElementById(id).style.display = 'flex';
}

function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}

function formatPrice(input) {
    let number = input.value.replace(/[^\d]/g, "");
    input.nextElementSibling.value = number;
    input.value = new Intl.NumberFormat('es-CL').format(number);
}

function showAlert(message, type = 'success') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert-custom alert-${type}`;
    alertDiv.innerHTML = `<span>${message}</span>`;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        alertDiv.classList.add('hiding');
        setTimeout(() => alertDiv.remove(), 300);
    }, 3000);
}

function showLoading(show = true) {
    const loader = document.getElementById('loadingOverlay');
    if (loader) {
        loader.style.display = show ? 'flex' : 'none';
    }
}

async function recargarPedidos() {
    try {
        const response = await fetch('/inventario/verPedidos?ajax=1', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html',
                'Cache-Control': 'no-cache, no-store, must-revalidate',
                'Pragma': 'no-cache',
                'Expires': '0'
            }
        });

        if (!response.ok) {
            console.error('Error HTTP:', response.status);
            return false;
        }

        const html = await response.text();

        // Parsear HTML recibido y colocar dentro de #inventario-pedidos
        const temp = document.createElement('div');
        temp.innerHTML = html;

        const nuevoRoot = temp.querySelector('.inventory-container') || temp.querySelector('#contenedor-pedidos-activos') || temp;
        const inventarioPedidosCont = document.getElementById('inventario-pedidos');

        if (inventarioPedidosCont) {
            inventarioPedidosCont.innerHTML = nuevoRoot.innerHTML;
            // Vincular UI de botones dentro del HTML insertado
            bindPedidosUI(inventarioPedidosCont);
        } else {
            const contenedorActual = document.getElementById('contenedor-pedidos-activos');
            const nuevoContenido = nuevoRoot.querySelector('#contenedor-pedidos-activos');
            if (nuevoContenido && contenedorActual) {
                contenedorActual.innerHTML = nuevoContenido.innerHTML;
                // console.log('Contenedor-pedidos-activos reemplazado');
                bindPedidosUI(document);
            }
        }

        // Extraer total global si viene en el HTML (ej: window.pedidosTotal = X;)
        const matchTotal = html.match(/window\.pedidosTotal\s*=\s*(\d+)\s*;/);
        if (matchTotal) {
            window.pedidosTotal = parseInt(matchTotal[1], 10);
        }

        // Actualizar contador: contar tarjetas en la página actual
        const contActualAfter = document.getElementById('contenedor-pedidos-activos');
        const tarjetas = contActualAfter ? contActualAfter.querySelectorAll('.product-card') : [];
        const cantidadPagina = tarjetas.length;
        const contadorActual = document.getElementById('contador-pedidos');

        if (contadorActual) {
            if (window.pedidosTotal && window.pedidosTotal > cantidadPagina) {
                contadorActual.textContent = `${window.pedidosTotal} Pedidos Pendientes a Realizar`;
            } else {
                contadorActual.textContent = `${cantidadPagina} Pedidos ${cantidadPagina === 1 ? 'Pendiente' : 'Pendientes'} a Realizar`;
            }
        }

        return true;

    } catch (error) {
        console.error('Error recargando pedidos:', error);
        return false;
    }
}

function bindPedidosUI(rootContainer) {
    const root = (rootContainer instanceof Element) ? rootContainer : document;
    const btnPedidosActivos = root.querySelector('#btn-pedidos-activos');
    const btnHistorialCompras = root.querySelector('#btn-historial-compras');
    const contPedidosActivos = root.querySelector('#contenedor-pedidos-activos') || document.getElementById('contenedor-pedidos-activos');
    const contHistorialCompras = root.querySelector('#contenedor-historial-compras') || document.getElementById('contenedor-historial-compras');

    if (btnPedidosActivos) {
        btnPedidosActivos.addEventListener('click', function () {
            if (contPedidosActivos) contPedidosActivos.classList.remove('hidden');
            if (contHistorialCompras) contHistorialCompras.classList.add('hidden');
            btnPedidosActivos.classList.add('active-view');
            if (btnHistorialCompras) btnHistorialCompras.classList.remove('active-view');
        });
    }

    if (btnHistorialCompras) {
        btnHistorialCompras.addEventListener('click', function () {
            if (contHistorialCompras) contHistorialCompras.classList.remove('hidden');
            if (contPedidosActivos) contPedidosActivos.classList.add('hidden');
            btnHistorialCompras.classList.add('active-view');
            if (btnPedidosActivos) btnPedidosActivos.classList.remove('active-view');
        });
    }

    // Asegurar que al insertar por primera vez Pedidos Activos quede activo
    try {
        const anyActive = (btnPedidosActivos && btnPedidosActivos.classList.contains('active-view')) || (btnHistorialCompras && btnHistorialCompras.classList.contains('active-view'));
        if (!anyActive) {
            if (btnPedidosActivos) btnPedidosActivos.classList.add('active-view');
            if (contPedidosActivos) contPedidosActivos.classList.remove('hidden');
            if (contHistorialCompras) contHistorialCompras.classList.add('hidden');
        }
    } catch (e) {
        //.
    }
}

/* =============================================
   FUNCIONES AJAX PARA PEDIDOS
   ============================================= */
async function eliminarPedidoAjax(pedidoId) {
    if (!confirm('¿Estás seguro de que deseas eliminar este pedido?')) {
        return;
    }

    showLoading(true);

    try {
        const res = await fetch(`/inventario/deletePedido/${pedidoId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const data = await res.json();
        showLoading(false);

        if (data.success) {
            showAlert(data.message, 'success');
            await recargarPedidos();
        } else {
            showAlert(data.message || 'Error al eliminar', 'error');
        }
    } catch (error) {
        showLoading(false);
        console.error('Error:', error);
        showAlert('Error al eliminar', 'error');
    }
}

async function archivarPedidoAjax(event, pedidoId) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    
    showLoading(true);

    try {
        const res = await fetch(`/inventario/archivarPedido/${pedidoId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });

        const data = await res.json();
        showLoading(false);

        if (data.success) {
            showAlert(data.message, 'success');
            closeModal(`modalUpdate${pedidoId}`);
            await recargarPedidos();
        } else {
            showAlert(data.message || 'Error', 'error');
        }
    } catch (error) {
        showLoading(false);
        console.error('Error:', error);
        showAlert('Error', 'error');
    }
}

async function crearProductoAjax(event, pedidoId) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    formData.append('confirmar', '1');
    
    showLoading(true);

    try {
        const res1 = await fetch('/inventario/store', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });
        const data1 = await res1.json();

        if (data1.status === 'success') {
            const res2 = await fetch(`/inventario/archivarPedido/${pedidoId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({ '_token': formData.get('_token') })
            });
            const data2 = await res2.json();
            showLoading(false);

            if (data2.success) {
                showAlert('Producto creado correctamente', 'success');
                closeModal(`modalNew${pedidoId}`);
                await recargarPedidos();
            } else {
                showAlert(data2.message || 'Error', 'error');
            }
        } else {
            showLoading(false);
            showAlert(data1.message || 'Error', 'error');
        }
    } catch (error) {
        showLoading(false);
        console.error('Error:', error);
        showAlert('Error', 'error');
    }
}

function cargarPagina(event, page, tipo) {
    event.preventDefault();

    const param = tipo === 'pedidos' ? 'pedidos_page' : 'historial_page';
    const url = `/inventario/verPedidos?ajax=1&${param}=${page}`;

    fetch(url, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html'
        },
        cache: 'no-store'
    })
    .then(response => {
        if (!response.ok) throw new Error('HTTP ' + response.status);
        return response.text();
    })
    .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');

        // ACTUALIZAR CONTENEDOR CORRECTO SEGÚN TIPO
        const contenedorId = tipo === 'pedidos' ? 'contenedor-pedidos-activos' : 'contenedor-historial-compras';
        const paginationId = tipo === 'pedidos' ? 'pagination-pedidos' : 'pagination-historial';

        // Reemplazar contenedor principal
        const nuevoContenedor = doc.getElementById(contenedorId);
        const actualContenedor = document.getElementById(contenedorId);
        if (nuevoContenedor && actualContenedor) {
            actualContenedor.innerHTML = nuevoContenedor.innerHTML;
            console.log(`Actualizado ${contenedorId}`);
        }

        // Reemplazar paginación
        const nuevoPaginacion = doc.getElementById(paginationId);
        const actualPaginacion = document.getElementById(paginationId);
        if (nuevoPaginacion && actualPaginacion) {
            actualPaginacion.innerHTML = nuevoPaginacion.innerHTML;
            console.log(`Actualizada ${paginationId}`);
        }

        // Extraer y actualizar totales si vienen en script
        const matchTotal = html.match(/window\.pedidosTotal\s*=\s*(\d+)\s*;/);
        if (matchTotal) window.pedidosTotal = parseInt(matchTotal[1], 10);

        // Re-bind UI
        bindPedidosUI(document.getElementById('inventario-pedidos') || document);

        // Actualizar contador
        const contActualAfter = document.getElementById('contenedor-pedidos-activos');
        const tarjetas = contActualAfter ? contActualAfter.querySelectorAll('.product-card') : [];
        const cantidadPagina = tarjetas.length;
        const contadorActual = document.getElementById('contador-pedidos');
        if (contadorActual) {
            if (window.pedidosTotal && window.pedidosTotal > cantidadPagina) {
                contadorActual.textContent = `${window.pedidosTotal} Pedidos Pendientes a Realizar`;
            } else {
                contadorActual.textContent = `${cantidadPagina} Pedidos ${cantidadPagina === 1 ? 'Pendiente' : 'Pendientes'} a Realizar`;
            }
        }

        // Scroll suave al contenedor
        if (actualContenedor) {
            window.scrollTo({ top: actualContenedor.offsetTop - 100, behavior: 'smooth' });
        }
    })
    .catch(err => {
        console.error('Error cargarPagina:', err);
        showAlert('Error al cargar página', 'error');
    });
}

(function setupPedidosVisibilityObserver() {
    const target = document.getElementById('inventario-pedidos');
    if (!target) return;

    // Estado para evitar recargas en loop
    const state = {
        prevVisible: (window.getComputedStyle(target).display !== 'none' && target.offsetParent !== null),
        recargando: false,
        timer: null
    };

    const isVisible = () => (window.getComputedStyle(target).display !== 'none' && target.offsetParent !== null);

    const scheduleReloadIfShown = () => {
        const visible = isVisible();

        // Solo reaccionar cuando pase de oculto -> visible
        if (visible && !state.prevVisible && !state.recargando) {
            // debounce corto
            if (state.timer) clearTimeout(state.timer);
            state.timer = setTimeout(async () => {
                state.recargando = true;
                try {
                    // console.log('#inventario-pedidos visible (transition) → recargando pedidos');
                    await recargarPedidos();
                } catch (e) {
                    console.error('Error recargando pedidos (observer):', e);
                } finally {
                    state.recargando = false;
                    state.prevVisible = isVisible();
                }
            }, 150);
        } else {
            // actualizar prevVisible en cambios normales
            state.prevVisible = visible;
        }
    };

    const obs = new MutationObserver(() => {
        scheduleReloadIfShown();
    });

    obs.observe(target, { attributes: true, attributeFilter: ['style', 'class'] });
    const parent = target.parentElement;
    if (parent) {
        const parentObs = new MutationObserver(() => {
            scheduleReloadIfShown();
        });
        parentObs.observe(parent, { attributes: true, attributeFilter: ['style', 'class'] });
    }
})();

/* =============================================
   LECTOR DE FACTURAS - CLASE COMPLETA CORREGIDA
   ============================================= */
class InvoiceReader {
    constructor() {
        this.productos = [];
        this.precioTotal = null;
    }

    async leerFactura(file) {
        try {
            // console.log('Iniciando lectura de PDF...');
            const arrayBuffer = await file.arrayBuffer();
            const pdf = await pdfjsLib.getDocument({ data: arrayBuffer }).promise;
            
            // console.log(`PDF cargado: ${pdf.numPages} páginas`);
            
            let textoCompleto = '';
            
            for (let i = 1; i <= pdf.numPages; i++) {
                const page = await pdf.getPage(i);
                const textContent = await page.getTextContent();
                
                let lastY = null;
                let pageText = '';
                
                textContent.items.forEach(item => {
                    const currentY = item.transform[5];
                    
                    if (lastY !== null && Math.abs(currentY - lastY) > 5) {
                        pageText += '\n';
                    }
                    
                    pageText += item.str + ' ';
                    lastY = currentY;
                });
                
                textoCompleto += pageText + '\n';
            }

            // console.log('Texto extraído (primeros 500 caracteres):', textoCompleto.substring(0, 500));
            // console.log('Total caracteres:', textoCompleto.length);
            
            this.parsearFactura(textoCompleto);
            
            // console.log('Productos encontrados:', this.productos);
            // console.log('Precio total:', this.precioTotal);
            
            return {
                success: true,
                productos: this.productos,
                precioTotal: this.precioTotal,
                textoCompleto: textoCompleto
            };
            
        } catch (error) {
            console.error('Error al leer PDF:', error);
            return {
                success: false,
                error: error.message
            };
        }
    }

    parsearFactura(texto) {
        this.productos = [];
        this.precioTotal = null;
        
        const lineas = texto.split('\n').filter(l => l.trim());
        
        const esFormatoTabla = this.detectarFormatoTabla(lineas);
        // console.log('Formato de tabla detectado:', esFormatoTabla);
        
        if (esFormatoTabla) {
            this.parsearFacturaTabla(lineas);
        } else {
            this.parsearFacturaSimple(lineas);
        }
        
        const patronPrecioTotal = /total\s*:?\s*[$\$]?\s*(\d+(?:[.,]\d{3})*(?:[.,]\d{2})?)/i;
        for (const linea of lineas) {
            const matchTotal = linea.match(patronPrecioTotal);
            if (matchTotal) {
                this.precioTotal = this.limpiarPrecio(matchTotal[1]);
            }
        }
        
        if (this.productos.length === 0) {
            this.buscarProductosSinPrecio(lineas);
        }
    }

    detectarFormatoTabla(lineas) {
        const palabrasClave = [
            'codigo', 'descripcion', 'cantidad', 'precio', 'valor',
            'código', 'descripción', 'cant', 'unid', 'monto'
        ];
        
        for (let i = 0; i < Math.min(20, lineas.length); i++) {
            const lineaLower = lineas[i].toLowerCase();
            const coincidencias = palabrasClave.filter(palabra => 
                lineaLower.includes(palabra)
            );
            
            if (coincidencias.length >= 2) {
                // console.log('Encabezado de tabla encontrado en línea', i, ':', lineas[i]);
                return true;
            }
        }
        return false;
    }

    parsearFacturaTabla(lineas) {
        // console.log('Parseando factura con formato de tabla...');
        
        let indiceEncabezado = -1;
        
        for (let i = 0; i < Math.min(30, lineas.length); i++) {
            const lineaLower = lineas[i].toLowerCase();
            
            if (lineaLower.includes('descripcion') || 
                lineaLower.includes('descripción') ||
                lineaLower.includes('producto') ||
                (lineaLower.includes('codigo') && lineaLower.includes('cantidad'))) {
                indiceEncabezado = i;
                // console.log('Encabezado encontrado en línea', i, ':', lineas[i]);
                break;
            }
        }
        
        if (indiceEncabezado === -1) {
            // console.log('No se encontró encabezado, usando parser simple');
            this.parsearFacturaSimple(lineas);
            return;
        }
        
        for (let i = indiceEncabezado + 1; i < lineas.length; i++) {
            const linea = lineas[i].trim();
            
            if (!linea || linea.match(/^[-_=|]+$/)) {
                continue;
            }
            
            if (this.esLineaTotal(linea) || this.esLineaPiePagina(linea)) {
                break;
            }
            
            const patronPipes = /^\|\s*(\d+)\s*\|\s*([^|]+)\s*\|\s*(\d+)\s*\|\s*\$?\s*([\d.,]+)\s*\|\s*\$?\s*([\d.,]+)\s*\|/;
            const matchPipes = linea.match(patronPipes);
            
            if (matchPipes) {
                const [, codigo, descripcion, cantidad, precioUnit, total] = matchPipes;
                
                this.productos.push({
                    nombre: descripcion.trim(),
                    cantidad: parseInt(cantidad),
                    precio: this.limpiarPrecio(precioUnit)
                });
                continue;
            }
            
            const patronGuion = /^[-•]\s*(.+?)\s+(\d+)\s+(?:unid?|u\.)?\s+([\d.,]+)/i;
            const matchGuion = linea.match(patronGuion);
            
            if (matchGuion) {
                const [, descripcion, cantidad, precio] = matchGuion;
                
                if (this.puedeSerNombreProducto(descripcion)) {
                    this.productos.push({
                        nombre: descripcion.trim(),
                        cantidad: parseInt(cantidad),
                        precio: this.limpiarPrecio(precio)
                    });
                    continue;
                }
            }
            
            const patronGeneral = /^(.+?)\s+(\d+)\s+(?:unid?|u\.)?\s*([\d.,\s]+)$/i;
            const matchGeneral = linea.match(patronGeneral);
            
            if (matchGeneral) {
                const descripcion = matchGeneral[1].trim();
                const cantidad = parseInt(matchGeneral[2]);
                const numeros = matchGeneral[3].match(/[\d.,]+/g);
                
                if (numeros && numeros.length >= 1 && this.puedeSerNombreProducto(descripcion)) {
                    const precioUnitario = this.limpiarPrecio(numeros[0]);
                    
                    this.productos.push({
                        nombre: descripcion,
                        cantidad: cantidad,
                        precio: precioUnitario
                    });
                    continue;
                }
            }
        }
        
        if (this.productos.length === 0) {
            // console.log('no se encontraron productos en formato tabla, intentando parser simple');
            this.parsearFacturaSimple(lineas);
        }
    }

    parsearFacturaSimple(lineas) {
        // console.log('Parseando factura con formato simple...');
        
        const patronProducto = /^(.+?)\s+(\d+(?:[.,]\d+)?)\s+(?:x\s+)?[$\$]?\s*(\d+(?:[.,]\d{3})*(?:[.,]\d{2})?)/i;
        let productoTemp = null;
        
        for (let i = 0; i < lineas.length; i++) {
            const linea = lineas[i].trim();
            
            const matchProducto = linea.match(patronProducto);
            if (matchProducto) {
                const nombre = matchProducto[1].trim();
                const cantidad = parseFloat(matchProducto[2].replace(',', '.'));
                const precio = this.limpiarPrecio(matchProducto[3]);
                
                if (!this.esLineaNoProducto(nombre)) {
                    this.productos.push({
                        nombre: nombre,
                        cantidad: cantidad,
                        precio: precio
                    });
                }
                continue;
            }
            
            if (this.puedeSerNombreProducto(linea)) {
                productoTemp = { nombre: linea };
                
                for (let j = i + 1; j < Math.min(i + 4, lineas.length); j++) {
                    const siguienteLinea = lineas[j].trim();
                    
                    const patronCantidad = /cantidad\s*:?\s*(\d+)/i;
                    const matchCant = siguienteLinea.match(patronCantidad);
                    if (matchCant && !productoTemp.cantidad) {
                        productoTemp.cantidad = parseInt(matchCant[1]);
                    }
                    
                    const matchPrecio = siguienteLinea.match(/[$\$]?\s*(\d+(?:[.,]\d{3})*(?:[.,]\d{2})?)/);
                    if (matchPrecio && !productoTemp.precio) {
                        productoTemp.precio = this.limpiarPrecio(matchPrecio[1]);
                    }
                }
                
                if (productoTemp.cantidad || productoTemp.precio) {
                    if (!productoTemp.cantidad) productoTemp.cantidad = 1;
                    this.productos.push(productoTemp);
                    productoTemp = null;
                }
            }
        }
    }

    buscarProductosSinPrecio(lineas) {
        // console.log('Buscando productos sin precio...');
        const patronSimple = /^(.+?)\s+(?:x\s*)?(\d+)(?:\s+(?:unid|un|u))?/i;
        
        for (const linea of lineas) {
            const match = linea.match(patronSimple);
            if (match && this.puedeSerNombreProducto(match[1])) {
                this.productos.push({
                    nombre: match[1].trim(),
                    cantidad: parseInt(match[2])
                });
            }
        }
    }

    limpiarPrecio(precioStr) {
        const limpio = precioStr.replace(/\./g, '').replace(',', '.');
        return parseFloat(limpio);
    }

    esLineaTotal(texto) {
        const textoLower = texto.toLowerCase();
        return /^(sub)?total|monto\s*(neto|total)|i\.?v\.?a/i.test(textoLower);
    }

    esLineaPiePagina(texto) {
        const textoLower = texto.toLowerCase();
        const keywords = [
            'forma de pago', 'timbre', 'verificar', 'documento',
            'www.', 'email', 'telefono', 'dirección', 'rut:'
        ];
        
        return keywords.some(keyword => textoLower.includes(keyword));
    }

    esLineaNoProducto(texto) {
        const keywords = [
            'subtotal', 'i.v.a', 'iva', 'descuento', 'neto',
            'efectivo', 'tarjeta', 'transferencia',
            'boleta', 'factura', 'rut:', 'giro:',
            'dirección:', 'direccion:', 'comuna:', 'ciudad:',
            'contacto:', 'teléfono:', 'telefono:', 'email:',
            'tipo de venta', 'tipo de compra', 'forma de pago', 'señor(es):',
            'timbre', 'impuesto adicional', 'proveedor:', 'n° factura',
            'fecha emision:', 'fecha:', 'verificar', 'www.', 'res.', 's.i.i',
            'cliente:', 'av.', 'avenida', 'calle', 'gracias por',
            'factura electronica', 'factura electrónica', 'del giro'
        ];
        
        const textoLower = texto.toLowerCase().trim();
        
        if (/r\.?u\.?t\.?:?\s*\d{1,2}\.\d{3}\.\d{3}-[\dk]/i.test(texto)) {
            return true;
        }
        
        if (/^\d{7,8}-[\dk]$/.test(texto.replace(/\./g, ''))) {
            return true;
        }
        
        if (texto.length < 3 || /^\d+$/.test(texto) || /^[-_=|]+$/.test(texto)) {
            return true;
        }
        
        if (/\d{1,2}[/-]\d{1,2}[/-]\d{4}/.test(texto)) {
            return true;
        }
        
        if (texto === texto.toUpperCase() && texto.length > 15 && !/\d/.test(texto)) {
            return true;
        }
        
        if (/@|\.cl|\.com|^\+?\d{1,3}[\s-]?\d/.test(texto)) {
            return true;
        }
        
        if (/n[°º]\s*\d+|factura\s*[:#]?\s*\d+/i.test(texto)) {
            return true;
        }
        
        return keywords.some(keyword => textoLower.includes(keyword));
    }

    puedeSerNombreProducto(texto) {
        if (texto.length < 3) {
            return false;
        }
        
        if (/^\d+$/.test(texto) || /^[-_=|.]+$/.test(texto)) {
            return false;
        }
        
        const textoLower = texto.toLowerCase().trim();
        const palabrasExcluir = [
            'subtotal', 'total', 'iva', 'monto', 'forma de', 'tipo de',
            'señor', 'rut:', 'giro:', 'fecha:', 'proveedor:', 'n°',
            'cliente:', 'factura', 'contacto:', 'telefono:', 'email:'
        ];
        
        if (palabrasExcluir.some(palabra => textoLower.startsWith(palabra))) {
            return false;
        }
        
        if (/@|\.cl|\.com|^\+?\d{1,3}[\s-]?\d|^\d{2}[-/]\d{2}[-/]\d{4}/.test(texto)) {
            return false;
        }
        
        return !this.esLineaNoProducto(texto);
    }

    obtenerResultado() {
        if (this.productos.length === 0) {
            return null;
        }

        const tienenPrecio = this.productos.every(p => p.precio);

        if (tienenPrecio) {
            return this.productos;
        } else {
            const resultado = this.productos.map(p => ({
                nombre: p.nombre,
                cantidad: p.cantidad
            }));

            if (this.precioTotal) {
                return {
                    productos: resultado,
                    precioTotal: this.precioTotal
                };
            }

            return resultado;
        }
    }
}

/* =============================================
   FUNCIONES PARA MANEJO DE FACTURAS - COMPLETAS
   ============================================= */
let invoiceReader = new InvoiceReader();

function handleFileUpload(event) {
    const file = event.target.files[0];
    
    // console.log('Archivo seleccionado:', file);
    
    if (!file) {
        showAlert('No se seleccionó ningún archivo', 'error');
        return;
    }
    
    if (file.type !== 'application/pdf') {
        showAlert('Por favor selecciona un archivo PDF', 'error');
        event.target.value = '';
        return;
    }
    
    showLoading(true);
    
    invoiceReader.leerFactura(file)
        .then(result => {
            showLoading(false);
            
            // console.log('Resultado de lectura:', result);
            
            if (result.success) {
                const datos = invoiceReader.obtenerResultado();
                
                if (!datos) {
                    showAlert('No se pudieron extraer productos de la factura. Revisa la consola para más detalles.', 'error');
                    // console.log('No se extrajeron datos');
                    // console.log('Texto completo del PDF:', result.textoCompleto);
                    event.target.value = '';
                    return;
                }
                
                // console.log('Datos extraídos:', datos);
                mostrarResultadosModal(datos);
                event.target.value = '';
            } else {
                showAlert('Error al leer el PDF: ' + result.error, 'error');
                event.target.value = '';
            }
        })
        .catch(error => {
            showLoading(false);
            console.error('Error:', error);
            showAlert('Error al procesar la factura', 'error');
            event.target.value = '';
        });
}

function mostrarResultadosModal(datos) {
    // console.log('Mostrando modal con datos:', datos);
    
    const modal = document.getElementById('modalResultadosFactura');
    const contenedor = document.getElementById('resultadosFacturaContainer');
    
    let html = '<div class="resultados-factura">';
    
    if (Array.isArray(datos)) {
        html += '<h4>Productos encontrados:</h4>';
        html += '<div class="productos-list">';
        
        datos.forEach((producto, index) => {
            html += `
                <div class="producto-item" id="producto-${index}" data-producto-id="">
                    <div class="producto-numero">${index + 1}</div>
                    <div class="producto-info">
                        <input
                            type="text" 
                            class="producto-nombre-editable" 
                            value="${producto.nombre}"
                            onblur="actualizarNombreProducto(${index}, this.value)"
                            placeholder="Nombre del producto"
                        >
                        <span id="cantidad-${index}">Cantidad: ${producto.cantidad}</span>
                        ${producto.precio ? `<span class="precio">Precio: $${producto.precio.toLocaleString('es-CL')}</span>` : ''}
                    </div>
                    <button 
                        class="btn-actualizar-producto" 
                        id="btn-actualizar-${index}"
                        onclick="actualizarProductoExistente(${index})" 
                        title="Actualizar producto existente">
                        <i class="fa-solid fa-refresh"></i>
                    </button>
                    <button class="btn-eliminar-producto" onclick="eliminarProductoModal(${index})" title="Eliminar este producto">
                        <i class="fa-solid fa-times"></i>
                    </button>
                </div>
            `;
        });
        
        html += '</div>';
        
        const total = datos.reduce((sum, p) => sum + (p.precio || 0) * p.cantidad, 0);
        if (total > 0) {
            html += `<div class="total-compra" id="total-compra">Total: $${total.toLocaleString('es-CL')}</div>`;
        }
        
    } else {
        html += '<h4>Productos encontrados:</h4>';
        html += '<div class="productos-list">';
        
        datos.productos.forEach((producto, index) => {
            html += `
                <div class="producto-item" id="producto-${index}" data-producto-id="">
                    <div class="producto-numero">${index + 1}</div>
                    <div class="producto-info">
                        <input 
                            type="text" 
                            class="producto-nombre-editable" 
                            value="${producto.nombre}"
                            onblur="actualizarNombreProducto(${index}, this.value)"
                            placeholder="Nombre del producto"
                        >
                        <span id="cantidad-${index}">Cantidad: ${producto.cantidad}</span>
                    </div>
                    <button 
                        class="btn-actualizar-producto" 
                        id="btn-actualizar-${index}"
                        onclick="actualizarProductoExistente(${index})" 
                        title="Actualizar producto existente">
                        <i class="fa-solid fa-refresh"></i>
                    </button>
                    <button class="btn-eliminar-producto" onclick="eliminarProductoModal(${index})" title="Eliminar este producto">
                        <i class="fa-solid fa-times"></i>
                    </button>
                </div>
            `;
        });
        
        html += '</div>';
        
        if (datos.precioTotal) {
            html += `<div class="total-compra" id="total-compra">Precio Total: $${datos.precioTotal.toLocaleString('es-CL')}</div>`;
        }
    }
    
    html += `
        <div class="acciones-factura">
            <button class="btn-inventory" onclick="aplicarProductosFactura()">
                Crear Nuevos en Inventario
            </button>
            <button class="btn-secondary" onclick="closeModal('modalResultadosFactura')">
                Cerrar
            </button>
        </div>
    `;
    
    html += '</div>';
    
    contenedor.innerHTML = html;
    openModal('modalResultadosFactura');
    
    // BUSCAR PRODUCTOS EXISTENTES AL CARGAR
    if (Array.isArray(datos)) {
        datos.forEach((producto, index) => {
            buscarProductoExistente(index, producto.nombre);
        });
    } else {
        datos.productos.forEach((producto, index) => {
            buscarProductoExistente(index, producto.nombre);
        });
    }
}

function eliminarProductoModal(index) {
    // console.log('Eliminando producto en índice:', index);
    
    const datos = invoiceReader.obtenerResultado();
    
    if (Array.isArray(datos)) {
        datos.splice(index, 1);
        invoiceReader.productos = datos;
    } else {
        datos.productos.splice(index, 1);
        invoiceReader.productos = datos.productos;
    }
    
    const elemento = document.getElementById(`producto-${index}`);
    if (elemento) {
        elemento.style.animation = 'slideOut 0.3s ease-in forwards';
        setTimeout(() => {
            const nuevoDatos = invoiceReader.obtenerResultado();
            if (nuevoDatos) {
                mostrarResultadosModal(nuevoDatos);
            } else {
                closeModal('modalResultadosFactura');
                showAlert('Se eliminaron todos los productos', 'info');
            }
        }, 300);
    }
}

function actualizarNombreProducto(index, nuevoNombre) {
    // console.log('Actualizando nombre del producto', index);
    
    const nombreFormateado = title(nuevoNombre);
    
    const datos = invoiceReader.obtenerResultado();
    
    if (Array.isArray(datos)) {
        datos[index].nombre = nombreFormateado;
        invoiceReader.productos[index].nombre = nombreFormateado;
    } else {
        datos.productos[index].nombre = nombreFormateado;
        invoiceReader.productos[index].nombre = nombreFormateado;
    }
    
    const input = document.querySelector(`#producto-${index} .producto-nombre-editable`);
    if (input) {
        input.value = nombreFormateado;
    }
    
    // BUSCAR SI EXISTE EN INVENTARIO
    buscarProductoExistente(index, nombreFormateado);
}

async function buscarProductoExistente(index, nombreBuscar) {
    // console.log('Buscando producto:', nombreBuscar);
    
    const nombreFormateado = title(nombreBuscar);
    
    const buscarRoute = window.buscarProductoRoute || '/inventario/buscarProducto';
    
    try {
        const response = await fetch(buscarRoute, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                nombre: nombreFormateado
            })
        });
        
        const data = await response.json();
        
        const btnActualizar = document.getElementById(`btn-actualizar-${index}`);
        const productoItem = document.getElementById(`producto-${index}`);
        const cantidadSpan = document.getElementById(`cantidad-${index}`);
        
        if (data.encontrado) {
            // console.log('Producto encontrado en inventario:', data.producto);
            
            // Guardar ID del producto
            productoItem.dataset.productoId = data.producto.id;
            
            // Mostrar botón de actualizar
            btnActualizar.classList.add('visible');
            
            // Agregar badge
            if (!cantidadSpan.querySelector('.producto-match-badge')) {
                const badge = document.createElement('span');
                badge.className = 'producto-match-badge';
                badge.textContent = '✓ En inventario';
                cantidadSpan.appendChild(badge);
            }
            
            // Guardar datos del producto existente
            const datos = invoiceReader.obtenerResultado();
            if (Array.isArray(datos)) {
                datos[index].productoExistente = data.producto;
            } else {
                datos.productos[index].productoExistente = data.producto;
            }
            
        } else {
            // console.log('Producto no encontrado, se creará nuevo');
            
            // Ocultar botón
            btnActualizar.classList.remove('visible');
            productoItem.dataset.productoId = '';
            
            // Quitar badge
            const badge = cantidadSpan.querySelector('.producto-match-badge');
            if (badge) badge.remove();
        }
        
    } catch (error) {
        console.error('Error al buscar producto:', error);
    }
}

async function actualizarProductoExistente(index) {
    const productoItem = document.getElementById(`producto-${index}`);
    const productoId = productoItem.dataset.productoId;
    
    if (!productoId) {
        showAlert('No se encontró el producto en el inventario', 'error');
        return;
    }
    
    const datos = invoiceReader.obtenerResultado();
    let producto;
    
    if (Array.isArray(datos)) {
        producto = datos[index];
    } else {
        producto = datos.productos[index];
    }
    
    if (!producto.precio) {
        showAlert('El producto debe tener precio para actualizar', 'error');
        return;
    }
    
    const inputNombre = document.querySelector(`#producto-${index} .producto-nombre-editable`);
    const nombreActual = inputNombre ? title(inputNombre.value) : title(producto.nombre);
    
    const productoExistente = producto.productoExistente;
    
    if (!productoExistente) {
        showAlert('No se encontraron datos del producto existente', 'error');
        return;
    }
    
    const cantidadFactura = parseFloat(producto.cantidad);
    const precioUnitarioFactura = parseFloat(producto.precio);
    
    // console.log('Actualizando producto existente:', {
    //     id: productoId,
    //     nombreNuevo: nombreActual,
    //     cantidadFactura: cantidadFactura,
    //     precioUnitarioFactura: precioUnitarioFactura
    // });
    
    showLoading(true);
    
    try {
        const response = await fetch(`/inventario/update/${productoId}`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                nombreProducto: nombreActual,
                cantidad: cantidadFactura,
                precio: precioUnitarioFactura,
                desde_factura: true
            })
        });
        
        const data = await response.json();
        
        showLoading(false);
        
        if (response.ok) {
            showAlert('Producto actualizado correctamente', 'success');
            eliminarProductoModal(index);
        } else {
            showAlert(data.message || 'Error al actualizar producto', 'error');
        }
        
    } catch (error) {
        showLoading(false);
        console.error('Error:', error);
        showAlert('Error al actualizar el producto', 'error');
    }
}

function aplicarProductosFactura() {
    const datos = invoiceReader.obtenerResultado();
    
    if (!datos || (Array.isArray(datos) && datos.length === 0) || 
        (datos.productos && datos.productos.length === 0)) {
        showAlert('No hay productos para agregar', 'error');
        return;
    }
    
    // console.log('Aplicar al inventario:', datos);
    
    showLoading(true);
    
    let productos = [];
    
    if (Array.isArray(datos)) {
        productos = datos.map(p => ({
            nombreProducto: title(p.nombre),
            cantidad: p.cantidad,
            precio: p.precio
        }));
    } else {
        showAlert('Los productos deben tener precio individual para ser agregados', 'error');
        showLoading(false);
        return;
    }
    
    const storeRoute = window.inventarioStoreRoute || '/inventario/store';
    
    fetch(storeRoute, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            productos: productos
        })
    })
    .then(response => response.json())
    .then(data => {
        showLoading(false);
        
        if (data.status === 'success') {
            showAlert(data.message, 'success');
            closeModal('modalResultadosFactura');
        } else {
            showAlert(data.message || 'Error al crear productos', 'error');
        }
    })
    .catch(error => {
        showLoading(false);
        console.error('❌ Error:', error);
        showAlert('Error al procesar la solicitud', 'error');
    });
}

function subirFactura() {
    // console.log('Abriendo selector de archivos...');
    const inputFile = document.getElementById('inputFactura');
    if (inputFile) {
        inputFile.click();
    } else {
        console.error('No se encontró el input de archivo');
    }
}

/* =============================================
   EXPONER FUNCIONES AL SCOPE GLOBAL
   ============================================= */
window.title = title;
window.openModal = openModal;
window.closeModal = closeModal;
window.formatPrice = formatPrice;
window.showAlert = showAlert;
window.showLoading = showLoading;
window.eliminarPedidoAjax = eliminarPedidoAjax;
window.archivarPedidoAjax = archivarPedidoAjax;
window.crearProductoAjax = crearProductoAjax;
window.cargarPagina = cargarPagina;
window.recargarPedidos = recargarPedidos;
window.subirFactura = subirFactura;
window.eliminarProductoModal = eliminarProductoModal;
window.aplicarProductosFactura = aplicarProductosFactura;
window.actualizarNombreProducto = actualizarNombreProducto;
window.buscarProductoExistente = buscarProductoExistente;
window.actualizarProductoExistente = actualizarProductoExistente;
window.showHistorialPreview = function() {
    const modal = document.getElementById("modalHistorialPreview");
    const iframe = document.getElementById('iframeHistorialPreview');
    const routeElement = document.getElementById('historial-preview-route');
    if (routeElement) {
        iframe.src = routeElement.dataset.historialPreview + '?t=' + Date.now();
    }
    modal.style.display = 'flex';
};

/* =============================================
   INICIALIZACIÓN
   ============================================= */
document.addEventListener('DOMContentLoaded', function() {
    // console.log('Inicializando pedidos...');

    // Crear input file para PDF (una sola vez)
    const inputFile = document.createElement('input');
    inputFile.type = 'file';
    inputFile.id = 'inputFactura';
    inputFile.accept = 'application/pdf';
    inputFile.style.display = 'none';
    inputFile.addEventListener('change', handleFileUpload);
    document.body.appendChild(inputFile);

    // No depender de botones que vienen en el HTML dinámico.
    // Solo forzamos carga inicial del bloque de "Mis Pedidos".
    // console.log('Cargando pedidos por defecto (inicial)...');
    setTimeout(function() {
        recargarPedidos();
    }, 100);

    // console.log('Pedidos inicializados correctamente');
});