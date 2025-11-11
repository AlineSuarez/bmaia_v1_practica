<div class="inventory-container">
    <div class="inventory-header">
        <h1 class="inventory-title">Lista de Inventario</h1>
        <div class="header-content">
            <div class="inventory-stats">
                <span>{{ $productos->total() }} Productos</span>
            </div>
            <div id="modal-imprimir">
                <!-- para hacer un espaciado debajo del total de productos -->
                <br>
                <br>
            </div>
            <div id="modal-imprimir" style="position:absolute;
                        display: flex;
                        justify-content: flex-end;
                        width: 100%;
                        margin-top: 40px;
                        left:-23px;">
                <button class="btn-inventory" onclick="showInventarioPreview()">
                    <i class="fa-solid fa-print"></i>
                    <span>Inventario</span>
                </button>
            </div>
            <div class="buttons-edit-container">
                <button id="btn-guardar-cambios" class="btn-inventory">
                    <i class="fa-solid fa-save"></i> Guardar cambios
                </button>
                <div class="edit-cancel-wrapper">
                    <button id="btn-editar" class="btn-inventory">
                        Editar
                    </button>
                    <button id="btn-cancelar" class="btn-inventory" style="display:none;">
                        Cancelar Edicion
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Preliminar de Inventario -->
    <div class="modal fade" id="modalInventarioPreview" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 900px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reporte de Inventario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <iframe id="iframeInventarioPreview"
                        src=""
                        width="100%" height="500px" style="border:none;">
                    </iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabla de productos --}}
    <div class="inventory-table-container">
        @if($productos->isEmpty())
            <div class="empty-state">
                <p>No se encontraron productos que coincidan con los filtros aplicados.</p>
            </div>
        @else
            <div >
                <table class="inventory-table">
                    <thead>
                        <tr>
                            <th>Nombre Producto</th>
                            <th>Cantidad</th>
                            <th>Unidad</th>
                            <th>Categoría</th>
                            <th>Subcategoría</th>
                            <th>Precio</th>
                            <th>Observación</th>
                            <th id="actions" style="text-align: center;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($productos as $producto)
                            <tr>
                                {{-- Nombre Producto y seleccion para archivar --}}
                                <td style="padding-left: 10px;">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <!-- Checkbox de selección -->
                                        <input type="checkbox" 
                                            class="checkbox-seleccionar-producto" 
                                            data-product-id="{{ $producto->id }}"
                                            style="cursor: pointer; width: 16px; height: 16px; flex-shrink: 0;">
                                        
                                        <div style="flex: 1;">
                                            <span class="view-mode">{{ $producto->nombreProducto }}</span>
                                            <input type="text" name="nombreProducto" class="edit-mode input-inventory" 
                                                placeholder="{{ $producto->nombreProducto }}" 
                                                value="{{ $producto->nombreProducto }}" hidden>
                                        </div>
                                    </div>
                                </td>
                                {{-- Cantidad --}}
                                <td>
                                    <span class="view-mode">
                                        {{-- Formateo de cantidad para mostrar solo decimales a Kg, Ml, L, Gr --}}
                                        @php
                                            // Unidades que deben mostrarse SIN decimales
                                            $sinDecimales = ['Unidad', 'Botellas', 'Caja', 'Tiras', 'Dosis'];
                                        @endphp

                                        @if(in_array($producto->unidad, $sinDecimales))
                                            {{ number_format($producto->cantidad, 0, ',', '.') }}
                                        @else
                                            {{ number_format($producto->cantidad, 2, ',', '.') }}
                                        @endif
                                    </span>

                                    <input type="number" 
                                        name="cantidad" 
                                        class="edit-mode input-number-inventory" 
                                        value="{{ $producto->cantidad }}" 
                                        min="0"
                                        hidden>
                                </td>
                                {{-- Unidad --}}
                                <td>
                                    <span class="view-mode">{{ $producto->unidad }}</span>
                                    <select class="edit-mode input-inventory" name="unidad" hidden>
                                        @foreach (['Gr', 'Kg', 'Ml', 'L', 'Unidad', 'Botellas', 'Caja', 'Tiras', 'Dosis'] as $unidad)
                                            <option value="{{ $unidad }}" {{ $producto->unidad == $unidad ? 'selected' : '' }}>
                                                {{ $unidad }}
                                            </option>
                                        @endforeach
                                        <option value="otros">Otros</option>
                                    </select>
                                </td>
                                {{-- Categorias --}}
                                <td>
                                    <span class="view-mode">{{ optional($producto->category)->nombreCategoria ?? 'Sin categoría' }}</span>
                                    <select class="edit-mode input-inventory" name="category_id" hidden>
                                        @foreach ($categories as $cat)
                                            <option value="{{ $cat->id }}" {{ $producto->category_id == $cat->id ? 'selected' : '' }}>
                                                {{ $cat->nombreCategoria }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                {{-- Subcategorias --}}
                                <td>
                                    <span class="view-mode">
                                        @foreach($producto->subcategories as $subcategory)
                                            {{ $subcategory->nombreSubcategoria }}@if(!$loop->last), @endif
                                        @endforeach
                                    </span>

                                    <div class="edit-mode checkbox-group" hidden>
                                        @foreach ($subcategories as $sub)
                                            <label style="display:block; font-size:13px;">
                                                <input type="checkbox" name="subcategories[]" value="{{ $sub->id }}"
                                                    {{ $producto->subcategories->contains('id', $sub->id) ? 'checked' : '' }}>
                                                {{ $sub->nombreSubcategoria }}
                                            </label>
                                        @endforeach
                                    </div>
                                </td>
                                {{-- Precio --}}
                                <td>
                                    {{-- Mostrar Precio en formato "255.000" SOLO EN LISTADO --}}
                                    <span class="view-mode">${{ number_format($producto->precio, 0, '.', '.') }}</span>
                                    <input type="number" name="precio" class="edit-mode input-number-inventory" 
                                        value="{{ $producto->precio }}" hidden>
                                </td>
                                {{-- Observacion --}}
                                <td>
                                    {{-- Modal para Editar Observacion --}}
                                    <span class="view-mode">
                                        <button class="btn-observacion truncate-text" id="btn-openModalEditObservacion-{{ $producto->id }}">
                                            {{ Str::limit($producto->observacion, 30, '...') }}
                                        </button>

                                        <div id="modalEditObservacion-{{ $producto->id }}" class="modal-observacion" style="display:none;">
                                            <div class="modal-header-obs">
                                                <span class="modal-title-obs">Editar Observación</span>
                                                <button type="button" class="btn-close-modal" onclick="document.getElementById('modalEditObservacion-{{ $producto->id }}').style.display='none'">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </div>
                                            <form method="POST" action="{{ route('inventario.update_observacion', $producto->id) }}">
                                                @csrf
                                                @method('PATCH')
                                                <textarea name="observacion" class="observacion-input" placeholder="Ingrese una observación para el producto...">{{ $producto->observacion }}</textarea>
                                                <div class="modal-actions">
                                                    <button type="button" class="btn-cancel-observacion" id="CancelEditarObservacion-{{ $producto->id }}">Cancelar</button>
                                                    <button type="submit" class="btn-ok-observacion">Guardar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </span>

                                    {{-- Modal para Editar Observacion en Editar Activo --}}
                                    <div class="edit-mode" hidden>
                                        <button class="btn-observacion truncate-text" id="btn-openModalEditObservacionInline-{{ $producto->id }}">
                                            {{ $producto->observacion ? Str::limit($producto->observacion, 15, '...') : '' }}
                                        </button>

                                        <div id="modalEditObservacionInline-{{ $producto->id }}" class="modal-observacion" style="display:none;">
                                            <div class="modal-header-obs">
                                                <span class="modal-title-obs">Editar Observación</span>
                                                <button type="button" class="btn-close-modal">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </div>
                                            <div>
                                                <textarea name="observacion" class="observacion-input" 
                                                        data-product-id="{{ $producto->id }}"
                                                        placeholder="Ingrese una observación para el producto...">{{ $producto->observacion }}</textarea>
                                                <div class="modal-actions">
                                                    <button type="button" class="btn-cancel-observacion">Cancelar</button>
                                                    <button type="button" class="btn-ok-observacion">Guardar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                {{-- Actions --}}
                                <td id="btn-archivar" class="actions-cell" >
                                    <div class="action-buttons-centered">
                                        <input type="hidden" class="product-id" value="{{ $producto->id }}">
                                        <form action="{{ route('inventario.archivar', $producto->id) }}" method="POST" onsubmit="return confirm('¿Descartar producto?');" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn-action btn-delete" title="Descartar">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Paginacion --}}
            <div class="pagination-container">
                @if ($productos->lastPage() > 1)
                    <ul class="pagination">
                        {{-- Boton anterior --}}
                        @if ($productos->onFirstPage())
                            <li class="disabled"><span>«</span></li>
                        @else
                            <li><a href="{{ $productos->previousPageUrl() }}">«</a></li>
                        @endif

                        {{-- Numeros de pagina --}}
                        @for ($i = 1; $i <= $productos->lastPage(); $i++)
                            <li class="{{ $productos->currentPage() == $i ? 'active' : '' }}">
                                <a href="{{ $productos->url($i) }}">{{ $i }}</a>
                            </li>
                        @endfor

                        {{-- Boton siguiente --}}
                        @if ($productos->hasMorePages())
                            <li><a href="{{ $productos->nextPageUrl() }}">»</a></li>
                        @else
                            <li class="disabled"><span>»</span></li>
                        @endif
                    </ul>
                @endif
            </div>
        @endif
    </div>
    
    {{-- Ruta para preview y actualización --}}
    <div id="inventario-preview-route"
        data-inventario-preview="{{ route('inventario.previewTodo') }}"
    >
    </div>
    
    <div id="update-route"
        data-update-multiple="{{ route('inventario.updateMultiple') }}"
    >
    </div>
</div>