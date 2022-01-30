<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use Config;

class Customer extends Model
{
    protected $table = 't_customer';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'name_japan',
        'tel_no', 'email',
        'birthday',
        'fax',
        'password',
        'first_name',
        'last_name',
        'first_huri',
        'last_huri',
        'device_id',
        'access_token',
        'member_no',
        'fcm_token',
        'fcm_flag',
    ];

    public static function generate_access_token($deviceID, $email)
    {
        return sha1($deviceID.$email);
    }

    public static function from_access_token($token)
    {
        return Customer::where('access_token', $token)->first();
    }

    public static function get_data($name) {
        return Customer::where('name', 'like', $name)->latest()->paginate(10);
    }

    public static function search_member_count($code, $name, $tel_no) {
        if (isset($code) && $code !== '')
            return Customer::where('member_no', $code)->count();
        else {
            $name = "%".$name."%";
            $tel_no = "%".$tel_no."%";
            return Customer::where('name', 'like', $name)
                            ->where('tel_no', 'like', $tel_no)->count();
        }
    }

    public static function getLicenseData()
    {
        return DB::table('t_license')
            ->latest()->first();
    }

    public static function search_member_id($code, $name, $tel_no) {
        $db = DB::table('t_customer')->select('*');
        if (isset($code) && $code !== '')
            return $db->where('member_no', $code)->get();
        else {
            $name = "%".$name."%";
            $tel_no = "%".$tel_no."%";
            return Customer::where('name', 'like', $name)
                            ->where('tel_no', 'like', $tel_no)->get();
        }
    }

    public static function get_member($id) {
        return Customer::where([
            ['id', '=', $id],
            ])->first();
    }

	public static function authenticate($id, $password, $deviceID)
    {
        return Customer::where('member_no', $id)
                    ->where('password', $password)
                    // ->where('device_id', $deviceID)
                    ->first();
    }

    public static function authenticate_transferCode($transferCode)
    {
        return Customer::where('transferCode', $transferCode)
                    ->where('transferCode_date', '>',  date('Y-m-d H:i:s', time() - 60 * 60 * 24))
                    ->first();
    }

    public static function updateMember($data, $member_no)
    {
        Customer::where('member_noo', $member_no)
            ->update($data);
    }

    public static function setResetToken($customerID, $resetToken)
    {
        Customer::where('id', $customerID)
            ->update(['resetPasswordToken' => $resetToken]);
    }

    public static function checkResetToken($customerID, $resetToken)
    {
        return Customer::where('id', $customerID)
            ->where('resetPasswordToken', $resetToken)->get();
    }

    public static function resetPassword($customerID, $password)
    {
        Customer::where('id', $customerID)
            ->update(['password' => sha1($password), 'resetPasswordToken' => null]);
    }

    public static function set_transfer_code($customerID, $transferCode)
    {
        Customer::where('id', $customerID)
            ->update(['transferCode' => $transferCode, 'transferCode_date' => date('Y-m-d H:i:s')]);
    }

    public function shop()
    {
        return $this->belongsToMany(Shop::class, MyShop::class, 'f_customer_id', 'f_shop_id');
    }

    public function notices()
    {
        return $this->hasMany(CustomerNotice::class, 'customer_id');
    }

    public function unreadNotice($member_no)
    {
        $shop = $this->shop->first();
        if ($shop != null) {
            $total = Notice::where('agree', 1)
            ->where(function($q) use($shop) {
                $q->where('shop_id', 0)->orWhere('shop_id', $shop->id);
            })
            ->where(function($q) use($member_no) {
                $q->where('customer_id', null)->orWhere('customer_id', $member_no);
            })
            ->count();
            $read = count($this->notices);
            return max($total - $read, 0);
        } else {
            return 0;
        }
    }
}
