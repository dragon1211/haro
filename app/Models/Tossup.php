<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use DB;

class Tossup extends Model
{
    protected $table = 't_tossup';

    protected $fillable = [
        'shop', 'content',
    ];

    public static function get_data() {
        $tossups =  DB::table('v_tossup')
                    ->paginate(10);
        return $tossups;
    }

    public static function get_untossed_tossup() {
        $tossup =  DB::table('v_tossup')
                    ->where('tossed', '=', 0)
                    ->latest()
                    ->paginate(10);
        return $tossup;
    }

    public static function get_tossup_by_shop($shop)
    {
        $tosses = self::where(['tossed' => 0, 'shop' => $shop])->latest()->get();
        return $tosses;
    }

    public function shopO()
    {
        return $this->belongsTo(Shop::class, 'shop');
    }
}
