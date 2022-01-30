<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalculationGoods extends Model
{
    protected $table = 't_calculation_goods';

    protected $fillable = [
        'calculation_id', 'type', 'name', 'other', 'amount', 'price'
    ];

    public static function get_data_by_calculation($calculation_id)
    {
        $calculation = CalculationGoods::where('calculation_id', $calculation_id)
            ->latest()->get();
        return $calculation;
    }
}
