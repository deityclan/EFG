<?php
namespace app\common\model;
use think\Model;    // 使用前进行声明

class Notice extends Model{
    
    protected $dateFormat = 'Y年m月d日 H时i分s秒';    // 日期格式

    public function Admin(){
        return $this->belongsTo('Admin');
    }

}
