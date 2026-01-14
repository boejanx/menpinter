<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class KmsModel extends Model
{
    protected $table = 'kms_data';
    protected $primaryKey = 'kms_id';

    protected $keyType = 'string';
    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'kms_id',
        'cat_id',
        'judul',
        'link',
        'author',
        'visibility',
        'created_at',
        'updated_at',
        'deleted_at',
        'thumbnail',
        'status',
    ];

    protected $casts = [
        'cat_id'     => 'integer',
        'visibility' => 'integer',
        'status'     => 'integer',
    ];

    /**
     * Auto-generate UUID
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(KmsCatModel::class, 'cat_id', 'cat_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'author', 'id');
    }
}
