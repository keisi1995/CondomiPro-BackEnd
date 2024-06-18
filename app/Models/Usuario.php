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
    protected $fillable = ['correo', 'clave', 'flag_activo','id_persona', 'id_tipo_usuario'];

    protected $hidden = ['clave'];
    
    protected $casts = [        
        'clave' => 'hashed',
        'flag_activo' => 'string',
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
