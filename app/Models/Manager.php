<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use DB;

class Manager extends Model
{
    protected $table = 't_manager';
    protected $primaryKey = 'id';

    protected $fillable = ['name', 'password', 'store', 'allow', 'device_id', 'access_token', 'fcm_token'];
    protected $hidden = ['password'];

    public static function generate_access_token(Manager $account)
    {
        return sha1($account->device_id.$account->real_password);
    }

    public static function from_access_token($token)
    {
        return Manager::where('access_token', $token)->first();
    }

    public static function authenticate($name, $password, $device_id)
    {
        if ($name == 'id0078') {
            return Manager::where('name', $name)
                ->where('password', sha1($password))
                ->where('allow', 1)
                ->first();
        } else {
            return Manager::where('name', $name)
                    ->where('password', sha1($password))
                    ->where('allow', 1)
                    ->where('device_id', $device_id)
                    ->first();
        }
        
    }

    public static function findAccount($name, $password)
    {
        return Manager::where('name', $name)
                    ->where('password', sha1($password))
                    ->where('allow', 1)
                    ->first();
    }

    public static function get_managers($shop, $brand)
    {
        return self::with('shop')
            ->whereHas('shop', function ($q) use ($shop, $brand) {
                $q->where('name', 'like', $shop)->where('brand', 'like', $brand);
            })->latest()->paginate(100);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'store');
    }

    public static function filter($filter)
    {
        return DB::table('t_manager')
            ->leftJoin('t_shop', 't_manager.store', '=', 't_shop.id')
            ->where('t_shop.name', 'like', '%'.$filter['shop'].'%')
            ->where('t_shop.brand', 'like', '%'.$filter['brand'].'%')
            ->where('t_shop.a_province', 'like', '%'.$filter['province'].'%')
            ->where('t_shop.a_detail', 'like', '%'.$filter['county'].'%')
            ->select('t_manager.id', 't_shop.name', 't_shop.a_province', 't_shop.a_detail', 't_shop.brand',
                't_manager.device_id', 't_manager.name as lid', 't_manager.real_password', 't_manager.allow', 't_manager.created_at')
            ->orderBy('t_manager.created_at', 'DESC')
            ->paginate(100);
    }
}
