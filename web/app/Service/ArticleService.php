<?php
namespace App\Service;

use App\Repository\ArticleRepository;
use App\Model\Article;
use App\Model\Picture;
use Illuminate\Support\Facades\Storage;
class ArticleService{
    function __construct(ArticleRepository $articleRepository) {
        $this->articleRepository = $articleRepository;
    }

    public function create($file, $data,  $type = 'App\Model\Category'){
        $picture = new Picture();
        $article = new Article();
        if($file) {
            $path = $file->store("public/Article", 'public');
            $picture->url = $path;
        }
        $c = $type::find($data['articlable_id']);
        $article->content = $data['content'];
        $article->title = $data['title'];
        $article->is_show = $data['is_show'];
        $c->articles()->save($article);
        $article->pictures()->save($picture);
        return $article;
    }

    public function edit($file, $data){
        $article = new Article();
        $picture = new Picture();
        $article = $article->find($data['id']);
        if($file) {
            Article::where(['id'=>$data['id']])->with('pictures')->get()->map(function($pic)use($data){
                Storage::disk("public")->delete($pic->path);
                if($pic->pictures) {
                    foreach ($pic->pictures as $p) {
                        $p->delete();
                    }
                }
            });
            $path = $file->store("public/Article", 'public');
            $picture->url = $path;
            $article->pictures()->save($picture);
        }
        $article->content = $data['content'];
        $article->title = $data['title'];
        $article->is_show = $data['is_show'];
        $article->save();
        return $article;
    }
}