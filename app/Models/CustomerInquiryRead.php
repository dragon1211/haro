<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use DB;

class CustomerInquiryRead extends Model
{
    protected $table = 't_customer_inquiry_read';
    protected $primaryKey = 'f_id';
    protected $fillable = ['f_customer', 'f_inquiry'];

    public static function get_read_inquiry_list($customerID)
    {
        return CustomerInquiryRead::where('f_customer', $customerID)
            ->get();
    }
}
