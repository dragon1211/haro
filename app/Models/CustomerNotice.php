<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerNotice extends Model
{
    //
    protected $table = 't_notify_confirm';
    protected $fillable = [
        'notify_id',
        'customer_id',
    ];
}
