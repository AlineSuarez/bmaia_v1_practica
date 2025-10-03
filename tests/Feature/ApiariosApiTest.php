<?php

use App\Models\User;
use App\Models\Apiario;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

// ✅ Helpers de Pest-Laravel (en lugar de $this->postJson etc.)
use function Pest\Laravel\{getJson, postJson, putJson, deleteJson};

uses(RefreshDatabase::class);
// ---- helpers locales ----
function seedRegionesComunas(): void {
    // --- Región Maule (id=7) ---
    $region = [
        'id'     => 7,
        'nombre' => 'Maule',
    ];
    if (Schema::hasColumn('regiones', 'abreviatura')) $region['abreviatura'] = 'MA';
    if (Schema::hasColumn('regiones', 'romano'))      $region['romano']      = 'VII';
    if (Schema::hasColumn('regiones', 'codigo'))      $region['codigo']      = 7;

    DB::table('regiones')->updateOrInsert(['id' => 7], $region);

    // --- Comuna Colbún (id=430) ---
    $colbun = [
        'id'        => 430,
        'nombre'    => 'Colbún',
        'region_id' => 7,
    ];
    if (Schema::hasColumn('comunas', 'lat')) $colbun['lat'] = -35.706;
    if (Schema::hasColumn('comunas', 'lon')) $colbun['lon'] = -71.410;

    DB::table('comunas')->updateOrInsert(['id' => 430], $colbun);

    // --- Comuna Rauco (id=431) ---
    $rauco = [
        'id'        => 431,
        'nombre'    => 'Rauco',
        'region_id' => 7,
    ];
    if (Schema::hasColumn('comunas', 'lat')) $rauco['lat'] = -34.600;
    if (Schema::hasColumn('comunas', 'lon')) $rauco['lon'] = -71.200;

    DB::table('comunas')->updateOrInsert(['id' => 431], $rauco);
}


function api(string $name, array $params = []): string {
    return route($name, $params, false);           // p.ej. api('api.apiarios.store')
}

// ---- tests ----

it('crea apiario (201) y devuelve nombre', function () {
    seedRegionesComunas();
    Sanctum::actingAs(User::factory()->create());

    $res = postJson(api('api.apiarios.store'), [
        'nombre' => 'Las Palmas',
        'tipo_apiario'   => 'fijo',
        'region' => 'Maule',
        'comuna' => 'Colbún',
        'colmenas_iniciales' => 5,
    ], ['Idempotency-Key' => 'pest-crear-001']);

    $res->assertCreated()
        ->assertJsonPath('data.nombre', 'Las Palmas');
});

it('es idempotente al repetir el mismo Idempotency-Key', function () {
    seedRegionesComunas();
    Sanctum::actingAs(User::factory()->create());

    $payload = [
        'nombre' => 'Idempo',
        'tipo_apiario'   => 'fijo',
        'region' => 'Maule',
        'comuna' => 'Colbún',
        'colmenas_iniciales' => 3,
    ];
    $h = ['Idempotency-Key' => 'pest-crear-002'];

    $a = postJson(api('api.apiarios.store'), $payload, $h)->assertCreated();
    $b = postJson(api('api.apiarios.store'), $payload, $h)->assertCreated();

    expect($b->json('data.id'))->toBe($a->json('data.id'));
});

it('actualiza apiario (200)', function () {
    seedRegionesComunas();
    Sanctum::actingAs(User::factory()->create());

    // crea primero
    $create = postJson(api('api.apiarios.store'), [
        'nombre' => 'Base',
        'tipo_apiario'   => 'fijo',
        'region' => 'Maule',
        'comuna' => 'Colbún',
        'colmenas_iniciales' => 1,
    ], ['Idempotency-Key' => 'pest-crear-003'])->assertCreated();

    $id = $create->json('data.id');

    // update: cambia nombre y comuna
    $upd = putJson(api('api.apiarios.update', ['apiario' => $id]), [
        'nombre' => 'Base Editado',
        'tipo_apiario'   => 'fijo',
        'region' => 'Maule',
        'comuna' => 'Rauco',
    ])->assertOk();

    $upd->assertJsonPath('data.nombre', 'Base Editado');

    // index debe reflejar el cambio
    getJson(api('api.apiarios.index'))
        ->assertOk()
        ->assertJsonFragment(['nombre' => 'Base Editado']);
});

it('elimina (desactiva) apiario (200)', function () {
    seedRegionesComunas();
    Sanctum::actingAs(User::factory()->create());

    $create = postJson(api('api.apiarios.store'), [
        'nombre' => 'A borrar',
        'tipo_apiario'   => 'fijo',
        'region' => 'Maule',
        'comuna' => 'Colbún',
        'colmenas_iniciales' => 1,
    ], ['Idempotency-Key' => 'pest-crear-004'])->assertCreated();

    $id = $create->json('data.id');

    deleteJson(api('api.apiarios.destroy', ['apiario' => $id]))
        ->assertOk()
        ->assertJsonPath('ok', true);

    // Tu destroy marca activo=false (no SoftDeletes)
    expect(Apiario::find($id)->activo)->toBeFalse();
});

it('requiere auth para store (401)', function () {
    seedRegionesComunas();
    postJson(api('api.apiarios.store'), [])->assertStatus(401);
});

it('requiere auth para update/destroy (401)', function () {
    seedRegionesComunas();

    // Creamos un apiario directamente en BD (sin pasar por API)
    $owner = User::factory()->create();
    $apiario = Apiario::query()->forceCreate([
    'user_id'   => $owner->id,
    'nombre'    => 'Tmp',
    'tipo_apiario'      => 'fijo',
    'region_id' => 7,
    'comuna_id' => 430,
    'activo'    => true,
]);

    putJson(api('api.apiarios.update', ['apiario' => $apiario->id]), [])
        ->assertStatus(401);

    deleteJson(api('api.apiarios.destroy', ['apiario' => $apiario->id]))
        ->assertStatus(401);
});

it('filtra por tipo y activo en index', function () {
    seedRegionesComunas();
    Sanctum::actingAs(User::factory()->create());

    // Crea 2 apiarios fijos activos
    postJson(api('api.apiarios.store'), [
        'nombre'=>'Fijo A','tipo'=>'fijo','region'=>'Maule','comuna'=>'Colbún','colmenas_iniciales'=>1,
    ], ['Idempotency-Key'=>'pest-filtro-1'])->assertCreated();

    postJson(api('api.apiarios.store'), [
        'nombre'=>'Fijo B','tipo'=>'fijo','region'=>'Maule','comuna'=>'Colbún','colmenas_iniciales'=>1,
    ], ['Idempotency-Key'=>'pest-filtro-2'])->assertCreated();

    // Crea 1 trashumante y luego lo desactiva (usa tu destroy que hace activo=false)
    $t = postJson(api('api.apiarios.store'), [
        'nombre'=>'Trashu X','tipo'=>'trashumante','region'=>'Maule','comuna'=>'Colbún','colmenas_iniciales'=>1,
    ], ['Idempotency-Key'=>'pest-filtro-3'])->assertCreated();

    $idT = $t->json('data.id');
    deleteJson(api('api.apiarios.destroy', ['apiario'=>$idT]))->assertOk();

    // Filtro: ?tipo=fijo  -> NO debe mostrar Trashu X
    getJson(api('api.apiarios.index').'?tipo=fijo')
        ->assertOk()
        ->assertJsonFragment(['nombre'=>'Fijo A'])
        ->assertJsonFragment(['nombre'=>'Fijo B'])
        ->assertJsonMissing(['nombre'=>'Trashu X']);

    // Filtro: ?activo=1 -> NO debe mostrar Trashu X (porque quedó inactivo)
    getJson(api('api.apiarios.index').'?activo=1')
        ->assertOk()
        ->assertJsonMissing(['nombre'=>'Trashu X']);
});

it('show incluye colmenas_count (sin factory)', function () {
    seedRegionesComunas();
    Sanctum::actingAs(User::factory()->create());

    $create = postJson(api('api.apiarios.store'), [
        'nombre'=>'Con Colmenas','tipo'=>'fijo','region'=>'Maule','comuna'=>'Colbún','colmenas_iniciales'=>0,
    ], ['Idempotency-Key'=>'pest-show-2'])->assertCreated();

    $id = $create->json('data.id');

    // crea 2 colmenas directo (ajusta campos si tu modelo los requiere)
    \App\Models\Colmena::query()->forceCreate(['apiario_id'=>$id, 'numero'=>1]);
    \App\Models\Colmena::query()->forceCreate(['apiario_id'=>$id, 'numero'=>2]);

    getJson(api('api.apiarios.show', ['apiario'=>$id]))
        ->assertOk()
        ->assertJsonPath('data.colmenas_count', 2);
});