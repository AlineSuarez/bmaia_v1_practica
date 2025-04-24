@extends('layouts.app')

@section('content')
<div class="container">
    <div id="wizard">
        <!-- Steps Navigation --> 
        <div class="row">
        <div class="col-md-3">
            <ul class="nav nav-pills flex-column mb-4">
            <li class="nav-item">
                <h2>Formulario de registro</h2>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="#step1" data-bs-toggle="tab"> PCC1</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#step2" data-bs-toggle="tab"> PCC2</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#step3" data-bs-toggle="tab"> PCC3</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#step4" data-bs-toggle="tab"> PCC4</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#step5" data-bs-toggle="tab"> PCC5</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#step6" data-bs-toggle="tab"> PCC6</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#step7" data-bs-toggle="tab"> PCC7</a>
                </li>
            </ul>
        </div>
        <div class="col-md-9">
        <!-- Steps Content -->
            <form action="{{ route('sistemaexperto.store')}}" method="POST">
                @csrf
                <input type="hidden" name="apiario_id" value="{{ $apiarios->first()->id ?? null }}">
                <div class="tab-content">
                    <!-- PCC1 -->
                    <div class="tab-pane fade show active" id="step1">
                        <h4>PCC1 - Desarrollo Cámara de Cría</h4>
                        <div class="mb-3">
                            <label class="form-label"><b>Vigor de la colmena</b></label>
                            <input type="text" class="form-control" name="desarrollo_cria[vigor_colmena]" placeholder="Débil, Regular o Vigorosa">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><b>Actividad de las abejas</b></label>
                            <input type="text" class="form-control" name="desarrollo_cria[actividad_abejas]" placeholder="Bajo, Medio o Alto">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><b>Ingreso de polen</b></label>
                            <input type="text" class="form-control" name="desarrollo_cria[ingreso_polen]" placeholder="Sí o No">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><b>Bloqueo de cámara de cría</b></label>
                            <input type="text" class="form-control" name="desarrollo_cria[bloqueo_camara_cria]" placeholder="Sí o No">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><b>Presencia de celdas reales</b></label>
                            <input type="text" class="form-control" name="desarrollo_cria[presencia_celdas_reales]" placeholder="Sí o No">
                        </div>
                        <button type="button" class="btn btn-primary next-step" data-bs-target="#step2">Siguiente</button>
                    </div>

                    <!-- PCC2 -->
                    <div class="tab-pane fade" id="step2">
                        <h4>PCC2 - Calidad de la Reina</h4>
                        <div class="mb-3">
                            <label class="form-label">Postura de la reina</label>
                            <input type="text" class="form-control" name="calidad_reina[postura_reina]" placeholder="Irregular, Regular, Compacta">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Estado de la cría</label>
                            <input type="text" class="form-control" name="calidad_reina[estado_cria]" placeholder="Compacta, Semisaltada, Saltada">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Postura de zánganos</label>
                            <input type="text" class="form-control" name="calidad_reina[postura_zanganos]" placeholder="Normal, Alta">
                        </div>
                        <button type="button" class="btn btn-secondary prev-step" data-bs-target="#step1">Anterior</button>
                        <button type="button" class="btn btn-primary next-step" data-bs-target="#step3">Siguiente</button>
                    </div>

                    <!-- PCC3 -->
                    <div class="tab-pane fade" id="step3">
                        <h4>PCC3 - Estado Nutricional</h4>
                        <div class="mb-3">
                            <label class="form-label">Relación reservas de miel y polen / cantidad de cría</label>
                            <textarea class="form-control" name="estado_nutricional[reserva_miel_polen]" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tipo de alimentación</label>
                            <input type="text" class="form-control" name="estado_nutricional[tipo_alimentacion]">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Fecha de aplicación</label>
                            <input type="date" class="form-control" name="estado_nutricional[fecha_aplicacion]">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Insumo utilizado</label>
                            <input type="text" class="form-control" name="estado_nutricional[insumo_utilizado]">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Dosificación</label>
                            <input type="text" class="form-control" name="estado_nutricional[dosifiacion]">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Método utilizado</label>
                            <input type="text" class="form-control" name="estado_nutricional[metodo_utilizado]">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">N° de colmenas tratadas</label>
                            <input type="number" class="form-control" name="estado_nutricional[n_colmenas_tratadas]" min="0">
                        </div>
                        <button type="button" class="btn btn-secondary prev-step" data-bs-target="#step2">Anterior</button>
                        <button type="button" class="btn btn-primary next-step" data-bs-target="#step4">Siguiente</button>
                    </div>

                    <!-- PCC4 -->
                    <div class="tab-pane fade" id="step4">
                        <h4>PCC4 - Nivel de Infestación de Varroa</h4>
                        <div class="mb-3">
                            <label class="form-label">Diagnóstico visual</label>
                            <textarea class="form-control" name="presencia_varroa[diagnostico_visual]" rows="2" placeholder="Ej: varroa forética visible, ala mocha, cría salteada..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Muestreo de abejas adultas</label>
                            <textarea class="form-control" name="presencia_varroa[muestreo_abejas_adultas]" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Muestreo en cría operculada</label>
                            <textarea class="form-control" name="presencia_varroa[muestreo_cria_operculada]" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tratamiento aplicado (producto o práctica)</label>
                            <input type="text" class="form-control" name="presencia_varroa[tratamiento]">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Fecha de aplicación del tratamiento</label>
                            <input type="date" class="form-control" name="presencia_varroa[fecha_aplicacion]">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Dosificación</label>
                            <input type="text" class="form-control" name="presencia_varroa[dosificacion]">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Método de aplicación</label>
                            <input type="text" class="form-control" name="presencia_varroa[metodo_aplicacion]">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">N° de colmenas tratadas</label>
                            <input type="number" class="form-control" name="presencia_varroa[n_colmenas_tratadas]" min="0">
                        </div>
                        <button type="button" class="btn btn-secondary prev-step" data-bs-target="#step3">Anterior</button>
                        <button type="button" class="btn btn-primary next-step" data-bs-target="#step5">Siguiente</button>
                    </div>

                    <!-- PCC5 -->
                    <div class="tab-pane fade" id="step5">
                        <h4>PCC5 - Presencia de Nosemosis</h4>
                        <div class="mb-3">
                            <label class="form-label">Signos clínicos (diagnóstico visual)</label>
                            <textarea class="form-control" name="presencia_nosemosis[signos_clinicos]" rows="1" placeholder="Ej: abdomen hinchado, alas separadas..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Muestreo de abejas adultas (análisis de laboratorio)</label>
                            <textarea class="form-control" name="presencia_nosemosis[muestreo_laboratorio]" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tratamiento aplicado</label>
                            <input type="text" class="form-control" name="presencia_nosemosis[tratamiento]">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Fecha de aplicación del tratamiento</label>
                            <input type="date" class="form-control" name="presencia_nosemosis[fecha_aplicacion]">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Dosificación</label>
                            <input type="text" class="form-control" name="presencia_nosemosis[dosificacion]">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Método de aplicación</label>
                            <input type="text" class="form-control" name="presencia_nosemosis[metodo_aplicacion]">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">N° de colmenas tratadas</label>
                            <input type="number" class="form-control" name="presencia_nosemosis[num_colmenas_tratadas]" min="0">
                        </div>
                        <button type="button" class="btn btn-secondary prev-step" data-bs-target="#step4">Anterior</button>
                        <button type="button" class="btn btn-primary next-step" data-bs-target="#step6">Siguiente</button>
                    </div>

                    <!-- PCC6 -->
                    <div class="tab-pane fade" id="step6">
                        <h4>PCC6 - Índice de Cosecha</h4>
                        <div class="mb-3">
                            <label class="form-label">Madurez de la miel</label>
                            <select class="form-select" name="indice_cosecha[madurez_miel]">
                                <option value="Inmadura">Inmadura</option>
                                <option value="Madura">Madura</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">N° de alzas promedio por colmena</label>
                            <input type="number" step="0.1" class="form-control" name="indice_cosecha[num_alzadas]">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">N° de marcos con miel promedio por alza</label>
                            <input type="number" step="0.1" class="form-control" name="indice_cosecha[marcos_miel]">
                        </div>
                        <button type="button" class="btn btn-secondary prev-step" data-bs-target="#step5">Anterior</button>
                        <button type="button" class="btn btn-primary next-step" data-bs-target="#step7">Siguiente</button>
                    </div>

                    <!-- PCC7 -->
                    <div class="tab-pane fade" id="step7">
                        <h4>PCC7 - Preparación para la Invernada</h4>
                        <div class="mb-3">
                            <label class="form-label">Control sanitario</label>
                            <textarea class="form-control" name="preparacion_invernada[control_sanitario]" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Fusión de colmenas débiles</label>
                            <textarea class="form-control" name="preparacion_invernada[fusion_colmenas]" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Reserva de alimento</label>
                            <textarea class="form-control" name="preparacion_invernada[reserva_alimento]" rows="2"></textarea>
                        </div>
                        <button type="button" class="btn btn-secondary prev-step" data-bs-target="#step6">Anterior</button>
                        <button type="submit" class="btn btn-success">Guardar</button>
                    </div>

                </div>
            </form>
        </div>
    </div>
    </div>
</div>

<script>
    document.getElementById('evaluarActividad').addEventListener('click', function () {
        // Obtener valores de las entradas
        const alto = parseInt(document.getElementById('nc1').value) || 0;
        const medio = parseInt(document.getElementById('nc2').value) || 0;
        const bajo = parseInt(document.getElementById('nc3').value) || 0;
        // Calcular el promedio ponderado
        const totalColmenas = alto + medio + bajo;
        if (totalColmenas === 0) {
            alert('Debe ingresar al menos una colmena para calcular la actividad.');
            return;
        }
        const promedio = (alto * 3 + medio * 2 + bajo * 1) / totalColmenas;
        // Determinar el resultado según el promedio
        let resultado;
        if (promedio >= 2.5) {
            resultado = 'Alto';
        } else if (promedio >= 1.5) {
            resultado = 'Medio';
        } else {
            resultado = 'Bajo';
        }
        // Actualizar el campo principal
        document.getElementById('actividad').value = resultado;
    });
</script>

<script>
    // Wizard Navigation
    document.querySelectorAll('.next-step').forEach(button => {
        button.addEventListener('click', function () {
            const target = document.querySelector(this.dataset.bsTarget);
            const activeTab = document.querySelector('.tab-pane.active');
            activeTab.classList.remove('show', 'active');
            target.classList.add('show', 'active');
        });
    });
    document.querySelectorAll('.prev-step').forEach(button => {
        button.addEventListener('click', function () {
            const target = document.querySelector(this.dataset.bsTarget);
            const activeTab = document.querySelector('.tab-pane.active');
            activeTab.classList.remove('show', 'active');
            target.classList.add('show', 'active');
        });
    });
    document.addEventListener('DOMContentLoaded', () => {
    const tablaVigor = document.getElementById('tablaVigor');
    const vigorInput = document.getElementById('vigor');
    document.getElementById('agregarFila').addEventListener('click', () => {
        const fila = document.createElement('tr');
        fila.innerHTML = `
            <td>Colmena ${tablaVigor.rows.length + 1}</td>
            <td>
                <select class="form-select vigor-select">
                    <option value="Debil">Débil</option>
                    <option value="Regular">Regular</option>
                    <option value="Vigoroso">Vigoroso</option>
                </select>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm eliminarFila">Eliminar</button>
            </td>
        `;
        tablaVigor.appendChild(fila);
    });
    tablaVigor.addEventListener('click', (event) => {
        if (event.target.classList.contains('eliminarFila')) {
            event.target.closest('tr').remove();
        }
    });
    document.getElementById('calcularPromedio').addEventListener('click', () => {
        const opciones = Array.from(tablaVigor.querySelectorAll('.vigor-select')).map(select => select.value);
        const frecuencias = { Debil: 0, Regular: 0, Vigoroso: 0 };
        opciones.forEach(opcion => frecuencias[opcion]++);
        let promedio = 'Debil';
        if (frecuencias.Vigoroso > frecuencias.Regular && frecuencias.Vigoroso > frecuencias.Debil) {
            promedio = 'Vigoroso';
        } else if (frecuencias.Regular >= frecuencias.Vigoroso && frecuencias.Regular >= frecuencias.Debil) {
            promedio = 'Regular';
        }
        vigorInput.value = promedio;
        bootstrap.Modal.getInstance(document.getElementById('modalVigor')).hide();
    });
});
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Inicializa los tooltips de Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        const nextButtons = document.querySelectorAll(".next-step");
        const prevButtons = document.querySelectorAll(".prev-step");
        // Función para activar la pestaña correspondiente
        function activateTab(target) {
            // Desactivar la pestaña activa actual
            const activeTab = document.querySelector(".tab-pane.active");
            if (activeTab) activeTab.classList.remove("show", "active");
            const activeNav = document.querySelector(".nav-link.active");
            if (activeNav) activeNav.classList.remove("active");
            // Activar la nueva pestaña
            const targetTab = document.querySelector(target);
            if (targetTab) targetTab.classList.add("show", "active");
            const targetNav = document.querySelector(`a[href="${target}"]`);
            if (targetNav) targetNav.classList.add("active");
        }
        // Manejadores de eventos para los botones "Siguiente"
        nextButtons.forEach((button) => {
            button.addEventListener("click", function () {
                const target = this.getAttribute("data-bs-target");
                activateTab(target);
            });
        });
        // Manejadores de eventos para los botones "Anterior"
        prevButtons.forEach((button) => {
            button.addEventListener("click", function () {
                const target = this.getAttribute("data-bs-target");
                activateTab(target);
            });
        });
    });

    function Check(fieldId) {
    const mappings = {
        'pcc1_vigor_total': ['pcc1_vigor_1', 'pcc1_vigor_2', 'pcc1_vigor_3'],
        'pcc1_activity_total': ['pcc1_activity_1', 'pcc1_activity_2', 'pcc1_activity_3'],
        'pcc1_pollen_total': ['pcc1_pollen_1', 'pcc1_pollen_2'],
        'pcc1_block_total': ['pcc1_block_1', 'pcc1_block_2'],
        'pcc1_cells_total': ['pcc1_cells_1', 'pcc1_cells_2'],
        'pcc2_postura_total': ['pcc2_postura_1', 'pcc2_postura_2','pcc2_postura_3'],
        'pcc2_cria_total': ['pcc2_cria_1', 'pcc2_cria_2','pcc2_cria_3'],
        'pcc2_zanganos_total': ['pcc2_zanganos_1', 'pcc2_zanganos_2'],
        
        'pcc3_reserva_total': ['pcc3_reserva_1', 'pcc3_reserva_2','pcc3_reserva_3'],
        'pcc4_varroa_total': ['pcc4_varroa_1', 'pcc4_varroa_2','pcc4_varroa_3'],
        'pcc6_cosecha_total': ['pcc6_cosecha_1', 'pcc6_cosecha_2'],
    
    };
    // Obtiene los IDs de los inputs relacionados al fieldId
    const relatedFields = mappings[fieldId];
    if (!relatedFields) {
        console.error(`No se encontraron campos relacionados para ${fieldId}`);
        return;
    }
    // Itera sobre los inputs relacionados para obtener los valores
    let maxValue = -1;
    let maxIndex = -1;
    relatedFields.forEach((inputId, index) => {
        const inputElement = document.getElementById(inputId);
        const value = parseInt(inputElement.value) || 0; // Convierte el valor a número (default 0)
        if (value >= maxValue) {
            maxValue = value;
            maxIndex = index;
        }
    });
    // Define el resultado basado en el índice con mayor valor
    const resultLabels = {
        'pcc1_vigor_total': ['Débil', 'Regular', 'Vigorosa'],
        'pcc1_activity_total': ['Bajo', 'Medio', 'Alto'],
        'pcc1_pollen_total': ['No', 'Sí'],
        'pcc1_block_total': ['No', 'Sí'],
        'pcc1_cells_total': ['No', 'Sí'],
        'pcc2_postura_total': ['Irregular', 'Regular','Compacta'],
        'pcc2_cria_total': ['Compacta', 'Semisaltada','Saltada'],
        'pcc2_zanganos_total': ['Normal', 'Alta'],
        'pcc3_reserva_total': ['Bajo', 'Medio','Alto'],
        'pcc4_varroa_total': ['< 3%', '> 3%','No observado'],
        'pcc6_cosecha_total': ['Inmadura', 'Madura'],
    };
    const result = maxIndex !== -1 ? resultLabels[fieldId][maxIndex] : 'Sin datos';
    // Escribe el resultado en el input de total correspondiente
    const totalField = document.getElementById(fieldId);
    totalField.value = result;
}
</script>

@if ($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const lastTab = document.querySelector('#nav-pcc1-tab'); // por defecto
        @if(old('pcc7_preparacion_invernada'))
            document.querySelector('#nav-pcc7-tab').click();
        @elseif(old('pcc6_cosecha_total'))
            document.querySelector('#nav-pcc6-tab').click();
        @elseif(old('pcc5_nosemosis'))
            document.querySelector('#nav-pcc5-tab').click();
        @elseif(old('pcc4_varroa_total'))
            document.querySelector('#nav-pcc4-tab').click();
        // ... sigue con los demás
        @else
            lastTab.click();
        @endif
    });
</script>
@endif

@endsection
