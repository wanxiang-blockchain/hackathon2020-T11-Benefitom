<?php
/**
 * Created by PhpStorm.
 * User: justshaw
 * Date: 2018-12-20
 * Time: 11:08
 */

namespace App\Model;

use App\Utils\ApiResUtil;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ListModel
 * @package App\Model
 */
class ListModel
{
    protected $query;
    protected $direct;
    protected $lastId;
    protected $limit;

    public function __construct(Builder $query)
    {
        $request = request();
        $this->lastId = $request->get('lastId', 0);
        $this->query = $query;
        $this->limit = ApiResUtil::PAGENATION;
        $op = '<';
        if ($this->lastId > 0) {
            $query->where('id', $op, $this->lastId);
        }
        $query->orderByDesc('id');
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function getLastId()
    {
        return $this->lastId;
    }

    public function fetchModels(Array $select)
    {
        return $this->query->select($select)->limit($this->limit)->get();
    }

}