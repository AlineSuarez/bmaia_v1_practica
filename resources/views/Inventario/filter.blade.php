<div class="modal-inventory">
    <div class="modal-header">
        <h3 class="modal-title">Filtrar Productos</h3>
        <button type="button" class="modal-close" onclick="closeFilterModal()">&times;</button>
    </div>
    
    <form class="form-inventory">
        <div class="form-group">
            <label class="form-label">Buscar Producto</label>
            <input type="search" class="form-input" placeholder="Ingrese nombre de Producto" name="q" id="searchInput">
        </div>

        <div class="form-group">
            <label class="form-label">Categoría</label>
            <select class="form-select" id="categorySelect" name="category_id">
                <option value="">Seleccione una categoría</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->nombreCategoria }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Subcategorías</label>
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
            <button type="button" id="btn-clear-filters" class="btn-filtro" style="display: none;">
                Quitar filtros
            </button>
        </div>
    </form>
</div>