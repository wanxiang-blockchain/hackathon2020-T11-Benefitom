<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 17/10/27
 * Time: 上午11:41
 */

namespace App\Model\Tender;


use Illuminate\Database\Eloquent\Model;

class TenderSignup extends Model
{
	protected $table = 'tender_signup';
	protected $fillable = [
		'member_id', 'day', 'addup'
	];
}