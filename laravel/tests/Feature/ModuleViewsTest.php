<?php

namespace Tests\Feature;

use Tests\TestCase;

class ModuleViewsTest extends TestCase
{
    public function test_login_page_route_is_available(): void
    {
        $this->get('/login')->assertStatus(200);
    }

    public function test_admin_module_pages_render(): void
    {
        $this->get('/admin/productos')->assertStatus(200);
        $this->get('/admin/materiales')->assertStatus(200);
        $this->get('/admin/materiales/create')->assertStatus(200);
        $this->get('/admin/materiales/1/edit')->assertStatus(200);
        $this->get('/admin/inventario')->assertStatus(200);
        $this->get('/admin/inventario/create')->assertStatus(200);
        $this->get('/admin/facturacion')->assertStatus(200);
        $this->get('/admin/envios')->assertStatus(200);
    }

    public function test_empleado_module_pages_render(): void
    {
        $this->get('/empleado/productos')->assertStatus(200);
        $this->get('/empleado/materiales')->assertStatus(200);
        $this->get('/empleado/envios')->assertStatus(200);
        $this->get('/empleado/pedidos')->assertStatus(200);
        $this->get('/empleado/pedidos/1')->assertStatus(200);
    }

    public function test_cliente_can_add_products_to_cart(): void
    {
        $response = $this->post('/cliente/carrito/agregar', [
            'producto_id' => 1,
            'talla' => 'M',
            'cantidad' => 2,
        ]);

        $response->assertRedirect('/cliente/carrito');
    }
}
