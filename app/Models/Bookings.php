<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Bookings extends Model
{
    protected $guarded = [];

    public $incrementing = false;
    protected $keyType = 'string';

    protected $uuids = ['id', 'user_id', 'hotel_id'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            foreach ($model->uuids as $field) {
                if (empty($model->{$field})) {
                    $model->{$field} = (string) Str::uuid();
                }
            }
        });
    }
}
