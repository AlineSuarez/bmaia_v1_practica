<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\HistorialCambios;
use App\Models\InventarioPredefinido;
use App\Models\Pedido;
use App\Models\Subcategory;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

        // Si no existen productos en el inventario para el usuario, insertar productos predefinidos
        if (Inventory::where('user_id', $user->id)->count() == 0) {
            $predefinedProducts = InventarioPredefinido::all();

            foreach ($predefinedProducts as $predefinedProduct) {
                // Crear producto base
                $inventoryItem = Inventory::create([
                    'user_id' => $user->id,
                    'nombreProducto' => $predefinedProduct->nombreProducto,
                    'cantidad' => $predefinedProduct->cantidad,
                    'category_id' => $predefinedProduct->category_id,
                    'precio' => $predefinedProduct->precio,
                    'observacion' => $predefinedProduct->observacion,
                    'archivada' => false,
                    //El campo de unidad por defecto se guarda como "Unidad"
                ]);

                // Asociar subcategorias
                $subcategories = DB::table('inventario_predefinido_subcategory')
                    ->where('inventario_predefinido_id', $predefinedProduct->id)
                    ->pluck('subcategory_id');

                if ($subcategories->isNotEmpty()) {
                    $inventoryItem->subcategories()->sync($subcategories);
                }
            }
        }

        // Mostrar los productos del inventario del usuario no archivados
        $productos = Inventory::with('subcategories')
            ->where('user_id', $user->id)
            ->where('archivada', false)
            ->paginate(8);

        // Devolver categorias y subcategorias
        $categories = Category::all();
        $subcategories = Subcategory::all();

        return view('Inventario.index', compact('categories', 'subcategories', 'productos'));
    }

    public function archivar(Request $request, $id = null)
    {
        $user = Auth::user();

        try {
            //Archivar varios
            if ($request->filled('ids')) {
                $ids = $request->input('ids');

                Inventory::where('user_id', $user->id)
                    ->whereIn('id', $ids)
                    ->update([
                        'archivada' => true,
                        'fecha_archivado' => now()
                    ]);

                return redirect()
                    ->route('inventario')
                    ->with('success', 'Productos archivados correctamente.');
            }

            // Archivar solo uno
            if ($id) {
                $producto = Inventory::where('user_id', $user->id)->findOrFail($id);
                $producto->archivada = true;
                $producto->fecha_archivado = now();
                $producto->save();

                return redirect()
                    ->route('inventario')
                    ->with('success', 'Producto archivado correctamente.');
            }

            // Si no viene nada
            return redirect()
                ->route('inventario')
                ->with('error', 'No se especificaron productos para archivar.');

        } catch (\Exception $e) {
            return redirect()
                ->route('inventario')
                ->with('error', 'Error al archivar productos: ' . $e->getMessage());
        }
    }

    public function restaurar(Request $request, $id = null)
    {
        $user = Auth::user();

        try {
            //restaurar varios
            if ($request->filled('ids')) {
                $ids = $request->input('ids');

                Inventory::where('user_id', $user->id)
                    ->whereIn('id', $ids)
                    ->update(['archivada' => false]);

                return redirect()
                    ->route('inventario')
                    ->with('success', 'Productos restaurados correctamente.');
            }

            //Restaurar uno
            if ($id) {
                $producto = Inventory::where('user_id', $user->id)->findOrFail($id);
                $producto->archivada = false;
                $producto->save();

                return redirect()
                    ->route('inventario')
                    ->with('success', 'Producto restaurado correctamente.');
            }

            //Si no viene nada
            return redirect()
                ->route('inventario')
                ->with('error', 'No se especificaron productos para restaurar.');

        } catch (\Exception $e) {
            return redirect()
                ->route('inventario')
                ->with('error', 'Error al restaurar productos: ' . $e->getMessage());
        }
    }

    public function verArchivadas(Request $request)
    {
        $user = Auth::user();

        $productos = Inventory::with('subcategories')
            ->where('user_id', $user->id)
            ->where('archivada', true)
            ->paginate(8);

        if ($request->ajax()) {
            return view('Inventario.archivadas', compact('productos'))->render();
        }

        // Fuerza el path base a /inventario/archivadas
        $productos->withPath(url('/inventario/archivadas'));

        return view('Inventario.archivadas', compact('productos'));
    }

    public function search(Request $request)
    {
        $user = Auth::user();
        
        $categories = Category::all();
        $subcategories = Subcategory::all();
        
        $query = Inventory::with('subcategories')
            ->where('user_id', $user->id)
            ->where('archivada', false);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        $subcategoriesIds = $request->input('subcategory_id');

        if (is_array($subcategoriesIds) && count($subcategoriesIds) > 0) {
            $query->whereHas('subcategories', function ($q) use ($subcategoriesIds) {
                $q->whereIn('subcategory_id', $subcategoriesIds);
            });
        }

        if ($request->filled('q')) {
            $query->where('nombreProducto', 'LIKE', '%' . $request->input('q') . '%');
        }

        $productos = $query->paginate(8);
        
        if ($request->ajax()) {
            return view('inventario.partials.listado', compact('productos', 'categories', 'subcategories'))->render();
        }

        // Fuerza el path base a /inventario/
        $productos->withPath(url('/inventario'));

        return view('Inventario.list', compact('productos', 'categories', 'subcategories'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $categories = Category::all();
        $subcategories = Subcategory::all();

        return view('Inventario.create', compact('categories', 'subcategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nombreProducto' => 'required|string|max:255',
            'cantidad' => 'required|numeric',
            'category_id' => 'required|integer',
            'subcategory_id' => 'required|array',
            'subcategory_id.*' => 'integer|exists:subcategories,id',
            'precio' => 'required|numeric',
            'observacion' => 'nullable|string',
        ]);

        if ($request->expectsJson()) {
            $isAjax = true;
        } else {
            $isAjax = $request->ajax();
        }

        // Buscar coincidencias (incluye archivadas)
        $similares = Inventory::where('user_id', $user->id)
            ->whereRaw('LOWER(nombreProducto) LIKE ?', ['%' . strtolower($request->nombreProducto) . '%'])
            ->get();

        // Si existen coincidencias y NO hay confirmacion
        if ($similares->isNotEmpty() && !$request->boolean('confirmar')) {

            if ($isAjax) {
                return response()->json([
                    'status' => 'duplicate',
                    'message' => 'Se encontraron productos con un nombre similar en tu inventario o archivados. ¿Deseas crearlo igualmente?'
                ]);
            }

            return back()
                ->with('warning_similares', $similares)
                ->withInput()
                ->with('mensaje_advertencia', 'Se encontraron productos similares. ¿Deseas crear este producto de todas formas?');
        }

        try {
            //Crear el nuevo producto
            $producto = Inventory::create([
                'user_id' => $user->id,
                'nombreProducto' => $request->nombreProducto,
                'cantidad' => $request->cantidad,
                'category_id' => $request->category_id,
                'precio' => $request->precio,
                'observacion' => $request->observacion,
                'archivada' => false,
            ]);

            //Asociar subcategorías
            $producto->subcategories()->sync($request->subcategory_id);

            //Registrar en historial
            HistorialCambios::create([
                'inventory_id' => $producto->id,
                'user_id' => $user->id,
                'precio' => $request->input('precio'),
                'cantidad' => $request->input('cantidad'),
                'fecha_actualizacion' => $producto->updated_at,
            ]);

            if ($isAjax) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Producto creado correctamente.'
                ]);
            }

            return redirect()
                ->route('inventario')
                ->with('success', 'Producto creado correctamente.');

        } catch (\Exception $e) {
            return redirect()
                ->route('inventario')
                ->with('error', 'Ocurrió un error al crear el producto: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Inventory $inventory)
    {
        //
    }

    private function aplicarActualizacionInventario(Request $request, $id)
    {
        $user = Auth::user();
        $inventory = Inventory::where('user_id', $user->id)->findOrFail($id);

        $datosActualizacion = [];
        $tienePrecio = false;
        $tieneCantidad = false;

        if ($request->filled('nombreProducto')) {
            $datosActualizacion['nombreProducto'] = $request->input('nombreProducto');
        }

        if ($request->filled('cantidad')) {
            $datosActualizacion['cantidad'] = $request->input('cantidad');
            $tieneCantidad = true;
        }

        if ($request->filled('precio')) {
            $datosActualizacion['precio'] = $request->input('precio');
            $tienePrecio = true;
        }

        if ($request->filled('unidad')) {
            $datosActualizacion['unidad'] = $request->input('unidad');
        }

        if ($request->filled('category_id')) {
            $datosActualizacion['category_id'] = $request->input('category_id');
        }

        if ($request->filled('observacion')) {
            $datosActualizacion['observacion'] = $request->input('observacion');
        }

        // Actualizar
        if (!empty($datosActualizacion)) {
            $inventory->update($datosActualizacion);
        }

        // Subcategorías
        if ($request->filled('subcategories')) {
            $inventory->subcategories()->sync($request->input('subcategories'));
        }

        // Historial SOLO si vienen precio Y cantidad
        if ($tienePrecio && $tieneCantidad) {
            HistorialCambios::create([
                'inventory_id' => $inventory->id,
                'user_id' => $user->id,
                'precio' => $request->input('precio'),
                'cantidad' => $request->input('cantidad'),
                'fecha_actualizacion' => $inventory->updated_at,
            ]);
        }

        return $inventory;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Inventory $inventory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $this->aplicarActualizacionInventario($request, $id);

            return redirect()
                ->route('inventario')
                ->with('success', 'Producto actualizado con éxito');
        } catch (\Exception $e) {
            return redirect()
                ->route('inventario')
                ->with('error', 'Ocurrió un error al guardar los cambios. ' . $e->getMessage())
                ->withInput();
        }
    }

    public function updateMultiple(Request $request)
    {
        $user = Auth::user();

        try {
            foreach ($request->productos as $productoData) {
                try {
                    $tempRequest = new Request($productoData);
                    $this->aplicarActualizacionInventario($tempRequest, $productoData['id']);
                } catch (\Exception $e) {
                    $errores[] = $productoData['id'];
                }
            }

            if (!empty($errores)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Algunos productos no pudieron actualizarse.',
                    'errores' => $errores
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update_observacion(Request $request, $id)
    {
        //
        $user = Auth::user();

        $request->validate([
            'observacion' => 'required|string|max:255',
        ]);

        try {

            $inventory = Inventory::where('user_id', $user->id)->findOrFail($id);
            $inventory->update([
                'observacion' => $request->input('observacion'),
            ]);
            
            return redirect()
                ->route('inventario')
                ->with('success', 'Producto actualizado con exito');
                
        } catch (\Exception $e) {
            return redirect()
                ->route('inventario')
                ->with('error', 'Ocurrio un error al guardar los cambios. ' . $e->getMessage())
                ->withInput();
        }
    }

    public function cotizador()
    {
        $user = Auth::user();

        $productos = Inventory::with('subcategories')
            ->where('user_id', $user->id)
            ->where('archivada', false)
            ->where('unidad','unit')
            ->where('cantidad', '<', 5)
            ->get();

        $response = Http::withoutVerifying()->get('https://api-inventario-morning-paper-8617.fly.dev/api/productos/');
        $url = 'https://api-inventario-morning-paper-8617.fly.dev/producto/';

        $productosProveedor = collect($response->json() ?? []);

        $page = request()->get('page', 1);
        $perPage = 5;

        $paginados = new LengthAwarePaginator(
            $productosProveedor->forPage($page, $perPage),
            $productosProveedor->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('Inventario.cotizador', compact('productos', 'paginados', 'url'));
    }

    public function addPedido(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nameProduct' => 'required|string|max:255',
            'priceProduct' => 'required|numeric',
            'descriptionProduct' => 'nullable|string',
            'urlProduct' => 'required|url',
        ]);

        try {

            Pedido::create(
                [
                    'user_id' => $user->id,
                    'nameProduct' => $request->nameProduct,
                    'priceProduct' => $request->priceProduct,
                    'descriptionProduct'=> $request->descriptionProduct,
                    'urlProduct' => $request->urlProduct,
                ]
            );
            
            return redirect()
                ->route('inventario')
                ->with('success', 'Producto guardado');
                
        } catch (\Exception $e) {
            return redirect()
                ->route('inventario')
                ->with('error', 'Ocurrio un error al guardar. ' . $e->getMessage())
                ->withInput();
        }
    }

    public function verPedidos()
    {
        $user = Auth::user();

        $pedidos = Pedido::where('user_id', $user->id)->get();

        $productos = Inventory::where('user_id', $user->id)
            ->where('archivada', false)
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->where('unidad', 'unit')->where('cantidad', '<', 5);
                })->orWhere(function ($q) {
                    $q->whereIn('unidad', ['gr', 'ml'])->where('cantidad', '<', 20);
                })->orWhere(function ($q) {
                    $q->whereIn('unidad', ['kg', 'l'])->where('cantidad', '<', 3);
                });
            })
            ->get();

        // Generar sugerencias
        $sugerencias = [];

        foreach ($pedidos as $pedido) {
            $nombrePedido = strtolower(trim($pedido->nameProduct));

            // Buscar todas las coincidencias posibles
            $coincidencias = $productos->map(function ($producto) use ($nombrePedido) {
                $nombreProducto = strtolower(trim($producto->nombreProducto));
                $distancia = levenshtein($nombreProducto, $nombrePedido);

                return [
                    'producto' => $producto,
                    'distancia' => $distancia,
                ];
            });

            // Ordenar por menor distancia (mas parecido)
            $mejorCoincidencia = $coincidencias->sortBy('distancia')->first();

            // Guardar solo el producto como sugerencia
            $sugerencias[$pedido->id] = $mejorCoincidencia['producto'] ?? null;
        }


        return view('inventario.pedidos', compact('pedidos', 'productos', 'sugerencias'));
    }

    public function deletePedido(Request $request, $id)
    {
        $user = Auth::user();
        $pedido = Pedido::where('user_id', $user->id)->findOrFail($id);

        // Si se envian datos de producto para actualizar inventario
        if ($request->filled('producto_id') && ($request->filled('precio') || $request->filled('cantidad'))) {
            $producto = Inventory::find($request->input('producto_id'));

            $precio_unitario = $request->input('precio');
            $cantidad = $request->input('cantidad');

            // Calcular Precio
            $precio_total = $precio_unitario * $cantidad;

            $request->merge(['precio' => $precio_total]);

            $this->aplicarActualizacionInventario($request, $producto->id);

            $mensaje = 'Pedido confirmado, inventario actualizado y eliminado correctamente.';
        } else {
            $mensaje = 'Pedido eliminado correctamente.';
        }

        $pedido->delete();

        return redirect()->route('inventario')->with('success', $mensaje);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id = null)
    {
        $user = Auth::user();

        try {
            //eliminar varios
            if ($request->filled('ids')) {
                $ids = $request->input('ids');

                Inventory::where('user_id', $user->id)
                    ->whereIn('id', $ids)
                    ->delete();

                return redirect()
                    ->route('inventario')
                    ->with('success', 'Productos eliminados correctamente.');
            }

            // Eliminar Solo uno
            if ($id) {
                $producto = Inventory::where('user_id', $user->id)->findOrFail($id);
                $producto->delete();

                return redirect()
                    ->route('inventario')
                    ->with('success', 'Producto eliminado correctamente.');
            }

            //Si no viene nada
            return redirect()
                ->route('inventario')
                ->with('error', 'No se especificaron productos para eliminar.');

        } catch (\Exception $e) {
            return redirect()
                ->route('inventario')
                ->with('error', 'Error al eliminar productos: ' . $e->getMessage());
        }
    }

}
