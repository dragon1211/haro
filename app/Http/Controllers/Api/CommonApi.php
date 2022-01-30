<?php

namespace App\Http\Controllers\Api;

use App\Models\Customer;
use DB;
use App\Models\Shop;
use App\Models\Tossup;
use App\Models\Inquiry;
use App\Models\Atec;
use App\Models\Coupon;
use App\Models\Notice;
use App\Models\CarryingGoods;
use App\Models\Manager;

class CommonApi
{
    /**
     * Get all stores
     */
    public static function get_stores($code=NULL)
    {
        return Shop::get_shops();
    }

    public static function add_tossup($shop, $content)
    {
        $tossup = new Tossup;
        $tossup->shop = $shop;
        $tossup->content = $content;
        $tossup->save();
    }

    public static function get_tossup($shop)
    {
        return Tossup::get_tossup_by_shop($shop);
    }

    public static function get_inquiry($shop)
    {
        return Inquiry::get_by_shop($shop);
    }

    public static function reply_inquiry($id, $reply)
    {
        $inquiry = Inquiry::find($id);
        $inquiry->reply = $reply;
        $inquiry->save();
    }

    public static function get_atec($shop)
    {
        return Atec::get_atecs($shop);
    }

    public static function add_coupon()
    {

    }

    public static function get_coupon_by_shop($shop)
    {
        $today = date('Y-m-d');

        return Coupon::where('shop_id', $shop)
            ->where('to_date', '>=', $today)
            ->where('stop', '=', '0')
            ->select(DB::raw('*, DATE_FORMAT(created_at,"%Y-%m-%d") as date, DATE_FORMAT(from_date,"%Y/%m/%d") as from_date1, DATE_FORMAT(to_date,"%Y/%m/%d") as to_date1'))
            ->latest()
            ->get();
    }

    public static function get_last_coupon_by_shop($shop)
    {
        $today = date('Y-m-d');

        return Coupon::where('shop_id', $shop)
            ->where('to_date', '<', $today)
            ->orWhere('stop', '1')
            ->select(DB::raw('*, DATE_FORMAT(created_at,"%Y-%m-%d") as date, DATE_FORMAT(from_date,"%Y/%m/%d") as from_date1, DATE_FORMAT(to_date,"%Y/%m/%d") as to_date1'))
            ->latest()
            ->get();
    }

    public static function get_notice_by_shop($shop)
    {
        return Notice::where('shop_id', $shop)
            ->where('customer_id', null)
            ->select(DB::raw('*, DATE_FORMAT(updated_at,"%Y-%m-%d") as date'))
            ->latest()
            ->get();
    }

    public static function get_goods_list()
    {
        return CarryingGoods::select()->get();
    }

    public static function generate_member_unique_id($prefix_no)
    {
        $year = date('Y');
        $month = date('m');
        $day = date('d');
        $hour = date('H');
        $minute = date('i');
        $second = date('s');
        $prefix_str = substr(sha1($prefix_no), 0, 2);
        // $suffix = (string)strlen($firstName).(string)strlen($lastName).(string)strlen($email);
        return $prefix_str.(string)($year - 2020).(string)($month * 31 + $day).(string)($hour).(string)$minute.(string)$second;
    }

    public static function generate_shop_unique_id($shop_id)
    {
        $tmp = '0000';
        $tmp = $tmp.$shop_id;
        $tmp_str = 'id';
        return $tmp_str.substr($tmp, strlen($tmp)-4, 4);
    }

    public static function generate_manager_unique_id()
    {
        $id = Manager::max('id') + 1;
        $tmp = '0000';
        $tmp = $tmp.$id;
        $tmp_str = 'id';
        return $tmp_str.substr($tmp, strlen($tmp)-4, 4);
    }

    public static function generate_password()
    {
        $text="";
        $length=8;
        $pattern = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";

        for($count = 0; $count < $length; $count++) {
            $text .= $pattern[mt_rand(0, strlen($pattern) - 1)];
        }
        return $text;
    }

    public static function generate_transcode()
    {
        $text="";
        $length=10;
        $pattern = "0123456789";

        for($count = 0; $count < $length; $count++) {
            $text .= $pattern[mt_rand(0, strlen($pattern) - 1)];
        }
        return $text;
    }

    public static function makeResetURL($customerID)
    {
        $resetToken = rand(100000, 999999);
        Customer::setResetToken($customerID, $resetToken);
        return url('/api/client/resetPassword'.'?resetToken='.$resetToken.'&customerID='.$customerID);
    }

    public static function sendSMS($phoneNumber, $data)
    {
    }

    public static function sendEmail($email, $data)
    {
    }
}
