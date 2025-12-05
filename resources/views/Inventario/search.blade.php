
<div>
    <button id="btnScraping" class="btn active">Tienda Oficial</button>
    <button id="btnApi" class="btn">Tienda Online</button>
    <!-- <button id="btnEnviarAApi" class="btn">Enviar productos a la API</button> -->
</div>
<hr>

<!-- TIENDA OFICIAL (SCRAPING) - VISIBLE POR DEFECTO -->
<div id="contenedorScraping" class="visible">
    <!-- BARRA DE BÚSQUEDA Y FILTROS -->
    <div class="barra-busqueda">
        <div class="search-container" style="flex: 1;">
            <input type="text" id="inputBuscarScraping" class="search-input" placeholder="Buscar producto..." style="width: 100%;">
            <button class="clear-search" id="clearBuscarScraping" title="Limpiar búsqueda">✕</button>
        </div>
        <button id="btnAbrirFiltros" class="btn-inventory">
            <i class="fas fa-filter"></i> Filtros
        </button>
        <button id="btnActualizarScraping" class="btn">Actualizar datos</button>
    </div>

<!-- MODAL FILTROS - TIENDA OFICIAL (SCRAPING) -->
<div id="modalFiltrosScraping" class="modal-overlay" style="display:none; position:fixed; inset:0; z-index:1200; background:rgba(0,0,0,0.5);">
    <div class="modal-content" style="max-width:720px; margin:60px auto; background:#fff; padding:20px; border-radius:6px;">
        <h3>Filtrar Productos - Tienda Oficial</h3>
        <label for="">Productos con bajo stock</label>
        <!-- AHORA ✅ -->
<select id="producto_id_modal_scraping" name="producto_scraping">
    <option value="">Todos los productos con bajo stock</option>
    @foreach($productos as $producto)
        <option value="{{ $producto->id }}">
            {{ $producto->nombreProducto }} ({{ $producto->cantidad }} {{ $producto->unidad }})
        </option>
    @endforeach
</select>
        <div class="modal-botones" style="margin-top:10px; display:flex; gap:8px; justify-content:flex-end;">
            <button class="btn-quitar-filtro" id="btnQuitarFiltro" style="display: none;">Quitar Filtros</button>
            <button class="btn-aplicar-filtro" id="btnAplicarFiltroScraping">Aplicar Filtro</button>
            <button class="btn-cerrar-modal" id="btnCerrarModalScraping">Cerrar</button>
        </div>
    </div>
</div>


    <br>

    <!-- SECCIÓN DE PRODUCTOS DESTACADOS -->
    <div id="seccionDestacados" style="display:none;">
        <h3>Mejores Precios</h3>
        <div style="position: relative; overflow: hidden; padding: 0 50px;">
            <div id="carouselDestacados" style="display: flex; gap: 15px; overflow-x: auto; scroll-behavior: smooth; padding: 20px 0; scroll-snap-type: x mandatory;">
            </div>
        </div>
        <p id="infoPromedio" style="text-align: center; color: #666; margin-top: 10px; font-size: 14px;"></p>
    </div>

    <h3 style="margin-top: 20px; color: #374151;">Todos los Productos</h3>
    <div id="listaScraping"></div>
    <div id="paginacionScraping"></div>
</div>

<!-- TIENDA ONLINE (GPT) - OCULTA POR DEFECTO -->
<div id="contenedorGpt" class="hidden">
    <!-- BARRA DE BÚSQUEDA Y FILTROS -->
    <div class="barra-busqueda">
        <div class="search-container" style="flex: 1;">
            <input type="text" id="inputBuscarGpt" class="search-input" placeholder="Buscar producto..." style="width: 100%;">
            <button class="clear-search" id="clearBuscarGpt" title="Limpiar búsqueda">✕</button>
        </div>
        <button id="btnAbrirFiltrosGpt" class="btn-inventory">
            <i class="fas fa-filter"></i> Filtros
        </button>
        <button id="btnActualizarGpt" class="btn">Actualizar datos</button>
    </div>

    <br>

    <!-- SECCIÓN DE PRODUCTOS DESTACADOS -->
    <div id="seccionDestacadosGpt" style="display:none;">
        <h3>Mejores Precios</h3>
        <div style="position: relative; overflow: hidden; padding: 0 50px;">
            
            <div id="carouselDestacadosGpt" style="display: flex; gap: 15px; overflow-x: auto; scroll-behavior: smooth; padding: 20px 0; scroll-snap-type: x mandatory;">
            </div>
            
        </div>
        <p id="infoPromedioGpt" style="text-align: center; color: #666; margin-top: 10px; font-size: 14px;"></p>
    </div>

    <h3 style="margin-top: 20px; color: #374151;">Todos los Productos</h3>
    <div id="listaGpt"></div>
    <div id="paginacionGpt"></div>
    <!-- MODAL FILTROS - TIENDA ONLINE (GPT) -->
    <div id="modalFiltrosGpt" class="modal-overlay" style="display:none; position:fixed; inset:0; z-index:1200; background:rgba(0,0,0,0.5);">
        <div class="modal-content" style="max-width:720px; margin:60px auto; background:#fff; padding:20px; border-radius:6px;">
            <h3>Filtrar Productos - Tienda Online</h3>
            <label for="producto_id_modal_gpt">Productos con bajo stock</label>
            <select id="producto_id_modal_gpt" name="producto_gpt">
                <option value="">Todos los productos con bajo stock</option>
                @foreach($productos as $producto)
                    <option value="{{ $producto->id }}">
                        {{ $producto->nombreProducto }} ({{ $producto->cantidad }} {{ $producto->unidad }})
                    </option>
                @endforeach
            </select>
            <div class="modal-botones" style="margin-top:10px; display:flex; gap:8px; justify-content:flex-end;">
                <button class="btn-quitar-filtro" id="btnQuitarFiltroGpt" style="display: none;">Quitar Filtros</button>
                <button class="btn-aplicar-filtro" id="btnAplicarFiltroGpt">Aplicar Filtro</button>
                <button class="btn-cerrar-modal" id="btnCerrarModalGpt">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
    window.csrfToken = '{{ csrf_token() }}';
    window.addPedidoRoute = '{{ route("inventario.addPedido") }}';
</script>
<script src="{{ asset('js/components/home-user/cotizador.js') }}"></script>