@extends('layouts.app')

@section('title', 'Colmenas del Apiario')

@section('content')
<style>
    .colmena-card {
        width: 60px;
        height: 60px;
        border: 2px solid #ccc;
        border-radius: 8px;
        margin: 8px;
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .colmena-card.active {
        background-color: #28a745;
        color: white;
        border-color: #28a745;
    }

    .colmena-card:hover {
        box-shadow: 0 0 8px rgba(0,0,0,0.1);
    }

    .colmena-icon {
        font-size: 1.2rem;
        margin-bottom: 2px;
    }

    .qr-tooltip {
        position: absolute;
        background-color: white;
        padding: 6px;
        border: 1px solid #ccc;
        border-radius: 6px;
        display: none;
        z-index: 10;
    }
</style>

<div class="container">
<!--<div class="d-flex justify-content-between align-items-center bg-light p-3 rounded shadow-sm mb-4">
        <h5 class="mb-0">Colmenas del Apiario "{{ $apiario->nombre }}"</h5>
        <a href="{{ route('colmenas.create', $apiario->id) }}" class="btn btn-sm btn-primary">
            <i class="fas fa-plus-circle me-1"></i> Agregar Colmena
        </a>
    </div>-->
    

    <div class="d-flex flex-wrap position-relative">
        @forelse($colmenas as $colmena)
            <div class="colmena-card"
                style="border-color: {{ $colmena->color_etiqueta }};"
                onclick="window.location='{{ route('colmenas.show', [$apiario->id, $colmena->id]) }}'"
                data-tooltip="<img src='https://api.qrserver.com/v1/create-qr-code/?data={{ urlencode($colmena->codigo_qr) }}&size=100x100'>">
                <div class="colmena-icon">
                    <i class="fas fa-cube"></i>
                </div>
                <div>#{{ $colmena->numero }}</div>
            </div>
           
        @empty
            <div class="alert alert-warning">
                No hay colmenas registradas para este apiario.
            </div>
        @endforelse
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const cards = document.querySelectorAll('.colmena-card');

        cards.forEach(card => {
            card.addEventListener('mouseenter', function (e) {
                const tooltip = document.createElement('div');
                tooltip.classList.add('qr-tooltip');
                tooltip.innerHTML = this.getAttribute('data-tooltip');
                document.body.appendChild(tooltip);

                const rect = this.getBoundingClientRect();
                tooltip.style.top = `${rect.top - tooltip.offsetHeight - 5 + window.scrollY}px`;
                tooltip.style.left = `${rect.left + rect.width / 2 - tooltip.offsetWidth / 2 + window.scrollX}px`;
                tooltip.style.display = 'block';

                card.addEventListener('mouseleave', () => {
                    tooltip.remove();
                });
            });
        });
    });
</script>
@endsection
