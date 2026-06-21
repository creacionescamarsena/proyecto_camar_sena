<?php

namespace App\Http\Controllers\Empleado;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $enviosPendientes = Schema::hasTable('envio')
            ? DB::table('envio')->where('tipo_envio', 'Pendiente')->count()
            : 0;

        $enTransito = Schema::hasTable('envio')
            ? DB::table('envio')->where('tipo_envio', 'En trÃ¡nsito')->count()
            : 0;

        $productosDisponibles = Schema::hasTable('chaqueta')
            ? DB::table('chaqueta')
                ->when(Schema::hasColumn('chaqueta', 'estado'), fn ($query) => $query->where('estado', 'Activo'))
                ->count()
            : 0;

        $entregadosHoy = Schema::hasTable('envio')
            ? DB::table('envio')->where('tipo_envio', 'Entregado')->count()
            : 0;

        return view('empleado.dashboard', [
            'enviosPendientes' => $enviosPendientes,
            'enTransito' => $enTransito,
            'productosDisponibles' => $productosDisponibles,
            'entregadosHoy' => $entregadosHoy,
            'pedidosActivos' => $this->pedidosActivos(),
        ]);
    }

    private function pedidosActivos()
    {
        if (! Schema::hasTable('envio') || ! Schema::hasTable('facturacion')) {
            return collect();
        }

        $query = DB::table('envio as e')
            ->leftJoin('facturacion as f', 'f.id_facturacion', '=', 'e.facturacion_id_facturacion')
            ->select(
                'e.id_envio',
                'e.tipo_envio',
                'e.empresa_transportadora',
                'f.id_facturacion',
                'f.fecha',
                'f.total as total_factura'
            )
            ->whereIn('e.tipo_envio', ['Pendiente', 'En proceso', 'Enviado', 'En trÃ¡nsito'])
            ->orderByDesc('e.id_envio')
            ->limit(3);

        if (Schema::hasTable('usuario')) {
            $query->leftJoin('usuario as u', 'u.id_usuario', '=', 'f.cliente_usuario_id_usuario')
                ->addSelect('u.nombres', 'u.apellidos', 'u.correo');
        } else {
            $query->addSelect(DB::raw('NULL as nombres'), DB::raw('NULL as apellidos'), DB::raw('NULL as correo'));
        }

        return $query->get()->map(function ($envio) {
            $fechaFormato = $envio->fecha ? (new \DateTime($envio->fecha))->format('d/m/Y') : 'sin fecha';
            $nombreCliente = trim(($envio->nombres ?? '') . ' ' . ($envio->apellidos ?? ''));

            if ($nombreCliente === '') {
                $nombreCliente = $envio->correo ?? 'Cliente #' . ($envio->id_facturacion ?? $envio->id_envio);
            }

            return (object) [
                'id' => $envio->id_envio,
                'codigo' => 'PED-' . str_pad($envio->id_facturacion ?? $envio->id_envio, 3, '0', STR_PAD_LEFT),
                'estado' => $envio->tipo_envio,
                'cliente' => $nombreCliente,
                'descripcion' => 'EnvÃ­o del ' . $fechaFormato,
                'fecha_envio' => $fechaFormato,
                'valor' => (float) ($envio->total_factura ?? 0),
            ];
        });
    }
}
