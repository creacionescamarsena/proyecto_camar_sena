<?php

namespace App\Http\Controllers;

use App\Models\Chaqueta;
use App\Models\Categoria;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ChaquetaController extends Controller
{
    public function index()
    {
        return response()->json(
            Chaqueta::query()
                ->when(Schema::hasColumn('chaqueta', 'estado'), fn ($query) => $query->where('estado', 'Activo'))
                ->get()
        );
    }

    public function store(Request $request)
    {
        $categoriaId = $request->categoria_id_categoria;

        if ($request->filled('nueva_categoria') && !$categoriaId) {
            $cat = Categoria::create([
                'tipo_categoria'   => $request->nueva_categoria,
                'estado_categoria' => 1,
            ]);
            $categoriaId = $cat->id_categoria;
        }

        $chaqueta = Chaqueta::create([
            'modelo_chaqueta'        => $request->modelo_chaqueta,
            'precio'                 => $request->precio,
            'estado'                 => $request->input('estado', 'Activo'),
            'categoria_id_categoria' => $categoriaId,
        ]);

        if ($request->has('materiales') && count($request->materiales) > 0) {
            $chaqueta->materiales()->sync($request->materiales);
        }

        if ($request->has('stock')) {
            foreach ($request->stock as $nombreTalla => $cantidad) {
                if ($cantidad > 0) {
                    $talla = \App\Models\Talla::where('talla', $nombreTalla)->first();
                    if ($talla) {
                        Stock::create([
                            'chaqueta_id_chaqueta' => $chaqueta->id_chaqueta,
                            'talla_id_talla'       => $talla->id_talla,
                            'cantidad'             => $cantidad,
                        ]);
                    }
                }
            }
        }

        return response()->json(['status' => 'success', 'data' => $chaqueta], 201);
    }
}
