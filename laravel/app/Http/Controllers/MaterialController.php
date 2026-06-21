<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class MaterialController extends Controller
{
    public function index()
    {
        $materiales = Material::query()
            ->when(Schema::hasColumn('materiales', 'estado'), fn ($query) => $query->where('estado', 'Activo'))
            ->get()
            ->map(function($m) {
            return [
                'id_materiales' => $m->id_materiales,
                'material'      => $m->material,
                'precio'        => (float) $m->precio,
                'cantidad'      => (int) $m->cantidad,
            ];
        });
        return response()->json($materiales);
    }
}
