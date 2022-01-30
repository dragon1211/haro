<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Calculation extends Model
{
    protected $table = 't_calculation';

    protected $fillable = [
        'customer_id', 'date', 'shop_id', 'sum1', 'sum2',
    ];

    public static function get_data_by_customer($customer_id, $shop_id)
    {
        $calculation = Calculation::where('shop_id', $shop_id)->where('customer_id', $customer_id)
            ->latest()->get();
        return $calculation;
    }
}
