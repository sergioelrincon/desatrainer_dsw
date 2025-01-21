<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InstructionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Puedes agregar lógica aquí para verificar si el usuario está autorizado
        // a realizar la solicitud, por ahora retornamos true
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'scenario_id' => 'required|exists:scenarios,id',
            'title' => 'required|string|max:255',
            'content' => 'required',
            'audio_file' => 'nullable|file|mimes:mp3,wav|max:10240', // max 10MB
            //'duration' => 'nullable|integer|min:0', // puedes descomentar si lo necesitas
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'scenario_id.required' => 'El escenario es obligatorio.',
            'scenario_id.exists' => 'El escenario seleccionado no existe.',
            'title.required' => 'El título es obligatorio.',
            'content.required' => 'El contenido es obligatorio.',
            'audio_file.file' => 'El archivo de audio debe ser un archivo.',
            'audio_file.mimes' => 'El archivo de audio debe ser en formato mp3 o wav.',
            'audio_file.max' => 'El archivo de audio no debe superar los 10MB.',
        ];
    }

    /**
     * Get the validation attributes that should be used for the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'scenario_id' => 'escenario',
            'title' => 'título',
            'content' => 'contenido',
            'audio_file' => 'archivo de audio',
        ];
    }
}
