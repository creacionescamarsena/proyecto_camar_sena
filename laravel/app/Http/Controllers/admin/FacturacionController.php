<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FacturacionController extends Controller
{
    public function index()
    {
        $totalFacturadoValor = 0;
        $totalPagadoValor = 0;
        $facturas = collect([]);

        if (Schema::hasTable('facturacion')) {
            $totalFacturadoValor = (float) DB::table('facturacion')->sum('total');
            $totalPagadoValor = Schema::hasTable('facturacion_has_metodo_pago')
                ? (float) DB::table('facturacion_has_metodo_pago')->sum('monto')
                : 0;

            $facturas = $this->facturas();
        }

        $totalPendienteValor = max($totalFacturadoValor - $totalPagadoValor, 0);

        return view('admin.facturacion', [
            'totalFacturado' => '$' . number_format($totalFacturadoValor, 0, ',', '.'),
            'totalPagado' => '$' . number_format($totalPagadoValor, 0, ',', '.'),
            'totalPendiente' => '$' . number_format($totalPendienteValor, 0, ',', '.'),
            'facturas' => $facturas,
        ]);
    }

    private function facturas()
    {
        $query = DB::table('facturacion as f')
            ->select(
                'f.id_facturacion',
                'f.fecha',
                'f.total',
                DB::raw('NULL as nombres'),
                DB::raw('NULL as apellidos'),
                DB::raw('NULL as correo'),
                DB::raw('0 as pagado')
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

        return $query->get()->map(function ($factura) {
            $cliente = trim(($factura->nombres ?? '') . ' ' . ($factura->apellidos ?? ''));
            if ($cliente === '') {
                $cliente = $factura->correo ?? 'Cliente #' . $factura->id_facturacion;
            }

            $fecha = $factura->fecha ? new \DateTime($factura->fecha) : null;
            $total = (float) $factura->total;
            $pagado = (float) ($factura->pagado ?? 0);

            return (object) [
                'numero' => 'FAC-' . str_pad($factura->id_facturacion, 4, '0', STR_PAD_LEFT),
                'cliente' => $cliente,
                'fecha_emision' => $fecha ? $fecha->format('d/m/Y') : 'sin fecha',
                'fecha_vencimiento' => $fecha ? (clone $fecha)->add(new \DateInterval('P30D'))->format('d/m/Y') : 'sin fecha',
                'monto' => $total,
                'estado' => $pagado >= $total && $total > 0 ? 'Pagado' : 'Pendiente',
            ];
        });
    }
}
