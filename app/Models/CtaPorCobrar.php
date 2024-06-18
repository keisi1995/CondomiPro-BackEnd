<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CtaPorCobrar extends Model
{
    use HasFactory;

    protected $table = 'cta_por_cobrar';
    protected $primaryKey = 'id_cta_por_cobrar';
    protected $fillable = ['total', 'insoluto', 'intereses', 'descuento', 'estado', 'observacion', 'id_servicio', 'id_declaracion_jurada'];

    public function servicio() 
    {
        return $this->belongsTo(Servicio::class, 'id_servicio');
    }

    public function declaracion_jurada() 
    {
        return $this->belongsTo(DeclaracionJurada::class, 'id_declaracion_jurada');
    }
    
}
