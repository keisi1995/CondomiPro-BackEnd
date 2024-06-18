<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Servicio extends Model
{
    use HasFactory;
    
    protected $table = 'servicio';
    protected $primaryKey = 'id_servicio';
    protected $fillable = ['periodo', 'anio', 'total','observacion', 'id_concepto'];
                            
    public function concepto() {
        return $this->belongsTo(Concepto::class, 'id_concepto');
    }
                            
}
