<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Services\ImageService;
use Intervention\Image\Facades\Image;

use App\Models\Shop;
use App\Models\Atec;
use App\Models\AtecConfirm;
use App\Models\Bottle;
use App\Models\Calculation;
use App\Models\Carrying;
use App\Models\Coupon;
use App\Models\Inquiry;
use App\Models\Manager;
use App\Models\MyShop;
use App\Models\Notice;
use App\Models\Performer;
use App\Models\ShopDocomoDate;
use App\Models\ShopImage;
use App\Models\ShopReserve;
use App\Models\ShopRestDate;
use App\Models\Tossup;


class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $name = $request->input('name');
        $brand = $request->input('brand');
        $area = $request->input('area');
        $old = [
            'name' => $name,
            'brand' => $brand,
            'area' => $area
        ];
        $shops = Shop::get_data($old);
        $image_url = Storage::url('shop_image/');
        return view('shop', [
            'shops' => $shops,
            'per_page' => 100,
            'image_url' => $image_url,
            'old' => $old,
        ]);
    }

    public function delete(Request $request)
    {
        $shop_id = $request->input('del_no');
        Shop::find($shop_id)->forceDelete();

        Atec::where('shop', $shop_id)->forceDelete();
        AtecConfirm::where('shop_id', $shop_id)->forceDelete();
        Bottle::where('shop_id', $shop_id)->forceDelete();
        Calculation::where('shop_id', $shop_id)->forceDelete();
        Carrying::where('shop_id', $shop_id)->forceDelete();
        Coupon::where('shop_id', $shop_id)->forceDelete();
        Inquiry::where('shop', $shop_id)->forceDelete();
        Manager::where('store', $shop_id)->forceDelete();
        MyShop::where('f_shop_id', $shop_id)->forceDelete();
        Notice::where('shop_id', $shop_id)->forceDelete();
        Performer::where('shop_id', $shop_id)->forceDelete();
        ShopDocomoDate::where('f_shop_id', $shop_id)->forceDelete();
        ShopImage::where('shop_id', $shop_id)->forceDelete();
        ShopReserve::where('f_shop_id', $shop_id)->forceDelete();
        ShopRestDate::where('f_shop_id', $shop_id)->forceDelete();
        Tossup::where('shop', $shop_id)->forceDelete();

        return redirect("/shop");
    }

    public function edit($no=NULL, $page_no=NULL)
    {
        $shop_model = new Shop();
        $image_url = Storage::url('shop_image/');

        if (isset($no)) {
            $shop = $shop_model->get_shop($no);
        } else {
            $shop = NULL;
        }

        return view('shop_edit', [
            'shop' => $shop,
            'image_url' => $image_url,
            'page_no' => $page_no != NULL ? $page_no : 1,
        ]);
    }

    public function update(Request $request)
    {
        if ($request->input('id') != '') {
            $shop = Shop::find($request->input('id'));
        } else {
            $shop = new Shop;
        }

        $page_no = $request->input('page_no');

        $shop->name = $request->input('name');
        $shop->a_province = $request->input('a_province');
        $shop->a_detail = $request->input('a_detail');
        $shop->address = $request->input('a_province').$request->input('a_detail');
        $shop->postal = $request->input('postal');
        $shop->tel_no = $request->input('tel_no');
        $shop->docomo = (NULL !== $request->input('docomo'));
        $shop->link = $request->input('link');
        $shop->latitude = $request->input('latitude');
        $shop->longitude = $request->input('longitude');
        $shop->brand = $request->input('brand');
        $shop->email = $request->input('email');
        $shop->class_link = $request->input('class_link');
        if ($request->file('thumbnail') != NULL)
        {
            $shop->image = time().'_'.$request->file('thumbnail')->getClientOriginalName();
            $shop->image_path = asset(Storage::url('shop_image/').$shop->image);
            $request->file('thumbnail')->storeAs('public/shop_image/',$shop->image);
            $targetName = 'thmb_'.$shop->image;
            // ImageService::resizeImage(
            //     storage_path('app/public/shop_image/'.$shop->image),
            //     storage_path('app/public/shop_image/'.$targetName),
            //     240,
            //     180
            // );
            // $shop->thumbnail = asset(Storage::url('shop_image/').$targetName);
            $thumbFile = Image::make($request->file('thumbnail')->getRealPath())->resize(240, 180, function ($constraint) {
                $constraint->aspectRatio();
            });
            $thumbFile->save(storage_path('app/public/shop_image/'.$targetName));
            $shop->thumbnail = asset(Storage::url('shop_image/').$targetName);
        }
        $shop->save();

        return redirect("/shop?page=$page_no");
    }

    public static function get_counties_by_province(Request $request) {
        $shop = new Shop;
        $counties = Shop::get_counties($request->input('province_no'));
        return response()->json($counties);
    }
}
