<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 17/12/5
 * Time: 下午4:35
 */

namespace App\Model\Tender;


use Illuminate\Database\Eloquent\Model;

class TenderCourse extends Model
{
	protected $table = 'tender_course';

	protected $fillable = [
		'name', 'summary', 'video', 'poster', 'info', 'stat'
	];

	public function statLabel()
	{
		return $this->stat == 1 ? '发布' : '未发布';
	}

}