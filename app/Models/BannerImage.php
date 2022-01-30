<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BannerImage extends Model
{
    protected $table = 't_banner_image';

    protected $fillable = [
        'url', 'thumbnail', 'type', 'order_no', 'filename'
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
