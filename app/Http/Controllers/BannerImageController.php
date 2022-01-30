<?php

namespace App\Http\Controllers;

use App\Models\BannerImage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class BannerImageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = BannerImage::orderBy('order_no', 'ASC')->get();
        return view('banner_image', ['data' => $data, 'type' => 0]);
    }

    public function banner_images()
    {
        $data = BannerImage::where('type', 0)->orderBy('order_no', 'ASC')->get();
        return view('banner_image', ['data' => $data, 'type' => 0]);
    }

    public function add(Request $request)
    {
        $data = new BannerImage;
        $data->type = $request->type;
        $filename = $request->file('file')->getClientOriginalName();
        $data->filename = $filename;
        $lastData = BannerImage::where('type', $request->type)->orderBy('order_no', 'DESC')->get();
        if (count($lastData) == 0) {
            $data->order_no = 0;
        } else {
            $data->order_no = $lastData[0]['order_no'] + 1;
        }

        
        $type = $data->type;
        if ($type == 0) {
            $data->url = asset(Storage::url('banner_image/').$filename);
            $request->file('file')->storeAs('public/banner_image/',  $filename);
            $targetName = 'tmb_'.$filename;
            $thumbFile = Image::make($request->file('file')->getRealPath())->resize(240, 180, function ($constraint) {
                $constraint->aspectRatio();
            });
            $thumbFile->save(storage_path('app/public/banner_image/'.$targetName));
            $data->thumbnail = asset(Storage::url('banner_image/').$targetName);
        }

        $data->save();

        return redirect('topic/banner_image');
    }

    public function delete($id)
    {
        $data = BannerImage::find($id);
        $type = $data->type;
        if ($type == 0) {
            Storage::delete('public/banner_image/'.$data->filename);
            Storage::delete('public/banner_image/tmb_'.$data->filename);            
        }
        $data->delete();
        
        return redirect('topic/banner_image');
    }

    public function reorder($type, $currentOrder, $newOrder)
    {
        $data = BannerImage::reorder($type, $currentOrder, $newOrder);
        return $data;
    }
}
