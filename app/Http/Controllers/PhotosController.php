<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use App\Photos;

class PhotosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'images' => 'required',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
            'itemid' => 'required'
        ]);

        $images = [];
        if ($request->images){
            foreach($request->images as $key => $image)
            {
                $imageName = Str::uuid().'.'.$image->extension();  
                $image->move(public_path('img'), $imageName);
            
                $images[]['img_url'] = $imageName;
            }
        }
    
        foreach ($images as $key => $image) {
            $image['item_id'] = $request->itemid;
            \App\Photos::create($image);
        }

        return redirect()->back()->with('success', __('Photo(s) has been added.'));
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
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        try {
            $item = Photos::findOrFail($request->photoid);

            $image_path = public_path().'/img/'.$item['img_url'];
            unlink($image_path);

            $item->delete();
        } catch (Exception $ex) {
            return redirect('/items')->with('error', $ex->getMessage());
        }

        return redirect()->back()->with('success', __('Photo has been deleted.'));
    }
}
