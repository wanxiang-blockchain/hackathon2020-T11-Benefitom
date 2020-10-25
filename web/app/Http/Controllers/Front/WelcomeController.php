<?php

namespace App\Http\Controllers\Front;

use App\Model\Article;
use App\Model\Category;
use App\Model\Link;
use App\Model\Project;
use App\Model\ProjectOrder;
use App\Model\Slide;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Detection\MobileDetect;

class WelcomeController extends Controller
{
	public function tang()
	{
		return view('front.tang');
	}
	public function index()
	{
		$minutes = 1;
		$data = [
			'banners' => \Cache::remember('banner', $minutes,
				function () {
					return Slide::where(['is_show' => 1])
						->where('pos', 0)
						->select(['title', 'link', 'url'])
						->orderBy('sort', 'desc')
						->get();
				}),
			'projects' => Project::where(['is_show' => 1])
					->orderBy('start', 'desc')
					->limit(2)
					->get(),
            'num' => Project::where(['is_show' => 1])
                ->count(),
             'notice' => Article::where(['articlable_id'=>2,'articlable_type'=>'App\Model\Category','is_show' => '1'])->orderBy('updated_at','desc')->get(),
             'articlePreview' => Article::with('pictures')->where(['articlable_id'=>5,'articlable_type'=>'App\Model\Category','is_show' => '1'])->orderBy('updated_at','desc')->get(),
			'category' => Category::with(['articles'=>function($query){
			    $query->where('is_show','=', 1);
			    $query->orderBy('updated_at','desc');
            }])->with('pictures')->limit(3)->get(),
			'links' => Link::where(['is_show' => 1])
                        ->where('url','!=', " ")
                        ->orderBy('sort', 'desc')
                        ->select(['title', 'url', 'link'])
                        ->get(),
            'friends' =>  Link::where(['is_show' => 1])
                        ->where('url','=', " ")
                        ->orderBy('sort', 'desc')
                        ->select(['title', 'url', 'link'])
                        ->get(),
		];
		foreach($data['category'] as $k => $a){
		    switch ($a -> id){
                case '1' :  $data['category'][$k]['href'] = 'about/industry';break;
                case '2' :  $data['category'][$k]['href'] = 'about/notice';break;
                case '3' :  $data['category'][$k]['href'] = 'about/analysis';break;
            }
        }
        $asset_code = isset($data['projects'][0]) ? $data['projects'][0]['asset_code'] : '';
        $data['code'] = $asset_code;
        $data['key']  = trim(file_get_contents("../rsa_1024_pub.pem"));
        $detect = new MobileDetect();
        if($detect->isMobile()){
	        return view('front.index', $data);
        }
		return view('front.index_old', $data);
	}

	function novice(){
	    return view("front.novice");
    }

	function exchange(){
		return view("front.exchange");
	}

    function knowUs(){
	    return view('front.introduct.knowUs');
    }

    function growth(){
        return view('front.introduct.growth');
    }

	function artists(){
		return view('front.introduct.artists');
	}

    function power(){
        return view('front.introduct.power');
    }

    function partner()
    {
	    return view("front.partner");
    }
}
