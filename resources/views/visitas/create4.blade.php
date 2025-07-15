@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Registro de Calidad de Reina</h2>

        {{-- Formulario de edición/creación --}}
        <form action="{{ route('visitas.store4', $apiario) }}" method="POST">
            @csrf
            {{-- Si estás editando, el método puede ser PUT/PATCH --}}
            {{-- Laravel automáticamente manejará POST si el método real es PUT/PATCH a través de @method --}}
            @if(isset($visita) && $visita->exists)
                {{-- Si la visita ya existe y tiene un registro de calidad de reina, asumimos que estamos editando. --}}
                {{-- Considera si necesitas un @method('PUT') si tu ruta de actualización lo requiere --}}
                {{-- Para este escenario, si store4 maneja tanto create como update, no es estrictamente necesario,
                     pero si tienes una ruta 'visitas.update4' aparte, lo necesitarías.
                     Por ahora, el action apunta a 'store4' que podría ser un "upsert" o simplemente la ruta de creación.
                     Si tu controlador store4 puede recibir un ID de calidad de reina para actualizar, podrías pasarla aquí.
                --}}
                <input type="hidden" name="visita_id" value="{{ $visita->id }}">
            @endif
            {{-- Asegúrate de pasar el ID de calidadReina si estás editando un registro existente --}}
            @if(isset($calidadReina) && $calidadReina->exists)
                 <input type="hidden" name="calidad_reina_id" value="{{ $calidadReina->id }}">
            @endif

            <div class="form-grid">
                {{-- Postura de la Reina --}}
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-egg me-2"></i>
                        <strong>Postura de la reina</strong>
                    </label>
                    <select class="form-select form-control-modern" name="calidad_reina[postura_reina]">
                        <option value="">Seleccionar postura...</option>
                        <option value="Irregular" {{ old('calidad_reina.postura_reina', $calidadReina->postura_reina ?? '') == 'Irregular' ? 'selected':'' }}>Irregular</option>
                        <option value="Regular" {{ old('calidad_reina.postura_reina', $calidadReina->postura_reina ?? '') == 'Regular' ? 'selected':'' }}>Regular</option>
                        <option value="Compacta" {{ old('calidad_reina.postura_reina', $calidadReina->postura_reina ?? '') == 'Compacta' ? 'selected':'' }}>Compacta</option>
                    </select>
                </div>

                {{-- Estado de la Cría --}}
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-baby me-2"></i>
                        <strong>Estado de la cría</strong>
                    </label>
                    <select class="form-select form-control-modern" name="calidad_reina[estado_cria]">
                        <option value="">Seleccionar estado...</option>
                        <option value="Compacta" {{ old('calidad_reina.estado_cria', $calidadReina->estado_cria ?? '') == 'Compacta' ? 'selected':'' }}>Compacta</option>
                        <option value="Semisaltada" {{ old('calidad_reina.estado_cria', $calidadReina->estado_cria ?? '') == 'Semisaltada' ? 'selected':'' }}>Semisaltada</option>
                        <option value="Saltada" {{ old('calidad_reina.estado_cria', $calidadReina->estado_cria ?? '') == 'Saltada' ? 'selected':'' }}>Saltada</option>
                    </select>
                </div>

                {{-- Postura de Zánganos --}}
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-male me-2"></i>
                        <strong>Postura de zánganos</strong>
                    </label>
                    <select class="form-select form-control-modern" name="calidad_reina[postura_zanganos]">
                        <option value="">Seleccionar postura...</option>
                        <option value="Normal" {{ old('calidad_reina.postura_zanganos', $calidadReina->postura_zanganos ?? '') == 'Normal' ? 'selected':'' }}>Normal</option>
                        <option value="Alta" {{ old('calidad_reina.postura_zanganos', $calidadReina->postura_zanganos ?? '') == 'Alta' ? 'selected':'' }}>Alta</option>
                    </select>
                </div>

                {{-- Origen de la Reina --}}
                <div class="form-group">
                    <label class="form-label"><strong>Origen de la reina</strong></label>
                    <select class="form-select form-control-modern" name="calidad_reina[origen_reina]">
                        <option value="">Seleccionar...</option>
                        <option value="natural" {{ old('calidad_reina.origen_reina', $calidadReina->origen_reina ?? '') == 'natural' ? 'selected':'' }}>Natural</option>
                        <option value="comprada" {{ old('calidad_reina.origen_reina', $calidadReina->origen_reina ?? '') == 'comprada' ? 'selected':'' }}>Comprada</option>
                        <option value="fecundada" {{ old('calidad_reina.origen_reina', $calidadReina->origen_reina ?? '') == 'fecundada' ? 'selected':'' }}>Fecundada</option>
                        <option value="virgen" {{ old('calidad_reina.origen_reina', $calidadReina->origen_reina ?? '') == 'virgen' ? 'selected':'' }}>Vírgen</option>
                    </select>
                </div>

                {{-- Raza --}}
                <div class="form-group">
                    <label class="form-label"><strong>Raza</strong></label>
                    <input type="text" class="form-control form-control-modern" name="calidad_reina[raza]" placeholder="Ej. italiana, carníca…" value="{{ old('calidad_reina.raza', $calidadReina->raza ?? '') }}">
                </div>

                {{-- Línea Genética --}}
                <div class="form-group">
                    <label class="form-label"><strong>Línea genética</strong></label>
                    <input type="text" class="form-control form-control-modern" name="calidad_reina[linea_genetica]" value="{{ old('calidad_reina.linea_genetica', $calidadReina->linea_genetica ?? '') }}">
                </div>

                {{-- Fecha de Introducción --}}
                <div class="form-group">
                    <label class="form-label"><strong>Fecha de introducción</strong></label>
                    <input type="date" class="form-control form-control-modern" name="calidad_reina[fecha_introduccion]" value="{{ old('calidad_reina.fecha_introduccion', $calidadReina->fecha_introduccion ?? '') }}">
                </div>

                {{-- Estado Actual --}}
                <div class="form-group">
                    <label class="form-label"><strong>Estado actual</strong></label>
                    <select class="form-select form-control-modern" name="calidad_reina[estado_actual]">
                        <option value="">Seleccionar...</option>
                        <option value="activa" {{ old('calidad_reina.estado_actual', $calidadReina->estado_actual ?? '') == 'activa' ? 'selected':'' }}>Activa</option>
                        <option value="fallida" {{ old('calidad_reina.estado_actual', $calidadReina->estado_actual ?? '') == 'fallida' ? 'selected':'' }}>Fallida</option>
                        <option value="reemplazada" {{ old('calidad_reina.estado_actual', $calidadReina->estado_actual ?? '') == 'reemplazada' ? 'selected':'' }}>Reemplazada</option>
                    </select>
                </div>

                ---

                {{-- Reemplazos Realizados (Dinámico) --}}
                <div class="form-group">
                    <label class="form-label"><strong>Reemplazos realizados</strong></label>
                    <div id="reemplazos-container">
                        @php
                            // Intentar cargar los reemplazos desde $calidadReina->reemplazos_realizados
                            // Si $calidadReina->reemplazos_realizados es un string JSON (guardado así en DB), decodificarlo.
                            // Si no hay datos, o si old() tiene datos de un submit fallido, usar old().
                            $reemplazos = old('calidad_reina.reemplazos_realizados', $calidadReina->reemplazos_realizados ?? []);

                            // Asegurarse de que $reemplazos es un array.
                            // Si viene de la base de datos como string JSON, json_decode lo convierte en array.
                            // Si es un objeto (e.g., Cast a Collection en el modelo), convertirlo a array.
                            if (is_string($reemplazos)) {
                                $decodedReemplazos = json_decode($reemplazos, true);
                                if (json_last_error() === JSON_ERROR_NONE && is_array($decodedReemplazos)) {
                                    $reemplazos = $decodedReemplazos;
                                } else {
                                    $reemplazos = []; // Fallback a array vacío si no es JSON válido
                                }
                            } elseif ($reemplazos instanceof \Illuminate\Support\Collection) {
                                $reemplazos = $reemplazos->toArray();
                            } elseif (!is_array($reemplazos)) {
                                $reemplazos = []; // Asegurarse de que es un array
                            }

                            // Si después de todo esto el array está vacío, añadir un elemento vacío para mostrar un campo inicial
                            if (empty($reemplazos)) {
                                $reemplazos = [[]]; // Añade un array vacío para un reemplazo inicial vacío
                            }
                        @endphp

                        @foreach($reemplazos as $index => $reemplazo)
                            <div class="row gx-2 mb-2 replacement-item">
                                <div class="col-5">
                                    <input type="date"
                                        name="calidad_reina[reemplazos_realizados][{{ $index }}][fecha]"
                                        value="{{ old('calidad_reina.reemplazos_realizados.' . $index . '.fecha', $reemplazo['fecha'] ?? '') }}"
                                        class="form-control form-control-modern"
                                        placeholder="dd-mm-aaaa" />
                                </div>
                                <div class="col-5">
                                    <input type="text"
                                        name="calidad_reina[reemplazos_realizados][{{ $index }}][motivo]"
                                        value="{{ old('calidad_reina.reemplazos_realizados.' . $index . '.motivo', $reemplazo['motivo'] ?? '') }}"
                                        class="form-control form-control-modern"
                                        placeholder="Motivo del reemplazo" />
                                </div>
                                <div class="col-2 d-flex align-items-center" title="Eliminar reemplazo">
                                    {{-- El botón de eliminar se deshabilita si es el único campo --}}
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove-replacement" {{ count($reemplazos) === 1 && $index === 0 ? 'disabled' : '' }}>
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-success btn-add-replacement mt-2" title="Añadir reemplazo">
                        <i class="fas fa-plus"></i> 
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Guardar</button>
        </form>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const reemplazosContainer = document.getElementById('reemplazos-container');
            const addButton = document.querySelector('.btn-add-replacement');
            // Inicializa replacementIndex con el número de elementos existentes en el DOM al cargar
            let replacementIndex = reemplazosContainer.children.length;

            // Función para actualizar el estado de los botones de eliminar (habilitar/deshabilitar)
            function updateRemoveButtons() {
                const removeButtons = reemplazosContainer.querySelectorAll('.btn-remove-replacement');
                if (removeButtons.length === 1) {
                    removeButtons[0].setAttribute('disabled', 'true'); // Deshabilita si solo hay un campo
                } else {
                    removeButtons.forEach(button => button.removeAttribute('disabled')); // Habilita si hay más de uno
                }
            }

            // Función para añadir un nuevo campo de reemplazo
            function addReplacementField() {
                const newRow = document.createElement('div');
                newRow.classList.add('row', 'gx-2', 'mb-2', 'replacement-item');
                newRow.innerHTML = `
                    <div class="col-5">
                        <input type="date"
                            name="calidad_reina[reemplazos_realizados][${replacementIndex}][fecha]"
                            class="form-control form-control-modern"
                            placeholder="dd-mm-aaaa" />
                    </div>
                    <div class="col-5">
                        <input type="text"
                            name="calidad_reina[reemplazos_realizados][${replacementIndex}][motivo]"
                            class="form-control form-control-modern"
                            placeholder="Motivo del reemplazo" />
                    </div>
                    <div class="col-2 d-flex align-items-center">
                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-replacement">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                `;
                reemplazosContainer.appendChild(newRow);
                replacementIndex++; // Incrementa el índice para el siguiente campo
                updateRemoveButtons(); // Actualiza los botones después de añadir un campo
            }

            // Añadir evento al botón "Añadir reemplazo"
            addButton.addEventListener('click', addReplacementField);

            // Añadir evento de delegación para los botones "Eliminar reemplazo"
            reemplazosContainer.addEventListener('click', function(event) {
                // Comprueba si el clic fue en un botón de eliminar o en un icono dentro de él
                if (event.target.classList.contains('btn-remove-replacement') || event.target.closest('.btn-remove-replacement')) {
                    const button = event.target.closest('.btn-remove-replacement');
                    // Solo permite eliminar si hay más de un campo
                    if (reemplazosContainer.children.length > 1) {
                        button.closest('.replacement-item').remove();
                        // Al eliminar, es buena práctica reindexar los names para asegurar que Laravel
                        // los reciba en un array secuencial, aunque Laravel maneja arrays dispersos.
                        // Esto es útil si esperas índices 0, 1, 2... sin saltos.
                        // Si no lo haces, y borras el [1], tendrías [0], [2], lo cual es válido para Laravel,
                        // pero podría ser confuso en otras lógicas.
                        reindexReplacementFields();
                        updateRemoveButtons(); // Actualiza los botones después de eliminar
                    }
                }
            });

            // Nueva función para reindexar los nombres de los campos después de una eliminación
            function reindexReplacementFields() {
                const replacementItems = reemplazosContainer.querySelectorAll('.replacement-item');
                replacementItems.forEach((item, newIndex) => {
                    item.querySelectorAll('input').forEach(input => {
                        const oldName = input.name;
                        // Reemplaza el índice numérico en el nombre del input
                        input.name = oldName.replace(/\[reemplazos_realizados\]\[\d+\]/, `[reemplazos_realizados][${newIndex}]`);
                    });
                });
                replacementIndex = replacementItems.length; // Actualiza el índice base para futuras adiciones
            }

            // Llama a la función para inicializar el estado de los botones al cargar la página
            updateRemoveButtons();
        });
    </script>
    @endpush
@endsection