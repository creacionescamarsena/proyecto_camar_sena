<?php

namespace App\Http\Controllers\Api;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rules\Password;

class AuthApiController extends Controller
{
    /**
     * Registro de nuevo usuario
     * POST /api/auth/register
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'id_usuario' => ['required', 'alpha_num', 'min:4', 'max:20', 'unique:usuario,id_usuario'],
            'nombres' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'correo' => ['required', 'email', 'unique:usuario,correo'],
            'telefono' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'tipo_documento_id' => ['required', 'integer', 'exists:tipo_documento,id_tipo'],
        ]);

        $usuario = Usuario::create([
            'id_usuario' => $validated['id_usuario'],
            'nombres' => $validated['nombres'],
            'apellidos' => $validated['apellidos'],
            'correo' => $validated['correo'],
            'telefono' => $validated['telefono'],
            'contraseña' => Hash::make($validated['password']),
            'tipo_documento_id' => $validated['tipo_documento_id'],
            'rol' => 'Cliente',
            'estado' => 'Activo',
        ]);

        $token = $usuario->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Usuario registrado exitosamente',
            'usuario' => [
                'id' => $usuario->id_usuario,
                'nombre_completo' => $usuario->nombre_completo,
                'correo' => $usuario->correo,
                'rol' => $usuario->rol,
            ],
            'token' => $token,
        ], 201);
    }

    /**
     * Login de usuario
     * POST /api/auth/login
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'correo' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $usuario = Usuario::where('correo', $validated['correo'])
            ->where('estado', 'Activo')
            ->first();

        if (!$usuario || !Hash::check($validated['password'], $usuario->contraseña)) {
            return response()->json([
                'message' => 'Las credenciales no son válidas o el usuario está inactivo',
            ], 401);
        }

        $token = $usuario->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Sesión iniciada exitosamente',
            'usuario' => [
                'id' => $usuario->id_usuario,
                'nombre_completo' => $usuario->nombre_completo,
                'correo' => $usuario->correo,
                'rol' => $usuario->rol,
                'telefono' => $usuario->telefono,
            ],
            'token' => $token,
        ], 200);
    }

    /**
     * Logout de usuario
     * POST /api/auth/logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada exitosamente',
        ], 200);
    }

    /**
     * Obtener perfil del usuario autenticado
     * GET /api/auth/profile
     */
    public function profile(Request $request)
    {
        return response()->json([
            'usuario' => [
                'id' => $request->user()->id_usuario,
                'nombre_completo' => $request->user()->nombre_completo,
                'correo' => $request->user()->correo,
                'rol' => $request->user()->rol,
                'telefono' => $request->user()->telefono,
                'estado' => $request->user()->estado,
            ],
        ], 200);
    }

    /**
     * Actualizar perfil del usuario
     * PUT /api/auth/profile
     */
    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'nombres' => ['sometimes', 'string', 'max:100'],
            'apellidos' => ['sometimes', 'string', 'max:100'],
            'telefono' => ['sometimes', 'string', 'max:20'],
            'password' => ['sometimes', 'string', 'min:8', 'confirmed'],
        ]);

        $usuario = $request->user();

        if (isset($validated['nombres'])) {
            $usuario->nombres = $validated['nombres'];
        }
        if (isset($validated['apellidos'])) {
            $usuario->apellidos = $validated['apellidos'];
        }
        if (isset($validated['telefono'])) {
            $usuario->telefono = $validated['telefono'];
        }
        if (isset($validated['password'])) {
            $usuario->contraseña = Hash::make($validated['password']);
        }

        $usuario->save();

        return response()->json([
            'message' => 'Perfil actualizado exitosamente',
            'usuario' => [
                'id' => $usuario->id_usuario,
                'nombre_completo' => $usuario->nombre_completo,
                'correo' => $usuario->correo,
                'rol' => $usuario->rol,
            ],
        ], 200);
    }
}
