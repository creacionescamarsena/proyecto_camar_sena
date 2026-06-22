<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminCrudDatabaseTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        DB::statement('PRAGMA foreign_keys=off');

        DB::statement('CREATE TABLE categoria (id_categoria INTEGER PRIMARY KEY AUTOINCREMENT, tipo_categoria TEXT NOT NULL, estado_categoria INTEGER NOT NULL DEFAULT 1)');
        DB::statement('CREATE TABLE proveedor_material (cod_proveedor INTEGER PRIMARY KEY AUTOINCREMENT, proveedor_material TEXT NOT NULL)');
        DB::statement('CREATE TABLE materiales (id_materiales INTEGER PRIMARY KEY AUTOINCREMENT, material TEXT NOT NULL, proveedor_material_cod_proveedor INTEGER NULL, precio REAL NOT NULL DEFAULT 0, cantidad INTEGER NOT NULL DEFAULT 0)');
        DB::statement('CREATE TABLE chaqueta (id_chaqueta INTEGER PRIMARY KEY AUTOINCREMENT, modelo_chaqueta TEXT NOT NULL, precio REAL NOT NULL DEFAULT 0, categoria_id_categoria INTEGER NULL)');
        DB::statement('CREATE TABLE chaqueta_has_materiales (chaqueta_id_chaqueta INTEGER NOT NULL, materiales_id_materiales INTEGER NOT NULL, PRIMARY KEY (chaqueta_id_chaqueta, materiales_id_materiales))');

        DB::statement('PRAGMA foreign_keys=on');
    }

    protected function tearDown(): void
    {
        DB::statement('DROP TABLE IF EXISTS chaqueta_has_materiales');
        DB::statement('DROP TABLE IF EXISTS chaqueta');
        DB::statement('DROP TABLE IF EXISTS materiales');
        DB::statement('DROP TABLE IF EXISTS proveedor_material');
        DB::statement('DROP TABLE IF EXISTS categoria');

        parent::tearDown();
    }

    public function test_admin_materiales_crud_uses_database(): void
    {
        DB::table('proveedor_material')->insert(['proveedor_material' => 'Textil Norte']);
        $materialId = DB::table('materiales')->insertGetId([
            'material' => 'Tela',
            'proveedor_material_cod_proveedor' => 1,
            'precio' => 1200,
            'cantidad' => 25,
        ]);

        $this->get('/admin/materiales')
            ->assertStatus(200)
            ->assertSee('Tela')
            ->assertSee('Textil Norte');

        $this->post('/admin/materiales', [
            'nombre' => 'Botón',
            'proveedor' => 'Proveedor Nuevo',
            'precio' => 500,
            'cantidad' => 10,
        ])->assertRedirect(route('admin.materiales.index'));

        $this->assertDatabaseHas('materiales', ['material' => 'Botón']);

        $this->put("/admin/materiales/{$materialId}", [
            'nombre' => 'Tela actualizada',
            'proveedor' => 'Textil Norte',
            'precio' => 1500,
            'cantidad' => 30,
        ])->assertRedirect(route('admin.materiales.index'));

        $this->assertDatabaseHas('materiales', ['id_materiales' => $materialId, 'material' => 'Tela actualizada']);

        $this->delete("/admin/materiales/{$materialId}")
            ->assertRedirect(route('admin.materiales.index'));

        $this->assertDatabaseMissing('materiales', ['id_materiales' => $materialId]);
    }

    public function test_admin_productos_page_reads_from_database(): void
    {
        DB::table('categoria')->insert(['tipo_categoria' => 'Casual', 'estado_categoria' => 1]);
        DB::table('proveedor_material')->insert(['proveedor_material' => 'Textil Sur']);
        DB::table('materiales')->insert([
            'material' => 'Algodón',
            'proveedor_material_cod_proveedor' => 1,
            'precio' => 300,
            'cantidad' => 40,
        ]);
        $productoId = DB::table('chaqueta')->insertGetId([
            'modelo_chaqueta' => 'Chaqueta prueba',
            'precio' => 95000,
            'categoria_id_categoria' => 1,
        ]);
        DB::table('chaqueta_has_materiales')->insert([
            'chaqueta_id_chaqueta' => $productoId,
            'materiales_id_materiales' => 1,
        ]);

        $this->get('/admin/productos')
            ->assertStatus(200)
            ->assertSee('Chaqueta prueba')
            ->assertSee('Algodón');
    }
}
