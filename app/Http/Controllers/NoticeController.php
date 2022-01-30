<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use App\Models\Shop;
use App\Models\CustomerNotice;

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Services\ImageService;
use Intervention\Image\Facades\Image;

class NoticeController extends Controller
{
    public function index(Request $request)
    {
        $shop = $request->input('shop');
        $brand = $request->input('brand');
        $area = $request->input('area');

        $old = [
            'shop' => $shop,
            'brand' => $brand,
            'area' => $area
        ];

        $notice_model = new Notice();
        $notices = $notice_model->get_agree_data($old);
        // dd($notices);
        $image_url = Storage::url('notice_image/');
        return view('notice', [
            'notices' => $notices,
            'per_page' => 10,
            'image_url' => $image_url,
            'old' => $old,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $notice_id = $request->input('del_no');
        Notice::find($notice_id)->forceDelete();
        CustomerNotice::where('notify_id', $notice_id)->forceDelete();
        return redirect("/notice");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
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
        //$no = $request->input('edit_no');

        $notice_model = new Notice();
        $shop_model = new Shop();

        $shops = $shop_model->get_shops();
        $image_url = Storage::url('notice_image/');

        if (isset($no))
            $notice = $notice_model->get_notice($no);
        else
            $notice = NULL;
        return view('notice_edit', [
            'notice' => $notice,
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
        $isNew = false;
        if ( $request->input('no') != '')
            $notice = Notice::find($request->input('no'));
        else
        {
            $notice = new Notice;
            $notice->agree = 1;
            $isNew = true;
        }

        $notice->kind = $request->input('kind');
        $notice->title = $request->input('title');
        $notice->content = $request->input('content');
        $notice->shop_id = $request->input('shop');
        $notice->created_by = "admin";
        if ($request->file('thumbnail') != NULL)
        {
            $notice->image = time().'_'.$request->file( 'thumbnail')->getClientOriginalName();
            $notice->image_path = asset(Storage::url('notice_image/').$notice->image);
            $request->file('thumbnail')->storeAs('public/notice_image/',$notice->image);
            $targetName = 'thmb_'.$notice->image;
            // ImageService::resizeImage(
            //     storage_path('app/public/notice_image/'.$notice->image),
            //     storage_path('app/public/notice_image/'.$targetName),
            //     240,
            //     180
            // );
            // $notice->thumbnail = asset(Storage::url('notice_image/').$targetName);
            $thumbFile = Image::make($request->file('thumbnail')->getRealPath())->resize(240, 180, function ($constraint) {
                $constraint->aspectRatio();
            });
            $thumbFile->save(storage_path('app/public/notice_image/'.$targetName));
            $notice->thumbnail = asset(Storage::url('notice_image/').$targetName);
        }
        $notice->save();

        if ($isNew) {
            $customers = Customer::get();
            if ($notice->shop_id != 0) {
                $shop = Shop::find($notice->shop_id);
                $customers = $shop->customers;
            }

            $fcmTokenList = array();
            $fcmNotify = $notice->id;
            $fcmTitle = $request->input('content');
            $fcmBody = $request->input('title');

            foreach($customers as $m) {
                if ($m->fcm_token != null && $m->fcm_flag == 1) {
                    array_push($fcmTokenList, $m->fcm_token);
                    if (count($fcmTokenList) >= 999) {
                        NoticeController::sendPushNotification($fcmTokenList, $fcmNotify, $fcmTitle, $fcmBody);
                        unset($fcmTokenList);
                        $fcmTokenList = array();
                    }
                }
            }
            if (count($fcmTokenList) >= 0) {
                NoticeController::sendPushNotification($fcmTokenList, $fcmNotify, $fcmTitle, $fcmBody);
            }
        }

        return redirect("/notice");
    }

    private function sendPushNotification($fcmTokenList, $fcmNotify, $fcmTitle, $fcmBody) {
        if (count($fcmTokenList) == 0) {
            return;
        }
        
        $client = new Client(['base_uri' => 'https://fcm.googleapis.com/fcm/']);
        $client->request('POST', 'send', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer AAAAP4epmpI:APA91bHjVCcjOcurbg5YYqVpx9cx_KLPMrmrw6l4pTIja0pMEw0SJLkUP6X0x5YZMHFHmpDmrJtVubC71VgPA_ZWB2NlQpgLm_kLT4mBGJfXlduAB-hVu0nwJvuB-TCgQvj7BP-Wc79q',
            ],
            'json' => [
                'registration_ids' => $fcmTokenList,
                'data' => [
                    'type' => 'notify',
                    'notify' => $fcmNotify,
                ],
                'notification' => [
                    'title' => $fcmTitle,
                    'body' => $fcmBody,
                ]
            ],
        ]);
    }
}
