<div class="{{ $type === 'settings' ? 'price-details mt-2' : 'price-breakdown' }}">
    <div style="font-size:15px; color:#6c757d;">
        Costo Mensual: ${{ number_format($monthlyWithIva, 0, ',', '.') }} con IVA
    </div>

    @if($perColmena)
        <div style="font-size:15px; color:#6c757d;">
            Costo Anual por Colmena: ${{ number_format($perColmena, 0, ',', '.') }}
        </div>
    @endif

    <span style="font-size:18px; font-weight:bold; color:#198754; display:block; margin-top:6px;">
        Total con IVA: ${{ number_format($withIva, 0, ',', '.') }}
    </span>
</div>
