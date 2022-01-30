<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Performer extends Model
{
    //
    protected $table = 't_performers';
    protected $fillable = ['shop_id', 'name', 'order_no'];
}
