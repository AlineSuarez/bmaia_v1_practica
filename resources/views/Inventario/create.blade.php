    
    <div class="modal-inventory">
        <div class="modal-header">
            <h3 class="modal-title">Crear Nuevo Producto</h3>
            <button type="button" class="modal-close" id="cancelProductCreation">&times;</button>
        </div>
        <form method="POST" action="{{ route('inventario.store') }}" class="form-inventory">
            @csrf
            <div class="form-group">
                <label for="" class="form-label">Ingrese el nombre del Producto</label>
                <div>
                    <input type="text" class="form-input" name="nombreProducto" id="create_nombre_producto">
                </div>
            </div>
            <div class="form-group">
                <label for="" class="form-label">Ingrese la Cantidad del Producto</label>
                <div>
                    <input type="text" class="form-input" name="cantidad" id="create_cantidad_producto">
                </div>
            </div>
            <div class="form-group">
                <label for="" class="form-label">Ingrese el Precio del Producto</label>
                <div>
                    <input type="text" class="form-input" name="precio" id="create_precio_producto">
                </div>
            </div>
            <div class="form-group">
                <label for="" class="form-label">Seleccione la Categoria</label>
                <div>
                    <select class="form-select" name="category_id" id="create_category_id">
                        <option value="">Seleccione una Categoria</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->nombreCategoria }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="" class="form-label">Seleccione Subcategoria del Producto</label>
                <div class="checkbox-group">
                    @foreach($subcategories as $subcategory)
                        <label class="checkbox-label">
                            <input type="checkbox" name="subcategory_id[]" value="{{ $subcategory->id }}">
                            {{ $subcategory->nombreSubcategoria }}
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" id="submitProductCreation" class="btn-inventory">Crear Producto</button>
            </div>
        </form>
    </div>