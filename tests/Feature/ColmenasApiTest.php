<?php

use App\Models\User;
use App\Models\Apiario;
use App\Models\Colmena;
use App\Models\Region;
use App\Models\Comuna;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\{getJson, postJson, putJson, deleteJson};

uses(RefreshDatabase::class);

// Semilla mínima de regiones/comunas para los tests
beforeEach(function () {
    seedRegionesComunas(); // ← misma helper que usaste en ApiariosApiTest
});
// helpers locales
function apiUrl(string $name, array $params = []): string {
    return route($name, $params, false);
}

it('crea colmena (201) en un apiario y aparece en el index', function () {
    Sanctum::actingAs(User::factory()->create());

    // Crea un apiario por API
    $apiario = postJson(apiUrl('api.apiarios.store'), [
        'nombre'=>'Apiario X','tipo'=>'fijo','region'=>'Maule','comuna'=>'Colbún','colmenas_iniciales'=>0
    ], ['Idempotency-Key'=>'col-crear-ap-1'])->assertCreated()->json('data');

    // Crea colmena por API (ajusta campos a tu StoreColmenaRequest)
    $res = postJson(apiUrl('api.colmenas.store'), [
        'apiario_id'    => $apiario['id'],
        'numero' => 'C-001',
        // 'color' => '#FFD700',   // si tu request lo exige
    ], ['Idempotency-Key'=>'col-crear-1']);

    $res->assertCreated()->assertJsonPath('data.numero','C-001');

    // index
    getJson(apiUrl('api.colmenas.index'))
        ->assertOk()
        ->assertJsonFragment(['numero'=>'C-001']);
});

it('actualiza colmena (200)', function () {
    Sanctum::actingAs(User::factory()->create());

    $ap = postJson(apiUrl('api.apiarios.store'), [
        'nombre'=>'Apiario Y','tipo'=>'fijo','region'=>'Maule','comuna'=>'Colbún','colmenas_iniciales'=>0
    ], ['Idempotency-Key'=>'col-up-ap'])->assertCreated()->json('data');

    $c = postJson(apiUrl('api.colmenas.store'), [
        'apiario_id'=>$ap['id'], 'numero'=>'C-010'
    ], ['Idempotency-Key'=>'col-up-1'])->assertCreated()->json('data');

    $upd = putJson(apiUrl('api.colmenas.update', ['colmena'=>$c['id']]), [
        'numero'=>'C-011'
    ])->assertOk();

    $upd->assertJsonPath('data.numero','C-011');
});

it('elimina colmena (200)', function () {
    Sanctum::actingAs(User::factory()->create());

    $ap = postJson(apiUrl('api.apiarios.store'), [
        'nombre'=>'Apiario Z','tipo'=>'fijo','region'=>'Maule','comuna'=>'Colbún','colmenas_iniciales'=>0
    ], ['Idempotency-Key'=>'col-del-ap'])->assertCreated()->json('data');

    $c = postJson(apiUrl('api.colmenas.store'), [
        'apiario_id'=>$ap['id'], 'numero'=>'C-100'
    ], ['Idempotency-Key'=>'col-del-1'])->assertCreated()->json('data');

    deleteJson(apiUrl('api.colmenas.destroy', ['colmena'=>$c['id']]))
        ->assertOk()
        ->assertJsonPath('ok', true);
});

it('requiere auth para store/update/destroy (401)', function () {

    // Prepara datos directos en BD (sin pasar por API)
    $owner = User::factory()->create();
    $apiario = Apiario::query()->forceCreate([
        'user_id'=>$owner->id, 'nombre'=>'Tmp', 'tipo_apiario'=>'fijo', 'region_id'=>7, 'comuna_id'=>430, 'activo'=>true, 'temporada_produccion'=>now()->year,
    ]);
    $colmena = Colmena::query()->forceCreate([
        'apiario_id'=>$apiario->id, 'numero'=>'C-NA'
    ]);

    postJson(apiUrl('api.colmenas.store'), [])->assertStatus(401);
    putJson(apiUrl('api.colmenas.update', ['colmena'=>$colmena->id]), [])->assertStatus(401);
    deleteJson(apiUrl('api.colmenas.destroy', ['colmena'=>$colmena->id]))->assertStatus(401);
});
