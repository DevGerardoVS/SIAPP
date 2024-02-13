<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class notificaciones extends Model
{
    use HasFactory;

    protected $table = 'notificaciones';
    protected $fillable = [ 
        'id_usuario',
        'id_sistema',
        'payload',
        'status',
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
