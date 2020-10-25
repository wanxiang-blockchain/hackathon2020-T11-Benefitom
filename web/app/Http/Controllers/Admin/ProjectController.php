<?php

namespace App\Http\Controllers\Admin;

use App\Model\AssetType;
use App\Model\Account;
use App\Model\Project;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Service\ProjectService;

class ProjectController extends Controller
{
    function __construct(ProjectService $projectService) {
        $this->projectService = $projectService;
    }

    function index(Request $request) {
        $Project = new Project();
        $projects = $Project->where(function($query)use($request){
            $name = request()->get('name');
            $is_show = request()->get('is_show');
            if($name) {
                $query->where('name', 'like', '%'.$name.'%');
            }
            if(in_array($is_show, [1,2])) {
                $show = $is_show == 1 ? 1 : 0;
                $query->where('is_show', '=', $show);
            }
        })->paginate(10);
        $projects->appends($request->all());
        return view("admin.project.index", [
            "projects" => $projects
        ]);
    }

    function create(Request $request) {
        $request->flash();
        $this->validate($request, [
            'name'    => 'required',
            'txid'    => 'required',
            'contractAddress'  => 'required',
            'asset_code'    => 'required|unique:projects',
            'picture' => 'required|image',
            'agent' => 'required',
            'rule' => 'required',
            'start' => 'required',
            'end' => 'required|after:'.$request->get('start'),
            'price'=>['required'],
            'total'=>'required|numeric|min:1',
            'limit'=>'required|numeric|min:1|max:'.$request->get('total'),
	        'artbc_prize'=>'numeric|min:0',
        ]);
        $this->projectService->create($request->file('picture'), $request->all());
        return redirect('admin/project?nav=2|1');
    }

    function getCreate() {
        $asset_types = AssetType::where('code', '!=', Account::BALANCE)
            ->get()->toArray();
        return view("admin.project.create", ['asset'=>$asset_types]);
    }

    function  delete (Request $request){
        $this->validate($request, [
            'id'    => 'required',
        ]);
        $this->projectService->delete($request->input('id'));
        return ['code'=>200, 'msg'=>'success'];
    }

    function getEdit(Request $request) {
        $project = $this->projectService->getProject($request->input("id"));
        return view("admin.project.edit", [
            "project" => $project,
            'asset'=>AssetType::all()->toArray()
        ]);
    }

    function postEdit(Request $request) {
        $id   = $request->input('id');
        $file = $request->file('picture');
        $data = $request->all();
        unset($data['id']);
        unset($data['_token']);
	    unset($data['_url']);
        $this->validate($request, [
            'name'    => 'required',
            'asset_code'    => 'required',
            'start' => 'required',
            'end' => 'required|after:'.$request->get('start'),
            'price'=>['required'],
            'total'=>'required|numeric|min:1',
            'limit'=>'required|numeric|min:1|max:'.$request->get('total'),
            'artbc_prize'=>'required|numeric|min:0',
        ]);
        $this->projectService->edit($id,$file,$data);
        return redirect('admin/project');
    }

    public function change(Request $request)
    {
        $project = Project::find($request->get('id'));
        if(!$project) {
            return ['code' => 400, 'message'=>'fail'];
        }
        $project->is_show = $project->is_show == 1 ? 0 : 1;
        $project->save();
        return ['code' => 200, 'message' => 'success'];
    }
}
