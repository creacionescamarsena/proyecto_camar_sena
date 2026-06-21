<?php

namespace App\Http\Controllers\Api;

use App\Models\Chaqueta;
use App\Models\Categoria;
use App\Models\Stock;
use App\Models\Talla;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\Controller;

class ChaquetaApiController extends Controller
{
    /**
     * GET /api/chaquetas
     * Obtener lista de todas las chaquetas
     */
    public function index(Request $request)
    {
        $query = Chaqueta::with(['categoria', 'materiales', 'stock'])
            ->when(Schema::hasColumn('chaqueta', 'estado'), fn ($query) => $query->where('estado', 'Activo'));

        // Filtros opcionales
        if ($request->has('categoria_id')) {
            $query->where('categoria_id_categoria', $request->categoria_id);
        }

        if ($request->has('precio_min') || $request->has('precio_max')) {
            if ($request->has('precio_min')) {
                $query->where('precio', '>=', $request->precio_min);
            }
            if ($request->has('precio_max')) {
                $query->where('precio', '<=', $request->precio_max);
            }
        }

        $chaquetas = $query->paginate(15);

        return response()->json([
            'message' => 'Chaquetas obtenidas exitosamente',
            'data' => $chaquetas->items(),
            'pagination' => [
                'total' => $chaquetas->total(),
                'per_page' => $chaquetas->perPage(),
                'current_page' => $chaquetas->currentPage(),
                'last_page' => $chaquetas->lastPage(),
            ],
        ], 200);
    }

    /**
     * POST /api/chaquetas
     * Crear nueva chaqueta (solo Admin/Empleado)
     */
    public function store(Request $request)
    {
        // Verificar permisos
        if (!in_array($request->user()->rol, ['Admin', 'Empleado'])) {
            return response()->json([
                'message' => 'No tienes permisos para crear chaquetas',
            ], 403);
        }

        $validated = $request->validate([
            'modelo_chaqueta' => ['required', 'string', 'max:100', 'unique:chaqueta,modelo_chaqueta'],
            'precio' => ['required', 'numeric', 'min:0.01'],
            'categoria_id_categoria' => ['required', 'integer', 'exists:categoria,id_categoria'],
            'estado' => ['sometimes', 'in:Activo,Inactivo'],
            'materiales' => ['sometimes', 'array'],
            'materiales.*' => ['integer', 'exists:materiales,id_materiales'],
            'stock' => ['sometimes', 'array'],
        ]);

        try {
            $chaqueta = Chaqueta::create([
                'modelo_chaqueta' => $validated['modelo_chaqueta'],
                'precio' => $validated['precio'],
                'categoria_id_categoria' => $validated['categoria_id_categoria'],
                'estado' => $validated['estado'] ?? 'Activo',
            ]);

            // Asociar materiales
            if (isset($validated['materiales']) && count($validated['materiales']) > 0) {
                $chaqueta->materiales()->sync($validated['materiales']);
            }

            // Crear registros de stock
            if (isset($validated['stock']) && is_array($validated['stock'])) {
                foreach ($validated['stock'] as $talla_id => $cantidad) {
                    if ((int)$cantidad > 0) {
                        Stock::create([
                            'chaqueta_id_chaqueta' => $chaqueta->id_chaqueta,
                            'talla_id_talla' => $talla_id,
                            'cantidad' => (int)$cantidad,
                        ]);
                    }
                }
            }

            $chaqueta->load(['categoria', 'materiales', 'stock']);

            return response()->json([
                'message' => 'Chaqueta creada exitosamente',
                'data' => $chaqueta,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear la chaqueta',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/chaquetas/{id}
     * Obtener detalle de una chaqueta
     */
    public function show($id)
    {
        $chaqueta = Chaqueta::with(['categoria', 'materiales', 'stock.talla'])->find($id);

        if (!$chaqueta) {
            return response()->json([
                'message' => 'Chaqueta no encontrada',
            ], 404);
        }

        return response()->json([
            'message' => 'Chaqueta obtenida exitosamente',
            'data' => $chaqueta,
        ], 200);
    }

    /**
     * PUT /api/chaquetas/{id}
     * Actualizar chaqueta (solo Admin/Empleado)
     */
    public function update(Request $request, $id)
    {
        // Verificar permisos
        if (!in_array($request->user()->rol, ['Admin', 'Empleado'])) {
            return response()->json([
                'message' => 'No tienes permisos para actualizar chaquetas',
            ], 403);
        }

        $chaqueta = Chaqueta::find($id);

        if (!$chaqueta) {
            return response()->json([
                'message' => 'Chaqueta no encontrada',
            ], 404);
        }

        $validated = $request->validate([
            'modelo_chaqueta' => ['sometimes', 'string', 'max:100', 'unique:chaqueta,modelo_chaqueta,' . $id . ',id_chaqueta'],
            'precio' => ['sometimes', 'numeric', 'min:0.01'],
            'categoria_id_categoria' => ['sometimes', 'integer', 'exists:categoria,id_categoria'],
            'estado' => ['sometimes', 'in:Activo,Inactivo'],
            'materiales' => ['sometimes', 'array'],
            'materiales.*' => ['integer', 'exists:materiales,id_materiales'],
        ]);

        try {
            $chaqueta->update($validated);

            if (isset($validated['materiales'])) {
                $chaqueta->materiales()->sync($validated['materiales']);
            }

            $chaqueta->load(['categoria', 'materiales', 'stock']);

            return response()->json([
                'message' => 'Chaqueta actualizada exitosamente',
                'data' => $chaqueta,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar la chaqueta',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * DELETE /api/chaquetas/{id}
     * Eliminar chaqueta (solo Admin)
     */
    public function destroy(Request $request, $id)
    {
        // Verificar permisos (solo Admin)
        if ($request->user()->rol !== 'Admin') {
            return response()->json([
                'message' => 'No tienes permisos para eliminar chaquetas',
            ], 403);
        }

        $chaqueta = Chaqueta::find($id);

        if (!$chaqueta) {
            return response()->json([
                'message' => 'Chaqueta no encontrada',
            ], 404);
        }

        try {
            $chaqueta->update(['estado' => 'Inactivo']);

            return response()->json([
                'message' => 'Chaqueta inactivada exitosamente',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar la chaqueta',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
