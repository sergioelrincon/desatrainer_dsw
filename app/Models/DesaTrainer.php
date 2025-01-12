<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DesaTrainer extends Model
{
    protected $fillable = [
        'name',
        'model',
        'description',
        'image',
        'settings'
    ];

    protected $casts = [
        'settings' => 'array'
    ];

    public function buttons()
    {
        return $this->hasMany(DesaButton::class);
    }

    public function scenarios()
    {
        return $this->hasMany(Scenario::class);
    }
}


