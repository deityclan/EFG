<?php
namespace app\common\model;
use think\Model;    // 使用前进行声明

class User extends Model{
    
    protected $dateFormat = 'Y年m月d日 H时i分';    // 日期格式

    public function University(){
        return $this->belongsTo('University');
    }

    public function Field(){
        return $this->belongsTo('Field');
    }

    public function getUserItemfileAttr($value){
    	$files = explode(";",$value);
        /*foreach($files as &$file){
            $file=urlencode(mb_convert_encoding($file, 'gb2312', 'utf-8'));
        }*/
    	return $files;
    }

    public function getUserItemstatusAttr($value){
        $result = array('0'=>'未审核','1'=>'已通过','2'=>"已退回");
        $data = $result[$value];
        if (isset($data))
        {
            return $data;
        } else {
            return $result[0];
        }
    }
}
