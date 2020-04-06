<?php
namespace app\common\validate;
use think\Validate;     // 内置验证类

class User extends Validate{
	/*
	** 数据验证
	** user_name：必须|长度2-30个字符|中文或英文
	** user_phone：必须|长度11个字符|数字
	** user_email：必须|email格式
	** user_itemname：必须|长度为1-50个字符|中文、英文或数字|唯一
	** 其他项均为表单提交，验证应放在前端进行
	*/
	protected $rule = [
        'user_name'  => 'require|length:2,30|chsAlpha',
        'user_phone' => 'require|length:11|number',
        'user_email' => 'require|email',
        'user_itemname' => 'require|length:1,50|chsAlphaNum|unique:user',
        'user_wechatID' => 'require|unique:user'
    ];

    protected $message  =   [
        'user_name.require' => '姓名不能为空',
        'user_name.length'     => '姓名长度为2-30个字符',
        'user_name.chsAlpha' => '姓名只能是中文或英文',
        'user_phone.require' => '手机号不能为空',
        'user_phone.length' => '手机号只能是11位',
        "user_phone.number" => '手机号只能是数字',
        'user_email.require' => '电子邮箱不能为空',
        'user_email.email' => '电子邮箱格式错误',
        'user_itemname.require' => '项目名称不能为空',
        'user_itemname.length' => '项目名称长度为1-50个字符',
        'user_itemname.chsAlphaNum' => '项目名称只能是中文、英文或数字',
        'user_itemname.unique' => '该项目名称在系统中已存在，不允许重复提交',
        "user_wechatID.unique" => '该微信号已提交过项目，不能重复提交'
    ];
}