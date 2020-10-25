<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\TradeException;
use App\Model\Artbc\WalletBtShop;
use App\Utils\OssUtil;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class WalletBtShopController extends Controller
{


    private $request;

    public function __construct(Request $request){
        $this->request = $request;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $models = WalletBtShop::where(function($query){
            $phone = request()->get('phone');
            if($phone) {
                $query->where('phone', $phone);
            }
        })->orderBy('created_at','desc')->paginate(10);
        $models->appends(Request()->all());
        return view('admin.btshop.index', compact('models'));
    }

    public function create()
    {
        if ($this->request->isMethod('get')) {
            return view('admin.btshop.create');
        }else{
            try{
                $this->validate($this->request, [
                    'name'=>'required|numeric',
                    'price'=>'required|numeric',
                    'score'=>'required|numeric',
                    'per_limit'=>'required|numeric',
                    'file' => 'required|image'
                ]);

                $data = $this->request->all();
                $file = $_FILES['file']['tmp_name'];
                if (!$file) {
                    throw new \Exception('上传文件失败');
                }
                $path = OssUtil::imgPath() . time() . $_FILES['file']['name'];
                if (!\Storage::put($path, file_get_contents($file))){
                    throw new TradeException('文件存储失败');
                }
            }catch (\Exception $e) {
                Log::error($e->getTraceAsString());
                return view('admin.btshop.create');
            }
        }
    }

}
