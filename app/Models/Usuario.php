<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Usuario extends Authenticatable implements JWTSubject
{
    use HasFactory;
    protected $table = 'usuario';
    protected $primaryKey = 'id_usuario';
    protected $fillable = ['usuario', 'clave', 'estado','id_persona'];

    protected $hidden = ['clave'];
    
    protected $casts = [        
        'clave' => 'hashed',
    ];

    public function getJWTIdentifier()
    {
    	return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
    	return [];
    }
}
