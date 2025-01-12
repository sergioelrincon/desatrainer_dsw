<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transition extends Model
{
    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array
     */
    protected $fillable = ['from_instruction_id', 'to_instruction_id', 'trigger', 'time_seconds', 'desa_button_id', 'loop_count'];

    /**
     * Los atributos que deben ser convertidos.
     *
     * @var array
     */
    protected $casts = [
        'trigger' => 'string',
        'time_seconds' => 'integer',
    ];

    /**
     * Los valores permitidos para el campo trigger
     *
     * @var array
     */
    const TRIGGER_TYPES = [
        'time' => 'Tiempo',
        'user_choice' => 'Elección de Usuario',
        'loop' => 'Bucle',
    ];

    /**
     * Obtiene la instrucción de origen de la transición.
     */
    public function fromInstruction()
    {
        return $this->belongsTo(Instruction::class, 'from_instruction_id');
    }

    /**
     * Obtiene la instrucción de destino de la transición.
     */
    public function toInstruction()
    {
        return $this->belongsTo(Instruction::class, 'to_instruction_id');
    }

}
