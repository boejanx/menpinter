<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiasnConfig extends Model
{

    protected $table = 'config';

    protected $fillable = [
        'cs_key',
        'cs_sec',
        'cs_id',
        'csso',
        'cwso',
        'cwso_exp',
    ];
}
