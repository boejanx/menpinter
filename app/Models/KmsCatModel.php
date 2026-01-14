<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KmsCatModel extends Model
{
     
    protected $table = 'kms_cat';
    protected $primaryKey = 'cat_id';
    public $timestamps = false;

    protected $fillable = [
        'cat_id',
        'cat_name',
        'status',
    ];

    public function kmsItems()
    {
        return $this->hasMany(KmsModel::class, 'cat_id', 'cat_id');
    }

    function hitung() {
        return $this->kmsItems()->count();
    }

    function aktif() {
        return $this->status === '1';
    }

    function getCategory() {
        return $this->cat_name;
    }
}
