<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 17/11/2
 * Time: 下午5:26
 */

namespace App\Model\Tender;


use App\Model\HasMemberTrait;
use Illuminate\Database\Eloquent\Model;

class TenderFeedback extends Model
{
	use HasMemberTrait;
	protected $table = 'tender_feedback';
	protected $fillable = ['member_id', 'con'];
}