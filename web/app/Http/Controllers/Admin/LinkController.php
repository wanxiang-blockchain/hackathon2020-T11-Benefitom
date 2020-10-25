<?php

namespace App\Http\Controllers\Admin;

use App\Model\Link;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class LinkController extends Controller
{
    //
    public function index(Request $request)
    {
        $link = Link::where(function($query)use($request){
            if($request->get('name')) {
                $query->where('title', 'like', '%'.$request->get('name').'%');
            }
        })->orderBy('created_at','desc')->get()->toArray();
        return view('admin.link.index', ['link'=>$link]);
    }

    public function getCreate()
    {
        return view('admin.link.create');
    }

    public function create(Request $request, Link $link)
    {
        $request->flash();
        $this->validate($request, [
            'sort'=>'required|numeric',
            'title'=>'required',
        ]);
        $url = ($request->file('url') ? $request->file('url')->store('public/link', 'public') : '');
        $link->create(array_merge($request->all(), ['url'=>$url]));
        return redirect('/admin/link');
    }

    public function getEdit(Request $request,Link $link)
    {
        return view('admin.link.edit', ['link'=>$link->find($request->get('id'))]);
    }

    public function edit(Request $request, Link $link)
    {
        $request->flash();
        $data = $request->all();
        unset($data['_token'], $data['_url']);
        $this->validate($request, [
            'sort'=>'required|numeric',
            'title'=>'required',
            'id'=>'required',
        ]);
        $url = ($request->file('url') ? ['url'=>$request->file('url')->store('public/link', 'public')] : []);
        $link->where(['id'=>$request->get('id')])->update(array_merge($data, $url));
        return redirect('/admin/link');
    }

    public function delete(Request $request, Link $link)
    {
        $item = $link->find($request->get('id'));
        Storage::disk('public')->delete($item['url']);
        $item->delete();
        return ['code'=>200];
    }
}
