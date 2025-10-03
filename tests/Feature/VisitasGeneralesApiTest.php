<?php

use App\Models\User;
use App\Models\Apiario;
use App\Models\Visita;
use App\Models\Region;
use App\Models\Comuna;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\{postJson, putJson, deleteJson};

uses(RefreshDatabase::class);

// Semilla mínima estable para Región/Comuna con ids fijos que usas en los tests
function seedRCV(): void {
    // Crear Región(7) ANTES que Comuna(430) y forzar asignación de 'id'
    if (!Region::query()->where('id', 7)->exists()) {
        Region::unguard();
        Region::create(['id' => 7, 'nombre' => 'Maule']);
        Region::reguard();
    }

    if (!Comuna::query()->where('id', 430)->exists()) {
        Comuna::unguard();
        Comuna::create(['id' => 430, 'nombre' => 'Colbún', 'region_id' => 7]);
        Comuna::reguard();
    }
}

// Garantiza las FKs en cada test
beforeEach(function () {
    seedRCV();
});

function apiV(string $name, array $params = []): string {
    return route($name, $params, false);
}

it('crea visita (201) con payload mínimo', function () {
    Sanctum::actingAs(User::factory()->create());

    // Crea apiario por API (usa nombres de región/comuna que el endpoint mapea)
    $ap = postJson(apiV('api.apiarios.store'), [
        'nombre' => 'Apiario V',
        'tipo'   => 'fijo',
        'region' => 'Maule',
        'comuna' => 'Colbún',
        'colmenas_iniciales' => 0,
    ], ['Idempotency-Key' => 'vis-ap-1'])->assertCreated()->json('data');

    // Usa el payload que tu endpoint acepta (si tu StoreVisitaRequest espera 'fecha', lo dejamos)
    $payload = [
        'apiario_id'    => $ap['id'],
        'fecha'         => now()->toDateString(),
        'objetivo'      => 'Inspección general',
        'observaciones' => 'Sin novedades',
    ];

    postJson(apiV('api.visitas.store'), $payload, ['Idempotency-Key' => 'vis-crear-1'])
        ->assertCreated();
});

it('actualiza visita (200)', function () {
    Sanctum::actingAs(User::factory()->create());

    $ap = postJson(apiV('api.apiarios.store'), [
        'nombre' => 'Apiario V2',
        'tipo'   => 'fijo',
        'region' => 'Maule',
        'comuna' => 'Colbún',
        'colmenas_iniciales' => 0,
    ], ['Idempotency-Key' => 'vis-ap-2'])->assertCreated()->json('data');

    // Inserta directo con la COLUMNA REAL de la tabla: 'fecha_visita'
    $v = Visita::query()->forceCreate([
        'apiario_id'    => $ap['id'],
        'fecha_visita'  => now()->toDateString(),
        'observaciones' => 'Inicial',
    ]);

    putJson(apiV('api.visitas.update', ['visita' => $v->id]), [
        'observaciones' => 'Editado',
    ])
        ->assertOk()
        ->assertJsonPath('data.observaciones', 'Editado');
});

it('elimina visita (200)', function () {
    Sanctum::actingAs(User::factory()->create());

    $ap = postJson(apiV('api.apiarios.store'), [
        'nombre' => 'Apiario V3',
        'tipo'   => 'fijo',
        'region' => 'Maule',
        'comuna' => 'Colbún',
        'colmenas_iniciales' => 0,
    ], ['Idempotency-Key' => 'vis-ap-3'])->assertCreated()->json('data');

    $v = Visita::query()->forceCreate([
        'apiario_id'    => $ap['id'],
        'fecha_visita'  => now()->toDateString(),
        'observaciones' => 'tmp',
    ]);

    deleteJson(apiV('api.visitas.destroy', ['visita' => $v->id]))
        ->assertOk()
        ->assertJsonPath('ok', true);
});

it('requiere auth (401) en store/update/destroy', function () {
    // store sin auth → 401
    postJson(apiV('api.visitas.store'), [])->assertStatus(401);

    // Prepara datos directos en BD con FKs válidas
    $owner = User::factory()->create();

    $ap = Apiario::query()->forceCreate([
        'user_id'   => $owner->id,
        'nombre'    => 'Apiario V4',
        'region_id' => 7,    // existen por seedRCV()
        'comuna_id' => 430,  // existen por seedRCV()
        'activo'    => true,
        'temporada_produccion' => now()->year,
    ]);

    $v = Visita::query()->forceCreate([
        'apiario_id'    => $ap->id,
        'fecha_visita'  => now()->toDateString(),
        'observaciones' => 'tmp',
    ]);

    // Update y destroy sin auth → 401
    putJson(apiV('api.visitas.update', ['visita' => $v->id]), [])->assertStatus(401);
    deleteJson(apiV('api.visitas.destroy', ['visita' => $v->id]))->assertStatus(401);
});
