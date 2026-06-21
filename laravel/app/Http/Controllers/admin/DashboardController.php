<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chaqueta;
use App\Models\Material;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $ventasTotales = Schema::hasTable('facturacion')
            ? (float) DB::table('facturacion')->sum('total')
            : 0;

        $enviosPendientes = 0;
        $enviosRecientes = collect([]);
        if (Schema::hasTable('envio')) {
            $enviosPendientes = (int) DB::table('envio')
                ->whereRaw('LOWER(tipo_envio) LIKE ?', ['%pendiente%'])
                ->count();

            $enviosRecientes = $this->enviosRecientes();
        }

        $totalProductos = 0;
        $resumenVentas = collect([]);
        if (Schema::hasTable('chaqueta')) {
            $totalProductos = Chaqueta::query()
                ->when(Schema::hasColumn('chaqueta', 'estado'), fn ($query) => $query->where('estado', 'Activo'))
                ->count();

            $resumenVentas = Chaqueta::with('categoria')
                ->when(Schema::hasColumn('chaqueta', 'estado'), fn ($query) => $query->where('estado', 'Activo'))
                ->orderByDesc('id_chaqueta')
                ->limit(5)
                ->get()
                ->map(fn ($producto) => $this->mapProductoResumen($producto));
        }

        $stockBajo = 0;
        $materialesStockBajo = collect([]);
        if (Schema::hasTable('materiales')) {
            $stockBajo = Material::where('cantidad', '<', 10)
                ->when(Schema::hasColumn('materiales', 'estado'), fn ($query) => $query->where('estado', 'Activo'))
                ->count();

            $materialesStockBajo = Material::where('cantidad', '<', 10)
                ->when(Schema::hasColumn('materiales', 'estado'), fn ($query) => $query->where('estado', 'Activo'))
                ->orderBy('cantidad')
                ->limit(5)
                ->get()
                ->map(fn ($material) => (object) [
                    'id' => $material->id_materiales,
                    'id_materiales' => $material->id_materiales,
                    'nombre' => $material->material,
                    'cantidad' => (int) $material->cantidad,
                ]);
        }

        return view('admin.dashboard', compact(
            'ventasTotales',
            'enviosPendientes',
            'totalProductos',
            'stockBajo',
            'enviosRecientes',
            'materialesStockBajo',
            'resumenVentas'
        ));
    }

    private function enviosRecientes()
    {
        $query = DB::table('envio as e')
            ->select('e.id_envio', 'e.tipo_envio', 'e.empresa_transportadora')
            ->orderByDesc('e.id_envio')
            ->limit(3);

        if (Schema::hasTable('facturacion')) {
            $query->leftJoin('facturacion as f', 'f.id_facturacion', '=', 'e.facturacion_id_facturacion')
                ->addSelect('f.total as total_factura');
        } else {
            $query->addSelect(DB::raw('0 as total_factura'));
        }

        if (Schema::hasTable('cliente') && Schema::hasTable('usuario')) {
            $query->leftJoin('cliente as c', 'c.usuario_id_usuario', '=', 'f.cliente_usuario_id_usuario')
                ->leftJoin('usuario as u', 'u.id_usuario', '=', 'c.usuario_id_usuario')
                ->addSelect('u.nombres', 'u.apellidos', 'u.correo');
        } else {
            $query->addSelect(DB::raw('NULL as nombres'), DB::raw('NULL as apellidos'), DB::raw('NULL as correo'));
        }

        return $query->get()->map(function ($envio) {
            $nombreCliente = trim(($envio->nombres ?? '') . ' ' . ($envio->apellidos ?? ''));

            return (object) [
                'cliente' => $nombreCliente !== '' ? $nombreCliente : ($envio->correo ?? 'Cliente desconocido'),
                'producto' => $envio->empresa_transportadora ?: 'Envio sin empresa',
                'estado' => $envio->tipo_envio ?: 'Pendiente',
                'total' => (float) $envio->total_factura,
            ];
        });
    }

    private function mapProductoResumen(Chaqueta $producto): object
    {
        $stockCantidad = 0;
        if (Schema::hasTable('stock')) {
            $stockQuery = DB::table('stock')->where('chaqueta_id_chaqueta', $producto->id_chaqueta);
            $stockCantidad = Schema::hasColumn('stock', 'cantidad')
                ? (int) $stockQuery->sum('cantidad')
                : (int) $stockQuery->count();
        }

        return (object) [
            'nombre' => $producto->modelo_chaqueta,
            'categoria' => $producto->categoria?->tipo_categoria ?? 'Sin categoria',
            'precio' => (float) $producto->precio,
            'stock' => $stockCantidad,
            'estado' => $stockCantidad > 0 ? 'Disponible' : 'Sin stock',
        ];
    }
}
