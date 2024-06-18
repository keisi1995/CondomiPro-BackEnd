<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Concepto extends Model
{
    use HasFactory;

    protected $table = 'concepto';
    protected $primaryKey = 'id_concepto';
    protected $fillable = ['id_tipo_concepto', 'descripcion'];
    
    public function tipo_concepto() {
        return $this->belongsTo(TipoConcepto::class, 'id_tipo_concepto');
    }
    
}
