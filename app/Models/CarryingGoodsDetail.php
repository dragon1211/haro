<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarryingGoodsDetail extends Model
{
    //
    protected $table = 't_carrying_goods_details';

    protected $fillable = [
        'goods_id', 'name', 'price', 'order_no'
    ];

    public static function reorder($goodsId, $orderNo, $newOrderNo) {      
        
        self::where('goods_id', $goodsId)
            ->where('order_no', $orderNo)
            ->update(['order_no' => "-1"]);

        self::where('goods_id', $goodsId)
            ->where('order_no', $newOrderNo)
            ->update(['order_no' => $orderNo]);

        self::where('goods_id', $goodsId)
            ->where('order_no', "-1")
            ->update(['order_no' => $newOrderNo]);

        return self::where('goods_id', $goodsId)->get();
    }
}
