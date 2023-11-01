<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class uppautorizadascpnomina extends Model
{
    protected $table = 'uppautorizadascpnomina';

    use HasFactory;
    use SoftDeletes;

}
