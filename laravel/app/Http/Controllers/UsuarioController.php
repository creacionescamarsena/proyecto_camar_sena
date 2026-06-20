<?php

namespace App\Http\Controllers;

use App\Http\Requests\UsuarioStoreRequest;
use App\Http\Requests\UsuarioUpdateRequest;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UsuarioController extends Controller
{
 
    public function index()
    {
        $usuarios = Usuario::latest('id_usuario')->get();

        return view('admin.usuarios.usu_admin', compact('usuarios'));
    }

   
    public function create()
    {
        $tiposDocumento = Schema::hasTable('tipo_documento')
            ? DB::table('tipo_documento')->get()
            : collect([]);

        return view('admin.usuarios.create', compact('tiposDocumento'));
    }

   
    public function store(UsuarioStoreRequest $request)
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($request->input('password'));

        $usuario = Usuario::create($validated);

        // If a role-specific table exists, create the associated record so FKs work
        if (! empty($validated['rol'])) {
            if ($validated['rol'] === 'Cliente' && Schema::hasTable('cliente')) {
                $exists = DB::table('cliente')->where('usuario_id_usuario', $usuario->getKey())->exists();
                if (! $exists) {
                    DB::table('cliente')->insert(['usuario_id_usuario' => $usuario->getKey()]);
                }
            }

            if ($validated['rol'] === 'Empleado' && Schema::hasTable('empleado')) {
                $exists = DB::table('empleado')->where('usuario_id_usuario', $usuario->getKey())->exists();
                if (! $exists) {
                    DB::table('empleado')->insert([
                        'usuario_id_usuario' => $usuario->getKey(),
                        'cargo' => '',
                    ]);
                }
            }
        }

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    public function show(Usuario $usuario)
    {
        return view('admin.usuarios.show', compact('usuario'));
    }

   
    public function edit(Usuario $usuario)
    {
        $tiposDocumento = Schema::hasTable('tipo_documento')
            ? DB::table('tipo_documento')->get()
            : collect([]);

        return view('admin.usuarios.edit', compact('usuario', 'tiposDocumento'));
    }

    public function update(UsuarioUpdateRequest $request, Usuario $usuario)
    {
        $validated = $request->validated();

        $oldId = $usuario->id_usuario;

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        // If ID change requested, update PK and related FK references in other tables
        if (! empty($validated['id_usuario']) && $validated['id_usuario'] !== $oldId) {
            $newId = $validated['id_usuario'];

            DB::transaction(function () use ($oldId, $newId) {
                if (DB::table('usuario')->where('id_usuario', $newId)->exists()) {
                    throw new \Exception("El id_usuario {$newId} ya existe.");
                }

                // Update the usuario primary key first so ON UPDATE CASCADE propagates to child tables
                DB::table('usuario')->where('id_usuario', $oldId)->update(['id_usuario' => $newId]);
            });

            // Force the model to use the new id
            $usuario->id_usuario = $newId;
        }

        $usuario->update($validated);

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(Usuario $usuario)
    {
        DB::transaction(function () use ($usuario) {
            $userId = $usuario->id_usuario;

            if (Schema::hasTable('facturacion')) {
                DB::table('facturacion')
                    ->where('cliente_usuario_id_usuario', $userId)
                    ->orWhere('empleado_usuario_id_usuario', $userId)
                    ->delete();
            }

            if (Schema::hasTable('cliente')) {
                DB::table('cliente')->where('usuario_id_usuario', $userId)->delete();
            }

            if (Schema::hasTable('empleado')) {
                DB::table('empleado')->where('usuario_id_usuario', $userId)->delete();
            }

            if (Schema::hasTable('usuario_has_roles')) {
                DB::table('usuario_has_roles')->where('usuario_id_usuario', $userId)->delete();
            }

            $usuario->delete();
        });

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }
}
