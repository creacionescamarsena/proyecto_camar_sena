<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InventarioController extends Controller
{
    public function index(Request $request)
    {
        $buscar = trim((string) $request->query('buscar', ''));

        $ventasTotales = Schema::hasTable('facturacion')
            ? (float) DB::table('facturacion')->sum('total')
            : 0;

        $enviosPendientes = Schema::hasTable('envio')
            ? (int) DB::table('envio')->where('tipo_envio', 'Pendiente')->count()
            : 0;

        $totalProductos = Schema::hasTable('chaqueta')
            ? (int) DB::table('chaqueta')
                ->when(Schema::hasColumn('chaqueta', 'estado'), fn ($query) => $query->where('estado', 'Activo'))
                ->count()
            : 0;

        $tallasList = $this->tallasList();
        $inventario = Schema::hasTable('chaqueta')
            ? $this->inventario($buscar, $tallasList)
            : collect([]);

        return view('admin.inventario.index', compact(
            'ventasTotales',
            'enviosPendientes',
            'totalProductos',
            'inventario',
            'tallasList'
        ));
    }

    public function create()
    {
        return view('admin.inventario.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.inventario.index');
    }

    private function tallasList()
    {
        if (Schema::hasTable('talla')) {
            return DB::table('talla')
                ->orderBy(Schema::hasColumn('talla', 'orden') ? 'orden' : 'id_talla')
                ->get()
                ->mapWithKeys(fn ($talla) => [$talla->id_talla => $talla->talla]);
        }

        if (Schema::hasTable('stock') && Schema::hasColumn('stock', 'talla')) {
            return DB::table('stock')
                ->select('talla')
                ->whereNotNull('talla')
                ->distinct()
                ->orderBy('talla')
                ->pluck('talla', 'talla');
        }

        return collect([]);
    }

    private function inventario(string $buscar, $tallasList)
    {
        return DB::table('chaqueta as c')
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
            ->map(fn ($chaqueta) => $this->mapInventario($chaqueta, $tallasList));
    }

    private function mapInventario(object $chaqueta, $tallasList): object
    {
        $stockPorTalla = $this->stockPorTalla($chaqueta->id_chaqueta);
        $stockTotal = $stockPorTalla->sum();

        $resultado = (object) [
            'id' => $chaqueta->id_chaqueta,
            'nombre' => $chaqueta->modelo_chaqueta,
            'categoria' => $chaqueta->tipo_categoria ?? 'Sin categoria',
            'stock_total' => $stockTotal,
            'estado_stock' => $stockTotal >= 10 ? 'Normal' : 'Bajo',
            'stock_por_talla' => [],
        ];

        foreach ($tallasList as $tallaId => $tallaNombre) {
            $resultado->stock_por_talla[$tallaNombre] = $stockPorTalla[$tallaId] ?? 0;
        }

        return $resultado;
    }

    private function stockPorTalla(int $chaquetaId)
    {
        if (! Schema::hasTable('stock')) {
            return collect([]);
        }

        if (Schema::hasColumn('stock', 'talla_id_talla')) {
            return DB::table('stock')
                ->where('chaqueta_id_chaqueta', $chaquetaId)
                ->get()
                ->keyBy('talla_id_talla')
                ->mapWithKeys(fn ($stock, $tallaId) => [$tallaId => $stock->cantidad ?? 0]);
        }

        if (Schema::hasColumn('stock', 'talla')) {
            return DB::table('stock')
                ->where('chaqueta_id_chaqueta', $chaquetaId)
                ->select('talla', DB::raw('COUNT(*) as cantidad'))
                ->groupBy('talla')
                ->pluck('cantidad', 'talla');
        }

        return collect([]);
    }
}
