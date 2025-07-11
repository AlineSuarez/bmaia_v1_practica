
<div id="pcc5-form" class="form-section pcc-section">
    <div class="section-header">
        <div class="section-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15h2v-6h-2v6zm0-8h2V7h-2v2z"/>
            </svg>
        </div>
        <h2 class="section-title">PCC5 - Presencia de Nosemosis</h2>
        <div class="section-decoration"></div>
    </div>
    <div class="field-group">
        <div class="form-field full-width">
            <label for="nosemosis_signos_clinicos" class="field-label">Signos Clínicos (Diagnóstico Visual)</label>
            <textarea id="nosemosis_signos_clinicos" name="nosemosis_signos_clinicos" class="field-textarea" rows="3"
                placeholder="Describa los signos clínicos observados de Nosemosis.">{{ old(
         'nosemosis_signos_clinicos',
         $pcc5['signos_clinicos'] ?? ''
    ) }}</textarea>
            <span class="field-helper">Observaciones visuales sobre la presencia de Nosemosis.</span>
        </div>

        <div class="form-field full-width">
            <label for="nosemosis_muestreo_laboratorio" class="field-label">Muestreo Laboratorio</label>
            <textarea id="nosemosis_muestreo_laboratorio" name="nosemosis_muestreo_laboratorio" class="field-textarea" rows="3"
                placeholder="Resultados del muestreo de laboratorio (ej. recuento de esporas).">{{ old(
         'nosemosis_muestreo_laboratorio',
         $pcc5['muestreo_laboratorio'] ?? ''
    ) }}</textarea>
            <span class="field-helper">Información sobre el análisis de laboratorio.</span>
        </div>

        <div class="form-field">
            <label for="nosemosis_metodo_diagnostico_laboratorio" class="field-label">Método Diagnóstico Laboratorio</label>
            <input type="text" id="nosemosis_metodo_diagnostico_laboratorio" name="nosemosis_metodo_diagnostico_laboratorio" class="field-input text-input"
                placeholder="Ej: Microscopía, PCR" value="{{ old(
         'nosemosis_metodo_diagnostico_laboratorio',
         $pcc5['metodo_diagnostico_laboratorio'] ?? ''
      ) }}">
            <span class="field-helper">Método de diagnóstico utilizado en laboratorio.</span>
        </div>

        <div class="form-field">
            <label for="nosemosis_fecha_monitoreo_nosema" class="field-label">Fecha Monitoreo Nosema</label>
            <input type="date" id="nosemosis_fecha_monitoreo_nosema" name="nosemosis_fecha_monitoreo_nosema" class="field-input date-input"
                value="{{ old('nosemosis_fecha_monitoreo_nosema') }}">
            <span class="field-helper">Fecha en que se realizó el monitoreo.</span>
        </div>

        <div class="form-field">
            <label for="nosemosis_tratamiento" class="field-label">Tratamiento</label>
            <input type="text" id="nosemosis_tratamiento" name="nosemosis_tratamiento" class="field-input text-input"
                placeholder="Ej: Fumagilina, Tratamiento natural" value="{{ old(
         'nosemosis_tratamiento',
         $pcc5['tratamiento'] ?? ''
      ) }}">
            <span class="field-helper">Tratamiento aplicado para Nosemosis.</span>
        </div>

        <div class="form-field">
            <label for="nosemosis_fecha_aplicacion" class="field-label">Fecha Aplicación Tratamiento</label>
            <input type="date" id="nosemosis_fecha_aplicacion" name="nosemosis_fecha_aplicacion" class="field-input date-input"
                value="{{ old('nosemosis_fecha_aplicacion') }}">
            <span class="field-helper">Fecha de aplicación del tratamiento.</span>
        </div>

        <div class="form-field">
            <label for="nosemosis_dosificacion" class="field-label">Dosificación</label>
            <input type="text" id="nosemosis_dosificacion" name="nosemosis_dosificacion" class="field-input text-input"
                placeholder="Ej: 200mg por litro, 10ml por colmena" value="{{ old(
         'nosemosis_dosificacion',
         $pcc5['dosificacion'] ?? ''
      ) }}">
            <span class="field-helper">Cantidad o dosis del tratamiento.</span>
        </div>

        <div class="form-field">
            <label for="nosemosis_metodo_aplicacion" class="field-label">Método de Aplicación</label>
            <input type="text" id="nosemosis_metodo_aplicacion" name="nosemosis_metodo_aplicacion" class="field-input text-input"
                placeholder="Ej: Jarabe, Drench" value="{{ old(
         'nosemosis_metodo_aplicacion',
         $pcc5['metodo_aplicacion'] ?? ''
      ) }}">
            <span class="field-helper">Forma en que se aplicó el tratamiento.</span>
        </div>

        <div class="form-field">
            <label for="nosemosis_producto_comercial" class="field-label">Producto Comercial</label>
            <input type="text" id="nosemosis_producto_comercial" name="nosemosis_producto_comercial" class="field-input text-input"
                placeholder="Ej: Nosevit, Apiherb" value="{{ old(
         'nosemosis_producto_comercial',
         $pcc5['producto_comercial'] ?? ''
      ) }}">
            <span class="field-helper">Nombre comercial del producto.</span>
        </div>

        <div class="form-field">
            <label for="nosemosis_ingrediente_activo" class="field-label">Ingrediente Activo</label>
            <input type="text" id="nosemosis_ingrediente_activo" name="nosemosis_ingrediente_activo" class="field-input text-input"
                placeholder="Ej: Fumagilina-B" value="{{ old(
         'nosemosis_ingrediente_activo',
         $pcc5['ingrediente_activo'] ?? ''
      ) }}">
            <span class="field-helper">Principio activo del producto.</span>
        </div>
    </div>
</div>