<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use Config;

class CustomerVerifyNumber extends Model
{
    protected $table = 't_customer_verifyNum';
    protected $primaryKey = 'f_id';

    protected $fillable = [
        'f_phone_number', 'f_verify_number'
    ];

    public static function get_verifyNumber_by_phoneNumber($phone_number)
    {
        return CustomerVerifyNumber::where('f_phone_number', $phone_number)
                    ->first();
    }

    public static function check_phoneNumber($phoneNumber)
    {
        return DB::table('t_customer')
            ->where('tel_no', $phoneNumber)
            ->limit(1)
            ->get();
    }
}
