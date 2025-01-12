<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DesaButton extends Model
{
    protected $fillable = [
        'desa_trainer_id',
        'label',
        'area',
        'color',
        'is_blinking'
    ];

    protected $casts = [
        'area' => 'array',
        'is_blinking' => 'boolean'
    ];

    public function desaTrainer()
    {
        return $this->belongsTo(DESATrainer::class);
    }

    // Colores predefinidos disponibles
    const AVAILABLE_COLORS = [
        '#007bff' => 'Azul',
        '#28a745' => 'Verde',
        '#dc3545' => 'Rojo',
        '#ffc107' => 'Amarillo',
        '#17a2b8' => 'Cian',
        '#6c757d' => 'Gris',
        '#6f42c1' => 'Púrpura'
    ];

    // Helper para obtener el color con transparencia
    public function getFillColorAttribute()
    {
        $hex = str_replace('#', '', $this->color);
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        return "rgba($r, $g, $b, 0.2)";
    }

    // Helper para obtener el color del borde
    public function getStrokeColorAttribute()
    {
        return $this->color;
    }
}
