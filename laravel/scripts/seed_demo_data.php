<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

function columns(string $table): array
{
    return Schema::hasTable($table) ? Schema::getColumnListing($table) : [];
}

function hasColumn(string $table, string $column): bool
{
    return Schema::hasTable($table) && Schema::hasColumn($table, $column);
}

function insertGetIdFlexible(string $table, array $data, string $idColumn): int
{
    $filtered = array_intersect_key($data, array_flip(columns($table)));

    return (int) DB::table($table)->insertGetId($filtered, $idColumn);
}

function updateOrInsertBy(string $table, array $where, array $data): void
{
    $filteredWhere = array_intersect_key($where, array_flip(columns($table)));
    $filteredData = array_intersect_key($data, array_flip(columns($table)));

    DB::table($table)->updateOrInsert($filteredWhere, $filteredData);
}

DB::transaction(function () {
    $now = now();

    if (Schema::hasTable('tipo_documento')) {
        foreach (['CC', 'CE', 'TI', 'NIT'] as $tipo) {
            updateOrInsertBy('tipo_documento', ['tipo' => $tipo], ['tipo' => $tipo]);
        }
    }

    $tipoDocumentoId = Schema::hasTable('tipo_documento')
        ? (int) DB::table('tipo_documento')->orderBy('id_tipo')->value('id_tipo')
        : null;

    $usuariosBase = [
        ['id' => 900001, 'nombres' => 'Admin', 'apellidos' => 'Principal', 'correo' => 'admin@test.com', 'telefono' => '3001000001', 'rol' => 'Admin'],
        ['id' => 900002, 'nombres' => 'Empleado', 'apellidos' => 'Principal', 'correo' => 'empleado@test.com', 'telefono' => '3001000002', 'rol' => 'Empleado'],
        ['id' => 900003, 'nombres' => 'Cliente', 'apellidos' => 'Principal', 'correo' => 'cliente@test.com', 'telefono' => '3001000003', 'rol' => 'Cliente'],
    ];

    $nombres = ['Camila', 'Andres', 'Laura', 'Mateo', 'Valentina', 'Santiago', 'Daniela', 'Nicolas', 'Juliana', 'Sebastian', 'Paula', 'David', 'Sara', 'Felipe', 'Manuela', 'Cristian', 'Natalia'];
    $apellidos = ['Rojas', 'Gomez', 'Perez', 'Martinez', 'Garcia', 'Lopez', 'Castro', 'Ramirez', 'Torres', 'Vargas', 'Moreno', 'Herrera', 'Suarez', 'Diaz', 'Mendoza', 'Ortiz', 'Navarro'];

    for ($i = 4; $i <= 20; $i++) {
        $usuariosBase[] = [
            'id' => 900000 + $i,
            'nombres' => $nombres[$i - 4],
            'apellidos' => $apellidos[$i - 4],
            'correo' => 'usuario' . str_pad((string) $i, 2, '0', STR_PAD_LEFT) . '@test.com',
            'telefono' => '30010000' . str_pad((string) $i, 2, '0', STR_PAD_LEFT),
            'rol' => $i % 5 === 0 ? 'Empleado' : 'Cliente',
        ];
    }

    foreach ($usuariosBase as $usuario) {
        $data = [
            'id_usuario' => $usuario['id'],
            'nombres' => $usuario['nombres'],
            'apellidos' => $usuario['apellidos'],
            'correo' => $usuario['correo'],
            'telefono' => $usuario['telefono'],
            'contraseña' => Hash::make('123456'),
            'rol' => $usuario['rol'],
            'estado' => 'Activo',
            'tipo_documento_id' => $tipoDocumentoId,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        updateOrInsertBy('usuario', ['correo' => $usuario['correo']], $data);
        $usuarioId = (int) DB::table('usuario')->where('correo', $usuario['correo'])->value('id_usuario');

        if ($usuario['rol'] === 'Cliente' && Schema::hasTable('cliente')) {
            updateOrInsertBy('cliente', ['usuario_id_usuario' => $usuarioId], ['usuario_id_usuario' => $usuarioId]);
        }

        if ($usuario['rol'] === 'Empleado' && Schema::hasTable('empleado')) {
            updateOrInsertBy('empleado', ['usuario_id_usuario' => $usuarioId], [
                'usuario_id_usuario' => $usuarioId,
                'cargo' => $usuario['correo'] === 'empleado@test.com' ? 'Vendedor' : 'Operario',
            ]);
        }
    }

    $categorias = ['Casual', 'Impermeable', 'Cuero', 'Deportiva', 'Urbana'];
    $categoriaIds = [];
    if (Schema::hasTable('categoria')) {
        foreach ($categorias as $categoria) {
            updateOrInsertBy('categoria', ['tipo_categoria' => $categoria], [
                'tipo_categoria' => $categoria,
                'estado_categoria' => 1,
            ]);
            $categoriaIds[] = (int) DB::table('categoria')->where('tipo_categoria', $categoria)->value('id_categoria');
        }
    }

    $proveedores = ['Textiles Norte', 'Insumos Capital', 'Cueros Andinos', 'Hilos del Sur', 'Moda Supply'];
    $proveedorIds = [];
    if (Schema::hasTable('proveedor_material')) {
        foreach ($proveedores as $proveedor) {
            updateOrInsertBy('proveedor_material', ['proveedor_material' => $proveedor], [
                'proveedor_material' => $proveedor,
            ]);
            $proveedorIds[] = (int) DB::table('proveedor_material')->where('proveedor_material', $proveedor)->value('cod_proveedor');
        }
    }

    $materiales = [
        'Tela antifluido', 'Cremallera metalica', 'Forro interno', 'Boton nacarado', 'Hilo poliester',
        'Cuero sintetico', 'Tela denim', 'Resorte tubular', 'Broche presion', 'Guata termica',
        'Cordones negros', 'Tela impermeable', 'Etiqueta tejida', 'Hebilla acero', 'Malla deportiva',
        'Ribete algodon', 'Velcro industrial', 'Tela polar', 'Lona pesada', 'Aplique bordado',
    ];

    $materialIds = [];
    foreach ($materiales as $index => $material) {
        $data = [
            'material' => $material,
            'proveedor_material_cod_proveedor' => $proveedorIds[$index % max(1, count($proveedorIds))] ?? null,
            'precio' => 1200 + ($index * 350),
            'cantidad' => 25 + ($index * 4),
            'estado' => 'Activo',
        ];

        if (Schema::hasTable('materiales')) {
            updateOrInsertBy('materiales', ['material' => $material], $data);
            $materialIds[] = (int) DB::table('materiales')->where('material', $material)->value('id_materiales');
        }
    }

    $modelos = [
        'Atenea', 'Boreal', 'Cenit', 'Duna', 'Eclipse', 'Fenix', 'Granate', 'Hera', 'Indigo', 'Jade',
        'Kaira', 'Lira', 'Mistral', 'Nacar', 'Onix', 'Prisma', 'Quilla', 'Runa', 'Siena', 'Trueno',
    ];

    $productoIds = [];
    foreach ($modelos as $index => $modelo) {
        $nombre = 'Chaqueta ' . $modelo;
        $data = [
            'modelo_chaqueta' => $nombre,
            'precio' => 85000 + ($index * 7500),
            'categoria_id_categoria' => $categoriaIds[$index % max(1, count($categoriaIds))] ?? null,
            'estado' => 'Activo',
        ];

        if (Schema::hasTable('chaqueta')) {
            $exists = DB::table('chaqueta')->where('modelo_chaqueta', $nombre)->first();
            if ($exists) {
                DB::table('chaqueta')->where('id_chaqueta', $exists->id_chaqueta)->update(array_intersect_key($data, array_flip(columns('chaqueta'))));
                $productoId = (int) $exists->id_chaqueta;
            } else {
                $productoId = insertGetIdFlexible('chaqueta', $data, 'id_chaqueta');
            }

            $productoIds[] = $productoId;

            if (Schema::hasTable('chaqueta_has_materiales') && $materialIds) {
                for ($m = 0; $m < 2; $m++) {
                    $materialId = $materialIds[($index + $m) % count($materialIds)];
                    updateOrInsertBy('chaqueta_has_materiales', [
                        'chaqueta_id_chaqueta' => $productoId,
                        'materiales_id_materiales' => $materialId,
                    ], [
                        'chaqueta_id_chaqueta' => $productoId,
                        'materiales_id_materiales' => $materialId,
                    ]);
                }
            }
        }
    }

    if (Schema::hasTable('talla')) {
        foreach ([['XS', 1], ['S', 2], ['M', 3], ['L', 4], ['XL', 5]] as [$talla, $orden]) {
            updateOrInsertBy('talla', ['talla' => $talla], ['talla' => $talla, 'orden' => $orden]);
        }
    }

    $tallaIds = Schema::hasTable('talla') ? DB::table('talla')->orderBy('orden')->pluck('id_talla', 'talla')->all() : [];
    $tallas = array_keys($tallaIds) ?: ['S', 'M', 'L', 'XL'];

    if (Schema::hasTable('stock')) {
        foreach ($productoIds as $index => $productoId) {
            $talla = $tallas[$index % count($tallas)];
            $data = [
                'chaqueta_id_chaqueta' => $productoId,
                'talla_id_talla' => $tallaIds[$talla] ?? null,
                'talla' => $talla,
                'cantidad' => 8 + ($index * 3),
            ];

            $where = ['chaqueta_id_chaqueta' => $productoId];
            if (hasColumn('stock', 'talla_id_talla') && isset($tallaIds[$talla])) {
                $where['talla_id_talla'] = $tallaIds[$talla];
            } elseif (hasColumn('stock', 'talla')) {
                $where['talla'] = $talla;
            }

            updateOrInsertBy('stock', $where, $data);
        }
    }

    $clienteIds = Schema::hasTable('cliente') ? DB::table('cliente')->pluck('usuario_id_usuario')->values()->all() : [];
    $empleadoId = Schema::hasTable('empleado') ? DB::table('empleado')->value('usuario_id_usuario') : null;

    if (Schema::hasTable('facturacion') && $clienteIds && $productoIds) {
        for ($i = 1; $i <= 20; $i++) {
            $clienteId = $clienteIds[($i - 1) % count($clienteIds)];
            $productoId = $productoIds[($i - 1) % count($productoIds)];
            $producto = DB::table('chaqueta')->where('id_chaqueta', $productoId)->first();
            $cantidad = ($i % 3) + 1;
            $total = ((float) ($producto->precio ?? 90000)) * $cantidad;
            $fecha = now()->subDays(21 - $i)->setTime(10 + ($i % 8), 15);

            $existing = DB::table('facturacion')
                ->where('cliente_usuario_id_usuario', $clienteId)
                ->whereDate('fecha', $fecha->toDateString())
                ->first();

            if ($existing) {
                $facturaId = (int) $existing->id_facturacion;
                DB::table('facturacion')->where('id_facturacion', $facturaId)->update(array_intersect_key([
                    'fecha' => $fecha,
                    'total' => $total,
                    'impuestos' => '0',
                    'empleado_usuario_id_usuario' => $empleadoId,
                ], array_flip(columns('facturacion'))));
            } else {
                $facturaId = insertGetIdFlexible('facturacion', [
                    'fecha' => $fecha,
                    'total' => $total,
                    'impuestos' => '0',
                    'cliente_usuario_id_usuario' => $clienteId,
                    'empleado_usuario_id_usuario' => $empleadoId,
                ], 'id_facturacion');
            }

            if (Schema::hasTable('facturacion_has_chaqueta')) {
                updateOrInsertBy('facturacion_has_chaqueta', [
                    'facturacion_id_facturacion' => $facturaId,
                    'chaqueta_id_chaqueta' => $productoId,
                ], [
                    'facturacion_id_facturacion' => $facturaId,
                    'chaqueta_id_chaqueta' => $productoId,
                    'cantidad_venta' => $cantidad,
                    'valor_venta' => (string) ($producto->precio ?? 90000),
                ]);
            }

            if (Schema::hasTable('envio')) {
                $estados = ['Pendiente', 'En proceso', 'Enviado', 'Entregado'];
                updateOrInsertBy('envio', ['facturacion_id_facturacion' => $facturaId], [
                    'facturacion_id_facturacion' => $facturaId,
                    'tipo_envio' => $estados[$i % count($estados)],
                    'empresa_transportadora' => ['Servientrega', 'Interrapidisimo', 'Coordinadora', 'EnvialoYa'][$i % 4],
                    'direccion_id_direccion' => null,
                ]);
            }
        }
    }
});

$tables = ['usuario', 'chaqueta', 'materiales', 'stock', 'facturacion', 'envio'];
foreach ($tables as $table) {
    if (Schema::hasTable($table)) {
        echo $table . ': ' . DB::table($table)->count() . PHP_EOL;
    }
}

echo "Usuarios de prueba: admin@test.com, empleado@test.com, cliente@test.com / contraseña: 123456" . PHP_EOL;
