<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inventario Apicola</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 20px;
            color: #333;
        }
        h1 {
            text-align: center;
            font-size: 18px;
            margin-bottom: 30px;
        }
        .tarea-general {
            background-color: #f4f4f4;
            padding: 8px;
            margin-top: 20px;
            border-left: 4px solid #0c5460;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        th, td {
            border: 1px solid #aaa;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #ddd;
        }
    </style>
</head>
<body>

    <h1>Historial de Compras</h1>
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Cantidad</th>
                <th>Precio</th>
                <th>Proveedor</th>
                <th>Fecha Ultima Modificacion</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($historial as $producto)
                <tr>
                    <td>{{ $producto->Inventory->nombreProducto }}</td>
                    <td>{{ $producto->cantidad }}</td>
                    <td>${{ $producto->precio }}</td>
                    <td>
                            {{ $producto->proveedor ?? 'No especificado' }}   
                    </td>
                    <td>{{ \Carbon\Carbon::parse($producto->fecha_actualizacion)->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">No se encontraron productos para mostrar.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
      <p><strong>Generado el {{ now()->format('d/m/Y H:i') }}</strong> - Sistema de Gestión Apícola</p>
    </div>
</body>
</html>
