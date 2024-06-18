<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Edificacion extends Model
{
    use HasFactory;
    
    protected $table = 'edificacion';
    protected $primaryKey = 'id_edificacion';
    protected $fillable = ['descripcion', 'direccion', 'cantidad_pisos', 'observacion'];
  
}
