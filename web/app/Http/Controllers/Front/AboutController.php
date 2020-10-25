<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Model\Article;
use App\Model\Category;
use App\Model\Reuqest;

class AboutController extends Controller
{
	public function index()
	{
        return view('front.about.company');
    }

    public function industry(){
        $id = Request()->input('id');
        if($id){
            $detail = $this->detail($id);
            return view('front.about.noticeDetails',['data'=>$detail]);

        }else{
            $data = $this->article(1);
            return view('front.about.industry', ['data'=>$data]);
        }
    }

    public function notice(){
        $id = Request()->input('id');
        if($id){
            $detail = $this->detail($id);
            return view('front.about.noticeDetails',['data'=>$detail]);

        }else{
            $data = $this->article(2);
            return view('front.about.noticeList', ['data'=>$data]);
        }
    }

	public function analysis(){
		$id = Request()->input('id');
		if($id){
			$detail = $this->detail($id);
			return view('front.about.analysisDetails',['data'=>$detail]);

		}else{
			$data = $this->article(3);
			return view('front.about.analysis', ['data'=>$data]);
		}
	}

    public function contactUs(){
        return view('front.about.contactUs');
    }

    public function joinUs(){
        return view('front.about.joinUs');
    }

    public function articleDetail($id)
    {
        $data = Article::where(['id'=>$id])->first();
        return view('front.about.noticeDetails',['data'=>$data]);
    }

    /**
     * @param $aid
     */
    public function article($aid){
        $ret_data = Article::where(['articlable_id'=>$aid,'articlable_type'=>'App\Model\Category','is_show' => '1'])
            ->orderBy('updated_at','desc')
            ->paginate();
        $data = Article::where(['articlable_id'=>$aid,'articlable_type'=>'App\Model\Category','is_show' => '1'])
            ->orderBy('updated_at','desc')->get();
        $this->cache($data);
        return $ret_data;
    }

    public function cache($data) {
        $data = $data->toArray();
        for($i = 0; $i < count($data); $i++) {
            $id = $data[$i]['id'];
            if ($i + 1 < count($data)) {
                \Cache::forever("{$id}_next", $data[$i+1]['id']);
                \Cache::forever("{$id}_nt", $data[$i+1]['title']);
            }
            if ($i - 1 >= 0) {
                \Cache::forever("{$id}_pre", $data[$i-1]['id']);
                \Cache::forever("{$id}_pt", $data[$i-1]['title']);
            }
        }
    }

    public function detail($id){
        $detail = Article::where(['id'=>$id])->first();
        return $detail;
    }

    public function helpCenter()
    {
        return view('front.about.helpCenter');
    }

    public function articlePreview($id){
        $content = Article::where('id',$id)->first();
        return view('front.about.articlePreview',compact('content'));
    }

}
