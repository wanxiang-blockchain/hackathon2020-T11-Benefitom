<?php
namespace App\Service;

use Illuminate\Support\Facades\Storage;
use App\Repository\ProjectRepository;
use DB;
use App\Model\Asset;
use App\Model\Account;

class ProjectService {
    function __construct(ProjectRepository $projectRepository) {
        $this->projectRepository = $projectRepository;
    }

    public function projects() {
        return $this->projectRepository->projects();
    }

    public function create($file, $data) {
        if ($file) {
             $path = $file->store("public/project", 'public');
             move_uploaded_file($_FILES['picture']['tmp_name'], storage_path() . '/' . $path);
             $data["picture"] = $path;
        }
        return $this->projectRepository
             ->create($data);
    }

    public function getProject($id){
        return $this->projectRepository
            ->getProject($id);
    }

    public function delete($id){
       $project = $this->projectRepository->getProject($id);
       Storage::disk("public")
           ->delete($project->picture);
       $project->delete();
    }

    public function edit($id,$file,$data){
        if ($file) {
            $path = $file->store("public/project", 'public');
            $data["picture"] = $path;
            move_uploaded_file($_FILES['picture']['tmp_name'], storage_path() . '/' . $path);
            $project = $this->projectRepository->getProject($id);
            //Storage::disk("public")
             //   ->delete($project->picture);
        }
        /* if(!isset($data['url']) && !$file) {
            $data['picture'] = '';
        }*/
        return  $project = $this->projectRepository
                ->modify($id,$data);
    }
}
