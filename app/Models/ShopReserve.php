<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ShopReserve extends Model
{

    protected $table = 't_shop_reserve';
    protected $fillable = ['f_customer_id', 'f_shop_id', 'f_reserve_date', 'f_reserve_time', 'f_reserve_purpose', 'f_other', 'f_agreed'];
    protected $primaryKey = 'f_id';

    public static function get_time_list()
    {
        return DB::table('t_time_list')
            ->orderBy('f_time', 'asc')->get();
    }

    public static function get_rest_date($shopID)
    {
        return DB::table('t_shop_rest_date')
            ->where('f_shop_id', $shopID)
            ->get();
    }
    public static function get_reserved_data($shopID)
    {
        return ShopReserve::where('f_shop_id', $shopID)
            ->where('f_reserve_date', '>=', date('Y-m-d', time()-60*60*24))
            ->orderBy('f_reserve_date', 'asc')
            ->get();
    }
    public static function get_new_reserve($shopID)
    {
        return ShopReserve::where('f_shop_id', $shopID)
            ->where('f_reserve_date', '>=', date('Y-m-d', time()-60*60*24))
            ->where('f_confirm', 0)
            ->count();
    }
    public static function get_my_recent_reserve_date($customerID)
    {
        return ShopReserve::select('t_shop_reserve.*', 't_shop.name AS shopName', 't_time_list.f_time AS reserveTimeName')
            ->leftjoin('t_shop', 't_shop_reserve.f_shop_id', '=', 't_shop.id' )
            ->leftjoin('t_time_list', 't_shop_reserve.f_reserve_time', '=', 't_time_list.f_id' )
            ->where('f_customer_id', $customerID)
            ->where('f_reserve_date', '>=', date('Y-m-d', time()-60*60*24))
            ->orderBy('f_reserve_date', 'asc')
            ->orderBy('f_reserve_time', 'asc')

            ->first();
    }
    public static function cancel_reserve($shopID, $customerID, $dateTimeList)
    {
        ShopReserve::where('f_shop_id', $shopID)
            ->where('f_customer_id', $customerID)
            ->whereRaw('CONCAT(f_reserve_date, "|", f_reserve_time)', $dateTimeList)
            ->delete();
    }
    public static function reserve_visit_date($data)
    {
        ShopReserve::insertOrIgnore($data);
    }
}
