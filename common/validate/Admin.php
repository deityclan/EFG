<?php
namespace app\common\validate;
use think\Validate;     // 内置验证类

class Admin extends Validate{
	/*
	** 数据验证
	** admin_name：必须|长度2-30个字符|中文
	** admin_username：必须|长度4-20个字符|英文或数字|唯一
	** admin_password：必须|长度为4-20个字符|英文或数字
	*/
	protected $rule = [
        'admin_name'  => 'require|length:2,30|chs',
        'admin_username' => 'require|length:4,30|alphaNum|unique:admin',
        'admin_password' => 'require|length:4,50|alphaNum',
    ];

    protected $message  =   [
        'admin_name.require' => '管理员姓名不能为空',
        'admin_name.length'     => '姓名长度必须为2-30个字符',
        'admin_name.chs' => '姓名必须为中文',
        'admin_username.require' => '账号不能为空',
        'admin_username.length'     => '账号长度必须为4-30个字符',
        'admin_username.alphaNum' => '账号必须为英文或数字',
        'admin_username.unique' => '账号在系统中已存在',
        'admin_password.require' => '密码不能为空',
        'admin_password.length'     => '密码长度必须为4-30个字符',
        'admin_password.alphaNum' => '密码必须为英文或数字',
    ];
}