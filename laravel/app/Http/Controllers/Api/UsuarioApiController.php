<?php

namespace App\Http\Controllers\Api;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class UsuarioApiController extends Controller
{
    /**
     * GET /api/usuarios
     * Obtener lista de usuarios (solo Admin)
     */
    public function index(Request $request)
    {
        if ($request->user()->rol !== 'Admin') {
            return response()->json([
                'message' => 'No tienes permisos para listar usuarios',
            ], 403);
        }

        $query = Usuario::query();

        // Filtro por rol
        if ($request->has('rol')) {
            $query->where('rol', $request->rol);
        }

        // Filtro por estado
        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        $usuarios = $query->paginate(20);

        return response()->json([
            'message' => 'Usuarios obtenidos exitosamente',
            'data' => $usuarios->items(),
            'pagination' => [
                'total' => $usuarios->total(),
                'per_page' => $usuarios->perPage(),
                'current_page' => $usuarios->currentPage(),
                'last_page' => $usuarios->lastPage(),
            ],
        ], 200);
    }

    /**
     * POST /api/usuarios
     * Crear nuevo usuario (solo Admin)
     */
    public function store(Request $request)
    {
        if ($request->user()->rol !== 'Admin') {
            return response()->json([
                'message' => 'No tienes permisos para crear usuarios',
            ], 403);
        }

        $validated = $request->validate([
            'id_usuario' => ['required', 'alpha_num', 'unique:usuario,id_usuario'],
            'nombres' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'correo' => ['required', 'email', 'unique:usuario,correo'],
            'telefono' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8'],
            'tipo_documento_id' => ['required', 'integer', 'exists:tipo_documento,id_tipo'],
            'rol' => ['required', 'in:Admin,Empleado,Cliente'],
            'estado' => ['required', 'in:Activo,Inactivo'],
        ]);

        try {
            $usuario = Usuario::create([
                'id_usuario' => $validated['id_usuario'],
                'nombres' => $validated['nombres'],
                'apellidos' => $validated['apellidos'],
                'correo' => $validated['correo'],
                'telefono' => $validated['telefono'],
                'contraseña' => Hash::make($validated['password']),
                'tipo_documento_id' => $validated['tipo_documento_id'],
                'rol' => $validated['rol'],
                'estado' => $validated['estado'],
            ]);

            return response()->json([
                'message' => 'Usuario creado exitosamente',
                'data' => [
                    'id' => $usuario->id_usuario,
                    'nombre_completo' => $usuario->nombre_completo,
                    'correo' => $usuario->correo,
                    'rol' => $usuario->rol,
                    'estado' => $usuario->estado,
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear el usuario',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/usuarios/{id}
     * Obtener detalle de un usuario (solo Admin o el mismo usuario)
     */
    public function show(Request $request, $id)
    {
        if ($request->user()->rol !== 'Admin' && $request->user()->id_usuario !== $id) {
            return response()->json([
                'message' => 'No tienes permisos para ver este usuario',
            ], 403);
        }

        $usuario = Usuario::find($id);

        if (!$usuario) {
            return response()->json([
                'message' => 'Usuario no encontrado',
            ], 404);
        }

        return response()->json([
            'message' => 'Usuario obtenido exitosamente',
            'data' => [
                'id' => $usuario->id_usuario,
                'nombre_completo' => $usuario->nombre_completo,
                'correo' => $usuario->correo,
                'telefono' => $usuario->telefono,
                'rol' => $usuario->rol,
                'estado' => $usuario->estado,
            ],
        ], 200);
    }

    /**
     * PUT /api/usuarios/{id}
     * Actualizar usuario (solo Admin o el mismo usuario para ciertos campos)
     */
    public function update(Request $request, $id)
    {
        $usuario = Usuario::find($id);

        if (!$usuario) {
            return response()->json([
                'message' => 'Usuario no encontrado',
            ], 404);
        }

        // Verificar permisos
        if ($request->user()->rol !== 'Admin' && $request->user()->id_usuario !== $id) {
            return response()->json([
                'message' => 'No tienes permisos para actualizar este usuario',
            ], 403);
        }

        // Si no es admin, solo puede actualizar su propio perfil (nombre, apellido, teléfono, password)
        if ($request->user()->rol !== 'Admin') {
            $validated = $request->validate([
                'nombres' => ['sometimes', 'string', 'max:100'],
                'apellidos' => ['sometimes', 'string', 'max:100'],
                'telefono' => ['sometimes', 'string', 'max:20'],
                'password' => ['sometimes', 'string', 'min:8'],
            ]);
        } else {
            // Admin puede actualizar todos los campos
            $validated = $request->validate([
                'nombres' => ['sometimes', 'string', 'max:100'],
                'apellidos' => ['sometimes', 'string', 'max:100'],
                'correo' => ['sometimes', 'email', 'unique:usuario,correo,' . $id . ',id_usuario'],
                'telefono' => ['sometimes', 'string', 'max:20'],
                'rol' => ['sometimes', 'in:Admin,Empleado,Cliente'],
                'estado' => ['sometimes', 'in:Activo,Inactivo'],
                'password' => ['sometimes', 'string', 'min:8'],
            ]);
        }

        try {
            if (isset($validated['password'])) {
                $validated['contraseña'] = Hash::make($validated['password']);
                unset($validated['password']);
            }

            $usuario->update($validated);

            return response()->json([
                'message' => 'Usuario actualizado exitosamente',
                'data' => [
                    'id' => $usuario->id_usuario,
                    'nombre_completo' => $usuario->nombre_completo,
                    'correo' => $usuario->correo,
                    'rol' => $usuario->rol,
                    'estado' => $usuario->estado,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar el usuario',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * DELETE /api/usuarios/{id}
     * Eliminar usuario (solo Admin)
     */
    public function destroy(Request $request, $id)
    {
        if ($request->user()->rol !== 'Admin') {
            return response()->json([
                'message' => 'No tienes permisos para eliminar usuarios',
            ], 403);
        }

        $usuario = Usuario::find($id);

        if (!$usuario) {
            return response()->json([
                'message' => 'Usuario no encontrado',
            ], 404);
        }

        try {
            $usuario->update(['estado' => 'Inactivo']);

            return response()->json([
                'message' => 'Usuario inactivado exitosamente',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar el usuario',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
