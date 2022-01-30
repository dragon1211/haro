<?php

namespace App\Http\Controllers;

use App\Models\CustomerTop;

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ImageService;
use Intervention\Image\Facades\Image;

class CustomerTopController extends Controller
{
    protected $image_url;

    function __construct()
    {
        $this->image_url = Storage::url('topic_image/');
    }

    public function index()
    {
        $topics = CustomerTop::latest()->paginate(10);
        return view('top.index', [
            'topics' => $topics,
            'per_page' => 10,
            'image_url' => $this->image_url
        ]);
    }

    public function create()
    {
        return view('top.form', [
            'topic' => NULL,
            'image_url' => $this->image_url
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
        CustomerTop::find($request->input('del_no'))->forceDelete();
        return redirect("/topic");
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
    public function edit($no)
    {
        //$no = $request->input('edit_no');

        $topic = CustomerTop::find($no);
        return view('top.form', [
            'topic' => $topic,
            'image_url' => $this->image_url
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
        if ( $request->input('no') != '')
            $topic = CustomerTop::find($request->input('no'));
        else
        {
            $topic = new CustomerTop;
        }

        $topic->title = $request->input('title');
        $topic->content = $request->input('content');
        if ($request->file('thumbnail') != NULL)
        {
            $topic->image = time().'_'.$request->file( 'thumbnail')->getClientOriginalName();
            $topic->image_link = asset(Storage::url('topic_image/').$topic->image);
            $request->file('thumbnail')->storeAs('public/topic_image/',$topic->image);
            $targetName = 'thmb_'.$topic->image;
            // ImageService::resizeImage(
            //     storage_path('app/public/topic_image/'.$topic->image),
            //     storage_path('app/public/topic_image/'.$targetName),
            //     240,
            //     180
            // );
            // $topic->thumbnail = asset(Storage::url('topic_image/').$targetName);
            $thumbFile = Image::make($request->file('thumbnail')->getRealPath())->resize(240, 180, function ($constraint) {
                $constraint->aspectRatio();
            });
            $thumbFile->save(storage_path('app/public/topic_image/'.$targetName));
            $topic->thumbnail = asset(Storage::url('topic_image/').$targetName);
        }
        $topic->save();

        return redirect("/topic");
    }
}
