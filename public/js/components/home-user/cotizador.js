//FORMATEAR PRECIOS
function formatearPrecio(precio) {
    let numero;
    if (typeof precio === 'string' && precio.includes('.')) {
        numero = Math.round(parseFloat(precio));
    } else {
        numero = Math.round(parseFloat(precio) || 0);
    }
    return numero.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

// NORMALIZAR TEXTO
function normalizarTexto(txt) {
    return txt
        .toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
        .replace(/[^a-z0-9 ]/g, " ");
}

// FILTRAR POR PRODUCTO INVENTARIO
function filtrarPorProductoInventario() {
    const select = document.getElementById("producto_id_modal_scraping");
    
    if (!select) {
        console.error('❌ [FILTRO SCRAPING] Select no encontrado');
        return productosGlobal;
    }
    
    const selectedValue = select.value;
    const selectedIndex = select.selectedIndex;
    const selectedOption = select.options[selectedIndex];
    
    console.log('📋 [FILTRO SCRAPING] Valor:', selectedValue);
    console.log('📋 [FILTRO SCRAPING] Index:', selectedIndex);
    console.log('📋 [FILTRO SCRAPING] Opción seleccionada:', selectedOption?.textContent);
    console.log('📋 [FILTRO SCRAPING] Tipo:', typeof selectedValue);
    console.log('📊 [FILTRO SCRAPING] Total:', productosGlobal.length);

    // ✅ CORREGIDO: Si hay un producto específico seleccionado
    if (selectedValue) { // <-- CAMBIO: Simplificado para coincidir con la lógica del filtro GPT
        const selectedText = selectedOption.textContent;
        const nombreInventario = normalizarTexto(
            selectedText.split("(")[0].trim()
        );
        
        console.log('🔍 [FILTRO SCRAPING] Filtrando producto específico:', nombreInventario);

        const filtrados = productosGlobal.filter(p =>
            normalizarTexto(p.nombreProducto).includes(nombreInventario)
        );

        console.log('✅ [FILTRO SCRAPING] Productos filtrados:', filtrados.length);
        return filtrados;
    }

    // SI value vacío = FILTRAR POR TODOS LOS DE BAJO STOCK
    console.log('📦 [FILTRO SCRAPING] Filtrando por TODOS los productos de bajo stock');
    
    const opcionesProductos = Array.from(select.options).slice(1);
    const productosCoincidentes = [];

    opcionesProductos.forEach(opcion => {
        const nombreInventario = normalizarTexto(
            opcion.textContent.split("(")[0].trim()
        );

        const coincidencias = productosGlobal.filter(p =>
            normalizarTexto(p.nombreProducto).includes(nombreInventario)
        );

        productosCoincidentes.push(...coincidencias);
    });

    const unicos = Array.from(
        new Map(productosCoincidentes.map(p => [p.linkProducto, p])).values()
    );

    console.log('✅ [FILTRO SCRAPING] Total productos con bajo stock:', unicos.length);
    return unicos;
}

// FILTRAR POR PRODUCTO INVENTARIO - GPT 
function filtrarPorProductoInventarioGpt() {
    const select = document.getElementById("producto_id_modal_gpt");
    const selectedValue = select.value;

    if (selectedValue) {
        const selectedText = select.options[select.selectedIndex].textContent;
        const nombreInventario = normalizarTexto(
            selectedText.split("(")[0].trim()
        );

        const filtrados = productosGlobalGpt.filter(p =>
            normalizarTexto(p.nombreProducto).includes(nombreInventario)
        );

        return filtrados;
    }

    const opcionesProductos = Array.from(select.options).slice(1);
    const productosCoincidentes = [];

    opcionesProductos.forEach(opcion => {
        const nombreInventario = normalizarTexto(
            opcion.textContent.split("(")[0].trim()
        );

        const coincidencias = productosGlobalGpt.filter(p =>
            normalizarTexto(p.nombreProducto).includes(nombreInventario)
        );

        productosCoincidentes.push(...coincidencias);
    });

    const unicos = Array.from(
        new Map(productosCoincidentes.map(p => [p.linkProducto, p])).values()
    );

    return unicos;
}

// MOSTRAR PRODUCTOS DESTACADOS
function mostrarProductosDestacados(lista) {
    const seccion = document.getElementById("seccionDestacados");
    const carousel = document.getElementById("carouselDestacados");
    const infoPromedio = document.getElementById("infoPromedio");

    if (lista.length < 2) {
        seccion.style.display = "none";
        return;
    }

    const precios = lista.map(p => parseFloat(p.precioProducto) || 0).filter(p => p > 0);
    
    if (precios.length === 0) {
        seccion.style.display = "none";
        return;
    }
    
    const promedio = precios.reduce((a, b) => a + b, 0) / precios.length;

    const productosBaratos = lista
        .filter(p => {
            const precio = parseFloat(p.precioProducto);
            return precio > 0 && precio <= promedio;
        })
        .sort((a, b) => parseFloat(a.precioProducto) - parseFloat(b.precioProducto))
        .slice(0, 4);

    if (productosBaratos.length === 0) {
        seccion.style.display = "none";
        return;
    }

    seccion.style.display = "block";
    carousel.innerHTML = "";

    productosBaratos.forEach((p, index) => {
        const badge = index === 0 ? " MÁS BARATO" : " DESTACADO";
        const badgeColor = index === 0 ? "#f89d13" : "#17a2b8";
        const ahorro = promedio - parseFloat(p.precioProducto);
        
        const addPedidoRoute = window.addPedidoRoute || '/inventario/addPedido';
        
        carousel.innerHTML += `
            <div class="scraping-card" style="min-width: 300px; max-width: 300px; flex-shrink: 0; border: 3px solid ${badgeColor}; position: relative; scroll-snap-align: start; background: white; border-radius: 8px; overflow: hidden;">
                <div class="badge-destacado" style="background: ${badgeColor};">
                    ${badge}
                </div>
                <img src="${p.imagen}" style="height: 200px; width: 100%; object-fit: cover;">
                <div style="padding: 15px;">
                    <div style="font-weight: bold; margin-bottom: 10px; color: #374151;">${p.nombreProducto}</div>
                    <div style="color: ${badgeColor}; font-size: 28px; font-weight: bold; margin-bottom: 5px;">$${formatearPrecio(p.precioProducto)}</div>
                    ${ahorro > 0 ? `<div style="color: #10b981; font-size: 12px; margin-bottom: 5px;"> Ahorras $${formatearPrecio(ahorro)} en comparacion al Promedio</div>` : ''}
                    <div style="font-size: 13px; color: #6b7280; background: #f0f0f0; padding: 5px; border-radius: 5px; margin-bottom: 10px;">
                        🏪 ${p.tienda || 'Tienda'}
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <a href="${p.linkProducto}" target="_blank" style="text-decoration: none; background: #17a2b8; color: white; padding: 8px 15px; border-radius: 5px; text-align: center;">Ver producto</a>
                        <form action="${addPedidoRoute}" method="POST" class="form-add-pedido">
                            ${window.csrfToken ? `<input type="hidden" name="_token" value="${window.csrfToken}">` : ''}
                            <input type="hidden" name="nombreProducto" value="${p.nombreProducto}">
                            <input type="hidden" name="precioProducto" value="${p.precioProducto}">
                            <input type="hidden" name="descripcion" value="${p.descripcion}">
                            <input type="hidden" name="linkProducto" value="${p.linkProducto}">
                            <input type="hidden" name="imagen" value="${p.imagen}">
                            <input type="hidden" name="categoria" value="${p.categoria}">
                            <input type="hidden" name="subcategoria" value="${p.subcategoria}">
                            <input type="hidden" name="tienda" value="${p.tienda}">
                            <button type="submit" style="width: 100%; background: #f89d13; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; font-weight: bold;">Añadir a Pedidos</button>
                        </form>
                    </div>
                </div>
            </div>
        `;
    });

    const precioMin = Math.min(...productosBaratos.map(p => parseFloat(p.precioProducto)));
    const precioMax = Math.max(...productosBaratos.map(p => parseFloat(p.precioProducto)));
    
    infoPromedio.innerHTML = `
        Precio promedio: <strong>$${formatearPrecio(promedio)}</strong> | 
        Rango destacados: <strong>$${formatearPrecio(precioMin)} - $${formatearPrecio(precioMax)}</strong> | 
        ${productosBaratos.length} producto(s) con mejor precio
    `;
}

// MOSTRAR PRODUCTOS DESTACADOS - GPT
function mostrarProductosDestacadosGpt(lista) {
    const seccion = document.getElementById("seccionDestacadosGpt");
    const carousel = document.getElementById("carouselDestacadosGpt");
    const infoPromedio = document.getElementById("infoPromedioGpt");

    if (lista.length < 2) {
        seccion.style.display = "none";
        return;
    }

    const precios = lista.map(p => parseFloat(p.precioProducto) || 0).filter(p => p > 0);
    
    if (precios.length === 0) {
        seccion.style.display = "none";
        return;
    }
    
    const promedio = precios.reduce((a, b) => a + b, 0) / precios.length;

    const productosBaratos = lista
        .filter(p => {
            const precio = parseFloat(p.precioProducto);
            return precio > 0 && precio <= promedio;
        })
        .sort((a, b) => parseFloat(a.precioProducto) - parseFloat(b.precioProducto))
        .slice(0, 4);

    if (productosBaratos.length === 0) {
        seccion.style.display = "none";
        return;
    }

    seccion.style.display = "block";
    carousel.innerHTML = "";

    productosBaratos.forEach((p, index) => {
        const badge = index === 0 ? " MÁS BARATO" : " DESTACADO";
        const badgeColor = index === 0 ? "#f89d13" : "#17a2b8";
        const ahorro = promedio - parseFloat(p.precioProducto);
        
        const addPedidoRoute = window.addPedidoRoute || '/inventario/addPedido';
        
        carousel.innerHTML += `
            <div class="scraping-card" style="min-width: 300px; max-width: 300px; flex-shrink: 0; border: 3px solid ${badgeColor}; position: relative; scroll-snap-align: start; background: white; border-radius: 8px; overflow: hidden;">
                <div class="badge-destacado" style="background: ${badgeColor};">
                    ${badge}
                </div>
                <img src="${p.imagen}" style="height: 200px; width: 100%; object-fit: cover;">
                <div style="padding: 15px;">
                    <div style="font-weight: bold; margin-bottom: 10px; color: #374151;">${p.nombreProducto}</div>
                    <div style="color: ${badgeColor}; font-size: 28px; font-weight: bold; margin-bottom: 5px;">$${formatearPrecio(p.precioProducto)}</div>
                    ${ahorro > 0 ? `<div style="color: #10b981; font-size: 12px; margin-bottom: 5px;"> Ahorras $${formatearPrecio(ahorro)} en comparacion al Promedio</div>` : ''}
                    <div style="font-size: 13px; color: #6b7280; background: #f0f0f0; padding: 5px; border-radius: 5px; margin-bottom: 10px;">
                        🏪 ${p.tienda || 'Tienda'}
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <a href="${p.linkProducto}" target="_blank" style="text-decoration: none; background: #17a2b8; color: white; padding: 8px 15px; border-radius: 5px; text-align: center;">Ver producto</a>
                        <form action="${addPedidoRoute}" method="POST" class="form-add-pedido">
                            ${window.csrfToken ? `<input type="hidden" name="_token" value="${window.csrfToken}">` : ''}
                            <input type="hidden" name="nombreProducto" value="${p.nombreProducto}">
                            <input type="hidden" name="precioProducto" value="${p.precioProducto}">
                            <input type="hidden" name="descripcion" value="${p.descripcion}">
                            <input type="hidden" name="linkProducto" value="${p.linkProducto}">
                            <input type="hidden" name="imagen" value="${p.imagen}">
                            <input type="hidden" name="categoria" value="${p.categoria}">
                            <input type="hidden" name="subcategoria" value="${p.subcategoria}">
                            <input type="hidden" name="tienda" value="${p.tienda}">
                            <button type="submit" style="width: 100%; background: #f89d13; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; font-weight: bold;">Añadir a Pedidos</button>
                        </form>
                    </div>
                </div>
            </div>
        `;
    });

    const precioMin = Math.min(...productosBaratos.map(p => parseFloat(p.precioProducto)));
    const precioMax = Math.max(...productosBaratos.map(p => parseFloat(p.precioProducto)));
    
    infoPromedio.innerHTML = `
        Precio promedio: <strong>$${formatearPrecio(promedio)}</strong> | 
        Rango destacados: <strong>$${formatearPrecio(precioMin)} - $${formatearPrecio(precioMax)}</strong> | 
        ${productosBaratos.length} producto(s) con mejor precio
    `;
}

// MOSTRAR PÁGINA - SCRAPING
function mostrarPagina(lista, pagina) {
    const cont = document.getElementById("listaScraping");
    const paginacion = document.getElementById("paginacionScraping");

    console.log('📄 [MOSTRAR PÁGINA] Mostrando página Scraping:', pagina, '- Total recibido:', lista.length);
    
    if (!lista || lista.length === 0) {
        console.warn('⚠️ [MOSTRAR PÁGINA] Lista vacía o undefined');
        cont.innerHTML = '<div style="text-align:center; padding:50px; color:#666;">No se encontraron productos</div>';
        paginacion.innerHTML = "";
        return;
    }

    cont.innerHTML = "";
    paginacion.innerHTML = "";
    paginaActual = pagina;

    const inicio = (pagina - 1) * porPagina;
    const fin = pagina * porPagina;
    const data = lista.slice(inicio, fin);
    
    console.log('📄 [MOSTRAR PÁGINA] Productos a mostrar en esta página:', data.length);
    
    const addPedidoRoute = window.addPedidoRoute || '/inventario/addPedido';

    data.forEach(p => {
        cont.innerHTML += `
            <div class="producto-horizontal">
                <img src="${p.imagen}" alt="${p.nombreProducto}">
                <div class="producto-info-horizontal">
                    <h3 class="producto-nombre-horizontal">${p.nombreProducto}</h3>
                    <div class="producto-tienda-horizontal">🏪 <strong>${p.tienda || 'Tienda'}</strong></div>
                    <div class="producto-link-horizontal"><a href="${p.linkProducto}" target="_blank">🔗 Ver producto en tienda</a></div>
                </div>
                <div class="producto-precio-horizontal">$${formatearPrecio(p.precioProducto)}</div>
                <div class="producto-accion-horizontal">
                    <form action="${addPedidoRoute}" method="POST" class="form-add-pedido">
                        ${window.csrfToken ? `<input type="hidden" name="_token" value="${window.csrfToken}">` : ''}
                        <input type="hidden" name="nombreProducto" value="${p.nombreProducto}">
                        <input type="hidden" name="precioProducto" value="${p.precioProducto}">
                        <input type="hidden" name="descripcion" value="${p.descripcion}">
                        <input type="hidden" name="linkProducto" value="${p.linkProducto}">
                        <input type="hidden" name="imagen" value="${p.imagen}">
                        <input type="hidden" name="categoria" value="${p.categoria}">
                        <input type="hidden" name="subcategoria" value="${p.subcategoria}">
                        <input type="hidden" name="tienda" value="${p.tienda}">
                        <button type="submit">Añadir a Mis Pedidos</button>
                    </form>
                </div>
            </div>
        `;
    });

    const total = Math.ceil(lista.length / porPagina);
    for (let i = 1; i <= total; i++) {
        const b = document.createElement("button");
        b.textContent = i;
        if (i === paginaActual) b.classList.add("active");
        b.onclick = () => mostrarPagina(lista, i);
        paginacion.appendChild(b);
    }
    mostrarProductosDestacados(lista);

}

// MOSTRAR PÁGINA - GPT
function mostrarPaginaGpt(lista, pagina) {
    const cont = document.getElementById("listaGpt");
    const paginacion = document.getElementById("paginacionGpt");

    mostrarProductosDestacadosGpt(lista);

    cont.innerHTML = "";
    paginacion.innerHTML = "";
    paginaActualGpt = pagina;

    const inicio = (pagina - 1) * porPagina;
    const fin = pagina * porPagina;
    const data = lista.slice(inicio, fin);
    
    const addPedidoRoute = window.addPedidoRoute || '/inventario/addPedido';

    data.forEach(p => {
        cont.innerHTML += `
            <div class="producto-horizontal">
                <img src="${p.imagen}" alt="${p.nombreProducto}">
                <div class="producto-info-horizontal">
                    <h3 class="producto-nombre-horizontal">${p.nombreProducto}</h3>
                    <div class="producto-tienda-horizontal">🏪 <strong>${p.tienda || 'Tienda'}</strong></div>
                    <div class="producto-link-horizontal"><a href="${p.linkProducto}" target="_blank">🔗 Ver producto en tienda</a></div>
                </div>
                <div class="producto-precio-horizontal">$${formatearPrecio(p.precioProducto)}</div>
                <div class="producto-accion-horizontal">
                    <form action="${addPedidoRoute}" method="POST" class="form-add-pedido">
                        ${window.csrfToken ? `<input type="hidden" name="_token" value="${window.csrfToken}">` : ''}
                        <input type="hidden" name="nombreProducto" value="${p.nombreProducto}">
                        <input type="hidden" name="precioProducto" value="${p.precioProducto}">
                        <input type="hidden" name="descripcion" value="${p.descripcion}">
                        <input type="hidden" name="linkProducto" value="${p.linkProducto}">
                        <input type="hidden" name="imagen" value="${p.imagen}">
                        <input type="hidden" name="categoria" value="${p.categoria}">
                        <input type="hidden" name="subcategoria" value="${p.subcategoria}">
                        <input type="hidden" name="tienda" value="${p.tienda}">
                        <button type="submit">Añadir a Mis Pedidos</button>
                    </form>
                </div>
            </div>
        `;
    });

    const total = Math.ceil(lista.length / porPagina);
    for (let i = 1; i <= total; i++) {
        const b = document.createElement("button");
        b.textContent = i;
        if (i === paginaActualGpt) b.classList.add("active");
        b.onclick = () => mostrarPaginaGpt(lista, i);
        paginacion.appendChild(b);
    }
}

// BÚSQUEDA SCRAPING
function realizarBusqueda() {
    const inputBuscarScraping = document.getElementById("inputBuscarScraping");
    const q = normalizarTexto(inputBuscarScraping.value.trim());

    if (q === "") {
        aplicarFiltroInventario();
        return;
    }

    const resultados = productosGlobal.filter(p =>
        normalizarTexto(p.nombreProducto).includes(q)
    );

    mostrarPagina(resultados, 1);
}

// BÚSQUEDA GPT CON API
async function realizarBusquedaGpt() {
    const inputBuscarGpt = document.getElementById("inputBuscarGpt");
    const select = document.getElementById("producto_id_modal_gpt");
    const q = inputBuscarGpt.value.trim();

    if (q === "") {
        aplicarFiltroInventarioGpt();
        return;
    }

    const qNormalizado = normalizarTexto(q);

    if (select.value) {
        const productosFiltrados = filtrarPorProductoInventarioGpt();
        const resultadosLocales = productosFiltrados.filter(p =>
            normalizarTexto(p.nombreProducto).includes(qNormalizado)
        );

        if (resultadosLocales.length > 0) {
            mostrarPaginaGpt(resultadosLocales, 1);
            return;
        }
    }

    if (!select.value) {
        const resultadosLocales = productosGlobalGpt.filter(p =>
            normalizarTexto(p.nombreProducto).includes(qNormalizado)
        );

        if (resultadosLocales.length > 0) {
            mostrarPaginaGpt(resultadosLocales, 1);
            return;
        }
    }

    mostrarLoader(true);

    try {
        const respuesta = await fetch("https://api-inventario-morning-paper-8617.fly.dev/api//recibir-productos/", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRFToken": window.csrfToken || ""
            },
            body: JSON.stringify({ productos: [q] })
        });

        if (!respuesta.ok) throw new Error(`Error HTTP: ${respuesta.status}`);

        const data = await respuesta.json();

        if (data.productos && data.productos.length > 0) {
            const productosNuevos = data.productos.map(p => ({
                nombreProducto: p.nombre || p.nombreProducto,
                precioProducto: p.precio || p.precioProducto,
                descripcion: p.descripcion || '',
                linkProducto: p.link || p.linkProducto || '#',
                imagen: p.imagen || '/img/no-img.png',
                categoria: p.categoria || '',
                subcategoria: p.subcategoria || '',
                tienda: p.tienda || 'Online'
            }));

            productosNuevos.forEach(nuevo => {
                const indice = productosGlobalGpt.findIndex(
                    p => p.linkProducto === nuevo.linkProducto
                );
                if (indice !== -1) {
                    productosGlobalGpt[indice] = nuevo;
                } else {
                    productosGlobalGpt.push(nuevo);
                }
            });

            localStorage.setItem("gpt_productos", JSON.stringify(productosGlobalGpt));

            const resultados = productosGlobalGpt.filter(p =>
                normalizarTexto(p.nombreProducto).includes(qNormalizado)
            );

            mostrarPaginaGpt(resultados, 1);
            mostrarAlerta(`Se encontraron ${data.productos.length} producto(s)`, 'success');
        } else {
            mostrarAlerta('No se encontraron productos para esta búsqueda', 'error');
            mostrarPaginaGpt([], 1);
        }

    } catch (error) {
        console.error("Error en búsqueda GPT:", error);
        mostrarAlerta(`Error al buscar: ${error.message}`, "error");
        mostrarPaginaGpt([], 1);
    } finally {
        mostrarLoader(false);
    }
}

// APLICAR FILTRO SCRAPING
function aplicarFiltroInventario() {
    console.log('🔍 [SCRAPING] Iniciando aplicarFiltroInventario');
    
    const select = document.getElementById("producto_id_modal_scraping");
    
    if (!select) {
        console.error('❌ [SCRAPING] Select no encontrado');
        return;
    }
    
    console.log('📋 [SCRAPING] Valor del select:', select.value);
    console.log('📊 [SCRAPING] Total productos:', productosGlobal.length);
    
    const filtrados = filtrarPorProductoInventario();
    
    console.log('✅ [SCRAPING] Productos filtrados:', filtrados.length);
    
    if (select.value) {
        filtroActivo = true;
        const btnQuitarFiltro = document.getElementById("btnQuitarFiltro");
        if (btnQuitarFiltro) btnQuitarFiltro.style.display = "inline-block";
    } else {
        filtroActivo = false;
        const btnQuitarFiltro = document.getElementById("btnQuitarFiltro");
        if (btnQuitarFiltro) btnQuitarFiltro.style.display = "none";
    }
    
    mostrarPagina(filtrados, 1);
    console.log('✅ [SCRAPING] Filtro aplicado');
}

// APLICAR FILTRO GPT CON FETCH
async function aplicarFiltroInventarioGpt() {
    const select = document.getElementById("producto_id_modal_gpt");
    const selectedValue = select.value;
    
    if (selectedValue) {
        const selectedText = select.options[select.selectedIndex].textContent;
        const nombreInventario = selectedText.split("(")[0].trim();
        const qNormalizado = normalizarTexto(nombreInventario);
        
        const filtrados = productosGlobalGpt.filter(p =>
            normalizarTexto(p.nombreProducto).includes(qNormalizado)
        );
        
        if (filtrados.length > 0) {
            filtroActivoGpt = true;
            document.getElementById("btnQuitarFiltroGpt").style.display = "inline-block";
            mostrarPaginaGpt(filtrados, 1);
            return;
        }
        
        mostrarLoader(true);
        
        try {
            const respuesta = await fetch("https://api-inventario-morning-paper-8617.fly.dev/api//recibir-productos/", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRFToken": window.csrfToken || ""
                },
                body: JSON.stringify({ productos: [nombreInventario] })
            });

            if (!respuesta.ok) throw new Error(`Error HTTP: ${respuesta.status}`);

            const data = await respuesta.json();

            if (data.productos && data.productos.length > 0) {
                const productosNuevos = data.productos.map(p => ({
                    nombreProducto: p.nombre || p.nombreProducto,
                    precioProducto: p.precio || p.precioProducto,
                    descripcion: p.descripcion || '',
                    linkProducto: p.link || p.linkProducto || '#',
                    imagen: p.imagen || '/img/no-img.png',
                    categoria: p.categoria || '',
                    subcategoria: p.subcategoria || '',
                    tienda: p.tienda || 'Online'
                }));

                productosNuevos.forEach(nuevo => {
                    const indice = productosGlobalGpt.findIndex(
                        p => p.linkProducto === nuevo.linkProducto
                    );
                    if (indice !== -1) {
                        productosGlobalGpt[indice] = nuevo;
                    } else {
                        productosGlobalGpt.push(nuevo);
                    }
                });

                localStorage.setItem("gpt_productos", JSON.stringify(productosGlobalGpt));
                
                const resultados = productosGlobalGpt.filter(p =>
                    normalizarTexto(p.nombreProducto).includes(qNormalizado)
                );

                filtroActivoGpt = true;
                document.getElementById("btnQuitarFiltroGpt").style.display = "inline-block";
                mostrarPaginaGpt(resultados, 1);
                mostrarAlerta(`Se encontraron ${data.productos.length} producto(s)`, 'success');
            } else {
                mostrarAlerta('No se encontraron productos para este filtro', 'error');
                mostrarPaginaGpt([], 1);
            }

        } catch (error) {
            console.error("Error en filtro GPT:", error);
            mostrarAlerta(`Error al buscar: ${error.message}`, "error");
            mostrarPaginaGpt([], 1);
        } finally {
            mostrarLoader(false);
        }
        
        return;
    }
    
    const filtrados = filtrarPorProductoInventarioGpt();
    
    filtroActivoGpt = false;
    document.getElementById("btnQuitarFiltroGpt").style.display = "none";
    mostrarPaginaGpt(filtrados, 1);
}

// FETCH SCRAPING
async function fetchScrapingReal() {
    try {
        const res = await fetch("https://api-inventario-morning-paper-8617.fly.dev/scraping/all/");
        const data = await res.json();

        let lista = [];
        for (const tienda in data.productos) {
            const productos = data.productos[tienda].productos || [];
            lista.push(...productos);
        }
        return lista;
    } catch (error) {
        console.error("Error obteniendo scraping:", error);
        return [];
    }
}

// FETCH GPT PRODUCTS
async function fetchGptProducts() {
    try {
        const response = await fetch('/api/gpt-products');
        const data = await response.json();
        return data.productos || [];
    } catch (error) {
        console.error("Error obteniendo productos de GPT:", error);
        return [];
    }
}

// MOSTRAR/OCULTAR LOADER
function mostrarLoader(mostrar = true) {
    let loader = document.getElementById('loaderOverlay');
    
    if (mostrar) {
        if (!loader) {
            loader = document.createElement('div');
            loader.id = 'loaderOverlay';
            loader.className = 'loader-overlay';
            loader.innerHTML = `
                <div class="loader-content">
                    <div class="loader-spinner"></div>
                    <p class="loader-text">Cargando datos...</p>
                    <p class="loader-subtext">Por favor espera, esto puede tomar unos momentos</p>
                </div>
            `;
            document.body.appendChild(loader);
        }
        loader.classList.add('active');
        document.body.style.overflow = 'hidden';
    } else {
        if (loader) {
            loader.classList.remove('active');
            document.body.style.overflow = '';
            setTimeout(() => {
                if (loader.parentNode) {
                    loader.remove();
                }
            }, 300);
        }
    }
}

// CARGAR DATOS CON CACHÉ
async function cargarDatos() {
    const SCRAPING_CACHE = "scraping_multi_tienda";
    const GPT_CACHE = "gpt_productos";
    
    mostrarLoader(true);
    
    try {
        // SCRAPING
        if (localStorage.getItem(SCRAPING_CACHE)) {
            productosGlobal = JSON.parse(localStorage.getItem(SCRAPING_CACHE));
        } else {
            productosGlobal = await fetchScrapingReal();
            if (productosGlobal.length > 0) {
                localStorage.setItem(SCRAPING_CACHE, JSON.stringify(productosGlobal));
            }
        }

        // GPT
        if (localStorage.getItem(GPT_CACHE)) {
            productosGlobalGpt = JSON.parse(localStorage.getItem(GPT_CACHE));
        } else {
            productosGlobalGpt = await fetchGptProducts();
            if (productosGlobalGpt.length > 0) {
                localStorage.setItem(GPT_CACHE, JSON.stringify(productosGlobalGpt));
            }
        }

        aplicarFiltroInventario();
        aplicarFiltroInventarioGpt();
    } catch (error) {
        console.error('Error cargando datos:', error);
        mostrarAlerta('Error al cargar los datos. Por favor, intenta nuevamente.', 'error');
    } finally {
        mostrarLoader(false);
    }
}

// ALERTAS PERSONALIZADAS
function mostrarAlerta(mensaje, tipo = 'success') {
    const alertasExistentes = document.querySelectorAll('.custom-alert');
    alertasExistentes.forEach(alerta => alerta.remove());
    
    const alerta = document.createElement('div');
    alerta.className = `custom-alert ${tipo}`;
    
    const icono = tipo === 'success' ? '✓' : '✕';
    
    alerta.innerHTML = `
        <span class="icon">${icono}</span>
        <span>${mensaje}</span>
        <button class="close-alert" onclick="cerrarAlerta(this)">✕</button>
    `;
    
    document.body.appendChild(alerta);
    
    setTimeout(() => {
        if (alerta && alerta.parentNode) {
            cerrarAlerta(alerta.querySelector('.close-alert'));
        }
    }, 4000);
}

function cerrarAlerta(btn) {
    const alerta = btn.closest('.custom-alert');
    if (alerta) {
        alerta.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => {
            if (alerta.parentNode) {
                alerta.remove();
            }
        }, 300);
    }
}


// VARIABLES GLOBALES
let productosGlobal = [];
let productosGlobalGpt = [];
let paginaActual = 1;
let paginaActualGpt = 1;
let porPagina = 10;
let filtroActivo = false;
let filtroActivoGpt = false;
let timeoutBusqueda = null;
let timeoutBusquedaGpt = null;
let pestanaActiva = 'scraping';

// EXPONER FUNCIONES
window.mostrarAlerta = mostrarAlerta;
window.cerrarAlerta = cerrarAlerta;

// INICIALIZACIÓN
window.addEventListener('DOMContentLoaded', () => {
    
    const btnApi = document.getElementById("btnApi");
    const btnScraping = document.getElementById("btnScraping");
    const contScraping = document.getElementById("contenedorScraping");
    const contGpt = document.getElementById("contenedorGpt");

    if (btnApi) {
        btnApi.onclick = function() {
            if (this.classList.contains('active')) return;
            
            this.classList.add('active');
            btnScraping.classList.remove('active');
            
            contGpt.classList.remove("hidden");
            contScraping.classList.add("hidden");
            
            pestanaActiva = 'gpt';

            aplicarFiltroInventarioGpt();
        };
    }

    if (btnScraping) {
        btnScraping.onclick = function() {
            if (this.classList.contains('active')) return;
            
            this.classList.add('active');
            btnApi.classList.remove('active');
            
            contScraping.classList.remove("hidden");
            contGpt.classList.add("hidden");
            
            pestanaActiva = 'scraping';

            aplicarFiltroInventario();
        };
    }

    // ENVIAR PRODUCTOS A LA API (7 ALEATORIOS)
    const btnEnviarAApi = document.getElementById("btnEnviarAApi");

    if (btnEnviarAApi) {
        btnEnviarAApi.onclick = async () => {
            try {
                const select = document.getElementById("producto_id_modal_gpt");
                const opcionesProductos = Array.from(select.options).slice(1);
                
                const todosLosProductos = opcionesProductos.map(opcion => {
                    return opcion.textContent.split("(")[0].trim();
                });

                let productosAEnviar;
                
                if (todosLosProductos.length > 7) {
                    console.log(`Tiene ${todosLosProductos.length} productos - Seleccionando 7 aleatorios`);
                    
                    const productosAleatorios = [...todosLosProductos];
                    for (let i = productosAleatorios.length - 1; i > 0; i--) {
                        const j = Math.floor(Math.random() * (i + 1));
                        [productosAleatorios[i], productosAleatorios[j]] = [productosAleatorios[j], productosAleatorios[i]];
                    }
                    productosAEnviar = productosAleatorios.slice(0, 7);
                } else {
                    productosAEnviar = todosLosProductos;
                }

                if (productosAEnviar.length === 0) {
                    mostrarAlerta("No hay productos con bajo stock para enviar", "error");
                    return;
                }

                mostrarLoader(true);

                const respuesta = await fetch("https://api-inventario-morning-paper-8617.fly.dev/api//recibir-productos/", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRFToken": window.csrfToken || ""
                    },
                    body: JSON.stringify({ productos: productosAEnviar })
                });

                if (!respuesta.ok) throw new Error(`Error HTTP: ${respuesta.status}`);

                const data = await respuesta.json();

                if (data.productos && data.productos.length > 0) {
                    const productosNuevos = data.productos.map(p => ({
                        nombreProducto: p.nombre || p.nombreProducto,
                        precioProducto: p.precio || p.precioProducto,
                        descripcion: p.descripcion || '',
                        linkProducto: p.link || p.linkProducto || '#',
                        imagen: p.imagen || '/img/no-img.png',
                        categoria: p.categoria || '',
                        subcategoria: p.subcategoria || '',
                        tienda: p.tienda || 'Online'
                    }));
                    
                    productosNuevos.forEach(nuevo => {
                        const indice = productosGlobalGpt.findIndex(
                            p => p.linkProducto === nuevo.linkProducto
                        );

                        if (indice !== -1) {
                            productosGlobalGpt[indice] = nuevo;
                        } else {
                            productosGlobalGpt.push(nuevo);
                        }
                    });
                    
                    localStorage.setItem("gpt_productos", JSON.stringify(productosGlobalGpt));
                    
                    if (!document.getElementById("contenedorGpt").classList.contains("hidden")) {
                        aplicarFiltroInventarioGpt();
                    }
                }

                mostrarAlerta(`${data.productos.length} Productos encontrados en Tiendas Online.`, "success");

            } catch (error) {
                console.error("Error:", error);
                mostrarAlerta(`Error al comunicarse con la API: ${error.message}`, "error");
            } finally {
                mostrarLoader(false);
            }
        };
    }

    // ======================================================
    // MODALES DE FILTROS INDEPENDIENTES (SCRAPING y GPT)
    // ======================================================
    const modalScraping = document.getElementById("modalFiltrosScraping");
    const modalGpt = document.getElementById("modalFiltrosGpt");

    const btnAbrirFiltros = document.getElementById("btnAbrirFiltros");
    const btnAbrirFiltrosGpt = document.getElementById("btnAbrirFiltrosGpt");

    const btnCerrarModalScraping = document.getElementById("btnCerrarModalScraping");
    const btnCerrarModalGpt = document.getElementById("btnCerrarModalGpt");

    const btnAplicarFiltroScraping = document.getElementById("btnAplicarFiltroScraping");
    const btnAplicarFiltroGpt = document.getElementById("btnAplicarFiltroGpt");

    const btnQuitarFiltro = document.getElementById("btnQuitarFiltro");       // scraping
    const btnQuitarFiltroGpt = document.getElementById("btnQuitarFiltroGpt"); // gpt

    // Abrir modales
    if (btnAbrirFiltros) {
        btnAbrirFiltros.onclick = (e) => {
            e.preventDefault();
            if (modalScraping) modalScraping.style.display = 'block';
        };
    }
    if (btnAbrirFiltrosGpt) {
        btnAbrirFiltrosGpt.onclick = (e) => {
            e.preventDefault();
            if (modalGpt) modalGpt.style.display = 'block';
        };
    }

    // Cerrar modales (botón)
    if (btnCerrarModalScraping && modalScraping) {
        btnCerrarModalScraping.onclick = () => modalScraping.style.display = 'none';
    }
    if (btnCerrarModalGpt && modalGpt) {
        btnCerrarModalGpt.onclick = () => modalGpt.style.display = 'none';
    }

    // Click fuera del modal cierra
    if (modalScraping) {
        modalScraping.addEventListener('click', (e) => {
            if (e.target === modalScraping) modalScraping.style.display = 'none';
        });
    }
    if (modalGpt) {
        modalGpt.addEventListener('click', (e) => {
            if (e.target === modalGpt) modalGpt.style.display = 'none';
        });
    }

    // Aplicar filtros
    if (btnAplicarFiltroScraping) {
        btnAplicarFiltroScraping.onclick = () => {
            aplicarFiltroInventario();
            if (modalScraping) modalScraping.style.display = 'none';
        };
    }
    if (btnAplicarFiltroGpt) {
        btnAplicarFiltroGpt.onclick = async () => {
            await aplicarFiltroInventarioGpt();
            if (modalGpt) modalGpt.style.display = 'none';
        };
    }

    // Quitar filtros
    if (btnQuitarFiltro) {
        btnQuitarFiltro.onclick = () => {
            const select = document.getElementById("producto_id_modal_scraping");
            if (select) select.selectedIndex = 0;
            filtroActivo = false;
            btnQuitarFiltro.style.display = "none";
            aplicarFiltroInventario();
            if (modalScraping) modalScraping.style.display = 'none';
        };
    }

    if (btnQuitarFiltroGpt) {
        btnQuitarFiltroGpt.onclick = async () => {
            const select = document.getElementById("producto_id_modal_gpt");
            if (select) select.selectedIndex = 0;
            filtroActivoGpt = false;
            btnQuitarFiltroGpt.style.display = "none";
            await aplicarFiltroInventarioGpt();
            if (modalGpt) modalGpt.style.display = 'none';
        };
    }
    
    // BÚSQUEDA SCRAPING
    const inputBuscarScraping = document.getElementById("inputBuscarScraping");
    const clearBuscarScraping = document.getElementById("clearBuscarScraping");
    
    if (inputBuscarScraping) {
        inputBuscarScraping.addEventListener("keypress", (e) => {
            if (e.key === "Enter") {
                if (timeoutBusqueda) {
                    clearTimeout(timeoutBusqueda);
                    timeoutBusqueda = null;
                }
                realizarBusqueda();
            }
        });
        
        inputBuscarScraping.addEventListener("input", (e) => {
            if (timeoutBusqueda) {
                clearTimeout(timeoutBusqueda);
            }
            
            const valor = e.target.value.trim();
            
            if (valor === "") {
                timeoutBusqueda = setTimeout(() => {
                    aplicarFiltroInventario();
                    timeoutBusqueda = null;
                }, 500);
            }
        });
    }
    
    if (clearBuscarScraping) {
        clearBuscarScraping.onclick = () => {
            inputBuscarScraping.value = "";
            if (timeoutBusqueda) {
                clearTimeout(timeoutBusqueda);
                timeoutBusqueda = null;
            }
            aplicarFiltroInventario();
        };
    }

    // BÚSQUEDA GPT
    const inputBuscarGpt = document.getElementById("inputBuscarGpt");
    const clearBuscarGpt = document.getElementById("clearBuscarGpt");
    
    if (inputBuscarGpt) {
        inputBuscarGpt.addEventListener("keypress", (e) => {
            if (e.key === "Enter") {
                if (timeoutBusquedaGpt) {
                    clearTimeout(timeoutBusquedaGpt);
                    timeoutBusquedaGpt = null;
                }
                realizarBusquedaGpt();
            }
        });
        
        inputBuscarGpt.addEventListener("input", (e) => {
            if (timeoutBusquedaGpt) {
                clearTimeout(timeoutBusquedaGpt);
            }
            
            const valor = e.target.value.trim();
            
            if (valor === "") {
                timeoutBusquedaGpt = setTimeout(() => {
                    aplicarFiltroInventarioGpt();
                    timeoutBusquedaGpt = null;
                }, 500);
            }
        });
    }
    
    if (clearBuscarGpt) {
        clearBuscarGpt.onclick = () => {
            inputBuscarGpt.value = "";
            if (timeoutBusquedaGpt) {
                clearTimeout(timeoutBusquedaGpt);
                timeoutBusquedaGpt = null;
            }
            aplicarFiltroInventarioGpt();
        };
    }
    
    // ACTUALIZAR SCRAPING
    const btnActualizarScraping = document.getElementById("btnActualizarScraping");
    if (btnActualizarScraping) {
        btnActualizarScraping.onclick = async () => {
            const SCRAPING_CACHE = "scraping_multi_tienda";
            mostrarLoader(true);
            
            try {
                localStorage.removeItem(SCRAPING_CACHE);
                productosGlobal = await fetchScrapingReal();
                if (productosGlobal.length > 0) {
                    localStorage.setItem(SCRAPING_CACHE, JSON.stringify(productosGlobal));
                }
                aplicarFiltroInventario();
                mostrarAlerta("Datos actualizados correctamente.", 'success');
            } catch (error) {
                console.error('Error actualizando:', error);
                mostrarAlerta('Error al actualizar los datos.', 'error');
            } finally {
                mostrarLoader(false);
            }
        };
    }

    // ACTUALIZAR GPT
    const btnActualizarGpt = document.getElementById("btnActualizarGpt");
    if (btnActualizarGpt) {
        btnActualizarGpt.onclick = async () => {
            const GPT_CACHE = "gpt_productos";
            mostrarLoader(true);
            
            try {
                localStorage.removeItem(GPT_CACHE);
                productosGlobalGpt = await fetchGptProducts();
                if (productosGlobalGpt.length > 0) {
                    localStorage.setItem(GPT_CACHE, JSON.stringify(productosGlobalGpt));
                }
                aplicarFiltroInventarioGpt();
                mostrarAlerta("Datos actualizados correctamente.", 'success');
            } catch (error) {
                console.error('Error actualizando:', error);
                mostrarAlerta('Error al actualizar los datos.', 'error');
            } finally {
                mostrarLoader(false);
            }
        };
    }
    
    // CARGAR DATOS INICIALES
    cargarDatos();
    
    document.addEventListener('submit', async function(e) {
        if (e.target.classList.contains('form-add-pedido')) {
            e.preventDefault();
            
            const form = e.target;
            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            
            const textoOriginal = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '⏳ Agregando...';
            
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    mostrarAlerta('Producto agregado a tus pedidos correctamente', 'success');
                    form.reset();
                    
                    // Recargar pedidos si está visible
                    if (typeof recargarPedidos === 'function') {
                        const contPedidos = document.getElementById('inventario-pedidos');
                        if (contPedidos && contPedidos.style.display !== 'none') {
                            console.log('Recargando pedidos...');
                            await recargarPedidos();
                        }
                    }
                } else {
                    mostrarAlerta('✕ ' + (data.message || 'Error'), 'error');
                }
                
            } catch (error) {
                console.error('Error:', error);
                mostrarAlerta('Error al procesar', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = textoOriginal;
            }
        }
    });
});

