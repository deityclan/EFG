<?php
namespace app\index\controller;     //命名空间，也说明了文件所在的文件夹
use think\Controller;
use think\Session;
use app\common\model\User;      // 引入教师
use app\common\model\Admin;
use app\common\model\Review;

// Index既是类名，也是文件名，说明这个文件的名字为Index.php。
class IndexController extends BaseController
{
	public function index(){
		return $this->fetch("login@login/index");
	}

	public function dashboard(){
		$aid=Session::get('admin_id');
		$Admin=Admin::get($aid);
		$map1["user_itemstatus"]=0;

		$User=new User();
		$Review=new Review();

		$checked=$User->where($map1)->count();

		$map2["create_time"]=array("gt",time()-60*60*24);
		$map3["create_time"]=array("gt",time()-60*60*24*7);

		$map4["admin_id"]=$aid;

		$newuser=$User->where($map2)->count();
		$newitem=$User->where($map3)->count();
		$adminreviewcount=$Review->where($map4)->count();

		$total=$User->count();

		$this->assign("checked",$checked);
		$this->assign("newuser",$newuser);
		$this->assign("newitem",$newitem);
		$this->assign("total",$total);
		$this->assign("Admin",$Admin);
		$this->assign("adminreviewcount",$adminreviewcount);

		header('Content-type:text/html;charset=utf-8');
 
 
		//配置您申请的appkey
		$appkey = "992b8436716e727988036eb95757b6f0";
		 
		 
		 
		 
		//************1.根据城市查询天气************
		$url = "http://op.juhe.cn/onebox/weather/query";
		$params = array(
		      "cityname" => "上海",//要查询的城市，如：温州、上海、北京
		      "key" => $appkey,//应用APPKEY(应用详细页查询)
		      "dtype" => "json",//返回数据的格式,xml或json，默认json
		);
		$paramstring = http_build_query($params);
		$content = juhecurl($url,$paramstring);
		$result = json_decode($content,true);
		if($result){
		    if($result['error_code']=='0'){
		    	//dump($result);
		    	$this->assign("weather",$result["result"]["data"]["realtime"]);
		        
		    }else{
		        echo $result['error_code'].":".$result['reason'];
		    }
		}else{
		    echo "请求失败";
		}
		//**************************************************
		

		return $this->fetch();
	}



}
/**
 * 请求接口返回内容
 * @param  string $url [请求的URL地址]
 * @param  string $params [请求的参数]
 * @param  int $ipost [是否采用POST形式]
 * @return  string
 */
function juhecurl($url,$params=false,$ispost=0){
    $httpInfo = array();
    $ch = curl_init();
 
    curl_setopt( $ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1 );
    curl_setopt( $ch, CURLOPT_USERAGENT , 'JuheData' );
    curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT , 60 );
    curl_setopt( $ch, CURLOPT_TIMEOUT , 60);
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER , true );
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    if( $ispost )
    {
        curl_setopt( $ch , CURLOPT_POST , true );
        curl_setopt( $ch , CURLOPT_POSTFIELDS , $params );
        curl_setopt( $ch , CURLOPT_URL , $url );
    }
    else
    {
        if($params){
            curl_setopt( $ch , CURLOPT_URL , $url.'?'.$params );
        }else{
            curl_setopt( $ch , CURLOPT_URL , $url);
        }
    }
    $response = curl_exec( $ch );
    if ($response === FALSE) {
        //echo "cURL Error: " . curl_error($ch);
        return false;
    }
    $httpCode = curl_getinfo( $ch , CURLINFO_HTTP_CODE );
    $httpInfo = array_merge( $httpInfo , curl_getinfo( $ch ) );
    curl_close( $ch );
    return $response;
}