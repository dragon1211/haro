<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::any('/store/test', 'Api\StoreApiController@test');
Route::post('/store/login', 'Api\StoreApiController@login');
Route::post('/store/signup', 'Api\StoreApiController@signup');
Route::get('/store/get_stores/{code?}', 'Api\StoreApiController@get_stores');
Route::post('/store/get_address', 'Api\StoreApiController@address');
Route::post('/store/register', 'Api\StoreApiController@register');
Route::post('/store/register_device', 'Api\StoreApiController@register_device');
Route::post('/store/auto_login', 'Api\StoreApiController@auto_login');
Route::group(['middleware'=>'token:store'], function() {
    Route::post('/store/add_tossup', 'Api\StoreApiController@add_tossup');
    Route::any('/store/get_tossup', 'Api\StoreApiController@get_tossup');
    Route::post('/store/reply_inquiry', 'Api\StoreApiController@reply_inquiry');
    Route::any('/store/get_inquiry', 'Api\StoreApiController@get_inquiry');
    Route::any('/store/get_atec', 'Api\StoreApiController@get_atec');
    Route::any('/store/confirm_atec', 'Api\StoreApiController@confirm_atec');
    Route::any('/store/search_member', 'Api\StoreApiController@search_member');
    Route::post('/store/add_coupon', 'Api\StoreApiController@add_coupon');
    Route::any('/store/delete_coupon', 'Api\StoreApiController@delete_coupon');
    Route::any('/store/get_coupon', 'Api\StoreApiController@get_coupon');
    Route::any('/store/change_date_coupon', 'Api\StoreApiController@change_date_coupon');
    Route::any('/store/get_last_coupon', 'Api\StoreApiController@get_last_coupon');
    Route::post('/store/add_notice', 'Api\StoreApiController@add_notice');
    Route::post('/store/delete_notice', 'Api\StoreApiController@delete_notice');
    Route::any('/store/get_notice', 'Api\StoreApiController@get_notice');
    Route::any('/store/get_member', 'Api\StoreApiController@get_member');
    Route::any('/store/register_member', 'Api\StoreApiController@register_member');
    Route::any('/store/get_bottle', 'Api\StoreApiController@get_bottle');
    Route::any('/store/get_bottle_use', 'Api\StoreApiController@get_bottle_use');
    Route::any('/store/bottle_input', 'Api\StoreApiController@bottle_input');
    Route::any('/store/bottle_delete', 'Api\StoreApiController@bottle_delete');
    Route::any('/store/index_carrying', 'Api\StoreApiController@index_carrying');
    Route::any('/store/carrying_confirm', 'Api\StoreApiController@carrying_confirm');
    Route::any('/store/history_image', 'Api\StoreApiController@history_image');
    Route::any('/store/get_goods', 'Api\StoreApiController@get_goods');
    Route::any('/store/get_carryings', 'Api\StoreApiController@get_carryings');
    Route::any('/store/get_carryings_subgoodsname', 'Api\StoreApiController@get_carryings_subgoodsname');
    Route::any('/store/get_carrying_image_history', 'Api\StoreApiController@get_carrying_image_history');
    Route::any('/store/getReservedDataByShop', 'Api\StoreApiController@getReservedDataByShop');
    Route::any('/store/restDate_register', 'Api\StoreApiController@restDate_register');
    Route::any('/store/restDate_docomo_register', 'Api\StoreApiController@restDate_docomo_register');
    Route::any('/store/restDate_register_time', 'Api\StoreApiController@restDate_register_time');
    Route::any('/store/reserve_confirm', 'Api\StoreApiController@reserve_confirm');
    Route::any('/store/calcualtion_save', 'Api\StoreApiController@calcualtion_save');
    Route::any('/store/calcualtion_get', 'Api\StoreApiController@calcualtion_get');
    Route::any('/store/calcualtion_get_goods', 'Api\StoreApiController@calcualtion_get_goods');
    Route::any('/store/get_new_counts', 'Api\StoreApiController@get_new_counts');
    Route::any('/store/get_manuals', 'Api\StoreApiController@get_manuals');
    Route::any('/store/get_tools', 'Api\StoreApiController@get_tools');
    Route::any('/store/get_agency_usages', 'Api\StoreApiController@get_agency_usages');
    Route::any('/store/change_shop_time', 'Api\StoreApiController@change_shop_time');
    Route::any('/store/get_shop_images', 'Api\StoreApiController@get_shop_images');
    Route::any('/store/update_shop_image', 'Api\StoreApiController@update_shop_image');
    Route::any('/store/delete_shop_image', 'Api\StoreApiController@delete_shop_image');
    Route::any('/store/get_performers', 'Api\StoreApiController@get_performers');
    Route::any('/store/add_performer', 'Api\StoreApiController@add_performer');
    Route::any('/store/delete_performer', 'Api\StoreApiController@delete_performer');
    Route::any('/store/order_performer', 'Api\StoreApiController@order_performer');
});

Route::any('/client/test', 'Api\ClientApiController@test');
Route::post('/client/login', 'Api\ClientApiController@login');
Route::post('/client/sendVerifyNumber', 'Api\ClientApiController@sendVerifyNumber');
Route::post('/client/confirmVerifyNumber', 'Api\ClientApiController@confirmVerifyNumber');
Route::post('/client/signup', 'Api\ClientApiController@signup');
Route::post('/client/createAccount', 'Api\ClientApiController@createAccount');
Route::post('/client/getLicense', 'Api\ClientApiController@getLicense');
Route::post('/client/getFaq', 'Api\ClientApiController@getFaq');
Route::get('/client/resetPassword', 'Api\ClientApiController@resetPassword');
Route::post('/client/doResetPassword', 'Api\ClientApiController@doResetPassword');
Route::any('/client/getProvinceList', 'Api\ClientApiController@getProvinceList');
Route::any('/client/getCityListByProvince', 'Api\ClientApiController@getCityListByProvince');
Route::any('/client/getShopListByCity', 'Api\ClientApiController@getShopListByCity');
Route::any('/client/getShopByLocation', 'Api\ClientApiController@getShopByLocation');
Route::any('/client/getMapCoordinate', 'Api\ClientApiController@getMapCoordinate');
Route::any('/client/getTopicList', 'Api\ClientApiController@getTopicList');

Route::group(['middleware'=>'token:client'], function() {
    Route::any('/logout', 'Api\ClientApiController@logout');
    Route::any('/client/deleteAccount', 'Api\ClientApiController@deleteAccount');
    Route::any('/client/getNotice', 'Api\ClientApiController@getNotice');
    Route::any('/client/getShopList', 'Api\ClientApiController@getShopList');
    Route::any('/client/sendQuestion', 'Api\ClientApiController@sendQuestion');
    Route::any('/client/getShopByArea', 'Api\ClientApiController@getShopByArea');
    Route::any('/client/getBannerImage', 'Api\ClientApiController@getBannerImage');
    Route::any('/client/searchShops', 'Api\ClientApiController@searchShops');
    Route::any('/client/getMyShop', 'Api\ClientApiController@getMyShop');
    Route::any('/client/getShopImage', 'Api\ClientApiController@getShopImage');
    Route::any('/client/registerMyShop', 'Api\ClientApiController@registerMyShop');
    Route::any('/client/getTimeList', 'Api\ClientApiController@getTimeList');
    Route::any('/client/getReservedDataByShop', 'Api\ClientApiController@getReservedDataByShop');
    Route::any('/client/reserveShop', 'Api\ClientApiController@reserveShop');
    Route::any('/client/getSigongList', 'Api\ClientApiController@getSigongList');
    Route::any('/client/getCouponList', 'Api\ClientApiController@getCouponList');
    Route::any('/client/useCoupon', 'Api\ClientApiController@useCoupon');
    Route::any('/client/expireCoupon', 'Api\ClientApiController@expireCoupon');
    Route::any('/client/getQuestionList', 'Api\ClientApiController@getQuestionList');
    Route::any('/client/calcUnReadInquires', 'Api\ClientApiController@calcUnReadInquires');
    Route::any('/client/setInquiryRead', 'Api\ClientApiController@setInquiryRead');
    Route::any('/client/getShopByProvince', 'Api\ClientApiController@getShopByProvince');
    Route::any('/client/generateTransferCode', 'Api\ClientApiController@generateTransferCode');
    Route::any('/client/fetchTransferCode', 'Api\ClientApiController@fetchTransferCode');
    Route::any('/client/switchNotify', 'Api\ClientApiController@switchNotify');
    Route::any('/client/readNotice', 'Api\ClientApiController@readNotice');
});
