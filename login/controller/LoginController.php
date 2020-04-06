<?php
namespace app\login\controller;
use think\Controller;
use think\Request;              // 请求
use app\common\model\Admin;   // 教师模型


class LoginController extends Controller{
    
    // 用户登录表单
    public function index(){
        return $this->fetch();
    }

    // 处理用户提交的登录数据
    public function login(){
        
        $postData = Request::instance()->post();

        // 验证用户名是否存在
        $map = array('admin_username'  => $postData['admin_username']);
        $Admin = Admin::get($map);

        // $Teacher要么是一个对象，要么是null。
        if (!is_null($Admin) && $Admin->getData('admin_password') === MD5(MD5($postData['admin_password']))) {
            // 用户名密码正确，将teacherId存session，并跳转至教师管理界面
            session('admin_id', $Admin->getData('admin_id'));
            cookie('admin_level',$Admin->getData('admin_level'));
            cookie('admin',$Admin);
            return $this->success('登录成功');
        } else {
            // 用户名不存在，跳转到登录界面。
            return $this->error('用户名或密码有误');
        }
    }

    public function logOut()
    {
        if (Admin::logOut()) {
            return $this->success('注销成功');
        } else {
            return $this->error('注销失败');
        }
    }

}