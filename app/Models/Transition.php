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
    protected $fillable = ['from_instruction_id', 'to_instruction_id', 'trigger', 'time_seconds', 'desa_button_id', 'loop_count', 'is_initial'];

    /**
     * Los atributos que deben ser convertidos.
     *
     * @var array
     */
    protected $casts = [
        'trigger' => 'string',
        'time_seconds' => 'integer',
        'is_initial' => 'boolean'
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

    // Nueva relación con el botón DESA
    /*
    public function desaButton()
    {
        return $this->belongsTo(DesaButton::class);
    }
    */

    // Obtener la configuración específica según el tipo de trigger
    public function getTriggerConfigAttribute()
    {
        switch ($this->trigger) {
            case 'time':
                return [
                    'type' => 'time',
                    'value' => $this->time_seconds,
                    'formatted' => $this->formatSeconds($this->time_seconds),
                ];
            case 'user_choice':
                $button = DesaButton::find($this->desa_button_id);
                return [
                    'type' => 'user_choice',
                    'button' => $button,
                    'formatted' => $button ? $button->label : 'Sin botón asignado',
                ];
            case 'loop':
                return [
                    'type' => 'loop',
                    'value' => $this->loop_count,
                    'formatted' => $this->loop_count . ' veces',
                ];
            default:
                return null;
        }
    }

    // Helper para formatear segundos en formato legible
    protected function formatSeconds($seconds)
    {
        if (!$seconds) {
            return 'No especificado';
        }

        if ($seconds < 60) {
            return $seconds . ' segundos';
        }

        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60;

        if ($remainingSeconds === 0) {
            return $minutes . ' ' . ($minutes === 1 ? 'minuto' : 'minutos');
        }

        return $minutes . ' ' . ($minutes === 1 ? 'minuto' : 'minutos') . ' y ' . $remainingSeconds . ' ' . ($remainingSeconds === 1 ? 'segundo' : 'segundos');
    }

    // Validación según el tipo de trigger
    public function validateTriggerConfig()
    {
        switch ($this->trigger) {
            case 'time':
                return !is_null($this->time_seconds) && $this->time_seconds > 0;
            case 'user_choice':
                return !is_null($this->desa_button_id);
            case 'loop':
                return !is_null($this->loop_count) && $this->loop_count > 0;
            default:
                return false;
        }
    }

    // Método para establecer la configuración según el tipo
    public function setTriggerConfig($data)
    {
        switch ($this->trigger) {
            case 'time':
                $this->time_seconds = $data['seconds'] ?? null;
                $this->desa_button_id = null;
                $this->loop_count = null;
                break;
            case 'user_choice':
                $this->time_seconds = null;
                $this->desa_button_id = $data['button_id'] ?? null;
                $this->loop_count = null;
                break;
            case 'loop':
                $this->time_seconds = null;
                $this->desa_button_id = null;
                $this->loop_count = $data['count'] ?? null;
                break;
        }
    }

    // Scopes existentes...

    // Nuevos scopes para filtrar por configuración específica
    public function scopeWithValidConfig($query)
    {
        return $query->where(function ($q) {
            $q->where(function ($q) {
                $q->where('trigger', 'time')->whereNotNull('time_seconds');
            })
                ->orWhere(function ($q) {
                    $q->where('trigger', 'user_choice')->whereNotNull('desa_button_id');
                })
                ->orWhere(function ($q) {
                    $q->where('trigger', 'loop')->whereNotNull('loop_count');
                });
        });
    }

    public function scopeByTimeRange($query, $minSeconds, $maxSeconds)
    {
        if ($minSeconds || $maxSeconds) {
            $query->where('trigger', 'time')->when($minSeconds, fn($q) => $q->where('time_seconds', '>=', $minSeconds))->when($maxSeconds, fn($q) => $q->where('time_seconds', '<=', $maxSeconds));
        }
        return $query;
    }

    public function scopeByButton($query, $buttonId)
    {
        if ($buttonId) {
            $query->where('trigger', 'user_choice')->where('desa_button_id', $buttonId);
        }
        return $query;
    }

    public function scopeByLoopCount($query, $count)
    {
        if ($count) {
            $query->where('trigger', 'loop')->where('loop_count', $count);
        }
        return $query;
    }

    /**
     * Obtiene el nombre legible del trigger
     */
    public function getTriggerNameAttribute()
    {
        return self::TRIGGER_TYPES[$this->trigger] ?? $this->trigger;
    }

    public function scopeOrderByField($query, $field, $direction = 'asc')
    {
        $allowedFields = ['id', 'from_instruction_id', 'to_instruction_id', 'trigger', 'created_at'];

        if (in_array($field, $allowedFields)) {
            $query->orderBy($field, $direction);
        }

        return $query;
    }

    public function scopeFilterByFromInstruction($query, $instructionId)
    {
        if ($instructionId) {
            return $query->where('from_instruction_id', $instructionId);
        }

        return $query;
    }

    public function scopeFilterByToInstruction($query, $instructionId)
    {
        if ($instructionId) {
            return $query->where('to_instruction_id', $instructionId);
        }

        return $query;
    }

    public function scopeFilterByTrigger($query, $trigger)
    {
        if ($trigger) {
            return $query->where('trigger', $trigger);
        }

        return $query;
    }

    public function scopeFilterByScenario($query, $scenarioId)
    {
        if ($scenarioId) {
            return $query
                ->whereHas('fromInstruction', function ($q) use ($scenarioId) {
                    $q->where('scenario_id', $scenarioId);
                })
                ->orWhereHas('toInstruction', function ($q) use ($scenarioId) {
                    $q->where('scenario_id', $scenarioId);
                });
        }

        return $query;
    }
}
