@extends('layouts.app')

@section('content')

    <head>
        <link href="{{ asset('./css/components/home-user/sisexperto.css') }}" rel="stylesheet">
    </head>
    <!-- Pantalla de carga (overlay) -->
    <div id="loadingOverlay" class="loading-overlay" style="display: none;" aria-hidden="true">
        <div class="spinner-container">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2">Cargando consejos, por favor espera...</p>
        </div>
    </div>

    <div class="container mt-4">
        <header class="mb-4">
            <h1 class="page-title">Sistema Experto de Apiarios</h1>
            <p class="lead">Recomendaciones personalizadas basadas en tus registros de colmenas</p>
        </header>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h2 class="h5 mb-0">Consejos para tus Apiarios</h2>
                <button class="btn btn-primary" id="regenerarConsejos" aria-label="Regenerar todos los consejos">
                    <i class="fas fa-sync me-1"></i> Regenerar Todos
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Nombre del Apiario</th>
                                <th scope="col">Número de Colmenas</th>
                                <th scope="col" class="w-50">Consejo Personalizado</th>
                                <th scope="col" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($apiarios as $apiario)
                                <tr data-apiario="{{ $apiario->id }}">
                                    <td>{{ $apiario->id }}</td>
                                    <td>
                                        <strong>{{ $apiario->nombre }}</strong>
                                    </td>
                                    <td class="text-center">{{ $apiario->num_colmenas }}</td>
                                    <td class="consejo-td">
                                        <div class="d-flex align-items-center">
                                            <div class="spinner-border spinner-border-sm text-primary me-2 d-none"
                                                role="status"></div>
                                            <div class="consejo-text"></div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <button class="btn btn-outline-primary btn-sm btn-consejo"
                                                data-id="{{ $apiario->id }}" title="Regenerar Consejo"
                                                aria-label="Regenerar consejo para {{ $apiario->nombre }}">
                                                <i class="fas fa-sync"></i>
                                            </button>
                                            <a href="{{ route('sistemaexperto.create', $apiario->id) }}"
                                                class="btn btn-success btn-sm" title="Registrar PCC"
                                                aria-label="Registrar PCC para {{ $apiario->nombre }}">
                                                <i class="fas fa-plus"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <div class="alert alert-info mb-0">
                                            <i class="fas fa-info-circle me-2"></i>
                                            No hay apiarios registrados actualmente.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-light d-flex justify-content-end">
                <span class="text-muted small">Última actualización: <time
                        datetime="{{ date('Y-m-d') }}">{{ date('d/m/Y') }}</time></span>
            </div>
        </div>

        <section class="text-center mb-4">
            <div class="alert alert-info" role="alert">
                <p class="mb-0"><i class="fas fa-lightbulb me-2"></i> Los consejos se generan analizando los datos
                    históricos de tus colmenas</p>
            </div>
        </section>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function cargarConsejo(apiarioId, row) {
            var consejoTd = row.find('.consejo-td');
            consejoTd.find('.spinner-border').removeClass('d-none');
            consejoTd.find('.consejo-text').text('');
            $.ajax({
                url: '/apiarios/' + apiarioId + '/obtener-consejo',
                type: 'GET',
                success: function (data) {
                    consejoTd.find('.spinner-border').addClass('d-none');
                    if (data.success) {
                        consejoTd.find('.consejo-text').text(data.consejo);
                    } else {
                        consejoTd.find('.consejo-text').html('<div class="alert alert-warning p-2 mb-0"><span>' + data.message + '</span> <a href="' + data.registrar_pcc_url + '" class="btn btn-sm btn-outline-secondary ms-2">Registrar PCC</a></div>');
                    }
                },
                error: function () {
                    consejoTd.find('.spinner-border').addClass('d-none');
                    consejoTd.find('.consejo-text').html('<div class="alert alert-danger p-2 mb-0">Error al obtener consejo</div>');
                }
            });
        }

        $(document).ready(function () {
            // Cargar consejos al iniciar
            $('tr[data-apiario]').each(function () {
                var apiarioId = $(this).data('apiario');
                cargarConsejo(apiarioId, $(this));
            });

            // Botón Regenerar Consejo individual
            $('.btn-consejo').click(function () {
                var apiarioId = $(this).data('id');
                var row = $(this).closest('tr');
                cargarConsejo(apiarioId, row);
            });

            // Botón Regenerar Consejos global
            $('#regenerarConsejos').click(function () {
                $('tr[data-apiario]').each(function () {
                    var apiarioId = $(this).data('apiario');
                    cargarConsejo(apiarioId, $(this));
                });
            });
        });
    </script>
@endsection