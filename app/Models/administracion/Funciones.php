<?php

namespace App\Models\administracion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Funciones extends Model
{
    use HasFactory;

    protected $table = 'adm_funciones';

    protected $fillable = [
    	'id_menu',
    	'modulo',
    	'funcion',
    	'tipo',
    	'descripcion'
    ];

    protected $hidden = [
    	'created_at',
    	'updated_at'
    ];
}
