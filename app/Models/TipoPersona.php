<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoPersona extends Model
{
    use HasFactory;
    
    protected $table = 'tipo_persona';
    protected $primaryKey = 'id_tipo_persona';
    protected $fillable = ['descripcion'];

    public function persona() {
        return $this->hasMany(Persona::class, 'id_tipo_persona', 'id_tipo_persona');
    }
}
