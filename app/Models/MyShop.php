<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class MyShop extends Model
{
    protected $table = 't_myshop';
    protected $fillable = [
        'f_customer_id', 'f_shop_id'
    ];
    protected $primaryKey = 'f_id';

    public static function get_my_shop($customerID)
    {
        return self::where('f_customer_id', $customerID)
            ->orderby('updated_at', 'desc')
            ->first();
    }

    public static function get_my_shop_image($myShopID)
    {
        return DB::table('t_shop_image')
            ->where('shop_id', $myShopID)
            ->get();
    }

    public static function get_my_shop_history($customerID)
    {
        return DB::table('v_my_shop')
            ->select(DB::raw('*, DATE_FORMAT(created_at,"%Y-%m-%d") as date'))
            ->where('f_customer_id', $customerID)
            ->orderby('updated_at', 'asc')
            ->get();
    }

}
