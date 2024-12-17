<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Clase UserRequest
 * 
 * Esta clase se encarga de validar los datos de las peticiones HTTP
 * que se realizan a la API de usuarios.
 * Más información sobre UserRequest en https://ies-el-rincon.gitbook.io/dsw/laravel/controladores/request
 * 
 * @package App\Http\Requests
 */
class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {        
        return [
            'name' => [
                $this->method() == 'POST' ? 'required' : 'nullable',
                'string',
                'max:255'
            ],
    
            'email' => [
                'required',
                'email',
                $this->method() == 'POST' 
                    ? 'unique:users' 
                    : Rule::unique('users')->ignore($this->user)
            ],
    
            'password' => [
                $this->method() == 'POST' ? 'required' : 'nullable',
                'min:6',
                'confirmed'
            ],
        ];
    }


    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio',
            'email.required' => 'El correo es obligatorio',
            'email.email' => 'El correo debe ser válido',
            'email.unique' => 'Este correo ya está registrado',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos :min caracteres',
            'password.confirmed' => 'Las contraseñas no coinciden',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nombre',
            'email' => 'correo electrónico',
            'password' => 'contraseña',
        ];
    }
}