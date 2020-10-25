<?php
namespace App\Repository;

use App\Model\AssetType;
use App\Model\Project;
use App\Service\TradeSetService;

class ProjectRepository{

    public function projects() {
        return Project::paginate(2);
    }

    public function create($data) {
        $price = $data['price'];
        AssetType::updateValue($data, $price);
        return Project::create($data);
    }

    public function modify($id, $data) {
        $price = $data['price'];
	    // 如果code不存在 如果未开启交易，修改market_value
	    if (!TradeSetService::isStartTrade( $data['asset_code'])) {
		    AssetType::updateValue($data, $price);
	    }
        $project = Project::where("id", $id)
         ->update($data);
        return $project;
    }

    public function delete($id) {
        return Project::destroy($id);
    }

    public function getProject($id){
        return Project::find($id);
    }

}