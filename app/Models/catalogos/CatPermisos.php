<?php

namespace App\Models\catalogos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CatPermisos extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cat_permisos';

    protected $fillable = [
    	'nombre',
        'id_sistema'
    ];

    protected $dates = ['deleted_at'];

    protected $hidden = [
    	'created_at', 
    	'updated_at'
    ];
}
