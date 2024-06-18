<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inquilino extends Model
{
    use HasFactory;

    protected $table = 'inquilino';
    protected $primaryKey = 'id_inquilino';
    protected $fillable = ['id_declaracion_jurada', 'id_usuario', 'fecha_baja'];
    
}
