<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DashboardCatalogDataTest extends TestCase
{
    public function test_inventario_uses_real_stock_data(): void
    {
        DB::statement('CREATE TABLE categoria (id_categoria INTEGER PRIMARY KEY AUTOINCREMENT, tipo_categoria TEXT NOT NULL, estado_categoria INTEGER NOT NULL DEFAULT 1)');
        DB::statement('CREATE TABLE chaqueta (id_chaqueta INTEGER PRIMARY KEY AUTOINCREMENT, modelo_chaqueta TEXT NOT NULL, precio REAL NOT NULL DEFAULT 0, categoria_id_categoria INTEGER NULL)');
        DB::statement('CREATE TABLE stock (cod_stock INTEGER PRIMARY KEY AUTOINCREMENT, talla TEXT NOT NULL, chaqueta_id_chaqueta INTEGER NULL)');

        DB::table('categoria')->insert(['tipo_categoria' => 'Casual', 'estado_categoria' => 1]);
        $productoId = DB::table('chaqueta')->insertGetId([
            'modelo_chaqueta' => 'Chaqueta inventario',
            'precio' => 150,
            'categoria_id_categoria' => 1,
        ]);
        DB::table('stock')->insert([
            'talla' => 'S',
            'chaqueta_id_chaqueta' => $productoId,
        ]);
        DB::table('stock')->insert([
            'talla' => 'M',
            'chaqueta_id_chaqueta' => $productoId,
        ]);
        for ($i = 0; $i < 19; $i++) {
            DB::table('stock')->insert([
                'talla' => 'L',
                'chaqueta_id_chaqueta' => $productoId,
            ]);
        }

        $response = $this->get('/admin/inventario');

        $response->assertStatus(200);
        $response->assertSee('Chaqueta inventario');
        $response->assertSee('Normal');

        DB::statement('DROP TABLE IF EXISTS stock');
        DB::statement('DROP TABLE IF EXISTS chaqueta');
        DB::statement('DROP TABLE IF EXISTS categoria');
    }

    public function test_dashboard_reports_real_sales_and_pending_shipments(): void
    {
        DB::statement('CREATE TABLE categoria (id_categoria INTEGER PRIMARY KEY AUTOINCREMENT, tipo_categoria TEXT NOT NULL, estado_categoria INTEGER NOT NULL DEFAULT 1)');
        DB::statement('CREATE TABLE materiales (id_materiales INTEGER PRIMARY KEY AUTOINCREMENT, material TEXT NOT NULL, proveedor_material_cod_proveedor INTEGER NULL, precio REAL NOT NULL DEFAULT 0, cantidad INTEGER NOT NULL DEFAULT 5)');
        DB::statement('CREATE TABLE chaqueta (id_chaqueta INTEGER PRIMARY KEY AUTOINCREMENT, modelo_chaqueta TEXT NOT NULL, precio REAL NOT NULL DEFAULT 0, categoria_id_categoria INTEGER NULL)');
        DB::statement('CREATE TABLE facturacion (id_facturacion INTEGER PRIMARY KEY AUTOINCREMENT, fecha TEXT NOT NULL, total REAL NOT NULL DEFAULT 0, impuestos TEXT NOT NULL DEFAULT 0, cliente_usuario_id_usuario INTEGER NULL, empleado_usuario_id_usuario INTEGER NULL)');
        DB::statement('CREATE TABLE envio (id_envio INTEGER PRIMARY KEY AUTOINCREMENT, facturacion_id_facturacion INTEGER NULL, tipo_envio TEXT NOT NULL DEFAULT "Pendiente", empresa_transportadora TEXT NOT NULL DEFAULT "", direccion_id_direccion TEXT NULL)');

        DB::table('categoria')->insert(['tipo_categoria' => 'Casual', 'estado_categoria' => 1]);
        DB::table('materiales')->insert([
            'material' => 'Algodón',
            'proveedor_material_cod_proveedor' => null,
            'precio' => 15,
            'cantidad' => 5,
        ]);
        $productoId = DB::table('chaqueta')->insertGetId([
            'modelo_chaqueta' => 'Chaqueta prueba',
            'precio' => 120,
            'categoria_id_categoria' => 1,
        ]);
        $facturaId = DB::table('facturacion')->insertGetId([
            'fecha' => now()->toDateTimeString(),
            'total' => 350,
            'impuestos' => '0',
            'cliente_usuario_id_usuario' => null,
            'empleado_usuario_id_usuario' => null,
        ]);
        DB::table('envio')->insert([
            'facturacion_id_facturacion' => $facturaId,
            'tipo_envio' => 'Pendiente',
            'empresa_transportadora' => 'DHL',
            'direccion_id_direccion' => null,
        ]);
        DB::table('envio')->insert([
            'facturacion_id_facturacion' => null,
            'tipo_envio' => 'Pendiente',
            'empresa_transportadora' => 'UPS',
            'direccion_id_direccion' => null,
        ]);

        $response = $this->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('350');
        $response->assertSee('2');

        DB::statement('DROP TABLE IF EXISTS envio');
        DB::statement('DROP TABLE IF EXISTS facturacion');
        DB::statement('DROP TABLE IF EXISTS chaqueta');
        DB::statement('DROP TABLE IF EXISTS materiales');
        DB::statement('DROP TABLE IF EXISTS categoria');
    }

    public function test_dashboard_uses_real_product_data(): void
    {
        DB::statement('CREATE TABLE categoria (id_categoria INTEGER PRIMARY KEY AUTOINCREMENT, tipo_categoria TEXT NOT NULL, estado_categoria INTEGER NOT NULL DEFAULT 1)');
        DB::statement('CREATE TABLE materiales (id_materiales INTEGER PRIMARY KEY AUTOINCREMENT, material TEXT NOT NULL, proveedor_material_cod_proveedor INTEGER NULL, precio REAL NOT NULL DEFAULT 0, cantidad INTEGER NOT NULL DEFAULT 0)');
        DB::statement('CREATE TABLE chaqueta (id_chaqueta INTEGER PRIMARY KEY AUTOINCREMENT, modelo_chaqueta TEXT NOT NULL, precio REAL NOT NULL DEFAULT 0, categoria_id_categoria INTEGER NULL)');

        DB::table('categoria')->insert(['tipo_categoria' => 'Casual', 'estado_categoria' => 1]);
        DB::table('materiales')->insert([
            'material' => 'Algodón',
            'proveedor_material_cod_proveedor' => null,
            'precio' => 15,
            'cantidad' => 5,
        ]);
        $productoId = DB::table('chaqueta')->insertGetId([
            'modelo_chaqueta' => 'Chaqueta prueba',
            'precio' => 120,
            'categoria_id_categoria' => 1,
        ]);

        $response = $this->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Chaqueta prueba');
        $response->assertSee('Casual');

        DB::statement('DROP TABLE IF EXISTS chaqueta');
        DB::statement('DROP TABLE IF EXISTS materiales');
        DB::statement('DROP TABLE IF EXISTS categoria');
    }

    public function test_empleado_productos_and_materiales_use_real_data(): void
    {
        DB::statement('CREATE TABLE categoria (id_categoria INTEGER PRIMARY KEY AUTOINCREMENT, tipo_categoria TEXT NOT NULL, estado_categoria INTEGER NOT NULL DEFAULT 1)');
        DB::statement('CREATE TABLE materiales (id_materiales INTEGER PRIMARY KEY AUTOINCREMENT, material TEXT NOT NULL, proveedor_material_cod_proveedor INTEGER NULL, precio REAL NOT NULL DEFAULT 0, cantidad INTEGER NOT NULL DEFAULT 5)');
        DB::statement('CREATE TABLE chaqueta (id_chaqueta INTEGER PRIMARY KEY AUTOINCREMENT, modelo_chaqueta TEXT NOT NULL, precio REAL NOT NULL DEFAULT 0, categoria_id_categoria INTEGER NULL)');
        DB::statement('CREATE TABLE chaqueta_has_materiales (chaqueta_id_chaqueta INTEGER NOT NULL, materiales_id_materiales INTEGER NOT NULL, PRIMARY KEY (chaqueta_id_chaqueta, materiales_id_materiales))');

        DB::table('categoria')->insert(['tipo_categoria' => 'Casual', 'estado_categoria' => 1]);
        $materialId = DB::table('materiales')->insertGetId([
            'material' => 'Algodón empleado',
            'proveedor_material_cod_proveedor' => null,
            'precio' => 10,
            'cantidad' => 12,
        ]);
        $productoId = DB::table('chaqueta')->insertGetId([
            'modelo_chaqueta' => 'Chaqueta empleado',
            'precio' => 180,
            'categoria_id_categoria' => 1,
        ]);
        DB::table('chaqueta_has_materiales')->insert([
            'chaqueta_id_chaqueta' => $productoId,
            'materiales_id_materiales' => $materialId,
        ]);

        $productosResponse = $this->get('/empleado/productos');
        $productosResponse->assertStatus(200);
        $productosResponse->assertSee('Chaqueta empleado');

        $materialesResponse = $this->get('/empleado/materiales');
        $materialesResponse->assertStatus(200);
        $materialesResponse->assertSee('Algodón empleado');

        DB::statement('DROP TABLE IF EXISTS chaqueta_has_materiales');
        DB::statement('DROP TABLE IF EXISTS chaqueta');
        DB::statement('DROP TABLE IF EXISTS materiales');
        DB::statement('DROP TABLE IF EXISTS categoria');
    }

    public function test_cliente_catalogo_uses_real_product_data(): void
    {
        DB::statement('CREATE TABLE categoria (id_categoria INTEGER PRIMARY KEY AUTOINCREMENT, tipo_categoria TEXT NOT NULL, estado_categoria INTEGER NOT NULL DEFAULT 1)');
        DB::statement('CREATE TABLE materiales (id_materiales INTEGER PRIMARY KEY AUTOINCREMENT, material TEXT NOT NULL, proveedor_material_cod_proveedor INTEGER NULL, precio REAL NOT NULL DEFAULT 0, cantidad INTEGER NOT NULL DEFAULT 0)');
        DB::statement('CREATE TABLE chaqueta (id_chaqueta INTEGER PRIMARY KEY AUTOINCREMENT, modelo_chaqueta TEXT NOT NULL, precio REAL NOT NULL DEFAULT 0, categoria_id_categoria INTEGER NULL)');
        DB::statement('CREATE TABLE chaqueta_has_materiales (chaqueta_id_chaqueta INTEGER NOT NULL, materiales_id_materiales INTEGER NOT NULL, PRIMARY KEY (chaqueta_id_chaqueta, materiales_id_materiales))');

        DB::table('categoria')->insert(['tipo_categoria' => 'Casual', 'estado_categoria' => 1]);
        $materialId = DB::table('materiales')->insertGetId([
            'material' => 'Algodón',
            'proveedor_material_cod_proveedor' => null,
            'precio' => 15,
            'cantidad' => 5,
        ]);
        $productoId = DB::table('chaqueta')->insertGetId([
            'modelo_chaqueta' => 'Chaqueta prueba',
            'precio' => 120,
            'categoria_id_categoria' => 1,
        ]);
        DB::table('chaqueta_has_materiales')->insert([
            'chaqueta_id_chaqueta' => $productoId,
            'materiales_id_materiales' => $materialId,
        ]);

        $response = $this->get('/cliente/catalogo');

        $response->assertStatus(200);
        $response->assertSee('Chaqueta prueba');
        $response->assertSee('Casual');

        DB::statement('DROP TABLE IF EXISTS chaqueta_has_materiales');
        DB::statement('DROP TABLE IF EXISTS chaqueta');
        DB::statement('DROP TABLE IF EXISTS materiales');
        DB::statement('DROP TABLE IF EXISTS categoria');
    }
}
