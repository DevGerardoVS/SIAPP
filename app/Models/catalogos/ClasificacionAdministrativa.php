<?php

namespace App\Models\catalogos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClasificacionAdministrativa extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'clasificacion_administrativa';

    protected $fillable = [
        'sector_publico_id',
        'sector_publico_f_id',
        'sector_economia_id',
        'subsector_economia_id',
        'ente_publico_id',
        'estatus',
        'created_user',
        'updated_user',
        'deleted_user'
    ];

    protected $dates = ['deleted_at'];

    protected $hidden = [
    	'created_at', 
    	'updated_at'
    ];
}
