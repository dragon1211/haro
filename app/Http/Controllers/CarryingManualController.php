<?php

namespace App\Http\Controllers;

use App\Models\CarryingManual;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CarryingManualController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = CarryingManual::orderBy('order_no', 'ASC')->get();
        return view('carrying_manual', ['data' => $data]);
    }

    public function manuals()
    {
        $data = CarryingManual::where('type', 0)->orderBy('order_no', 'ASC')->get();
        return view('carrying_manual', ['data' => $data, 'type' => 0]);
    }

    public function suggest_tools()
    {
        $data = CarryingManual::where('type', 1)->orderBy('order_no', 'ASC')->get();
        return view('carrying_manual', ['data' => $data, 'type' => 1]);
    }

    public function agency_usages()
    {
        $data = CarryingManual::where('type', 2)->orderBy('order_no', 'ASC')->get();
        return view('carrying_manual', ['data' => $data, 'type' => 2]);
    }

    public function add(Request $request)
    {
        $data = new CarryingManual;
        $data->type = $request->type;
        $data->display_name = $request->file('file')->getClientOriginalName();
        $data->filename = $request->file('file')->getClientOriginalName();
        $lastData = CarryingManual::where('type', $request->type)->orderBy('order_no', 'DESC')->get();
        if (count($lastData) == 0) {
            $data->order_no = 0;
        } else {
            $data->order_no = $lastData[0]['order_no'] + 1;
        }

        $type = $data->type;
        if ($type == 0) {
            $data->url = asset(Storage::url('manual/').$data->filename);
            $request->file('file')->storeAs('public/manual/', $data->filename);
        } else {
            $data->url = asset(Storage::url('tools/').$data->filename);
            $request->file('file')->storeAs('public/tools/', $data->filename);
        }

        $data->save();

        if ($type == 0) {
            return redirect('/manual');
        } else if ($type == 1) {
            return redirect('/suggest_tools');
        } else {
            return redirect('/agency_usages');
        }
    }

    public function delete($id)
    {
        $data = CarryingManual::find($id);
        $type = $data->type;
        if ($type == 0) {
            Storage::delete('public/manual/'.$data->filename);
        } else {
            Storage::delete('public/tools/'.$data->filename);
        }
        $data->delete();
        
        if ($type == 0) {
            return redirect('/manual');
        } else if ($type == 1) {
            return redirect('/suggest_tools');
        } else {
            return redirect('/agency_usages');
        }
    }

    public function reorder($type, $currentOrder, $newOrder)
    {
        $data = CarryingManual::reorder($type, $currentOrder, $newOrder);
        return $data;
    }
}
