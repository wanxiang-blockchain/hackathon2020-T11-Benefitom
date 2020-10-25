<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\TradeException;
use App\Model\Btshop\BtshopProduct;
use App\Model\Link;
use App\Utils\OssUtil;
use App\Utils\ResUtil;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class BtshopProductController extends Controller
{
    //
    public function index(Request $request)
    {
        $models = BtshopProduct::where(function($query)use($request){
            if($request->get('name')) {
                $query->where('title', 'like', '%'.$request->get('name').'%');
            }
        })->orderBy('enable','desc')->orderByDesc('created_at')
        ->limit(20)->get()->toArray();
        return view('admin.btshop.product.index', ['models'=>$models]);
    }

    public function create(Request $request, Validator $validator)
    {
        if ($request->isMethod('GET')) {
            return view('admin.btshop.product.create');
        }else{
            $this->validate($request, [
                'name'=>'required',
                'score'=>'required',
                'img' => 'required'
            ]);
            $data = $request->all();
            $file = $_FILES['image'];
//            $data['img'] = OssUtil::imgPath() . $file['name'];
//            Storage::disk('oss');
//            Storage::put('path/to/file/file.jpg', file_get_contents($file['tmp_name']));
            if ($data['paytype'] == BtshopProduct::PAYTYPE_BT && $data['bt_price'] <= 0){
                return redirect('/admin/btshop/product/create')->withErrors(['bt_price' => 'ARTTBC价格不可为空']);
            }
            if ($data['paytype'] == BtshopProduct::PAYTYPE_ARTBC && $data['price'] <= 0){
                return redirect('/admin/btshop/product/create')->withErrors(['price' => 'ARTBC价格不可为空']);
            }
            if ($data['paytype'] == BtshopProduct::PAYTYPE_RMN && $data['rmb_price'] <= 0){
                return redirect('/admin/btshop/product/create')->withErrors(['rmb_price' => 'RMB价格不可为空']);
            }
            if ($data['paytype'] == BtshopProduct::PAYTYPE_ARTBCS && ($data['artbcs_price'] <= 0 || $data['rmb_price'] <= 0)){
                return redirect('/admin/btshop/product/create')->withErrors(['artbcs_price' => 'ARTBCS价格和RMB价格不可为空']);
            }
            !isset($data['price']) && $data['price'] = 0;
            !isset($data['bt_price']) && $data['bt_price'] = 0;
            !isset($data['rmb_price']) && $data['rmb_price'] = 0;
            !isset($data['artbcs_price']) && $data['artbcs_price'] = 0;
            BtshopProduct::create($data);

            return redirect('/admin/btshop/products');
        }
    }

    public function enable(Request $request)
    {
        $id = $request->get('id');
        $enable = $request->get('enable', '0');
        $model = BtshopProduct::find($id);
        if (!$model) {
            return ResUtil::error(201, '数据不存在');
        }
        $model->enable = $enable;
        $model->save();
        return ResUtil::ok();
    }

}
