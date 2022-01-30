<?php

namespace App\Models;

use DB;

use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    protected $table = 't_inquiry';
    protected $primaryKey = 'id';

    protected $fillable = [
        'shop', 'content', 'customer', 'sender', 'reply'
    ];

    public static function get_data($shop_name) {
        $inquiries = self::with('shop')
            ->leftjoin('t_shop as receiver', 't_inquiry.shop', '=', 'receiver.id')
            ->leftjoin('t_shop as sender', 't_inquiry.sender', '=', 'sender.id')
            ->select('t_inquiry.*', 'receiver.name as shop_name', 'sender.name as sender_name')
            ->whereHas('shop', function ($q) use ($shop_name) {
                $q->where('name', 'like', $shop_name);
            })
            ->latest()
            ->paginate(10);

        return $inquiries;
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop');
    }

    public static function get_by_shop($shop)
    {
        return DB::table('v_inquiry')
            ->select(DB::raw('*, DATE_FORMAT(created_at,"%Y-%m-%d") as date'))
            ->where('shop', $shop)
            ->where('reply', NULL)
            ->latest()
            ->get();
    }

    public static function get_new_inqueries($shop)
    {
        return DB::table('v_inquiry')
            ->select(DB::raw('*, DATE_FORMAT(created_at,"%Y-%m-%d") as date'))
            ->where('shop', $shop)
            ->where('reply', NULL)
            ->latest()
            ->count();
    }

    public static function get_inquiry_list($customerID)
    {
        return Inquiry::select('t_inquiry.*', 't_shop.name AS shop_name')
            ->leftjoin('t_shop', 't_inquiry.sender', '=', 't_shop.id')
            ->where('customer', $customerID)
            ->where('isReply', 1)
            ->get();
    }

    public static function count_inquiries_by_customer($customerID)
    {
        return Inquiry::where('customer', $customerID)
            ->where('isReply', 1)
            ->count();
    }

    public static function count_read_inquiries_by_customer($customerID)
    {
        return DB::table('t_customer_inquiry_read')->where('f_customer', $customerID)
            ->count();
    }
}
