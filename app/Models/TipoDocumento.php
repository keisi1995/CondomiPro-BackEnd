<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model
{
    use HasFactory;
    
    protected $table = 'tipo_documento';
    protected $primaryKey = 'id_tipo_documento';
    protected $fillable = ['descripcion'];

    public function persona() {
        return $this->hasMany(Persona::class, 'id_tipo_documento', 'id_tipo_documento');
    }
}
