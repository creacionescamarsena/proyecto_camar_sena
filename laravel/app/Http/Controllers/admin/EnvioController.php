<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EnvioController extends Controller
{
    public function index(Request $request)
    {
        $buscar = trim((string) $request->query('buscar', ''));
        $envios = Schema::hasTable('envio') ? $this->envios($buscar) : collect([]);

        $totalEnvios = $envios->count();
        $enTransito = $envios->where('tipo_envio', 'En transito')->count() + $envios->where('tipo_envio', 'En trÃ¡nsito')->count();
        $entregados = $envios->where('tipo_envio', 'Entregado')->count();

        return view('admin.envios', compact('totalEnvios', 'enTransito', 'entregados', 'envios'));
    }

    private function envios(string $buscar)
    {
        $query = DB::table('envio as e')
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
            ->distinct();

        if ($buscar !== '') {
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
        }

        return $query->get()->map(function ($envio) {
            $clienteNombre = trim(($envio->cliente_nombres ?? '') . ' ' . ($envio->cliente_apellidos ?? ''));
            $clientePartes = array_filter([$clienteNombre, $envio->cliente_tipo_documento ?? null]);

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
}
