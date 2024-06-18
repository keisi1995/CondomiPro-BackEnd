<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoConcepto extends Model
{
    use HasFactory;

    protected $table = 'tipo_concepto';
    protected $primaryKey = 'id_tipo_concepto';
    protected $fillable = ['descripcion', 'id_usuario_registro'];

    public function concepto() {
        return $this->hasMany(Concepto::class, 'id_tipo_concepto', 'id_tipo_concepto');
    }
}
