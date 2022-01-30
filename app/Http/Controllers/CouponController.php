<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

use App\Models\Coupon;
use App\Models\CouponCustomer;
use App\Models\Shop;
use App\Services\ImageService;
use Intervention\Image\Facades\Image;

use Mail;
use App\Mail\ApproveCouponEmail;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $shop = $request->input('shop');
        $brand = $request->input('brand');

        $old = [
            'shop' => $shop,
            'brand' => $brand,
        ];

        $coupon_model = new Coupon();
        $coupons = $coupon_model->get_agree_data($old);
        $image_url = Storage::url('coupon_image/');

        return view('coupon', [
            'coupons' => $coupons,
            'per_page' => 10,
            'image_url' => $image_url,
            'old' => $old,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $coupon_id = $request->input('del_no');
        Coupon::find($coupon_id)->forceDelete();
        CouponCustomer::where('f_coupon', $coupon_id)->forceDelete();
        return redirect("/coupon");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($no=NULL)
    {

        $coupon_model = new Coupon();
        $shop_model = new Shop();

        $shops = $shop_model->get_shops();
        $image_url = Storage::url('coupon_image/');

        if (isset($no))
            $coupon = $coupon_model->get_coupon($no);
        else
            $coupon = NULL;
        return view('coupon_edit', [
            'coupon' => $coupon,
            'shops' => $shops,
            'image_url' => $image_url
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        if ( $request->input('no') != '')
            $coupon = Coupon::find($request->input('no'));
        else
        {
            $coupon = new Coupon;
        }

        $coupon->title = $request->input('title');
        $coupon->content = $request->input('content');
        $coupon->from_date = $request->input('from_date');
        $coupon->to_date = $request->input('to_date');
        $shop_id = $request->input('shop');
        if ($shop_id != null && $shop_id != '') {
            $coupon->shop_id = $shop_id;
        }
        $coupon->reuse = $request->input('reuse');
        $coupon->type = $request->input('type');
        $coupon->amount = $request->input('amount');
        $coupon->unit = $request->input('unit');
        $coupon->agree = 1;
        $coupon->created_by = "admin";
        if ($request->file('thumbnail') != NULL)
        {
            $coupon->image = time().'_'.$request->file( 'thumbnail')->getClientOriginalName();
            $coupon->image_path = asset(Storage::url('coupon_image/').$coupon->image);
            $request->file('thumbnail')->storeAs('public/coupon_image/',$coupon->image);
            $targetName = 'thmb_'.$coupon->image;
            // ImageService::resizeImage(
            //     storage_path('app/public/coupon_image/'.$coupon->image),
            //     storage_path('app/public/coupon_image/'.$targetName),
            //     240,
            //     180
            // );
            // $coupon->thumbnail = asset(Storage::url('coupon_image/').$targetName);
            $thumbFile = Image::make($request->file('thumbnail')->getRealPath())->resize(240, 180, function ($constraint) {
                $constraint->aspectRatio();
            });
            $thumbFile->save(storage_path('app/public/coupon_image/'.$targetName));
            $coupon->thumbnail = asset(Storage::url('coupon_image/').$targetName);
        }
        $coupon->save();

        return redirect("/coupon");
    }
}
