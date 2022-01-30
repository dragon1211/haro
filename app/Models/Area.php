<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Area extends Model
{
    protected $table = 't_area';
    // protected $primaryKey = 'f_id';

    protected $fillable = [
        'f_area_name'
    ];

    public static function get_province_list()
    {
        return Area::select(DB::raw('code, prefix, postal, name_p'))
            ->groupBy('name_p')
            ->orderby('name_p', 'asc')
            ->get();
    }

    public static function get_city_list_by_province($name_province)
    {
        return Area::select(DB::raw('code, prefix, postal, name_p, name_c, name_v'))
            ->where('name_p', $name_province)
            ->groupBy('name_c')
            ->orderby('name_c', 'asc')
            ->get();
    }

    public static function get_map_coordinate()
    {
        return DB::table('t_map_area')->get();
    }

}
