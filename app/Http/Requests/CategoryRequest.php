<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


/**
 * Clase UserRequest
 * 
 * Esta clase se encarga de validar los datos de las peticiones HTTP
 * que se realizan a la API de usuarios.
 * 
 * Más información sobre UserRequest en https://ies-el-rincon.gitbook.io/dsw/laravel/controladores/request
 * 
 * @package App\Http\Requests
 */
class CategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|max:255',
            'description' => 'nullable'
        ];
    }

    //add messages
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio',
            'name.max' => 'El nombre no puede tener más de :max caracteres',
        ];
    }
}
