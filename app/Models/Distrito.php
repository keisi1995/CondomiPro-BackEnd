<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Distrito extends Model
{
    use HasFactory;

    protected $table = 'distrito';
    protected $primaryKey = 'id_distrito';
    protected $fillable = ['id_distrito', 'nombre'];
    protected $casts = [
        'id_distrito' => 'string',
    ];

    public function persona() {
        return $this->hasMany(Persona::class, 'id_distrito');
    }
}
