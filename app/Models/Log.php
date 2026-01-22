<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Log extends Model
{
    protected $table = 'bangkom_log';
    protected $fillable = [
        'user_id',
        'action',
        'module',
        'description',
        'ip_address',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
