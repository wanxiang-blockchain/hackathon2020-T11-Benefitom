<?php

namespace App\Http\Controllers\Admin;

use App\Model\Category;
use App\Model\Picture;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $category = Category::with('pictures')->where(function($query) use ($request){
            if($request->get('name')) {
                $query->where('name', 'like', '%'.$request->get('name').'%');
            }
        })->orderBy('created_at','desc')->get()->toArray();
        return view('admin.category.index', ['category'=>$category]);
    }

    public function create(Request $request)
    {
        if($request->method() == 'GET') {
            return view('admin.category.create');
        } else {
            request()->flash();
            $this->validate(request(), [
                'name'    => 'required',
                'picture' => 'required|image',
            ]);
            $category = new Category();
            $category->createCategory($request->file('picture'), $request->all());
            return redirect('/admin/category');
        }
    }

    public function edit(Request $request)
    {
        if(request()->method() == 'GET') {
            $category = Category::with('pictures')->find($request->get('id'))->toArray();
            return view('admin.category.edit', ['category'=>$category]);
        } else {
            request()->flash();
            $this->validate(request(), [
                'id'    =>'required|numeric',
                'name'    => 'required',
            ]);
            $category = new Category();
            $category->edit($request->file('picture'), $request->all());
            return redirect('/admin/category');
        }
    }

    public function delete(Request $request)
    {
        $category = Category::find($request->get('id'));
        $category->with('pictures')->get()->map(function($pic)use($request){
            Storage::disk("public")->delete($pic->path);
            Picture::where(['picturable_id'=>$request->get('id'), 'picturable_type'=>'App\Model\Category'])->delete();
        });
        $category->delete();
        return ['code' => 200];
    }
}
