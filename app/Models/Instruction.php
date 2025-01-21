<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Instruction extends Model
{
    protected $fillable = [
        'scenario_id',
        'title',
        'content',
        'audio_file',
       // 'duration'
    ];
    
    // Relación con Scenario
    public function scenario()
    {
        return $this->belongsTo(Scenario::class);
    }
    
    // Scopes para filtros y ordenamiento

    public function scopeOrderByField(Builder $query, $field, $direction = 'asc')
    {
    if ($field === 'title') {
        return $query->orderByRaw('CAST(title AS UNSIGNED) ' . $direction);
    }
    return $query->orderBy($field, $direction);
    }
    
    public function scopeFilterByTitle(Builder $query, $title)
    {
        if ($title) {
            return $query->where('title', 'like', "%{$title}%");
        }
        return $query;
    }
    
    public function scopeFilterByScenario(Builder $query, $scenarioId)
    {
        if ($scenarioId) {
            return $query->where('scenario_id', $scenarioId);
        }
        return $query;
    }
    
    public function scopeFilterByDuration(Builder $query, $duration)
    {
        if ($duration) {
            return $query->where('duration', '<=', $duration);
        }
        return $query;
    }

        // Añadir estas nuevas relaciones para las transiciones
        /*public function fromTransitions()
        {
            return $this->hasMany(Transition::class, 'from_instruction_id');
        }
    
        public function toTransitions()
        {
            return $this->hasMany(Transition::class, 'to_instruction_id');
        }*/
        
        // Resto de tus scopes...
    
        // Método helper para obtener las transiciones disponibles
        public function getAvailableTransitions()
        {
            return $this->fromTransitions;
        }
}
