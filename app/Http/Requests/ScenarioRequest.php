<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScenarioRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado a realizar esta solicitud.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Aquí puedes poner lógica de autorización si lo necesitas
    }

    /**
     * Obtiene las reglas de validación que se deben aplicar a la solicitud.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'category_id' => 'required|exists:categories,id',   // Comprueba que la categoría exista
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ];
    }

    /**
     * Obtiene los mensajes de error personalizados.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'category_id.required' => 'La categoría es obligatoria.',
            'category_id.exists' => 'La categoría seleccionada no existe.',
            'title.required' => 'El título es obligatorio.',
            'title.string' => 'El título debe ser una cadena de texto.',
            'title.max' => 'El título no debe superar los 255 caracteres.',
            'description.string' => 'La descripción debe ser una cadena de texto.',
        ];
    }
}
