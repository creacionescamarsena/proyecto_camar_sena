<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function index()
    {
        $materiales = Material::all()->map(function($m) {
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