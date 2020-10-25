<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TradeOrder extends Model
{
    protected $fillable = [
        'type', 'asset_type', 'member_id', 'quantity','amount','price','status'
    ];
    public function assetTypes() {
        return $this->belongsTo("App\Model\AssetType", "asset_type", "code");
    }
    public function member() {
        return $this->belongsTo("App\Model\Member", "member_id", "id");
    }

    public function assetType()
    {
    	return $this->hasOne(AssetType::class, 'code', 'asset_type');
    }
}
