<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class carga_masiva_estatus extends Model
{
    use HasFactory;

    protected $table = 'carga_masiva_estatus';
    protected $fillable = [ 
        'id_usuario',
        'cargapayload',
        'cargaMasClav',
        'created_user',
        'updated_user'
    ];

    protected function data(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value, true),
            set: fn ($value) => json_encode($value),
        );
    } 
}
