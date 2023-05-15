<?php

namespace App\Models\administracion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    use HasFactory;

    protected $table = 'adm_bitacora';

    protected $fillable = [
    	'username',
    	'accion',
    	'modulo',
    	'ip_origen',
    	'fecha_movimiento'
    ];

    protected $hidden = [
    	'created_at',
    	'updated_at'
    ];
}
