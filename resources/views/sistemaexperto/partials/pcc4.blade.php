<div id="pcc4-form" class="form-section pcc-section">
    <div class="section-header">
        <div class="section-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15h2v-6h-2v6zm0-8h2V7h-2v2z"/>
            </svg>
        </div>
        <h2 class="section-title">PCC4 - Nivel de Infestación de Varroa</h2>
        <div class="section-decoration"></div>
    </div>
    <div class="field-group">
        <div class="form-field full-width">
            <label for="varroa_diagnostico_visual" class="field-label">Diagnóstico Visual</label>
            <textarea id="varroa_diagnostico_visual" name="varroa_diagnostico_visual" class="field-textarea" rows="3"
                placeholder="Describa el diagnóstico visual de Varroa.">{{ old('varroa_diagnostico_visual', $visita['diagnostico_visual'] ?? '') }}</textarea>
            <span class="field-helper">Observaciones visuales sobre la presencia de Varroa.</span>
        </div>

        <div class="form-field full-width">
            <label for="varroa_muestreo_abejas_adultas" class="field-label">Muestreo Abejas Adultas</label>
            <textarea id="varroa_muestreo_abejas_adultas" name="varroa_muestreo_abejas_adultas" class="field-textarea" rows="3"
                placeholder="Detalles del muestreo en abejas adultas (ej. cantidad de varroas por 100 abejas).">{{ old(
         'varroa_muestreo_abejas_adultas',
         $pcc4['muestreo_abejas_adultas'] ?? ''
    ) }}</textarea>
            <span class="field-helper">Resultados de muestreo en abejas adultas.</span>
        </div>

        <div class="form-field full-width">
            <label for="varroa_muestreo_cria_operculada" class="field-label">Muestreo Cría Operculada</label>
            <textarea id="varroa_muestreo_cria_operculada" name="varroa_muestreo_cria_operculada" class="field-textarea" rows="3"
                placeholder="Detalles del muestreo en cría operculada (ej. porcentaje de celdas infestadas).">{{ old(
         'varroa_muestreo_cria_operculada',
         $pcc4['muestreo_cria_operculada'] ?? ''
    ) }}</textarea>
            <span class="field-helper">Resultados de muestreo en cría operculada.</span>
        </div>

        <div class="form-field">
            <label for="varroa_metodo_diagnostico" class="field-label">Método Diagnóstico</label>
            <input type="text" id="varroa_metodo_diagnostico" name="varroa_metodo_diagnostico" class="field-input text-input"
                placeholder="Ej: Lavado con alcohol, Sugar roll, etc." value="{{ old('varroa_metodo_diagnostico', $visita['diagnostico'] ?? '') }}">
            <span class="field-helper">Método utilizado para el diagnóstico de Varroa.</span>
        </div>

        <div class="form-field">
            <label for="varroa_fecha_monitoreo_varroa" class="field-label">Fecha Monitoreo Varroa</label>
            <input type="date" id="varroa_fecha_monitoreo_varroa" name="varroa_fecha_monitoreo_varroa" class="field-input date-input"
                value="{{ old(
    'varroa_fecha_monitoreo_varroa',
    optional(\Carbon\Carbon::parse($pcc4['fecha_monitoreo_varroa'] ?? null))->format('Y-m-d')
  ) }}">
            <span class="field-helper">Fecha en que se realizó el monitoreo.</span>
        </div>

        <div class="form-field">
            <label for="varroa_tratamiento" class="field-label">Tratamiento</label>
            <input type="text" id="varroa_tratamiento" name="varroa_tratamiento" class="field-input text-input"
                placeholder="Ej: Ácido oxálico, Timol, etc." value="{{ old(
         'varroa_tratamiento',
         $pcc4['tratamiento'] ?? ''
      ) }}">
            <span class="field-helper">Tratamiento aplicado para Varroa.</span>
        </div>

        <div class="form-field">
            <label for="varroa_fecha_aplicacion" class="field-label">Fecha Aplicación Tratamiento</label>
            <input type="date" id="varroa_fecha_aplicacion" name="varroa_fecha_aplicacion" class="field-input date-input"
                value="{{ old(
    'varroa_fecha_aplicacion',
    optional(\Carbon\Carbon::parse($pcc4['fecha_aplicacion'] ?? null))->format('Y-m-d')
  ) }}">
            <span class="field-helper">Fecha de aplicación del tratamiento.</span>
        </div>

        <div class="form-field">
            <label for="varroa_dosificacion" class="field-label">Dosificación</label>
            <input type="text" id="varroa_dosificacion" name="varroa_dosificacion" class="field-input text-input"
                placeholder="Ej: 35g por colmena" value="{{ old(
         'varroa_dosificacion',
         $pcc4['dosificacion'] ?? ''
      ) }}">
            <span class="field-helper">Cantidad o dosis del tratamiento.</span>
        </div>

        <div class="form-field">
            <label for="varroa_metodo_aplicacion" class="field-label">Método de Aplicación</label>
            <input type="text" id="varroa_metodo_aplicacion" name="varroa_metodo_aplicacion" class="field-input text-input"
                placeholder="Ej: Tiras, sublimación, etc." value="{{ old(
         'varroa_metodo_aplicacion',
         $pcc4['metodo_aplicacion'] ?? ''
      ) }}">
            <span class="field-helper">Forma en que se aplicó el tratamiento.</span>
        </div>
        
        <div class="form-field">
            <label for="varroa_producto_comercial" class="field-label">Producto Comercial</label>
            <input type="text" id="varroa_producto_comercial" name="varroa_producto_comercial" class="field-input text-input"
                placeholder="Ej: ApiGuard, Apivar" value="{{ old(
         'varroa_producto_comercial',
         $pcc4['producto_comercial'] ?? ''
      ) }}">
            <span class="field-helper">Nombre comercial del producto.</span>
        </div>

        <div class="form-field">
            <label for="varroa_ingrediente_activo" class="field-label">Ingrediente Activo</label>
            <input type="text" id="varroa_ingrediente_activo" name="varroa_ingrediente_activo" class="field-input text-input"
                placeholder="Ej: Amitraz, Flumetrina" value="{{ old(
         'varroa_ingrediente_activo',
         $pcc4['ingrediente_activo'] ?? ''
      ) }}">
            <span class="field-helper">Principio activo del producto.</span>
        </div>

        <div class="form-field">
            <label for="varroa_periodo_carencia" class="field-label">Período de Carencia</label>
            <input type="text" id="varroa_periodo_carencia" name="varroa_periodo_carencia" class="field-input text-input"
                placeholder="Ej: 0, 14" value="{{ old(
         'varroa_periodo_carencia',
         $pcc4['periodo_carencia'] ?? ''
      ) }}">
            <span class="field-helper">Ingrese numero de días que debe transcurrir entre la aplicación y la cosecha.</span>
        </div>
    </div>
</div>