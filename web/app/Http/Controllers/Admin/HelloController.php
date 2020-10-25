<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Module;
class HelloController extends Controller
{

    function hello(Module $m) {
        return view("admin.hello");
    }
}
