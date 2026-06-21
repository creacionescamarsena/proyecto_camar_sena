<?php

namespace App\Http\Controllers\Api;

use App\Models\Stock;
use App\Models\Chaqueta;
use App\Models\Talla;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StockApiController extends Controller
{
    /**
     * GET /api/stock
     * Obtener todos los registros de stock
     */
    public function index(Request $request)
    {
        $query = Stock::with(['chaqueta', 'talla']);

        // Filtro por chaqueta
        if ($request->has('chaqueta_id')) {
            $query->where('chaqueta_id_chaqueta', $request->chaqueta_id);
        }

        // Filtro por disponibilidad baja
        if ($request->has('bajo_stock') && $request->bajo_stock === 'true') {
            $query->where('cantidad', '<', 10);
        }

        $stock = $query->paginate(20);

        return response()->json([
            'message' => 'Stock obtenido exitosamente',
            'data' => $stock->items(),
            'pagination' => [
                'total' => $stock->total(),
                'per_page' => $stock->perPage(),
                'current_page' => $stock->currentPage(),
                'last_page' => $stock->lastPage(),
            ],
        ], 200);
    }

    /**
     * POST /api/stock
     * Crear nuevo registro de stock (solo Admin/Empleado)
     */
    public function store(Request $request)
    {
        if (!in_array($request->user()->rol, ['Admin', 'Empleado'])) {
            return response()->json([
                'message' => 'No tienes permisos para crear registros de stock',
            ], 403);
        }

        $validated = $request->validate([
            'chaqueta_id_chaqueta' => ['required', 'integer', 'exists:chaqueta,id_chaqueta'],
            'talla_id_talla' => ['required', 'integer', 'exists:talla,id_talla'],
            'cantidad' => ['required', 'integer', 'min:0'],
        ]);

        try {
            // Verificar si ya existe stock para esta combinación
            $existing = Stock::where('chaqueta_id_chaqueta', $validated['chaqueta_id_chaqueta'])
                ->where('talla_id_talla', $validated['talla_id_talla'])
                ->first();

            if ($existing) {
                return response()->json([
                    'message' => 'Ya existe un registro de stock para esta combinación de chaqueta y talla',
                ], 409);
            }

            $stock = Stock::create($validated);
            $stock->load(['chaqueta', 'talla']);

            return response()->json([
                'message' => 'Registro de stock creado exitosamente',
                'data' => $stock,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear el registro de stock',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/stock/{id}
     * Obtener detalle de un registro de stock
     */
    public function show($id)
    {
        $stock = Stock::with(['chaqueta', 'talla'])->find($id);

        if (!$stock) {
            return response()->json([
                'message' => 'Registro de stock no encontrado',
            ], 404);
        }

        return response()->json([
            'message' => 'Registro de stock obtenido exitosamente',
            'data' => $stock,
        ], 200);
    }

    /**
     * PUT /api/stock/{id}
     * Actualizar cantidad de stock (solo Admin/Empleado)
     */
    public function update(Request $request, $id)
    {
        if (!in_array($request->user()->rol, ['Admin', 'Empleado'])) {
            return response()->json([
                'message' => 'No tienes permisos para actualizar stock',
            ], 403);
        }

        $stock = Stock::find($id);

        if (!$stock) {
            return response()->json([
                'message' => 'Registro de stock no encontrado',
            ], 404);
        }

        $validated = $request->validate([
            'cantidad' => ['required', 'integer', 'min:0'],
        ]);

        try {
            $stock->update($validated);
            $stock->load(['chaqueta', 'talla']);

            return response()->json([
                'message' => 'Registro de stock actualizado exitosamente',
                'data' => $stock,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar el stock',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * DELETE /api/stock/{id}
     * Eliminar registro de stock (solo Admin)
     */
    public function destroy(Request $request, $id)
    {
        if ($request->user()->rol !== 'Admin') {
            return response()->json([
                'message' => 'No tienes permisos para eliminar registros de stock',
            ], 403);
        }

        $stock = Stock::find($id);

        if (!$stock) {
            return response()->json([
                'message' => 'Registro de stock no encontrado',
            ], 404);
        }

        try {
            $stock->update(['cantidad' => 0]);

            return response()->json([
                'message' => 'Registro de stock dejado en cero exitosamente',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar el stock',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
