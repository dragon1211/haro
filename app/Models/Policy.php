<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use DB;

class Policy extends Model
{
    protected $table = 't_policy';

    protected $fillable = [
        'policy', 'privacy',
    ];
}
