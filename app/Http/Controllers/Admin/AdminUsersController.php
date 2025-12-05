<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminUsersController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['apiarios', 'datosFacturacion', 'facturas', 'boletas'])->withCount('apiarios');

        // Filtro por Plan
        if ($request->has('filter_plan') && $request->filter_plan != '') {
            if ($request->filter_plan === 'sin_plan') {
                $query->where(function($q) {
                    $q->whereNull('plan')->orWhere('plan', '');
                });
            } else {
                $query->where('plan', $request->filter_plan);
            }
        }

        // Filtro por Estado
        if ($request->has('filter_estado') && $request->filter_estado != '') {
            $ahora = Carbon::now();
            $estado = $request->filter_estado;

            switch ($estado) {
                case 'sin_plan':
                    $query->where(function($q) {
                        $q->whereNull('plan')->orWhere('plan', '');
                    });
                    break;

                case 'vencido':
                    $query->whereNotNull('plan')
                          ->where('plan', '!=', '')
                          ->whereNotNull('fecha_vencimiento')
                          ->where('fecha_vencimiento', '<', $ahora);
                    break;

                case 'proximo':
                    $query->whereNotNull('plan')
                          ->where('plan', '!=', '')
                          ->whereNotNull('fecha_vencimiento')
                          ->where('fecha_vencimiento', '>=', $ahora)
                          ->where('fecha_vencimiento', '<=', $ahora->copy()->addDays(7));
                    break;

                case 'activo':
                    $query->whereNotNull('plan')
                          ->where('plan', '!=', '')
                          ->whereNotNull('fecha_vencimiento')
                          ->where('fecha_vencimiento', '>', $ahora->copy()->addDays(7));
                    break;
            }
        }

        // Filtro por Rango de Fechas (fecha de registro)
        if ($request->has('fecha_desde') && $request->fecha_desde != '') {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        if ($request->has('fecha_hasta') && $request->fecha_hasta != '') {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        // Búsqueda avanzada
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $searchLower = strtolower(trim($search));

            // Verificar si es búsqueda por plan específico
            $planBuscado = null;
            if ($searchLower === 'afc' || $searchLower === 'AFC') {
                $planBuscado = 'afc';
            } elseif ($searchLower === 'me' || $searchLower === 'ME') {
                $planBuscado = 'me';
            } elseif ($searchLower === 'ge' || $searchLower === 'GE') {
                $planBuscado = 'ge';
            } elseif ($searchLower === 'drone' || $searchLower === 'Drone' || $searchLower === 'DRONE') {
                $planBuscado = 'drone';
            }

            // Verificar si es búsqueda por estado
            $estadoBuscado = null;
            if (str_contains($searchLower, 'sin plan')) {
                $estadoBuscado = 'sin_plan';
            } elseif (str_contains($searchLower, 'vencido')) {
                $estadoBuscado = 'vencido';
            } elseif (str_contains($searchLower, 'próximo') || str_contains($searchLower, 'proximo')) {
                $estadoBuscado = 'proximo';
            } elseif (str_contains($searchLower, 'activo') && !$planBuscado) {
                $estadoBuscado = 'activo';
            }

            if ($planBuscado) {
                // Búsqueda exacta por plan
                $query->where('plan', $planBuscado);
            } elseif ($estadoBuscado) {
                // Filtrar por estado del plan
                $query->where(function($q) use ($estadoBuscado) {
                    $ahora = Carbon::now();

                    switch ($estadoBuscado) {
                        case 'sin_plan':
                            $q->where(function($subQ) {
                                $subQ->whereNull('plan')
                                     ->orWhere('plan', '');
                            });
                            break;

                        case 'vencido':
                            $q->whereNotNull('plan')
                              ->where('plan', '!=', '')
                              ->whereNotNull('fecha_vencimiento')
                              ->where('fecha_vencimiento', '<', $ahora);
                            break;

                        case 'proximo':
                            $q->whereNotNull('plan')
                              ->where('plan', '!=', '')
                              ->whereNotNull('fecha_vencimiento')
                              ->where('fecha_vencimiento', '>=', $ahora)
                              ->where('fecha_vencimiento', '<=', $ahora->copy()->addDays(7));
                            break;

                        case 'activo':
                            $q->whereNotNull('plan')
                              ->where('plan', '!=', '')
                              ->whereNotNull('fecha_vencimiento')
                              ->where('fecha_vencimiento', '>', $ahora->copy()->addDays(7));
                            break;
                    }
                });
            } else {
                // Búsqueda normal en campos de texto (excluyendo plan)
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('email', 'like', '%' . $search . '%')
                      ->orWhere('rut', 'like', '%' . $search . '%')
                      ->orWhere('telefono', 'like', '%' . $search . '%')
                      ->orWhere('direccion', 'like', '%' . $search . '%');
                });
            }
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function show($id)
    {
        $user = User::with(['apiarios.colmenas', 'apiarios.comuna.region'])
            ->withCount(['apiarios', 'apiarios as colmenas_count' => function($query) {
                $query->join('colmenas', 'apiarios.id', '=', 'colmenas.apiario_id')
                    ->whereNull('colmenas.deleted_at');
            }])
            ->findOrFail($id);

        return view('admin.users.show', compact('user'));
    }

    public function billing($id)
    {
        // Cache key único por usuario
        $cacheKey = "user_billing_{$id}";

        // Intentar obtener del cache (10 minutos)
        $user = \Cache::remember($cacheKey, 600, function() use ($id) {
            return User::with(['datosFacturacion.region', 'datosFacturacion.comuna', 'facturas', 'boletas'])
                ->findOrFail($id);
        });

        // Combinar facturas y boletas en un solo array con tipo
        $documentos = collect();

        if ($user->facturas) {
            foreach ($user->facturas as $factura) {
                $factura->tipo_documento = 'Factura';
                $documentos->push($factura);
            }
        }

        if ($user->boletas) {
            foreach ($user->boletas as $boleta) {
                $boleta->tipo_documento = 'Boleta';
                $documentos->push($boleta);
            }
        }

        // Ordenar por fecha de emisión descendente
        $user->documentos = $documentos->sortByDesc('fecha_emision')->values();

        return view('admin.users.billing', compact('user'));
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Verificar que no sea el último admin (si tiene rol)
        if (isset($user->role) && $user->role === 'admin') {
            $adminCount = User::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                return redirect()->route('admin.users.index')
                    ->with('error', 'No se puede eliminar el último administrador del sistema.');
            }
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }

    public function assignPlan(Request $request, $id)
    {
        $request->validate([
            'plan' => 'required|in:afc,me,ge,drone',
            'years' => 'nullable|integer|min:0',
            'months' => 'nullable|integer|min:0',
            'weeks' => 'nullable|integer|min:0',
            'days' => 'nullable|integer|min:0'
        ]);

        $user = User::findOrFail($id);

        // Convertir a enteros
        $years = (int) ($request->years ?? 0);
        $months = (int) ($request->months ?? 0);
        $weeks = (int) ($request->weeks ?? 0);
        $days = (int) ($request->days ?? 0);

        // Validar que al menos una unidad de tiempo sea mayor a 0
        if ($years === 0 && $months === 0 && $weeks === 0 && $days === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Debes especificar al menos una duración'
            ], 400);
        }

        // Calcular fecha de vencimiento
        $fechaVencimiento = Carbon::now();

        if ($years > 0) {
            $fechaVencimiento->addYears($years);
        }
        if ($months > 0) {
            $fechaVencimiento->addMonths($months);
        }
        if ($weeks > 0) {
            $fechaVencimiento->addWeeks($weeks);
        }
        if ($days > 0) {
            $fechaVencimiento->addDays($days);
        }

        // Actualizar usuario
        $user->plan = $request->plan;
        $user->fecha_vencimiento = $fechaVencimiento;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Plan asignado correctamente',
            'fecha_vencimiento' => $fechaVencimiento->format('d/m/Y')
        ]);
    }

    public function storeFactura(Request $request, $userId)
    {
        $request->validate([
            'plan' => 'required|in:afc,me,ge,drone',
            'monto_neto' => 'required|numeric|min:0',
            'duracion_meses' => 'required|integer|min:1|max:12',
            'fecha_emision' => 'required|date',
            'estado' => 'required|in:emitida,pagada,pendiente',
            'actualizar_plan' => 'nullable|boolean',
            'tipo_documento' => 'required|in:factura,boleta'
        ]);

        $user = User::findOrFail($userId);

        // Determinar si es factura o boleta
        $tieneFacturacion = $user->datosFacturacion &&
                          $user->datosFacturacion->razon_social &&
                          $user->datosFacturacion->rut;
        $esFactura = $request->tipo_documento === 'factura' && $tieneFacturacion;

        // Calcular montos
        $montoNeto = (int) $request->monto_neto;
        $montoIva = (int) round($montoNeto * 0.19);
        $montoTotal = $montoNeto + $montoIva;

        // Calcular fecha de vencimiento (1 mes = 30 días)
        $duracionMeses = (int) $request->duracion_meses;
        $duracionDias = $duracionMeses * 30;
        $fechaEmision = Carbon::parse($request->fecha_emision);
        $fechaVencimiento = $fechaEmision->copy()->addDays($duracionDias);

        // Crear Payment
        $payment = \App\Models\Payment::create([
            'user_id' => $user->id,
            'transaction_id' => 'ADMIN-' . time(),
            'status' => $request->estado === 'pagada' ? 'paid' : 'pending',
            'amount' => $montoTotal,
            'plan' => $request->plan,
            'expires_at' => $fechaVencimiento,
        ]);

        if ($esFactura) {
            // Crear Factura
            $ultimaFactura = \App\Models\Factura::orderBy('id', 'desc')->first();
            $nuevoNumero = $ultimaFactura ? $ultimaFactura->id + 1 : 1;
            $numeroDocumento = 'F-' . date('Y') . '-' . str_pad($nuevoNumero, 6, '0', STR_PAD_LEFT);

            $documento = \App\Models\Factura::create([
                'user_id' => $user->id,
                'payment_id' => $payment->id,
                'numero' => $numeroDocumento,
                'estado' => $request->estado,
                'monto_neto' => $montoNeto,
                'monto_iva' => $montoIva,
                'monto_total' => $montoTotal,
                'porcentaje_iva' => 19,
                'moneda' => 'CLP',
                'fecha_emision' => $fechaEmision,
                'fecha_vencimiento' => $fechaVencimiento,
                'plan' => $request->plan,
                'datos_facturacion_snapshot' => [
                    'razon_social' => $user->datosFacturacion->razon_social,
                    'rut' => $user->datosFacturacion->rut,
                    'giro' => $user->datosFacturacion->giro,
                    'direccion_comercial' => $user->datosFacturacion->direccion_comercial,
                    'ciudad' => $user->datosFacturacion->ciudad,
                    'correo' => $user->datosFacturacion->correo,
                ],
            ]);
            $tipoDocumento = 'Factura';
        } else {
            // Crear Boleta
            $ultimaBoleta = \App\Models\Boleta::orderBy('id', 'desc')->first();
            $nuevoNumero = $ultimaBoleta ? $ultimaBoleta->id + 1 : 1;
            $numeroDocumento = 'B-' . date('Y') . '-' . str_pad($nuevoNumero, 6, '0', STR_PAD_LEFT);

            $documento = \App\Models\Boleta::create([
                'user_id' => $user->id,
                'payment_id' => $payment->id,
                'numero' => $numeroDocumento,
                'estado' => $request->estado,
                'monto_neto' => $montoNeto,
                'monto_iva' => $montoIva,
                'monto_total' => $montoTotal,
                'porcentaje_iva' => 19,
                'moneda' => 'CLP',
                'fecha_emision' => $fechaEmision,
                'fecha_vencimiento' => $fechaVencimiento,
                'plan' => $request->plan,
                'datos_usuario_snapshot' => [
                    'nombre' => $user->name,
                    'apellido' => $user->last_name,
                    'rut' => $user->rut,
                    'correo' => $user->email,
                    'telefono' => $user->telefono,
                    'direccion' => $user->direccion,
                ],
            ]);
            $tipoDocumento = 'Boleta';
        }

        // Actualizar plan del usuario si está marcado
        if ($request->actualizar_plan) {
            $user->plan = $request->plan;
            $user->fecha_vencimiento = $fechaVencimiento;
            $user->estado_usuario = 'activo';
            $user->webpay_status = $request->estado === 'pagada' ? 'paid' : 'pending';
            $user->save();
        }

        // Limpiar cache del usuario
        \Cache::forget("user_billing_{$userId}");

        return redirect()
            ->route('admin.users.billing', $userId)
            ->with('success', "$tipoDocumento {$numeroDocumento} emitida correctamente. Monto: $" . number_format($montoTotal, 0, ',', '.'));
    }
}
