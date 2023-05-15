<?php

namespace App\Models\administracion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sistema extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'adm_sistemas';

    protected $fillable = [
    	'nombre_sistema',
        'ruta',
        'logo',
        'log_min',
    	'descripcion',
    	'estatus'
    ];

    protected $dates = ['deleted_at'];

    protected $hidden = [
    	'created_at', 
    	'updated_at'
    ];
}
