@extends('layouts.app')

@section('title', 'Colmenas del Apiario')

@section('content')

    <head>
        <link href="{{ asset('./css/components/home-user/colmenas.css') }}" rel="stylesheet">
    </head>

    <div class="main-layout">
        <div class="container">
            <div class="page-header">
                <div class="header-row">
                    <h1 class="page-title">Colmenas del Apiario</h1>
                    <div class="back-button-container">
                        <a href="{{ route('apiarios') }}" class="back-button">
                            <i class="fas fa-arrow-left"></i>
                            <span>Volver a Apiarios</span>
                        </a>
                    </div>
                </div>
                <div class="apiario-info">Apiario: {{ $apiario->nombre }}</div>
                <div class="apiario-stats">
                    <div class="stat-item">
                        <div class="stat-icon"><i class="fas fa-cube"></i></div>
                        <span>Total: {{ $colmenasPorApiarioBase->flatten()->count() }} colmenas</span>
                    </div>
                    @if($apiario->tipo_apiario === 'trashumante' && !$apiario->es_temporal)
                        <div class="stat-item">
                            <div class="stat-icon"><i class="fas fa-layer-group"></i></div>
                            <span>Apiario Base</span>
                        </div>
                    @endif
                    @if($apiario->es_temporal)
                        <div class="stat-item">
                            <div class="stat-icon"><i class="fas fa-clock"></i></div>
                            <span>Apiario Temporal</span>
                        </div>
                    @endif
                    <div class="stat-item">
                        <div class="stat-icon"><i class="fas fa-layer-group"></i></div>
                        <span>Apiarios de Origen: {{ $colmenasPorApiarioBase->count() }}</span>
                    </div>
                </div>
            </div>

            @foreach($colmenasPorApiarioBase as $nombreBase => $colmenas)
                <div class="section-group">
                    <div class="group-header">
                        @if($mostrarTitulos)
                            <h2 class="section-title">
                                <i class="fas fa-home mr-2"></i>Apiario de Origen: {{ $nombreBase }}
                            </h2>
                        @else
                            <h2 class="section-title">
                                <i class="fas fa-cube mr-2"></i>Colmenas del Apiario
                            </h2>
                        @endif
                    </div>

                    <div class="colmenas-container">
                        <div class="colmenas-info">
                            <div class="colmenas-count">
                                <strong>{{ $colmenas->count() }}</strong> colmenas en este grupo
                            </div>
                        </div>

                        <div class="colmenas-grid">
                            @forelse($colmenas as $colmena)
                                @php
        $url = route('colmenas.show', [
            'apiario' => $apiario->id,
            'colmena' => $colmena->id,
        ]);
        $color = $colmena->color_etiqueta ?? 'transparent';
        if ($colmena->color_etiqueta) {
            $hex = ltrim($colmena->color_etiqueta, '#');
            if (strlen($hex) === 6) {
                $r = hexdec(substr($hex, 0, 2));
                $g = hexdec(substr($hex, 2, 2));
                $b = hexdec(substr($hex, 4, 2));
                $color = "rgba($r, $g, $b, 0.47)";
            } else {
                $color = $colmena->color_etiqueta;
            }
        } else {
            $color = 'transparent';
        }
                                @endphp

                                <div class="colmena-card"
                                    style="background-color: {{ $color }}; border-color: {{ $colmena->color_etiqueta ?? '#70707045' }}; --colmena-color: {{ $colmena->color_etiqueta ?? '#f5f5f5' }};"
                                    onclick="window.location='{{ $url }}'"
                                    data-tooltip="<img src='https://api.qrserver.com/v1/create-qr-code/?data={{ urlencode($url) }}&size=100x100'>">
                                    <div class="colmena-icon">
                                        <i class="fas fa-cube"></i>
                                    </div>
                                    <div class="colmena-number">#{{ $colmena->numero }}</div>
                                </div>
                            @empty
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="fas fa-cube"></i>
                                    </div>
                                    <div>No hay colmenas registradas para este grupo</div>
                                </div>
                            @endforelse
                        </div>

                        <!-- Botón después de las colmenas -->
                        <div class="print-qr-container">
                            <button class="print-qr-button"
                                onclick="openQrModal('{{ $nombreBase }}', {{ $colmenas->toJson() }})">
                                <i class="fas fa-qrcode"></i>
                                <span>Imprimir QR</span>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Modal para selección de colmenas -->
    <div id="qrModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Seleccionar Colmenas para Imprimir QR</h3>
                <span class="close" onclick="closeQrModal()">&times;</span>
            </div>

            <div class="colmenas-selection" id="colmenasSelection">
                <!-- Se llenará dinámicamente -->
            </div>

            <div class="modal-actions">
                <button class="select-all-btn" onclick="toggleSelectAll()">
                    <span id="selectAllText">Seleccionar Todas</span>
                </button>

                <div class="selected-count">
                    Seleccionadas: <span id="selectedCount">0</span>
                </div>

                <button class="generate-pdf-btn" onclick="generateQrPdf()" id="generateBtn" disabled>
                    <i class="fas fa-file-pdf"></i>
                    Generar PDF
                </button>
            </div>
        </div>
    </div>

    <div id="loadingOverlay" class="loading-overlay">
        <div class="loading-content">
            <div class="loading-spinner">
                <svg class="spinner-svg" viewBox="0 0 50 50">
                    <circle class="spinner-bg" cx="25" cy="25" r="20" fill="none" stroke="#f3f3f3" stroke-width="5"/>
                    <circle class="spinner-fg" cx="25" cy="25" r="20" fill="none" stroke="#f5a700" stroke-width="5"/>
                </svg>
            </div>
            <div class="loading-title">Generando PDF</div>
            <div class="loading-text">Por favor espera mientras preparamos tu archivo.<br>¡Esto puede tardar un momento!</div>
        </div>
    </div>

    <script>
        let selectedColmenas = [];
        let allColmenas = [];
        let currentGrupo = '';

        function openQrModal(grupo, colmenas) {
            currentGrupo = grupo;
            allColmenas = colmenas;
            selectedColmenas = [];

            document.getElementById('modalTitle').textContent = `Seleccionar Colmenas - ${grupo}`;

            const container = document.getElementById('colmenasSelection');
            container.innerHTML = '';

            colmenas.forEach(colmena => {
                const div = document.createElement('div');
                div.className = 'colmena-checkbox';
                div.dataset.colmenaId = colmena.id;
                div.onclick = () => toggleColmena(colmena.id, div);

                const color = colmena.color_etiqueta || '#f5f5f5';

                div.innerHTML = `
                                    <input type="checkbox" style="margin-right: 8px;">
                                    <div style="display: flex; align-items: center;">
                                        <div style="width: 20px; height: 20px; background-color: ${color}; border: 1px solid #ccc; border-radius: 50px; margin-right: 8px;"></div>
                                        <span>#${colmena.numero}</span>
                                    </div>
                                `;

                container.appendChild(div);
            });

            updateUI();
            document.getElementById('qrModal').style.display = 'block';
        }

        function closeQrModal() {
            document.getElementById('qrModal').style.display = 'none';
        }

        function toggleColmena(colmenaId, element) {
            const checkbox = element.querySelector('input[type="checkbox"]');

            if (selectedColmenas.includes(colmenaId)) {
                selectedColmenas = selectedColmenas.filter(id => id !== colmenaId);
                element.classList.remove('selected');
                checkbox.checked = false;
            } else {
                selectedColmenas.push(colmenaId);
                element.classList.add('selected');
                checkbox.checked = true;
            }

            updateUI();
        }

        function toggleSelectAll() {
            const selectAllBtn = document.getElementById('selectAllText');

            if (selectedColmenas.length === allColmenas.length) {
                // Deseleccionar todas
                selectedColmenas = [];
                document.querySelectorAll('.colmena-checkbox').forEach(el => {
                    el.classList.remove('selected');
                    el.querySelector('input[type="checkbox"]').checked = false;
                });
                selectAllBtn.textContent = 'Seleccionar Todas';
            } else {
                // Seleccionar todas
                selectedColmenas = allColmenas.map(c => c.id);
                document.querySelectorAll('.colmena-checkbox').forEach(el => {
                    el.classList.add('selected');
                    el.querySelector('input[type="checkbox"]').checked = true;
                });
                selectAllBtn.textContent = 'Deseleccionar Todas';
            }

            updateUI();
        }

        function updateUI() {
            const count = selectedColmenas.length;
            document.getElementById('selectedCount').textContent = count;

            const generateBtn = document.getElementById('generateBtn');
            generateBtn.disabled = count === 0;

            const selectAllBtn = document.getElementById('selectAllText');
            selectAllBtn.textContent = count === allColmenas.length ? 'Deseleccionar Todas' : 'Seleccionar Todas';
        }

        function generateQrPdf() {
            if (selectedColmenas.length === 0) {
                alert('Por favor selecciona al menos una colmena');
                return;
            }

            // Mostrar pantalla de carga
            document.getElementById('loadingOverlay').classList.add('active');

            // Prepara los datos del formulario
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            selectedColmenas.forEach(colmenaId => formData.append('colmenas[]', colmenaId));
            formData.append('grupo', currentGrupo);

            fetch('{{ route("colmenas.qr.multiple", $apiario->id) }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/pdf'
                }
            })
                .then(response => {
                    if (!response.ok) throw new Error('Error generando el PDF');
                    return response.blob();
                })
                .then(blob => {
                    // Forzar descarga del PDF
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'Colmenas_QR_{{ $apiario->nombre }}.pdf';
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                    window.URL.revokeObjectURL(url);
                })
                .catch(() => {
                    alert('Ocurrió un error al generar el PDF');
                })
                .finally(() => {
                    document.getElementById('loadingOverlay').classList.remove('active');
                    closeQrModal();
                });
        }

        // Cerrar modal al hacer clic fuera
        window.onclick = function (event) {
            const modal = document.getElementById('qrModal');
            if (event.target === modal) {
                closeQrModal();
            }
        }
    </script>

    <script src="{{ asset('js/components/home-user/colmenas.js') }}"></script>
@endsection