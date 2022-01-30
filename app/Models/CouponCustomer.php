<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class CouponCustomer extends Model
{
    protected $table = 't_customer_coupon';
    protected $primaryKey = 'f_id';
    protected $fillable = ['f_customer', 'f_coupon'];

}
