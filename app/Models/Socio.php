<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class Socio extends Model
{
    use HasFactory;
    
    protected $table = 'socio';
    protected $primaryKey = 'id_socio';
    protected $fillable = ['id_usuario', 'fecha_baja'];
  
    public function usuario() {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }
}
