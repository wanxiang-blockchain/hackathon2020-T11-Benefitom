<?php
/**
 * Created by PhpStorm.
 * User: justshaw
 * Date: 2018-11-29
 * Time: 13:16
 */

namespace App\Model\Cms;


use Illuminate\Database\Eloquent\Model;

class Push extends Model
{
    protected $connection = 'cms';

    use \Illuminate\Database\Eloquent\SoftDeletes;
    protected $perPage = 20;

    const TYPE_BLOCK_TRANSFER_IN = 10;  // arttbc 转入
    const TYPE_NOTICE = 11;  // 运营通知

    protected $casts = [
        'type' => 'int'
    ];

    protected $dates = [
        'push_at'
    ];

    protected $fillable = [
        'type',
        'con_id',
        'con',
        'img',
        'push_at',
        'stat',
        'push_to',
        'title',
        'subtitle'
    ];


    public static function boot()
    {
        parent::boot();

        self::creating(function($model){
            if (empty($model->push_at)){
                $model->push_at = date('Y-m-d H:i:s');
            }
        });
    }


}