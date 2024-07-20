<?php

namespace App\Models\catalogos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EntidadEjecutora extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'entidad_ejecutora';

    protected $fillable = [
        'upp_id',
        'subsecretaria_id',
        'ur_id',
        'sector_publico_id',
        'sector_publico_f_id',
        'sector_economia_id',
        'subsector_economia_id',
        'ente_publico_id',
        'estatus',
        'created_user',
        'updated_user',
        'deleted_user',
        'ejercicio'
    ];

    protected $dates = ['deleted_at'];

    protected $hidden = [
    	'created_at', 
    	'updated_at'
    ];
}
