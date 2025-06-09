<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Topics extends Model
{
    const NEW = 0;
    const SUCCESS = 1;
    const ERROR = 2;

    protected $guarded = [];
}
