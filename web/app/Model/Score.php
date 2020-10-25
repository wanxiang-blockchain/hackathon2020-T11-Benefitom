<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
	protected $fillable = [
		'score', 'account_id'
	];
}
