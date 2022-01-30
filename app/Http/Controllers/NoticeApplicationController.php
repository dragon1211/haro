<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Mail;
use Config;

use App\Models\Notice;
use App\Models\Shop;
use App\Models\CustomerNotice;
use App\Mail\ApproveNoticeEmail;
use App\Mail\DisapproveNoticeEmail;

class NoticeApplicationController extends Controller
{
    public function index()
    {
        $notice_model = new Notice();
        $notices = $notice_model->get_application_data();
        $image_url = Storage::url('notice_image/');
        return view('notice_application', [
            'notices' => $notices,
            'per_page' => 10,
            'image_url' => $image_url
        ]);
    }

    public function update(Request $request)
    {
        $notice = Notice::find($request->input('agree_no'));
        $agree = $request->input('agree');
        $notice->agree = $agree;
        $notice->save();

        $shop = Shop::find($notice->shop_id);
        $customers = $shop->customers;

        if ($shop && $shop->email) {
            try {
                $data = [
                    'kind' => $notice->kind,
                    'title' => $notice->title,
                ];
                if ($agree == 1) {
                    Mail::to($shop->email)->send(new ApproveNoticeEmail($data, config('mail.MAIL_FROM_ADDRESS')));
                } else {
                    Mail::to($shop->email)->send(new DisapproveNoticeEmail($data, config('mail.MAIL_FROM_ADDRESS')));
                }
            } catch (\Exception $e) {
            }
        }
        CustomerNotice::where('notify_id', $notice->id)->delete();

        if ($agree == 1) {
            $fcmTokenList = array();
            $fcmNotify = $notice->id;
            $fcmTitle = $notice->title;
            $fcmBody = $notice->content;
    
            foreach($customers as $m) {
                if ($m->fcm_token != null && $m->fcm_flag == 1) {
                    array_push($fcmTokenList, $m->fcm_token);
                    if (count($fcmTokenList) >= 999) {
                        NoticeApplicationController::sendPushNotification($fcmTokenList, $fcmNotify, $fcmTitle, $fcmBody);
                        unset($fcmTokenList);
                        $fcmTokenList = array();
                    }
                }
            }
            if (count($fcmTokenList) >= 0) {
                NoticeApplicationController::sendPushNotification($fcmTokenList, $fcmNotify, $fcmTitle, $fcmBody);
            }
        }
        
        return redirect("/notice_application");
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
