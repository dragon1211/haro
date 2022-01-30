<?php

namespace App\Models;

use DB;

use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    protected $table = 't_notice';

    protected $fillable = [
        'kind', 'title', 'content', 'shop_id', 'agree', 'image_path', 'thumbnail'
    ];

    public static function get_data() {
        $notices = DB::table('t_notice')->latest()
                    ->paginate(10);
        return $notices;
    }

    public static function get_agree_data($filter) {
        $query = self::where('t_notice.agree', '=' , 1)->where('t_notice.customer_id', null)->whereHas('shop');
        if ($filter['shop'] != '' || $filter['brand'] != '' || $filter['area'] != '') {
            $query = $query->whereHas('shop', function ($q) use ($filter) {
                $q->where('name', 'like', '%'.$filter['shop'].'%')
                    ->Where('brand', 'like', '%'.$filter['brand'].'%')
                    ->Where('address', 'like', '%'.$filter['area'].'%');
            });
        }
        return $query->latest()->paginate(10);
    }

    public static function get_application_data() {
        $coupons =  self::where('agree', '=', 0)
                    ->where('customer_id', null)
                    ->latest()
                    ->paginate(10);
        return $coupons;
    }

    public static function get_notice($id) {
        $notice =  DB::table('t_notice')
                    ->where('id', '=', $id)
                    ->first();
        return $notice;
    }

    public static function get_all_data() {
        return Notice::where('agree', 1)->orderBy('updated_at', 'DESC')->get();
    }

    public static function get_by_shop($shopid, $member_no) {
        return Notice::where('agree', 1)
            ->where(function($q) use($shopid) {
                $q->where('shop_id', 0)->orWhere('shop_id', $shopid);
            })
            ->where(function($q) use($member_no) {
                $q->where('customer_id', null)->orWhere('customer_id', $member_no);
            })
            ->orderBy('updated_at', 'DESC')
            ->get();
    }

    public function shop() {
        return $this->belongsTo(Shop::class, 'shop_id');
    }
}
