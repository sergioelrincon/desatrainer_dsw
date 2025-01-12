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
}
