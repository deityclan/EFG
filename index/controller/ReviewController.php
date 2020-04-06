<?php
namespace app\index\controller;
use app\common\model\User;
use app\common\model\Admin;
use app\common\model\Review;
use think\Request;
use think\Db;

class ReviewController extends BaseController{

    /*
    ** 输入：无
    ** 输出：view模板
    ** 功能：列出项目相关的数据
    */
    public function listItemData(){

        $Users = User::all();

        $this->assign('Users', $Users);

        $Fields=Db::table('field')->orderRaw('CONVERT(field_name USING gbk)')->select();
        $this->assign("Fields",$Fields);

        return $this->fetch();
    
    }

    /*
    ** 输入：无
    ** 输出：view模板
    ** 功能：列出每个项目对应的审核数据
    */
    public function listItemReviewData(){

        $id=input('post.user_id');

        $User=User::get($id);
        $Reviews=Db::table("review")->alias("r")->where("user_id",$id)->join("admin a","a.admin_id=r.admin_id")->select();

        foreach($Reviews as &$Review){
        	if($Review["review_stage"]==0){
        		$Review["review_stage"]="初审";
        	}else if($Review["review_stage"]==1){
        		$Review["review_stage"]="test";
        	}
        	if($Review["review_result"]==0){
        		$Review["review_result"]="未通过";
        	}else if($Review["review_result"]==1){
        		$Review["review_result"]="通过";
        	}

        	$Review["create_time"]=date('Y-m-d',$Review["create_time"]);
        }

        if($User&&$Reviews){
        	$data=["user"=>$User,"reviews"=>$Reviews,"status"=>1];
            return $data;
        }else{
        	$data=["status"=>0];
        	return $data;
        }
        
    }
    
    /*
    ** 输入：无
	** 输出：view模板
	** 功能：列出表格的所有数据，并输出到模板页面
	*/
    public function listAllData(){

    	$Reviews = Review::all();

        foreach($Reviews as &$Review){
            $proposal=$Review->review_proposal;
            $Review->review_proposal=str_replace("<br/>", "", $proposal);
            $Review->review_proposal=str_replace("", "\s", $Review->review_proposal);
        }

        $this->assign('Reviews', $Reviews);

        //$Universitys=University::all();
        //使用orderRaw是为了将utf8字符集的数据转换成gbk进行排序，否则中文排序不正常
        $Users=Db::table('user')->orderRaw('CONVERT(user_itemname USING gbk)')->select();
        $this->assign('Users',$Users);

        $Admins=Db::table('admin')->orderRaw('CONVERT(admin_name USING gbk)')->select();
        $this->assign("Admins",$Admins);

        return $this->fetch();
    
    }

    /*
    ** 输入：无
    ** 输出：文本结果
    ** 功能：对数据进行保存
    */
    public function save(){
        
        if(input('post.review_stage')===NULL||input('post.review_result')===NULL){
            
            return $this->error('操作失败，原因：必选项不能为空！');
        }
        //先验证，同一个项目，不允许重复进行同一个阶段的审核（例如针对项目1只能进行一次初审）
        $map["user_id"]=input('post.user_id');
        $map["review_stage"]=input('post.review_stage');

        $Validate=Review::get($map);

        if($Validate){
            return $this->error('操作失败，原因：对同一个项目不允许重复审核！');
        }
        


        $User=User::get(input('post.user_id'));

        if(!$User){
            return $this->error('操作失败，原因：' . $Review->getError());
        }else{
            $User->user_itemstatus=1;
            if(input('post.review_result')==0){
                $User->user_itemstatus=2;
            }
            $User->save($User->getData());
            
        }
        
        // 实例化
        $Review = new Review;

        // 写入要更新的数据
        $Review->admin_id = input('post.admin_id');
        $Review->user_id = input('post.user_id');
        $Review->review_time = date("Y-m-d");
        $Review->review_stage = input('post.review_stage');
        $Review->review_result = input('post.review_result');
        $Review->review_proposal = input('post.review_proposal');

        // 新增数据并进行validate验证
        // 失败返回json
        if (!$Review->validate(true)->save($Review->getData())){
            return $this->error('添加失败，原因：' . $Review->getError());
        }
    
        // 成功返回json
        return $this->success('添加成功');
    
    }

    /*
    ** 输入：无
    ** 输出：某条数据
    ** 功能：返回需要编辑的原始数据
    */
    public function edit(){
        
        $id = Request::instance()->param('id/d');

        // 根据id获取数据
        if (false === $Review = Review::get($id)){
            return $this->error('系统未找到ID为' . $id . '的记录');
        }

        //成功则返回获取到的数据
        return $this->success('读取数据成功',null,$Review);
    
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
        $Review = Review::get($id);

        // 写入要更新的数据
        $Review->admin_id = input('post.admin_id');
        $Review->user_id = input('post.user_id');
        $Review->review_time = input('post.review_date');
        $Review->review_stage = input('post.review_stage');
        $Review->review_result = input('post.review_result');
        $Review->review_proposal = input('post.review_proposal');

        if (!is_null($Review)){
            if (!$Review->validate(true)->save($Review->getData())){
                return $this->error('更新失败' . $Review->getError());
            }
        }else{
            return $this->error('当前操作的记录不存在');
        }
    
        // 成功返回json
        return $this->success('更新成功');
    
    }

    /*
    ** 输入：无
    ** 输出：删除的结果 
    ** 功能：删除某条记录
    */
    public function delete(){
        
        // 接收数据，取要更新的关键字信息
        $id = Request::instance()->param('id/d');
        
        // 判断是否成功接收
        if (0 === $id){
            throw new \Exception('未获取到ID信息', 1);
        }

        // 获取要删除的对象
        $Review = Review::get($id);

        // 要删除的对象存在
        if (is_null($Review)){
            return $this->error('不存在id为' . $id . '的数据，删除失败');
        }

        //要将项目的状态清零
        $User = User::get($Review->user_id);

        $User->user_itemstatus=0;

        $User->save($User->getData());

        // 删除对象
        if (!$Review->delete()){
            return $this->error('删除失败，原因：' . $Review->getError());
        }
        
        // 返回json
        return $this->success('删除成功'); 
    
    }

    /*
    ** 输入：无
    ** 输出：多条删除的条数
    ** 功能：多条删除
    */
    public function multidelete(){
        // 实例化请求类
        $Request = Request::instance();
        
        // 获取get数据
        $id = Request::instance()->param('id/a');

        return $id;
    }
}
