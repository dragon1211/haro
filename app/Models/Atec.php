<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use DB;

class Atec extends Model
{
    protected $table = 't_atec';

    protected $fillable = [
        'kind', 'title', 'content','shop', 'thumbnail'
    ];

    public static function get_data() {
        $atecs =  DB::table('t_atec')
                    ->latest()
                    ->paginate(10);
        return $atecs;
    }

    public static function get_atecs($shop) {
        $query='SELECT *, DATE_FORMAT(t_atec.created_at,"%Y-%m-%d") as date FROM t_atec
                left JOIN (SELECT shop_id, atec_id FROM t_atec_confirm WHERE shop_id='.$shop.') A ON t_atec.id=A.atec_id
                WHERE (shop='.$shop.' OR shop=0) 
                ORDER BY id DESC';
        $atecs =  DB::select($query);
        return $atecs;
    }

    public static function get_new_atecs($shop) {
        $query='select * from t_atec
                left JOIN (select shop_id, atec_id from t_atec_confirm where shop_id='.$shop.') A on t_atec.id=A.atec_id
                where shop_id is NULL';
        $atec =  DB::select($query);
        return count($atec);
    }

    public static function get_atec($id) {
        $atec =  DB::table('t_atec')
                    ->where('id', '=', $id)
                    ->first();
        return $atec;
    }
}
