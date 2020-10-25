<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Slide extends Model
{
    protected $fillable = [
        'title', 'url', 'link', 'sort','is_show', 'pos'
    ];

    public function pos()
    {
    	return $this->pos == 0 ? '绍德堂' : '艺奖堂';
    }

}
