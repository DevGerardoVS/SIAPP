<?php

namespace App\Models\calendarizacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CierreMetas extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cierre_ejercicio_metas';

    protected $fillable = [
        'clv_upp',
        'estatus',
        'ejercicio',
        'created_user',
        'updated_user',
        'deleted_user',
        'activos'
    ];

    protected $dates = ['deleted_at'];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}