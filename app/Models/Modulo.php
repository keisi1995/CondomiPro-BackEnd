<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modulo extends Model
{
    use HasFactory;

    protected $table = 'modulo';
    protected $primaryKey = 'id_modulo';
    protected $fillable = ['descripcion', 'icono', 'nro_orden'];

    public function menu() {
        return $this->hasMany(Menu::class, 'id_modulo', 'id_modulo');
    }
}
