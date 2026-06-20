<?php
require __DIR__ . '/../vendor/autoload.php';
putenv('APP_ENV=local');
putenv('APP_DEBUG=true');
putenv('DB_CONNECTION=mysql');
putenv('DB_HOST=127.0.0.1');
putenv('DB_PORT=3306');
putenv('DB_DATABASE=crudlaravel');
putenv('DB_USERNAME=root');
putenv('DB_PASSWORD=');
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

$email = 'mrjuan.2112@gmail.com';
$usuario = Usuario::where('correo', $email)->first();

if (! $usuario) {
    $usuario = Usuario::create([
        'nombres' => 'Juan Nicolas',
        'apellidos' => 'Pinzón Medina',
        'correo' => $email,
        'telefono' => null,
        'contraseña' => Hash::make('123456'),
        'rol' => 'Cliente',
        'estado' => 'Activo',
    ]);
    echo "Usuario creado: id={$usuario->id_usuario}\n";
} else {
    $usuario->contraseña = Hash::make('123456');
    $usuario->save();
    echo "Usuario existente actualizado: id={$usuario->id_usuario}\n";
}

if (Schema::hasTable('cliente')) {
    $exists = DB::table('cliente')->where('usuario_id_usuario', $usuario->getKey())->exists();
    if (! $exists) {
        DB::table('cliente')->insert(['usuario_id_usuario' => $usuario->getKey()]);
        echo "Fila cliente creada.\n";
    } else {
        echo "Fila cliente ya existente.\n";
    }
}
