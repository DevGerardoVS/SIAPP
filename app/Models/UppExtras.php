<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UppExtras extends Model
{
    use HasFactory;

    protected $table = 'upp_extras';

    protected $fillable = [
        'upp_id',
        'clasificacion_administrativa_id',
        'estatus_epp',
        'ejercicio'
    ];
    protected $hidden = [
    	'created_at',
    	'updated_at'
    ];
}
