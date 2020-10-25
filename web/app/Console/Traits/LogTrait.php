<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 17/8/3
 * Time: 上午11:16
 */

namespace App\Console\Traits;


Trait LogTrait
{

	protected $_startTime;

	protected function logstart()
	{
		$this->_startTime = time();
	}

	/**
	 * log日志
	 *
	 * @param $msg string 日志内容
	 */
	protected function logmsg($msg)
	{
		echo date('Y-m-d H:i:s')
			. ' pid[' . posix_getpid() . ']'
			. ' cost[' . (time() - $this->_startTime) . ']'
			. ' memory[' . memory_get_usage()/1024/1024 . 'M]'
			. ' message[' . $msg . ']' . PHP_EOL;
		return 'exit';
	}

}