<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class RechargeAudit extends Model
{
    //
    protected $fillable = [
        'member_id', 'unlock_time', 'asset_type', 'content', 'balance', 'amount', 'status', 'audit_id', 'audit_reason'
    ];
}
