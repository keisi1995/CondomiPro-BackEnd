<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Visita extends Model
{
    use HasFactory;

    protected $table = 'visita';
    protected $primaryKey = 'id_visita';
    protected $fillable = ['fecha_hora_visita', 'flag_movilidad', 'observacion', 'codigo_qr', 'placa_vehiculo', 'id_usuario_visita', 'id_usuario_seguridad', 'id_visitante', 'id_propiedad', 'id_motivo'];

}