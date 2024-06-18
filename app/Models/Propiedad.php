<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Propiedad extends Model
{
    use HasFactory;    

    protected $table = 'propiedad';
    protected $primaryKey = 'id_propiedad';
    protected $fillable = ['nro_interior', 'area_propiedad','observacion', 'id_edificacion'];
  
    public function edificacion()
    {
        return $this->belongsTo(Edificacion::class, 'id_edificacion', 'id_edificacion');
    }

}