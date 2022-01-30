<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonApi;
use Faker\Provider\DateTime;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Notice;
use App\Models\Inquiry;
use App\Models\Area;
use App\Models\Shop;
use App\Models\Coupon;
use App\Models\MyShop;
use App\Models\Carrying;
use App\Models\ShopReserve;
use App\Models\CustomerInquiryRead;
use App\Models\CouponCustomer;
use App\Models\CustomerNotice;
use App\Models\CustomerVerifyNumber;
use App\Models\Policy;
use App\Models\ShopRestDate;
use App\Models\ShopDocomoDate;
use App\Models\CustomerTop;
use App\Models\BannerImage;
use Config;

class ClientApiController extends Controller
{
    public function test(Request $request)
    {
        return response()->json([$request->header('x-access-token'), 'OK']);
    }

    public function login(Request $request)
    {
        $id = $request->input('id');
        $password = $request->input('password');
        $device_id = $request->input('deviceId');
        $transferCode = $request->input('transferCode');
        $token = $request->input('token');
        if ($transferCode) {
            $account = Customer::authenticate_transferCode($transferCode);
        } else {
            $account = Customer::authenticate($id, $password, $device_id);
        }
        if (!isset($account)) {
            return response()->json([
                'result' => Config::get('constants.errno.E_LOGIN'),
                'access_token' => null
            ]);
        }
        else {
            $account->fcm_token = $token;
            $account->save();
            return response()->json([
                'result' => Config::get('constants.errno.E_OK'),
                'account' => $account,
                'accessToken' => $account->access_token,
                'shop' => $account->shop->first(),
                'new_notice_count' => $account->unreadNotice($account->member_no),
            ]);
        }
    }

    public function deleteAccount(Request $request)
    {
        $customerID = $request->input('customerID');
        $customer = Customer::find($customerID);
        if ($customer) {
            $customer->delete();
        }

        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
        ]);
    }

    public function getLicense()
    {
        $policy = Policy::first();
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'license' => $policy,
        ]);
    }

    public function getFaq()
    {
        $policy = Policy::skip(1)->first();
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'faq' => $policy,
        ]);
    }

    public function createAccount(Request $request)
    {
        $shopID = $request->input('shop');
        $member_no = CommonApi::generate_member_unique_id($shopID);
        $password = CommonApi::generate_password();
        $account = new Customer;
        $account->password =  $password;
        $account->device_id = $request->input('device_id');
        $account->member_no = $member_no;
        $account->access_token = Customer::generate_access_token($account->device_id, $member_no);
        $account->fcm_token = $request->input('fcm_token');
        $account->fcm_flag = 1;

        $check_account = Customer::authenticate($member_no, $password, $account->device_id);
        if (isset($check_account)) {
            return response()->json([
                'result' => Config::get('constants.errno.E_MEMBER_ALREADY_EXIST'),
            ]);
        } else {
            $account->save();
            $myShop = new MyShop;
            $myShop->f_customer_id = $account->id;
            $myShop->f_shop_id = $shopID;
            $myShop->save();
            return response()->json([
                'result' => Config::get('constants.errno.E_OK'),
                'accessToken' => $account->access_token,
                'member_no' => $member_no,
                'password' => $account->password,
                'id' => $account->id,
                'new_notice_count' => $account->unreadNotice($account->member_no),
            ]);
        }
    }

    public function signup(Request $request)
    {
        $memberInfo = $request->input('memberInfo');
        $isUpdate = $request->input('isUpdate');
        if ($isUpdate == true) {
            $updateInfo = array(
                'email' => $memberInfo['email'],
                'password' => $memberInfo['password'],
                'fax' => $memberInfo['fax'],
                'birthday' => $memberInfo['birthDate'],
                'first_name' => $memberInfo['firstName'],
                'last_name' => $memberInfo['lastName'],
                'name' => $memberInfo['firstName'].' '.$memberInfo['lastName'],
                'first_huri' => $memberInfo['japanese_firstName'],
                'last_huri' => $memberInfo['japanese_lastName'],
                'tel_no' => $memberInfo['phoneNumber'],
                'device_id' => $memberInfo['device_id'],
                'access_token' => Customer::generate_access_token($memberInfo['device_id'], $memberInfo['email'])
            );
            Customer::updateMember($updateInfo, $request->input('member_no'));
            return response()->json([
                'result' => Config::get('constants.errno.E_OK'),
                'accessToken' => $updateInfo['access_token'],
            ]);
        } else {
            $account = new Customer;
            $account->email = $memberInfo['email'];
            $account->password =  sha1($memberInfo['password']);
            $account->fax = $request->input('fax');
            $account->birthday = $memberInfo['birthDate'];
            $account->first_name = $memberInfo['firstName'];
            $account->last_name = $memberInfo['lastName'];
            $account->name = $memberInfo['firstName'].' '.$memberInfo['lastName'];
            $account->first_huri = $memberInfo['japanese_firstName'];
            $account->last_huri = $memberInfo['japanese_lastName'];
            $account->name_japan = $memberInfo['japanese_firstName'].' '.$memberInfo['japanese_lastName'];
            $account->tel_no = $request->input('phoneNumber');
            $account->member_no = CommonApi::generate_member_unique_id($account->first_name, $account->last_name, $account->email);
            $account->device_id = $memberInfo['device_id'];
            $account->access_token = Customer::generate_access_token($account->device_id, $account->email);

            $check_account = Customer::authenticate($account->email, $account->password, $account->device_id);
            if (isset($check_account)) {
                return response()->json([
                    'result' => Config::get('constants.errno.E_MEMBER_ALREADY_EXIST'),
                ]);
            } else {
                $account->save();
                return response()->json([
                    'result' => Config::get('constants.errno.E_OK'),
                    'accessToken' => $account->access_token,
                ]);
            }
        }

    }

    public function sendVerifyNumber(Request $request)
    {
        $phoneNumber = $request->input('phoneNumber');
        CustomerVerifyNumber::where('f_phone_number', $phoneNumber)->forceDelete();
        $forgotAccount = $request->input('forgotAccount');
        if ($forgotAccount === true)
        {
            $tmp = CustomerVerifyNumber::check_phoneNumber($phoneNumber);
            if (!isset($tmp) || count($tmp) < 1) {
               return response()->json([
                   'result' => Config::get('constants.errno.E_INTERNAL'),
                   'aa' => $tmp,
               ]);
            }
        }
        $verifyNumber = (string)rand(100000, 999999);
        CommonApi::sendSMS($phoneNumber, $verifyNumber);
        $verify = new CustomerVerifyNumber;
        $verify->f_phone_number = $phoneNumber;
        $verify->f_verify_number = $verifyNumber;
        $verify->save();

        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'verifyNumber' => $verifyNumber,
        ]);
    }

    public function confirmVerifyNumber(Request $request)
    {
        $verifyNumber = $request->input('verifyNumber');
        $phoneNumber = $request->input('phoneNumber');
        $forgotAccount = $request->input('forgotAccount');

        $verifyNumber_org = CustomerVerifyNumber::get_verifyNumber_by_phoneNumber($phoneNumber);
        $isMatch = 999;
        $customerAccount = CustomerVerifyNumber::check_phoneNumber($phoneNumber);
        $resetURL = '';

        if (isset($verifyNumber_org) && $verifyNumber_org['f_verify_number'] == $verifyNumber) {
            $isMatch = 0;
            CustomerVerifyNumber::where('f_phone_number', $phoneNumber)->forceDelete();
            if ($forgotAccount === true) {
                $email = $customerAccount[0]->email;
                $customerID = $customerAccount[0]->id;
                $resetURL = CommonApi::makeResetURL($customerID);
                CommonApi::sendSMS($phoneNumber, $email);
                CommonApi::sendEmail($email, $resetURL);
            }
        }

        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'isMatch' => $isMatch,
            'memberInfo' => $customerAccount,
            'resetURL' => $resetURL,
        ]);
    }

    public function getNotice(Request $request)
    {
        if ($request->account) {
            $account = $request->account;
            $token = $request->input('token');
            if ($token != null && $token != '') {
                $account->fcm_token = $token;
            }
            $account->save();
            return response()->json([
                'result' => Config::get('constants.errno.E_OK'),
                'notice' => Notice::get_by_shop($request->input('shop'), $account->member_no),
                'new_notice_count' => $account->unreadNotice($account->member_no),
                'notice_flag' => $account->fcm_flag,
            ]);
        } else {
            return response()->json([
                'result' => Config::get('constants.errno.E_NO_MEMBER'),
            ]);
        }
    }

    public function readNotice(Request $request)
    {
        $exist = CustomerNotice::where('notify_id', $request->input('notify'))
            ->where('customer_id', $request->account->id)->count();
        if ($exist == 0) {
            CustomerNotice::create([
                'notify_id' => $request->input('notify'),
                'customer_id' => $request->account->id,
            ]);
        }
        return response() -> json([
            'result' => Config::get('constants.errno.E_OK'),
        ]);
    }

    public function getShopList(Request $request)
    {
        $customerID = $request->input('customerID');
        $myShopID = $request->input('myShopID');
        if (isset($customerID)) {
            return response() -> json([
                'result' => Config::get('constants.errno.E_OK'),
                'recentReserveDate' => ShopReserve::get_my_recent_reserve_date($customerID),
                'shopList' => Shop::get_shops($myShopID),
            ]);
        } else {
            return response() -> json([
                'result' => Config::get('constants.errno.E_OK'),
                'shopList' => Shop::get_shops($myShopID),
            ]);
        }
    }

    public function searchShops(Request $request)
    {
        $shops = Shop::filter_shop_by_address($request->input('address'));
        return response() -> json([
            'result' => Config::get('constants.errno.E_OK'),
            'shopList' => $shops,
        ]);
    }

    public function sendQuestion(Request $request)
    {
        $inquiry = new Inquiry;
        $inquiry->shop = $request->input('shop');
        $inquiry->content = $request->input('contentOfQuery');
        $inquiry->customer = $request->input('customer');
        $inquiry->save();

        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
        ]);
    }

    public function getProvinceList()
    {
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'provinceList' => Shop::get_province_list(),
        ]);
    }

    public function getCityListByProvince(Request $request)
    {
        $name_province = $request->input('name_province');
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'cityList' => Shop::get_city_list_by_province($name_province),
        ]);
    }

    public function getMapCoordinate()
    {
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'coordinate' => Area::get_map_coordinate(),
        ]);
    }

    public function getShopListByCity(Request $request)
    {
        $a_province = $request->input('a_province');
        $a_detail = $request->input('a_detail');
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'shopList' => Shop::get_shop_by_city($a_province, $a_detail),
        ]);
    }

    public function getShopByLocation(Request $request)
    {
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $minDistance = -1;
        $shipList = Shop::get_shops(NULL);

        foreach ($shipList as $shop) {
            $distance = ClientApiController::distance($latitude, $longitude, $shop->latitude, $shop->longitude, "K");
            if ($minDistance < 0) {
                $minDistance = $distance;
                $minShop = $shop;
            } else if ($distance < $minDistance) {
                $minDistance = $distance;
                $minShop = $shop;
            }
        }

        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'shop' => $minShop,
        ]);
    }

    public function distance($lat1, $lon1, $lat2, $lon2, $unit) {
        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
            return 0;
        } else {
            $theta = $lon1 - $lon2;
            $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;
            $unit = strtoupper($unit);
        
            if ($unit == "K") {
                return ($miles * 1.609344);
            } else if ($unit == "N") {
                return ($miles * 0.8684);
            } else {
                return $miles;
            }
        }
    }

    public function getShopByArea(Request $request)
    {
        $areaID = $request->input('areaID');
        $postalCode = $request->input('postalCode');
        if (isset($postalCode)) {
            return response()->json([
                'result' => Config::get('constants.errno.E_OK'),
                'shop' => Shop::get_shop_by_postalCode($postalCode),
            ]);
        } else {
            return response()->json([
                'result' => Config::get('constants.errno.E_OK'),
                'shopList' => Shop::get_shop_by_area_id($areaID),
            ]);
        }
    }

    public function getBannerImage(Request $request)
    {
        $type = $request->input('type');
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'imageList' => BannerImage::where('type', $type)->orderBy('order_no', 'ASC')->get(),
        ]);
    }

    public function getShopByProvince(Request $request)
    {
        $name_province = $request->input('province');
        $shop_groupByCity = Shop::get_shop_by_province($name_province);
        $cityList = array_keys($shop_groupByCity);
        $shopList = array_values($shop_groupByCity);
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'cityList' => $cityList,
            'shopList' => $shopList,
        ]);
    }

    public function getMyShop(Request $request)
    {
        $customerID = $request->input('customerID');
        $myShop = MyShop::get_my_shop($customerID);
        $shopModel = Shop::find($myShop->f_shop_id);
        if(isset($myShop)) {
            return response()->json([
                'result' => Config::get('constants.errno.E_OK'),
                'restType' => ShopRestDate::check_rest($myShop->f_shop_id),
                'myShop' => $myShop,
                'myShopImage' => MyShop::get_my_shop_image($myShop->f_shop_id),
                'businessHours' => [$shopModel->start_time, $shopModel->end_time],
                'restDocomoList' => ShopDocomoDate::where('f_shop_id', $myShop->f_shop_id)->get(),
                'shopOrg' => $shopModel
            ]);
        } else {
            return response()->json([
                'result' => Config::get('constants.errno.E_NO_MY_SHOP'),
            ]);
        }
    }

    public function getShopImage(Request $request)
    {
        $shopID = $request->input('shopID');
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'shopImage' => MyShop::get_my_shop_image($shopID),
        ]);
    }


    public function registerMyShop(Request $request)
    {
        $customerID = $request->input('customerID');
        $shopID = $request->input('shopID');
        MyShop::where('f_customer_id', $customerID)->delete();
        $myShop = new MyShop;
        $myShop->f_customer_id = $customerID;
        $myShop->f_shop_id = $shopID;
        $myShop->save();

        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
        ]);
    }

    public function getTimeList()
    {
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'timeList' => ShopReserve::get_time_list(),
        ]);
    }

    public function getReservedDataByShop(Request $request)
    {
        $shopID = $request->input('shopID');
        $customerID = $request->input('customerID');
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'restDateList' => ShopReserve::get_rest_date($shopID),
            'reservedData' => ShopReserve::get_reserved_data($shopID),
        ]);
    }

    public function reserveShop(Request $request)
    {
        $customerID = $request->input('customerID');
        $shopID = $request->input('shopID');
        $purpose = $request->input('purpose');
        $other = $request->input('other');
        $reserveDate = $request->input('reserveDate');
//        $reserveCancelDate = $request->input('reserveCancelDate');
//        if (isset($reserveCancelDate) && count($reserveCancelDate) > 0) {
//            $cancelList = array();
//            foreach ($reserveCancelDate as $cancel)
//                $cancelList[] = $cancel['date'].'|'.$cancel['time'];
//            ShopReserve::cancel_reserve($shopID, $customerID, $cancelList);
//        }
        if (isset($reserveDate))
        {
            $reserveData = array(
                'f_customer_id' => $customerID,
                'f_shop_id' => $shopID,
                'f_reserve_date' => $reserveDate['date'],
                'f_reserve_time' => $reserveDate['time'],
                'f_reserve_purpose' => $purpose,
                'updated_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'f_other' => $other
            );
            ShopReserve::reserve_visit_date($reserveData);
        }
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
        ]);
    }

    public function getSigongList(Request $request)
    {
        $customerID = $request->input('customerID');
        $sortMode = $request->input('sortMode');
        $carryingType = $request->input('type');
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'sigongList' => Carrying::get_sigong_by_customer($customerID, $sortMode, $carryingType),
        ]);
    }

    public function getCouponList(Request $request)
    {
        $myShopID = $request->input('myShopID');
        $customerID = $request->input('customerID');
        [$myShopCoupon, $commonCoupon, $usedCoupon, $usedCouponState, $isExpireList] = Coupon::get_coupon_by_shop_id($myShopID, $customerID);
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'commonCoupon' => $commonCoupon,
            'myShopCoupon' => $myShopCoupon,
            'usedCoupon' => $usedCoupon,
            'usedCouponState' => $usedCouponState,
            'isExpireList' => $isExpireList,
        ]);
    }

    public function useCoupon(Request $request)
    {
        $useCoupon = new CouponCustomer;
        $useCoupon->f_customer = $request->input('customerID');
        $useCoupon->f_coupon = $request->input('couponID');
        $useCoupon->save();
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
        ]);
    }

    public function expireCoupon(Request $request)
    {
        $customerID = $request->input('customerID');
        $couponID = $request->input('couponID');
        Coupon::expire_customer_coupon($customerID, $couponID);
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
        ]);
    }

    public function getQuestionList(Request $request)
    {
        $customerID = $request->input('customerID');
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'inquiryList' => Inquiry::get_inquiry_list($customerID),
            'readInquiryList' => CustomerInquiryRead::get_read_inquiry_list($customerID),
        ]);
    }

    public function calcUnReadInquires(Request $request)
    {
        $customerID = $request->input('customerID');
        $total = Inquiry::count_inquiries_by_customer($customerID);
        $read = Inquiry::count_read_inquiries_by_customer($customerID);
        if (isset($total) && isset($read)) {
            return response()->json([
                'result' => Config::get('constants.errno.E_OK'),
                'countUnread' => $total - $read,
            ]);
        } else {
            return response()->json([
                'result' => Config::get('constants.errno.E_INTERNAL'),
            ]);
        }
    }

    public function setInquiryRead(Request $request)
    {
        $customerID = $request->input('customerID');
        $inquiryID = $request->input('inquiryID');
        $inquiryRead = new CustomerInquiryRead;
        $inquiryRead->f_customer = $customerID;
        $inquiryRead->f_inquiry = $inquiryID;
        $inquiryRead->save();
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $resetToken = $request->input('resetToken');
        $customerID = $request->input('customerID');
        $rlt = Customer::checkResetToken($customerID, $resetToken);
        if (isset($rlt) && count($rlt) > 0) {
            return view('api.password_reset', ['customerID' => $customerID]);
        }
    }

    public function doResetPassword(Request $request)
    {
        $password = $request->input('password');
        $customerID = $request->input('customerID');
        Customer::resetPassword($customerID, $password);
    }

    public function generateTransferCode(Request $request)
    {
        $customerID = $request->input('customerID');
        $transferCode = CommonApi::generate_transcode();
        Customer::set_transfer_code($customerID, $transferCode);
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'code' => $transferCode,
        ]);
    }

    public function fetchTransferCode(Request $request)
    {
        $customerID = $request->input('customerID');
        $customer = Customer::find($customerID);
        $transferCode = '';
        //transfercode is still valid
        if ($customer->transferCode != NULL && $customer->transferCode_date > date('Y-m-d H:i:s', time() - 60 * 60 * 24)) {
            $transferCode = $customer->transferCode;
        }
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'code' => $transferCode,
        ]);
    }

    public function getMyShopDocomoDays(Request $request)
    {
        $shopID = $request->input('shop');
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'restDocomoList' => ShopDocomoDate::where('f_shop_id', $shopID)->get(),
        ]);
    }

    public function getTopicList(Request $request)
    {
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'topics' => CustomerTop::orderBy('created_at', 'DESC')->get(),
        ]);
    }

    public function switchNotify(Request $request)
    {
        $customerID = $request->input('customerID');
        $fcm_flag = $request->input('fcmFlag');
        $customer = Customer::find($customerID);
        $customer->fcm_flag = $fcm_flag;
        $customer->save();
        return response()->json([
            'result' => Config::get('constants.errno.E_OK'),
            'updated' => $customer->fcm_flag,
        ]);
    }
}
