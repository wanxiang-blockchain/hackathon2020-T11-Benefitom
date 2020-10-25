<?php
namespace App\Exceptions;
use Exception;
class NotEnough extends Exception
{
    protected $id;
    protected $details;
     
    public function __construct($message)
    {
        parent::__construct($message);
    }
 
    protected function create(array $args)
    {
        $this->id = array_shift($args);
        $error = $this->errors($this->id);
        $this->details = vsprintf($error['context'], $args);
        return $this->details;
    }
 
    private function errors($id)
    {
        $data= [
            'asset_not_enough' => [
                'context'  => '资产数量不够',
            ]
            //   ...
        ];
        return $data[$id];
    }
}