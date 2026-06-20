<?php

namespace App\Http\Controllers\Admin;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Password;
use App\Notifications\ResetPasswordNotification;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('login.index');
    }

    public function showRegister()
    {
        $tiposDocumento = Schema::hasTable('tipo_documento')
            ? DB::table('tipo_documento')->get()
            : collect([]);

        return view('login.register', compact('tiposDocumento'));
    }

    public function showForgotPassword()
    {
        return view('login.forgot');
    }

    public function login(Request $request)
    {
        $request->validate([
            'correo' => ['required', 'email'],
            'contraseña' => ['required'],
        ], [
            'correo.required' => 'Por favor rellena el correo electrónico.',
            'correo.email' => 'El correo debe tener un formato válido (ej: usuario@ejemplo.com).',
            'contraseña.required' => 'Por favor rellena la contraseña.',
        ]);

        $usuario = Usuario::where('correo', $request->input('correo'))
            ->where('estado', 'Activo')
            ->first();

        if (! $usuario || ! Hash::check($request->input('contraseña'), $usuario->contraseña)) {
            return back()->withErrors([
                'correo' => 'Las credenciales no coinciden con un usuario activo.',
            ])->withInput();
        }

        Auth::login($usuario);

        return match ($usuario->rol) {
            'Admin' => redirect()->route('admin.dashboard'),
            'Empleado' => redirect()->route('empleado.dashboard'),
            'Cliente' => redirect()->route('cliente.catalogo'),
            default => redirect()->route('cliente.catalogo'),
        };
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'id_usuario' => ['required', 'alpha_num', 'min:4', 'max:20', 'unique:usuario,id_usuario'],
            'tipo_documento_id' => ['required', 'integer', 'exists:tipo_documento,id_tipo'],
            'nombres' => ['required', 'string', 'max:100', 'regex:/^[\p{L}\s]+$/u'],
            'apellidos' => ['required', 'string', 'max:100', 'regex:/^[\p{L}\s]+$/u'],
            'correo' => ['required', 'email', 'max:255', 'unique:usuario,correo'],
            'telefono' => ['nullable', 'regex:/^[0-9]+$/', 'min:8', 'max:16'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'id_usuario.required' => 'Por favor rellena el ID de usuario.',
            'id_usuario.alpha_num' => 'El ID de usuario solo puede contener letras y números.',
            'id_usuario.min' => 'El ID de usuario debe tener al menos 4 caracteres.',
            'id_usuario.max' => 'El ID de usuario no puede tener más de 20 caracteres.',
            'id_usuario.unique' => 'Este ID de usuario ya está registrado.',
            'tipo_documento_id.required' => 'Por favor selecciona un tipo de documento.',
            'tipo_documento_id.integer' => 'El tipo de documento no es válido.',
            'tipo_documento_id.exists' => 'El tipo de documento seleccionado no existe.',
            'nombres.required' => 'Por favor rellena el campo de nombres.',
            'nombres.string' => 'El nombre debe ser un texto válido.',
            'nombres.max' => 'Los nombres no pueden tener más de 100 caracteres.',
            'nombres.regex' => 'Los nombres solo pueden contener letras y espacios.',
            'apellidos.required' => 'Por favor rellena el campo de apellidos.',
            'apellidos.string' => 'Los apellidos deben ser un texto válido.',
            'apellidos.max' => 'Los apellidos no pueden tener más de 100 caracteres.',
            'apellidos.regex' => 'Los apellidos solo pueden contener letras y espacios.',
            'correo.required' => 'Por favor rellena el correo electrónico.',
            'correo.email' => 'El correo debe tener un formato válido (ej: usuario@ejemplo.com).',
            'correo.max' => 'El correo no puede tener más de 255 caracteres.',
            'correo.unique' => 'Este correo electrónico ya está registrado.',
            'telefono.regex' => 'El teléfono solo puede contener números.',
            'telefono.min' => 'El teléfono debe tener al menos 8 dígitos.',
            'telefono.max' => 'El teléfono no puede tener más de 16 dígitos.',
            'password.required' => 'Por favor rellena la contraseña.',
            'password.string' => 'La contraseña debe ser un texto válido.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
        ]);

        $usuario = Usuario::create([
            'id_usuario' => $data['id_usuario'],
            'tipo_documento_id' => $data['tipo_documento_id'],
            'nombres' => $data['nombres'],
            'apellidos' => $data['apellidos'],
            'correo' => $data['correo'],
            'telefono' => $data['telefono'] ?? null,
            'contraseña' => Hash::make($data['password']),
            'rol' => 'Cliente',
            'estado' => 'Activo',
        ]);

        // Ensure a cliente row exists when a Cliente registers so foreign keys work
        if (Schema::hasTable('cliente')) {
            $exists = DB::table('cliente')->where('usuario_id_usuario', $usuario->getKey())->exists();
            if (! $exists) {
                DB::table('cliente')->insert([
                    'usuario_id_usuario' => $usuario->getKey(),
                ]);
            }
        }

        return redirect()->route('login')->with('success', 'Registro completado. Ahora puedes iniciar sesión.');
    }

    public function sendPasswordReset(Request $request)
    {
        $request->validate([
            'correo' => ['required', 'email'],
        ], [
            'correo.required' => 'Por favor rellena el correo electrónico.',
            'correo.email' => 'El correo debe tener un formato válido (ej: usuario@ejemplo.com).',
        ]);

        $usuario = Usuario::where('correo', $request->input('correo'))
            ->where('estado', 'Activo')
            ->first();

        if ($usuario) {
            try {
                $token = Password::broker()->createToken($usuario);
                $usuario->notify(new ResetPasswordNotification($token));
            } catch (\Throwable $e) {
                // swallow errors to avoid leaking info
            }
        }

        return back()->with('success', 'Si existe una cuenta activa con ese correo, te enviaremos un enlace para restablecer tu contraseña.');
    }

    public function logout()
    {
        Auth::logout();

        return redirect()->route('login');
    }

    public function showResetForm($token)
    {
        return view('login.reset', ['token' => $token]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'correo' => ['required', 'email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'token' => ['required', 'string'],
        ], [
            'correo.required' => 'Por favor rellena el correo electrónico.',
            'correo.email' => 'El correo debe tener un formato válido (ej: usuario@ejemplo.com).',
            'password.required' => 'Por favor rellena la contraseña.',
            'password.string' => 'La contraseña debe ser un texto válido.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'token.required' => 'El token es requerido para restablecer la contraseña.',
        ]);

        $credentials = [
            'email' => $request->input('correo'),
            'password' => $request->input('password'),
            'password_confirmation' => $request->input('password_confirmation'),
            'token' => $request->input('token'),
        ];

        $status = Password::broker()->reset(
            $credentials,
            function ($user, $password) {
                $user->contraseña = Hash::make($password);
                $user->save();
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('success', 'Contraseña restablecida correctamente.');
        }

        return back()->withErrors(['correo' => 'Token inválido o datos incorrectos.']);
    }
}
