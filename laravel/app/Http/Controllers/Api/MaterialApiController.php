<?php

namespace App\Http\Controllers\Api;

use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\Controller;

class MaterialApiController extends Controller
{
    /**
     * GET /api/materiales
     * Obtener todos los materiales
     */
    public function index(Request $request)
    {
        $query = Material::query()
            ->when(Schema::hasColumn('materiales', 'estado'), fn ($query) => $query->where('estado', 'Activo'));

        // Filtro por disponibilidad
        if ($request->has('disponible')) {
            if ($request->disponible === 'true' || $request->disponible === '1') {
                $query->where('cantidad', '>', 0);
            }
        }

        $materiales = $query->paginate(20);

        return response()->json([
            'message' => 'Materiales obtenidos exitosamente',
            'data' => $materiales->items(),
            'pagination' => [
                'total' => $materiales->total(),
                'per_page' => $materiales->perPage(),
                'current_page' => $materiales->currentPage(),
                'last_page' => $materiales->lastPage(),
            ],
        ], 200);
    }

    /**
     * POST /api/materiales
     * Crear nuevo material (solo Admin/Empleado)
     */
    public function store(Request $request)
    {
        if (!in_array($request->user()->rol, ['Admin', 'Empleado'])) {
            return response()->json([
                'message' => 'No tienes permisos para crear materiales',
            ], 403);
        }

        $validated = $request->validate([
            'material' => ['required', 'string', 'max:100', 'unique:materiales,material'],
            'precio' => ['required', 'numeric', 'min:0'],
            'cantidad' => ['required', 'integer', 'min:0'],
            'estado' => ['sometimes', 'in:Activo,Inactivo'],
            'proveedor_material_cod_proveedor' => ['sometimes', 'string', 'max:50'],
        ]);

        try {
            $material = Material::create($validated);

            return response()->json([
                'message' => 'Material creado exitosamente',
                'data' => $material,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear el material',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/materiales/{id}
     * Obtener detalle de un material
     */
    public function show($id)
    {
        $material = Material::find($id);

        if (!$material) {
            return response()->json([
                'message' => 'Material no encontrado',
            ], 404);
        }

        return response()->json([
            'message' => 'Material obtenido exitosamente',
            'data' => $material,
        ], 200);
    }

    /**
     * PUT /api/materiales/{id}
     * Actualizar material (solo Admin/Empleado)
     */
    public function update(Request $request, $id)
    {
        if (!in_array($request->user()->rol, ['Admin', 'Empleado'])) {
            return response()->json([
                'message' => 'No tienes permisos para actualizar materiales',
            ], 403);
        }

        $material = Material::find($id);

        if (!$material) {
            return response()->json([
                'message' => 'Material no encontrado',
            ], 404);
        }

        $validated = $request->validate([
            'material' => ['sometimes', 'string', 'max:100', 'unique:materiales,material,' . $id . ',id_materiales'],
            'precio' => ['sometimes', 'numeric', 'min:0'],
            'cantidad' => ['sometimes', 'integer', 'min:0'],
            'estado' => ['sometimes', 'in:Activo,Inactivo'],
            'proveedor_material_cod_proveedor' => ['sometimes', 'string', 'max:50'],
        ]);

        try {
            $material->update($validated);

            return response()->json([
                'message' => 'Material actualizado exitosamente',
                'data' => $material,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar el material',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * DELETE /api/materiales/{id}
     * Eliminar material (solo Admin)
     */
    public function destroy(Request $request, $id)
    {
        if ($request->user()->rol !== 'Admin') {
            return response()->json([
                'message' => 'No tienes permisos para eliminar materiales',
            ], 403);
        }

        $material = Material::find($id);

        if (!$material) {
            return response()->json([
                'message' => 'Material no encontrado',
            ], 404);
        }

        try {
            $material->update(['estado' => 'Inactivo']);

            return response()->json([
                'message' => 'Material inactivado exitosamente',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar el material',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
