<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use DB;

class AtecConfirm extends Model {
    protected $table = 't_atec_confirm';

    protected $fillable = [
        'atec_id', 'shop_id',
    ];
}
