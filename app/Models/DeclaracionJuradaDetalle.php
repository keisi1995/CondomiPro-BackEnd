<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeclaracionJuradaDetalle extends Model
{
    use HasFactory;

    protected $table = 'declaracion_jurada_detalle';
    protected $primaryKey = 'id_declaracion_jurada_detalle';
    protected $fillable = ['tipo_calculo', 'id_concepto', 'id_declaracion_jurada'];

}
