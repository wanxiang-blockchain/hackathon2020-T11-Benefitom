<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class FinanceType extends Model
{
    protected $table = 'finance_types';
    protected $fillable = [
        'code', 'name'
    ];
}
