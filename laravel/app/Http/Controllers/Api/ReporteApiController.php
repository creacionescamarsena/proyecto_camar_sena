<?php

namespace App\Http\Controllers\Api;

use App\Models\Chaqueta;
use App\Models\Usuario;
use App\Models\Material;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\Controller;

class ReporteApiController extends Controller
{
    /**
     * GET /api/reportes/ventas
     * Reporte de ventas por mes
     */
    public function ventasPorMes(Request $request)
    {
        // Verificar permiso - solo Admin y Empleado
        if (!in_array($request->user()->rol, ['Admin', 'Empleado'])) {
            return response()->json([
                'message' => 'No tienes permisos para ver reportes',
            ], 403);
        }

        $ventasPorMes = [];

        if (Schema::hasTable('facturacion')) {
            $ventasPorMes = DB::table('facturacion')
                ->selectRaw('MONTH(fecha) as mes, YEAR(fecha) as anio, SUM(total) as total_mes, COUNT(*) as cantidad_facturas')
                ->groupByRaw('YEAR(fecha), MONTH(fecha)')
                ->orderByRaw('YEAR(fecha) DESC, MONTH(fecha) DESC')
                ->limit(12)
                ->get()
                ->map(function($item) {
                    $meses = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 
                             'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                    return [
                        'mes_nombre' => $meses[$item->mes] . ' ' . $item->anio,
                        'mes' => $item->mes,
                        'anio' => $item->anio,
                        'total_ventas' => (float)$item->total_mes,
                        'cantidad_facturas' => (int)$item->cantidad_facturas,
                    ];
                })
                ->toArray();
        }

        $totalGeneral = array_sum(array_column($ventasPorMes, 'total_ventas'));

        return response()->json([
            'message' => 'Reporte de ventas obtenido exitosamente',
            'total_general' => $totalGeneral,
            'datos' => $ventasPorMes,
        ], 200);
    }

    /**
     * GET /api/reportes/stock
     * Reporte de disponibilidad de stock
     */
    public function stockDisponible(Request $request)
    {
        if (!in_array($request->user()->rol, ['Admin', 'Empleado'])) {
            return response()->json([
                'message' => 'No tienes permisos para ver reportes',
            ], 403);
        }

        $reporteStock = Chaqueta::with(['stock.talla', 'categoria'])
            ->when(Schema::hasColumn('chaqueta', 'estado'), fn ($query) => $query->where('estado', 'Activo'))
            ->get()
            ->map(function($chaqueta) {
                $totalStock = $chaqueta->stock->sum(function($s) {
                    return (int)$s->cantidad;
                });

                return [
                    'id_chaqueta' => $chaqueta->id_chaqueta,
                    'modelo' => $chaqueta->modelo_chaqueta,
                    'categoria' => $chaqueta->categoria?->tipo_categoria,
                    'precio' => (float)$chaqueta->precio,
                    'total_stock' => $totalStock,
                    'estado' => $totalStock > 0 ? 'Disponible' : 'Agotado',
                    'stock_por_talla' => $chaqueta->stock->map(function($s) {
                        return [
                            'talla' => $s->talla->talla ?? 'N/A',
                            'cantidad' => (int)$s->cantidad,
                        ];
                    })->toArray(),
                ];
            })
            ->toArray();

        $resumen = [
            'total_productos' => count($reporteStock),
            'productos_disponibles' => count(array_filter($reporteStock, fn($p) => $p['total_stock'] > 0)),
            'productos_agotados' => count(array_filter($reporteStock, fn($p) => $p['total_stock'] == 0)),
            'stock_total' => array_sum(array_column($reporteStock, 'total_stock')),
        ];

        return response()->json([
            'message' => 'Reporte de stock obtenido exitosamente',
            'resumen' => $resumen,
            'datos' => $reporteStock,
        ], 200);
    }

    /**
     * GET /api/reportes/usuarios
     * Reporte de usuarios por rol
     */
    public function usuariosPorRol(Request $request)
    {
        // Solo Admin
        if ($request->user()->rol !== 'Admin') {
            return response()->json([
                'message' => 'No tienes permisos para ver este reporte',
            ], 403);
        }

        $usuariosPorRol = Usuario::selectRaw('rol, COUNT(*) as cantidad, SUM(CASE WHEN estado = "Activo" THEN 1 ELSE 0 END) as activos')
            ->groupBy('rol')
            ->get()
            ->map(function($item) {
                return [
                    'rol' => $item->rol,
                    'total_usuarios' => (int)$item->cantidad,
                    'usuarios_activos' => (int)$item->activos,
                    'usuarios_inactivos' => (int)($item->cantidad - $item->activos),
                ];
            })
            ->toArray();

        $totalUsuarios = Usuario::count();
        $usuariosActivos = Usuario::where('estado', 'Activo')->count();

        return response()->json([
            'message' => 'Reporte de usuarios obtenido exitosamente',
            'resumen' => [
                'total_usuarios' => $totalUsuarios,
                'usuarios_activos' => $usuariosActivos,
                'usuarios_inactivos' => $totalUsuarios - $usuariosActivos,
            ],
            'datos' => $usuariosPorRol,
        ], 200);
    }

    /**
     * GET /api/reportes/productos-mas-vendidos
     * Reporte de productos más vendidos
     */
    public function productosMasVendidos(Request $request)
    {
        if (!in_array($request->user()->rol, ['Admin', 'Empleado'])) {
            return response()->json([
                'message' => 'No tienes permisos para ver reportes',
            ], 403);
        }

        $productosMasVendidos = [];

        if (Schema::hasTable('facturacion_has_chaqueta')) {
            $productosMasVendidos = DB::table('facturacion_has_chaqueta as fhc')
                ->leftJoin('chaqueta as c', 'c.id_chaqueta', '=', 'fhc.chaqueta_id_chaqueta')
                ->selectRaw('fhc.chaqueta_id_chaqueta, c.modelo_chaqueta, COUNT(*) as cantidad_vendida, SUM(fhc.cantidad) as total_items')
                ->groupBy('fhc.chaqueta_id_chaqueta', 'c.modelo_chaqueta')
                ->orderByDesc('total_items')
                ->limit(10)
                ->get()
                ->map(function($item) {
                    return [
                        'id_chaqueta' => $item->chaqueta_id_chaqueta,
                        'modelo' => $item->modelo_chaqueta ?? 'Desconocido',
                        'veces_vendida' => (int)$item->cantidad_vendida,
                        'total_unidades_vendidas' => (int)$item->total_items,
                    ];
                })
                ->toArray();
        }

        return response()->json([
            'message' => 'Reporte de productos más vendidos obtenido exitosamente',
            'datos' => $productosMasVendidos,
        ], 200);
    }

    /**
     * GET /api/reportes/exportar/chaquetas
     * Exportar lista de chaquetas en formato JSON/CSV
     */
    public function exportarChaquetas(Request $request)
    {
        if (!in_array($request->user()->rol, ['Admin', 'Empleado'])) {
            return response()->json([
                'message' => 'No tienes permisos para exportar reportes',
            ], 403);
        }

        $formato = $request->input('formato', 'json'); // json o csv

        $chaquetas = Chaqueta::with(['categoria', 'materiales', 'stock'])
            ->when(Schema::hasColumn('chaqueta', 'estado'), fn ($query) => $query->where('estado', 'Activo'))
            ->get()
            ->map(function($chaqueta) {
                return [
                    'id' => $chaqueta->id_chaqueta,
                    'modelo' => $chaqueta->modelo_chaqueta,
                    'categoria' => $chaqueta->categoria?->tipo_categoria,
                    'precio' => (float)$chaqueta->precio,
                    'stock_total' => $chaqueta->stock->sum(fn($s) => (int)$s->cantidad),
                    'materiales' => $chaqueta->materiales->pluck('material')->join(', '),
                ];
            });

        if ($formato === 'csv') {
            return $this->generarCSV($chaquetas->toArray());
        }

        return response()->json([
            'message' => 'Reporte de chaquetas exportado exitosamente',
            'formato' => 'json',
            'cantidad_registros' => $chaquetas->count(),
            'datos' => $chaquetas->toArray(),
        ], 200);
    }

    /**
     * Generar CSV
     */
    private function generarCSV($datos)
    {
        if (empty($datos)) {
            return response('', 204);
        }

        $headers = array_keys($datos[0]);
        $csvContent = implode(',', array_map(fn($h) => '"' . $h . '"', $headers)) . "\n";

        foreach ($datos as $row) {
            $csvContent .= implode(',', array_map(fn($v) => '"' . str_replace('"', '""', $v) . '"', $row)) . "\n";
        }

        return response($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="chaquetas_' . date('Y-m-d_His') . '.csv"',
        ]);
    }
}
