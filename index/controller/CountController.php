<?php
namespace app\index\controller;
use app\common\model\Admin;
use app\common\model\User;
use app\common\model\Review;
use app\common\model\University;
use app\common\model\Field;
use think\Request;
use think\Db;

class CountController extends BaseController{
    
    /*
    ** 输入：无
	** 输出：view模板
	** 功能：统计所有未审核的项目
	*/
    public function uncheckItem(){

    	$count=Db::table("user")->where("user_itemstatus",0)->count();
        return $count;
    
    }

}
