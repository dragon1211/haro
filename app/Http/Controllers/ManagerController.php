<?php

namespace App\Http\Controllers;

use App\Mail\TerminalApproveEmail;

use App\Models\Manager;
use App\Models\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Mail;
use Config;

class ManagerController extends Controller
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
        $province = $request->input('province');
        $county = $request->input('county');
        $old = [
            'shop' => $shop,
            'brand' => $brand,
            'province' => $province,
            'county' => $county,
        ];
        $managers = Manager::filter($old);

        return view('manager', [
            'managers' => $managers,
            'per_page' => 100,
            'old' => $old,
        ]);
    }

    public function allow($id)
    {
        $manager = Manager::find($id);

        if ($manager->allow == 0)
            $manager->allow = 1;
        else
            $manager->allow = 0;

        $manager->save();

        $shop = $manager->shop;
        if ($shop) {
            try {
                $data = [
                    'allow' => $manager->allow,
                    'shop_name' => $shop->name,
                    'device_id' => $manager->device_id,
                    'login_id' => $manager->name,
                    'login_password' => $manager->real_password,
                ];
                Mail::to($shop->email)->send(new TerminalApproveEmail($data, config('mail.MAIL_FROM_ADDRESS')));
            } catch (\Exception $e) {
            }
        }
        return redirect("/manager");
    }
}
