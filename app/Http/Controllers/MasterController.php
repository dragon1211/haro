<?php

namespace App\Http\Controllers;

use DB;

use Illuminate\Http\Request;

use App\Models\Customer;
use App\Models\Carrying;
use App\Models\Inquiry;
use App\Models\Policy;
use App\User;

class MasterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show Customer list.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show_customer(Request $request)
    {
        $name = $request->input('member_no');
        $shop = $request->input('shop');
        $brand = $request->input('brand');
        $old = [
            'member_no' => $name,
            'shop' => $shop,
            'brand' => $brand,
        ];
        $name = "%".$name."%";
        $shop = "%".$shop."%";
        $brand = "%".$brand."%";

        $customers = Customer::with('shop')
            ->whereHas('shop', function ($query) use ($shop, $brand) {
                $query->where('name', 'like', $shop)->where('brand', 'like', $brand);
            })
            ->where('member_no', 'like', $name)->latest()->paginate(10);

        return view('customer', [
            'customers' => $customers,
            'old' => $old,
            'per_page' => 10
        ]);
    }

    /**
     * Show Carrying list.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show_carrying(Request $request)
    {
        $date = $request->input('date');
        $goods = $request->input('goods');
        $customer = $request->input('customer');
        $shop = $request->input('shop');
        $performer = $request->input('performer');
        $old = [
            'date' => $date,
            'goods' => $goods,
            'customer' => $customer,
            'shop' => $shop,
            'performer' => $performer,
        ];
        $goods = "%".$goods."%";
        $customer = "%".$customer."%";
        $shop = "%".$shop."%";
        $performer = "%".$performer."%";

        $carries = Carrying::get_data($date, [
            'goods' => $goods,
            'customer' => $customer,
            'shop' => $shop,
            'performer' => $performer,
        ]);

        return view('carrying', [
            'carries' => $carries,
            'old' => $old,
            'per_page' => 10
        ]);
    }

    public function show_inquiry(Request $request)
    {
        $shop_name = $request->input('shop_name');
        $old['shop_name'] = $shop_name;
        $shop_name = "%".$shop_name."%";

        $inquiries = Inquiry::get_data($shop_name);

        return view('inquiry', [
            'inquiries' => $inquiries,
            'old' => $old,
            'per_page' => 10
        ]);
    }

    public function policy()
    {
        $data = Policy::first();
        return view('policy', ['data' => $data]);
    }

    public function faq()
    {
        $data = Policy::skip(1)->first();
        if ($data == null) {
            $data = Policy::create([
                'policy' => '',
                'privacy' => '',
            ]);
        }
        return view('faq', ['data' => $data]);
    }

    public function save_policy(Request $request)
    {
        $policy = $request->input('policy');
        $privacy = $request->input('privacy');

        $data = Policy::first();
        if (!isset($data))
            $data = new Policy;
        if (isset($policy))
            $data->policy = $policy;
        if (isset($privacy))
            $data->privacy = $privacy;
        $data->save();

        return redirect('/master/policy');
    }

    public function save_faq(Request $request)
    {
        $policy = $request->input('policy');
        $privacy = $request->input('privacy');

        $data = Policy::skip(1)->first();
        if (!isset($data))
            $data = new Policy;
        if (isset($policy))
            $data->policy = $policy;
        if (isset($privacy))
            $data->privacy = $privacy;
        $data->save();

        return redirect('/master/faq');
    }
}
