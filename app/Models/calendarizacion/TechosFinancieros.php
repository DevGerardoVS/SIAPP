<?php

namespace App\Models\calendarizacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TechosFinancieros extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'techos_financieros';

    protected $fillable = [
        'clv_upp',
        'clv_fondo',
        'tipo',
        'presupuesto',
        'ejercicio',
        'updated_user',
        'created_user'
    ];

    protected $dates = ['deleted_at'];
    protected $hidden = [
    	'created_at', 
    	'updated_at'
    ];
}
