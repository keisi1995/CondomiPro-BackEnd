<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provincia extends Model
{
    use HasFactory;

    protected $table = 'provincia';
    protected $primaryKey = 'id_provincia';
    protected $fillable = ['id_provincia', 'nombre'];
    protected $casts = [
        'id_provincia' => 'string',
    ];
    
}
