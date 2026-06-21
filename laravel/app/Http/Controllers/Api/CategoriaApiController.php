<?php

namespace App\Http\Controllers\Api;

use App\Models\Categoria;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoriaApiController extends Controller
{
    /**
     * GET /api/categorias
     * Obtener todas las categorías
     */
    public function index()
    {
        $categorias = Categoria::with('chaquetas')
            ->where('estado_categoria', 1)
            ->paginate(20);

        return response()->json([
            'message' => 'Categorías obtenidas exitosamente',
            'data' => $categorias->items(),
            'pagination' => [
                'total' => $categorias->total(),
                'per_page' => $categorias->perPage(),
                'current_page' => $categorias->currentPage(),
                'last_page' => $categorias->lastPage(),
            ],
        ], 200);
    }

    /**
     * POST /api/categorias
     * Crear nueva categoría (solo Admin/Empleado)
     */
    public function store(Request $request)
    {
        if (!in_array($request->user()->rol, ['Admin', 'Empleado'])) {
            return response()->json([
                'message' => 'No tienes permisos para crear categorías',
            ], 403);
        }

        $validated = $request->validate([
            'tipo_categoria' => ['required', 'string', 'max:100', 'unique:categoria,tipo_categoria'],
            'estado_categoria' => ['sometimes', 'boolean'],
        ]);

        try {
            $categoria = Categoria::create([
                'tipo_categoria' => $validated['tipo_categoria'],
                'estado_categoria' => $validated['estado_categoria'] ?? true,
            ]);

            return response()->json([
                'message' => 'Categoría creada exitosamente',
                'data' => $categoria,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear la categoría',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/categorias/{id}
     * Obtener detalle de una categoría
     */
    public function show($id)
    {
        $categoria = Categoria::with('chaquetas')->find($id);

        if (!$categoria) {
            return response()->json([
                'message' => 'Categoría no encontrada',
            ], 404);
        }

        return response()->json([
            'message' => 'Categoría obtenida exitosamente',
            'data' => $categoria,
        ], 200);
    }

    /**
     * PUT /api/categorias/{id}
     * Actualizar categoría (solo Admin/Empleado)
     */
    public function update(Request $request, $id)
    {
        if (!in_array($request->user()->rol, ['Admin', 'Empleado'])) {
            return response()->json([
                'message' => 'No tienes permisos para actualizar categorías',
            ], 403);
        }

        $categoria = Categoria::find($id);

        if (!$categoria) {
            return response()->json([
                'message' => 'Categoría no encontrada',
            ], 404);
        }

        $validated = $request->validate([
            'tipo_categoria' => ['sometimes', 'string', 'max:100', 'unique:categoria,tipo_categoria,' . $id . ',id_categoria'],
            'estado_categoria' => ['sometimes', 'boolean'],
        ]);

        try {
            $categoria->update($validated);

            return response()->json([
                'message' => 'Categoría actualizada exitosamente',
                'data' => $categoria,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar la categoría',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * DELETE /api/categorias/{id}
     * Eliminar categoría (solo Admin)
     */
    public function destroy(Request $request, $id)
    {
        if ($request->user()->rol !== 'Admin') {
            return response()->json([
                'message' => 'No tienes permisos para eliminar categorías',
            ], 403);
        }

        $categoria = Categoria::find($id);

        if (!$categoria) {
            return response()->json([
                'message' => 'Categoría no encontrada',
            ], 404);
        }

        try {
            $categoria->update(['estado_categoria' => 0]);

            return response()->json([
                'message' => 'Categoría inactivada exitosamente',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar la categoría',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
