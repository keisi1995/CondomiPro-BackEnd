<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoComprobante extends Model
{
    use HasFactory;

    protected $table = 'tipo_comprobante';
    protected $primaryKey = 'id_tipo_comprobante';
    protected $fillable = ['descripcion'];
}
