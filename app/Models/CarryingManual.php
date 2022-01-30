<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarryingManual extends Model
{
    protected $table = 't_manual';

    protected $fillable = [
        'filename', 'display_name', 'url', 'type', 'order_no'
    ];

    public static function reorder($type, $orderNo, $newOrderNo) {      

        self::where('type', $type)
            ->where('order_no', $orderNo)
            ->update(['order_no' => "-1"]);

        self::where('type', $type)
            ->where('order_no', $newOrderNo)
            ->update(['order_no' => $orderNo]);

        self::where('type', $type)
            ->where('order_no', "-1")
            ->update(['order_no' => $newOrderNo]);

        return self::where('type', $type)->get();
    }
}
