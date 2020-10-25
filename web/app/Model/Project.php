<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'asset_code', 'name', 'start', 'end', 'picture', 'desc', 'price', 'total', 'limit', 'is_show', 'postion',
	    'agent', 'rule', 'rule_desc', 'price_unit', 'age', 'per_limit', 'tender_prize', 'init_sold',
	    'artbc_prize'
    ];

    function articles() {
        return $this->morphMany('App\Model\Article', 'articlable');
    }

    function getOrderNumberAttribute() {
        $id = $this->id;
        $projects = \DB::table('project_orders')
            ->select("member_id")
            ->groupBy('member_id')
            ->where('project_id', $id)
            ->get();
        return count($projects);
    }

    function getOrderCountAttribute() {
        $id = $this->id;
        $count = \DB::table('project_orders')
            ->select("id")
            ->where('project_id', $id)
            ->count();
        if($this->init_sold > 0){
        	$count += ceil($this->init_sold / $this->per_limit);
        }

        return $count;
    }

    public function sold() {
    	return $this->position + $this->init_sold;
    }

    function getProgressAttribute() {
        if ($this->limit == 0) return 100;
        return 100 * number_format($this->sold() / $this->limit,4);
    }

    public function assetType() {
	    return $this->hasOne(AssetType::class, 'code', 'asset_code');

    }

    public function hasSellOut() {
    	return $this->limit <= $this->sold();
    }

    public function artbcPrice() {
    	return round($this->price / Artbc::giftRate(), 5);
    }


}
