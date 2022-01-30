<?php

namespace App\Models;

use DB;

use Illuminate\Database\Eloquent\Model;

class ShopImage extends Model
{
    protected $table = 't_shop_image';

    protected $fillable = [
        'shop_id', 'filename', 'display_name', 'url', 'thumbnail'
    ];

    public static function get_images_by_shop($shop_id) {
        $images =  DB::table('t_shop_image')
                    ->where('shop_id', '=', $shop_id)
                    ->orderBy('id')
                    ->limit(9)
                    ->get();
        return $images;
    }
}
