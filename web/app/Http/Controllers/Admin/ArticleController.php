<?php

namespace App\Http\Controllers\Admin;

use App\Model\Article;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Category;
use App\Service\ArticleService;
use Illuminate\Support\Facades\Storage;
class ArticleController extends Controller
{
    function __construct(ArticleService $articleService){
        $this->articleService = $articleService;
    }


    function index(Article $article,Request $request){
        $id = request()->get('id');
        $type = request()->get('type');
        $data['article'] = $article->with('pictures')->where(function($query)use($request,$id,$type){
            if($type == 1){
                $type="App\Model\Category";
            }else if($type == 2){
                $type="App\Model\Project";
            }
            if($id) {
                $query->where('articlable_id', '=', $id);
            }
            if($type) {
                $query->where('articlable_type', '=', $type);
            }
        })->orderBy('updated_at','desc')->paginate(10);
        $data['article']->appends($request->all());
        $data['class'] = ['id' => $id, 'type' => $type];
        return view('admin.article.index',['data' => $data]);
    }
    function getCreate(){
        return view('admin.article.create');
    }

    function create(Request $request){
        request()->flash();
        $this->validate(request(), [
            'title'    => 'required',
//            'picture' => 'required|image',
            'content' => 'required'
        ]);
        if($request->input('articlable_type')==2){
            $this->articleService ->create($request->file('picture'), $request->except("_token"),"App\Model\Project");
        }else{
            $this->articleService ->create($request->file('picture'), $request->except("_token"));

        }
        $data=[
            'id'   => $request->input('articlable_id'),
            'type' => $request->input('articlable_type')
        ];
        return redirect()->route('article/index',$data);
    }
    function getEdit(Request $request){
        $article =Article::with('pictures')->find($request->input('id'));
        $category = Category::all()->map(function($v) {
            return [
                "name"  => $v["name"],
                "value" => $v['id']
            ];
        });
        return view("admin.article.edit", [
            "article" => $article,
            "values" => $category
        ]);
    }
    function edit(Request $request){
        request()->flash();
        $this->validate(request(), [
            'title'    => 'required',
            'content' => 'required'
        ]);
        $article = $this->articleService->edit($request->file('picture'), $request->all());
        if($article['articlable_type'] == 'App\Model\Category'){
            $type = 1;
        }else if ($article['articlable_type'] == 'App\Model\Project'){
            $type = 2;
        }
        $data=[
            'id'   => $article['articlable_id'],
            'type' => $type
        ];
        return redirect()->route('article/index',$data);
    }

    function delete(Request $request){
        $article = Article::find($request->get('id'));
        $article->with('pictures')->get()->map(function($pic)use($request){
            Storage::disk("public")->delete($pic->path);
            if($pic->pictures) {
                foreach ($pic->pictures as $p) {
                    $p->delete();
                }
            }
        });
        $article -> delete();
        return ['code'=>200];
    }

    function change(Request $request){
        $article = Article::find($request->get('id'));
        $article->is_show = $article->is_show == 1 ? 0 : 1;
        $article->save();
        return ['code' => 200, 'message' => 'success'];
    }
}
