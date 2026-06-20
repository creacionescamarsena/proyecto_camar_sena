<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UsuarioUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $usuario = $this->route('usuario');
        $primaryKey = $usuario?->getKeyName() ?? 'id_usuario';

        return [
            'id_usuario' => ['required', 'alpha_num', 'min:4', 'max:20', Rule::unique('usuario', 'id_usuario')->ignore($usuario?->getKey(), $primaryKey)],
            'tipo_documento_id' => ['required', 'integer', 'exists:tipo_documento,id_tipo'],
            'nombres' => ['required', 'string', 'max:100', 'regex:/^[\p{L}\s]+$/u'],
            'apellidos' => ['required', 'string', 'max:100', 'regex:/^[\p{L}\s]+$/u'],
            'email' => ['required', 'email', 'max:255', Rule::unique('usuario', 'correo')->ignore($usuario?->getKey(), $primaryKey)],
            'telefono' => ['nullable', 'regex:/^[0-9]+$/', 'min:8', 'max:16'],
            'password' => ['nullable', 'string', 'min:6'],
            'rol' => ['required', 'in:Admin,Empleado,Cliente'],
            'estado' => ['required', 'in:Activo,Inactivo'],
        ];
    }

    public function messages(): array
    {
        return [
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
            'email.required' => 'Por favor rellena el correo electrónico.',
            'email.email' => 'El correo debe tener un formato válido (ej: usuario@ejemplo.com).',
            'email.max' => 'El correo no puede tener más de 255 caracteres.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'telefono.regex' => 'El teléfono solo puede contener números.',
            'telefono.min' => 'El teléfono debe tener al menos 8 dígitos.',
            'telefono.max' => 'El teléfono no puede tener más de 16 dígitos.',
            'password.string' => 'La contraseña debe ser un texto válido.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'rol.required' => 'Por favor selecciona un rol.',
            'rol.in' => 'El rol seleccionado no es válido.',
            'estado.required' => 'Por favor selecciona un estado.',
            'estado.in' => 'El estado seleccionado no es válido.',
        ];
    }
}
