<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Parentesco extends Model
{
    use HasFactory;

    protected $table = 'parentesco';
    protected $primaryKey = 'id_parentesco';
    protected $fillable = ['descripcion'];
  
}
