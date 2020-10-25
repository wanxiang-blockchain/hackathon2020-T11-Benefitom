<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ProjectOrder extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'member_id', 'order_id', 'status', 'user_id', 'project_id', 'project_name', 'quantity', 'price','pay_type'
    ];
    public function member() {
        return $this->belongsTo("App\Model\Member", "member_id", "id");
    }

}
