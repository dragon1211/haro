<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Config;

class ShopRestDate extends Model
{
    protected $table = 't_shop_rest_date';
    protected $fillable = ['f_shop_id', 'f_rest_date', 'f_rest_type'];
    protected $primaryKey = 'f_id';

    public static function check_rest($shopID)
    {
        $restList = ShopRestDate::select('t_shop_rest_date.*', 't_time_list.f_time AS f_time')
            ->leftjoin('t_time_list', 't_shop_rest_date.f_rest_time', '=', 't_time_list.f_id')
            ->where('f_shop_id', $shopID)
            ->where('f_rest_date', date('Y-m-d'))
            ->get();
        if ($restList && count($restList) > 0) {
            foreach ($restList as $restItem) {
                if ($restItem->f_rest_time == 0)
                    return Config::get('constants.restType.WHOLE_REST');
                $hour = $restItem->f_time.split(':')[0];
                $min = $restItem->f_time.split(':')[1];
                $range_start = mktime((int)$hour, (int)$min);
                $range_end = mktime((int)$hour, (int)$min + 30);
                $currentTime = mktime(date('h', date('m')));
                if ($currentTime >= $range_start && $currentTime <= $range_end)
                    return Config::get('constants.restType.OUT_OF_DUTY');
            }
        }
        return Config::get('constants.restType.ON_DUTY');
    }
}
