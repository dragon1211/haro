<?php

namespace App\Http\Controllers;

use App\Models\CarryingGoods;
use App\Models\CarryingGoodsDetail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class CarryingGoodsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $type = $request->input('type');
        $type = -1;
        $tail = request()->segment(count(request()->segments()));
        if ($tail == 'haruto') {
            $type = 0;
        } else if ($tail == 'typef') {
            $type = 1;
        } else if ($tail == 'other') {
            $type = 2;
        }

        $goods = CarryingGoods::get_data($type);
        $image_url = Storage::url('goods_image/');

        return view('goods', [
            'goods' => $goods,
            'per_page' => 15,
            'image_url' => $image_url,
            'type'=> $type
        ]);
    }

    public function delete(Request $request)
    {
        $goods_id = $request->input('del_no');
        CarryingGoods::find($goods_id)->forceDelete();
        CarryingGoodsDetail::where('goods_id', $goods_id)->forceDelete();
        return redirect("/master/carrying_goods");
    }

    public function edit($no=NULL, $page_no=NULL)
    {
        $goods_model = new CarryingGoods();
        $image_url = Storage::url('goods_image/');

        if (isset($no))
            $goods = CarryingGoods::find($no);
        else
            $goods = NULL;
        return view('goods_edit', [
            'goods' => $goods,
            'page_no' => $page_no != NULL ? $page_no : 1,
            'image_url' => $image_url
        ]);
    }

    public function editName($id, Request $request)
    {
        $goodsDetail = CarryingGoodsDetail::find($id);
        $goodsDetail->name = $request->input('name');
        $goodsDetail->save();
        return $goodsDetail;
    }

    public function editPrice($id, Request $request)
    {
        $goodsDetail = CarryingGoodsDetail::find($id);
        $goodsDetail->price = $request->input('price');
        $goodsDetail->save();
        return $goodsDetail;
    }

    public function reorder($goodsId, $currentOrder, $newOrder)
    {
        $goods = CarryingGoodsDetail::reorder($goodsId, $currentOrder, $newOrder);
        return $goods;
    }

    public function update(Request $request)
    {
        if ($request->input('no') != '') {
            $goods = CarryingGoods::find($request->input('no'));
        } else {
            $goods = new CarryingGoods;
        }
        $page_no = $request->input('page_no');

        $goods->type = $request->input('type');
        $goods->name = $request->input('name');
        $goods->price = $request->input('price');
        $goods->agree_kind = $request->input('agree_kind');
        if ($request->file('thumbnail') != NULL)
        {
            $goods->image = time().'_'.$request->file( 'thumbnail')->getClientOriginalName();
            $goods->image_path = asset(Storage::url('goods_image/').$goods->image);
            $request->file('thumbnail')->storeAs('public/goods_image/',$goods->image);
        }
        $goods->save();

        return redirect("/master/carrying_goods?page=$page_no");
    }

    public function detail($id) {
        $goods = CarryingGoods::find($id);
        return view('goods_detail', ['goods' => $goods]);
    }

    public function detail_post($id) {
        $lastGoods = CarryingGoodsDetail::where('goods_id', $id)->orderBy('order_no', 'DESC')->first();
        $orderNo = 0;
        if ($lastGoods != null) {
            $orderNo = $lastGoods->order_no + 1;
        }

        CarryingGoodsDetail::create([
            'goods_id' => $id,
            'order_no' => $orderNo,
            'name' => request('name'),
            'price' => request('price'),
        ]);
        return redirect("/master/carrying_goods/detail/".$id);
    }

    public function delete_detail($id) {
        $detail = CarryingGoodsDetail::find($id);
        $goods_id = $detail->goods_id;
        $detail->delete();
        return redirect("/master/carrying_goods/detail/".$goods_id);
    }

}
