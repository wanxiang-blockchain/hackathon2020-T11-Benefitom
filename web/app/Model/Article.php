<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
class Article extends Model
{
    function pictures() {
        return $this->morphMany('App\Model\Picture', 'picturable');
    }

    public function articlable() {
        return $this->morphTo();
    }

    public function getPictureAttribute() {
        $pictures = $this->pictures;
        if (count($pictures) > 0) {
            return $pictures[0]->url;
        } else {
            return "";
        }
    }
}
