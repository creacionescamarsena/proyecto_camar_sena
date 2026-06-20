<?php

namespace Tests\Feature;

use App\Models\Usuario;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

class UsuarioViewAvailabilityTest extends TestCase
{
    public function test_admin_usuario_show_view_can_render(): void
    {
        $this->assertNotNull(
            View::make('admin.usuarios.show', ['usuario' => new Usuario()])
        );

        $html = View::file('resources/views/admin/usuarios/show.blade.php', ['usuario' => new Usuario()])->render();

        $this->assertStringContainsString('Detalle del usuario', $html);
    }
}
