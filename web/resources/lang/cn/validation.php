<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => ':attribute必须可接收.',
    'active_url'           => ':attribute不是一个有效的url.',
    'after'                => ':attribute必须大于:date的日期.',
    'after_or_equal'       => ':attribute必须大于等于:date.',
    'alpha'                => ':attribute只能包含字母.',
    'alpha_dash'           => ':attribute只能包含字母，数字和破折号.',
    'alpha_num'            => ':attribute只能包含字母和数字.',
    'array'                => ':attribute必须是数组.',
    'before'               => ':attribute大于:date的日期.',
    'before_or_equal'      => ':attribute必须小于等于:date的日期.',
    'between'              => [
        'numeric' => ':attribute必须是在:min和:max.',
        'file'    => ':attribute必须是在:min和:max千字节.',
        'string'  => ':attribute长度必须是在:min到:max位之间.',
        'array'   => ':attribute必须是在:min和:max之间的项.',
    ],
    'boolean'              => ':attribute该值必须是真或者假.',
    'confirmed'            => '*您两次输入的:attribute不一致.',
    'date'                 => ':attribute格式无效.',
    'date_format'          => ':attribute和:format.这个格式不匹配',
    'different'            => ':attribute和:other重复.',
    'digits'               => ':attribute必须是:digits位.',
    'digits_between'       => '*:attribute长度须在:min至:max个字符之间.',
    'dimensions'           => ':attribute具有无效的图片尺寸.',
    'distinct'             => ':attribute字段具有重复值.',
    'email'                => ':attribute必须是一个有效的电子邮箱.',
    'exists'               => '选中的:attribute无效.',
    'file'                 => ':attribute必须是个文件.',
    'filled'               => ':attribute字段是required.',
    'image'                => ':attribute必须是一个图片.',
    'in'                   => ' 选中的:attribute是无效.',
    'in_array'             => ':attribute字段不存在与:other.',
    'integer'              => ':attribute必须是一个整型.',
    'ip'                   => ':attribute必须是一个有效的ip地址.',
    'json'                 => ':attribute必须是一个有效的json格式.',
    'max'                  => [
        'numeric' => ':attribute不大于:max.',
        'file'    => ':attribute不大于:max千字节.',
        'string'  => ':attribute不大于:max字符.',
        'array'   => ':attribute不会超过:max项.',
    ],
    'mimes'                => ':attribute必须是一个文件并且类型是:values.',
    'mimetypes'            => ':attribute必须是一个文件并且类型是:values.',
    'min'                  => [
        'numeric' => ':attribute必须至少:min.',
        'file'    => ':attribute必须至少:min千字节.',
        'string'  => ':attribute必须至少:min字符.',
        'array'   => ':attribute必须至少:min 项.',
    ],
    'not_in'               => ':attribute是无效的',
    'numeric'              => ':attribute必须是数字.',
    'present'              => ':attribute字段必须是当前的值.',
    'regex'                => ':attribute格式错误.',
    'required'             => ':attribute不能为空',
    'required_if'          => 'The :attribute field is required when :other is :value.',
    'required_unless'      => 'The :attribute field is required unless :other is in :values.',
    'required_with'        => 'The :attribute field is required when :values is present.',
    'required_with_all'    => 'The :attribute field is required when :values is present.',
    'required_without'     => 'The :attribute field is required when :values is not present.',
    'required_without_all' => ':attribute field is required when none of :values are present.',
    'same'                 => ':attribute和:other必须匹配.',
    'size'                 => [
        'numeric' => ':attribute必须是:size.',
        'file'    => ':attribute必须是:size千字节.',
        'string'  => ':attribute必须是:size字符.',
        'array'   => ':attribute必须是:size项.',
    ],
    'string'               => ':attribute必须是字符串',
    'timezone'             => ':attribute必须是一个有效的时间戳',
    'unique'               => ':attribute该值已经存在',
    'uploaded'             => ':attribute上传失败',
    'url'                  => ':attribute不是一个有效的格式',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [

    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [
        'name'=>'名称',
        'picture'=>'图片',
        'desc'=>'描述',
        'start'=>'开始时间',
        'end'=>'结束时间',
        'price'=>'价格',
        'total'=>'数量',
        'is_show'=>'是否开启',
        'nickname'=>'昵称',
        'phone'=>'手机号',
        'password'=>'密码',
        'code' => '身份证',
        'trade_pwd' => '交易密码',
        'asset_code'=>'资源类型',
        'mobile'=>'手机号',
        'limit' => '可卖数',
        'captcha'=>'验证码',
        'changePhoneTwoPhone'=>'手机号',
        'resetTradePwPhone'=>'手机号',
        'resetTradePwNewPassword'=>'交易密码',
        'limit'=>'可卖数量',
        'content' =>'详情',
        'tradePassword'=>'交易密码',
        'againTradePassword'=>'重复交易密码'
    ],

];
