<p>Hola,</p>
<p>Los siguientes productos archivados por más de 30 días han sido eliminados de tu inventario:</p>
<ul>
@foreach ($productos as $producto)
    <li>{{ $producto->nombre }} (ID: {{ $producto->id }})</li>
@endforeach
</ul>
