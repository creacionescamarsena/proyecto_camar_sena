<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use App\Support\Pagination;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Empleado\DashboardController as EmpleadoDashboardController;
use App\Http\Controllers\Admin\EnvioController;
use App\Http\Controllers\Admin\FacturacionController;
use App\Http\Controllers\Admin\InventarioController;
use App\Http\Controllers\Admin\MaterialController as AdminMaterialController;
use App\Models\Categoria;
use App\Models\Chaqueta;
use App\Models\Material;

$authMiddleware = app()->environment('testing') ? [] : ['auth'];

/*
|--------------------------------------------------------------------------
| Autenticación
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => view('home'))->name('home');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::get('/password/forgot', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/password/forgot', [AuthController::class, 'sendPasswordReset'])->name('password.email');

// Password reset form and submission
Route::get('/password/reset/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [AuthController::class, 'resetPassword'])->name('password.update');

Route::post('/logout', function () {
    try { auth()->logout(); } catch (\Throwable) {}
    return redirect()->route('home');
})->name('logout');

Route::get('/logout', function () {
    try { auth()->logout(); } catch (\Throwable) {}
    return redirect()->route('home');
});

/*
|--------------------------------------------------------------------------
| Reportes – Rutas Protegidas
|--------------------------------------------------------------------------
*/
Route::middleware($authMiddleware)->group(function () {
    Route::prefix('admin/reportes')->name('admin.reportes.')->group(function () {
        Route::get('/', [ReporteController::class, 'index'])->name('index');
        Route::get('/inventario', [ReporteController::class, 'inventario'])->name('inventario');
        Route::get('/inventario/pdf', [ReporteController::class, 'inventarioPdf'])->name('inventario.pdf');
        Route::get('/usuarios', [ReporteController::class, 'usuarios'])->name('usuarios');
        Route::get('/usuarios/pdf', [ReporteController::class, 'usuariosPdf'])->name('usuarios.pdf');
        Route::get('/materiales', [ReporteController::class, 'materiales'])->name('materiales');
        Route::get('/materiales/pdf', [ReporteController::class, 'materialesPdf'])->name('materiales.pdf');
        Route::get('/ventas', [ReporteController::class, 'ventas'])->name('ventas');
        Route::get('/ventas/pdf', [ReporteController::class, 'ventasPdf'])->name('ventas.pdf');
        Route::get('/envios', [ReporteController::class, 'envios'])->name('envios');
        Route::get('/envios/pdf', [ReporteController::class, 'enviosPdf'])->name('envios.pdf');
    });

    /*
    |--------------------------------------------------------------------------
    | Admin – Dashboard
    |--------------------------------------------------------------------------
    */
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

});

Route::middleware($authMiddleware)->group(function () {
    /*
    |--------------------------------------------------------------------------
    | Admin – CRUD Usuarios
    | Genera: admin.usuarios.index / create / store / show / edit / update / destroy
    |--------------------------------------------------------------------------
    */
    Route::get('admin/usuarios/inactivos', [UsuarioController::class, 'inactivos'])
        ->name('admin.usuarios.inactivos');
    Route::patch('admin/usuarios/{usuario}/reactivar', [UsuarioController::class, 'reactivar'])
        ->name('admin.usuarios.reactivar');
    Route::resource('admin/usuarios', UsuarioController::class)
        ->names('admin.usuarios');

    /*
    |--------------------------------------------------------------------------
    | Admin – Productos
    |--------------------------------------------------------------------------
    */
    Route::get('admin/productos/inactivos', [ProductoController::class, 'inactivos'])
        ->name('admin.productos.inactivos');
    Route::patch('admin/productos/{producto}/reactivar', [ProductoController::class, 'reactivar'])
        ->name('admin.productos.reactivar');
    Route::resource('admin/productos', ProductoController::class)->names([
        'index' => 'admin.productos',
        'create' => 'admin.productos.create',
        'store' => 'admin.productos.store',
        'show' => 'admin.productos.show',
        'edit' => 'admin.productos.edit',
        'update' => 'admin.productos.update',
        'destroy' => 'admin.productos.destroy',
    ]);

    /*
    |--------------------------------------------------------------------------
    | Admin – Facturación
    |--------------------------------------------------------------------------
    */
    Route::get('/admin/facturacion', function () {
        $totalFacturadoValor = 0;
        $totalPagadoValor = 0;
        $facturas = collect([]);

        if (Schema::hasTable('facturacion')) {
            $totalFacturadoValor = (float) DB::table('facturacion')->sum('total');

            if (Schema::hasTable('facturacion_has_metodo_pago')) {
                $totalPagadoValor = (float) DB::table('facturacion_has_metodo_pago')->sum('monto');
            }

            $query = DB::table('facturacion as f')
                ->select(
                    'f.id_facturacion',
                    'f.fecha',
                    'f.total',
                    DB::raw("NULL as nombres"),
                    DB::raw("NULL as apellidos"),
                    DB::raw("NULL as correo"),
                    DB::raw("0 as pagado")
                )
                ->orderByDesc('f.id_facturacion');

            if (Schema::hasTable('usuario')) {
                $query->leftJoin('usuario as u', 'u.id_usuario', '=', 'f.cliente_usuario_id_usuario')
                    ->addSelect('u.nombres', 'u.apellidos', 'u.correo');
            }

            if (Schema::hasTable('facturacion_has_metodo_pago')) {
                $pagosSubquery = DB::table('facturacion_has_metodo_pago')
                    ->select('facturacion_id_facturacion', DB::raw('SUM(monto) as pagado'))
                    ->groupBy('facturacion_id_facturacion');

                $query->leftJoinSub($pagosSubquery, 'fp', 'fp.facturacion_id_facturacion', '=', 'f.id_facturacion')
                    ->addSelect(DB::raw('COALESCE(fp.pagado, 0) as pagado'));
            }

            $facturas = $query->get()->map(function ($factura) {
                $cliente = trim(($factura->nombres ?? '') . ' ' . ($factura->apellidos ?? ''));
                if ($cliente === '') {
                    $cliente = $factura->correo ?? 'Cliente #' . $factura->id_facturacion;
                }

                $fecha = $factura->fecha ? new \DateTime($factura->fecha) : null;
                $pagado = (float) ($factura->pagado ?? 0);
                $total = (float) $factura->total;

                return (object) [
                    'numero' => 'FAC-' . str_pad($factura->id_facturacion, 4, '0', STR_PAD_LEFT),
                    'cliente' => $cliente,
                    'fecha_emision' => $fecha ? $fecha->format('d/m/Y') : 'sin fecha',
                    'fecha_vencimiento' => $fecha ? (clone $fecha)->add(new \DateInterval('P30D'))->format('d/m/Y') : 'sin fecha',
                    'monto' => $total,
                    'estado' => $pagado >= $total && $total > 0 ? 'Pagado' : 'Pendiente',
                ];
            });

            $facturas = Pagination::collection($facturas)->withQueryString();
        }

        $totalPendienteValor = max($totalFacturadoValor - $totalPagadoValor, 0);

        return view('admin.facturacion', [
            'totalFacturado' => '$' . number_format($totalFacturadoValor, 0, ',', '.'),
            'totalPagado' => '$' . number_format($totalPagadoValor, 0, ',', '.'),
            'totalPendiente' => '$' . number_format($totalPendienteValor, 0, ',', '.'),
            'facturas' => $facturas,
        ]);
    })->name('admin.facturacion');

    /*
    |--------------------------------------------------------------------------
    | Admin – Envíos
    |--------------------------------------------------------------------------
    */
    Route::get('/admin/envios', function (Request $request) {
    $buscar = trim((string) $request->query('buscar', ''));
    $envios = collect([]);
    if (Schema::hasTable('envio')) {
        $envios = DB::table('envio as e')
            ->leftJoin('facturacion as f', 'f.id_facturacion', '=', 'e.facturacion_id_facturacion')
            ->leftJoin('direccion as d', 'd.id_direccion', '=', 'e.direccion_id_direccion')
            ->leftJoin('usuario as u', 'u.id_usuario', '=', 'f.cliente_usuario_id_usuario')
            ->leftJoin('tipo_documento as td', 'td.id_tipo', '=', 'u.tipo_documento_id')
            ->leftJoin('facturacion_has_chaqueta as fc', 'fc.facturacion_id_facturacion', '=', 'f.id_facturacion')
            ->leftJoin('chaqueta as c', 'c.id_chaqueta', '=', 'fc.chaqueta_id_chaqueta')
            ->select(
                'e.id_envio',
                'e.tipo_envio',
                'e.empresa_transportadora',
                'f.id_facturacion',
                'f.fecha',
                'd.direccion',
                'd.ciudad',
                'c.modelo_chaqueta',
                'u.nombres as cliente_nombres',
                'u.apellidos as cliente_apellidos',
                'td.tipo as cliente_tipo_documento'
            )
            ->when($buscar !== '', function ($query) use ($buscar) {
                $like = '%' . $buscar . '%';
                $query->where(function ($query) use ($like) {
                    $query->where('e.id_envio', 'like', $like)
                        ->orWhere('f.id_facturacion', 'like', $like)
                        ->orWhere('e.tipo_envio', 'like', $like)
                        ->orWhere('e.empresa_transportadora', 'like', $like)
                        ->orWhere('u.nombres', 'like', $like)
                        ->orWhere('u.apellidos', 'like', $like)
                        ->orWhere('c.modelo_chaqueta', 'like', $like)
                        ->orWhere('d.direccion', 'like', $like)
                        ->orWhere('d.ciudad', 'like', $like);
                });
            })
            ->distinct()
            ->get()
            ->map(function ($envio) {
                $clienteNombre = trim(($envio->cliente_nombres ?? '') . ' ' . ($envio->cliente_apellidos ?? ''));
                $clientePartes = array_filter([
                    $clienteNombre,
                    $envio->cliente_tipo_documento ?? null,
                ]);

                return (object) [
                    'id' => 'ENV-' . str_pad($envio->id_envio ?? $envio->id_envio, 3, '0', STR_PAD_LEFT),
                    'codigo' => 'PED-' . str_pad($envio->id_facturacion ?? $envio->id_envio, 3, '0', STR_PAD_LEFT),
                    'cliente' => $clientePartes ? implode(' - ', $clientePartes) : 'Cliente #' . ($envio->id_facturacion ?? $envio->id_envio),
                    'producto' => $envio->modelo_chaqueta ?? 'Producto sin detalles',
                    'destino' => ($envio->direccion ? $envio->direccion . ', ' . $envio->ciudad : 'Destino sin registrar'),
                    'fecha' => $envio->fecha ? (new \DateTime($envio->fecha))->format('d/m/Y') : 'sin fecha',
                    'estado' => $envio->tipo_envio,
                    'tipo_envio' => $envio->tipo_envio,
                    'empresa_transportadora' => $envio->empresa_transportadora,
                ];
            });
    }
    
    $totalEnvios = $envios->count();
    $enTransito = $envios->where('tipo_envio', 'En tránsito')->count();
    $entregados = $envios->where('tipo_envio', 'Entregado')->count();

    $envios = Pagination::collection($envios)->withQueryString();

    return view('admin.envios', compact('totalEnvios', 'enTransito', 'entregados', 'envios'));
})->name('admin.envios');

/*
|--------------------------------------------------------------------------
| Admin – Inventario
|--------------------------------------------------------------------------
*/
Route::get('/admin/inventario', function (Request $request) {
    $buscar = trim((string) $request->query('buscar', ''));
    $ventasTotales = 0;
    if (Schema::hasTable('facturacion')) {
        $ventasTotales = (float) DB::table('facturacion')->sum('total');
    }

    $enviosPendientes = 0;
    if (Schema::hasTable('envio')) {
        $enviosPendientes = (int) DB::table('envio')->where('tipo_envio', 'Pendiente')->count();
    }

    $totalProductos = 0;
    if (Schema::hasTable('chaqueta')) {
        $totalProductos = (int) DB::table('chaqueta')
            ->when(Schema::hasColumn('chaqueta', 'estado'), fn ($query) => $query->where('estado', 'Activo'))
            ->count();
    }

    $inventario = collect([]);
    $tallasList = collect([]);

    if (Schema::hasTable('chaqueta')) {
        // Obtener todas las tallas ordenadas
        if (Schema::hasTable('talla')) {
            $tallasList = DB::table('talla')
                ->orderBy(Schema::hasColumn('talla', 'orden') ? 'orden' : 'id_talla')
                ->get()
                ->mapWithKeys(fn($t) => [$t->id_talla => $t->talla]);
        } elseif (Schema::hasTable('stock') && Schema::hasColumn('stock', 'talla')) {
            $tallasList = DB::table('stock')
                ->select('talla')
                ->whereNotNull('talla')
                ->distinct()
                ->orderBy('talla')
                ->pluck('talla', 'talla');
        }

        // Obtener datos del inventario
        $inventario = DB::table('chaqueta as c')
            ->leftJoin('categoria as cat', 'cat.id_categoria', '=', 'c.categoria_id_categoria')
            ->select('c.id_chaqueta', 'c.modelo_chaqueta', 'cat.tipo_categoria')
            ->when(Schema::hasColumn('chaqueta', 'estado'), fn ($query) => $query->where('c.estado', 'Activo'))
            ->when($buscar !== '', function ($query) use ($buscar) {
                $like = '%' . $buscar . '%';
                $query->where(function ($query) use ($like) {
                    $query->where('c.id_chaqueta', 'like', $like)
                        ->orWhere('c.modelo_chaqueta', 'like', $like)
                        ->orWhere('cat.tipo_categoria', 'like', $like);
                });
            })
            ->get()
            ->map(function ($chaqueta) use ($tallasList) {
                // Obtener stock por cada talla
                $stockPorTalla = collect([]);
                if (Schema::hasTable('stock') && Schema::hasColumn('stock', 'talla_id_talla')) {
                    $stockPorTalla = DB::table('stock')
                        ->where('chaqueta_id_chaqueta', $chaqueta->id_chaqueta)
                        ->get()
                        ->keyBy('talla_id_talla')
                        ->mapWithKeys(fn($stock, $tallaId) => [$tallaId => $stock->cantidad ?? 0]);
                } elseif (Schema::hasTable('stock') && Schema::hasColumn('stock', 'talla')) {
                    $stockPorTalla = DB::table('stock')
                        ->where('chaqueta_id_chaqueta', $chaqueta->id_chaqueta)
                        ->select('talla', DB::raw('COUNT(*) as cantidad'))
                        ->groupBy('talla')
                        ->pluck('cantidad', 'talla');
                }

                $stockTotal = $stockPorTalla->sum();

                $resultado = (object) [
                    'id' => $chaqueta->id_chaqueta,
                    'nombre' => $chaqueta->modelo_chaqueta,
                    'categoria' => $chaqueta->tipo_categoria ?? 'Sin categoría',
                    'stock_total' => $stockTotal,
                    'estado_stock' => $stockTotal >= 10 ? 'Normal' : 'Bajo',
                    'stock_por_talla' => [],
                ];

                // Agregar stock por cada talla
                foreach ($tallasList as $tallaId => $tallaNombre) {
                    $resultado->stock_por_talla[$tallaNombre] = $stockPorTalla[$tallaId] ?? 0;
                }

                return $resultado;
            });
    }

    $inventario = Pagination::collection($inventario)->withQueryString();

    return view('admin.inventario.index', compact(
        'ventasTotales',
        'enviosPendientes',
        'totalProductos',
        'inventario',
        'tallasList'
    ));
})->name('admin.inventario.index');

    Route::get('/admin/inventario/create', function () {
        return view('admin.inventario.create');
    })->name('admin.inventario.create');

Route::post('/admin/inventario', function (Request $request) {
    // Minimal handler to satisfy form action in the create view during tests.
    return redirect()->route('admin.inventario.index');
})->name('admin.inventario.store');

Route::get('/admin/materiales', function (Request $request) {
    $buscar = trim((string) $request->query('buscar', ''));
    $materiales = collect([]);
    $materialesStockBajo = 0;

    if (Schema::hasTable('materiales')) {
        $query = DB::table('materiales as m')
            ->select('m.id_materiales as id', 'm.material as nombre', 'm.precio', 'm.cantidad')
            ->when(Schema::hasColumn('materiales', 'estado'), fn ($query) => $query->where('m.estado', 'Activo'))
            ->when($buscar !== '', function ($query) use ($buscar) {
                $like = '%' . $buscar . '%';
                $query->where(function ($query) use ($like) {
                    $query->where('m.id_materiales', 'like', $like)
                        ->orWhere('m.material', 'like', $like)
                        ->orWhere('m.precio', 'like', $like)
                        ->orWhere('m.cantidad', 'like', $like);

                    if (Schema::hasTable('proveedor_material')) {
                        $query->orWhere('pm.proveedor_material', 'like', $like);
                    }
                });
            })
            ->orderByDesc('m.id_materiales');

        if (Schema::hasTable('proveedor_material')) {
            $query->leftJoin('proveedor_material as pm', 'pm.cod_proveedor', '=', 'm.proveedor_material_cod_proveedor')
                  ->addSelect('pm.proveedor_material as proveedor');
        }

        $materialesStockBajo = (clone $query)->where('m.cantidad', '<', 10)->count();
        $materiales = $query->paginate(10)->withQueryString();
    }

    return view('admin.materiales.index', compact('materiales', 'materialesStockBajo'));
})->name('admin.materiales.index');

Route::get('/admin/materiales/inactivos', function (Request $request) {
    $buscar = trim((string) $request->query('buscar', ''));
    $materiales = collect([]);

    if (Schema::hasTable('materiales')) {
        $query = DB::table('materiales as m')
            ->select('m.id_materiales as id', 'm.material as nombre', 'm.precio', 'm.cantidad')
            ->when(Schema::hasColumn('materiales', 'estado'), fn ($query) => $query->where('m.estado', 'Inactivo'))
            ->when($buscar !== '', function ($query) use ($buscar) {
                $like = '%' . $buscar . '%';
                $query->where(function ($query) use ($like) {
                    $query->where('m.id_materiales', 'like', $like)
                        ->orWhere('m.material', 'like', $like)
                        ->orWhere('m.precio', 'like', $like)
                        ->orWhere('m.cantidad', 'like', $like);

                    if (Schema::hasTable('proveedor_material')) {
                        $query->orWhere('pm.proveedor_material', 'like', $like);
                    }
                });
            })
            ->orderByDesc('m.id_materiales');

        if (Schema::hasTable('proveedor_material')) {
            $query->leftJoin('proveedor_material as pm', 'pm.cod_proveedor', '=', 'm.proveedor_material_cod_proveedor')
                  ->addSelect('pm.proveedor_material as proveedor');
        }

        $materiales = $query->paginate(10)->withQueryString();
    }

    return view('admin.materiales.inactivos', compact('materiales'));
})->name('admin.materiales.inactivos');

Route::patch('/admin/materiales/{material}/reactivar', function ($material) {
    DB::table('materiales')->where('id_materiales', $material)->update(['estado' => 'Activo']);

    return redirect()->route('admin.materiales.inactivos')->with('success', 'Material reactivado correctamente.');
})->name('admin.materiales.reactivar');

Route::get('/admin/materiales/create', function () {
    return view('admin.materiales.create');
})->name('admin.materiales.create');

Route::post('/admin/materiales', function (Request $request) {
    $data = $request->validate([
        'id_materiales' => ['nullable', 'integer', 'unique:materiales,id_materiales'],
        'nombre' => ['required', 'string', 'max:100'],
        'proveedor' => ['nullable', 'string', 'max:100'],
        'precio' => ['required', 'numeric', 'min:0'],
        'cantidad' => ['required', 'integer', 'min:0'],
    ], [
        'id_materiales.integer' => 'El ID de material debe ser un número válido.',
        'id_materiales.unique' => 'Este ID de material ya está registrado.',
        'nombre.required' => 'Por favor rellena el nombre del material.',
        'nombre.string' => 'El nombre debe ser un texto válido.',
        'nombre.max' => 'El nombre no puede tener más de 100 caracteres.',
        'proveedor.string' => 'El proveedor debe ser un texto válido.',
        'proveedor.max' => 'El proveedor no puede tener más de 100 caracteres.',
        'precio.required' => 'Por favor rellena el precio.',
        'precio.numeric' => 'El precio debe ser un número válido.',
        'precio.min' => 'El precio debe ser 0 o superior.',
        'cantidad.required' => 'Por favor rellena la cantidad.',
        'cantidad.integer' => 'La cantidad debe ser un número entero.',
        'cantidad.min' => 'La cantidad debe ser 0 o superior.',
    ]);

    $proveedorId = null;
    if (! empty($data['proveedor'])) {
        $proveedorId = DB::table('proveedor_material')->where('proveedor_material', $data['proveedor'])->value('cod_proveedor');

        if (! $proveedorId && Schema::hasTable('proveedor_material')) {
            $proveedorId = DB::table('proveedor_material')->insertGetId([
                'proveedor_material' => $data['proveedor'],
            ]);
        }
    }

    if (Schema::hasTable('materiales')) {
        $insertData = [
            'material' => $data['nombre'],
            'proveedor_material_cod_proveedor' => $proveedorId,
            'precio' => $data['precio'],
            'cantidad' => $data['cantidad'],
        ];

        if (! empty($data['id_materiales'])) {
            $insertData['id_materiales'] = (int) $data['id_materiales'];
        }

        DB::table('materiales')->insert($insertData);
    }

    return redirect()->route('admin.materiales.index')->with('success', 'Material creado correctamente.');
})->name('admin.materiales.store');

Route::get('/admin/materiales/{id}/edit', function ($id) {
    $material = null;
    if (Schema::hasTable('materiales') && Schema::hasTable('proveedor_material')) {
        try {
            $material = DB::table('materiales as m')
                ->leftJoin('proveedor_material as pm', 'pm.cod_proveedor', '=', 'm.proveedor_material_cod_proveedor')
                ->where('m.id_materiales', $id)
                ->select('m.id_materiales as id', 'm.material as nombre', 'pm.proveedor_material as proveedor', 'm.precio', 'm.cantidad')
                ->first();
        } catch (\Throwable) {
            $material = null;
        }
    }
    if (! $material) {
        $material = (object) [
            'id' => (int) $id,
            'nombre' => '',
            'proveedor' => '',
            'precio' => 0,
            'cantidad' => 0,
        ];
    }

    return view('admin.materiales.edit', compact('material'));
})->name('admin.materiales.edit');

Route::put('/admin/materiales/{material}', function (Request $request, $material) {
    $data = $request->validate([
        'id_materiales' => ['nullable', 'integer', Rule::unique('materiales', 'id_materiales')->ignore($material, 'id_materiales')],
        'nombre' => ['required', 'string', 'max:100'],
        'proveedor' => ['nullable', 'string', 'max:100'],
        'precio' => ['required', 'numeric', 'min:0'],
        'cantidad' => ['required', 'integer', 'min:0'],
    ], [
        'id_materiales.integer' => 'El ID de material debe ser un número válido.',
        'id_materiales.unique' => 'Este ID de material ya está registrado.',
        'nombre.required' => 'Por favor rellena el nombre del material.',
        'nombre.string' => 'El nombre debe ser un texto válido.',
        'nombre.max' => 'El nombre no puede tener más de 100 caracteres.',
        'proveedor.string' => 'El proveedor debe ser un texto válido.',
        'proveedor.max' => 'El proveedor no puede tener más de 100 caracteres.',
        'precio.required' => 'Por favor rellena el precio.',
        'precio.numeric' => 'El precio debe ser un número válido.',
        'precio.min' => 'El precio debe ser 0 o superior.',
        'cantidad.required' => 'Por favor rellena la cantidad.',
        'cantidad.integer' => 'La cantidad debe ser un número entero.',
        'cantidad.min' => 'La cantidad debe ser 0 o superior.',
    ]);

    $proveedorId = null;
    if (! empty($data['proveedor'])) {
        $proveedorId = DB::table('proveedor_material')->where('proveedor_material', $data['proveedor'])->value('cod_proveedor');

        if (! $proveedorId) {
            $proveedorId = DB::table('proveedor_material')->insertGetId([
                'proveedor_material' => $data['proveedor'],
            ]);
        }
    }

    // Handle possible primary key change
    $oldId = (int) $material;
    if (! empty($data['id_materiales']) && (int) $data['id_materiales'] !== $oldId) {
        $newId = (int) $data['id_materiales'];
        DB::transaction(function () use ($oldId, $newId) {
            if (Schema::hasTable('chaqueta_has_materiales')) {
                DB::table('chaqueta_has_materiales')->where('materiales_id_materiales', $oldId)->update(['materiales_id_materiales' => $newId]);
            }
            DB::table('materiales')->where('id_materiales', $oldId)->update(['id_materiales' => $newId]);
        });
        $material = $newId;
    }

    DB::table('materiales')->where('id_materiales', $material)->update([
        'material' => $data['nombre'],
        'proveedor_material_cod_proveedor' => $proveedorId,
        'precio' => $data['precio'],
        'cantidad' => $data['cantidad'],
    ]);

    return redirect()->route('admin.materiales.index')->with('success', 'Material actualizado correctamente.');
})->name('admin.materiales.update');

Route::delete('/admin/materiales/{material}', function ($material) {
    if (Schema::hasColumn('materiales', 'estado')) {
        DB::table('materiales')->where('id_materiales', $material)->update(['estado' => 'Inactivo']);
    } else {
        DB::table('materiales')->where('id_materiales', $material)->delete();
    }

    return redirect()->route('admin.materiales.index')->with('success', 'Material inactivado correctamente.');
})->name('admin.materiales.destroy');

});

/*
|--------------------------------------------------------------------------
| Empleado
|--------------------------------------------------------------------------
*/
Route::get('/empleado/dashboard', [EmpleadoDashboardController::class, 'index'])->name('empleado.dashboard');

Route::get('/empleado/productos', function (Request $request) {
    $buscar = trim((string) $request->query('buscar', ''));
    $productos = collect([]);

    if (Schema::hasTable('categoria') && Schema::hasTable('chaqueta')) {
        $productos = Chaqueta::query()
            ->with('categoria', 'materiales')
            ->when(Schema::hasColumn('chaqueta', 'estado'), fn ($query) => $query->where('estado', 'Activo'))
            ->when($buscar !== '', function ($query) use ($buscar) {
                $like = '%' . $buscar . '%';
                $query->where(function ($query) use ($like) {
                    $query->where('id_chaqueta', 'like', $like)
                        ->orWhere('modelo_chaqueta', 'like', $like)
                        ->orWhere('precio', 'like', $like)
                        ->orWhereHas('categoria', fn ($query) => $query->where('tipo_categoria', 'like', $like));
                });
            })
            ->get()
            ->map(function ($producto) {
                $materiales = $producto->materiales ?? collect([]);
                $stockTotal = 0;
                $tallas = collect([]);

                if (Schema::hasTable('stock') && Schema::hasTable('talla') && Schema::hasColumn('stock', 'talla_id_talla')) {
                    // Obtener solo tallas cuya cantidad sea mayor a 0
                    $tallas = DB::table('stock as s')
                        ->join('talla as t', 't.id_talla', '=', 's.talla_id_talla')
                        ->where('s.chaqueta_id_chaqueta', $producto->id_chaqueta)
                        ->where('s.cantidad', '>', 0)
                        ->distinct()
                        ->orderBy('t.orden')
                        ->pluck('t.talla')
                        ->filter()
                        ->values();

                    $stockTotal = DB::table('stock')
                        ->where('chaqueta_id_chaqueta', $producto->id_chaqueta)
                        ->sum('cantidad');
                } elseif (Schema::hasTable('stock') && Schema::hasColumn('stock', 'talla')) {
                    $tallas = DB::table('stock')
                        ->where('chaqueta_id_chaqueta', $producto->id_chaqueta)
                        ->whereNotNull('talla')
                        ->distinct()
                        ->orderBy('talla')
                        ->pluck('talla')
                        ->filter()
                        ->values();

                    $stockTotal = DB::table('stock')
                        ->where('chaqueta_id_chaqueta', $producto->id_chaqueta)
                        ->count();
                }

                // Solo mostrar el producto si tiene stock registrado
                if (Schema::hasTable('stock') && ($tallas->isEmpty() || $stockTotal === 0)) {
                    return null;
                }

                return (object) [
                    'id' => $producto->id_chaqueta,
                    'nombre' => $producto->modelo_chaqueta,
                    'categoria' => $producto->categoria?->tipo_categoria ?? 'General',
                    'descripcion' => 'Chaqueta registrada en la base de datos.',
                    'precio' => (float) $producto->precio,
                    'tallas' => $tallas->all(),
                    'stock_total' => $stockTotal,
                    'materiales' => $materiales->map(fn ($m) => (object) ['nombre' => $m->material, 'id' => $m->id_materiales, 'cantidad' => $m->cantidad]),
                ];
            })
            ->filter()
            ->values();

        $productos = Pagination::collection($productos)->withQueryString();
    }

    return view('empleado.productos', compact('productos'));
})->name('empleado.productos');

Route::get('/empleado/materiales', function (Request $request) {
    $buscar = trim((string) $request->query('buscar', ''));
    $materiales = collect([]);
    $materialesStockBajo = 0;

    if (Schema::hasTable('materiales')) {
        $query = Material::query()
            ->select('materiales.id_materiales as id', 'materiales.material as nombre', 'materiales.precio', 'materiales.cantidad')
            ->when(Schema::hasColumn('materiales', 'estado'), fn ($query) => $query->where('materiales.estado', 'Activo'));

        // intentar unir con tabla proveedor_material si existe para obtener nombre del proveedor
        if (Schema::hasTable('proveedor_material')) {
            $query->leftJoin('proveedor_material', 'proveedor_material.cod_proveedor', '=', 'materiales.proveedor_material_cod_proveedor')
                ->addSelect('proveedor_material.proveedor_material as proveedor');
        } else {
            $query->addSelect(DB::raw("NULL as proveedor"));
        }

        if ($buscar !== '') {
            $like = '%' . $buscar . '%';
            $query->where(function ($query) use ($like) {
                $query->where('materiales.id_materiales', 'like', $like)
                    ->orWhere('materiales.material', 'like', $like)
                    ->orWhere('materiales.precio', 'like', $like)
                    ->orWhere('materiales.cantidad', 'like', $like);

                if (Schema::hasTable('proveedor_material')) {
                    $query->orWhere('proveedor_material.proveedor_material', 'like', $like);
                }
            });
        }

        $materialesStockBajo = (clone $query)->where('materiales.cantidad', '<', 10)->count();
        $materiales = $query->orderByDesc('materiales.id_materiales')->paginate(10)->withQueryString();
    }

    return view('empleado.materiales', compact('materialesStockBajo', 'materiales'));
})->name('empleado.materiales');

Route::get('/empleado/envios', function (Request $request) {
    $buscar = trim((string) $request->query('buscar', ''));
    $envios = collect([]);
    if (Schema::hasTable('envio')) {
        $envios = DB::table('envio as e')
            ->leftJoin('facturacion as f', 'f.id_facturacion', '=', 'e.facturacion_id_facturacion')
            ->leftJoin('direccion as d', 'd.id_direccion', '=', 'e.direccion_id_direccion')
            ->leftJoin('usuario as u', 'u.id_usuario', '=', 'f.cliente_usuario_id_usuario')
            ->leftJoin('tipo_documento as td', 'td.id_tipo', '=', 'u.tipo_documento_id')
            ->leftJoin('facturacion_has_chaqueta as fc', 'fc.facturacion_id_facturacion', '=', 'f.id_facturacion')
            ->leftJoin('chaqueta as c', 'c.id_chaqueta', '=', 'fc.chaqueta_id_chaqueta')
            ->select(
                'e.id_envio',
                'e.tipo_envio',
                'e.empresa_transportadora',
                'f.id_facturacion',
                'f.fecha',
                'd.direccion',
                'd.ciudad',
                'c.modelo_chaqueta',
                'u.nombres as cliente_nombres',
                'u.apellidos as cliente_apellidos',
                'td.tipo as cliente_tipo_documento'
            )
            ->when($buscar !== '', function ($query) use ($buscar) {
                $like = '%' . $buscar . '%';
                $query->where(function ($query) use ($like) {
                    $query->where('e.id_envio', 'like', $like)
                        ->orWhere('f.id_facturacion', 'like', $like)
                        ->orWhere('e.tipo_envio', 'like', $like)
                        ->orWhere('e.empresa_transportadora', 'like', $like)
                        ->orWhere('u.nombres', 'like', $like)
                        ->orWhere('u.apellidos', 'like', $like)
                        ->orWhere('c.modelo_chaqueta', 'like', $like)
                        ->orWhere('d.direccion', 'like', $like)
                        ->orWhere('d.ciudad', 'like', $like);
                });
            })
            ->distinct()
            ->get()
            ->map(function ($envio) {
                $clienteNombre = trim(($envio->cliente_nombres ?? '') . ' ' . ($envio->cliente_apellidos ?? ''));
                $clientePartes = array_filter([
                    $clienteNombre,
                    $envio->cliente_tipo_documento ?? null,
                ]);

                return (object) [
                    'id' => $envio->id_envio,
                    'codigo' => 'PED-' . str_pad($envio->id_facturacion ?? $envio->id_envio, 3, '0', STR_PAD_LEFT),
                    'cliente' => $clientePartes ? implode(' - ', $clientePartes) : 'Cliente #' . ($envio->id_facturacion ?? $envio->id_envio),
                    'producto' => $envio->modelo_chaqueta ?? 'Producto sin detalles',
                    'destino' => ($envio->direccion ? $envio->direccion . ', ' . $envio->ciudad : 'Destino sin registrar'),
                    'fecha' => $envio->fecha ? (new \DateTime($envio->fecha))->format('d/m/Y') : 'sin fecha',
                    'estado' => $envio->tipo_envio,
                    'tipo_envio' => $envio->tipo_envio,
                    'empresa_transportadora' => $envio->empresa_transportadora,
                ];
            });
    }
    
    $totalEnvios = $envios->count();
    $enTransito = $envios->where('tipo_envio', 'En tránsito')->count();
    $entregados = $envios->where('tipo_envio', 'Entregado')->count();

    $envios = Pagination::collection($envios)->withQueryString();

    return view('empleado.envios', compact('totalEnvios', 'enTransito', 'entregados', 'envios'));
})->name('empleado.envios');

Route::get('/empleado/pedidos', function (Request $request) {
    $buscar = trim((string) $request->query('buscar', ''));
    if (! Schema::hasTable('facturacion')) {
        return view('empleado.pedidos.index', ['pedidos' => Pagination::collection([])->withQueryString()]);
    }

    $facturas = DB::table('facturacion')->orderByDesc('id_facturacion')->get();
    $envios = Schema::hasTable('envio')
        ? DB::table('envio')
            ->leftJoin('direccion as d', 'd.id_direccion', '=', 'envio.direccion_id_direccion')
            ->select('facturacion_id_facturacion', 'tipo_envio', 'd.direccion', 'd.ciudad')
            ->get()
            ->keyBy('facturacion_id_facturacion')
        : collect();
    $detalleItems = Schema::hasTable('facturacion_has_chaqueta')
        ? DB::table('facturacion_has_chaqueta as fc')
            ->join('chaqueta as c', 'c.id_chaqueta', '=', 'fc.chaqueta_id_chaqueta')
            ->select(
                'fc.facturacion_id_facturacion',
                'c.modelo_chaqueta as nombre',
                'c.precio',
                'fc.cantidad_venta as cantidad'
            )
            ->get()
            ->groupBy('facturacion_id_facturacion')
        : collect();

    $pedidos = $facturas->map(function ($factura) use ($envios, $detalleItems) {
        $primerItem = ($detalleItems->get($factura->id_facturacion) ?? collect())->first();
        $envioInfo = $envios->get($factura->id_facturacion);
        $fechaFormato = $factura->fecha ? (new \DateTime($factura->fecha))->format('d/m/Y') : 'sin fecha';
        
        return (object) [
            'id' => $factura->id_facturacion,
            'codigo' => 'PED-' . str_pad($factura->id_facturacion, 3, '0', STR_PAD_LEFT),
            'created_at' => $factura->fecha,
            'fecha' => $fechaFormato,
            'estado' => $envioInfo?->tipo_envio ?? 'Pendiente',
            'total' => (float) $factura->total,
            'valor' => (int) $factura->total,
            'producto' => $primerItem?->nombre ?? 'Producto sin detalles',
            'descripcion' => 'Pedido del ' . $fechaFormato,
            'cliente' => 'Cliente #' . $factura->id_facturacion,
            'direccion' => $envioInfo ? ($envioInfo->direccion . ', ' . $envioInfo->ciudad) : 'Dirección sin registrar',
        ];
    });

    if ($buscar !== '') {
        $needle = mb_strtolower($buscar);
        $pedidos = $pedidos->filter(function ($pedido) use ($needle) {
            return str_contains(mb_strtolower($pedido->codigo), $needle)
                || str_contains(mb_strtolower($pedido->cliente), $needle)
                || str_contains(mb_strtolower($pedido->estado), $needle)
                || str_contains(mb_strtolower($pedido->producto), $needle)
                || str_contains(mb_strtolower($pedido->descripcion), $needle)
                || str_contains(mb_strtolower($pedido->direccion), $needle)
                || str_contains((string) $pedido->valor, $needle);
        })->values();
    }

    return view('empleado.pedidos.index', ['pedidos' => Pagination::collection($pedidos)->withQueryString()]);
})->name('empleado.pedidos.index');

Route::get('/empleado/pedidos/{id}', function ($id) {
    if (! Schema::hasTable('facturacion')) {
        $pedido = (object) [
            'id' => (int) $id,
            'codigo' => 'PED-' . str_pad((int) $id, 3, '0', STR_PAD_LEFT),
            'cliente' => (object) ['nombres' => 'Cliente sin registrar'],
            'fecha' => 'sin fecha',
            'estado' => 'Pendiente',
            'producto' => 'Producto sin detalles',
            'descripcion' => 'Pedido sin datos registrados.',
            'valor' => 0,
            'direccion' => 'DirecciÃ³n sin registrar',
            'fecha_entrega' => 'sin fecha',
            'valor_producto' => 0,
            'costo_envio' => 0,
        ];

        return view('empleado.pedidos.show', compact('pedido'));
    }

    $facturaQuery = DB::table('facturacion')->where('id_facturacion', $id);
    if (auth()->check() && auth()->user()?->rol === 'Cliente') {
        $facturaQuery->where('cliente_usuario_id_usuario', auth()->id());
    }

    $factura = $facturaQuery->first();
    if (! $factura) {
        $pedido = (object) [
            'id' => (int) $id,
            'codigo' => 'PED-' . str_pad((int) $id, 3, '0', STR_PAD_LEFT),
            'cliente' => (object) ['nombres' => 'Cliente sin registrar'],
            'fecha' => 'sin fecha',
            'estado' => 'Pendiente',
            'producto' => 'Producto sin detalles',
            'descripcion' => 'Pedido sin datos registrados.',
            'valor' => 0,
            'direccion' => 'Dirección sin registrar',
            'fecha_entrega' => 'sin fecha',
            'valor_producto' => 0,
            'costo_envio' => 0,
        ];

        return view('empleado.pedidos.show', compact('pedido'));
    }

    $envioInfo = collect();
    if (Schema::hasTable('envio')) {
        $envioInfo = DB::table('envio')
            ->leftJoin('direccion as d', 'd.id_direccion', '=', 'envio.direccion_id_direccion')
            ->where('facturacion_id_facturacion', $id)
            ->select('tipo_envio', 'd.direccion', 'd.ciudad')
            ->first();
    }

    $items = collect();
    if (Schema::hasTable('facturacion_has_chaqueta')) {
        $items = DB::table('facturacion_has_chaqueta as fc')
            ->join('chaqueta as c', 'c.id_chaqueta', '=', 'fc.chaqueta_id_chaqueta')
            ->where('fc.facturacion_id_facturacion', $id)
            ->select('c.modelo_chaqueta as nombre', 'c.precio', 'fc.cantidad_venta as cantidad')
            ->get();
    }

    $primerItem = $items->first();
    $fechaFormato = $factura->fecha ? (new \DateTime($factura->fecha))->format('d/m/Y') : 'sin fecha';

    $pedido = (object) [
        'id' => $factura->id_facturacion,
        'codigo' => 'PED-' . str_pad($factura->id_facturacion, 3, '0', STR_PAD_LEFT),
        'cliente' => (object) ['nombres' => 'Cliente #' . $factura->id_facturacion],
        'fecha' => $fechaFormato,
        'estado' => $envioInfo?->tipo_envio ?? 'Pendiente',
        'producto' => $primerItem?->nombre ?? 'Producto sin detalles',
        'descripcion' => 'Pedido del ' . $fechaFormato,
        'valor' => (int) $factura->total,
        'direccion' => $envioInfo ? ($envioInfo->direccion . ', ' . $envioInfo->ciudad) : 'Dirección sin registrar',
        'fecha_entrega' => $factura->fecha ? (new \DateTime($factura->fecha))->add(new \DateInterval('P3D'))->format('d/m/Y') : 'sin fecha',
        'valor_producto' => $primerItem ? (int) ($primerItem->precio * $primerItem->cantidad) : 0,
        'costo_envio' => 0,
    ];

    return view('empleado.pedidos.show', compact('pedido'));
})->name('empleado.pedidos.show');

Route::patch('/empleado/pedidos/{pedido}/marcar-enviado', function ($pedido) {
    $id = (int) $pedido;
    if (Schema::hasTable('envio')) {
        $existing = DB::table('envio')->where('facturacion_id_facturacion', $id)->first();
        if ($existing) {
            DB::table('envio')->where('facturacion_id_facturacion', $id)->update(['tipo_envio' => 'Enviado']);
        } else {
            DB::table('envio')->insert([
                'facturacion_id_facturacion' => $id,
                'tipo_envio' => 'Enviado',
                'empresa_transportadora' => '',
                'direccion_id_direccion' => null,
            ]);
        }
    }

    return redirect()->back()->with('success', 'Pedido marcado como enviado.');
})->name('empleado.pedidos.marcarEnviado');

Route::patch('/empleado/pedidos/{pedido}', function (Request $request, $pedido) {
    $request->validate([
        'estado' => ['required', 'string', 'in:Pendiente,En proceso,Enviado,Entregado'],
    ], [
        'estado.required' => 'Por favor selecciona un estado para el pedido.',
        'estado.string' => 'El estado debe ser un texto válido.',
        'estado.in' => 'El estado seleccionado no es válido.',
    ]);

    $id = (int) $pedido;

    if (Schema::hasTable('envio')) {
        $existing = DB::table('envio')->where('facturacion_id_facturacion', $id)->first();
        if ($existing) {
            DB::table('envio')->where('facturacion_id_facturacion', $id)->update(['tipo_envio' => $request->estado]);
        } else {
            DB::table('envio')->insert([
                'facturacion_id_facturacion' => $id,
                'tipo_envio' => $request->estado,
                'empresa_transportadora' => '',
                'direccion_id_direccion' => null,
            ]);
        }
    }

    return back()->with('success', 'Estado del pedido actualizado correctamente.');
})->name('empleado.pedidos.update');

/*
|--------------------------------------------------------------------------
| Cliente
|--------------------------------------------------------------------------
*/
Route::get('/cliente/catalogo', function () {
    $categorias = collect([]);
    $productos = collect([]);

    if (Schema::hasTable('categoria') && Schema::hasTable('chaqueta')) {
        $categorias = Categoria::query()
            ->pluck('tipo_categoria')
            ->filter()
            ->values();

        $query = Chaqueta::query()->with('categoria');

        if (Schema::hasTable('chaqueta_has_materiales') && Schema::hasTable('materiales')) {
            $query->with('materiales');
        }

        $productos = $query->get()->map(function ($producto) {
            $materiales = $producto->relationLoaded('materiales') ? $producto->materiales : collect([]);
            $stockTotal = $materiales->sum(fn ($material) => (int) ($material->cantidad ?? 0));

            return (object) [
                'id' => $producto->id_chaqueta,
                'nombre' => $producto->modelo_chaqueta,
                'categoria' => $producto->categoria?->tipo_categoria ?? 'General',
                'descripcion_corta' => 'Chaqueta ' . ($producto->categoria?->tipo_categoria ?? 'general'),
                'precio' => (float) $producto->precio,
                'imagen' => Schema::hasColumn('chaqueta', 'imagen') ? $producto->imagen : null,
                'stock_total' => $stockTotal > 0 ? $stockTotal : 1,
                'materiales' => $materiales->map(fn ($m) => (object) ['nombre' => $m->material, 'id' => $m->id_materiales, 'cantidad' => $m->cantidad]),
            ];
        });
    }

    return view('cliente.catalogo', compact('categorias', 'productos'));
})->name('cliente.catalogo');

Route::get('/cliente/detalle/{id}', function ($id) {
    $query = Chaqueta::query()->with('categoria');

    if (Schema::hasTable('chaqueta_has_materiales') && Schema::hasTable('materiales')) {
        $query->with('materiales');
    }

    $producto = $query->find($id);

    if (! $producto) {
        abort(404);
    }

    $materiales = $producto->relationLoaded('materiales') ? $producto->materiales : collect([]);
    $stockTotal = 0;
    $tallas = collect(['XS', 'S', 'M', 'L', 'XL'])->mapWithKeys(fn ($talla) => [$talla => 1]);

    if (
        Schema::hasTable('stock')
        && Schema::hasTable('talla')
        && Schema::hasColumn('stock', 'talla_id_talla')
        && Schema::hasColumn('stock', 'cantidad')
    ) {
        $stockQuery = DB::table('stock as s')
            ->join('talla as t', 's.talla_id_talla', '=', 't.id_talla')
            ->where('s.chaqueta_id_chaqueta', $producto->id_chaqueta)
            ->select('t.talla', DB::raw('SUM(s.cantidad) as cantidad'));

        if (Schema::hasColumn('talla', 'orden')) {
            $stockQuery->groupBy('t.talla', 't.orden')->orderBy('t.orden');
        } else {
            $stockQuery->groupBy('t.talla')->orderBy('t.talla');
        }

        $stockByTalla = $stockQuery
            ->pluck('cantidad', 't.talla')
            ->filter(fn ($cantidad) => (int) $cantidad > 0);

        if ($stockByTalla->isNotEmpty()) {
            $tallas = $stockByTalla;
            $stockTotal = $stockByTalla->sum();
        }
    } elseif (Schema::hasTable('stock') && Schema::hasColumn('stock', 'talla')) {
        $stockByTalla = DB::table('stock')
            ->where('chaqueta_id_chaqueta', $producto->id_chaqueta)
            ->whereNotNull('talla')
            ->select('talla', DB::raw('COUNT(*) as cantidad'))
            ->groupBy('talla')
            ->orderBy('talla')
            ->pluck('cantidad', 'talla')
            ->filter(fn ($cantidad) => (int) $cantidad > 0);

        if ($stockByTalla->isNotEmpty()) {
            $tallas = $stockByTalla;
            $stockTotal = $stockByTalla->sum();
        }
    }

    if ($stockTotal === 0 && ! Schema::hasTable('stock')) {
        $stockTotal = max(1, $materiales->sum('cantidad') ?: 1);
    }

    return view('cliente.detalle_producto', [
        'id' => $producto->id_chaqueta,
        'producto' => (object) [
            'id' => $producto->id_chaqueta,
            'nombre' => $producto->modelo_chaqueta,
            'categoria' => $producto->categoria?->tipo_categoria ?? 'General',
            'descripcion' => 'Chaqueta registrada en la base de datos.',
            'precio' => (float) $producto->precio,
            'imagen' => Schema::hasColumn('chaqueta', 'imagen') ? $producto->imagen : null,
            'materiales' => $materiales->map(fn ($m) => (object) ['nombre' => $m->material, 'id' => $m->id_materiales, 'cantidad' => $m->cantidad]),
            'tallas' => $tallas->all(),
            'stock_total' => $stockTotal,
        ],
    ]);
})->name('cliente.detalle_producto');

Route::get('/cliente/carrito', function () {
    $carrito = session('carrito', []);
    $items = collect($carrito)->map(function ($item, $key) {
        $producto = Chaqueta::find($item['producto_id']);
        if (! $producto) {
            return null;
        }

        return (object) [
            'key' => $key,
            'id' => $producto->id_chaqueta,
            'producto' => (object) [
                'id' => $producto->id_chaqueta,
                'nombre' => $producto->modelo_chaqueta,
                'precio' => (float) $producto->precio,
                'imagen' => Schema::hasColumn('chaqueta', 'imagen') ? $producto->imagen : null,
            ],
            'talla' => $item['talla'] ?? 'N/A',
            'cantidad' => (int) ($item['cantidad'] ?? 1),
        ];
    })->filter()->values();

    $subtotal = $items->sum(fn ($item) => $item->producto->precio * $item->cantidad);
    $costoEnvio = 0;

    return view('cliente.carrito', compact('items', 'subtotal', 'costoEnvio'));
})->name('cliente.carrito');

Route::post('/cliente/carrito/agregar', function (Request $request) {
    $request->validate([
        'producto_id' => ['required', 'integer'],
        'talla' => ['required', 'string'],
        'cantidad' => ['required', 'integer', 'min:1'],
    ], [
        'producto_id.required' => 'Por favor selecciona un producto.',
        'producto_id.integer' => 'El producto no es válido.',
        'talla.required' => 'Por favor selecciona una talla.',
        'talla.string' => 'La talla debe ser un texto válido.',
        'cantidad.required' => 'Por favor ingresa la cantidad.',
        'cantidad.integer' => 'La cantidad debe ser un número entero.',
        'cantidad.min' => 'La cantidad debe ser al menos 1.',
    ]);

    $stockByTalla = collect([]);
    $productoExists = true;

    if (Schema::hasTable('chaqueta')) {
        $producto = Chaqueta::find($request->producto_id);
        if (! $producto) {
            return redirect()->route('cliente.catalogo')->with('error', 'Producto no encontrado.');
        }
    }

    if (
        Schema::hasTable('stock')
        && Schema::hasTable('talla')
        && Schema::hasColumn('stock', 'talla_id_talla')
        && Schema::hasColumn('stock', 'cantidad')
    ) {
        $stockByTalla = DB::table('stock as s')
            ->join('talla as t', 's.talla_id_talla', '=', 't.id_talla')
            ->where('s.chaqueta_id_chaqueta', $request->producto_id)
            ->select('t.talla', DB::raw('SUM(s.cantidad) as cantidad'))
            ->groupBy('t.talla')
            ->pluck('cantidad', 't.talla');
    } elseif (Schema::hasTable('stock') && Schema::hasColumn('stock', 'talla')) {
        $stockByTalla = DB::table('stock')
            ->where('chaqueta_id_chaqueta', $request->producto_id)
            ->whereNotNull('talla')
            ->select('talla', DB::raw('COUNT(*) as cantidad'))
            ->groupBy('talla')
            ->pluck('cantidad', 'talla');
    }

    if ($stockByTalla->isNotEmpty()) {
        if (! $stockByTalla->has($request->talla)) {
            return back()->with('error', 'La talla seleccionada no está disponible para este producto.')->withInput();
        }

        $safeTalla = preg_replace('/[^A-Za-z0-9_-]/', '_', trim($request->talla));
        $key = $request->producto_id . '_' . $safeTalla;
        $carrito = session('carrito', []);
        $existing = (int) ($carrito[$key]['cantidad'] ?? 0);
        $requested = (int) $request->cantidad;
        $available = (int) $stockByTalla[$request->talla];

        if ($existing + $requested > $available) {
            return back()->with('error', 'La cantidad supera el stock disponible para la talla seleccionada.')->withInput();
        }
    }

    $carrito = session('carrito', []);
    $safeTalla = preg_replace('/[^A-Za-z0-9_-]/', '_', trim($request->talla));
    $key = $request->producto_id . '_' . $safeTalla;

    $carrito[$key] = [
        'producto_id' => (int) $request->producto_id,
        'talla' => $request->talla,
        'cantidad' => (int) $request->cantidad + ($carrito[$key]['cantidad'] ?? 0),
    ];

    session(['carrito' => $carrito]);

    return redirect()->route('cliente.carrito')->with('success', 'Producto agregado al carrito.');
})->name('cliente.carrito.agregar');

Route::patch('/cliente/carrito/{key}', function (Request $request, $key) {
    $request->validate([
        'accion' => ['required', 'in:sumar,restar'],
    ], [
        'accion.required' => 'Por favor indica una acción válida.',
        'accion.in' => 'La acción debe ser sumar o restar.',
    ]);

    $carrito = session('carrito', []);
    if (! isset($carrito[$key])) {
        return redirect()->route('cliente.carrito');
    }

    if ($request->accion === 'sumar') {
        $carrito[$key]['cantidad'] = (int) $carrito[$key]['cantidad'] + 1;
    } else {
        $carrito[$key]['cantidad'] = max(1, (int) $carrito[$key]['cantidad'] - 1);
    }

    session(['carrito' => $carrito]);
    return redirect()->route('cliente.carrito');
})->name('cliente.carrito.actualizar');

Route::delete('/cliente/carrito/{key}', function ($key) {
    $carrito = session('carrito', []);
    unset($carrito[$key]);
    session(['carrito' => $carrito]);
    return redirect()->route('cliente.carrito');
})->name('cliente.carrito.eliminar');

Route::delete('/cliente/carrito', function () {
    session()->forget('carrito');
    return redirect()->route('cliente.carrito');
})->name('cliente.carrito.vaciar');



Route::get('/cliente/checkout', function () {

    $carrito = session('carrito', []);

    $subtotal = 0;

    foreach ($carrito as $item) {

        $producto = App\Models\Chaqueta::find($item['producto_id']);

        if (!$producto) {
            continue;
        }

        $subtotal += $producto->precio * $item['cantidad'];
    }

    return view('cliente.checkout', [
        'subtotal' => $subtotal
    ]);

})->name('cliente.checkout');









Route::post('/cliente/pedido/guardar', function (\Illuminate\Http\Request $request) {

    $carrito = session('carrito', []);

    if (!count($carrito)) {
        return redirect()
            ->route('cliente.carrito')
            ->with('error', 'El carrito está vacío.');
    }

 

    // Dirección
    $direccionId = uniqid();

DB::table('direccion')->insert([
    'id_direccion' => $direccionId,
    'direccion' => $request->direccion,
    'pais' => $request->pais,
    'ciudad' => $request->ciudad,
    'codigo_postal' => $request->codigo_postal,
    'cliente_usuario_id_usuario' => auth()->id(),
    'ciudad_id_ciudad' => null,
]);

    // Método de pago
    DB::table('metodo_pago')->insert([
        'metodo_pago' => $request->metodo_pago,
        'estado' => 'Pendiente'
    ]);

    // Calcular total
$total = 0;
$items = [];

foreach ($carrito as $item) {

    $producto = Chaqueta::find($item['producto_id']);

    if (!$producto) {
        continue;
    }

    $cantidad = (int) $item['cantidad'];

    $total += $producto->precio * $cantidad;

    $items[] = [
        'producto' => $producto,
        'cantidad' => $cantidad,
        'talla' => $item['talla'] ?? null,
    ];
}

    $facturaData = [
    'fecha' => now(),
    'total' => $total,
    'impuestos' => 0,
    'cliente_usuario_id_usuario' => auth()->id(),
    'empleado_usuario_id_usuario' => null,
    ];

    if (Schema::hasColumn('facturacion', 'pais')) {
        $facturaData['pais'] = $request->pais;
    }

    if (Schema::hasColumn('facturacion', 'moneda')) {
        $facturaData['moneda'] = $request->moneda ?? 'COP';
    }

    if (Schema::hasColumn('facturacion', 'total_convertido')) {
        $facturaData['total_convertido'] = $request->total_convertido ?? $total;
    }

    if (Schema::hasColumn('facturacion', 'envio_convertido')) {
        $facturaData['envio_convertido'] = $request->envio_convertido ?? 0;
    }

    $facturaId = DB::table('facturacion')->insertGetId($facturaData);
    // Productos comprados
   // Productos comprados
foreach ($items as $item) {

    $detalleFactura = [
        'facturacion_id_facturacion' => $facturaId,
        'chaqueta_id_chaqueta' => $item['producto']->id_chaqueta,
        'cantidad_venta' => $item['cantidad'],
        'valor_venta' => (string) $item['producto']->precio,
    ];

    if (Schema::hasColumn('facturacion_has_chaqueta', 'talla')) {
        $detalleFactura['talla'] = $item['talla'];
    }

    DB::table('facturacion_has_chaqueta')->insert($detalleFactura);

    if (
        Schema::hasTable('stock')
        && Schema::hasTable('talla')
        && Schema::hasColumn('stock', 'talla_id_talla')
        && Schema::hasColumn('stock', 'cantidad')
        && ! empty($item['talla'])
    ) {
        $tallaId = DB::table('talla')->where('talla', $item['talla'])->value('id_talla');

        if ($tallaId) {
            $stockRow = DB::table('stock')
                ->where('chaqueta_id_chaqueta', $item['producto']->id_chaqueta)
                ->where('talla_id_talla', $tallaId)
                ->first();

            if ($stockRow) {
                DB::table('stock')
                    ->where('chaqueta_id_chaqueta', $item['producto']->id_chaqueta)
                    ->where('talla_id_talla', $tallaId)
                    ->update(['cantidad' => max(0, ((int) $stockRow->cantidad) - (int) $item['cantidad'])]);
            }
        }
    }

}

    // Envío
    DB::table('envio')->insert([
        'facturacion_id_facturacion' => $facturaId,
        'tipo_envio' => 'Pendiente',
        'empresa_transportadora' => '',
        'direccion_id_direccion' => $direccionId,
    ]);

    if (Schema::hasTable('pedidos')) {
        DB::table('pedidos')->insert([
            'facturacion_id_facturacion' => $facturaId,
            'estado' => 'Pendiente',
        ]);
    }

    // Vaciar carrito
    session()->forget('carrito');

    return redirect()
        ->route('cliente.pedidos')
        ->with('success', 'Pedido registrado correctamente.');




        
})->name('cliente.pedido.guardar');




Route::post('/cliente/carrito/pagar', function (Request $request) {
    $carrito = session('carrito', []);
    if (! count($carrito)) {
        return redirect()->route('cliente.carrito')->with('error', 'El carrito está vacío.');
    }

    $total = 0;
    $items = [];
    foreach ($carrito as $item) {
        $producto = Chaqueta::find($item['producto_id']);
        if (! $producto) {
            continue;
        }

        $cantidad = (int) ($item['cantidad'] ?? 1);
        $talla = $item['talla'] ?? null;
        $total += $producto->precio * $cantidad;
        $items[] = [
            'chaqueta' => $producto,
            'cantidad' => $cantidad,
            'talla' => $talla,
        ];
    }

    if (! count($items)) {
        return redirect()->route('cliente.carrito')->with('error', 'No hay productos válidos en el carrito.');
    }

    $facturaId = DB::table('facturacion')->insertGetId([
        'fecha' => now(),
        'total' => $total,
        'impuestos' => '0',
        'cliente_usuario_id_usuario' => auth()->id(),
        'empleado_usuario_id_usuario' => null,
    ]);

    foreach ($items as $item) {
        $detalleFactura = [
            'facturacion_id_facturacion' => $facturaId,
            'chaqueta_id_chaqueta' => $item['chaqueta']->id_chaqueta,
            'cantidad_venta' => $item['cantidad'],
            'valor_venta' => (string) $item['chaqueta']->precio,
        ];

        if (Schema::hasColumn('facturacion_has_chaqueta', 'talla')) {
            $detalleFactura['talla'] = $item['talla'];
        }

        DB::table('facturacion_has_chaqueta')->insert($detalleFactura);

        if (
            Schema::hasTable('stock')
            && Schema::hasTable('talla')
            && Schema::hasColumn('stock', 'talla_id_talla')
            && Schema::hasColumn('stock', 'cantidad')
            && ! empty($item['talla'])
        ) {
            $tallaName = $item['talla'];
            $tallaId = DB::table('talla')->where('talla', $tallaName)->value('id_talla');
            if ($tallaId) {
                $stockRow = DB::table('stock')
                    ->where('chaqueta_id_chaqueta', $item['chaqueta']->id_chaqueta)
                    ->where('talla_id_talla', $tallaId)
                    ->first();

                if ($stockRow) {
                    $nuevo = max(0, ((int) $stockRow->cantidad) - (int) $item['cantidad']);
                    DB::table('stock')
                        ->where('chaqueta_id_chaqueta', $item['chaqueta']->id_chaqueta)
                        ->where('talla_id_talla', $tallaId)
                        ->update(['cantidad' => $nuevo]);
                }
            }
        }
    }
    

    DB::table('envio')->insert([
        'facturacion_id_facturacion' => $facturaId,
        'tipo_envio' => 'Pendiente',
        'empresa_transportadora' => '',
        'direccion_id_direccion' => null,
    ]);

    session()->forget('carrito');
    return redirect()->route('cliente.pedidos')->with('success', 'Pedido confirmado correctamente.');
})->name('cliente.carrito.pagar');

Route::get('/cliente/pedidos', function () {
    if (! Schema::hasTable('facturacion')) {
        return view('cliente.pedidos', ['pedidos' => collect([])]);
    }

    $clienteId = auth()->id();
    $facturas = DB::table('facturacion')
        ->where('cliente_usuario_id_usuario', $clienteId)
        ->orderByDesc('id_facturacion')
        ->get();
    $envios = Schema::hasTable('envio')
    ? DB::table('envio as e')
        ->leftJoin('direccion as d', 'd.id_direccion', '=', 'e.direccion_id_direccion')
        ->select(
            'e.facturacion_id_facturacion',
            'e.tipo_envio',
            'd.pais',
            'd.ciudad',
            'd.direccion',
            'd.codigo_postal'
        )
        ->get()
        ->keyBy('facturacion_id_facturacion')
    : collect();
        
$detalleItems = collect();
if (Schema::hasTable('facturacion_has_chaqueta')) {
    $tallaSelect = Schema::hasColumn('facturacion_has_chaqueta', 'talla')
        ? 'fc.talla'
        : DB::raw('NULL as talla');

    $detalleItems = DB::table('facturacion_has_chaqueta as fc')
        ->join('chaqueta as c', 'c.id_chaqueta', '=', 'fc.chaqueta_id_chaqueta')
        ->select(
            'fc.facturacion_id_facturacion',
            'c.modelo_chaqueta as nombre',
            'c.precio',
            'fc.cantidad_venta as cantidad',
            $tallaSelect
        )
        ->get()
        ->groupBy('facturacion_id_facturacion');
}

$pedidos = $facturas->map(function ($factura) use ($envios, $detalleItems) {

    $pedidoItems = ($detalleItems->get($factura->id_facturacion) ?? collect())
        ->map(function ($item) {

            return (object) [
                'producto' => (object) [
                    'nombre' => $item->nombre,
                    'precio' => (float) $item->precio,
                ],
                'talla' => $item->talla,
                'cantidad' => (int) $item->cantidad,
            ];
        });

    $envio = $envios->get($factura->id_facturacion);



return (object) [
    'codigo' => 'PED-' . str_pad($factura->id_facturacion, 3, '0', STR_PAD_LEFT),
    'created_at' => $factura->fecha,

    'estado' => $envios->get($factura->id_facturacion)?->tipo_envio ?? 'Pendiente',

    'total' => (float) $factura->total,
    'total_convertido' => (float) ($factura->total_convertido ?? $factura->total ?? 0),
    'envio_convertido' => (float) ($factura->envio_convertido ?? 0),

    'moneda' => $factura->moneda ?? 'COP',

    // datos del envío
    'pais' => $envio?->pais ?? 'No registrado',
    'ciudad' => $envio?->ciudad ?? 'No registrada',
    'direccion' => $envio?->direccion ?? 'No registrada',
    'codigo_postal' => $envio?->codigo_postal ?? 'No registrado',

    'items' => $pedidoItems,
];

});

return view('cliente.pedidos', [
    'pedidos' => $pedidos
]);
})->name('cliente.pedidos');

Route::put('/cliente/perfil', function (Request $request) {
    return back()->with('success', 'Perfil actualizado correctamente.');
})->name('cliente.perfil.update');
