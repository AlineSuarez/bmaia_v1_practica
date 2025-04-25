@extends('layouts.app')
@section('title', 'MaiA - Sistema Experto')

@section('content')
<!-- Pantalla de carga (overlay) -->
<div id="loadingOverlay" class="loading-overlay" style="display: none;">
    <div class="spinner-container">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Cargando...</span>
        </div>
        <p class="mt-2">Cargando consejos, por favor espera...</p>
    </div>
</div>

<div class="container mt-4">
    <h1>Consejos Basados en los Registros</h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID del Apiario</th>
                <th>Nombre del Apiario</th>
                <th>Número de Colmenas</th>
                <th>Consejo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($apiarios as $apiario)
                <tr data-apiario="{{ $apiario->id }}">
                    <td>{{ $apiario->id }}</td>
                    <td>{{ $apiario->nombre }}</td>
                    <td>{{ $apiario->num_colmenas }}</td>
                    <td class="consejo-td">
                        <span class="spinner-border spinner-border-sm text-primary d-none" role="status"></span>
                        <span class="consejo-text"></span>
                    </td>
                    <td>
                        <button class="btn btn-primary btn-sm btn-consejo" data-id="{{ $apiario->id }}" title="Regenerar Consejo">
                            <i class="fas fa-sync" aria-label="Regenerar Consejo"></i>
                        </button>
                        <a href="{{ route('sistemaexperto.create', $apiario->id) }}" class="btn btn-success btn-sm" title="Registrar PCC">
                            <i class="fas fa-plus" aria-label="Registrar PCC"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <button class="btn btn-primary" id="regenerarConsejos">Regenerar Consejos</button>
</div>

<style>
.loading-overlay {
    position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background: white; display: flex; justify-content: center; align-items: center; z-index: 9999;
}
.spinner-container { text-align: center; }
</style>
@endsection

@section('optional-scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function cargarConsejo(apiarioId, row) {
    var consejoTd = row.find('.consejo-td');
    consejoTd.find('.spinner-border').removeClass('d-none');
    consejoTd.find('.consejo-text').text('');
    $.ajax({
        url: '/apiarios/' + apiarioId + '/obtener-consejo',
        type: 'GET',
        success: function(data){
            consejoTd.find('.spinner-border').addClass('d-none');
            if(data.success){
                consejoTd.find('.consejo-text').text(data.consejo);
            } else {
                consejoTd.find('.consejo-text').html('<span class="text-warning">'+data.message+'</span> <a href="'+data.registrar_pcc_url+'" class="btn btn-link btn-sm">Registrar PCC</a>');
            }
        },
        error: function(){
            consejoTd.find('.spinner-border').addClass('d-none');
            consejoTd.find('.consejo-text').html('<span class="text-danger">Error al obtener consejo</span>');
        }
    });
}

$(document).ready(function(){
    // Cargar consejos al iniciar
    $('tr[data-apiario]').each(function(){
        var apiarioId = $(this).data('apiario');
        cargarConsejo(apiarioId, $(this));
    });

    // Botón Regenerar Consejo individual
    $('.btn-consejo').click(function(){
        var apiarioId = $(this).data('id');
        var row = $(this).closest('tr');
        cargarConsejo(apiarioId, row);
    });

    // Botón Regenerar Consejos global
    $('#regenerarConsejos').click(function(){
        $('tr[data-apiario]').each(function(){
            var apiarioId = $(this).data('apiario');
            cargarConsejo(apiarioId, $(this));
        });
    });
});
</script>
@endsection
