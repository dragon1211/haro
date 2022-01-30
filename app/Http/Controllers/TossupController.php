<?php

namespace App\Http\Controllers;

use App\Models\Tossup;
use App\Models\Shop;
use App\Models\Inquiry;
use App\Models\Atec;
use Mail;
use Config;

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Mail\StaticEmail;
use Illuminate\Http\Request;

class TossupController extends Controller
{
    public function index()
    {

        $tossup_model = new Tossup();
        $tossups = Tossup::where('tossed', '=', 0)->latest()->paginate(10);
        $shop_model = new Shop();

        $shops = $shop_model->get_shops();

        return view('tossup', [
            'tossups' => $tossups,
            'shops' => $shops,
            'per_page' => 10,
        ]);
    }

    /**
     * tossup a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function tossup(Request $request)
    {
        $id = $request->input('toss_no');
        $shop = $request->input('shop');
        $tossup = Tossup::find($id);

        $atec = new Atec;
        $atec->kind = 'トスアップ';
        $atec->title = 'トスアップ';
        $atec->content = $tossup->content;
        $atec->shop = $shop;
        $atec->save();

        $inquiry = new Inquiry;
        $inquiry->shop = $shop;
        $inquiry->content = $tossup->content;
        $inquiry->sender = $tossup->shop;
        $inquiry->save();

        $tossup->tossed = 1;
        $tossup->save();

        $shop_dest = Shop::find($shop);
        if ($shop_dest && $shop_dest->email) {
            try {
                $data = [
                    'subject' => 'トスアップを受信しました。',
                    'message' => '店舗アプリから内容を確認してください。'
                ];
                Mail::to($shop_dest->email)->send(new StaticEmail($data, config('mail.MAIL_FROM_ADDRESS')));
            } catch (\Exception $e) {
            }
        }

        return redirect("/tossup");
    }
}
