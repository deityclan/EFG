<?php
namespace app\wechat\controller;
use think\Controller;
use think\Request;              // 请求
use think\Db;
use app\common\model\Admin;   // 教师模型
use app\common\model\User;
use app\common\model\University;
use app\common\model\Field;
use app\common\model\Review;
use app\common\model\Notice;

class ExportController extends Controller{

    public static $appid = 'wx810e3a753e429d9d';  //小程序appid

    public static $secret = '7e38bf829390dea6350185faef32a65d'; //小程序秘钥   

    public $sessionKey ='';

	public function wechatLogin(){
		if(Request::instance()->isPost()){
			

            $code = Request::instance()->post('code');
            
            $host="https://api.weixin.qq.com/sns/jscode2session";

            $appid=self::$appid;
            $appsecret=self::$secret;

            $url=$host."?appid=".$appid."&secret=".$appsecret."&js_code=".$code."&grant_type=authorization_code";

            $output = httpGet($url);
            

            return $output;
            

		}
	}

    /*
    ** 输入：无
    ** 输出：全部数据
    ** 功能：提取表格的全部数据
    */
    public function selallNotice(){
        
        
        $datas=Notice::all();

        $return=["status"=>0,"data"=>$datas];

        return json_encode($return);
        
    }

    public function getUniversitys(){
        if(Request::instance()->isPost()){
            $Universitys=Db::table('university')->orderRaw('CONVERT(university_name USING gbk)')->select();
            return json($Universitys);
        }
    }

    public function getFields(){
        if(Request::instance()->isPost()){
            $Fields=Db::table('field')->orderRaw('CONVERT(field_name USING gbk)')->select();
            return json($Fields);
        }
    }

    public function getUser(){
    	if(Request::instance()->isPost()){
    		
    		// 接收数据，取要更新的关键字信息
        	$id = Request::instance()->post('id');

        	// 获取当前对象
        	$User = User::get(["user_wechatID"=>$id]);

            if($User){
                $User["university_name"]=$User->University->university_name;
                $User["field_name"]=$User->Field->field_name;
                //$arr=explode(";",$User->user_itemfile);
                //$User["file1"]=$arr[0];

                $return=array("status"=>1,"value"=>$User);
                return json($return);
            }else{
                $return=array("status"=>0,"value"=>'操作失败，原因：该微信用户尚未提交过项目申请，系统内无用户信息' );
                return json($return);
            }
        
        	

        	
    	}
    }

    public function countbyid(){
    	if(Request::instance()->isPost()){
    		
    		// 接收数据，取要更新的关键字信息
        	$map["user_wechatID"] = Request::instance()->post('id');
        	$map["user_itemstatus"]=0;

        	// 获取当前对象
        	$User = User::get($map);

            if($User){
               
                $return=array("status"=>1,"uncheck"=>1,"check"=>0);
                return json($return);
            }

            // 接收数据，取要更新的关键字信息
        	$map["user_wechatID"] = Request::instance()->post('id');
        	$map["user_itemstatus"]=1;

        	// 获取当前对象
        	$User = User::get($map);

        	if($User){
               
                $return=array("status"=>1,"uncheck"=>0,"check"=>1);
                return json($return);
            }

           
            $return=array("status"=>0,"uncheck"=>0,"check"=>0 );
            return json($return);
            
        	
    	}
    }

    public function deleteUser(){
        if(Request::instance()->isPost()){
            // 接收数据，取要更新的关键字信息
            $id = Request::instance()->post('id');
            // 获取当前对象
            $User = User::get(["user_wechatID"=>$id]);

            //正常来说，应该删除对应在review表里的审核记录，但是在用户重新申报的流程中加入了限制条件：即只有未经审核的项目（用户）才可以删除，所以review表不需要考虑删除的事宜

            // 要删除的对象不存在
            if (is_null($User)) {
                $return=array("status"=>0,"value"=>'操作失败，原因：不存在该记录' );
                return json($return);
            }

            // 删除对象
            if (!$User->delete()) {

                $return=array("status"=>0,"value"=>'操作失败，原因'.$User->getError() );
                return json($return);
            }

            $return=array("status"=>1,"value"=>"操作成功");
            return json($return);
        }
    }

    public function getReview(){
        if(Request::instance()->isPost()){

            // 接收数据，取要更新的关键字信息
            $id = Request::instance()->post('id');


            $Review = Review::get(["user_id"=>$id]);

            if($Review){
               

                $return=array("status"=>1,"value"=>$Review);
                return json($return);
            }else{
                $return=array("status"=>0,"value"=>'操作失败，原因：无审核信息' );
                return json($return);
            }
        
            

            
        }
    }

    public function saveUser(){
        if(Request::instance()->isPost()){
            
            // 接收数据，取要更新的关键字信息
            $id = Request::instance()->post('id');
            $name=Request::instance()->post('name');
            $value=Request::instance()->post('value');

            
            $User = User::get(["user_wechatID"=>$id]);

            $User->$name=$value;
            // 失败返回json
            if (!$User->validate(true)->save($User->getData())){
                $return=array("status"=>0,"reason"=>'保存失败，原因：' . $User->getError());
                return json_encode($return);
            }
            $return=array("status"=>1,"reason"=>'保存成功');
            // 成功返回json
            return json_encode($return);
            

        }
    }

    public function insertUser(){
        if(Request::instance()->isPost()){
            //调试用
            /*var_dump($_POST);
            $data=$_POST;
            return json($data);*/
            // 实例化
	        $User = new User;

	        // 写入要更新的数据
	        $User->user_name = input('post.user_name');
	        $User->user_phone = input('post.user_phone');
	        $User->user_email = input('post.user_email');
	        $User->university_id = input('post.university_id');
	        $User->user_wechatID = input('post.user_wechatID');
	        $User->user_applyYear = date("Y");
	        $User->user_degree = input('post.user_degree');
	        $User->user_itemname = input('post.user_itemname');
	        $User->field_id = input('post.field_id');
	        $User->user_itemfile = input('post.user_itemfile');


	        // 新增数据并进行validate验证
	        // 失败返回json
	        if (!$User->validate(true)->save($User->getData())){
	        	$return=array("status"=>0,"reason"=>'添加失败，原因：' . $User->getError());
	            return json_encode($return);
	        }
	    	$return=array("status"=>1,"reason"=>'添加成功');
	        // 成功返回json
	        return json_encode($return);
        }
    }

    public function uploadFile(){
        $file = request()->file("file");
        $data = request()->post();
        $id=$data["id"];
        
        $filename=$data["filename"];
        $type=$data["type"];
        
        $path='public' . DS . 'uploads'.DS."user".DS.$id.DS;
        if($type==1){
            //5MB DOC
            $info = $file->validate(['size'=>5242880,'ext'=>'doc,docx'])->move(ROOT_PATH . $path,$filename);
        }else if($type==2){
            //2MB PDF
            $info = $file->validate(['size'=>2097152,'ext'=>'pdf'])->move(ROOT_PATH . $path,$filename);
        }else if($type==3){
            //30MB PPT PDF
            $info = $file->validate(['size'=>31457280,'ext'=>'ppt,pptx,pdf'])->move(ROOT_PATH . $path,$filename);
        }else if($type==4){
            //2MB PDF
            $info = $file->validate(['size'=>2097152,'ext'=>'pdf'])->move(ROOT_PATH . $path,$filename);
        }
        
        if($info){
            $returnData=$info->getSaveName();
            //$exclePath = iconv("GB2312","UTF-8",  $returnData);
            echo $path.$returnData;
        }else{
            echo $file->getError();
        }
        
        
    }

    public static function downloadDoc()
    {
        $fileLocation=ROOT_PATH . 'public' . DS . 'uploads' . '/1/'; 
        $file_name="2020年上海市研究生创新创业能力培养计划项目申请书.docx";
        $file_dir=$fileLocation.$file_name;
        $file1 = fopen($file_dir, "r");
            // 输入文件标签
            Header("Content-type: application/octet-stream");
            Header("Accept-Ranges: bytes");
            Header("Accept-Length:".filesize($file_dir));
            Header("Content-Disposition: attachment;filename=" . $file_dir);
            ob_clean();     // 重点！！！
            flush();        // 重点！！！！可以清除文件中多余的路径名以及解决乱码的问题：
            //输出文件内容
            //读取文件内容并直接输出到浏览器
            echo fread($file1, filesize($file_dir));
            fclose($file1);
            exit();

    }

    public static function getItemStatus(){

    }

    /**
     * 发送HTTP请求方法
     * @param  string $url    请求URL
     * @param  array  $params 请求参数
     * @param  string $method 请求方法GET/POST
     * @return array  $data   响应数据
     */
    function httpCurl($url, $params, $method = 'POST', $header = array(), $multi = false){
        date_default_timezone_set('PRC');
        $opts = array(
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER     => $header,
            CURLOPT_COOKIESESSION  => true,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_COOKIE         =>session_name().'='.session_id(),
        );
        /* 根据请求类型设置特定参数 */
        switch(strtoupper($method)){
            case 'GET':
                // $opts[CURLOPT_URL] = $url . '?' . http_build_query($params);
                // 链接后拼接参数  &  非？
                $opts[CURLOPT_URL] = $url . '?' . http_build_query($params);
                break;
            case 'POST':
                //判断是否传输文件
                $params = $multi ? $params : http_build_query($params);
                $opts[CURLOPT_URL] = $url;
                $opts[CURLOPT_POST] = 1;
                $opts[CURLOPT_POSTFIELDS] = $params;
                break;
            default:
                throw new Exception('不支持的请求方式！');
        }
        /* 初始化并执行curl请求 */
        $ch = curl_init();
        curl_setopt_array($ch, $opts);
        $data  = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if($error) throw new Exception('请求发生错误：' . $error);
        return  $data;
    }
    /**
     * 微信信息解密
     * @param  string  $appid  小程序id
     * @param  string  $sessionKey 小程序密钥
     * @param  string  $encryptedData 在小程序中获取的encryptedData
     * @param  string  $iv 在小程序中获取的iv
     * @return array 解密后的数组
     */
    function decryptData( $appid , $sessionKey, $encryptedData, $iv ){
        $OK = 0;
        $IllegalAesKey = -41001;
        $IllegalIv = -41002;
        $IllegalBuffer = -41003;
        $DecodeBase64Error = -41004;
        if (strlen($sessionKey) != 24) {
            return $IllegalAesKey;
        }
        $aesKey=base64_decode($sessionKey);

        if (strlen($iv) != 24) {
            return $IllegalIv;
        }
        $aesIV=base64_decode($iv);

        $aesCipher=base64_decode($encryptedData);

        $result=openssl_decrypt( $aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);
        $dataObj=json_decode( $result );
        if( $dataObj  == NULL )
        {
            return $IllegalBuffer;
        }
        if( $dataObj->watermark->appid != $appid )
        {
            return $DecodeBase64Error;
        }
        $data = json_decode($result,true);
        return $result;
    }

    /**
     * 请求过程中因为编码原因+号变成了空格
     * 需要用下面的方法转换回来
     */
    function define_str_replace($data)
    {
        return str_replace(' ','+',$data);
    }


    //获取手机号
    public function number($sessionKey, $encryptedData, $iv)
    {
        include_once (ROOT_PATH."public/static/wechat/wxBizDataCrypt.php"); //引入 wxBizDataCrypt.php 文件
        
        $appid=self::$appid;
        $sessionKey = $sessionKey;
        $encryptedData= $encryptedData;
        $iv = $iv;
        $data = '';

        $pc = new \WXBizDataCrypt($appid, $sessionKey); //注意使用\进行转义
        $errCode = $pc->decryptData($encryptedData, $iv, $data );
        if ($errCode == 0) {
            print($data . "\n");
        } else {
            print($errCode . "\n");
        }
    }

}

function httpGet($url) {

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curl, CURLOPT_TIMEOUT, 500);

        // 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。

        // 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        curl_setopt($curl, CURLOPT_URL, $url);

     

        $res = curl_exec($curl);

        curl_close($curl);

     

        return $res;

    }