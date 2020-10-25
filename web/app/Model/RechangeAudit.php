<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class RechangeAudit extends Model
{
    //
    protected $fillable = [
        'member_id', 'type', 'asset_type', 'content', 'balance', 'amount', 'status', 'audit_id', 'audit_reason'
    ];
}
