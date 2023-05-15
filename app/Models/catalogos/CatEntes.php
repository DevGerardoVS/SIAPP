<?php

namespace App\Models\catalogos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CatEntes extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cat_entes';

    protected $fillable = [
    	'cve_upp',
    	'nombre_upp',
    	'cve_ur',
    	'nombre_ur',
    	'cve_uo',
    	'nombre_uo',

    ];

    protected $dates = ['deleted_at'];

    protected $hidden = [
    	'created_at', 
    	'updated_at'
    ];
}
