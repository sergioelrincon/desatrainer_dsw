<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DesaTrainerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Asumimos que la autorización se maneja a través de middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'description' => 'nullable|string'
        ];

        // Establecemos la regla de validación en función de si estamos creando o actualizando un registro
        if ($this->isMethod('post') || $this->hasFile('image')) {
            $rules['image'] = 'required|image|mimes:jpeg,png,jpg,gif|max:2048';
        } else {
            $rules['image'] = 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.max' => 'El nombre no puede tener más de 255 caracteres.',
            'model.required' => 'El modelo es obligatorio.',
            'model.max' => 'El modelo no puede tener más de 255 caracteres.',
            'image.required' => 'La imagen es obligatoria.',
            'image.image' => 'El archivo debe ser una imagen.',
            'image.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg o gif.',
            'image.max' => 'La imagen no debe pesar más de 2MB.',
        ];
    }
}