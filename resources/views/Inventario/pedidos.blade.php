
<div class="inventory-container">
    <div class="inventory-header">
        <h1 class="inventory-title">Mis Pedidos</h1>
        <div class="header-content">
            <div class="inventory-stats">
                <span id="contador-pedidos">{{ $pedidos->count() }} Pedidos Pendientes a Realizar</span>
            </div>
            
            <!-- BOTÓN PARA SUBIR FACTURA -->
            <div style="position:absolute; display: flex; justify-content: flex-end; width: 100%; right:400px; top:20px;">
                <button class="btn-inventory" onclick="subirFactura()">
                    <i class="fa-solid fa-file-pdf"></i>
                    <span>Subir Factura (PDF)</span>
                </button>
            </div>
            
            <div id="modal-imprimir-historial" style="position:absolute; display: flex; justify-content: flex-end; width: 100%; left:-23px;">
                <button class="btn-inventory" onclick="showHistorialPreview()">
                    <i class="fa-solid fa-print"></i>
                    <span>Historial de Compras</span>
                </button>
            </div>
            
            <div style="position: absolute; right: 20px; top: 20px; display:flex; gap:20px;">
                <button id="btn-pedidos-activos" class="btn-inventory">Pedidos Activos</button>
                <button id="btn-historial-compras" class="btn-inventory">Historial de Compras</button>
            </div>
        </div>
    </div>

    <div id="modalHistorialPreview" class="modal-overlay">
        <div class="modal-box-historial">
            <div class="modal-historial-header">
                <h5>Historial de Compras</h5>
                <button class="modal-historial-close" onclick="closeModal('modalHistorialPreview')">&times;</button>
            </div>
            <div class="modal-historial-body">
                <iframe id="iframeHistorialPreview" src=""></iframe>
            </div>
        </div>
    </div>

    <!-- MODAL PARA MOSTRAR RESULTADOS DE FACTURA -->
    <div id="modalResultadosFactura" class="modal-overlay">
        <div class="modal-box" style="max-width: 600px;">
            <div class="modal-historial-header">
                <h5>Resultados de la Factura</h5>
                <button class="modal-historial-close" onclick="closeModal('modalResultadosFactura')">&times;</button>
            </div>
            <div id="resultadosFacturaContainer"></div>
        </div>
    </div>

    <div id="contenedor-pedidos-activos">
        @if($pedidos->isEmpty())
            <div class="empty-state">
                <p>No tienes pedidos activos.</p>
            </div>
        @else
            <div class="cards-container" id="cards-container">
                @foreach($pedidos as $pedido)
                    <div class="product-card" data-pedido-id="{{ $pedido->id }}">
                        <img style="width: 120px; height:120px;" src="{{ $pedido->imagen ?? '/img/no-img.png' }}" alt="{{ $pedido->nameProduct }}">
                        
                        <div class="product-info">
                            <div class="nombre">{{ $pedido->nameProduct }}</div>
                            <div class="proveedor">
                                🏪 <strong>Proveedor: {{ $pedido->tienda ?? 'No especificado' }}</strong>
                            </div>
                            <div class="product-actions">
                                <a href="{{ $pedido->urlProduct }}" target="_blank">🔗 Ver Producto en Página</a>
                            </div>      
                        </div>
                        
                        <div class="precio">${{ number_format($pedido->priceProduct, 0, '.', '.') }}</div>
                        
                        <div class="product-actions">
                            <button type="button" class="btn-confirmar" onclick="openModal('modalUpdate{{ $pedido->id }}')">
                                Agregar Producto Inventario
                            </button>

                            <button type="button" class="btn-eliminar" onclick="eliminarPedidoAjax({{ $pedido->id }})">
                                Eliminar Pedido
                            </button>
                        </div>
                    </div>

                    <div id="modalUpdate{{ $pedido->id }}" class="modal-overlay">
                        <div class="modal-box">
                            <h3>Confirmar ingreso al Inventario de:</h3>
                            <p><strong>{{ $pedido->nameProduct }}</strong></p>

                            @if(isset($sugerencias[$pedido->id]) && $sugerencias[$pedido->id])
                                <p> Sugerencia de producto en inventario encontrada: 
                                <strong>{{ $sugerencias[$pedido->id]->nombreProducto }}</strong>
                                </p>
                            @else
                                <p> No se encontró sugerencia automática.</p>
                            @endif

                            <form method="POST" onsubmit="archivarPedidoAjax(event, {{ $pedido->id }})">
                                @csrf

                                <label>Seleccionar producto existente</label>
                                <select name="producto_id" required>
                                    @foreach($productos as $prod)
                                        <option value="{{ $prod->id }}"
                                            @if(isset($sugerencias[$pedido->id]) && $sugerencias[$pedido->id] && $sugerencias[$pedido->id]->id === $prod->id) selected @endif>
                                            {{ $prod->nombreProducto }} ({{ $prod->cantidad }} {{ $prod->unidad }})
                                        </option>
                                    @endforeach
                                </select>

                                <input type="hidden" name="precio" value="{{ $pedido->priceProduct }}">

                                <label>Cantidad comprada</label>
                                <input type="number" name="cantidad" step="any" min="1" required>

                                <button class="btn-inventory" type="submit">Confirmar</button>
                                <button class="btn-secondary" type="button"
                                    onclick="closeModal('modalUpdate{{ $pedido->id }}')">Cancelar</button>

                                <hr>

                                <button type="button" class="btn-inventory"
                                    onclick="closeModal('modalUpdate{{ $pedido->id }}'); openModal('modalNew{{ $pedido->id }}');">
                                    Crear NUEVO Producto
                                </button>
                            </form>
                        </div>
                    </div>

                    <div id="modalNew{{ $pedido->id }}" class="modal-overlay">
                        <div class="modal-box">
                            <h3>Nuevo Producto en Inventario</h3>

                            <form method="POST" onsubmit="crearProductoAjax(event, {{ $pedido->id }})">
                                @csrf

                                <label>Nombre del producto</label>
                                <input type="text" name="nombreProducto"
                                    value="{{ $pedido->nameProduct }}" required>

                                <label>Precio Compra</label>
                                <input 
                                    type="text" 
                                    class="price-display" 
                                    value="{{ number_format($pedido->priceProduct, 0, '.', '.') }}"
                                    oninput="formatPrice(this)"
                                >

                                <input 
                                    type="hidden" 
                                    name="precio" 
                                    class="price-real"
                                    value="{{ $pedido->priceProduct }}"
                                >

                                <label>Categoría</label>
                                <select name="category_id" required>
                                    @foreach($categorias as $c)
                                        <option value="{{ $c->id }}">{{ $c->nombreCategoria }}</option>
                                    @endforeach
                                </select>

                                <label>Subcategorías</label>
                                <div class="checkbox-subcategory-container">
                                    @foreach($subcategorias as $sub)
                                        <label class="checkbox-subcategory-label">
                                            <input type="checkbox" name="subcategory_id[]" value="{{ $sub->id }}">
                                            <span>{{ $sub->nombreSubcategoria }}</span>
                                        </label>
                                    @endforeach
                                </div>

                                <label>Cantidad comprada</label>
                                <input type="number" name="cantidad" step="any" min="1" required>

                                <button class="btn-inventory" type="submit">Crear Producto</button>
                                <button class="btn-secondary" type="button"
                                    onclick="closeModal('modalNew{{ $pedido->id }}')">Cancelar</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="pagination-container" id="pagination-pedidos">
                @if ($pedidos->lastPage() > 1)
                    <ul class="pagination">
                        @if ($pedidos->onFirstPage())
                            <li class="disabled"><span>«</span></li>
                        @else
                            <li><a href="#" onclick="cargarPagina(event, {{ $pedidos->currentPage() - 1 }}, 'pedidos')">«</a></li>
                        @endif

                        @for ($i = 1; $i <= $pedidos->lastPage(); $i++)
                            <li class="{{ $pedidos->currentPage() == $i ? 'active' : '' }}">
                                <a href="#" onclick="cargarPagina(event, {{ $i }}, 'pedidos')">{{ $i }}</a>
                            </li>
                        @endfor

                        @if ($pedidos->hasMorePages())
                            <li><a href="#" onclick="cargarPagina(event, {{ $pedidos->currentPage() + 1 }}, 'pedidos')">»</a></li>
                        @else
                            <li class="disabled"><span>»</span></li>
                        @endif
                    </ul>
                @endif
            </div>
        @endif
    </div>

    <div id="contenedor-historial-compras" class="hidden">
        @if($archivados->isEmpty())
            <div class="empty-state">
                <p>No tienes compras registradas en el historial.</p>
            </div>
        @else
            <div class="inventory-table-container" id="tabla-historial">
                <table class="inventory-table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Proveedor</th>
                            <th>Fecha de Compra</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($archivados as $archivado)
                            <tr>
                                <td>{{ $archivado->nameProduct }}</td>
                                <td>${{ number_format($archivado->priceProduct, 0, '.', '.') }}</td>
                                <td>
                                    <a href="{{ $archivado->urlProduct }}" target="_blank">
                                        {{ $archivado->tienda ?? 'No especificado' }}
                                    </a>    
                                </td>
                                <td>{{ $archivado->fecha_archivado }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="pagination-container" id="pagination-historial">
                @if ($archivados->lastPage() > 1)
                    <ul class="pagination">
                        @if ($archivados->onFirstPage())
                            <li class="disabled"><span>«</span></li>
                        @else
                            <li><a href="#" onclick="cargarPagina(event, {{ $archivados->currentPage() - 1 }}, 'historial')">«</a></li>
                        @endif

                        @for ($i = 1; $i <= $archivados->lastPage(); $i++)
                            <li class="{{ $archivados->currentPage() == $i ? 'active' : '' }}">
                                <a href="#" onclick="cargarPagina(event, {{ $i }}, 'historial')">{{ $i }}</a>
                            </li>
                        @endfor

                        @if ($archivados->hasMorePages())
                            <li><a href="#" onclick="cargarPagina(event, {{ $archivados->currentPage() + 1 }}, 'historial')">»</a></li>
                        @else
                            <li class="disabled"><span>»</span></li>
                        @endif
                    </ul>
                @endif
            </div>
        @endif
    </div>

    <div id="historial-preview-route" data-historial-preview="{{ route('inventario.previewHistorial') }}"></div>
</div>

<script>
    window.pedidosTotal = {{ $pedidos->total() }};
    window.archivadosTotal = {{ $archivados->total() }};
    window.buscarProductoRoute = '{{ route("inventario.buscarProducto") }}';
    window.inventarioStoreRoute = '{{ route("inventario.store") }}';
    window.inventarioRoute = '{{ route("inventario") }}';
</script>
