<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeclaracionJurada extends Model
{
   use HasFactory;

   protected $table = 'declaracion_jurada';
   protected $primaryKey = 'id_declaracion_jurada';
   protected $fillable = ['descripcion', 'porcentaje_acciones', 'estado', 'observacion', 'id_propiedad', 'id_socio', 'id_persona', 'id_parentesco'];

   public function declaracion_jurada_detalle() {
      return $this->hasMany(DeclaracionJuradaDetalle::class, 'id_declaracion_jurada', 'id_declaracion_jurada');
   }

   public function propiedad()
   {
      return $this->belongsTo(Propiedad::class, 'id_propiedad');
   }

   public function socio()
   {
      return $this->belongsTo(Socio::class, 'id_socio');
   }

   public function persona()
   {
      return $this->belongsTo(Persona::class, 'id_Persona');
   }

   public function parentesco()
   {
      return $this->belongsTo(Parentesco::class, 'id_parentesco');
   }
}
