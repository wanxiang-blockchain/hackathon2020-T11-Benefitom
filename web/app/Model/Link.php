<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    protected $fillable = [
        'title', 'url', 'link', 'sort','is_show'
    ];
}
