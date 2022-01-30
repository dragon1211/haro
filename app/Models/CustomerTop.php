<?php

namespace App\Models;

use DB;

use Illuminate\Database\Eloquent\Model;

class CustomerTop extends Model
{
    protected $table = 't_customer_top';

    protected $fillable = [
        'id',
        'title',
        'content',
        'image',
        'image_link',
        'thumbnail'
    ];
}
