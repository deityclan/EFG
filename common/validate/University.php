<?php
namespace app\common\validate;
use think\Validate;     // 内置验证类

class University extends Validate{
	/*
	** 数据验证:
	** university_name：必须|长度4-30个字符|中文|唯一
	*/
	protected $rule = [
        'university_name'  => 'require|length:4,30|chs|unique:university',
    ];

    protected $message  =   [
        'university_name.require' => '大学的名称不能为空',
        'university_name.length'     => '名称长度必须为4-30个字符',
        'university_name.chs' => '名称必须为中文',
        'university_name.unique' => '该大学在系统中已存在',
    ];
}