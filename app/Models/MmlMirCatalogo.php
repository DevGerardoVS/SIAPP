<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MmlMirCatalogo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'mml_catalogos';

    protected $fillable = [ 
        'id',
        'grupo',
        'valor',
        'created_user',
        'updated_user',
        'deleted_user',
    ];

    protected $dates = ['deleted_at'];

    protected $hidden = [
    	'created_at', 
    	'updated_at'
    ];
}

