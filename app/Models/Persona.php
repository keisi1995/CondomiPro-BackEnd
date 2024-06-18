<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class Persona extends Model
{
    use HasFactory;
    // use SoftDeletes;

    protected $table = 'persona';
    protected $primaryKey = 'id_persona';
    protected $fillable = ['nombres', 'apellidos', 'direccion', 'nro_documento', 'telefono', 'correo', 'id_distrito', 'id_tipo_documento', 'id_tipo_persona'];
    protected $casts = [
        'id_distrito' => 'string'
    ];    

    public function tipo_documento() {
        return $this->belongsTo(TipoDocumento::class, 'id_tipo_documento');
    }
    
    public function tipo_persona() {
        return $this->belongsTo(TipoPersona::class, 'id_tipo_persona');
    }

    public function distrito() {
        return $this->belongsTo(Distrito::class, 'id_distrito');
    }

}
