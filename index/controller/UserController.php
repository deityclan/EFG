<?php
namespace app\index\controller;
use app\common\model\User;
use app\common\model\University;
use app\common\model\Field;
use think\Request;
use think\Db;

class UserController extends BaseController{
    
    /*
    ** 输入：无
	** 输出：view模板
	** 功能：列出表格的所有数据，并输出到模板页面
	*/
    public function listAllData(){

    	$Users = User::all();

        $this->assign('Users', $Users);

        //$Universitys=University::all();
        //使用orderRaw是为了将utf8字符集的数据转换成gbk进行排序，否则中文排序不正常
        $Universitys=Db::table('university')->orderRaw('CONVERT(university_name USING gbk)')->select();
        $this->assign('Universitys',$Universitys);

        $Fields=Db::table('field')->orderRaw('CONVERT(field_name USING gbk)')->select();
        $this->assign("Fields",$Fields);

        return $this->fetch();
    
    }

    

    /*
    ** 输入：无
    ** 输出：文本结果
    ** 功能：对数据进行保存
    */
    public function save(){
        
        // 实例化
        $User = new User;

        // 写入要更新的数据
        $User->user_name = input('post.user_name');
        $User->user_phone = input('post.user_phone');
        $User->user_email = input('post.user_email');
        $User->university_id = input('post.university_id');
        $User->user_wechatID = input('post.user_wechatID');
        $User->user_applyYear = input('post.user_applyYear');
        $User->user_degree = input('post.user_degree');
        $User->user_itemname = input('post.user_itemname');
        $User->field_id = input('post.field_id');
        $User->user_itemfile = input('post.user_itemfile');

        // 新增数据并进行validate验证
        // 失败返回json
        if (!$User->validate(true)->save($User->getData())){
            return $this->error('添加失败，原因：' . $User->getError());
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
        if (false === $User = User::get($id)){
            return $this->error('系统未找到ID为' . $id . '的记录');
        }

        //成功则返回获取到的数据
        return $this->success('读取数据成功',null,$User);
    
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
        $User = User::get($id);

        // 写入要更新的数据
        $User->user_name = input('post.user_name');
        $User->user_phone = input('post.user_phone');
        $User->user_email = input('post.user_email');
        $User->university_id = input('post.university_id');
        $User->user_wechatID = input('post.user_wechatID');
        $User->user_applyYear = input('post.user_applyYear');
        $User->user_degree = input('post.user_degree');
        $User->user_itemname = input('post.user_itemname');
        $User->field_id = input('post.field_id');
        $User->user_itemfile = input('post.user_itemfile');

        if (!is_null($User)){
            if (!$User->validate(true)->save($User->getData())){
                return $this->error('更新失败' . $User->getError());
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
        $User = User::get($id);

        // 要删除的对象存在
        if (is_null($User)){
            return $this->error('不存在id为' . $id . '的数据，删除失败');
        }

        // 删除对象
        if (!$User->delete()){
            return $this->error('删除失败，原因：' . $User->getError());
        }
        
        // 返回json
        return $this->success('删除成功'); 
    
    }


    public function downloadFile(){
        $file_dir = ROOT_PATH.$_GET['url'];

        $file_array=explode(".",$_GET['url']);

        $filetype=intval(substr($file_array[0],-1));

        $extension=$file_array[1];

        switch($filetype){
            case 1:
            $filename="2020年上海市研究生创新创业能力培养计划项目申请书";
            break;
            case 2:
            $filename="学生证电子扫描";
            break;
            case 3:
            $filename="拟创业项目商业计划书";
            break;
            case 4:
            $filename="导师同意学生创业文件";
            break;
        }
        
        $down_host = $_SERVER['HTTP_HOST'];
        if (!file_exists ( $file_dir )) {    
            header('HTTP/1.1 404 NOT FOUND');  
        } else {    
            //以只读和二进制模式打开文件   
            $file = fopen ( $file_dir, "rb" ); 

            //告诉浏览器这是一个文件流格式的文件    
            Header ( "Content-type: application/octet-stream" ); 
            //请求范围的度量单位  
            Header ( "Accept-Ranges: bytes" );  
            //Content-Length是指定包含于请求或响应中数据的字节长度    
            Header ( "Accept-Length: " . filesize ( $file_dir ) );  
            //用来告诉浏览器，文件是可以当做附件被下载，下载后的文件名称为$file_name该变量的值。
            Header ( "Content-Disposition: attachment; filename=".$filename.".".$extension  );    

            //读取文件内容并直接输出到浏览器    
            echo fread ( $file, filesize ( $file_dir ) );    
            fclose ( $file );    
            exit ();    
        }  

        //return $file_dir;
        // 检查文件是否存在
        /*if (!file_exists($file_dir) ) {
            $this->error('文件未找到');
        }else{
            // 打开文件
            $file1 = fopen($file_dir, "r");
            // 输入文件标签
            Header("Content-type: application/octet-stream");
            Header("Accept-Ranges: bytes");
            Header("Accept-Length:".filesize($file_dir));
            Header("Content-Disposition: attachment;filename=" . $file_dir);
            $buffer=1024;
            $file_count=0;
            //ob_clean();     // 重点！！！
            //flush();        // 重点！！！！可以清除文件中多余的路径名以及解决乱码的问题：
            //输出文件内容
            //读取文件内容并直接输出到浏览器
            while(!feof($file1) &&$file_count<filesize($file_dir)){

                $file_con=fread($file1,$buffer);

                $file_count+=$buffer;

                echo $file_con;

            }

            
            //echo fread($file1, 1024);
            fclose($file1);
            exit();
        }*/
    }

    public function downloadFiles(){
        $id=input("get.id");
        $User=User::get($id);
        $name=$User->getData("user_name");
        $name = urlencode($name);
        $files=explode(";",$User->getData("user_itemfile"));
        //$files="/opt/lampp/htdocs/efg/public/uploads/test.png";
        $id=$User->getData("user_wechatID");
        
       
        $path = '/tmp/'.$name.'.zip';

        

        $zip = new \ZipArchive;//使用本类，linux需开启zlib，windows需取消php_zip.dll前的注释
       	
        if(file_exists($path)){
            unlink($path);
        }

        $zip->open($path, \ZipArchive::CREATE);
        foreach($files as $file){
        	
        	$true_path="/opt/lampp/htdocs/efg/".$file;
        	$type=substr(basename($file), 0,1);
        	$array=explode(".",basename($file));
        	$extension=$array[1];
        	if($type=="1"){
        		$true_name="2020年上海市研究生创新创业能力培养计划项目申请书.".$extension;
        	}else if($type=="2"){
        		$true_name="学生证电子扫描.".$extension;
        	}else if($type=="3"){
        		$true_name="拟创业项目商业计划书.".$extension;
        	}else if($type=="4"){
        		$true_name="导师推荐意见.".$extension;
        	}
        	
        	$zip->addFile($true_path,$true_name);
        }
        
        $zip->close();
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header('Content-disposition: attachment; filename='.basename($path)); //文件名
        header("Content-Type: application/zip"); //zip格式的
        header("Content-Transfer-Encoding: binary"); //告诉浏览器，这是二进制文件
        header('Content-Length: '. filesize($path)); //告诉浏览器，文件大小
        ob_clean();
		flush();

        readfile($path);
 
       
    }

}
