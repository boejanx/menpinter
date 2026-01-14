<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TtdModel extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'bangkom_ttd';

    protected $fillable = ['id', 'nama', 'jabatan', 'pangkat', 'nip', 'updated_at', 'created_at', 'status'];
}
