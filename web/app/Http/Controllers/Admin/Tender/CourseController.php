<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 17/12/5
 * Time: 下午4:26
 */

namespace App\Http\Controllers\Admin\Tender;


use App\Http\Controllers\Controller;
use App\Model\Tender\Tender;
use App\Model\Tender\TenderCourse;
use App\Service\ValidatorService;
use App\Utils\ResUtil;
use Illuminate\Http\Request;

class CourseController extends Controller
{
	public $request;
	public function __construct(Request $request)
	{
		$this->request = $request;
	}

	public function index()
	{
		$query = new TenderCourse();
		$models = $query->where(function($query) {
			$name = $this->request->get('name');
			$stat = $this->request->get('stat');
			if($name) {
				$query->where('name', 'like', "%{$name}%");
			}
			if($stat) {
				$query->where('stat', $stat);
			}
		})->orderByDesc('id')->paginate(10);
		$models->appends($this->request->all());
		return view('admin.tender.course.index', compact('models'));
	}

	public function create(ValidatorService $validatorService)
	{
		if($this->request->isMethod('get')){
			return view('admin.tender.course.create');
		}else{
			$data = $this->request->all();
			$ret = $validatorService->checkValidator([
				'name' => 'required',
				'summary' => 'required',
				'info' => 'required',
				'poster' => 'required|url',
				'video' => 'required|url',
				'stat' => 'required|in:0,1'
			], $data);
			if($ret['code'] != 200){
				return $ret;
			}
			if(isset($data['id'])){
				$model = TenderCourse::find($data['id']);
				if(empty($model)){
					return redirect('/admin/tender/courses');
				}
				$model->update($data);
			}else{
				$model = new TenderCourse();
				$model->fill($data);
				$model->save();
			}
			return redirect('/admin/tender/courses');
		}
	}

	public function edit($id)
	{
		$model = TenderCourse::find($id);
		if(empty($model)){
			return view('errors.404');
		}
		return view('admin.tender.course.edit', compact('model'));
	}

	public function del($id)
	{
		TenderCourse::where('id', $id)->delete();
		return ResUtil::ok();
	}
}