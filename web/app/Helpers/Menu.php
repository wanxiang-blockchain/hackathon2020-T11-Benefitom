<?php
namespace App\Helpers;
use Illuminate\Support\Facades\Auth;
use App\Model\Module;
class Menu {
    public static function roleType() {
        $user = Auth::User();
        if ($user) {
            return Auth::User()->role_type;
        }
        return 1;
        return -1;      
    }

    public static function Menu() {
        $m = new Module();
        return $m->getMenus(self::roleType());
    }

    public static function Crumbs()
    {
        $m = new Module();
        return $m->getCrumbsView();
    }
}