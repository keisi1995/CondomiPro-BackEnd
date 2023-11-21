<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Socio extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'socio';
    protected $primaryKey = 'id_socio';
    protected $fillable = ['fecha_baja', 'id_persona'];
  
    public function persona() {
        return $this->belongsTo(Persona::class, 'id_persona');
    }
}
