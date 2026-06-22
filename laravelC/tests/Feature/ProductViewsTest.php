<?php

namespace Tests\Feature;

use Tests\TestCase;

class ProductViewsTest extends TestCase
{
    public function test_admin_productos_page_renders_without_undefined_variables(): void
    {
        $response = $this->get('/admin/productos');

        $response->assertStatus(200);
    }

    public function test_cliente_catalogo_page_renders_without_undefined_variables(): void
    {
        $response = $this->get('/cliente/catalogo');

        $response->assertStatus(200);
    }
}
