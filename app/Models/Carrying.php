<?php

namespace App\Models;

use DB;

use Illuminate\Database\Eloquent\Model;

class Carrying extends Model
{
    protected $table = 't_carrying';

    protected $fillable = [
        'shop_id', 'date', 'goods',
    ];

    public static function get_data($date, $filter) {
        $carries = self::where('goods', 'like', $filter['goods'])
            ->where('customer_id', 'like', $filter['customer']);
        if (isset($date)) {
            $carries = $carries->where('date', '=', $date);
        }
        if ($filter['performer'] != '%%') {
            $carries = $carries->where('performer', 'like', $filter['performer']);
        }
        if ($filter['shop'] != '%%') {
            $carries = $carries->whereHas('shop', function ($query) use ($filter) {
                $query->where('name', 'like', $filter['shop']);
            });
        }
        $carries = $carries->latest()->paginate(10);
        return $carries;
    }

    public static function get_data_by_customer($customer_id, $shop_id)
    {
        $carries = DB::table('v_carrying')
                    ->where('shop_id', $shop_id)
                    ->where('customer_id', $customer_id)
                    ->latest()
                    ->get();
        return $carries;
    }

    public static function get_data_by_shop($shop_id, $performer)
    {
        $carries = self::with('shop')->where('shop_id', $shop_id);
        if ($performer != '') {
            $carries = $carries->where('performer', $performer);
        }
        return $carries->latest()->get();
    }

    public static function get_today_data_by_shop($shop_id, $performer)
    {
        $today = date('Y-m-d');
        $carries = self::with('shop')->where('shop_id', $shop_id)->where('date', $today);
        if ($performer != '') {
            $carries = $carries->where('performer', $performer);
        }
        return $carries->latest()->get();
    }

    public static function get_date_data_by_shop($shop_id, $from, $to, $performer)
    {
        if (!$from) $from='';
        if (!$to) $to='2100/01/01';
        $carries = self::with('shop')->where('shop_id', $shop_id)
            ->where('date', '>=', $from)
            ->where('date', '<=', $to);
        if ($performer != '') {
            $carries = $carries->where('performer', $performer);
        }
        return $carries->latest()->get();
    }

    public static function get_last_carrying_date($customer_id, $shop_id)
    {
        $carrying = DB::table('t_carrying')
                    ->select('date')
                    ->where('shop_id', $shop_id)
                    ->where('customer_id', $customer_id)
                    ->latest()
                    ->first();
        return $carrying;
    }

    public static function get_sigong_by_customer($customerID, $sortMode, $type)
    {
        return self::with(['shop', 'good', 'himages'])->where('customer_id', $customerID)
            ->where('carrying_kind', $type)
            ->orderBy('created_at', $sortMode)
            ->get();
    }

    public function shop() {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    public function good() {
        return $this->belongsTo(CarryingGoods::class, 'goods_id');
    }

    public function himages() {
        return $this->hasMany(CarryingHistoryImage::class, 'carrying_id', 'id');
    }
}
