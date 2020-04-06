<?php
namespace app\index\controller;
use app\common\model\Notice;
use think\Request;

class NoticeController extends BaseController{
    
    

    /*
    ** 输入：无
    ** 输出：某条数据
    ** 功能：返回需要编辑的原始数据
    */
    public function edit(){
        
        $id = Request::instance()->param('id/d');

        // 根据id获取数据
        if (false === $Field = Field::get($id)){
            return $this->error('系统未找到ID为' . $id . '的记录');
        }

        //成功则返回获取到的数据
        return $this->success('读取数据成功',null,$Field);
    
    }

    /*
    ** 输入：无
    ** 输出：更新的结果
    ** 功能：完成数据项的更新
    */
    public function update(){
        
        //调试用
        //var_dump($_POST);
        //return;

        // 接收数据，取要更新的关键字信息
        $id = Request::instance()->post('id/d');
        
        // 获取当前对象
        $Field = Field::get($id);

        // 写入要更新的数据
        $Field->field_name = input('post.field_name');

        if (!is_null($Field)){
            if (!$Field->validate(true)->save($Field->getData())){
                return $this->error('更新失败' . $Field->getError());
            }
        }else{
            return $this->error('当前操作的记录不存在');
        }
    
        // 成功返回json
        return $this->success('更新成功');
    
    }

   
    
}
