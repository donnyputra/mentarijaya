<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;

class CategoryController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('categories.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('categories.create');
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
            'category_code' => 'required',
            'category_description' => 'required'
        ]);
        
        $category = new Category([
            'code' => $request->get('category_code'),
            'description' => $request->get('category_description'),
        ]);
        $category->save();

        return redirect('/categories')->with('success', __('Category has been created.'));
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
        try {
            $category = Category::findOrFail($id);
        } catch (Exception $ex) {
            return redirect()->route('categories.index')->withError($ex->getMessage());
        }

        return view('categories.edit')
            ->with('category', $category);
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
        try {
            $request->validate([
                'category_code' => 'required',
                'category_description' => 'required'
            ]);

            $category = Category::findOrFail($id);
            $category->code = $request->get("category_code");
            $category->description = $request->get("category_description");
            $category->save();

        } catch (Exception $ex) {
            return redirect('/categories')->with('error', $ex->getMessage());
        }

        return redirect('/categories')->with('success', __('Category has been updated.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->delete();
        } catch (Exception $ex) {
            return redirect('/categories')->with('error', $ex->getMessage());
        }

        return redirect('/categories')->with('success', __('Category has been deleted.'));
    }
}
