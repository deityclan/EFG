<?php
namespace app\index\controller;
use app\common\model\Admin;
use think\Controller;
use think\Request;
use think\Db;

class BaseController extends Controller
{
	
	public function __construct(){
        // 调用父类构造函数(必须)
        parent::__construct();

        // 验证用户是否登陆
        if (!Admin::isLogin()) {
            return $this->error('请先登录', url('/login/login/index'));
        }
    }

    public function index(){
		return $this->fetch();
	}

	/*
    ** 输入：无
    ** 输出：文本结果
    ** 功能：对数据进行保存
    */
    public function save(){
        if(request()->isPost()){
        	$target = model('app\\'.request()->module().'\\model\\'.request()->controller());
	        $postData = input('post.');
	        $target->allowField(true)->data($postData);
	        if (!$target->validate(true)->save($target->getData())){
	            return $this->error('添加失败，原因：' . $target->getError());
	        }
	    
	        // 成功返回json
	        return $this->success('添加成功');
        }else{
			return $this->error('非法操作！');
		}	
    }

    /*
    ** 输入：无
    ** 输出：删除的结果 
    ** 功能：删除某条记录
    */
    public function delete(){
        if(request()->isPost()){
	        // 接收数据，取要更新的关键字信息
	        $target = model('app\\'.request()->module().'\\model\\'.request()->controller());
	        $tid = input('post.id');
	        $data = $target->find($tid);
	        // 要删除的对象存在
	        if (is_null($data)){
	            return $this->error('不存在id为' . $tid . '的数据，删除失败');
	        }

	        // 删除对象
	        if (!$data->delete()){
	            return $this->error('删除失败，原因：' . $data->getError());
	        }
	        
	        // 返回json
	        return $this->success('删除成功'); 
        }else{
			return $this->error('非法操作！');
		}	
    
    }

    /*
    ** 输入：无
    ** 输出：多条删除的条数
    ** 功能：多条删除
    */
    public function multidelete(){
    	if(request()->isPost()){
	    	$target = model('app\\'.request()->module().'\\model\\'.request()->controller());
	    	$tids = input('post.ids/a');
	    	foreach($tids as $tid){
	    		$data = $target->find($tid);
	    		// 删除对象
		        if (!$data->delete()){
		            return $this->error('删除失败，原因：' . $data->getError());
		        }
	    	}
	    	
	      	// 返回json
		    return $this->success('删除成功'); 
	    }else{
			return $this->error('非法操作！');
		}	
    }

    /*
    ** 输入：无
    ** 输出：全部数据
    ** 功能：提取表格的全部数据
    */
    public function selall(){
    	$mname = request()->controller();
    	$target = model('app\\'.request()->module().'\\model\\'.request()->controller());
    	
    	$datas=$target->all();

    	$this->assign('datas', $datas);

    	$tables = Db::table("relations")->where("table_1",$mname)->select();

    	foreach($tables as $table){
			$relation=model('app\\'.request()->module().'\\model\\'.$table["table_N"]);
			
			$this->assign($table["table_N"]."s", $relation->all());
			
		}


        // 取回打包后的数据
        return $this->fetch();
	    
    }

    /*
    ** 输入：无
    ** 输出：结果
    ** 功能：根据id删除数据，如果存在关联数据，提示无法删除
    */
    public function del(){
		if(request()->isPost()){
			
			$tid = input('post.id');
			$mname = request()->controller();
			$target = model('app\\'.request()->module().'\\model\\'.$mname);
			
			$id_name=strtolower($mname)."_id";
			$target = $target->where($id_name,$tid)->find();
			
			$tables = Db::table("relations")->where("table_N",$mname)->select();
			
			$count=0;
			foreach($tables as $table){
				$relation=model('app\\'.request()->module().'\\model\\'.$table["table_1"]);
				
				$count+=$relation->where($id_name,$tid)->count();
				
			}

			if($count>0){
				return json(['data'=>null,'code'=>0,'msg'=>'操作失败，请检查此项数据是否已经在其他表格中使用！']); 
			}else{
				$result = $target->delete();
				if($result){
					return json(['data'=>$result,'code'=>1,'msg'=>'操作成功，数据已删除!']); 
				}
				else{
					return json(['data'=>$result,'code'=>0,'msg'=>'操作失败!']); 
				}
			}
		}else{
			return json(['data'=>'','code'=>0,'msg'=>'非法操作!']);
		}
		
	}
}
