<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackingVisita extends Model
{
    use HasFactory;

    protected $table = 'tracking_visita';
    protected $primaryKey = 'id_tracking_visita';
    protected $fillable = ['estado', 'id_solicitud_visita'];
        
}