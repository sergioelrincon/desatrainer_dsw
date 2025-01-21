<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Scenario extends Model
{
    protected $fillable = [
        'category_id',
        'desa_trainer_id',
        'title',
        'description'
    ];
    
    // Relación con Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relación con Instruction
    public function instructions()
    {
        return $this->hasMany(Instruction::class);
    }

    // Relación con DesaTrainer
    public function desaTrainer()
    {
        return $this->belongsTo(DesaTrainer::class);
    }
    
    
    // Scopes para filtros y ordenamiento
    public function scopeOrderByField(Builder $query, $field, $direction = 'asc')
    {
        return $query->orderBy($field, $direction);
    }
    
    public function scopeFilterByTitle(Builder $query, $title)
    {
        if ($title) {
            return $query->where('title', 'like', "%{$title}%");
        }
        return $query;
    }
    
    public function scopeFilterByCategory(Builder $query, $categoryId)
    {
        if ($categoryId) {
            return $query->where('category_id', $categoryId);
        }
        return $query;
    }
}