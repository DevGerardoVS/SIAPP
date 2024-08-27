<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;

class Beneficiarios extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'beneficiarios';

    protected $fillable = [
        'clave',
        'beneficiario',
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
