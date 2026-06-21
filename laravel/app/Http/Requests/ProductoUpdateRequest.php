<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class ProductoUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $cantidades = $this->input('cantidades', []);
            if (! is_array($cantidades)) {
                return;
            }

            if (! Schema::hasTable('stock') || ! Schema::hasTable('talla')) {
                return;
            }

            $hasPositive = false;
            foreach ($cantidades as $cantidad) {
                if ((int) $cantidad > 0) {
                    $hasPositive = true;
                    break;
                }
            }

            if (! $hasPositive) {
                $validator->errors()->add('cantidades', 'Debes ingresar al menos una cantidad mayor a cero.');
            }
        });
    }

    public function rules(): array
    {
        $producto = $this->route('producto');
        $primaryKey = $producto?->getKeyName() ?? 'id_chaqueta';

        return [
            'id_chaqueta' => ['nullable', 'integer', Rule::unique('chaqueta', 'id_chaqueta')->ignore($producto?->getKey(), $primaryKey)],
            'nombre' => ['required', 'string', 'max:25'],
            'categoria_id' => ['nullable', 'integer', 'exists:categoria,id_categoria'],
            'categoria_nueva' => ['nullable', 'string', 'max:25'],
            'precio' => ['required', 'numeric', 'min:0', 'regex:/^\d+(\.\d{1,2})?$/'],
            'cantidades' => [Schema::hasTable('stock') && Schema::hasTable('talla') ? 'required' : 'nullable', 'array'],
            'cantidades.*' => ['integer', 'min:0'],
            'imagen' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'materiales' => ['nullable', 'array'],
            'materiales.*' => ['integer', 'exists:materiales,id_materiales'],
        ];
    }

    public function messages(): array
    {
        return [
            'id_chaqueta.integer' => 'El ID de chaqueta debe ser un número válido.',
            'id_chaqueta.unique' => 'Este ID de chaqueta ya está registrado.',
            'nombre.required' => 'Por favor rellena el nombre del producto.',
            'nombre.string' => 'El nombre debe ser un texto válido.',
            'nombre.max' => 'El nombre no puede tener más de 25 caracteres.',
            'categoria_id.integer' => 'La categoría no es válida.',
            'categoria_id.exists' => 'La categoría seleccionada no existe.',
            'categoria_nueva.string' => 'La nueva categoría debe ser un texto válido.',
            'categoria_nueva.max' => 'La nueva categoría no puede tener más de 25 caracteres.',
            'precio.required' => 'Por favor rellena el precio del producto.',
            'precio.numeric' => 'El precio debe ser un número válido.',
            'precio.min' => 'El precio debe ser 0 o superior.',
            'precio.regex' => 'El precio debe tener un formato válido (ej: 100 o 100.50).',
            'cantidades.required' => 'Por favor ingresa las cantidades para las tallas.',
            'cantidades.array' => 'Las cantidades deben ser una lista válida.',
            'cantidades.*.integer' => 'Cada cantidad debe ser un número entero.',
            'cantidades.*.min' => 'Cada cantidad debe ser 0 o superior.',
            'imagen.image' => 'El archivo debe ser una imagen válida.',
            'imagen.mimes' => 'La imagen debe ser JPG, JPEG, PNG o WEBP.',
            'imagen.max' => 'La imagen no debe superar los 2 MB.',
            'materiales.array' => 'Los materiales deben ser una lista válida.',
            'materiales.*.integer' => 'Cada material debe tener un identificador válido.',
            'materiales.*.exists' => 'Uno de los materiales seleccionados no existe.',
        ];
    }
}
