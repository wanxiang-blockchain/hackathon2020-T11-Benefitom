<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{
    function pictures() {
        return $this->morphMany('App\Model\Picture', 'picturable');
    }

    function articles() {
        return $this->morphMany('App\Model\Article', 'articlable');
    }
    public function createCategory($file, $data){
        $picture = new Picture();
        if($file) {
            $path = $file->store("public/category", 'public');
            $picture->url = $path;
        }
        $this->name = $data['name'];
        $this->save();
        return $this->pictures()->save($picture);
    }

    public function edit($file, $data){
        $picture = new Picture();
        $category = $this->find($data['id']);
        if($file) {
            $this->where(['id'=>$data['id']])->with('pictures')->get()->map(function($pic)use($data){
                Storage::disk("public")->delete($pic->path);
                if($pic->pictures) {
                    foreach ($pic->pictures as $picture) {
                        $picture->delete();
                    }
                }
            });
            $path = $file->store("public/category", 'public');
            $picture->url = $path;
            $category->pictures()->save($picture);
        }
        $category->name = $data['name'];
        return $category->save();
    }
}
