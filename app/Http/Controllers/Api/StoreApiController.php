<?php

namespace App\Http\Controllers\Api;
use Config;
use Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Manager;
use App\Models\Atec;
use App\Models\AtecConfirm;
use App\Models\Customer;
use App\Models\Notice;
use App\Models\Bottle;
use App\Models\Coupon;
use App\Models\Shop;
use App\Models\Carrying;
use App\Models\CarryingHistoryImage;
use App\Models\MyShop;
use App\Models\CarryingGoods;
use App\Models\ShopReserve;
use App\Models\ShopRestDate;
use App\Models\ShopDocomoDate;
use App\Models\Calculation;
use App\Models\CalculationGoods;
use App\Models\Inquiry;
use App\Models\CarryingManual;
use App\Models\ShopImage;
use App\Models\CarryingGoodsDetail;
use App\Models\CustomerNotice;
use App\Http\Controllers\Api\CommonApi;
use App\Mail\RegisterShopEmail;
use App\Mail\StaticEmail;
use App\Mail\TossUpEmail;
use App\Models\Area;
use App\Models\Performer;
use App\Services\ImageService;
use Intervention\Image\Facades\Image;

class StoreApiController extends Controller
{
    public function test(Request $request)
    {
        return response()->json([$request->header('x-access-token'), 'OK']);
    }

    public function login(Request $request)
    {
        $name = $request->input('name');
        $password = $request->input('password');
        $device_id = $request->input('deviceId');

        $account = Manager::authenticate($name, $password, $device_id);
        if (!isset($account))
            return response()->json([
                'result' => Config::get('constants.errno.E_LOGIN'),
                'accessToken' => null,
                'shopData' => null,
            ]);
        else if($account->allow == 0)
            return response()->json([
                'result' => Config::get('constants.errno.E_MANAGER_DISABLED'),
                'accessToken' => null,
                'shopData' => null,
            ]);
        else {
            $account->fcm_token = $request->input('fcmToken');
            $account->save();
            return response()->json([
                'result' => Config::get('constants.errno.E_OK'),
                'accessToken' => $account->access_token,
                'shopData' => Shop::get_shop($account->store),
            ]);
        }
    }

    public function auto_login(Request $request)
    {
        $device_id = $request->input('deviceID');
        $manager = Manager::where('device_id', $device_id)->first();
        if (!$manager) {
            return response()->json([
                'result' => Config::get('constants.errno.E_LOGIN'),
                'accessToken' => null,
                'shopData' => null,
            ]);
        }
        if ($manager->allow == 0) {
            return response()->json([
                'result' => Config::get('constants.errno.E_MANAGER_DISABLED'),
                'accessToken' => null,
                'shopData' => null,
            ]);
        }
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'accessToken' => $manager->access_token,
            'shopData' => Shop::get_shop($manager->store),
        ]);
    }

    public function signup(Request $request)
    {
        $device_id = $request->input('deviceId');
        if (Manager::where('device_id', $device_id)->count() > 0)
        {
            return response()->json([
                'result' => Config::get('constants.errno.E_SHOP_DEVICE_ALREADY_EXIST'),
            ]);
        }
        $account = new Manager;
        $account->device_id = $device_id;
        $account->store = $request->input('store');
        $account->real_password = CommonApi::generate_password();
        $account->password = sha1($account->real_password);
        $account->allow = 0;
        $account->access_token = Manager::generate_access_token($account);

        $account->save();

        $account->name = CommonApi::generate_shop_unique_id($account->id);
        $account->save();
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'accessToken' => $account->access_token,
        ]);
    }

    public function get_stores($area=NULL)
    {
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'data' => CommonApi::get_stores(),
        ]);
    }

    public function add_tossup(Request $request)
    {
        $account = $request->account;
        $content = $request->input('content');
        CommonApi::add_tossup($account->store, $content);

        $shop_dest = $account->shop;
        if ($shop_dest->email) {
            try {
                $data = [
                    'subject' => 'トスアップ申請',
                    'message' => $shop_dest->name.'店からトスアップの申請がありました。'
                ];
                // Mail::to($shop_dest->email)->send(new StaticEmail($data, config('mail.MAIL_FROM_ADDRESS')));
                Mail::to(config('mail.MANAGER_MAIL_ADDRESS'))->send(new StaticEmail($data, config('mail.MAIL_FROM_ADDRESS')));
            } catch (\Exception $e) {
            }
        }

        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'data' => CommonApi::get_tossup($account->store),
        ]);
    }

    public function get_tossup(Request $request)
    {
        $account = $request->account;
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'data' => CommonApi::get_tossup($account->store),
        ]);
    }

    public function get_inquiry(Request $request)
    {
        $account = $request->account;
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'data' => CommonApi::get_inquiry($account->store),
        ]);
    }

    public function reply_inquiry(Request $request)
    {
        $account = $request->account;
        $id = $request->input('id');
        $reply = $request->input('reply');
        CommonApi::reply_inquiry($id, $reply);
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'data' => CommonApi::get_inquiry($account->store),
        ]);
    }

    public function get_atec(Request $request)
    {
        $account = $request->account;
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'data' => CommonApi::get_atec($account->store),
        ]);
    }

    public function confirm_atec(Request $request)
    {
        $account = $request->account;
        $exist = AtecConfirm::where('atec_id', $request->input('atec_id'))->where('shop_id', $account->store)->count();
        if ($exist < 1) {
            $atec = new AtecConfirm;
            $atec->atec_id = $request->input('atec_id');
            $atec->shop_id = $account->store;
            $atec->save();
        }
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'data' => CommonApi::get_atec($account->store),
        ]);
    }

    public function search_member(Request $request)
    {
        $account = $request->account;
        $name = $request->input('name');
        $tel_no = $request->input('tel_no');
        $code = $request->input('code');
        $count = Customer::search_member_count($code, $name, $tel_no);
        if ($count === 0)
        {
            return response()->json([
                'result' => Config::get('constants.errno.E_NO_MEMBER'),
                'count' => $count,
            ]);
        }
        if ($count > 1)
        {
            return response()->json([
                'result' => Config::get('constants.errno.E_TOO_MANY_MEMBER'),
            ]);
        }
        $data = Customer::search_member_id($code, $name, $tel_no);
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'memberId' => $data[0]->id,
            'first_name' => $data[0]->first_name,
            'last_name' => $data[0]->last_name,
        ]);
    }

    public function get_member(Request $request)
    {
        $id = $request->input('id');
        $account = $request->account;
        $carries = Carrying::get_data_by_customer($id, $account->store);
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'data' => Customer::get_member($id),
            'bottleInputData' => Bottle::get_data(1, $id, 0),
            'bottleRemain' => Bottle::get_remain($id, $account->store),
            'carryingData' => $carries,
            'carryingCount' => count($carries),
            'lastCarryingDate' => Carrying::get_last_carrying_date($id, $account->store),
            'myShopData' => MyShop::get_my_shop_history($id),
            'myShop' => MyShop::get_my_shop($id),
            'couponData' => Coupon::get_coupon_by_customer($id, $account->store),
            'calculationData' => Calculation::get_data_by_customer($id, $account->store),
        ]);
    }

    // public function register_member(Request $request)
    // {
    //     if ($request->input('id') == 0)
    //     {
    //         $account = new Customer;
    //     }
    //     else
    //     {
    //         $account = Customer::find($request->input('id'));
    //     }
    //     $account->email = $request->input('email');
    //     $account->password =  sha1($request->input('password'));
    //     $account->fax = $request->input('fax');
    //     $account->birthday = $request->input('birthday');
    //     $account->first_name = $request->input('first_name');
    //     $account->last_name = $request->input('last_name');
    //     $account->name = $request->input('first_name').' '.$request->input('last_name');
    //     $account->first_huri = $request->input('first_huri');
    //     $account->last_huri = $request->input('last_huri');
    //     $account->name_japan = $request->input('first_huri').' '.$request->input('last_huri');
    //     $account->tel_no = $request->input('tel_no');
    //     $account->access_token = Customer::generate_access_token($account);

    //     $account->save();
    //     return response()->json([
    //         'result' => Config::get('constants.errno.E_OK'),
    //         'accessToken' => $account->access_token,
    //     ]);

    // }

    public function get_bottle(Request $request)
    {
        $id = $request->input('id');
        $account = $request->account;
        $from_date = Bottle::get_last_input_date($id, $account->store)->from_date;
        $to_date = strtotime(date("Y-m-d", strtotime($from_date)) . " +1 year");
        $to_date = date('Y-m-d', $to_date);
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'bottleUseDataLimit' => Bottle::get_limit_data(2, $id, $account->store),
            'bottleRemain' => Bottle::get_remain($id, $account->store),
            'from_date' => $from_date,
            'to_date' => $to_date,
        ]);
    }

    public function get_bottle_use(Request $request)
    {
        $id = $request->input('id');
        $account = $request->account;
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'bottleUseData' => Bottle::get_data(2, $id, $account->store),
        ]);
    }

    public function bottle_input(Request $request)
    {
        $id = $request->input('id');
        $half = $request->input('half');
        $full = $request->input('full');
        $account = $request->account;
        $bottle = new Bottle;
        $bottle->customer_id = $id;
        $bottle->shop_id = $account->store;
        $bottle->use_type = 1; //type 1: input
        if ($half === true)
            $bottle->amount = 50;
        else
            $bottle->amount = 100;
        $bottle->save();
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
        ]);
    }

    public function bottle_delete(Request $request)
    {
        $id = $request->input('id');
        $account = $request->account;
        $bottle = new Bottle;
        $bottle->customer_id = $id;
        $bottle->shop_id = $account->store;
        $bottle->use_type = 3; //type 3: delete
        $bottle->amount = Bottle::get_remain($id, $account->store);
        $bottle->save();
        return response()->json([
            'bottleRemain' => Bottle::get_remain($id, $account->store),
            'result' => Config::get('constants.errno.E_OK'),
        ]);
    }

    public function get_coupon(Request $request)
    {
        $account = $request->account;
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'data' => CommonApi::get_coupon_by_shop($account->store),
        ]);
    }

    public function get_last_coupon(Request $request)
    {
        $account = $request->account;
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'data' => CommonApi::get_last_coupon_by_shop($account->store),
        ]);
    }

    public function change_date_coupon(Request $request)
    {
        $account = $request->account;
        $coupon = Coupon::find($request->input('id'));
        $coupon->from_date = $request->input('from_date');
        $coupon->to_date = $request->input('to_date');
        $coupon->save();
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'data' => CommonApi::get_coupon_by_shop($account->store),
            'item' => $coupon,
        ]);
    }

    public function get_notice(Request $request)
    {
        $account = $request->account;
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'data' => CommonApi::get_notice_by_shop($account->store),
        ]);
    }

    public function add_notice(Request $request)
    {
        $account = $request->account;
        if ($request->input('id') == 0) {
            $notice = new Notice;
        }
        else {
            $notice = Notice::find($request->input('id'));
        }
        $notice->kind = $request->input('kind');
        $notice->title = $request->input('title');
        $notice->content = $request->input('content');
        $notice->shop_id = $account->store;
        $notice->agree = 0;
        $notice->created_by = $account->email;

        if ($request->file('_file') != NULL) {
            $notice->image = time().'_'.$request->file( '_file')->getClientOriginalName();
            $notice->image_path = asset(Storage::url('notice_image/').$notice->image);
            $request->file('_file')->storeAs('public/notice_image/', $notice->image);
            $targetName = 'tmb_'.$notice->image;

            // ImageService::resizeImage(
            //     storage_path('app/public/notice_image/'.$notice->image),
            //     storage_path('app/public/notice_image/'.$targetName),
            //     240,
            //     180
            // );
            // $notice->thumbnail = asset(Storage::url('notice_image/').$targetName);

            $thumbFile = Image::make($request->file('_file')->getRealPath())->resize(240, 180, function ($constraint) {
                $constraint->aspectRatio();
            });
            $thumbFile->save(storage_path('app/public/notice_image/'.$targetName));
            $notice->thumbnail = asset(Storage::url('notice_image/').$targetName);

        } else if ($request->input('fileUrl') != NULL) {
            $imageFile = Image::make($request->input('fileUrl'));
            $imageFileName = time().'.jpg';
            $thumbFileName = 'tmb_'.$imageFileName;

            $folderName = 'notice_image/';
            
            $imageFile->save(storage_path('app/public/'.$folderName.$imageFileName));
            $thumbFile = $imageFile->resize(240, 180, function ($constraint) {
                $constraint->aspectRatio();
            });
            $thumbFile->save(storage_path('app/public/'.$folderName.$thumbFileName));

            $notice->image = $imageFileName;
            $notice->image_path = asset(Storage::url($folderName).$imageFileName);
            $notice->thumbnail = asset(Storage::url($folderName).$thumbFileName);
        }
        $notice->save();
        $shop_dest = $account->shop;
        if ($shop_dest->email) {
            try {
                $data = [
                    'subject' => 'お知らせ申請',
                    'message' => $shop_dest->name.'店からお知らせの申請がありました。'
                ];
                // Mail::to($shop_dest->email)->send(new StaticEmail($data, config('mail.MAIL_FROM_ADDRESS')));
                Mail::to(config('mail.MANAGER_MAIL_ADDRESS'))->send(new StaticEmail($data, config('mail.MAIL_FROM_ADDRESS')));
            } catch (\Exception $e) {
            }
        }
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'data' => CommonApi::get_notice_by_shop($account->store),
        ]);
    }

    public function delete_notice(Request $request)
    {
        $account = $request->account;
        $notice_id = $request->input('id');
        Notice::where('id', $notice_id)
                    ->forceDelete();
        CustomerNotice::where('notify_id', $notice_id)
                    ->forceDelete();
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'data' => CommonApi::get_notice_by_shop($account->store),
        ]);
    }

    public function add_coupon(Request $request)
    {
        $account = $request->account;
        $coupon = new Coupon;
        $coupon->title = $request->input('title');
        $coupon->content = $request->input('content');
        $coupon->from_date = $request->input('from');
        $coupon->to_date = $request->input('to');
        $coupon->shop_id = $account->store;
        $coupon->reuse = $request->input('reuse');
        $coupon->type = $request->input('type');
        $coupon->amount = $request->input('amount');
        $coupon->unit = $request->input('unit');
        $coupon->agree = 0;
        $coupon->stop = 0;
        $coupon->created_by = $account->email;

        if ($request->file('_file') != NULL) {
            $coupon->image = time().'_'.$request->file( '_file')->getClientOriginalName();
            $coupon->image_path = asset(Storage::url('coupon_image/').$coupon->image);
            $request->file('_file')->storeAs('public/coupon_image/', $coupon->image);
            $targetName = 'tmb_'.$coupon->image;

            // ImageService::resizeImage(
            //     storage_path('app/public/coupon_image/'.$coupon->image),
            //     storage_path('app/public/coupon_image/'.$targetName),
            //     240,
            //     180
            // );
            // $coupon->thumbnail = asset(Storage::url('coupon_image/').$targetName);

            $thumbFile = Image::make($request->file('_file')->getRealPath())->resize(240, 180, function ($constraint) {
                $constraint->aspectRatio();
            });
            $thumbFile->save(storage_path('app/public/coupon_image/'.$targetName));
            $coupon->thumbnail = asset(Storage::url('coupon_image/').$targetName);

        } else if ($request->input('fileUrl') != NULL) {
            $imageFile = Image::make($request->input('fileUrl'));
            $imageFileName = time().'.jpg';
            $thumbFileName = 'tmb_'.$imageFileName;

            $folderName = 'coupon_image/';
            
            $imageFile->save(storage_path('app/public/'.$folderName.$imageFileName));
            $thumbFile = $imageFile->resize(240, 180, function ($constraint) {
                $constraint->aspectRatio();
            });
            $thumbFile->save(storage_path('app/public/'.$folderName.$thumbFileName));

            $coupon->image = $imageFileName;
            $coupon->image_path = asset(Storage::url($folderName).$imageFileName);
            $coupon->thumbnail = asset(Storage::url($folderName).$thumbFileName);
        }
        $coupon->save();

        $shop_dest = $account->shop;
        if ($shop_dest->email) {
            try {
                $data = [
                    'subject' => 'クーポン申請',
                    'message' => $shop_dest->name.'店からクーポンの申請がありました。'
                ];
                // Mail::to($shop_dest->email)->send(new StaticEmail($data, config('mail.MAIL_FROM_ADDRESS')));
                Mail::to(config('mail.MANAGER_MAIL_ADDRESS'))->send(new StaticEmail($data, config('mail.MAIL_FROM_ADDRESS')));
            } catch (\Exception $e) {
            }
        }

        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'data' => CommonApi::get_coupon_by_shop($account->store),
        ]);
    }

    public function delete_coupon(Request $request)
    {
        $account = $request->account;
        $coupon_id = $request->input('id');
        $coupon = Coupon::find($coupon_id);
        if ($coupon) {
            if ($coupon->agree == 1) {
                $coupon->stop = 1;
                $coupon->save();
            } else {
                $coupon->delete();
            }
        }
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'data' => CommonApi::get_coupon_by_shop($account->store),
        ]);
    }

    public function index_carrying(Request $request)
    {
        $account = $request->account;
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'serial' => Carrying::max('id') + 1,
            'goods' => CommonApi::get_goods_list(),
            'details' => CarryingGoodsDetail::get(),
        ]);
    }

    public function carrying_confirm(Request $request)
    {
        $account = $request->account;
        $carrying = new Carrying;

        $carrying->shop_id = $request->input('shop_id');
        $carrying->customer_id = $request->input('customer_id');
        $carrying->serial_no = $request->input('serial_no');
        $carrying->carrying_kind = $request->input('carrying_kind');
        $carrying->goods_id = $request->input('goods_id');
        $carrying->goods = $request->input('goods');
        $carrying->face = $request->input('face');
        $carrying->phone_kind = $request->input('phone_kind');
        $carrying->amount = $request->input('amount');
        $carrying->price = $request->input('price');
        $carrying->new_product = $request->input('new_product');
        $carrying->agree_kind = $request->input('agree_kind');
        $carrying->notify = $request->input('notify');
        $carrying->goods_name = $request->input('goods_name');
        //$carrying->bottle_use = $request->input('bottle_use');
        //$carrying->bottle_use_amount = $request->input('bottle_use_amount');
        $carrying->performer = $request->input('performer');
        $carrying->date = date('Y-m-d');
        $carrying->checked = $request->input('checked');
        $carrying->goods_subs = $request->input('goods_subs');

        if ($request->file('_file') != NULL) {
            $carrying->sign_image = 'carrying_'.time();
            $carrying->sign_image_path = asset(Storage::url('sign_image/').$carrying->sign_image);
            $request->file('_file')->storeAs('public/sign_image/', $carrying->sign_image);

        } else if ($request->input('fileUrl') != NULL) {
            $imageFile = Image::make($request->input('fileUrl'));
            $imageFileName = 'carrying_'.time();
            $thumbFileName = 'tmb_'.$imageFileName;
            $folderName = 'sign_image/';
            $imageFile->save(storage_path('app/public/'.$folderName.$imageFileName));
            $carrying->sign_image = $imageFileName;
            $carrying->sign_image_path = asset(Storage::url($folderName).$imageFileName);
        }

        $carrying->save();

        if ($carrying->notify == 1) {
            $notice_3_days = new Notice;
            $notice_3_days->kind = 'Wハルトコーティングのご案内';
            $notice_3_days->title = 'Wハルトしませんか！？';
            $notice_3_days->content = 'ハルト施工から3日が経ちました。 Wハルトのお申込みは施工店舗まで！';
            $notice_3_days->image = '';
            $notice_3_days->image_path = '';
            $notice_3_days->shop_id = $carrying->shop_id;
            $notice_3_days->customer_id = $carrying->customer_id;
            $notice_3_days->agree = 0;
            $notice_3_days->save();
        }

        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'newId' => $carrying->id,
        ]);
    }

    public function history_image(Request $request)
    {
        $account = $request->account;
        $history_image = new CarryingHistoryImage;

        $history_image->carrying_id = $request->input('carrying_id');

        if ($request->file('_file') != NULL) {
            $history_image->image = time().'_'.$request->file( '_file')->getClientOriginalName();
            $history_image->image_path = asset(Storage::url('carrying_history_image/').$history_image->image);
            $request->file('_file')->storeAs('public/carrying_history_image/', time().'_'.$request->file( '_file')->getClientOriginalName());
            $targetName = 'tmb_'.$history_image->image;

            // ImageService::resizeImage(
            //     storage_path('app/public/carrying_history_image/'.$history_image->image),
            //     storage_path('app/public/carrying_history_image/'.$targetName),
            //     240,
            //     180
            // );
            // $history_image->thumbnail = asset(Storage::url('carrying_history_image/').$targetName);

            $thumbFile = Image::make($request->file('_file')->getRealPath())->resize(240, 180, function ($constraint) {
                $constraint->aspectRatio();
            });
            $thumbFile->save(storage_path('app/public/carrying_history_image/'.$targetName));
            $history_image->thumbnail = asset(Storage::url('carrying_history_image/').$targetName);

        } else if ($request->input('fileUrl') != NULL) {
            $imageFile = Image::make($request->input('fileUrl'));
            $imageFileName = time().'.jpg';
            $thumbFileName = 'tmb_'.$imageFileName;

            $folderName = 'carrying_history_image/';
            
            $imageFile->save(storage_path('app/public/'.$folderName.$imageFileName));
            $thumbFile = $imageFile->resize(240, 180, function ($constraint) {
                $constraint->aspectRatio();
            });
            $thumbFile->save(storage_path('app/public/'.$folderName.$thumbFileName));

            $history_image->image = $imageFileName;
            $history_image->image_path = asset(Storage::url($folderName).$imageFileName);
            $history_image->thumbnail = asset(Storage::url($folderName).$thumbFileName);
        }

        $history_image->save();

        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
        ]);
    }

    public function get_goods(Request $request)
    {
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'goodsData' => CarryingGoods::get(),
            'details' => CarryingGoodsDetail::get(),
        ]);
    }

    public function get_carryings(Request $request)
    {
        $account = $request->account;
        if ($request->input('today_search') == 1)
        {
            return response()->json([
                'result' => Config::get('constants.errno.E_OK'),
                'data' => Carrying::get_today_data_by_shop($account->store, $request->input('performer')),
            ]);
        }
        if ($request->input('from_date') || $request->input('to_date'))
        {
            return response()->json([
                'result' => Config::get('constants.errno.E_OK'),
                'data' => Carrying::get_date_data_by_shop($account->store, $request->input('from_date'), $request->input('to_date'), $request->input('performer')),
            ]);
        }
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'data' => Carrying::get_data_by_shop($account->store, $request->input('performer')),
        ]);
    }

    public function get_carryings_subgoodsname(Request $request)
    {
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'data' => Carrying::find($request->id)->goods_subs,
        ]);
    }

    public function get_carrying_image_history(Request $request)
    {
        $account = $request->account;
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'imageData' => CarryingHistoryImage::get_image_by_carrying($request->input('carrying_id')),
        ]);
    }

    public function getReservedDataByShop(Request $request)
    {
        $shopID = $request->input('shopID');
        $shop = Shop::find($shopID);
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'restDateList' => ShopReserve::get_rest_date($shopID),
            'restDocomoList' => ShopDocomoDate::where('f_shop_id', $shopID)->get(),
            'reservedData' => ShopReserve::get_reserved_data($shopID),
            'timeList' => ShopReserve::get_time_list(),
            'docomo' => $shop->docomo,
            'link' => $shop->link,
            'classLink' => $shop->class_link,
        ]);
    }

    public function restDate_register(Request $request)
    {
        $count = ShopRestDate::where('f_shop_id', $request->input('shopId'))->count();
        if ($count > 0) {
            ShopRestDate::where('f_shop_id', $request->input('shopId'))->forceDelete();
        }

        $shopId = $request->input('shopId');
        $uploaded = $request->input('dates');
        foreach($uploaded as $dt) {
            $rest = new ShopRestDate;
            $rest->f_shop_id = $shopId;
            $rest->f_rest_date = $dt;
            $rest->save();
        }

        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'restDateList' => ShopReserve::get_rest_date($shopId),
        ]);
    }

    public function restDate_docomo_register(Request $request)
    {
        $count = ShopDocomoDate::where('f_shop_id', $request->input('shopId'))->count();
        if ($count > 0) {
            ShopDocomoDate::where('f_shop_id', $request->input('shopId'))->forceDelete();
        }

        $shopId = $request->input('shopId');
        $uploaded = $request->input('dates');
        foreach($uploaded as $dt) {
            $rest = new ShopDocomoDate;
            $rest->f_shop_id = $shopId;
            $rest->f_rest_date = $dt;
            $rest->save();
        }

        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'restDocomoList' => ShopDocomoDate::where('f_shop_id', $shopId)->get(),
        ]);
    }

    public function restDate_register_time(Request $request)
    {
        if ($request->input('rest_type') == 'OK')
        {
            $rest = new ShopRestDate;
            $rest->f_shop_id = $request->input('shopId');
            $rest->f_rest_date = $request->input('rest_date');
            $rest->f_rest_time = $request->input('rest_time');

            $rest->save();
        } else {
            ShopRestDate::where('f_shop_id', $request->input('shopId'))
                ->where('f_rest_date', $request->input('rest_date'))
                ->where('f_rest_time', $request->input('rest_time'))
                ->forceDelete();
        }
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
        ]);
    }

    public function reserve_confirm(Request $request)
    {
        $reserve = ShopReserve::find($request->input('reserveId'));
        $reserve->f_confirm = 1;
        $reserve->save();

        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
        ]);
    }

    public function calcualtion_save(Request $request)
    {
        $calculation = new Calculation;
        $calculation->customer_id = $request->input('customer_id');
        $calculation->shop_id = $request->input('shop_id');
        $calculation->sum1 = $request->input('sum1');
        $calculation->sum2 = $request->input('sum2');
        $calculation->sum1 = $request->input('sum1');
        $calculation->date = date('Y-m-d');

        $calculation->save();

        foreach ($request->input('goods') as $goods)
            $this->calculation_goods_save($calculation->id, $goods);
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
        ]);
    }

    public function calculation_goods_save($calculation_id, $goods)
    {
        $calculation_goods = new CalculationGoods;
        $calculation_goods->calculation_id = $calculation_id;
        $calculation_goods->type = $goods['type'];
        $calculation_goods->name = $goods['name'];
        $calculation_goods->other = $goods['other'];
        $calculation_goods->amount = $goods['amount'];
        $calculation_goods->price = $goods['price'];

        $calculation_goods->save();
        return;
    }

    public function calcualtion_get_goods(Request $request)
    {
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'goodsData' => CalculationGoods::get_data_by_calculation($request->input('id')),
        ]);
    }

    public function get_new_counts(Request $request)
    {
        $account = $request->account;
        // $new_atec_count = Atec::get_new_atecs($account->store);
        $new_atec_count = $account->shop->unreadAtec();
        $new_inquiry_count = Inquiry::get_new_inqueries($account->store);
        $new_reserve_count = ShopReserve::get_new_reserve($account->store);
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'new_atec_count' => $new_atec_count,
            'new_inquiry_count' => $new_inquiry_count,
            'new_reserve_count' => $new_reserve_count,
        ]);
    }

    public function get_manuals(Request $request)
    {
        $account = $request->account;
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'manuals' => CarryingManual::where('type', 0)->orderBy('order_no', 'ASC')->get(),
        ]);
    }

    public function get_tools(Request $request)
    {
        $account = $request->account;
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'manuals' => CarryingManual::where('type', 1)->orderBy('order_no', 'ASC')->get(),
        ]);
    }

    public function get_agency_usages(Request $request)
    {
        $account = $request->account;
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'manuals' => CarryingManual::where('type', 2)->orderBy('order_no', 'ASC')->get(),
        ]);
    }


    public function change_shop_time(Request $request)
    {
        $account = $request->account;
        $shop = Shop::find($account->store);
        $shop->start_time = $request->input('start_time');
        $shop->end_time = $request->input('end_time');
        $shop->link = $request->input('link');
        $shop->class_link = $request->input('class_link');
        $shop->save();
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'shopData' => Shop::get_shop($account->store),
        ]);
    }

    public function get_nine_images_array($shop_id)
    {
        $images = ShopImage::get_images_by_shop($shop_id);
        $imageList = array();
        $tmp = array();
        $c = 0;
        foreach ($images as $image) {
            $c++;
            $tmp = array('no'=> $c, 'image'=>$image);
            array_push($imageList, $tmp);
        }
        for($i = $c+1; $i <= 9; $i++) {
            $tmp = array('no'=> $i, 'image'=>null);
            array_push($imageList, $tmp);
        }

        return $imageList;

    }

    public function get_shop_images(Request $request)
    {
        $account = $request->account;
        $imageList = $this->get_nine_images_array($account->store);

        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'shopImages' => $imageList,
        ]);
    }

    public function update_shop_image(Request $request)
    {
        $account = $request->account;
        $id = $request->input('id');
        if ($id == 0)
        {
            $shopImage = new ShopImage;
        }
        else
        {
            $shopImage = ShopImage::find($id);
            $org_file = 'storage/shop_image/'.$shopImage->filename;
            if (file_exists($org_file))
                unlink($org_file);
        }

        $shopImage->shop_id = $account->store;

        if ($request->file('_file') != NULL) {
            $shopImage->filename = time().'_'.$request->file( '_file')->getClientOriginalName();
            $shopImage->display_name = $request->file( '_file')->getClientOriginalName();
            $shopImage->url = asset(Storage::url('shop_image/').$shopImage->filename);
            $request->file('_file')->storeAs('public/shop_image/', $shopImage->filename);
            $targetName = 'tmb_'.$shopImage->filename;

            // ImageService::resizeImage(
            //     storage_path('app/public/shop_image/'.$shopImage->filename),
            //     storage_path('app/public/shop_image/'.$targetName),
            //     240,
            //     180
            // );
            // $shopImage->thumbnail = asset(Storage::url('shop_image/').$targetName);

            $thumbFile = Image::make($request->file('_file')->getRealPath())->resize(240, 180, function ($constraint) {
                $constraint->aspectRatio();
            });
            $thumbFile->save(storage_path('app/public/shop_image/'.$targetName));
            $shopImage->thumbnail = asset(Storage::url('shop_image/').$targetName);

        } else if ($request->input('fileUrl') != NULL) {
            $imageFile = Image::make($request->input('fileUrl'));
            $imageFileName = time().'.jpg';
            $thumbFileName = 'tmb_'.$imageFileName;

            $folderName = 'shop_image/';
            
            $imageFile->save(storage_path('app/public/'.$folderName.$imageFileName));
            $thumbFile = $imageFile->resize(240, 180, function ($constraint) {
                $constraint->aspectRatio();
            });
            $thumbFile->save(storage_path('app/public/'.$folderName.$thumbFileName));

            $shopImage->image = $imageFileName;
            $shopImage->image_path = asset(Storage::url($folderName).$imageFileName);
            $shopImage->thumbnail = asset(Storage::url($folderName).$thumbFileName);
        }
        $shopImage->save();
        $imageList = $this->get_nine_images_array($account->store);

        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'shopImages' => $imageList,
        ]);
    }

    public function delete_shop_image(Request $request)
    {
        $account = $request->account;
        $id = $request->input('id');
        $shopImage = ShopImage::find($id);
        if (!is_null($shopImage)) {
            $org_file = 'storage/shop_image/'.$shopImage->filename;
            if (file_exists($org_file)) unlink($org_file);
            $shopImage->delete();
        }
        return response()->json([
            'result' => Config::get('constants.errno.E_OK')
        ]);
    }

    public function address(Request $request) {
        $code = $request->code;
        $area = Area::where('postal', implode('', explode('-', $code)))->first();
        $curl = curl_init();
        $where = urlencode('{ "Postal_Code": "'.$code.'" }');
        curl_setopt($curl, CURLOPT_URL, 'https://parseapi.back4app.com/classes/Japanzipcode_Japan_Postal_Code?limit=10&where=' . $where);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'X-Parse-Application-Id: Sda3VP3mt7IOm4MMfFU9rrBeW1riTtouLN1IcSV7', // This is your app's application id
            'X-Parse-REST-API-Key: lDht8g8bbmY0Uga2pBZqyasy99if6nYBzqMWo2qF' // This is your app's REST API key
        ));
        $data = json_decode(curl_exec($curl)); // Here you have the data that you need
        curl_close($curl);
        return response()->json([
            'text' => $area,
            'location' => $data,
        ]);
    }

    public function register(Request $request) {
        $shop = new Shop();
        $shop->name = $request->input('name');
        $shop->a_province = $request->input('a_province');
        $shop->a_detail = $request->input('a_detail');
        $shop->address = $request->input('a_province').$request->input('a_detail');
        $shop->postal = $request->input('postal');
        $shop->tel_no = $request->input('tel_no');
        $shop->docomo = $request->input('docomo');
        $shop->link = $request->input('link');
        $shop->latitude = $request->input('latitude');
        $shop->longitude = $request->input('longitude');
        $shop->brand = $request->input('brand');
        $shop->email = $request->input('email');
        $shop->class_link = $request->input('class_link');

        if ($request->file('_file') != NULL) {
            $shop->image = time().'_'.$request->file( '_file')->getClientOriginalName();
            $shop->image_path = asset(Storage::url('shop_image/').$shop->image);
            $request->file('_file')->storeAs('public/shop_image/',$shop->image);
            $targetName = 'tmb_'.$shop->image;

            // ImageService::resizeImage(
            //     storage_path('app/public/shop_image/'.$shop->image),
            //     storage_path('app/public/shop_image/'.$targetName),
            //     240,
            //     180
            // );
            // $shop->thumbnail = asset(Storage::url('shop_image/').$targetName);

            $thumbFile = Image::make($request->file('_file')->getRealPath())->resize(240, 180, function ($constraint) {
                $constraint->aspectRatio();
            });
            $thumbFile->save(storage_path('app/public/shop_image/'.$targetName));
            $shop->thumbnail = asset(Storage::url('shop_image/').$targetName);

        } else if ($request->input('fileUrl') != NULL) {
            $imageFile = Image::make($request->input('fileUrl'));
            $imageFileName = time().'.jpg';
            $thumbFileName = 'tmb_'.$imageFileName;

            $folderName = 'shop_image/';
            
            $imageFile->save(storage_path('app/public/'.$folderName.$imageFileName));
            $thumbFile = $imageFile->resize(240, 180, function ($constraint) {
                $constraint->aspectRatio();
            });
            $thumbFile->save(storage_path('app/public/'.$folderName.$thumbFileName));

            $shop->image = $imageFileName;
            $shop->image_path = asset(Storage::url($folderName).$imageFileName);
            $shop->thumbnail = asset(Storage::url($folderName).$thumbFileName);
        }

        $shop->login_id = CommonApi::generate_manager_unique_id($shop->id);
        $shop->login_password = CommonApi::generate_password();
        $shop->save();

        $account = new Manager;
        $account->device_id = $request->input('deviceID');
        $account->name = $shop->login_id;
        $account->store = $shop->id;
        $account->real_password = $shop->login_password;
        $account->password = sha1($account->real_password);
        $account->allow = 0;
        $account->access_token = Manager::generate_access_token($account);

        $account->save();

        // Mail::to($shop->email)->send(new RegisterShopEmail($shop, config('mail.MAIL_FROM_ADDRESS')));
        Mail::to(config('mail.MANAGER_MAIL_ADDRESS'))->send(new RegisterShopEmail($shop, config('mail.MAIL_FROM_ADDRESS')));

        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'shop' => $shop->id,
        ]);
    }

    public function register_device(Request $request)
    {
        $exist = Manager::where('device_id', $request->input('deviceID'))->first();
        if ($exist) {
            return response()->json([
                'result' => Config::get('constants.errno.E_SHOP_DEVICE_ALREADY_EXIST'),
            ]);
        }
        $origin = Manager::findAccount($request->input('name'), $request->input('password'));
        if (!$origin) {
            return response()->json([
                'result' => Config::get('constants.errno.E_NO_MEMBER')
            ]);
        }
        $shop = $origin->shop;
        $account = new Manager;
        $account->device_id = $request->input('deviceID');
        $account->name = CommonApi::generate_manager_unique_id($shop->id);
        $account->store = $shop->id;
        $account->real_password = CommonApi::generate_password();
        $account->password = sha1($account->real_password);
        $account->allow = 0;
        $account->access_token = Manager::generate_access_token($account);
        $account->save();

        $shop_dest = $account->shop;
        if ($shop_dest->email) {
            try {
                $data = [
                    'subject' => 'デバイス申請',
                    'message' => $shop_dest->name.'店舗の新しいデバイスが申請されました。'
                ];
                // Mail::to($shop_dest->email)->send(new StaticEmail($data, config('mail.MAIL_FROM_ADDRESS')));
                Mail::to(config('mail.MANAGER_MAIL_ADDRESS'))->send(new StaticEmail($data, config('mail.MAIL_FROM_ADDRESS')));
            } catch (\Exception $e) {
            }
        }

        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'shop' => $account->id,
        ]);
    }

    public function get_performers(Request $request)
    {
        $shop = $request->input('shop');
        $performers = Performer::where('shop_id', $shop)->orderBy('order_no', 'ASC')->get();
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'performers' => $performers,
        ]);
    }

    public function add_performer(Request $request)
    {
        $name = $request->input('name');
        $shop = $request->input('shop');
        Performer::create([
            'name' => $name,
            'shop_id' => $shop,
        ]);
        $shop = $request->input('shop');
        $performers = Performer::where('shop_id', $shop)->get();
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'performers' => $performers,
        ]);
    }

    public function delete_performer(Request $request)
    {
        $id = $request->input('id');
        Performer::find($id)->delete();
        $shop = $request->input('shop');
        $performers = Performer::where('shop_id', $shop)->get();
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'performers' => $performers,
        ]);
    }

    public function order_performer(Request $request)
    {
        $orders = $request->input('orders');
        foreach($orders as $o) {
            $p = Performer::find($o['id']);
            $p->order_no = $o['order'];
            $p->save();
        }
        $shop = $request->input('shop');
        $performers = Performer::where('shop_id', $shop)->orderBy('order_no', 'ASC')->get();
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'performers' => $performers,
        ]);
    }
}
