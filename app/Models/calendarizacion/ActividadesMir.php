<?php

namespace App\Models\calendarizacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActividadesMir extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'actividades_mir';

    protected $fillable = [
        'proyecto_mir_id',
        'clv_actividad',
        'actividad',
        'estatus'
    ];

    protected $dates = ['deleted_at'];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}