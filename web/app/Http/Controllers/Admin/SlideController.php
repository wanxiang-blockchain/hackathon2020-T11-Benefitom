<?php

namespace App\Http\Controllers\Admin;

use App\Model\Slide;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class SlideController extends Controller
{
    //
    public function index(Request $request)
    {
        $slide = Slide::where(function($query)use($request){
            if($request->get('name')) {
                $query->where('title', 'like', '%'.$request->get('name').'%');
            }
        })->orderBy('created_at','desc')->get();
        return view('admin.slide.index', ['slide'=>$slide]);
    }

    public function getCreate()
    {
        return view('admin.slide.create');
    }

    public function create(Request $request, Slide $slide)
    {
        $request->flash();
        $this->validate($request, [
            'sort'=>'required|numeric',
            'title'=>'required',
	        'pos' => 'required|in:0,1'
        ]);
        $url = ($request->file('url') ? $request->file('url')->store('public/link', 'public') : '');
        $slide->create(array_merge($request->all(), ['url'=>$url]));
        return redirect('/admin/slide');
    }

    public function getEdit(Request $request,Slide $slide)
    {
        return view('admin.slide.edit', ['slide'=>$slide->find($request->get('id'))]);
    }

    public function edit(Request $request, Slide $slide)
    {
        $request->flash();
        $data = $request->all();
        unset($data['_token'], $data['_url'], $data['admin/slide/edit']);
        $this->validate($request, [
            'sort'=>'required|numeric',
            'title'=>'required',
            'id'=>'required',
            'pos' => 'required|in:0,1'
        ]);
        $url = ($request->file('url') ? $request->file('url')->store('public/link', 'public') : '');
        if($url) {
            $data = array_merge($data, ['url'=>$url]);
        }
        $slide->where(['id'=>$request->get('id')])->update($data);
        return redirect('/admin/slide');
    }

    public function delete(Request $request, Slide $slide)
    {
        $item = $slide->find($request->get('id'));
        Storage::disk('public')->delete($item['url']);
        $item->delete();
        return ['code'=>200];
    }
}
