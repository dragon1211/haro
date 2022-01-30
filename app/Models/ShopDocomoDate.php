<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Config;

class ShopDocomoDate extends Model
{
    protected $table = 't_shop_docomo_date';
    protected $fillable = ['f_shop_id', 'f_rest_date'];
    protected $primaryKey = 'f_id';
}
