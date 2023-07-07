<?php

namespace App\Models\calendarizacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProyectosMir extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'proyectos_mir';

    protected $fillable = [
        'clv_upp',
        'clv_ur',
        'clv_programa',
        'clv_subprograma',
        'clv_proyecto',
        'ejercicio'
    ];

    protected $dates = ['deleted_at'];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}