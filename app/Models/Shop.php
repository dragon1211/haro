<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Shop extends Model
{
    protected $table = 't_shop';

    protected $fillable = [
        'name',
        'address',
        'a_province',
        'a_detail',
        'postal',
        'tel_no',
        'image',
        'docomo',
        'link',
        'latitude',
        'longitude',
        'brand',
        'email',
        'class_link',
        'login_id',
        'login_password',
        'thumbnail'
    ];

    public static function authenticate($name, $password)
    {
        return self::where('login_id', $name)
            ->where('login_password', $password)
            ->first();
    }

    public static function get_data($keys) {
        return self::where('name', 'like', '%'.$keys['name'].'%')
            ->where('brand', 'like', '%'.$keys['brand'].'%')
            ->where('address', 'like', '%'.$keys['area'].'%')
            ->latest()
            ->paginate(100);
    }

    public static function get_shops($myShopID=NULL) {
        if (isset($myShopID) && $myShopID !== '') {
            $myShop = Shop::where('id', $myShopID)->get()->toArray();
            $otherShop = Shop::where('id', '<>', $myShopID)->get()->toArray();
            return array_merge($myShop, $otherShop);
        } else {
            return Shop::select('*')->get();
        }
    }

    public static function get_shop($id) {
        $shop =  DB::table('t_shop')
                    ->where('id', '=', $id)
                    ->first();
        return $shop;
    }

    public static function get_shop_by_area_id($areaID)
    {
        return Shop::where('area_id', $areaID)
            ->get();
    }

    public static function get_shop_by_postalCode($postalCode)
    {
        return Shop::where('postal', $postalCode)
            ->first();
    }

    public static function get_shop_name($id) {
        $shop = Shop::where('id', $id)
                ->first();
        return $shop->name;
    }

    public static function get_province_list()
    {
        return self::select('a_province')
            ->groupBy('a_province')
            ->orderby('a_province', 'asc')
            ->get();
    }

    public static function get_city_list_by_province($name_province)
    {
        return self::where('a_province', $name_province)
            ->groupBy('a_detail')
            ->select('a_detail')
            ->get();
    }

    public static function filter_shop_by_address($address)
    {
        return self::where('address', 'like', '%'.$address.'%')
            ->orWhere('name', 'like', '%'.$address.'%')
            ->limit(100)
            ->get();
    }

    public static function get_shop_by_city($province, $detail)
    {
        return self::where('a_province', $province)->where('a_detail', $detail)->get();
    }

    public static function get_shop_by_province($name_province)
    {
        $cityList = DB::table('v_shop')
                ->select('name_c')
                ->where('name_p', $name_province)
                ->groupBy('name_c')
                ->get();
        $shopList = DB::table('v_shop')
                ->where('name_p', $name_province)
                ->get();
        $shopByCity = [];
        foreach($cityList as $city) {
            $shopByCity[$city->name_c] = array();
        }
        foreach($shopList as $shop){
            $shopByCity[$shop->name_c][] = $shop;
        }
        return $shopByCity;
    }

    public function area()
    {
        return $this->hasOne(Area::class, 'postal', 'postal');
    }

    public function customers()
    {
        return $this->belongsToMany(Customer::class, MyShop::class, 'f_shop_id', 'f_customer_id');
    }

    public function atecs()
    {
        return $this->hasMany(Atec::class, 'shop');
    }

    public function atecConfirms()
    {
        return $this->hasMany(AtecConfirm::class, 'shop_id');
    }

    public function notices()
    {
        return $this->hasMany(Notice::class, 'shop_id');
    }

    public function unreadAtec()
    {
        $total = Atec::where('shop', 0)->count() + count($this->atecs);
        $read = count($this->atecConfirms);
        return max($total - $read, 0);
    }
}
