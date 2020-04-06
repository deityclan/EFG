<?php
namespace app\common\model;
use think\Model;    // 使用前进行声明

class Admin extends Model{

    protected $dateFormat = 'Y年m月d日 H时i分s秒';    // 日期格式


    public function setAdminPasswordAttr($value){
        return MD5($value);
    }

    /**
     ** 输入：无 
     ** 输出：boolean
     ** 功能：判断是否登录
     */
    static public function isLogin(){
        $id = session('admin_id');

        // isset()和is_null()是一对反义词
        if (isset($id)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     ** 输入：无
     ** 输出：boolean
     ** 功能：注销session
     */
    static public function logOut()
    {
        // 销毁session中数据
        session('admin_id', null);
        session("admin_level",null);
        cookie("admin",null);
        return true;
    }

}
