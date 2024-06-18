<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comprobante extends Model
{
    use HasFactory;

    protected $table = 'comprobante';
    protected $primaryKey = 'id_comprobante';
    protected $fillable = ['nro_comprobante', 'total', 'estado', 'observacion', 'id_tipo_comprobante'];

    public function tipo_comprobante() {
        return $this->belongsTo(TipoComprobante::class, 'id_tipo_comprobante', 'id_tipo_comprobante');
    }

}