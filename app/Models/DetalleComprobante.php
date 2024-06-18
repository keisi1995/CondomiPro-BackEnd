<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetalleComprobante extends Model
{
    use HasFactory;    
   
    protected $table = 'detalle_comprobante';
    protected $primaryKey = 'id_detalle_comprobante';
    protected $fillable = ['subtotal', 'descuento','total', 'id_comprobante', 'id_cta_por_cobrar'];

    public function comprobante() {
        return $this->hasMany(Comprobante::class, 'id_comprobante');
    }
    
    public function cta_por_cobrar() {
        return $this->hasMany(CtaPorCobrar::class, 'id_cta_por_cobrar');
    }

}