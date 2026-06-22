<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ProductCrudTest extends TestCase
{
    public function test_admin_product_crud_pages_render_and_store_work(): void
    {
        DB::statement('CREATE TABLE categoria (id_categoria INTEGER PRIMARY KEY AUTOINCREMENT, tipo_categoria TEXT NOT NULL, estado_categoria INTEGER NOT NULL DEFAULT 1)');
        DB::statement('CREATE TABLE chaqueta (id_chaqueta INTEGER PRIMARY KEY AUTOINCREMENT, modelo_chaqueta TEXT NOT NULL, precio REAL NOT NULL DEFAULT 0, categoria_id_categoria INTEGER NULL)');
        DB::statement('CREATE TABLE materiales (id_materiales INTEGER PRIMARY KEY AUTOINCREMENT, material TEXT NOT NULL, proveedor_material_cod_proveedor INTEGER NULL, precio REAL NOT NULL DEFAULT 0, cantidad INTEGER NOT NULL DEFAULT 0)');
        DB::statement('CREATE TABLE chaqueta_has_materiales (chaqueta_id_chaqueta INTEGER NOT NULL, materiales_id_materiales INTEGER NOT NULL, PRIMARY KEY (chaqueta_id_chaqueta, materiales_id_materiales))');

        DB::table('categoria')->insert(['tipo_categoria' => 'Casual', 'estado_categoria' => 1]);

        $this->get('/admin/productos/create')->assertStatus(200);
        $this->get('/admin/productos/1/edit')->assertStatus(200);

        $this->post('/admin/productos', [
            'nombre' => 'Chaqueta test CRUD',
            'categoria' => 'Casual',
            'precio' => 99.99,
            'descripcion' => 'Descrip',
        ])->assertRedirect(route('admin.productos'));

        $this->assertDatabaseHas('chaqueta', ['modelo_chaqueta' => 'Chaqueta test CRUD']);

        DB::statement('DROP TABLE IF EXISTS chaqueta_has_materiales');
        DB::statement('DROP TABLE IF EXISTS chaqueta');
        DB::statement('DROP TABLE IF EXISTS materiales');
        DB::statement('DROP TABLE IF EXISTS categoria');
    }
}
