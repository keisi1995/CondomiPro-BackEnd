<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitante extends Model
{
    use HasFactory;

    protected $table = 'visitante';
    protected $primaryKey = 'id_visitante';
    protected $fillable = ['nombres', 'apellidos', 'nro_documento', 'telefono', 'correo', 'id_tipo_documento'];
     
}