<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;

class UnidadesMedida extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'unidades_medida';

    protected $fillable = [
        'clave',
        'unidad_medida',
        'ejercicio',
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
