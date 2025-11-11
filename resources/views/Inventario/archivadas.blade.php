@extends('layouts.app')

@section('title', 'B-MaiA - Plan de Trabajo Anual')

@section('content')
<head>
        <link rel="stylesheet" href="{{ asset('css/components/home-user/inventario/inventario.css') }}">
</head>
<div class="inventory-container">
    <div style="margin-bottom: 25px;">
        <button class="btn-inventory">
            <a href='{{ route("inventario") }}' class="text-white">
                Volver al Inventario
            </a>
        </button>
    </div>

    <div class="inventory-header">
        <h1 class="inventory-title">Lista de Archivadas</h1>
        <div class="header-content">
            <div class="inventory-stats">
                <span>{{ $productos->total() }} Archivadas</span>
            </div>
            <div class="buttons-edit-container">
            </div>
        </div>
    </div>

    <div class="inventory-table-container" id="inventario-listado">

        @if($productos->isEmpty())
            <div class="empty-state">
                <p>No hay productos archivados.</p>
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
                                    {{-- Nombre Producto y select para archivar o recuperar --}}
                                    <td style="padding-left: 10px;">
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <!-- Checkbox de selección -->
                                            <input type="checkbox" 
                                                class="checkbox-seleccionar-producto" 
                                                data-product-id="{{ $producto->id }}"
                                                style="cursor: pointer; width: 16px; height: 16px; flex-shrink: 0;">
                                            
                                            <div style="flex: 1;">
                                                <span class="view-mode">{{ $producto->nombreProducto }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    {{-- Cantidad --}}
                                    <td>
                                        <span class="view-mode">{{ $producto->cantidad }}</span>
                                    </td>
                                    {{-- Unidad --}}
                                    <td>
                                        <span class="view-mode">{{ $producto->unidad }}</span>
                                    </td>
                                    {{-- Categorias --}}
                                    <td>
                                        <span class="view-mode">{{ optional($producto->category)->nombreCategoria ?? 'Sin categoría' }}</span>
                                    </td>
                                    {{-- Subcategorias --}}
                                    <td>
                                        <span class="view-mode">
                                            @foreach($producto->subcategories as $subcategory)
                                                {{ $subcategory->nombreSubcategoria }}@if(!$loop->last), @endif
                                            @endforeach
                                        </span>
                                    </td>
                                    {{-- Precio --}}
                                    <td>
                                        <span class="view-mode">${{ $producto->precio }}</span>
                                    </td>
                                    {{-- Observacion --}}
                                    <td>
                                        {{-- Modal para visualizar Observacion --}}
                                        <span class="view-mode">
                                            <button class="btn-observacion truncate-text" id="btn-openModalEditObservacion-{{ $producto->id }}">
                                                {{ Str::limit($producto->observacion, 30, '...') }}
                                            </button>

                                            <div id="modalEditObservacion-{{ $producto->id }}" class="modal-observacion" style="display:none;">
                                                <div class="modal-header-obs">
                                                    <span class="modal-title-obs">Observación</span>
                                                    <button type="button" class="btn-close-modal" onclick="document.getElementById('modalEditObservacion-{{ $producto->id }}').style.display='none'">
                                                        <i class="fa fa-times"></i>
                                                    </button>
                                                </div>
                                                    <textarea name="observacion" class="observacion-input">{{ $producto->observacion }}</textarea>
                                                    <div class="modal-actions">
                                                        <button type="button" class="btn-cancel-observacion" id="CancelEditarObservacion-{{ $producto->id }}">Cerrar</button>
                                                    </div>
                                            </div>
                                        </span>
                                    </td>
                                    {{-- Actions --}}
                                    <td id="btn-archivar" class="actions-cell" >
                                        <div class="action-buttons-centered">
                                            <input type="hidden" class="product-id" value="{{ $producto->id }}">
                                            <form action="{{ route('inventario.restaurar', $producto->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" title="Restaurar Producto" class="btn-action btn-restaurar">
                                                    <i class="fa fa-rotate-left"></i>
                                                </button>
                                            </form>
                                        </div>
                                        <div class="action-buttons-centered">
                                            <input type="hidden" class="product-id" value="{{ $producto->id }}">
                                            <form action="{{ route('inventario.destroy', $producto->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este producto permanentemente?');" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-action btn-delete" title="Eliminar">
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
</div>
<form id="formAccionMultiple" method="POST" style="display:none;">
    @csrf
    <input type="hidden" name="ids" id="input-ids">
</form>

<script src="{{ asset('js/components/home-user/archivadas.js') }}"></script>
@endsection