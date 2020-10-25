<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 17/10/26
 * Time: 上午10:00
 */

namespace App\Model\Tender;


use Illuminate\Database\Eloquent\Model;

class TenderBroadcast extends Model
{
	protected $table = 'tender_broadcast';
	protected $fillable = ['content', 'type'];
}