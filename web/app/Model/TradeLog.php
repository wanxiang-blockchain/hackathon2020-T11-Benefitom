<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TradeLog extends Model
{
    protected $fillable = [
        'type', 'asset_type', 'buyer_id', 'seller_id','amount','price','total'
    ];
    public function assetTypes() {
        return $this->belongsTo("App\Model\AssetType", "asset_type", "code");
    }
    public function member() {
        return $this->belongsTo("App\Model\Member", "buyer_id", "id");
    }
    public function sell() {
        return $this->belongsTo("App\Model\Member", "seller_id", "id");
    }
}
