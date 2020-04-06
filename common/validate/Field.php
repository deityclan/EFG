<?php
namespace app\common\validate;
use think\Validate;     // 内置验证类

class Field extends Validate{
	/*
	** 数据验证
	** field_name：必须|长度1-30个字符|中文、英文或数字|唯一
	*/
	protected $rule = [
        'field_name'  => 'require|length:1,30|chsAlphaNum|unique:field',
    ];

    protected $message  =   [
        'field_name.require' => '行业的名称不能为空',
        'field_name.length'     => '名称长度必须为1-30个字符',
        'field_name.chsAlphaNum' => '名称必须为中文、英文或数字',
        'field_name.unique' => '该行业在系统中已存在',
    ];

    
}