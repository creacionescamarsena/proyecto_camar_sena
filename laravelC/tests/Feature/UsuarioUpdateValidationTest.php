<?php

namespace Tests\Feature;

use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Tests\TestCase;

class UsuarioUpdateValidationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('usuario');
        Schema::create('usuario', function (Blueprint $table) {
            $table->id('id_usuario');
            $table->string('nombres');
            $table->string('apellidos');
            $table->string('correo')->unique();
            $table->string('telefono')->nullable();
            $table->string('contraseña');
            $table->string('rol');
            $table->string('estado')->default('Activo');
            $table->timestamps();
        });

        Usuario::create([
            'nombres' => 'Admin',
            'apellidos' => 'Test',
            'email' => 'admin@test.com',
            'telefono' => '123456789',
            'password' => bcrypt('secret123'),
            'rol' => 'Admin',
            'estado' => 'Activo',
        ]);
    }

    public function test_update_user_validation_uses_the_real_primary_key(): void
    {
        $usuario = Usuario::firstOrFail();

        $response = $this->actingAs($usuario)
            ->put(route('admin.usuarios.update', $usuario), [
                'nombres' => $usuario->nombres,
                'apellidos' => $usuario->apellidos,
                'email' => $usuario->email,
                'telefono' => $usuario->telefono,
                'rol' => $usuario->rol,
                'estado' => $usuario->estado,
            ]);

        $response->assertRedirect(route('admin.usuarios.index'));
    }
}
