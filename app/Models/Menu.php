<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{    
    use HasFactory;

    protected $table = 'menu';
    protected $primaryKey = 'id_menu';
    protected $fillable = ['descripcion', 'icono', 'ruta', 'nro_orden', 'id_modulo'];

    public function modulo() {
        return $this->belongsTo(Modulo::class, 'id_modulo');
    }
    
}
