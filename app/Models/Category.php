<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Category extends Model
{
    
    protected $fillable = ['name', 'description'];

    public function scenarios()
    {
        return $this->hasMany(Scenario::class);
    }

}
