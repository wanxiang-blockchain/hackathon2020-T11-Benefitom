<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CommandController extends Controller
{
    function cacheClear() {
        if (\Artisan::call('cache:clear',[]) == 0) {
            return ['code' => 200, "message success"];
        }
    }

    function tradeClear() {
        if (\Artisan::call('trade:clear',[]) == 0) {
            return ['code' => 200, "message success"];
        }
    }
}
