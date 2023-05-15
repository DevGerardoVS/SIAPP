<?php

namespace App\Models\administracion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'adm_menus';

    protected $fillable = [
    	'padre',
    	'nombre_menu',
    	'ruta',
    	'icono',
    	'nivel',
    	'posicion',
    	'descripcion',
    	'estatus'
    ];

    protected $dates = ['deleted_at'];

    protected $hidden = [
    	'created_at', 
    	'updated_at'
    ];
}
